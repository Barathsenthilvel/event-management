@extends('emails.gnat.layout')

@section('content')
@switch($templateKey)

@case('a01_profile_submitted')
<p style="margin-top:0;">Dear Admin,</p>
<p>A new GNAT member profile has been submitted and is pending verification.</p>
<p><strong>Submission Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Email Address: {{ $email }}<br>
• Mobile Number: {{ $mobile }}<br>
• Submitted On: {{ $submittedOn }}<br>
</p>
<p>Please review and verify the member profile through the GNAT Admin Panel.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a02_subscription_payment')
<p style="margin-top:0;">Dear Admin,</p>
<p>A GNAT member has successfully completed a membership subscription payment.</p>
<p><strong>Subscription Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Membership Type: {{ $membershipPlan }}<br>
• Transaction ID: {{ $transactionId }}<br>
• Amount Paid: {{ $amount }}<br>
• Payment Date: {{ $paymentDate }}<br>
</p>
<p>Please review the same.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a03_renewal_reminder_sent')
<p style="margin-top:0;">Dear Admin,</p>
<p>A membership renewal reminder has been sent to the GNAT member.</p>
<p><strong>Member Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Membership Expiry Date: {{ $expiryDate }}<br>
</p>
<p>Please monitor pending renewals through the admin dashboard.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a04_membership_expired')
<p style="margin-top:0;">Dear Admin,</p>
<p>A GNAT membership expiry notification has been sent to the following member.</p>
<p><strong>Member Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Membership ID: {{ $membershipId }}<br>
• Expired On: {{ $expiryDate }}<br>
</p>
<p>The member account may be restricted until renewal is completed.</p>
<p>Regards,<br><strong>GNAT Membership System</strong></p>
@break

@case('a05_account_inactive')
<p style="margin-top:0;">Dear Admin,</p>
<p>A GNAT member account has been marked as inactive due to pending subscription renewal.</p>
<p><strong>Member Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Membership ID: {{ $membershipId }}<br>
• Pending Since: {{ $pendingSince }}<br>
</p>
<p>Please review the account status if manual intervention is required.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a06_cancellation')
<p style="margin-top:0;">Dear Admin,</p>
<p>A GNAT membership cancellation has been completed successfully.</p>
<p><strong>Cancellation Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Membership ID: {{ $membershipId }}<br>
• Cancellation Date: {{ $cancellationDate }}<br>
</p>
<p>Please verify if any additional closure process is required.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a07_meeting_attendance_confirmed')
<p style="margin-top:0;">Dear Admin,</p>
<p>A GNAT member has confirmed attendance for the scheduled meeting.</p>
<p><strong>Meeting Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Meeting Title: {{ $meetingName }}<br>
• Meeting Date: {{ $meetingDate }}<br>
</p>
<p>Please update the meeting participation records.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a08_meeting_non_attendance')
<p style="margin-top:0;">Dear Admin,</p>
<p>A GNAT member has marked non-attendance for the scheduled meeting.</p>
<p><strong>Meeting Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Meeting Title: {{ $meetingName }}<br>
• Meeting Date: {{ $meetingDate }}<br>
</p>
<p>Please update the meeting participation records.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a09_event_interest')
<p style="margin-top:0;">Dear Admin,</p>
<p>A GNAT/Guest member has expressed interest in an upcoming event.</p>
<p><strong>Event Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Event Name: {{ $eventName }}<br>
• Event Date: {{ $eventDate }}<br>
</p>
<p>Please review participant interest and event engagement details.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a10_nomination_received')
<p style="margin-top:0;">Dear Admin,</p>
<p>A new GNAT nomination received from member.</p>
<p><strong>Nomination Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Nomination Category: {{ $category }}<br>
• Submitted On: {{ $submittedOn }}<br>
</p>
<p>Please review the nomination list.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a11_poll_response')
<p style="margin-top:0;">Dear Admin,</p>
<p>A GNAT member has submitted a polling response.</p>
<p><strong>Polling Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Poll Title: {{ $pollTitle }}<br>
• Submitted On: {{ $submittedOn }}<br>
</p>
<p>Please review polling analytics and responses in the admin dashboard.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a12_job_application')
<p style="margin-top:0;">Dear Admin,</p>
<p>A GNAT member has submitted a job application through the platform.</p>
<p><strong>Applicant Details:</strong></p>
<p style="margin:0;">
• Applicant Name: {{ $memberName }}<br>
• Job Title: {{ $jobTitle }}<br>
• Company: {{ $companyName }}<br>
• Application Date: {{ $applicationDate }}<br>
</p>
<p>Please review the submitted application in the admin portal.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a13_donation_received')
<p style="margin-top:0;">Dear Admin,</p>
<p>A new donation payment has been received through the GNAT platform.</p>
<p><strong>Donation Details:</strong></p>
<p style="margin:0;">
• Donor Name: {{ $donorName }}<br>
• Transaction ID: {{ $transactionId }}<br>
• Donation Amount: {{ $amount }}<br>
• Payment Date: {{ $paymentDate }}<br>
</p>
<p>Please verify the payment transaction records.</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@break

@case('a14_support_request')
<p style="margin-top:0;">Dear Admin,</p>
<p>A new support request has been submitted by a GNAT member.</p>
<p><strong>Support Details:</strong></p>
<p style="margin:0;">
• Member Name: {{ $memberName }}<br>
• Ticket ID: {{ $ticketId }}<br>
• Subject: {{ $supportSubject }}<br>
• Submitted On: {{ $submittedOn }}<br>
</p>
@if(!empty($supportBody))
<p><strong>Message:</strong></p>
<p style="white-space:pre-wrap; font-size:14px;">{{ $supportBody }}</p>
@endif
<p>Please review and assign the support request for resolution.</p>
<p>Regards,<br><strong>GNAT Support System</strong></p>
@break

@default
<p style="margin-top:0;">Dear Admin,</p>
<p>{{ $body ?? 'Please review this notification in the GNAT Admin Panel.' }}</p>
<p>Regards,<br><strong>GNAT Team</strong></p>
@endswitch
@endsection
