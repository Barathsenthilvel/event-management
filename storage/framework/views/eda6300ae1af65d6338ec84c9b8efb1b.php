<div class="mt-8 border-t border-[#351c42]/10 pt-4" x-data="{ logoutOpen: false }">
    <form x-ref="memberLogoutForm" method="POST" action="<?php echo e(route('member.logout')); ?>" class="hidden"><?php echo csrf_field(); ?></form>
    <button
        type="button"
        @click="logoutOpen = true"
        class="md-sidebar-link w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700"
    >
        <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span> Log out
    </button>
    
    <template x-teleport="body">
        <div
            x-show="logoutOpen"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm"
            role="dialog"
            aria-modal="true"
            aria-labelledby="member-logout-confirm-title"
        >
            <div class="absolute inset-0" @click="logoutOpen = false" aria-hidden="true"></div>
            <div class="relative z-10 w-full max-w-sm rounded-2xl border border-[#351c42]/10 bg-white p-6 shadow-2xl">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <h3 id="member-logout-confirm-title" class="mt-4 text-center text-lg font-bold text-[#351c42]">Log out?</h3>
                <p class="mt-2 text-center text-sm leading-relaxed text-[#351c42]/65">Are you sure you want to sign out of your member account?</p>
                <div class="mt-6 flex gap-3">
                    <button type="button" @click="logoutOpen = false" class="flex-1 rounded-xl border border-[#351c42]/15 py-2.5 text-sm font-bold text-[#351c42] transition hover:bg-[#351c42]/5">Cancel</button>
                    <button type="button" @click="$refs.memberLogoutForm.submit()" class="flex-1 rounded-xl bg-[#351c42] py-2.5 text-sm font-bold text-[#fddc6a] transition hover:brightness-105">Log out</button>
                </div>
            </div>
        </div>
    </template>
</div>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/member/partials/sidebar-logout.blade.php ENDPATH**/ ?>