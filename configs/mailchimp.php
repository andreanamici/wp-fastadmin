<?php

//https://us17.admin.mailchimp.com/account/api/

//Api key for mailchimp
define("WP_FA_MAILCHIMP_API_KEY", getenv('WP_FA_MAILCHIMP_API_KEY'));

//https://mandrillapp.com//settings
define("WP_FA_MAILCHIMP_TRANSACTIONAL_API_KEY", getenv('WP_FA_MAILCHIMP_TRANSACTIONAL_API_KEY'));

//To find the value for the server parameter used in mailchimp. setConfig , log into your Mailchimp account and look at the URL in your browser. 
//You'll see something like https://us19.admin.mailchimp.com/ ; the us19 part is the server prefix. Note that your specific value may be different.
define("WP_FA_MAILCHIMP_SERVER_PREFIX", getenv('WP_FA_MAILCHIMP_SERVER_PREFIX'));

//https://us17.admin.mailchimp.com/lists/settings/defaults?id=1356209
//Some plugins and integrations may request your Audience ID.
define("WP_FA_MAILCHIMP_DEFAULT_LIST_ID", getenv('WP_FA_MAILCHIMP_DEFAULT_LIST_ID'));

//Subscriber's current status. Possible values: "subscribed", "unsubscribed", "cleaned", "pending", or "transactional".
define("WP_FA_MAILCHIMP_SUBSCRIBER_DEFAULT_STATUS", "subscribed");