@extends('emails.gnat.layout')

@section('content')
@switch($templateKey)

@case('m01_registration_successful')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Welcome to GNAT Association.</p>
<p>Your registration has been completed successfully. We are delighted to have you as part of our growing professional community.</p>
<p>To help us serve you better and enable access to member services, kindly complete your profile information through the GNAT portal.</p>
<p>We look forward to your active participation with GNAT.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m02_profile_verification_pending')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Thank you for submitting your profile details to GNAT Association.</p>
<p>Your profile is currently under verification by our admin team. Once the review process is completed, you will receive further updates regarding your membership status.</p>
<p>We appreciate your patience and interest in being part of GNAT.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m03_profile_approved_subscription')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>We are pleased to inform you that your profile verification has been completed successfully.</p>
<p>You are now eligible to activate your GNAT membership account by completing the membership subscription process.</p>
<p>As an active member, you will gain access to meetings, networking opportunities, events, and member-exclusive activities.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m04_profile_verification_failed')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Thank you for your interest in joining GNAT Association.</p>
<p>At present, your profile verification could not be completed due to incomplete or mismatched information submitted during registration.</p>
<p>We request you to review the submitted details and contact GNAT support for further guidance.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m05_membership_activated')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Your membership payment has been received successfully and your GNAT membership account is now active.</p>
<p>We sincerely thank you for becoming a valued member of GNAT Association and look forward to your participation in our activities and initiatives.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m06_renewal_reminder')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>This is a friendly reminder that your GNAT membership validity is scheduled to expire on <strong>{{ $expiryDate }}</strong>.</p>
<p>To continue enjoying uninterrupted access to member benefits, meetings, events, and association activities, kindly renew your membership before the due date.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m07_membership_expired')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Your GNAT membership validity has expired on <strong>{{ $expiryDate }}</strong>.</p>
<p>We value your association with GNAT and encourage you to renew your membership to continue participating in our professional network and member activities.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m08_membership_cancellation')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Your membership subscription cancellation request has been processed successfully.</p>
<p>We sincerely value the time you have been associated with GNAT Association and hope to welcome you again in the future.</p>
<p>If you require any assistance or wish to reactivate your membership, kindly contact our support team.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m09_account_inactive_pending_subscription')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>We would like to inform you that your GNAT Association account is currently marked as inactive due to non-subscription for the past 90 days.</p>
<p>To continue accessing member benefits, meetings, networking opportunities, events, and association services, kindly renew your membership through the GNAT portal.</p>
<p>We value your association with GNAT and look forward to your continued participation in our community.</p>
<p>For any assistance regarding membership renewal, please contact the GNAT support team.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m10_meeting_schedule')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>A new GNAT Association meeting has been scheduled on <strong>{{ $meetingDate }}</strong> at <strong>{{ $meetingTime }}</strong>.</p>
<p>Your participation and presence are valuable to the community. Kindly login to the GNAT portal for complete meeting details and attendance confirmation.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m11_meeting_attendance_confirmed')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Thank you for confirming your attendance for the GNAT meeting scheduled on <strong>{{ $meetingDate }}</strong>.</p>
<p>We appreciate your participation and look forward to your presence during the meeting.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m12_meeting_non_attendance')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Your non-attendance response for the GNAT meeting scheduled on <strong>{{ $meetingDate }}</strong> has been recorded successfully.</p>
<p>Thank you for updating your availability. We look forward to your participation in upcoming GNAT activities.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m13_new_event')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>GNAT Association is pleased to announce a new upcoming event for members.</p>
<p>We invite you to participate and engage with fellow members through this initiative. Kindly login to the GNAT portal for complete event information and registration details.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m14_event_interest')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Thank you for showing interest in the GNAT event.</p>
<p>Your interest has been recorded successfully, and additional event updates and participation details will be shared through the GNAT portal.</p>
<p>We look forward to your participation.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m15_event_participation')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Thank you for attending the GNAT Association event and being part of the program.</p>
<p>Your participation has been recorded successfully. If applicable, your participation certificate is now available for download through the GNAT portal.</p>
<p>We appreciate your continued engagement with GNAT activities.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m16_nomination_live')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Nomination submissions are now open in GNAT Association.</p>
<p>Eligible members are invited to participate by submitting their nominations through the GNAT portal within the announced timeline.</p>
<p>We appreciate your active involvement in association activities.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m17_nomination_submitted')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Thank you for submitting your nomination in GNAT Association.</p>
<p>Your nomination has been received successfully and will be reviewed by the authorized team as per the association process.</p>
<p>We appreciate your participation and contribution.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m18_polling_live')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Polling activity is currently active in GNAT Association.</p>
<p>Your participation is valuable in helping us gather member opinions and improve association initiatives. Kindly submit your response through the GNAT portal.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m19_polling_response')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Thank you for participating in the GNAT Association polling activity.</p>
<p>Your response has been submitted successfully and your participation is highly valued in supporting association decisions and member engagement.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m20_polling_results')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Polling results have now been published in GNAT Association.</p>
<p>Thank you for your valuable participation. Kindly login to the GNAT portal to view the published results and related updates.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m21_job_posting')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>A new job opportunity has been published through GNAT Association.</p>
<p>We encourage you to explore the opportunity and submit your application if it matches your interests and qualifications.</p>
<p>Kindly login to the GNAT portal for complete details.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m22_job_application_confirmation')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Thank you for applying for the opportunity shared through GNAT Association.</p>
<p>Your application has been submitted successfully for the job Code <strong>{{ $jobCode }}</strong> and will be reviewed by the concerned team. Further communication will be shared based on the review process.</p>
<p>We wish you the very best.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m23_job_request_reviewed')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Your submitted job request has been reviewed successfully by the concerned team.</p>
<p>Further communication regarding the next stage of the process will be shared shortly. Thank you for your patience and continued interest.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m24_job_request_contact')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Communication has been initiated regarding your submitted job request.</p>
<p>Kindly check your registered contact details and remain available for further coordination and updates.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m25_job_application_selected')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Congratulations. Your job request status has been updated as selected.</p>
<p>Further instructions and onboarding-related communication will be shared shortly. We wish you continued success in this opportunity.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m26_donation_confirmation')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Thank you for your generous contribution to GNAT Association.</p>
<p>Your donation payment has been received successfully and your support is sincerely appreciated. Contributions from members like you help us strengthen community initiatives and association activities.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@case('m27_support_confirmation')
<p style="margin-top:0;">Dear <strong>{{ $memberName }},</strong></p>
<p>Thank you for contacting GNAT Association support.</p>
<p>Your request has been received successfully and our support team will review and respond to your query at the earliest possible time.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@break

@default
<p style="margin-top:0;">Dear <strong>{{ $memberName ?? 'Member' }},</strong></p>
<p>Please find this update from GNAT Association.</p>
<p>Warm Regards,<br><strong>GNAT Association</strong></p>
@endswitch
@endsection
