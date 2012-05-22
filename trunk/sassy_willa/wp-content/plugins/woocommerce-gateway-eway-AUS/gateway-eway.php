<?php
/*
Plugin Name: WooCommerce eWAY gateway (AU)
Plugin URI: http://hypnoticzoo.com
Description: A payment gateway for eWAY (Australia). A eWAY (Australia) account, Curl support, and a server with SSL support and an SSL certificate is required (for security reasons) for this gateway to function.
Version: 1.2.1
Author: Andy Zhang
Author URI: http://hypnoticzoo.com/

	Copyright: © 2009-2011 WooThemes.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Plugin updates
 */
if (is_admin()) {
	if ( ! class_exists( 'WooThemes_Plugin_Updater' ) ) require_once( 'woo-updater/plugin-updater.class.php' );
	
	$woo_plugin_updater_eway_au = new WooThemes_Plugin_Updater( __FILE__ );
	$woo_plugin_updater_eway_au->api_key = 'ee703cf9db069fdbfc41bdcdd604a23b';
	$woo_plugin_updater_eway_au->init();
}

add_action('plugins_loaded', 'woocommerce_eway_init', 0);

function woocommerce_eway_init() {

    if (!class_exists('WC_Payment_Gateway'))
        return;

    /**
     * Gateway class
     * */
    class WC_Gateway_EWAY_AUS extends WC_Payment_Gateway {

        function __construct() {

            $this->id = 'eway';
            $this->method_title = __('eWAY (AU)', 'woothemes');
            $this->icon = plugins_url('images/eway_icon.png', __FILE__);
            $this->has_fields = true;
            $this->testid = "87654321";
            $this->antifraudurl = 'https://www.eway.com.au/gateway_cvn/xmlbeagle.asp';
            $this->liveurl = 'https://www.eway.com.au/gateway_cvn/xmlpayment.asp';
            $this->testurl = 'https://www.eway.com.au/gateway_cvn/xmltest/testpage.asp';
            $this->currency = get_option('woocommerce_currency');

            $this->avaiable_card_types = array(
                "Visa" => array("icon" => plugins_url('images/card_visa.png', __FILE__)),
                "MasterCard" => array("icon" => plugins_url('images/card_mastercard.png', __FILE__)),
                "AmEx" => array("icon" => plugins_url('images/card_amex.png', __FILE__)),
                "Diners" => array("icon" => plugins_url('images/card_diners.png', __FILE__)),
                "JCB" => array("icon" => plugins_url('images/card_jcb.png', __FILE__))
            );
            
            // Load the form fields
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();

            // Get setting values
            $this->title = $this->settings['title'];
            $this->description = $this->settings['description'];
            $this->enabled = $this->settings['enabled'];
            $this->testmode = $this->settings['testmode'];
            $this->antifraud = $this->settings['antifraud'];
            $this->customer_id = ($this->testmode == "yes") ? $this->testid : $this->settings['customer_id'];

            // Hooks
            add_action('admin_notices', array(&$this, 'ssl_check'));
            add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
            add_action('wp_head', array(&$this, 'checkout_css_styles'));
        }

        /**
         * Check if SSL is enabled and notify the user
         * */
        function ssl_check() {

            if ($this->testmode == 'yes' && $this->antifraud == 'yes') :

                echo '<div class="error"><p>' . sprintf(__('Only Beagle Anti-Fraud will be used.', 'woothemes'), '') . '</p></div>';

            endif;

            if (get_option('woocommerce_force_ssl_checkout') == 'no' && $this->enabled == 'yes') :

                echo '<div class="error"><p>' . sprintf(__('eWAY is enabled, but the <a href="%s">force SSL option</a> is disabled; your checkout is not secure! Please enable SSL and ensure your server has a valid SSL certificate - eWAY will only work in test mode.', 'woothemes'), admin_url('admin.php?page=woocommerce')) . '</p></div>';

            endif;
        }

        /**
         * Initialise Gateway Settings Form Fields
         */
        function init_form_fields() {

            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'woothemes'),
                    'label' => __('Enable eWAY', 'woothemes'),
                    'type' => 'checkbox',
                    'description' => '',
                    'default' => 'no'
                ),
                'antifraud' => array(
                    'title' => __('eWAY Beagle Anti-Fraud', 'woothemes'),
                    'label' => __('Enable beagle anti-fraud mode', 'woothemes'),
                    'type' => 'checkbox',
                    'description' => __('Place the payment gateway in Beagle Anti-Fraud mode.', 'woothemes'),
                    'default' => 'no'
                ),
                'testmode' => array(
                    'title' => __('eWAY test', 'woothemes'),
                    'label' => __('Enable test mode', 'woothemes'),
                    'type' => 'checkbox',
                    'description' => __('Place the payment gateway in development mode.', 'woothemes'),
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => __('Title', 'woothemes'),
                    'type' => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'woothemes'),
                    'default' => __('Credit card (eWAY)', 'woothemes')
                ),
                'description' => array(
                    'title' => __('Description', 'woothemes'),
                    'type' => 'textarea',
                    'description' => __('This controls the description which the user sees during checkout.', 'woothemes'),
                    'default' => 'Pay with your credit card via eWAY gateway.'
                ),
                'customer_id' => array(
                    'title' => __('eWAY Customer ID', 'woothemes'),
                    'type' => 'text',
                    'description' => __('Your eWay Customer ID.', 'woothemes'),
                    'default' => ''
                )
            );
        }

        /**
         * Admin Panel Options 
         * - Options for bits like 'title' and availability on a country-by-country basis
         */
        function admin_options() {
            ?>
            <h3><?php _e('eWAY', 'woothemes'); ?></h3>
            <p><?php _e('eWAY works by sending credit card details to eWAY for verification.', 'woothemes'); ?></p>
            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
            </table><!--/.form-table-->
            <?php
        }

        /**
         * Check if this gateway is enabled and available in the user's country
         */
        function is_available() {
            if ($this->enabled == "no")  return false;
            
            if (get_option('woocommerce_force_ssl_checkout') == 'no' && $this->testmode == 'no')
                return false;
            
            if ($this->currency != "AUD")
                return false;

            // Required fields check
            if (!$this->customer_id)
                return false;

            return true;
        }

        /**
         * Get the users country either from their order, or from their customer data
         */
        function get_country_code() {
            global $woocommerce;

            if (isset($_GET['order_id']) && $_GET['order_id'] > 0) :

                $order = new WC_Order($_GET['order_id']);

                return $order->billing_country;

            elseif ($woocommerce->customer->get_country()) :

                return $woocommerce->customer->get_country();

            endif;

            return NULL;
        }
        
		/**
         * CSS for checkout
         */
		function checkout_css_styles() {
			?><style type="text/css">
				.eway_card_type_radios {
					overflow: hidden;
					zoom: 1;
				}
				.eway_card_type_radios label {
					float: left; 
					margin-right: 12px;
					line-height: 25px;
				}
				.eway_card_type_radios .card_type input {
					margin-right: 6px;
				}
				.eway_card_type_radios .card_type input, .eway_card_type_radios .card_type img {
					vertical-align: middle;
					float: none;
				}
			</style><?php
		}
		
        /**
         * Payment form on checkout page
         * Card type: "visa", "mc", "amex", "dc", "jcb"
         */
        function payment_fields() {
            $user_country = $this->get_country_code();

            if (empty($user_country)) :
                _e('Please complete your billing information before entering payment details.', 'woothemes');
                return;
            endif;
            ?>
            <?php if ($this->testmode == 'yes') : ?><p><?php _e('TEST MODE/SANDBOX ENABLED', 'woothemes'); ?></p><?php endif; ?>
            <?php if ($this->description) : ?><p><?php echo $this->description; ?></p><?php endif; ?>
            <fieldset>
            	<p class="form-row form-row-wide eway_card_type_radios">
					<label><?php echo __("Card type", 'woocommerce') ?>:</label>
					<?php foreach ($this->avaiable_card_types as $type => $card) : ?>
						<label class="card_type"><input type="radio" name="eway_card_type" <?php checked($type, "Visa"); ?> value="<?php echo $type ?>" /><img width="38" src="<?php echo $card["icon"]; ?>" /></label>
					<?php endforeach; ?>
				</p>
				
				<div class="clear"></div>
				
				<p class="form-row form-row-first">
					<label for="eway_card_number"><?php echo __("Card Holder's Name", 'woocommerce') ?> <span class="required">*</span></label>
                    <input type="text" class="input-text" name="eway_card_holdername" />
				</p>
				
				<p class="form-row form-row-last">
					<label for="eway_card_number"><?php echo __("Credit Card number", 'woocommerce') ?> <span class="required">*</span></label>
					<input type="text" class="input-text" name="eway_card_number" id="eway_card_number" />
				</p>
				
                <div class="clear"></div>

                <p class="form-row form-row-first">
                    <label for="cc-expire-month"><?php echo __("Expiration date", 'woocommerce') ?> <span class="required">*</span></label>
                    <select name="eway_card_expiration_month" id="cc-expire-month" class="woocommerce-select woocommerce-cc-month">
                        <option value=""><?php _e('Month', 'woocommerce') ?></option>
                        <?php
                        $months = array();
                        for ($i = 1; $i <= 12; $i++) :
                            $timestamp = mktime(0, 0, 0, $i, 1);
                            $months[date('n', $timestamp)] = date('F', $timestamp);
                        endfor;
                        foreach ($months as $num => $name)
                            printf('<option value="%u">%s</option>', $num, $name);
                        ?>
                    </select>
                    <select name="eway_card_expiration_year" id="cc-expire-year" class="woocommerce-select woocommerce-cc-year">
                        <option value=""><?php _e('Year', 'woocommerce') ?></option>
                        <?php
                        for ($i = date('y'); $i <= date('y') + 15; $i++)
                            printf('<option value="%u">20%u</option>', $i, $i);
                        ?>
                    </select>
                </p>
                <p class="form-row form-row-last">
                    <label for="eway_card_csc"><?php _e("Card security code", 'woocommerce') ?> <span class="required">*</span></label>
                    <input type="text" class="input-text" id="eway_card_csc" name="eway_card_csc" maxlength="4" style="width:4em;" />
                    <span class="help eway_card_csc_description"></span>
                </p>
                <div class="clear"></div>
            </fieldset>
            <script type="text/javascript">    
                                                                                                                                                			
                jQuery(".eway_card_type_radios input").click(function(){
                                                                                                                                                					
                    var card_type = jQuery(".eway_card_type_radios input:radio:checked").val();
                    var csc = jQuery("#eway_card_csc").parent();    
                                                                                                                                                					
                    if (card_type == "Visa" || card_type == "MasterCard" || card_type == "Diners" || card_type == "JCB") {
                        jQuery('.eway_card_csc_description').text("<?php _e('3 digits usually found on the signature strip.', 'woocommerce'); ?>");
                    } else if ( card_type == "AmEx" ) {
                        jQuery('.eway_card_csc_description').text("<?php _e('4 digits usually found on the front of the card.', 'woocommerce'); ?>");
                    } else {
                        jQuery('.eway_card_csc_description').text('');
                    }
                                                                                                                                                					
                }).eq(0).click();
                                                                                                                                                			
            </script>
            <?php
        }

        /**
         * Validate the payment form
         */
        function validate_fields() {
            global $woocommerce;

            $card_type = isset($_POST['eway_card_type']) ? woocommerce_clean($_POST['eway_card_type']) : '';
            $card_number = isset($_POST['eway_card_number']) ? woocommerce_clean($_POST['eway_card_number']) : '';
            $cardholder_name = isset($_POST['eway_card_holdername']) ? woocommerce_clean($_POST['eway_card_holdername']) : '';
            $card_csc = isset($_POST['eway_card_csc']) ? woocommerce_clean($_POST['eway_card_csc']) : '';
            $card_exp_month = isset($_POST['eway_card_expiration_month']) ? woocommerce_clean($_POST['eway_card_expiration_month']) : '';
            $card_exp_year = isset($_POST['eway_card_expiration_year']) ? woocommerce_clean($_POST['eway_card_expiration_year']) : '';

            // Check card security code
            if (!ctype_digit($card_csc)) :
                $woocommerce->add_error(__('Card security code is invalid (only digits are allowed)', 'woothemes'));
                return false;
            endif;

            if ((strlen($card_csc) != 3 && in_array($card_type, array('Visa', 'MasterCard', 'Diners', 'JCB'))) || (strlen($card_csc) != 4 && $card_type == 'AmEx')) :
                $woocommerce->add_error(__('Card security code is invalid (wrong length)', 'woothemes'));
                return false;
            endif;

            // Check card expiration data
            if (
                    !ctype_digit($card_exp_month) ||
                    !ctype_digit($card_exp_year) ||
                    $card_exp_month > 12 ||
                    $card_exp_month < 1 ||
                    $card_exp_year < date('y') ||
                    $card_exp_year > date('y') + 20
            ) :
                $woocommerce->add_error(__('Card expiration date is invalid', 'woothemes'));
                return false;
            endif;

            // Check card number
            $card_number = str_replace(array(' ', '-'), '', $card_number);

            if (empty($card_number) || !ctype_digit($card_number)) :
                $woocommerce->add_error(__('Card number is invalid', 'woothemes'));
                return false;
            endif;

            return true;
        }

        /**
         * Process the payment
         */
        function process_payment($order_id) {
            global $woocommerce;

            $order = new WC_Order($order_id);

            $card_type = isset($_POST['eway_card_type']) ? woocommerce_clean($_POST['eway_card_type']) : '';
            $card_number = isset($_POST['eway_card_number']) ? woocommerce_clean($_POST['eway_card_number']) : '';
            $cardholder_name = isset($_POST['eway_card_holdername']) ? woocommerce_clean($_POST['eway_card_holdername']) : '';
            $card_csc = isset($_POST['eway_card_csc']) ? woocommerce_clean($_POST['eway_card_csc']) : '';
            $card_exp_month = isset($_POST['eway_card_expiration_month']) ? woocommerce_clean($_POST['eway_card_expiration_month']) : '';
            $card_exp_year = isset($_POST['eway_card_expiration_year']) ? woocommerce_clean($_POST['eway_card_expiration_year']) : '';

            // Format card expiration data
            $card_exp_month = (int) $card_exp_month;
            if ($card_exp_month < 10) :
                $card_exp_month = '0' . $card_exp_month;
            endif;

            $card_exp_year = (int) $card_exp_year;
            $card_exp_year += 2000;

            $card_exp = $card_exp_month . $card_exp_year;

            // Format card number
            $card_number = str_replace(array(' ', '-'), '', $card_number);

            // Send request to eway
            try {
                $url = (($this->antifraud == "yes")) ? $this->antifraudurl : ($this->testmode == 'yes') ? $this->testurl : $this->liveurl;

                $post_data = array(
                    'ewayCustomerID' => $this->customer_id,
                    'ewayTotalAmount' => $order->order_total*100,
                    'ewayCardNumber' => $card_number,
                    'ewayCardExpiryMonth' => $card_exp_month,
                    'ewayCardExpiryYear' => $card_exp_year,
                    'ewayCVN' => $card_csc,
                    'ewayTrxnNumber' => '',
                    'ewayCustomerInvoiceDescription' => '',
                    'ewayCustomerInvoiceRef' => '',
                    'ewayOption1' => '',
                    'ewayOption2' => '',
                    'ewayOption3' => '',
                    'ewayCustomerFirstName' => $order->billing_first_name,
                    'ewayCustomerLastName' => $order->billing_last_name,
                    'ewayCustomerEmail' => $order->billing_email,
                    'ewayCardHoldersName' => $cardholder_name,
                    'ewayCustomerAddress' => $order->billing_address_1 . ' ' . $order->billing_address_2 . ' ' . $order->billing_city . ' ' . $order->billing_state . ' ' . $order->billing_country,
                    'ewayCustomerPostcode' => $order->billing_postcode
                );
		
		if($this->antifraud == "yes"){
			$post_data['ewayCustomerIPAddress'] = $this->get_user_ip();
			$post_data['ewayCustomerBillingCountry'] = $this->get_country_code();
		}

                $xmlRequest = "<ewaygateway>";
                foreach ($post_data as $key => $value)
                    $xmlRequest .= "<$key>$value</$key>";
                $xmlRequest .= "</ewaygateway>";

                $response = wp_remote_post($url, array(
                    'method' => 'POST',
                    'body' => $xmlRequest,
                    'timeout' => 70,
                    'sslverify' => true
                        ));

                if (is_wp_error($response))
                    throw new Exception(__('There was a problem connecting to the payment gateway.', 'woothemes'));

                if (empty($response['body']))
                    throw new Exception(__('Empty eWAY response.', 'woothemes'));

                $parsed_response = $response['body'];
                $parsed_response = $this->parseResponse($parsed_response);

                switch (strtolower($parsed_response['EWAYTRXNSTATUS'])) :
                    case 'true':

                        // Add order note
                        $order->add_order_note(sprintf(__('eWAY payment completed', 'woothemes')));

                        // Payment complete
                        $order->payment_complete();

                        // Remove cart
                        $woocommerce->cart->empty_cart();

                        // Empty awaiting payment session
                        unset($_SESSION['order_awaiting_payment']);

                        // Return thank you page redirect
                        return array(
                            'result' => 'success',
                            'redirect' => add_query_arg('key', $order->order_key, add_query_arg('order', $order_id, get_permalink(get_option('woocommerce_thanks_page_id'))))
                        );

                        break;
                    case 'false':
                        // Payment failed :(
                        $order->add_order_note(sprintf(__('eWAY payment failed (Correlation ID: %s). Payment was rejected due to an error: ', 'woothemes'), $parsed_response['EWAYAUTHCODE']) . '"' . $parsed_response['EWAYTRXNERROR'] . '"');

                        $woocommerce->add_error(__('Payment error:', 'woothemes') . $parsed_response['EWAYTRXNERROR']);
                        return;

                        break;
                    default:

                        // Payment failed :(
                        $order->add_order_note(sprintf(__('eWAY payment failed (Correlation ID: %s). Payment was rejected due to an error: ', 'woothemes'), $parsed_response['CORRELATIONID']) . '"' . $error_message . '"');

                        $woocommerce->add_error(__('Payment error:', 'woothemes') . $parsed_response['EWAYTRXNERROR']);
                        return;

                        break;
                endswitch;
            } catch (Exception $e) {
                $woocommerce->add_error(__('Connection error:', 'woothemes') . ': "' . $e->getMessage() . '"');
                return;
            }
        }

        /**
         * Get user's IP address
         */
        function get_user_ip() {
            return (isset($_SERVER['HTTP_X_FORWARD_FOR']) && !empty($_SERVER['HTTP_X_FORWARD_FOR'])) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
        }

        function parseResponse($xmlResponse) {
            $xml_parser = xml_parser_create();
            xml_parse_into_struct($xml_parser, $xmlResponse, $xmlData, $index);
            $responseFields = array();
            foreach ($xmlData as $data)
                if ($data["level"] == 2)
                    $responseFields[$data["tag"]] = $data["value"];
            return $responseFields;
        }

    }

    /**
     * Add the Gateway to WooCommerce
     * */
    function add_eway_gateway($methods) {
        $methods[] = 'WC_Gateway_EWAY_AUS';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'add_eway_gateway');
}
