<?php
/*
 * common variable used for price packae and transaction
 */
define('PACKAGES_TITLE',__('Manage Price Packages',ADMINDOMAIN));
define('ADD_A_PACKAGE_LINK',__('Add New Package',ADMINDOMAIN));
define('MONETIZATION_SETTINGS',__('Monetization Settings',ADMINDOMAIN));
define('ADD_NEW',__('Add New Package',ADMINDOMAIN));
define('ADD_NEW_DESC',__('Here are the currency settings where you can set the currency code, currency symbol and currency position for your domain',ADMINDOMAIN));
define('YES',__('Yes',ADMINDOMAIN));
define('NO',__('No',ADMINDOMAIN));
define('ACTIVE',__('Active',ADMINDOMAIN));
define('INACTIVE',__('Inactive',ADMINDOMAIN));
define('REQUIRED_TEXT',__('*',ADMINDOMAIN));
define('PACKAGE_TITLE',__('Title',ADMINDOMAIN));
define('PACKAGE_NAME_DESC',__('The package name will be shown inside the submission form. Feel free to get creative',ADMINDOMAIN));
define('PER_POST_DESC',__(' Use Single submission if you want users to post once with this package. Use &raquoSubscription&raquo  to allow users to post a certain number of posts with this package.',ADMINDOMAIN));
define('PER_SUBSCRIPTION_DESC',__('This option creates a subscription that allows members to submit a preset number of listings in a set amount of time.',ADMINDOMAIN));
define('PACKAGE_CATEGORIES_DESC',__('This package will be displayed for the selected categories',ADMINDOMAIN));
define('PACKAGE_DESC_TITLE',__('Package Description',ADMINDOMAIN));
define('PACKAGE_DESC',__('In a few words, describe what this packages offers',ADMINDOMAIN));
define('SHOW_PACKAGE',__('Show Package',ADMINDOMAIN));
define('SHOW_PACKAGE_TITLE',__('Show Package even when no category is selected',ADMINDOMAIN));
define('SHOW_PACKAGE_DESC',__('Check the above box if you want the price package to show at all times',ADMINDOMAIN));
define('SELECT_POST_TYPES',__('Select Post Type',ADMINDOMAIN));
define('POST_TYPE_DESC',__('This package will be enabled for the selected post type',ADMINDOMAIN));
define('PACKAGE_AMOUNT',__('Amount',ADMINDOMAIN));
define('PRICE_AMOUNT_DESC',__("This is the price which will be the cost to submit on this package. Do not enter thousand separators. Use the dot (.) as the decimal separator (if necessary). <strong>Tip</strong>: Enter 0 to make the package free",ADMINDOMAIN));
define('BILLING_PERIOD',__('Package Duration',ADMINDOMAIN));
define('BILLING_PERIOD_DESC',__('Enter the duration in number of days, months or years for this package.',ADMINDOMAIN));
define('DAYS_TEXT',__('Days',ADMINDOMAIN));
define('MONTHS_TEXT',__('Months',ADMINDOMAIN));
define('YEAR_TEXT',__('Years',ADMINDOMAIN));
define('PACKAGE_STATUS',__('Enable Package',ADMINDOMAIN));
define('IS_RECURRING',__('Recurring package',ADMINDOMAIN));
define('RECURRING_DESC',__('If "Yes" is selected, Listing owners will be billed automatically as soon as the price package\'s billing period expires. ',ADMINDOMAIN));
define('CHARGE_USER',__('Charge users every',ADMINDOMAIN));
define('RECURRING_BILLING_PERIOD',__('Billing Period for Recurring package',ADMINDOMAIN));
define('RECURRING_BILLING_PERIOD_DESC',__('Time between each billing',ADMINDOMAIN));
define('RECURRING_BILLING_CYCLE',__('Billing Cycle',ADMINDOMAIN));
define('RECURRING_BILLING_CYCLE_DESC',__('The number of times members will be billed, i.e. the number of times the process will be repeated. <strong>Note:</strong> With PayPal the number of payment cycles can range from 2 to 52.',ADMINDOMAIN));
define('SETTINGS_FOR_FEATURED',__('Settings For Featured Entries',ADMINDOMAIN));
define('SETTINGS_FOR_FEATURED_DESC',__('These settings are must if you want to charge the users for featured posts otherwise leave them blank',ADMINDOMAIN));
define('SETTINGS_FOR_THOUGHTFUL_COMMENT',__('Comment Moderation',ADMINDOMAIN));
define('SETTINGS_FOR_THOUGHTFUL_COMMENT_DESC',__('Allows people to moderate comments on your site which came on the entries they submitted',ADMINDOMAIN));
define('CAN_AUTHOR_MODERATE',__('Allow author to moderate comments?',ADMINDOMAIN));
define('THOUGHTFUL_COMMENT_STATUS_DESC',__('Select this to allow listing authors to moderate reviews on the listings they submit using this price package',ADMINDOMAIN));
define('THOUGHTFUL_COMMENT_CHARGE',__('Amount to be charged for Comment moderation',ADMINDOMAIN));
define('IS_FEATURED',__('Featured options',ADMINDOMAIN));
define('FEATURED_HOME_PAGE_ALIVE_DAYS',__('Featured status duration',ADMINDOMAIN));
define('FEATURED_CATEGORY_PAGE_ALIVE_DAYS',__('Featured status duration',ADMINDOMAIN));
define('FEATURED_STATUS_DESC',__('Select either or both to allow listing submitters to make their listing featured for an additional cost. You can also make the package have all listings in it featured by default.',ADMINDOMAIN));
define('HOME_PAGE_FEATURED_STATUS_DESC',__('Check the above option to define additional prices for featuring the listing on homepage page',ADMINDOMAIN));
define('FEATURED_AMOUNT_HOME',__('Homepage featured price',ADMINDOMAIN));
define('CATEGORY_PAGE_FEATURED_STATUS_DESC',__('Check the above option to define additional prices for featuring the listing on category page',ADMINDOMAIN));
define('FEATURED_AMOUNT_HOME_DESC',__('Mention the amount to charge extra for home page featured posts',ADMINDOMAIN));
define('FEATURED_AMOUNT_CAT',__('Category page featured price',ADMINDOMAIN));
define('FEATURED_AMOUNT_CAT_DESC',__('Mention the amount to charge extra for category listing page featured posts',ADMINDOMAIN));
define('BACK_LINK_TEXT',__('Back to packages list',ADMINDOMAIN));
define('ACTION',__('Action',ADMINDOMAIN));
define('PACKAGE_DETAILS',__('Package Detail',ADMINDOMAIN));
define('EDIT_PACKAGE',__('Edit Package',ADMINDOMAIN));
define('DELETE_PACKAGE',__('Delete Package',ADMINDOMAIN));
define('NO_PACKAGE_AVAIL',__('You have not inserted any package yet',ADMINDOMAIN));
define('PACKAGE_TYPE',__('Package Type',ADMINDOMAIN));
define('PAY_PER_POST',__('Single Submission',ADMINDOMAIN));
define('PAY_PER_SUB',__('Subscription',ADMINDOMAIN));
define('LIMIT_NO_POST',__('Number of Posts',ADMINDOMAIN));
define('NO_POST_DESC',__('Enter the number of posts members can submit with this price package, e.g. 10',ADMINDOMAIN));
define('VALIDITY_TEXT',__('Validity',ADMINDOMAIN));
define('CURRENCY_SYMB',__('Currency',ADMINDOMAIN));
define('CURRENCY_SYMB_DESC',__('You can specify the currency symbol here',ADMINDOMAIN));
define('CURRENCY_CODE',__('Currency Code',ADMINDOMAIN));
define('CURRENCY_CODE_DESC',__('Not sure which code to enter? Click <a href="http://www.xe.com/iso4217.php" title="Currency Codes" target="_blank">here</a>',ADMINDOMAIN));
define('CURRENCY_POS',__('Currency Position',ADMINDOMAIN));
define('CURRENCY_POS_DESC',__('You can set the currency position from here',ADMINDOMAIN));
define('SYMB_BFR_AMT',__('Use currency symbol as prefix',ADMINDOMAIN));
define('SPACE_BET_BFR_AMT',__('Space between amount and prefixed symbol',ADMINDOMAIN));
define('SYM_AFTR_AMT',__('Use currency symbol as Suffix',ADMINDOMAIN));
define('SPACE_BET_AFTR_AMT',__('Space between amount and suffixed symbol',ADMINDOMAIN));
define('FEATURED_TITLE',__('Would you like to make this post Featured?',ADMINDOMAIN));
define('FEATURED_HOME',__('Make this post featured on Home page',ADMINDOMAIN));
define('FEATURED_CAT',__('Make this post featured on Category listing page',ADMINDOMAIN));
define('MODERATE_COMMENT',__('Allow me to moderate comments of this entry.',ADMINDOMAIN));
define('FEATURED_MSG_DESC',__('An additional charges will be applied to make this post featured on any of the pages',ADMINDOMAIN));
define('TOTAL_CHARGES_TEXT',__('Total Charges',ADMINDOMAIN));
define('FEATURE_HEAD_TITLE',__('Price settings for Featured listings',ADMINDOMAIN)); 
define('FEATURE_HEAD_NOTE',__('<p class="notes_spec">You might want to charge users extra amount for featured listings within this price package.</p> ',ADMINDOMAIN)); 
define('FEATURE_STATUS_NOTE',__('Activate/deactivate additional price settings for &raquo featured &raquo listings.',ADMINDOMAIN)); 
define('FEATURE_AMOUNT_TITLE',__('Amount to be charged<br><small>(for featuring on homepage)</small>',ADMINDOMAIN)); 
define('FEATURE_AMOUNT_NOTE',__('This is the price you wish to charge extra for featuring a place or an event listing on the homepage. eg. 20',ADMINDOMAIN)); 
define('FEATURE_CAT_TITLE',__('Amount to be charged<br><small>(for featuring on categories page)</small>',ADMINDOMAIN)); 
define('FEATURE_CAT_NOTE',__('This is the price you wish to charge extra for a place or an event listing on category pages. eg. 12',ADMINDOMAIN)); 
define('STATUS_NOTE',__('This setting will activate / deactivate this price package.',ADMINDOMAIN));
define('RECENT_TRANSACTION_TEXT',__('Latest Transactions Report',ADMINDOMAIN));

/*submit page price packages */
define('SUBMIT_LISTING_DAYS_TEXT',__('You are going to submit a post for %s days',ADMINDOMAIN));
define('ENTER_EVENT',__('1. Enter Event Listing Details',ADMINDOMAIN));
define('PREVIEW_EVENT',__('2. Preview Event &amp; Payment',ADMINDOMAIN));
define('SUCCESS_EVENT',__('3. Event Successful',ADMINDOMAIN));
define('FEATURED_H',__('Yes &sbquo; feature this listing on homepage.',ADMINDOMAIN));
define('FEATURED_C',__('Yes &sbquo; feature this listing on category pages.',ADMINDOMAIN));
define('PRICE_PACKAGE_ERROR',__('Please Select Price Package',ADMINDOMAIN));
define('FEATURED_C_EVENT',__('Yes &sbquo; feature this event on category pages.',ADMINDOMAIN));
define('TOTAL_TEXT',__('Total price as per your selection.',ADMINDOMAIN));
define('ORDER_STATUS_TITLE',__('Order Status',ADMINDOMAIN));

/* PAYMENT OPTIONS */
define('APPROVED_TEXT',__('Approve',ADMINDOMAIN));
define('PENDING_MONI',__('Pending',ADMINDOMAIN));
define('PAYMENT_SUCCESS_TITLE',__('Payment Success',ADMINDOMAIN));
define('PAYMENT_CANCEL_TITLE',__('Payment Success',ADMINDOMAIN));
define('BACK_TO_OREDR_TITLE',__('Back to Orders Listing',ADMINDOMAIN));
define('ORDER_STATUS_SAVE_MSG',__('Order status changed successfully',ADMINDOMAIN));
define('PAYMENT_SUCCESS_MSG','<h4>'.__("Your payment has been successfully received. The submitted content is now published.",ADMINDOMAIN).'</h4><p><a href="[#submited_information_link#]" >'.__("View your submitted information",ADMINDOMAIN).'</a></p><h5>'.__("Thank you for participating at",ADMINDOMAIN).' [#site_name#].</h5>');
define('INVALID_TRANSACTION_TITLE',__('Invalid transaction',ADMINDOMAIN));
define('INVALID_TRANSACTION_CONTENT',__('There is some error occurred while transaction is being process, please try again.',ADMINDOMAIN));
define('AUTHENTICATION_CONTENT',__('Authentication fail, invalid transaction id generated.',ADMINDOMAIN));
define('PAY_CANCEL_MSG',__('Your request has been cancelled.',DOMAIN));
define('NEW_PAYMENT_GATEWAY_TITLE',__('Install New Payment Gateway',ADMINDOMAIN));
define('ORDER_UPDATE_TITLE',__('Update',ADMINDOMAIN));
define('MAIL_TO_FRIEND',__('Mail to friend',ADMINDOMAIN));
define('SEND_INQUIRY',__('Send inquiry',ADMINDOMAIN));

/*Transaction Report*/ 
define('TRANSACTION_REPORT_TEXT',__('Transaction Report',ADMINDOMAIN));
define('BACK_TO_TRANSACTION_LINK',__('Back to transaction list',ADMINDOMAIN));
define('COLOR',__('Color',ADMINDOMAIN));
define('ORDER_CANCEL_TEXT',__('Cancel',ADMINDOMAIN));


define('PAID_AMOUNT',__('Paid amount',ADMINDOMAIN));
define('PAYMENT_METHOD',__('Payment Method',ADMINDOMAIN));
define('Status',__('Status',ADMINDOMAIN));
?>