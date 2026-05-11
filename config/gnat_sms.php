<?php

return [

    /*
    |--------------------------------------------------------------------------
    | GNAT SMS (MSG91)
    |--------------------------------------------------------------------------
    |
    | Set GNAT_SMS_DRIVER=msg91 after you register DLT templates in MSG91 and
    | map each scenario to a Flow ID from the MSG91 dashboard. Variables are
    | sent as VAR1, VAR2, VAR3 in the same order as the {#var#} placeholders
    | in your approved template text.
    |
    | Drivers: off | log | msg91
    |
    */

    'driver' => env('GNAT_SMS_DRIVER', 'off'),

    'authkey' => env('GNAT_MSG91_AUTHKEY', ''),

    'sender' => env('GNAT_MSG91_SENDER_ID', ''),

    'default_country_code' => env('GNAT_SMS_DEFAULT_COUNTRY', '91'),

    'flow_url' => env('GNAT_MSG91_FLOW_URL', 'https://api.msg91.com/api/v5/flow/'),

    /**
     * MSG91 Flow ID per scenario (same wording as GNAT SMS templates).
     * Leave null until templates are approved in MSG91.
     *
     * @var array<string, string|null>
     */
    'flow_ids' => [
        's01_registration_complete' => env('GNAT_SMS_FLOW_S01'),
        's02_profile_submitted' => env('GNAT_SMS_FLOW_S02'),
        's03_profile_verified' => env('GNAT_SMS_FLOW_S03'),
        's04_profile_rejected' => env('GNAT_SMS_FLOW_S04'),
        's05_membership_payment_received' => env('GNAT_SMS_FLOW_S05'),
        's06_membership_expiry_reminder' => env('GNAT_SMS_FLOW_S06'),
        's07_membership_expired' => env('GNAT_SMS_FLOW_S07'),
        's08_account_inactive_90_days' => env('GNAT_SMS_FLOW_S08'),
        's09_membership_cancellation' => env('GNAT_SMS_FLOW_S09'),
        's11_meeting_scheduled' => env('GNAT_SMS_FLOW_S11'),
        's12_meeting_attendance_confirmed' => env('GNAT_SMS_FLOW_S12'),
        's13_meeting_non_attendance' => env('GNAT_SMS_FLOW_S13'),
        's14_new_event' => env('GNAT_SMS_FLOW_S14'),
        's15_event_interest' => env('GNAT_SMS_FLOW_S15'),
        's16_event_participation' => env('GNAT_SMS_FLOW_S16'),
        's17_nomination_live' => env('GNAT_SMS_FLOW_S17'),
        's18_nomination_submitted' => env('GNAT_SMS_FLOW_S18'),
        's19_polling_live' => env('GNAT_SMS_FLOW_S19'),
        's20_polling_results' => env('GNAT_SMS_FLOW_S20'),
        's21_polling_response' => env('GNAT_SMS_FLOW_S21'),
        's22_job_posting' => env('GNAT_SMS_FLOW_S22'),
        's23_job_application_submitted' => env('GNAT_SMS_FLOW_S23'),
        's24_job_request_reviewed' => env('GNAT_SMS_FLOW_S24'),
        's25_job_request_status_updated' => env('GNAT_SMS_FLOW_S25'),
        's26_job_request_communication' => env('GNAT_SMS_FLOW_S26'),
        's27_donation_received' => env('GNAT_SMS_FLOW_S27'),
        's28_support_request' => env('GNAT_SMS_FLOW_S28'),
    ],

];
