<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GNAT WhatsApp (MSG91)
    |--------------------------------------------------------------------------
    |
    | Mirrors gnat_sms.php template keys. Leave template IDs empty until the
    | client provides approved WhatsApp template names from MSG91 / Meta.
    | When you set GNAT_WHATSAPP_TEMPLATE_* in .env, notifications start sending.
    |
    | Drivers: off | log | msg91
    |
    */

    'driver' => env('GNAT_WHATSAPP_DRIVER', 'off'),

    'authkey' => env('GNAT_MSG91_AUTHKEY', env('GNAT_WHATSAPP_AUTHKEY', '')),

    /** WhatsApp Business integrated number (with country code, e.g. 9198xxxxxxxx) */
    'integrated_number' => env('GNAT_WHATSAPP_INTEGRATED_NUMBER', ''),

    'default_country_code' => env('GNAT_WHATSAPP_DEFAULT_COUNTRY', env('GNAT_SMS_DEFAULT_COUNTRY', '91')),

    'language' => env('GNAT_WHATSAPP_LANGUAGE', 'en'),

    'bulk_url' => env(
        'GNAT_WHATSAPP_BULK_URL',
        'https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk'
    ),

    /**
     * WhatsApp template name per key (MSG91 dashboard / Meta approved name).
     * Same keys as gnat_sms.php — update each when client provides templates.
     *
     * @var array<string, string|null>
     */
    'template_keys' => [
        'profilesubmission' => env('GNAT_WHATSAPP_TEMPLATE_PROFILESUBMISSION', ''),
        'contactsubmitted' => env('GNAT_WHATSAPP_TEMPLATE_CONTACTSUBMITTED', ''),
        'pollresult' => env('GNAT_WHATSAPP_TEMPLATE_POLLRESULT', ''),
        'nominationsubmitted' => env('GNAT_WHATSAPP_TEMPLATE_NOMINATIONSUBMITTED', ''),
        'pollinglive' => env('GNAT_WHATSAPP_TEMPLATE_POLLINGLIVE', ''),
        'membershipcancellation' => env('GNAT_WHATSAPP_TEMPLATE_MEMBERSHIPCANCELLATION', ''),
        'meetingdeclined' => env('GNAT_WHATSAPP_TEMPLATE_MEETINGDECLINED', ''),
        'jobstatusupdate' => env('GNAT_WHATSAPP_TEMPLATE_JOBSTATUSUPDATE', ''),
        'jobsubmitted' => env('GNAT_WHATSAPP_TEMPLATE_JOBSUBMITTED', ''),
        'eventattended' => env('GNAT_WHATSAPP_TEMPLATE_EVENTATTENDED', ''),
        'meetingreminder' => env('GNAT_WHATSAPP_TEMPLATE_MEETINGREMINDER', ''),
        'meetingalert' => env('GNAT_WHATSAPP_TEMPLATE_MEETINGALERT', ''),
        'pollingsubmission' => env('GNAT_WHATSAPP_TEMPLATE_POLLINGSUBMISSION', ''),
        'profiledeclined' => env('GNAT_WHATSAPP_TEMPLATE_PROFILEDECLINED', ''),
        'meetingconfirmation' => env('GNAT_WHATSAPP_TEMPLATE_MEETINGCONFIRMATION', ''),
        'jobstatusreviewed' => env('GNAT_WHATSAPP_TEMPLATE_JOBSTATUSREVIEWED', ''),
        'eventalert' => env('GNAT_WHATSAPP_TEMPLATE_EVENTALERT', ''),
        'meetingcancelled' => env('GNAT_WHATSAPP_TEMPLATE_MEETINGCANCELLED', ''),
        'eventreminder' => env('GNAT_WHATSAPP_TEMPLATE_EVENTREMINDER', ''),
        'nominationreminder' => env('GNAT_WHATSAPP_TEMPLATE_NOMINATIONREMINDER', ''),
        'pollingreminder' => env('GNAT_WHATSAPP_TEMPLATE_POLLINGREMINDER', ''),
        'paymentconfirmation' => env('GNAT_WHATSAPP_TEMPLATE_PAYMENTCONFIRMATION', ''),
        'donationpaid' => env('GNAT_WHATSAPP_TEMPLATE_DONATIONPAID', ''),
        'nominationalert' => env('GNAT_WHATSAPP_TEMPLATE_NOMINATIONALERT', ''),
        'membershipinactive' => env('GNAT_WHATSAPP_TEMPLATE_MEMBERSHIPINACTIVE', ''),
        'eventinterestsubmitted' => env('GNAT_WHATSAPP_TEMPLATE_EVENTINTERESTSUBMITTED', ''),
        'jobalert' => env('GNAT_WHATSAPP_TEMPLATE_JOBALERT', ''),
        'otpauthentication' => env('GNAT_WHATSAPP_TEMPLATE_OTPAUTHENTICATION', ''),
        'renewalalert' => env('GNAT_WHATSAPP_TEMPLATE_RENEWALALERT', ''),
        'expiryalert' => env('GNAT_WHATSAPP_TEMPLATE_EXPIRYALERT', ''),
        'eventcancel' => env('GNAT_WHATSAPP_TEMPLATE_EVENTCANCEL', ''),
        'jobcommunication' => env('GNAT_WHATSAPP_TEMPLATE_JOBCOMMUNICATION', ''),
        'registrationcomplete' => env('GNAT_WHATSAPP_TEMPLATE_REGISTRATIONCOMPLETE', ''),
        'profileverified' => env('GNAT_WHATSAPP_TEMPLATE_PROFILEVERIFIED', ''),
    ],

    /**
     * Same scenario → template_key mapping as gnat_sms.php.
     *
     * @var array<string, string|null>
     */
    'scenario_template_keys' => [
        's01_registration_complete' => 'registrationcomplete',
        's02_profile_submitted' => 'profilesubmission',
        's03_profile_verified' => 'profileverified',
        's04_profile_rejected' => 'profiledeclined',
        's05_membership_payment_received' => 'paymentconfirmation',
        's06_membership_expiry_reminder' => 'renewalalert',
        's07_membership_expired' => 'expiryalert',
        's08_account_inactive_90_days' => 'membershipinactive',
        's09_membership_cancellation' => 'membershipcancellation',
        's11_meeting_scheduled' => 'meetingalert',
        's12_meeting_attendance_confirmed' => 'meetingconfirmation',
        's13_meeting_non_attendance' => 'meetingdeclined',
        's14_new_event' => 'eventalert',
        's15_event_interest' => 'eventinterestsubmitted',
        's16_event_participation' => 'eventattended',
        's17_nomination_live' => 'nominationalert',
        's18_nomination_submitted' => 'nominationsubmitted',
        's19_polling_live' => 'pollinglive',
        's20_polling_results' => 'pollresult',
        's21_polling_response' => 'pollingsubmission',
        's22_job_posting' => 'jobalert',
        's23_job_application_submitted' => 'jobsubmitted',
        's24_job_request_reviewed' => 'jobstatusreviewed',
        's25_job_request_status_updated' => 'jobstatusupdate',
        's26_job_request_communication' => 'jobcommunication',
        's27_donation_received' => 'donationpaid',
        's28_support_request' => 'contactsubmitted',
        's29_meeting_reminder' => 'meetingreminder',
        's30_meeting_cancelled' => 'meetingcancelled',
        's31_event_reminder' => 'eventreminder',
        's32_nomination_reminder' => 'nominationreminder',
        's33_polling_reminder' => 'pollingreminder',
        's34_event_cancelled' => 'eventcancel',
    ],

];
