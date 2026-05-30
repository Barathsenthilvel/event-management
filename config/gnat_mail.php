<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin notification inbox(es)
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of addresses that receive GNAT admin templates.
    | Falls back to CONTACT_FORM_TO, then MAIL_FROM_ADDRESS, then homepage contact email.
    |
    */

    'admin_addresses' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('GNAT_ADMIN_MAIL', ''))
    ))),

    'logo_url' => env(
        'GNAT_MAIL_LOGO_URL',
        'https://via.placeholder.com/140x60/fddc6a/311742?text=GNAT+LOGO'
    ),

    'portal_member_url' => env('GNAT_MEMBER_PORTAL_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Membership lifecycle
    |--------------------------------------------------------------------------
    */

    'renewal_reminder_days_before' => (int) env('GNAT_RENEWAL_REMINDER_DAYS', 14),

    'inactive_after_subscription_days' => (int) env('GNAT_INACTIVE_AFTER_DAYS', 90),

];
