<?php
/**
 * ccbill.php payment module class for CCBill Payment method
 *
 * @package paymentMethod
 * @copyright Copyright 2014-2021 CCBill
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version 1.0
 */

  class ccbill extends base {
    var $code, $title, $description, $enabled, $order_status, $subscriptionId;

    // class constructor
    function ccbill() {

	  global $order, $db, $messageStack;

      // Throw an error if the amount is zero
      if (  !($order->info['total'] > 0) )
        return "";

      $check_query1 = $db->Execute("SELECT configuration_key,configuration_value FROM `configuration` WHERE `configuration_key`  LIKE 'MODULE\_PAYMENT\_CCBILL\_%'");
      while(!$check_query1->EOF)
      {

        switch($check_query1->fields['configuration_key']){
          case 'MODULE_PAYMENT_CCBILL_ClientAccountNo':     $this->ClientAccountNo      = $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_ClientSubAccountNo':  $this->ClientSubAccountNo   = $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_FormName':            $this->FormName             = $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_IsFlexForm':          $this->IsFlexForm           = $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_Currency':            $this->Currency             = $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_Salt':                $this->Salt                 = $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_ORDER_STATUS_ID':     $this->order_status         = $check_query1->fields['configuration_value'];
            break;
        }// end switch

        $check_query1->MoveNext();
      }// end while

      $this->code = 'ccbill';
	    $this->codeVersion = '1.1.0';

	    $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_CCBILL_STATUS'");
	     // print();
      $this->title = MODULE_PAYMENT_CCBILL_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_CCBILL_TEXT_DESCRIPTION;
      $this->enabled = (($check_query->fields['configuration_value'] == 'True') ? true : false);

	  	//$this->sort_order = 7;//MODULE_PAYMENT_CCBILL_SORT_ORDER;
      $this->error = '';

      //Added this line to allow Set Order Status to work.
      if ((int)MODULE_PAYMENT_CCBILL_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_CCBILL_ORDER_STATUS_ID;
      }

      $isFlexForm = $this->IsFlexForm == 'True' ? true : false;

      // Change made by using ADC Direct Connection
      $this->form_action_url = $isFlexForm ?
                               'https://api.ccbill.com/wap-frontflex/flexforms/' . $this->FormName
                             : 'https://bill.ccbill.com/jpost/signup.cgi';

      // Change made by using ADC Direct Connection
      $this->flexform_base_url = 'https://api.ccbill.com/wap-frontflex/flexforms/';
      //$this->form_action_url = 'http://www.google' . $this->FormName . '.com';
    }// end constructor

    // class methods
    function javascript_validation() {
      return false;
    }// end javascript_validation

    function selection() {

      $selection = array('id' => $this->code,
                         'module' => $this->title);

      return $selection;
    }// end selection

    function pre_confirmation_check() {
      return false;
    }// end pre_confirmation_check

    function confirmation() {
      return false;
    }// end confirmation

    // Build the actions to process when the Submit button is clicked
    // on the order confirmation screen.  Sends data to CCBill gateway.
    function process_button() {

      global $order, $_POST, $HTTP_SERVER_VARS;
      global $db;

      $myOptions    = array();
      $buttonArray  = array();

		  // A.NET INVOICE NUMBER FIX
		  // find the next order_id to pass as x_Invoice_Num
		  $next_inv = '';
		  $inv_id = $db->Execute("select orders_id from " . TABLE_ORDERS . " order by orders_id DESC limit 1");
		  $last_inv = $inv_id->fields['orders_id'];
		  $next_inv = $last_inv+1;
		  // END A.NET INVOICE NUMBER FIX

      $check_query1 = $db->Execute("SELECT configuration_key,configuration_value FROM `configuration` WHERE `configuration_key`  LIKE 'MODULE\_PAYMENT\_CCBILL\_%'");
      while(!$check_query1->EOF)
      {

        switch($check_query1->fields['configuration_key']){
          case 'MODULE_PAYMENT_CCBILL_ClientAccountNo':     			$this->ClientAccountNo    		= $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_ClientSubAccountNo':  			$this->ClientSubAccountNo 		= $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_FormName':            			$this->FormName           		= $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_IsFlexForm':            	        $this->IsFlexForm         		= $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_Currency':            			$this->Currency           		= $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_Salt':                			$this->Salt               		= $check_query1->fields['configuration_value'];
            break;
          case 'MODULE_PAYMENT_CCBILL_ORDER_STATUS_ID':					$this->order_status				= $check_query1->fields['configuration_value'];
            break;
        }// end switch

        $check_query1->MoveNext();
      }// end while

      $this->setCurrencyCode();

      $isFlexForm = $this->IsFlexForm == 'True' ? true : false;

      $this->TransactionAmount = number_format($order->info['total'], 2, '.', '');
      $billingPeriodInDays = 2;


  		$priceVarName      = 'formPrice';
  		$periodVarName     = 'formPeriod';

      $stringToHash = '' . $this->TransactionAmount
  	                     . $billingPeriodInDays
  	                     . $this->CurrencyCode
  	                     . $this->Salt;

  	  $this->Hash = md5($stringToHash);

      $ccbill_addr = 'http://www.goolge.com';//$this->form_action_url;

      if($isFlexForm) {
        $ccbill_addr    = $this->flexform_base_url . $this->FormName;
    		$priceVarName   = 'initialPrice';
    		$periodVarName  = 'initialPeriod';
      }// end if


      $myOptions['clientAccnum']    = $this->ClientAccountNo;
      $myOptions['clientSubacc']    = $this->ClientSubAccountNo;
      $myOptions['formName']        = $this->FormName;
      $myOptions[$priceVarName]     = $this->TransactionAmount;
      $myOptions[$periodVarName]    = $billingPeriodInDays;
      $myOptions['currencyCode']    = $this->CurrencyCode;
      $myOptions['customer_fname']  = $order->customer['firstname'];
      $myOptions['customer_lname']  = $order->customer['lastname'];
      $myOptions['email']           = $order->customer['email_address'];
      $myOptions['zipcode']         = $order->billing['postcode'];
      $myOptions['country']         = $order->billing['country']['iso_code_2'];
      $myOptions['city']            = $order->billing['city'];
      $myOptions['state']           = $this->getStateCodeFromName($order->billing['state']);
      $myOptions['address1']        = $order->billing['street_address'];
      $myOptions['zc_orderid']      = $next_inv;
      $myOptions['MyCount']         = $_SESSION['MYCOUNT'];
      $myOptions['formDigest']      = $this->Hash;

      $_SESSION['CCBILL_AMOUNT'] = $this->TransactionAmount;

      // build the button fields
      foreach ($myOptions as $name => $value) {
        // remove quotation marks
        $value = str_replace('"', '', $value);
        // check for invalid characters
        if (preg_match('/[^a-zA-Z_0-9]/', $name)) {
          //ipn_debug_email('datacheck - ABORTING - preg_match found invalid submission key: ' . $name . ' (' . $value . ')');
          break;
        }// end if

        $buttonArray[] = zen_draw_hidden_field($name, $value);
      }// end foreach

      $process_button_string = "\n" . implode("\n", $buttonArray) . "\n";

      $_SESSION['ccbill_transaction_info'] = array($this->transaction_amount, $this->transaction_currency);

      return $process_button_string;

    }// end process button

    function getStateCodeFromName($stateName){

      $rVal = $stateName;

      switch($rVal){
        case 'Alabama':         $rVal = 'AL';
          break;
        case 'Alaska':          $rVal = 'AK';
          break;
        case 'Arizona':         $rVal = 'AZ';
          break;
        case 'Arkansas':        $rVal = 'AR';
          break;
        case 'California':      $rVal = 'CA';
          break;
        case 'Colorado':        $rVal = 'CO';
          break;
        case 'Connecticut':     $rVal = 'CT';
          break;
        case 'Delaware':        $rVal = 'DE';
          break;
        case 'Florida':         $rVal = 'FL';
          break;
        case 'Georgia':         $rVal = 'GA';
          break;
        case 'Hawaii':          $rVal = 'HI';
          break;
        case 'Idaho':           $rVal = 'ID';
          break;
        case 'Illinois':        $rVal = 'IL';
          break;
        case 'Indiana':         $rVal = 'IN';
          break;
        case 'Iowa':            $rVal = 'IA';
          break;
        case 'Kansas':          $rVal = 'KS';
          break;
        case 'Kentucky':        $rVal = 'KY';
          break;
        case 'Louisiana':       $rVal = 'LA';
          break;
        case 'Maine':           $rVal = 'ME';
          break;
        case 'Maryland':        $rVal = 'MD';
          break;
        case 'Massachusetts':   $rVal = 'MA';
          break;
        case 'Michigan':        $rVal = 'MI';
          break;
        case 'Minnesota':       $rVal = 'MN';
          break;
        case 'Mississippi':     $rVal = 'MS';
          break;
        case 'Missouri':        $rVal = 'MO';
          break;
        case 'Montana':         $rVal = 'MT';
          break;
        case 'Nebraska':        $rVal = 'NE';
          break;
        case 'Nevada':          $rVal = 'NV';
          break;
        case 'New York':        $rVal = 'NY';
          break;
        case 'Ohio':            $rVal = 'OH';
          break;
        case 'Oklahoma':        $rVal = 'OK';
          break;
        case 'Oregon':          $rVal = 'OR';
          break;
        case 'Pennsylvania':    $rVal = 'PN';
          break;
        case 'Rhode Island':    $rVal = 'RI';
          break;
        case 'South Carolina':  $rVal = 'SC';
          break;
        case 'South Dakota':    $rVal = 'SD';
          break;
        case 'Tennessee':       $rVal = 'TN';
          break;
        case 'Texas':           $rVal = 'TX';
          break;
        case 'Utah':            $rVal = 'UT';
          break;
        case 'Virginia':        $rVal = 'VA';
          break;
        case 'Vermont':         $rVal = 'VT';
          break;
        case 'Washington':      $rVal = 'WA';
          break;
        case 'Wisconsin':       $rVal = 'WI';
          break;
        case 'West Virginia':   $rVal = 'WV';
          break;
        case 'Wyoming':         $rVal = 'WY';
          break;
      }// end switch

      return $rVal;

    }// end getStateCodeFromName


    // Return the CCBill currency code
    // based on user selection
    function setCurrencyCode(){
      switch($this->Currency){
        case "USD": $this->CurrencyCode = 840;
          break;
        case "EUR": $this->CurrencyCode = 978;
          break;
        case "AUD": $this->CurrencyCode = 036;
          break;
        case "CAD": $this->CurrencyCode = 124;
          break;
        case "GBP": $this->CurrencyCode = 826;
          break;
        case "JPY": $this->CurrencyCode = 392;
          break;
      }// end switch
    }// end getCurrencyCode

    // Store transaction info and process
    // result returned by the CCBill payment gateway
    function before_process() {

      global $order, $order_total_modules, $db;

      $myAction = '';

      if(isset($_GET['Action'])){

        $myAction = $_GET['Action'];
        if($myAction == 'CheckoutSuccess'
           && isset($_SESSION['CCBILL_AMOUNT'])
           && strlen('' . $_SESSION['CCBILL_AMOUNT']) > 2){

           $myDigest = $_SESSION['CCBILL_AMOUNT'];

           $tcSql = "SELECT * FROM ccbill WHERE email = '" . $order->customer['email_address'] . "' AND amount = '" . $myDigest . "' AND success = 1 AND order_created = 0";

           //die('sql: ' . $tcSql);

           $check_query = $db->Execute($tcSql);
           $rowCount = $check_query->RecordCount();

           //die('record count: ' . $rowCount);

           if($rowCount > 0){

             $tcSql = "UPDATE ccbill SET order_created = 1 WHERE email = '" . $order->customer['email_address'] . "' AND amount = '" . $myDigest . "'";

             $db->Execute($tcSql);

             unset($_SESSION['CCBILL_AMOUNT']);
             return true;
           }
           else{
            $this->notify('NOTIFY_PAYMENT_CCBILL_CANCELLED_DURING_CHECKOUT');
            zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
           }// end if/else
        }
        else{
          $this->notify('NOTIFY_PAYMENT_CCBILL_CANCELLED_DURING_CHECKOUT');
          zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
        }// end if/else

      }
      else{
        $this->notify('NOTIFY_PAYMENT_CCBILL_CANCELLED_DURING_CHECKOUT');
        zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
      }// end if response is included


    }// end before_process

    function after_process() {
      global $insert_id, $db, $subscriptionId;
      $sql = "insert into " . TABLE_ORDERS_STATUS_HISTORY . " (comments, orders_id, orders_status_id, customer_notified, date_added) values (:orderComments, :orderID, :orderStatus, -1, now() )";
      $sql = $db->bindVars($sql, ':orderComments', 'CCBill payment.', 'string');
      $sql = $db->bindVars($sql, ':orderID', $insert_id, 'integer');
      $sql = $db->bindVars($sql, ':orderStatus', $this->order_status, 'integer');
      $db->Execute($sql);
      return false;
    }// end after_process

    function get_error() {
      return $this->error;
    }// end get_error

    // Check to see if the CCBill payment module is installed
    function check() {

      global $db;

      if (!isset($this->_check)) {
        $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_CCBILL_STATUS'");
        $this->_check = $check_query->RecordCount();
      }// end if

      return $this->_check;

    }// end check


    // Install the CCBill payment module
    // and its configuration settings
    function install() {

      global $db, $messageStack;

	    if(defined(MODULE_PAYMENT_CCBILL_STATUS)){
        $messsageStack->add_session('CCBill payment module already installed.', 'error');
        zen_redirect(zen_href_link(FILENAME_MODULES, 'set=payment&module=ccbill', 'NONSSL'));
        return 'failed';
	    }// end if

      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable CCBill Module',  'MODULE_PAYMENT_CCBILL_STATUS',             'True', 'Do you want to accept CCBill payments ?',          '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Client Account Number',               'MODULE_PAYMENT_CCBILL_ClientAccountNo',    '',     'Your six-digit CCBill Client Account Number',      '6', '1', now())");
      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Client SubAccount Number',            'MODULE_PAYMENT_CCBILL_ClientSubAccountNo', '',     'Your four-digit CCBill Client SubAccount Number',  '6', '2', now())");
      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Form Name',                           'MODULE_PAYMENT_CCBILL_FormName',           '',     'The name of your CCBill payment form',             '6', '3', now())");
      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Is Flex Form',          'MODULE_PAYMENT_CCBILL_IsFlexForm',         'False','Select Yes if the form name provided is a CCBill FlexForm', '6', '4', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");

      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Transaction Currency',  'MODULE_PAYMENT_CCBILL_Currency',           'USD',  'The currency that will be used by CCBill.',        '6', '5', 'zen_cfg_select_option(array(\'USD\', \'EUR\', \'AUD\', \'CAD\', \'GBP\', \'JPY\'), ', now())");
      //$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Currency Code',                       'MODULE_PAYMENT_CCBILL_CurrencyCode',       '840',  'The three-digit currency code that CCBill will utilize for billing (USD => 840, EUR => 978, AUD => 036, CAD => 124, GBP => 826, JPY => 392)', '6', '4',  now())");
      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Salt',                                'MODULE_PAYMENT_CCBILL_Salt',               '',     'The salt value is used by CCBill to verify the hash and can be obtained in one of two ways: (1) Contact client support and receive the salt value, OR (2) Create your own salt value (up to 32 alphanumeric characters) and provide it to client support.', '6', '6',  now())");

			$db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_CCBILL_ORDER_STATUS_ID', '1', 'Set the status of orders made with this payment module to this value', '6', '7', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");


      $tcSql = 'CREATE TABLE ccbill '
             . '('
             . 'ccbill_id int(10) UNSIGNED NOT NULL AUTO_INCREMENT, '
             . 'ccbill_tx_id bigint(20)unsigned, '
             . 'first_name varchar(255), '
             . 'last_name varchar(255), '
             . 'email varchar(255), '
             . 'amount decimal(7,2), '
             . 'currency_code int(3), '
             . 'digest varchar(32), '
             . 'success bit DEFAULT 0, '
             . 'order_created bit DEFAULT 0, '
             . 'created TIMESTAMP DEFAULT CURRENT_TIMESTAMP, '
             . 'PRIMARY KEY(ccbill_id)'
             . ')';

      $db->Execute($tcSql);

      $tcSql = 'ALTER TABLE orders ADD ccbill_id int(10) UNSIGNED';

      $db->Execute($tcSql);

      $this->notify('NOTIFY_PAYMENT_CCBILL_INSTALLED');

      //$messsageStack->add_session('CCBill payment module successfully installed.', 'error');
      //zen_redirect(zen_href_link(FILENAME_MODULES, 'set=payment&module=ccbill', 'NONSSL'));

    }// end install

    // Remove the CCBill payment module
    // and its configuration settings
    function remove() {

      global $db;

      $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key LIKE 'MODULE\_PAYMENT\_CCBILL\_%'");

      $tcSql = 'drop table if exists ccbill';

      $db->Execute($tcSql);

      $tcSql = 'ALTER TABLE orders DROP COLUMN ccbill_id';

      $db->Execute($tcSql);

      $this->notify('NOTIFY_PAYMENT_CCBILL_UNINSTALLED');

      //$messsageStack->add_session('CCBill payment module successfully removed.', 'error');
      //zen_redirect(zen_href_link(FILENAME_MODULES, 'set=payment', 'NONSSL'));

    }// end remove

    function keys() {
      return array( 'MODULE_PAYMENT_CCBILL_STATUS',
                    'MODULE_PAYMENT_CCBILL_ClientAccountNo',
                    'MODULE_PAYMENT_CCBILL_ClientSubAccountNo',
                    'MODULE_PAYMENT_CCBILL_FormName',
                    'MODULE_PAYMENT_CCBILL_IsFlexForm',
                    'MODULE_PAYMENT_CCBILL_Currency',
                    'MODULE_PAYMENT_CCBILL_Salt',
                    'MODULE_PAYMENT_CCBILL_ORDER_STATUS_ID'
                  );
    }// end keys

  }// end class ccbill
?>
