<?php

/* @author    2codeThemes
*  @package   WPQA/payments
*  @version   1.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/* Do payments */
add_action("wpqa_do_payments","wpqa_do_payments");
if (!function_exists('wpqa_do_payments')):
	function wpqa_do_payments() {
		$payment_available = wpqa_payment_available();
		if ($payment_available == true) {
			$paypal_sandbox = wpqa_options('paypal_sandbox');
			if ($paypal_sandbox == "on") {
				$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			}else {
				$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
			}
			$user_id = get_current_user_id();
			switch ((isset($_REQUEST['action'])?$_REQUEST['action']:"")) {
				case 'success':
					if ((isset($_REQUEST['txn_id']) && $_REQUEST['txn_id'] != "") || (isset($_REQUEST['tx']) && $_REQUEST['tx'] != "")) {
						$paypal_transaction = (isset($_REQUEST['tx'])?$_REQUEST['tx']:(isset($_REQUEST['txn_id'])?$_REQUEST['txn_id']:''));
						update_user_meta($user_id,"wpqa_paypal_transaction",$paypal_transaction);
						$data = wp_remote_post($paypal_url.'?cmd=_notify-synch&tx='.$paypal_transaction.'&at='.wpqa_options("identity_token".($paypal_sandbox == "on"?"_sandbox":"")));
						if (!is_wp_error($data)) {
							$data = $data['body'];
							$response = substr($data, 7);
							$response = urldecode($response);
							
							preg_match_all('/^([^=\s]++)=(.*+)/m', $response, $m, PREG_PATTERN_ORDER);
							$response = array_combine($m[1], $m[2]);
							
							if (isset($response['charset']) && strtoupper($response['charset']) !== 'UTF-8') {
								foreach ($response as $key => &$value) {
									$value = mb_convert_encoding($value, 'UTF-8', $response['charset']);
								}
								$response['charset_original'] = $response['charset'];
								$response['charset'] = 'UTF-8';
							}
							
							ksort($response);
						}else {
							wp_safe_redirect(esc_url(home_url('/')));
							die();
						}
						if (isset($response['payment_status']) && ($response['payment_status'] == "Completed" || $response['payment_status'] == "Processed" || $response['payment_status'] == "Created")) {
							$add_payment_success = apply_filters("wpqa_add_payment_success",true,$response);
							if ($add_payment_success == true) {
								/* PayPal confirmed */
								wpqa_paypal_confirmed($response,$user_id);
							}
						}
					}else {
						wp_safe_redirect(esc_url(home_url('/')));
						die();
					}
				break;
				case 'cancel':
					do_action("wpqa_paypal_canceled");
					echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("The payment was canceled!","wpqa").'</p></div>';
				break;
				case 'ipn':
					$raw_post_data = file_get_contents('php://input');
					$raw_post_array = explode('&', $raw_post_data);
					$myPost = array();
					foreach ($raw_post_array as $keyval) {
						$keyval = explode ('=', $keyval);
						if (count($keyval) == 2)
							$myPost[$keyval[0]] = urldecode($keyval[1]);
					}

					$req = 'cmd=_notify-validate';
					foreach ($myPost as $key => $value) {
						if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() == 1) {
							$value = urlencode(stripslashes($value));
						}else {
							$value = urlencode($value);
						}
						$req .= "&$key=$value";
					}

					$paypal_sandbox = wpqa_options('paypal_sandbox');
					if ($paypal_sandbox == "on") {
						$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
					}else {
						$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
					}
					$ch = curl_init($paypal_url);
					if ($ch == FALSE) {
						return FALSE;
					}
					curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
					curl_setopt($ch, CURLOPT_SSLVERSION, 6);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
					curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: company-name'));
					$res = curl_exec($ch);

					$tokens = explode("\r\n\r\n", trim($res));
					$res = trim(end($tokens));
					if (strcmp($res, "VERIFIED") == 0 || strcasecmp($res, "VERIFIED") == 0) {
						if (isset($_POST['txn_type']) && $_POST['txn_type'] == "subscr_cancel") {
							$subscr_id = (isset($_POST['subscr_id'])?$_POST['subscr_id']:"");
							$args = array(
								'meta_key'       => 'payment_subscr_id',
								'meta_value'     => $subscr_id,
								'post_type'      => 'statement',
								'posts_per_page' => -1
							);
							$query = new WP_Query($args);
							if ($query->have_posts()) {
								$post_id = (isset($query->posts[0]->ID)?$query->posts[0]->ID:0);
								if ($post_id > 0) {
									$user_id = $query->posts[0]->post_author;
									$payment_replace = get_post_meta($post_id,"payment_replace",true);
									wpqa_delete_subscription("",$payment_replace,true,$user_id);
								}
							}
						}else if (isset($_POST['payment_status']) && ($_POST['payment_status'] == "Refunded" || $_POST['payment_status'] == "Reversed")) {
							$item_transaction = (isset($_POST['parent_txn_id'])?$_POST['parent_txn_id']:"");
							$args = array(
								'meta_key'       => 'payment_item_transaction',
								'meta_value'     => $item_transaction,
								'post_type'      => 'statement',
								'posts_per_page' => -1
							);
							$query = new WP_Query($args);
							if ($query->have_posts()) {
								$post_id = (isset($query->posts[0]->ID)?$query->posts[0]->ID:0);
								if ($post_id > 0) {
									$item_transaction_refund = (isset($_POST['txn_id'])?esc_html($_POST['txn_id']):"");
									$user_id = $query->posts[0]->post_author;
									if (!wpqa_find_refund($item_transaction_refund)) {
										$item_currency = (isset($_POST['mc_currency'])?esc_html($_POST['mc_currency']):"");
										$item_number = (isset($_POST['item_number'])?esc_html($_POST['item_number']):"");
										$item_price = (isset($_POST['mc_gross'])?-floatval($_POST['mc_gross']):get_post_meta($post_id,"payment_item_price",true));
										wpqa_refund_canceled_payment($user_id,$post_id,$item_number);
										$response = array(
											"item_name"            => get_the_title($post_id),
											"item_price"           => $item_price,
											"item_currency"        => $item_currency,
											"item_transaction"     => $item_transaction_refund,
											"original_transaction" => $item_transaction,
										);
										wpqa_insert_refund($response,$user_id,($_POST['payment_status'] == "Refunded"?"refund":"reversed"));
										wpqa_site_user_money($item_price,"-",$item_currency,$user_id);
										if ($_POST['payment_status'] == "Refunded") {
											update_post_meta($post_id,"payment_refund","refund");
										}else {
											update_post_meta($post_id,"payment_reversed","reversed");
										}
										update_post_meta($post_id,"payment_original_transaction",$item_transaction_refund);
									}
								}
							}
						}else if (isset($_POST['payment_status']) && $_POST['payment_status'] == "Canceled_Reversal") {
							$item_transaction = (isset($_POST['parent_txn_id'])?$_POST['parent_txn_id']:"");
							$args = array(
								'meta_key'       => 'payment_item_transaction',
								'meta_value'     => $item_transaction,
								'post_type'      => 'statement',
								'posts_per_page' => -1
							);
							$query = new WP_Query($args);
							if ($query->have_posts()) {
								$post_id = (isset($query->posts[0]->ID)?$query->posts[0]->ID:0);
								if ($post_id > 0) {
									$original_transaction = get_post_meta($post_id,"payment_original_transaction",true);
									delete_post_meta($post_id,"payment_reversed");
									delete_post_meta($post_id,"payment_original_transaction");
								}
							}
							$args = array(
								'meta_key'       => 'payment_item_transaction',
								'meta_value'     => $original_transaction,
								'post_type'      => 'statement',
								'posts_per_page' => -1
							);
							$query = new WP_Query($args);
							if ($query->have_posts()) {
								$post_id = (isset($query->posts[0]->ID)?$query->posts[0]->ID:0);
								wp_delete_post($post_id,true);
							}
						}else if (isset($_POST['payment_status']) && ($_POST['payment_status'] == "Completed" || $_POST['payment_status'] == "Processed" || $_POST['payment_status'] == "Created")) {
							$item_transaction = (isset($_POST['txn_id'])?esc_html($_POST['txn_id']):"");
							$custom           = (isset($_POST['custom'])?esc_html($_POST['custom']):"");
							$subscr_id        = (isset($_POST['subscr_id'])?esc_html($_POST['subscr_id']):"");
							$item_no          = (isset($_POST['item_number'])?esc_html($_POST['item_number']):"");
							$str_replace      = str_replace("wpqa_".$item_no."-","",$custom);
							$post_id          = (int)$str_replace;
							$get_post         = get_post($post_id);
							if (isset($get_post->ID)) {
								$payment_kind = get_post_meta($get_post->ID,prefix_meta."payment_kind",true);
								$subscriptions_options = get_post_meta($get_post->ID,prefix_meta."subscriptions_options",true);
								$payment_action = get_post_meta($get_post->ID,prefix_meta."payment_action",true);
								$payment_role = get_post_meta($get_post->ID,prefix_meta."payment_role",true);
								if ($payment_action == "role" && $payment_role != "") {
									$get_post_id = $get_post->ID;
								}
							}
							$get_post_id = (isset($get_post_id) && $get_post_id > 0?$get_post_id:"");
							if ($subscr_id != "") {
								$users = get_users(array('meta_key' => 'wpqa_paypal_customer'.$get_post_id,'meta_value' => $subscr_id,'number' => 1,'count_total' => false));
								if (isset($users[0]) && isset($users[0]->ID) && $users[0]->ID > 0) {
									if ($item_transaction != "") {
										$item_price    = (isset($_POST['mc_gross'])?esc_html($_POST['mc_gross']):"");
										$item_currency = (isset($_POST['mc_currency'])?esc_html($_POST['mc_currency']):"");
										$payer_email   = (isset($_POST['payer_email'])?esc_html($_POST['payer_email']):"");
										$first_name    = (isset($_POST['first_name'])?esc_html($_POST['first_name']):"");
										$last_name     = (isset($_POST['last_name'])?esc_html($_POST['last_name']):"");
										$item_name     = (isset($_POST['item_name'])?esc_html($_POST['item_name']):"");
										$array = array (
											'item_no'          => $item_no,
											'item_name'        => $item_name.' '.esc_html__('(Renew)','WPQA'),
											'item_price'       => $item_price,
											'item_currency'    => $item_currency,
											'item_transaction' => $item_transaction,
											'payer_email'      => $payer_email,
											'first_name'       => $first_name,
											'last_name'        => $last_name,
											'sandbox'          => ($paypal_sandbox == 'on'?'sandbox':''),
											'payment'          => 'PayPal',
											'renew'            => 'subscribe',
											'payment_type'     => 'subscribe',
											'customer'         => $subscr_id,
											'subscr_id'        => $subscr_id,
											'custom'           => $custom,
										);
										wpqa_payment_succeeded($users[0]->ID,$array);
									}
								}else {
									if ($item_transaction != "") {
										wpqa_check_paypal_payment($item_transaction,$_POST);
									}
								}
							}else if ($item_transaction != "") {
								$add_payment_ipn = apply_filters("wpqa_add_payment_ipn",true,$item_transaction,$_POST);
								if ($add_payment_ipn == true) {
									wpqa_check_paypal_payment($item_transaction,$_POST);
								}
							}
						}
					}
				break;
			}
		}
	}
endif;
/* Check PayPal payment */
function wpqa_check_paypal_payment($item_transaction,$response) {
	$args = array(
		's'              => $item_transaction,
		'post_type'      => 'statement',
		'posts_per_page' => -1,
		'date_query'     => array(array('after' => '10 minute ago'))
	);
	$query = new WP_Query($args);
	if ($query->have_posts()) {
		// Found it
	}else {
		$user_id = get_current_user_id();
		$user_id = apply_filters("wpqa_payment_succeeded_user_id",$user_id,$response);
		if ($user_id > 0) {
			/* PayPal confirmed */
			wpqa_paypal_confirmed($response,$user_id);
		}
	}
}
/* PayPal confirmed */
function wpqa_paypal_confirmed($response,$user_id) {
	$item_transaction = (isset($response['txn_id'])?esc_html($response['txn_id']):(isset($response['item_transaction'])?esc_html($response['item_transaction']):""));
	$last_payments    = get_user_meta($user_id,"wpqa_paypal_last_transaction",true);
	if ($item_transaction != "") {
		if ($last_payments != "" && $last_payments == $item_transaction) {
			if ((isset($_REQUEST['sig']) || isset($_REQUEST['verify_sign'])) && ((isset($_REQUEST['payment_status']) && $_REQUEST['payment_status'] == "Completed" || $_REQUEST['payment_status'] == "Processed" || $_REQUEST['payment_status'] == "Created") || (isset($_REQUEST['st']) && $_REQUEST['st'] == "Completed" || $_REQUEST['st'] == "Processed" || $_REQUEST['st'] == "Created")) && (isset($_REQUEST['cm']) && $_REQUEST['cm'] != "") && (isset($_REQUEST['tx']) && $_REQUEST['tx'] != "")) {
				$payment_success = wpqa_get_payment_success(esc_html($_REQUEST['cm']),esc_html($_REQUEST['tx']),true);
				$show_payment_success = apply_filters("wpqa_show_payment_success",true,(isset($response['item_number'])?$response['item_number']:''),$payment_success);
				if ($payment_success && $show_payment_success == true) {
					wpqa_session('<div class="alert-message alert-message-success"><i class="icon-check"></i><p>'.$payment_success.'</p></div>','wpqa_session');
				}
				$redirect_to = wpqa_get_redirect_link(esc_html($_REQUEST['cm']),esc_html($_REQUEST['item_number']),$user_id);
			}else {
				$redirect_to = esc_url(home_url('/'));
			}
			wp_safe_redirect($redirect_to);
			die();
		}else {
			$item_no       = (isset($response['item_number'])?esc_html($response['item_number']):"");
			$item_price    = (isset($response['mc_gross'])?esc_html($response['mc_gross']):"");
			$item_currency = (isset($response['mc_currency'])?esc_html($response['mc_currency']):"");
			$payer_email   = (isset($response['payer_email'])?esc_html($response['payer_email']):"");
			$first_name    = (isset($response['first_name'])?esc_html($response['first_name']):"");
			$last_name     = (isset($response['last_name'])?esc_html($response['last_name']):"");
			$item_name     = (isset($response['item_name'])?esc_html($response['item_name']):"");
			$custom        = (isset($response['custom'])?$response['custom']:(isset($response['cm'])?$response['cm']:""));
			$array = array (
				'item_no'          => $item_no,
				'item_name'        => $item_name,
				'item_price'       => $item_price,
				'item_currency'    => $item_currency,
				'item_transaction' => $item_transaction,
				'payer_email'      => $payer_email,
				'first_name'       => $first_name,
				'last_name'        => $last_name,
				'sandbox'          => (wpqa_options('paypal_sandbox') == 'on'?'sandbox':''),
				'payment'          => 'PayPal',
				'custom'           => $custom,
				'customer'         => (isset($response['subscr_id'])?$response['subscr_id']:''),
				'subscr_id'        => (isset($response['subscr_id'])?$response['subscr_id']:''),
			);
			$redirect_to = wpqa_get_redirect_link(esc_html($custom),$item_no,$user_id);
			wpqa_payment_succeeded($user_id,$array);
			wp_safe_redirect($redirect_to);
			die();
		}
	}else {
		echo '<div class="alert-message alert-message-error"><i class="icon-cancel"></i><p>'.esc_html__("The payment was failed!","wpqa").'</p></div>';
	}
}
/* Change subscription status */
function wpqa_change_subscription_status($subscr_id,$action,$post_id = 0) {
	if ($subscr_id != "") {
		$paypal_sandbox = wpqa_options('paypal_sandbox');
		$sandbox_options = ($paypal_sandbox == 'on'?'_sandbox':'');
		$api_request = 'USER='.urlencode(wpqa_options('paypal_api_username'.$sandbox_options)).'&PWD='.urlencode(wpqa_options('paypal_api_password'.$sandbox_options)).'&SIGNATURE='.urlencode(wpqa_options('paypal_api_signature'.$sandbox_options)).'&VERSION=94.0&METHOD=ManageRecurringPaymentsProfileStatus&PROFILEID='.urlencode($subscr_id).'&ACTION='.urlencode($action).'&NOTE='.urlencode('User canceled the subscription');
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, ($paypal_sandbox == 'on'?'https://api-3t.sandbox.paypal.com/nvp':'https://api-3t.paypal.com/nvp'));
		curl_setopt($ch,CURLOPT_VERBOSE,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$api_request);
		$parsed_response = curl_exec($ch);
		if (!$parsed_response) {
			error_log('Calling PayPal to canceled the subscription failed: '.curl_error($ch).'('.curl_errno($ch).')');
		}
		curl_close($ch);
		parse_str($parsed_response,$response);
		if (isset($response['ACK']) && $response['ACK'] == "Success") {
			wpqa_delete_subscription($subscr_id,$post_id,true);
		}
		return $response;
	}
}
/* Refund */
function wpqa_paypal_refund($transactionID,$currencyCode,$refundType = 'Partial',$amount = '') {
	if ($transactionID != "") {
		$paypal_sandbox = wpqa_options('paypal_sandbox');
		$sandbox_options = ($paypal_sandbox == 'on'?'_sandbox':'');
		$api_request = 'USER='.urlencode(wpqa_options('paypal_api_username'.$sandbox_options)).'&PWD='.urlencode(wpqa_options('paypal_api_password'.$sandbox_options)).'&SIGNATURE='.urlencode(wpqa_options('paypal_api_signature'.$sandbox_options)).'&VERSION=94.0&METHOD=RefundTransaction&TRANSACTIONID='.$transactionID.'&REFUNDTYPE='.$refundType.'&CURRENCYCODE='.$currencyCode.($amount != ''?'&AMT='.$amount:'').'&NOTE='.urlencode('User refund the payment');
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, ($paypal_sandbox == 'on'?'https://api-3t.sandbox.paypal.com/nvp':'https://api-3t.paypal.com/nvp'));
		curl_setopt($ch,CURLOPT_VERBOSE,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$api_request);
		$parsed_response = curl_exec($ch);
		if (!$parsed_response) {
			error_log('Calling PayPal to refund failed: '.curl_error($ch).'('.curl_errno($ch).')');
		}
		curl_close($ch);
		parse_str($parsed_response,$response);
		return $response;
	}
}?>