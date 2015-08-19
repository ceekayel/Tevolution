<?php
/*
 * common variable used for price packae and transaction
 */
define('PACKAGES_TITLE',__('Manage Price Packages','templatic-admin'));
define('ADD_A_PACKAGE_LINK',__('Add New Package','templatic-admin'));
define('MONETIZATION_SETTINGS',__('Monetization Settings','templatic-admin'));
define('ADD_NEW',__('Add New Package','templatic-admin'));
define('ADD_NEW_DESC',__('Here are the currency settings where you can set the currency code, currency symbol and currency position for your domain','templatic-admin'));
define('YES',__('Yes','templatic-admin'));
define('NO',__('No','templatic-admin'));
define('ACTIVE',__('Active','templatic-admin'));
define('INACTIVE',__('Inactive','templatic-admin'));
define('REQUIRED_TEXT',__('*','templatic-admin'));
define('PACKAGE_TITLE',__('Title','templatic-admin'));
define('PACKAGE_NAME_DESC',__('The package name will be shown inside the submission form. Feel free to get creative','templatic-admin'));
define('PER_POST_DESC',__(' Use Single submission if you want users to post once with this package. Use &raquoSubscription&raquo  to allow users to post a certain number of posts with this package.','templatic-admin'));
define('PER_SUBSCRIPTION_DESC',__('This option creates a subscription that allows members to submit a preset number of listings in a set amount of time.','templatic-admin'));
define('PACKAGE_CATEGORIES_DESC',__('This package will be displayed for the selected categories','templatic-admin'));
define('PACKAGE_DESC_TITLE',__('Package Description','templatic-admin'));
define('PACKAGE_DESC',__('In a few words, describe what this packages offers','templatic-admin'));
define('SHOW_PACKAGE',__('Show Package','templatic-admin'));
define('SHOW_PACKAGE_TITLE',__('Show Package even when no category is selected','templatic-admin'));
define('SHOW_PACKAGE_DESC',__('Check the above box if you want the price package to show at all times','templatic-admin'));
define('SELECT_POST_TYPES',__('Select Post Type','templatic-admin'));
define('POST_TYPE_DESC',__('This package will be enabled for the selected post type','templatic-admin'));
define('PACKAGE_AMOUNT',__('Amount','templatic-admin'));
define('PRICE_AMOUNT_DESC',__("This is the price which will be the cost to submit on this package. Do not enter thousand separators. Use the dot (.) as the decimal separator (if necessary). <strong>Tip</strong>: Enter 0 to make the package free",'templatic-admin'));
define('BILLING_PERIOD',__('Package Duration','templatic-admin'));
define('BILLING_PERIOD_DESC',__('Enter the duration in number of days, months or years for this package.','templatic-admin'));
define('DAYS_TEXT',__('Days','templatic-admin'));
define('MONTHS_TEXT',__('Months','templatic-admin'));
define('YEAR_TEXT',__('Years','templatic-admin'));
define('PACKAGE_STATUS',__('Enable Package','templatic-admin'));
define('IS_RECURRING',__('Recurring package','templatic-admin'));
define('RECURRING_DESC',__('If "Yes" is selected, Listing owners will be billed automatically as soon as the price package\'s billing period expires. ','templatic-admin'));
define('CHARGE_USER',__('Charge users every','templatic-admin'));
define('RECURRING_BILLING_PERIOD',__('Billing Period for Recurring package','templatic-admin'));
define('RECURRING_BILLING_PERIOD_DESC',__('Time between each billing','templatic-admin'));
define('RECURRING_BILLING_CYCLE',__('Billing Cycle','templatic-admin'));
define('RECURRING_BILLING_CYCLE_DESC',__('The number of times members will be billed, i.e. the number of times the process will be repeated. <strong>Note:</strong> With PayPal the number of payment cycles can range from 2 to 52.','templatic-admin'));
define('SETTINGS_FOR_FEATURED',__('Settings For Featured Entries','templatic-admin'));
define('SETTINGS_FOR_FEATURED_DESC',__('These settings are must if you want to charge the users for featured posts otherwise leave them blank','templatic-admin'));
define('SETTINGS_FOR_THOUGHTFUL_COMMENT',__('Comment Moderation','templatic-admin'));
define('SETTINGS_FOR_THOUGHTFUL_COMMENT_DESC',__('Allows people to moderate comments on your site which came on the entries they submitted','templatic-admin'));
define('CAN_AUTHOR_MODERATE',__('Allow author to moderate comments?','templatic-admin'));
define('THOUGHTFUL_COMMENT_STATUS_DESC',__('Select this to allow listing authors to moderate reviews on the listings they submit using this price package','templatic-admin'));
define('THOUGHTFUL_COMMENT_CHARGE',__('Amount to be charged for Comment moderation','templatic-admin'));
define('IS_FEATURED',__('Featured options','templatic-admin'));
define('FEATURED_HOME_PAGE_ALIVE_DAYS',__('Featured status duration','templatic-admin'));
define('FEATURED_CATEGORY_PAGE_ALIVE_DAYS',__('Featured status duration','templatic-admin'));
define('FEATURED_STATUS_DESC',__('Select either or both to allow listing submitters to make their listing featured for an additional cost. You can also make the package have all listings in it featured by default.','templatic-admin'));
define('HOME_PAGE_FEATURED_STATUS_DESC',__('Check the above option to define additional prices for featuring the listing on homepage page','templatic-admin'));
define('FEATURED_AMOUNT_HOME',__('Homepage featured price','templatic-admin'));
define('CATEGORY_PAGE_FEATURED_STATUS_DESC',__('Check the above option to define additional prices for featuring the listing on category page','templatic-admin'));
define('FEATURED_AMOUNT_HOME_DESC',__('Mention the amount to charge extra for home page featured posts','templatic-admin'));
define('FEATURED_AMOUNT_CAT',__('Category page featured price','templatic-admin'));
define('FEATURED_AMOUNT_CAT_DESC',__('Mention the amount to charge extra for category listing page featured posts','templatic-admin'));
define('BACK_LINK_TEXT',__('Back to packages list','templatic-admin'));
define('ACTION',__('Action','templatic-admin'));
define('PACKAGE_DETAILS',__('Package Detail','templatic-admin'));
define('EDIT_PACKAGE',__('Edit Package','templatic-admin'));
define('DELETE_PACKAGE',__('Delete Package','templatic-admin'));
define('NO_PACKAGE_AVAIL',__('You have not inserted any package yet','templatic-admin'));
define('PACKAGE_TYPE',__('Package Type','templatic-admin'));
define('PAY_PER_POST',__('Single Submission','templatic-admin'));
define('PAY_PER_SUB',__('Subscription','templatic-admin'));
define('LIMIT_NO_POST',__('Number of Posts','templatic-admin'));
define('NO_POST_DESC',__('Enter the number of posts members can submit with this price package, e.g. 10','templatic-admin'));
define('VALIDITY_TEXT',__('Validity','templatic-admin'));
define('CURRENCY_SYMB',__('Currency','templatic-admin'));
define('CURRENCY_SYMB_DESC',__('You can specify the currency symbol here','templatic-admin'));
define('CURRENCY_CODE',__('Currency Code','templatic-admin'));
define('CURRENCY_CODE_DESC',__('Not sure which code to enter? Click <a href="http://www.xe.com/iso4217.php" title="Currency Codes" target="_blank">here</a>','templatic-admin'));
define('CURRENCY_POS',__('Currency Position','templatic-admin'));
define('CURRENCY_POS_DESC',__('You can set the currency position from here','templatic-admin'));
define('SYMB_BFR_AMT',__('Use currency symbol as prefix','templatic-admin'));
define('SPACE_BET_BFR_AMT',__('Space between amount and prefixed symbol','templatic-admin'));
define('SYM_AFTR_AMT',__('Use currency symbol as Suffix','templatic-admin'));
define('SPACE_BET_AFTR_AMT',__('Space between amount and suffixed symbol','templatic-admin'));
define('FEATURED_TITLE',__('Would you like to makes this post Featured?','templatic-admin'));
define('FEATURED_HOME',__('Make this post featured on Home page','templatic-admin'));
define('FEATURED_CAT',__('Make this post featured on Category listing page','templatic-admin'));
define('MODERATE_COMMENT',__('Allow me to moderate comments of this entry.','templatic-admin'));
define('FEATURED_MSG_DESC',__('An additional charges will be applied to make this post featured on any of the pages','templatic-admin'));
define('TOTAL_CHARGES_TEXT',__('Total Charges','templatic-admin'));
define('FEATURE_HEAD_TITLE',__('Price settings for Featured listings','templatic-admin')); 
define('FEATURE_HEAD_NOTE',__('<p class="notes_spec">You might want to charge users extra amount for featured listings within this price package.</p> ','templatic-admin')); 
define('FEATURE_STATUS_NOTE',__('Activate/deactivate additional price settings for &raquo featured &raquo listings.','templatic-admin')); 
define('FEATURE_AMOUNT_TITLE',__('Amount to be charged<br><small>(for featuring on homepage)</small>','templatic-admin')); 
define('FEATURE_AMOUNT_NOTE',__('This is the price you wish to charge extra for featuring a place or an event listing on the homepage. eg. 20','templatic-admin')); 
define('FEATURE_CAT_TITLE',__('Amount to be charged<br><small>(for featuring on categories page)</small>','templatic-admin')); 
define('FEATURE_CAT_NOTE',__('This is the price you wish to charge extra for a place or an event listing on category pages. eg. 12','templatic-admin')); 
define('STATUS_NOTE',__('This setting will activate / deactivate this price package.','templatic-admin'));
define('RECENT_TRANSACTION_TEXT',__('Latest Transactions Report','templatic-admin'));

/*submit page price packages */
define('SUBMIT_LISTING_DAYS_TEXT',__('You are going to submit a post for %s days','templatic-admin'));
define('ENTER_EVENT',__('1. Enter Event Listing Details','templatic-admin'));
define('PREVIEW_EVENT',__('2. Preview Event &amp; Payment','templatic-admin'));
define('SUCCESS_EVENT',__('3. Event Successful','templatic-admin'));
define('FEATURED_H',__('Yes &sbquo; feature this listing on homepage.','templatic-admin'));
define('FEATURED_C',__('Yes &sbquo; feature this listing on category pages.','templatic-admin'));
define('PRICE_PACKAGE_ERROR',__('Please Select Price Package','templatic-admin'));
define('FEATURED_C_EVENT',__('Yes &sbquo; feature this event on category pages.','templatic-admin'));
define('TOTAL_TEXT',__('Total price as per your selection.','templatic-admin'));
define('ORDER_STATUS_TITLE',__('Order Status','templatic-admin'));

/* PAYMENT OPTIONS */
define('APPROVED_TEXT',__('Approve','templatic-admin'));
define('PENDING_MONI',__('Pending','templatic-admin'));
define('PAYMENT_SUCCESS_TITLE',__('Payment Success','templatic-admin'));
define('PAYMENT_CANCEL_TITLE',__('Payment Success','templatic-admin'));
define('BACK_TO_OREDR_TITLE',__('Back to Orders Listing','templatic-admin'));
define('ORDER_STATUS_SAVE_MSG',__('Order status changed successfully','templatic-admin'));
define('PAYMENT_SUCCESS_MSG','<h4>'.__("Your payment has been successfully received. The submitted content is now published.",'templatic-admin').'</h4><p><a href="[#submited_information_link#]" >'.__("View your submitted information",'templatic-admin').'</a></p><h5>'.__("Thank you for participating at",'templatic-admin').' [#site_name#].</h5>');
define('INVALID_TRANSACTION_TITLE',__('Invalid transaction','templatic-admin'));
define('INVALID_TRANSACTION_CONTENT',__('There is some error occurred while transaction is being process, please try again.','templatic-admin'));
define('AUTHENTICATION_CONTENT',__('Authentication fail, invalid transaction id generated.','templatic-admin'));
define('PAY_CANCEL_MSG',__('Your request has been cancelled.','templatic'));
define('NEW_PAYMENT_GATEWAY_TITLE',__('Install New Payment Gateway','templatic-admin'));
define('ORDER_UPDATE_TITLE',__('Update','templatic-admin'));
define('MAIL_TO_FRIEND',__('Mail to friend','templatic-admin'));
define('SEND_INQUIRY',__('Send inquiry','templatic-admin'));

/*Transaction Report*/ 
define('TRANSACTION_REPORT_TEXT',__('Transaction Report','templatic-admin'));
define('BACK_TO_TRANSACTION_LINK',__('Back to transaction list','templatic-admin'));
define('COLOR',__('Color','templatic-admin'));
define('ORDER_CANCEL_TEXT',__('Cancel','templatic-admin'));


define('PAID_AMOUNT',__('Paid amount','templatic-admin'));
define('PAYMENT_METHOD',__('Payment Method','templatic-admin'));
define('Status',__('Status','templatic-admin'));
?>