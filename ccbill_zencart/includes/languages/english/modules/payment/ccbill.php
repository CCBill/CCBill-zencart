<?php
/**
 * @package languageDefines
 * @copyright Copyright 2014-2021 CCBill
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version: 1.0
 * ------
 */
  define('MODULE_PAYMENT_CCBILL_TEXT_ADMIN_TITLE', 'CCBill Payments');
  define('MODULE_PAYMENT_CCBILL_TEXT_ADMIN_TITLE_NONUSA', 'CCBill Payments');
  define('MODULE_PAYMENT_CCBILL_TEXT_CATALOG_TITLE', 'CCBill');
  
  if (IS_ADMIN_FLAG === true) {
    define('MODULE_PAYMENT_CCBILL_TEXT_DESCRIPTION', '<strong>CCBill Payments Standard</strong>' );
  } else {
    define('MODULE_PAYMENT_CCBILL_TEXT_DESCRIPTION', '<strong>CCBill</strong>');
  }
  
  define('MODULE_PAYMENT_CCBILL_MARK_BUTTON_IMG', 'https://www.ccbill.com/common/img/logo_CCBill_home.png');
  define('MODULE_PAYMENT_CCBILL_MARK_BUTTON_ALT', 'Pay with Credit Card via CCBill');
  define('MODULE_PAYMENT_CCBILL_ACCEPTANCE_MARK_TEXT', 'Save time. Check out securely. <br />Pay via credit card with CCBill.');

  define('MODULE_PAYMENT_CCBILL_TEXT_CATALOG_LOGO', '<img src="' . MODULE_PAYMENT_CCBILL_MARK_BUTTON_IMG . '" alt="' . MODULE_PAYMENT_CCBILL_MARK_BUTTON_ALT . '" title="' . MODULE_PAYMENT_CCBILL_MARK_BUTTON_ALT . '" /> &nbsp;' .
                                                    '<span class="smallText">' . MODULE_PAYMENT_CCBILL_ACCEPTANCE_MARK_TEXT . '</span>');

  define('MODULE_PAYMENT_CCBILL_ENTRY_FIRST_NAME', 'First Name:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_LAST_NAME', 'Last Name:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_BUSINESS_NAME', 'Business Name:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_ADDRESS_NAME', 'Address Name:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_ADDRESS_STREET', 'Address Street:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_ADDRESS_CITY', 'Address City:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_ADDRESS_STATE', 'Address State:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_ADDRESS_ZIP', 'Address Zip:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_ADDRESS_COUNTRY', 'Address Country:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_EMAIL_ADDRESS', 'Payer Email:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_EBAY_ID', 'Ebay ID:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_PAYER_ID', 'Payer ID:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_PAYER_STATUS', 'Payer Status:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_ADDRESS_STATUS', 'Address Status:');

  define('MODULE_PAYMENT_CCBILL_ENTRY_PAYMENT_TYPE', 'Payment Type:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_PAYMENT_STATUS', 'Payment Status:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_PENDING_REASON', 'Pending Reason:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_INVOICE', 'Invoice:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_PAYMENT_DATE', 'Payment Date:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_CURRENCY', 'Currency:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_GROSS_AMOUNT', 'Gross Amount:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_PAYMENT_FEE', 'Payment Fee:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_EXCHANGE_RATE', 'Exchange Rate:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_CART_ITEMS', 'Cart items:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_TXN_TYPE', 'Trans. Type:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_TXN_ID', 'Trans. ID:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_PARENT_TXN_ID', 'Parent Trans. ID:');
  define('MODULE_PAYMENT_CCBILL_ENTRY_COMMENTS', 'System Comments: ');


  define('MODULE_PAYMENT_CCBILL_PURCHASE_DESCRIPTION_TITLE', 'All the items in your shopping basket (see details in the store and on your store receipt).');
  define('MODULE_PAYMENT_CCBILL_PURCHASE_DESCRIPTION_ITEMNUM', STORE_NAME . ' Purchase');
  define('MODULES_PAYMENT_PAYPALSTD_LINEITEM_TEXT_ONETIME_CHARGES_PREFIX', 'One-Time Charges related to ');
  define('MODULES_PAYMENT_PAYPALSTD_LINEITEM_TEXT_SURCHARGES_SHORT', 'Surcharges');
  define('MODULES_PAYMENT_PAYPALSTD_LINEITEM_TEXT_SURCHARGES_LONG', 'Handling charges and other applicable fees');
  define('MODULES_PAYMENT_PAYPALSTD_LINEITEM_TEXT_DISCOUNTS_SHORT', 'Discounts');
  define('MODULES_PAYMENT_PAYPALSTD_LINEITEM_TEXT_DISCOUNTS_LONG', 'Credits applied, including discount coupons, gift certificates, etc');

  

  define('MODULE_PAYMENT_CCBILL_TEXT_TITLE', 'Pay by Credit Card with CCBill');
  define('MODULE_PAYMENT_CCBILL_TEXT_DESCRIPTION', 'Payments by Credit Card via CCBill');
  define('MODULE_PAYMENT_CCBILL_TEXT_TYPE', 'Type:');
  define('MODULE_PAYMENT_CCBILL_TEXT_CREDIT_CARD_OWNER', 'Name on Card:');
  define('MODULE_PAYMENT_CCBILL_TEXT_CREDIT_CARD_NUMBER', 'Credit Card Number:');
  define('MODULE_PAYMENT_CCBILL_TEXT_CREDIT_CARD_CVV', 'CVV Code:');
  define('MODULE_PAYMENT_CCBILL_TEXT_CREDIT_CARD_EXPIRES', 'Credit Card Expiry Date:');
  define('MODULE_PAYMENT_CCBILL_TEXT_JS_CC_OWNER', '* The owner\'s name of the credit card must be at least ' . CC_OWNER_MIN_LENGTH . ' characters.\n');
  define('MODULE_PAYMENT_CCBILL_TEXT_JS_CC_NUMBER', '* The credit card number must be at least ' . CC_NUMBER_MIN_LENGTH . ' characters.\n');
  define('MODULE_PAYMENT_CCBILL_TEXT_ERROR_MESSAGE', 'There has been an error processing your credit card. Please try again.');
  define('MODULE_PAYMENT_CCBILL_TEXT_DECLINED_MESSAGE', 'Your credit card was declined. Please try another card or contact your bank for more info.');
  define('MODULE_PAYMENT_CCBILL_TEXT_ERROR', 'Credit Card Error!');
?>
