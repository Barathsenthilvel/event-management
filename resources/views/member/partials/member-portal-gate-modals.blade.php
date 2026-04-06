@if(!$member?->profile_completed)
    <div x-data x-cloak>
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl border-2 border-[#965995]/30 bg-white p-8 text-center shadow-2xl">
                <h2 class="text-2xl font-bold text-[#351c42]">Hello, {{ $firstName }}</h2>
                <p class="mt-4 text-sm leading-relaxed text-[#351c42]/75">Your profile is incomplete. Please complete it to be part of the GNAT member community.</p>
                <a href="{{ route('member.profile.edit') }}" class="mx-auto mt-8 inline-flex min-w-[10rem] items-center justify-center rounded-full bg-gradient-to-r from-[#351c42] to-[#4d2a5c] px-6 py-2.5 text-sm font-bold text-[#fddc6a] shadow-lg shadow-[#351c42]/25 transition hover:brightness-105">Update profile</a>
            </div>
        </div>
    </div>
@elseif(!$member?->is_approved)
    <div x-data="{ open: true }" x-cloak>
        <div x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">
            <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl border border-[#351c42]/10 bg-white p-8 shadow-2xl">
                <p class="text-xs font-bold uppercase tracking-widest text-[#965995]">Approval pending</p>
                <h3 class="mt-2 text-xl font-extrabold text-[#351c42]">Please wait for admin approval</h3>
                <p class="mt-3 text-sm text-[#351c42]/75">We received your profile. Once approved, you can purchase membership plans.</p>
                <div class="mt-6 flex flex-wrap justify-end gap-3">
                    <a href="{{ route('member.profile.edit') }}" class="rounded-full border border-[#351c42]/15 px-5 py-2.5 text-sm font-bold text-[#351c42] hover:bg-[#351c42]/5">Review profile</a>
                    <button type="button" @click="open = false" class="rounded-full bg-[#351c42] px-5 py-2.5 text-sm font-bold text-[#fddc6a]">OK</button>
                </div>
            </div>
        </div>
    </div>
@endif
