<?php $__env->startSection('content'); ?>
<?php
    $m = $member;
    $doc = fn ($path) => $path ? asset('storage/' . ltrim($path, '/')) : null;
    $showApprovalActions = $showApprovalActions ?? true;
    $backUrl = $backUrl ?? route('admin.members.pending-approvals.index', request()->only('q'));
    $backLabel = $backLabel ?? 'Back to pending list';
    $statusTitle = $m->is_approved ? 'Approved member' : 'Pending approval';
?>
<div class="flex-1 overflow-y-auto custom-scroll p-4 sm:p-6">
    
    <div class="mx-auto max-w-4xl">
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <a href="<?php echo e($backUrl); ?>"
               class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-extrabold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-[#351c42]/20">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <?php echo e($backLabel); ?>

            </a>
        </div>

        <div class="overflow-hidden rounded-[28px] border border-[#351c42]/12 bg-white shadow-[0_24px_80px_-12px_rgba(53,28,66,0.18)] ring-1 ring-black/5">
            
            <div class="relative bg-gradient-to-r from-[#351c42] via-[#4a2660] to-[#965995] px-6 py-8 text-white">
                <div class="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-[#fddc6a]/20 blur-3xl"></div>
                <div class="pointer-events-none absolute -bottom-10 left-10 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border-2 border-[#fddc6a]/40 bg-white/10 text-xl font-black tracking-tight text-[#fddc6a] shadow-inner">
                            <?php echo e(strtoupper(substr($m->name ?? 'ME', 0, 2))); ?>

                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-[0.25em] text-[#fddc6a]/90"><?php echo e($statusTitle); ?></p>
                            <h1 class="mt-1 text-xl font-extrabold tracking-tight sm:text-2xl"><?php echo e($m->name); ?></h1>
                            <p class="mt-1 text-xs font-semibold text-white/80"><?php echo e($m->email); ?></p>
                        </div>
                    </div>
                    <?php if($showApprovalActions): ?>
                    <div class="flex flex-wrap gap-2">
                        <form id="approve-member-form" method="POST" action="<?php echo e(route('admin.members.pending-approvals.approve', $m->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="button" data-open-approve-modal class="rounded-xl bg-emerald-500 px-5 py-2.5 text-xs font-extrabold text-white shadow-lg shadow-emerald-900/30 transition hover:bg-emerald-400">
                                Approve member
                            </button>
                        </form>
                        <form method="POST" action="<?php echo e(route('admin.members.pending-approvals.reject', $m->id)); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="rounded-xl border border-white/30 bg-white/10 px-5 py-2.5 text-xs font-extrabold text-white backdrop-blur transition hover:bg-white/20">
                                Reject
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid gap-6 p-6 sm:p-8 lg:grid-cols-2">
                <section class="rounded-2xl border border-slate-100 bg-gradient-to-b from-slate-50/80 to-white p-5 shadow-sm">
                    <h2 class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-[#965995]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span>
                        Personal
                    </h2>
                    <dl class="mt-4 space-y-3 text-xs">
                        <?php $__currentLoopData = [
                            'First name' => $m->first_name,
                            'Last name' => $m->last_name,
                            'Mobile' => $m->mobile,
                            'Date of birth' => $m->dob?->format('d M Y'),
                            'Gender' => $m->gender,
                            'Blood group' => $m->blood_group,
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between gap-4 border-b border-slate-100/80 pb-2 last:border-0 last:pb-0">
                                <dt class="font-bold text-slate-500"><?php echo e($label); ?></dt>
                                <dd class="max-w-[60%] text-right font-semibold text-slate-900"><?php echo e($val ?: '—'); ?></dd>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex justify-between gap-4 border-t border-slate-100/80 pt-3">
                            <dt class="font-bold text-slate-500">Designation</dt>
                            <dd class="max-w-[60%] text-right font-semibold text-slate-900"><?php echo e($m->designation?->name ?? '—'); ?></dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-gradient-to-b from-slate-50/80 to-white p-5 shadow-sm">
                    <h2 class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-[#965995]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span>
                        Professional &amp; address
                    </h2>
                    <dl class="mt-4 space-y-3 text-xs">
                        <?php $__currentLoopData = [
                            'Profile type' => $m->profile_type ? ucwords(str_replace('_', ' ', (string) $m->profile_type)) : null,
                            'Referred by' => $m->referrer?->name
                                ? $m->referrer->name . ($m->referrer->mobile ? ' (' . $m->referrer->mobile . ')' : '')
                                : null,
                            'Qualification' => $m->qualification,
                            'RNRM number & date' => $m->rnrm_number_with_date,
                            'Student ID' => $m->student_id,
                            'College' => $m->college_name,
                            'Door no.' => $m->door_no,
                            'Locality / area' => $m->locality_area,
                            'State' => $m->state,
                            'Country' => 'India',
                            'PIN code' => $m->pin_code,
                            'Council state' => $m->council_state,
                            'Currently working' => $m->currently_working,
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex justify-between gap-4 border-b border-slate-100/80 pb-2 last:border-0 last:pb-0">
                                <dt class="shrink-0 font-bold text-slate-500"><?php echo e($label); ?></dt>
                                <dd class="text-right font-semibold text-slate-900"><?php echo e($val ?: '—'); ?></dd>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </dl>
                </section>

                <section class="rounded-2xl border border-dashed border-[#351c42]/20 bg-[#faf8fc] p-5 lg:col-span-2">
                    <h2 class="flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-[#351c42]">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#351c42]"></span>
                        Documents
                    </h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <?php $__currentLoopData = [
                            'Educational certificate' => $doc($m->educational_certificate_path),
                            'RNRM certificate' => $doc($m->rnrm_certificate_path),
                            'Student ID card' => $doc($m->student_id_card_path),
                            'Aadhar card' => $doc($m->aadhar_card_path),
                            'Passport photo' => $doc($m->passport_photo_path),
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="rounded-xl border border-white bg-white p-4 shadow-sm">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-500"><?php echo e($label); ?></p>
                                <?php if($url): ?>
                                    <a href="<?php echo e($url); ?>" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex items-center gap-1.5 text-xs font-extrabold text-[#965995] hover:text-[#351c42]">
                                        View file
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                <?php else: ?>
                                    <p class="mt-2 text-[11px] font-bold text-slate-400">Not uploaded</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<?php if($showApprovalActions): ?>
<div id="approve-member-modal" class="fixed inset-0 z-[160] hidden items-center justify-center bg-[#111827]/60 p-4 backdrop-blur-[2px]" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="approve-member-modal-title">
    <div data-approve-member-backdrop class="absolute inset-0" aria-hidden="true"></div>
    <div class="relative w-full max-w-md overflow-hidden rounded-3xl border border-white/20 bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 bg-[#faf9fc] px-5 py-4">
            <h3 id="approve-member-modal-title" class="text-base font-extrabold text-[#351c42]">Approve this member?</h3>
            <button type="button" data-close-approve-modal class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Close">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6l-12 12"/>
                </svg>
            </button>
        </div>
        <div class="px-5 py-5 text-sm text-slate-700">
            They will be able to sign in and purchase membership plans.
        </div>
        <div class="flex flex-col gap-2 border-t border-slate-100 bg-white px-5 py-4 sm:flex-row sm:justify-end">
            <button type="button" data-close-approve-modal class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50">Cancel</button>
            <button type="button" data-confirm-approve-member class="rounded-xl bg-emerald-500 px-4 py-2 text-xs font-extrabold text-white transition hover:bg-emerald-400">Approve member</button>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php if($showApprovalActions): ?>
<script>
    (() => {
        const modal = document.getElementById("approve-member-modal");
        const openBtn = document.querySelector("[data-open-approve-modal]");
        const form = document.getElementById("approve-member-form");
        if (!modal || !openBtn || !form) return;

        const backdrop = modal.querySelector("[data-approve-member-backdrop]");
        const closeEls = modal.querySelectorAll("[data-close-approve-modal]");
        const confirmBtn = modal.querySelector("[data-confirm-approve-member]");
        let lastActive = null;

        function setOpen(open) {
            modal.classList.toggle("hidden", !open);
            modal.classList.toggle("flex", open);
            modal.setAttribute("aria-hidden", open ? "false" : "true");
            document.body.style.overflow = open ? "hidden" : "";
            if (!open && lastActive && typeof lastActive.focus === "function") {
                lastActive.focus();
            }
        }

        openBtn.addEventListener("click", () => {
            lastActive = openBtn;
            setOpen(true);
            confirmBtn?.focus({ preventScroll: true });
        });

        closeEls.forEach((el) => el.addEventListener("click", () => setOpen(false)));
        backdrop?.addEventListener("click", () => setOpen(false));
        confirmBtn?.addEventListener("click", () => form.submit());

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && !modal.classList.contains("hidden")) {
                setOpen(false);
            }
        });
    })();
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\members\pending-approval-show.blade.php ENDPATH**/ ?>