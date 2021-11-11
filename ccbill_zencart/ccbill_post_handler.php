<?php
/**
 * ccbill.php payment module class for CCBill Payment method
 *
 * @package paymentMethod
 * @copyright Copyright 2014-2021 CCBill
 * @version 1.2.0
 */


  require('includes/application_top.php');

  process_post();

  // Store transaction info and process
  // result returned by the CCBill payment gateway
  function process_post() {

    global $order_total_modules, $db;//, $subscriptionId;

    $myAction = '';

    //$check_query1 = $db->Execute("SELECT configuration_key,configuration_value FROM `configuration` WHERE `configuration_key` LIKE 'MODULE\_PAYMENT\_CCBILL\_Salt%'");

    $salt = getSalt();// $check_query1->fields['configuration_value'];

    $prefix = isset($_POST['subscriptionId']) ? 'X-' : '';

    $myFirstName    = '';
    $myLastName     = '';
    $myEmail        = '';
    $myAmount       = '';
    $myCurrencyCode = 0;
    $myDigest       = '';

    if(isset($_POST['subscriptionId']))
      $myAction = 'Approval_Post';
    else if(isset($_GET['Action']))
      $myAction = $_GET['Action'];

    if(($myAction == 'Approval_Post' || $myAction == 'Denial_Post' )){

      $txId      = 0;
      $mySuccess = 0;

      $responseDigest = 0;

      if($myAction == 'Approval_Post'){
/*
        if(isset($_POST[$prefix . 'subscription_id'])){
          $txId = $_POST[$prefix . 'subscription_id'];
          $responseDigest = $_POST[$prefix . 'responseDigest'];
          $mySuccess = 1;
        }
*/
        if(isset($_POST[$prefix . 'subscription_id'])){
          $txId = $_POST[$prefix . 'subscription_id'];
          $responseDigest = $_POST['responseDigest'];
          $mySuccess = 1;
        }
        else if(isset($_POST['subscriptionId'])){
          $txId = $_POST['subscriptionId'];
          //$responseDigest = $_POST['dynamicPricingValidationDigest'];
          $mySuccess = 1;
        }// end if/else
      }
      else if($myAction == 'Denial_Post'){
        //if(isset($_POST['denialId'])) $txId = $_POST['denialId'];
        $mySuccess = 0;
      }// end if/else

      if(isset($_POST[$prefix . 'customer_fname'])) $myFirstName    = $_POST[$prefix . 'customer_fname'];
      if(isset($_POST[$prefix . 'customer_lname'])) $myLastName     = $_POST[$prefix . 'customer_lname'];
      if(isset($_POST[$prefix . 'email']))          $myEmail        = $_POST[$prefix . 'email'];
      if(isset($_POST[$prefix . 'initialPrice']))   $myAmount       = $_POST[$prefix . 'initialPrice'];
      if(isset($_POST[$prefix . 'formPrice']))      $myAmount       = $_POST[$prefix . 'formPrice'];
      if(isset($_POST[$prefix . 'currencyCode']))   $myCurrencyCode = $_POST[$prefix . 'currencyCode'];
      if(isset($_POST[$prefix . 'responseDigest'])) $responseDigest = $_POST[$prefix . 'responseDigest'];
      //if(isset($_POST[$prefix . 'dynamicPricingValidationDigest'])) $responseDigest = $_POST[$prefix . 'dynamicPricingValidationDigest'];
      //if(isset($_POST[$prefix . 'formDigest']))     $myDigest       = $_POST[$prefix . 'formDigest'];

      // Validate response digest
      $subscriptionIdToHash = $txId;

      if(isFlexForm()) {
        $subscriptionIdToHash = ltrim($txId, '0');
      }// end if

      $stringToHash = $subscriptionIdToHash . '1' . $salt;
      $myDigest = md5($stringToHash);

      if ($myDigest === $responseDigest) {
        $mySuccess = 1;
      } else {
        $mySuccess = 0;
      }// end if/else

      //$subscriptionId = $txId;

      if($mySuccess === 1) {

        $tcSql = "INSERT INTO ccbill "
               . "(ccbill_tx_id, first_name, last_name, email, "
               . "amount, currency_code, digest, success) "
               . "VALUES(" . $txId . ", '" . $myFirstName . "', '" . $myLastName
               . "', '" . $myEmail . "', '" . $myAmount . "', " . $myCurrencyCode
               . ", 'MyDigest:" . $myDigest . "', " . $mySuccess . ")";

        $db->Execute($tcSql);
      }// end if

    }
    else{
      print_r('this is a test');
      //$this->notify('NOTIFY_PAYMENT_CCBILL_CANCELLED_DURING_CHECKOUT');
      //zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
    }// end if response is included


  }// end before_process

  // Check to see if the CCBill payment module is installed
  function check() {

    global $db;

    $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_CCBILL_STATUS'");
    $check = $check_query->RecordCount();

    return $check;

  }// end check

  function keys() {
    return array( 'MODULE_PAYMENT_CCBILL_STATUS',
                  'MODULE_PAYMENT_CCBILL_ClientAccountNo',
                  'MODULE_PAYMENT_CCBILL_ClientSubAccountNo',
                  'MODULE_PAYMENT_CCBILL_FormName',
                  'MODULE_PAYMENT_CCBILL_IsFlexForm',
                  'MODULE_PAYMENT_CCBILL_Currency',
                  'MODULE_PAYMENT_CCBILL_Salt'
                );
  }// end keys

  function getSalt() {

    global $db;

    $check_query1 = $db->Execute("SELECT configuration_key,configuration_value FROM `configuration` WHERE `configuration_key` LIKE 'MODULE\_PAYMENT\_CCBILL\_Salt%'");
    return $check_query1->fields['configuration_value'];

  }

  function isFlexForm() {

    global $db;

    $check_query1 = $db->Execute("SELECT configuration_key,configuration_value FROM `configuration` WHERE `configuration_key` LIKE 'MODULE\_PAYMENT\_CCBILL\_IsFlexForm%'");
    return $check_query1->fields['configuration_value'] === 'True';

  }

?>
