@extends('home.layouts.legal-document')

@section('document_title', 'Cancellation & Refund Policy')

@section('document_body')
    <section>
        <h2 id="membership-fees">1. Membership Fees</h2>
        <ul>
            <li>Membership fees (including registration fee) are non-refundable once paid.</li>
            <li>Membership is valid for one year from activation date.</li>
        </ul>
    </section>

    <section>
        <h2 id="renewal">2. Renewal</h2>
        <ul>
            <li>Renewal fees are non-refundable once processed.</li>
            <li>Failure to renew within 60 days will result in account deactivation.</li>
        </ul>
    </section>

    <section>
        <h2 id="donations-refund">3. Donations</h2>
        <ul>
            <li>Donations are voluntary and non-refundable.</li>
        </ul>
    </section>

    <section>
        <h2 id="exceptions">4. Exceptions</h2>
        <p>Refunds, if any, will be at the sole discretion of GNAT in exceptional cases.</p>
    </section>

    <section>
        <h2 id="contact-refund">5. Contact</h2>
        <p>For refund-related queries, please email <a href="mailto:{{ $contact['email'] }}" class="font-semibold text-[#965995] hover:text-[#351c42] underline decoration-[#351c42]/20 underline-offset-2">{{ $contact['email'] }}</a>.</p>
    </section>
@endsection
