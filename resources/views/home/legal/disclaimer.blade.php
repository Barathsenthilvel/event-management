@extends('home.layouts.legal-document')

@section('document_title', 'Disclaimer')

@section('document_subtitle')
    Important information about how you use the GNAT platform and services.
@endsection

@section('document_body')
    <p>GNAT Association provides its platform and services on an &ldquo;as-is&rdquo; basis.</p>
    <ul>
        <li>GNAT does not guarantee approval of membership applications.</li>
        <li>GNAT does not guarantee job placement or employment.</li>
        <li>Event participation does not guarantee certification unless specified.</li>
        <li>GNAT is not responsible for accuracy of third-party content or opportunities.</li>
        <li>Donation usage is based on organizational objectives and may vary.</li>
    </ul>
    <p class="mt-6 rounded-2xl border border-[#351c42]/10 bg-[#f8f6fa] px-5 py-4 text-[#351c42]/90">
        Users are advised to verify information independently before making decisions.
    </p>
@endsection
