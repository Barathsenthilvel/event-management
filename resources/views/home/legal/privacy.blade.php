@extends('home.layouts.legal-document')

@section('document_title', 'Privacy Policy')

@section('document_body')
    <p>GNAT Association (&ldquo;GNAT&rdquo;, &ldquo;we&rdquo;, &ldquo;our&rdquo;, &ldquo;us&rdquo;) is committed to protecting the privacy of its members, donors, and website users.</p>

    <section>
        <h2 id="collect">1. Information We Collect</h2>
        <p>We may collect the following information:</p>
        <ul>
            <li>Personal details (Name, Email ID, Phone Number, Address)</li>
            <li>KYC documents (ID proof, organization documents where applicable)</li>
            <li>Payment information (processed via secure third-party gateways)</li>
            <li>Communication data (emails, SMS, WhatsApp interactions)</li>
            <li>Usage data (cookies, IP address, browser details)</li>
        </ul>
    </section>

    <section>
        <h2 id="use">2. How We Use Your Information</h2>
        <p>We use your information to:</p>
        <ul>
            <li>Process membership applications and approvals</li>
            <li>Verify KYC documents</li>
            <li>Manage memberships and renewals</li>
            <li>Facilitate event participation and issue certificates</li>
            <li>Share job opportunities (for members only)</li>
            <li>Process donations</li>
            <li>Conduct internal activities (polls, meetings, nominations)</li>
            <li>Send notifications via Email, SMS, and WhatsApp</li>
        </ul>
    </section>

    <section>
        <h2 id="sharing">3. Data Sharing</h2>
        <p>We do not sell your data. We may share data with:</p>
        <ul>
            <li>Payment gateway providers</li>
            <li>Communication service providers (SMS, WhatsApp, Email)</li>
            <li>Legal authorities when required</li>
        </ul>
    </section>

    <section>
        <h2 id="security">4. Data Security</h2>
        <p>We implement reasonable security measures to protect your data. However, no online platform is 100% secure.</p>
    </section>

    <section>
        <h2 id="cookies">5. Cookies</h2>
        <p>Our website may use cookies to improve user experience and analyze traffic.</p>
    </section>

    <section>
        <h2 id="retention">6. Data Retention</h2>
        <p>We retain your data as long as your membership is active and as required by law.</p>
    </section>

    <section>
        <h2 id="rights">7. Your Rights</h2>
        <p>You may request:</p>
        <ul>
            <li>Access to your data</li>
            <li>Correction of inaccurate data</li>
            <li>Account deactivation (subject to legal obligations)</li>
        </ul>
    </section>

    <section>
        <h2 id="contact-privacy">8. Contact Us</h2>
        <p>For privacy-related concerns, please email us at <a href="mailto:{{ $contact['email'] }}" class="font-semibold text-[#965995] hover:text-[#351c42] underline decoration-[#351c42]/20 underline-offset-2">{{ $contact['email'] }}</a>.</p>
    </section>
@endsection
