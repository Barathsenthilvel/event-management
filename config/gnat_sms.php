<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GNAT SMS (MSG91)
    |--------------------------------------------------------------------------
    |
    | Set GNAT_SMS_DRIVER=msg91 after templates are registered in MSG91.
    | Variables are sent as var1, var2, var3 in the same order as {#var#}
    | placeholders in your approved MSG91 template text.
    |
    | Drivers: off | log | msg91
    |
    */

    'driver' => env('GNAT_SMS_DRIVER', 'off'),

    'authkey' => env('GNAT_MSG91_AUTHKEY', '514733A1dYnzpcHcfn6a299440P1'),

    'sender' => env('GNAT_MSG91_SENDER_ID', env('MSG91_SENDER_ID', 'GNATIN')),

    'default_country_code' => env('GNAT_SMS_DEFAULT_COUNTRY', '91'),

    'flow_url' => env('GNAT_MSG91_FLOW_URL', 'https://control.msg91.com/api/v5/flow'),

    'otp_url' => env('GNAT_MSG91_OTP_URL', 'https://control.msg91.com/api/v5/otp'),

    /**
     * MSG91 Flow ID for signup/login OTP (otpauthentication).
     * Template: var1 = member name, var2 = 4-digit OTP.
     */
    'otp_flow_id' => env('GNAT_SMS_OTP_FLOW_ID', '6a2c081b62dba7561a086de2'),

    /** MSG91 OTP API template_id (otpauthentication) */
    'otp_template_id' => env('GNAT_SMS_OTP_TEMPLATE_ID', '6a2c081b62dba7561a086de2'),

    /**
     * MSG91 Flow ID per template key (from MSG91 dashboard).
     * Leave empty until approved — SMS for that scenario is skipped.
     *
     * @var array<string, string|null>
     */
    'template_keys' => [
        'profilesubmission' => env('GNAT_SMS_TEMPLATE_PROFILESUBMISSION', '6a242c255f874bf1ba0adb72'),
        'contactsubmitted' => env('GNAT_SMS_TEMPLATE_CONTACTSUBMITTED', '6a242c6cf8dfd6a3160b83b3'),
        'pollresult' => env('GNAT_SMS_TEMPLATE_POLLRESULT', '6a264e4db6ba505dd005a422'),
        'nominationsubmitted' => env('GNAT_SMS_TEMPLATE_NOMINATIONSUBMITTED', '6a2651b5a61ad9670d0f0ff3'),
        'pollinglive' => env('GNAT_SMS_TEMPLATE_POLLINGLIVE', '6a265230842a20065a03f4d3'),
        'membershipcancellation' => env('GNAT_SMS_TEMPLATE_MEMBERSHIPCANCELLATION', '6a2652cb2f4c5df346069c52'),
        'meetingdeclined' => env('GNAT_SMS_TEMPLATE_MEETINGDECLINED', '6a265303c53813614905a602'),
        'jobstatusupdate' => env('GNAT_SMS_TEMPLATE_JOBSTATUSUPDATE', '6a2654c6c919153c0b0f4543'),
        'jobsubmitted' => env('GNAT_SMS_TEMPLATE_JOBSUBMITTED', '6a2654f0e8517be9bb037865'),
        'eventattended' => env('GNAT_SMS_TEMPLATE_EVENTATTENDED', '6a265576b9c624f7f60b2cd9'),
        'meetingreminder' => env('GNAT_SMS_TEMPLATE_MEETINGREMINDER', '6a2655cf0d0d0034b206493c'),
        'meetingalert' => env('GNAT_SMS_TEMPLATE_MEETINGALERT', '6a2656370ab3277ca6056c14'),
        'pollingsubmission' => env('GNAT_SMS_TEMPLATE_POLLINGSUBMISSION', '6a265673f12903530a09aa2a'),
        'profiledeclined' => env('GNAT_SMS_TEMPLATE_PROFILEDECLINED', '6a2656ac4f0828d7b70ade62'),
        'meetingconfirmation' => env('GNAT_SMS_TEMPLATE_MEETINGCONFIRMATION', '6a265703c4a8d27e6c002922'),
        'jobstatusreviewed' => env('GNAT_SMS_TEMPLATE_JOBSTATUSREVIEWED', '6a2657a3784b5b116708b622'),
        'eventalert' => env('GNAT_SMS_TEMPLATE_EVENTALERT', '6a265ab557a23f90af07fce2'),
        'meetingcancelled' => env('GNAT_SMS_TEMPLATE_MEETINGCANCELLED', '6a265b0f011e7cfe0b0d8ca2'),
        'eventreminder' => env('GNAT_SMS_TEMPLATE_EVENTREMINDER', '6a265b51a07d63a90e0a7d22'),
        'nominationreminder' => env('GNAT_SMS_TEMPLATE_NOMINATIONREMINDER', '6a265ba896320491490147e2'),
        'pollingreminder' => env('GNAT_SMS_TEMPLATE_POLLINGREMINDER', '6a265c144f0828d7b70ade64'),
        'paymentconfirmation' => env('GNAT_SMS_TEMPLATE_PAYMENTCONFIRMATION', '6a265c5c2a8b5067f5061132'),
        'donationpaid' => env('GNAT_SMS_TEMPLATE_DONATIONPAID', '6a265cdf4dcd760d5c019732'),
        'nominationalert' => env('GNAT_SMS_TEMPLATE_NOMINATIONALERT', '6a265d4eb29995d89509f95b'),
        'membershipinactive' => env('GNAT_SMS_TEMPLATE_MEMBERSHIPINACTIVE', '6a265da69606c3a57a002eb3'),
        'eventinterestsubmitted' => env('GNAT_SMS_TEMPLATE_EVENTINTERESTSUBMITTED', '6a265e072b9ac316d408a072'),
        'jobalert' => env('GNAT_SMS_TEMPLATE_JOBALERT', '6a265e451b823bc326066f64'),
        'otpauthentication' => env('GNAT_SMS_TEMPLATE_OTPAUTHENTICATION', '6a2c081b62dba7561a086de2'),
        'renewalalert' => env('GNAT_SMS_TEMPLATE_RENEWALALERT', '6a2c07b3f7291dd3fb0f41a2'),
        'expiryalert' => env('GNAT_SMS_TEMPLATE_EXPIRYALERT', '6a2c0745005cb1fd52082052'),

        // No MSG91 template provided yet — leave empty
        'registrationcomplete' => env('GNAT_SMS_TEMPLATE_REGISTRATIONCOMPLETE', ''),
        'profileverified' => env('GNAT_SMS_TEMPLATE_PROFILEVERIFIED', ''),
        'jobcommunication' => env('GNAT_SMS_TEMPLATE_JOBCOMMUNICATION', ''),
    ],

    /**
     * Maps internal scenario keys (used in code) to template_keys above.
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
    ],

];
