<?php
    $isEdit = isset($job);
    $action = $isEdit ? route('admin.jobs.update', $job->id) : route('admin.jobs.store');
?>

<form method="POST" action="<?php echo e($action); ?>" class="space-y-6" id="adminJobForm">
    <?php echo csrf_field(); ?>
    <?php if($isEdit): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <div>
                <div class="mb-2 flex items-center justify-between gap-2">
                    <label class="block text-xs font-black text-slate-600">Search Hospital <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                    <button
                        type="button"
                        id="openHospitalModalBtn"
                        class="inline-flex items-center gap-1 rounded-lg border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-[11px] font-extrabold text-indigo-700 transition hover:bg-indigo-100"
                    >
                        <span class="text-base leading-none">+</span>
                        Add hospital
                    </button>
                </div>
                <?php
                    $selectedHospital = old('hospital', $job->hospital ?? '');
                    $hospitalOptions = collect($hospitalSuggestions ?? []);
                    if ($selectedHospital !== '' && ! $hospitalOptions->contains($selectedHospital)) {
                        $hospitalOptions->push($selectedHospital);
                    }
                    $hospitalOptions = $hospitalOptions->filter()->unique()->sort()->values();
                ?>
                <div class="relative" id="hospitalSearchWrap">
                    <input type="hidden" name="hospital" id="hospitalValue" value="<?php echo e($selectedHospital); ?>">
                    <input
                        type="text"
                        id="hospitalSearchInput"
                        value="<?php echo e($selectedHospital); ?>"
                        placeholder="Type to search hospital"
                        autocomplete="off"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-10 text-sm"
                        required
                    >
                    <button
                        type="button"
                        id="hospitalDropdownToggle"
                        class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                        aria-label="Toggle hospital list"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="hospitalSearchPanel" class="absolute z-20 mt-1 hidden w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                        <ul id="hospitalSearchList" class="max-h-56 overflow-y-auto py-1 text-sm"></ul>
                        <p id="hospitalNoResults" class="hidden px-3 py-2 text-xs font-semibold text-slate-500">No matching hospital. Use “Add hospital”.</p>
                    </div>
                </div>
                <p class="mt-1 text-[11px] text-slate-500">Choose from available hospitals (searchable select).</p>
                <?php $__errorArgs = ['hospital'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Job Title <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                <input type="text" name="title" value="<?php echo e(old('title', $job->title ?? '')); ?>" placeholder="Enter Job Title"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Job Code <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                <input type="text" name="code" value="<?php echo e(old('code', $job->code ?? '')); ?>" placeholder="Enter Job Code"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Vacancy Type <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                <div class="flex items-center gap-4 text-sm">
                    <label><input type="checkbox" name="vacancy_permanent" value="1" <?php echo e(old('vacancy_permanent', $job->vacancy_permanent ?? false) ? 'checked' : ''); ?>> Permanent</label>
                    <label><input type="checkbox" name="vacancy_temporary" value="1" <?php echo e(old('vacancy_temporary', $job->vacancy_temporary ?? false) ? 'checked' : ''); ?>> Temporary</label>
                    <label><input type="checkbox" name="vacancy_any" value="1" <?php echo e(old('vacancy_any', $job->vacancy_any ?? true) ? 'checked' : ''); ?>> Any</label>
                </div>
                <?php $__errorArgs = ['vacancy_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Preference <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                <div class="flex items-center gap-4 text-sm">
                    <label><input type="checkbox" name="preference_wfh" value="1" <?php echo e(old('preference_wfh', $job->preference_wfh ?? false) ? 'checked' : ''); ?>> WFH</label>
                    <label><input type="checkbox" name="preference_onsite" value="1" <?php echo e(old('preference_onsite', $job->preference_onsite ?? false) ? 'checked' : ''); ?>> Onsite</label>
                    <label><input type="checkbox" name="preference_any" value="1" <?php echo e(old('preference_any', $job->preference_any ?? true) ? 'checked' : ''); ?>> Any</label>
                </div>
                <?php $__errorArgs = ['preference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">No. of Openings <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                <input type="number" min="0" name="no_of_openings" value="<?php echo e(old('no_of_openings', $job->no_of_openings ?? 0)); ?>"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
                <?php $__errorArgs = ['no_of_openings'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 p-4 min-h-40">
                <label class="block text-xs font-black text-slate-600 mb-2">Job Description</label>
                <textarea name="description" rows="6" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"><?php echo e(old('description', $job->description ?? '')); ?></textarea>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 min-h-40">
                <label class="block text-xs font-black text-slate-600 mb-2">Key Skills or Roles & Responsibility</label>
                <textarea name="key_skills" rows="6" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm"><?php echo e(old('key_skills', $job->key_skills ?? '')); ?></textarea>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <label class="text-xs"><input type="checkbox" name="promote_front" value="1" <?php echo e(old('promote_front', $job->promote_front ?? false) ? 'checked' : ''); ?>> Promote Front</label>
                <div>
                    <label class="block text-xs font-black text-slate-600 mb-1">Listing</label>
                    <?php $listing = old('listing_status', $job->listing_status ?? 'listed'); ?>
                    <select name="listing_status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                        <option value="listed" <?php echo e($listing === 'listed' ? 'selected' : ''); ?>>Listed</option>
                        <option value="unlisted" <?php echo e($listing === 'unlisted' ? 'selected' : ''); ?>>Unlisted</option>
                    </select>
                </div>
                <label class="text-xs"><input type="checkbox" name="is_active" value="1" <?php echo e(old('is_active', $job->is_active ?? true) ? 'checked' : ''); ?>> Active</label>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="<?php echo e(route('admin.jobs.index')); ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-700">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold"><?php echo e($isEdit ? 'Update' : 'Create'); ?></button>
    </div>
</form>

<div id="hospitalModal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-slate-900/45 p-4">
    <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Add Hospital</h3>
                <p class="mt-0.5 text-[11px] text-slate-500">Create a hospital name and address for quick selection.</p>
            </div>
            <button type="button" id="closeHospitalModalBtn" class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Close hospital modal">✕</button>
        </div>
        <form id="hospitalModalForm" class="space-y-4 px-5 py-4">
            <div>
                <label class="mb-1 block text-xs font-black text-slate-600">Hospital Name <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                <input id="hospitalModalName" name="name" type="text" maxlength="255" placeholder="Example: Apollo Hospital, Chennai" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" required>
            </div>
            <div>
                <label class="mb-1 block text-xs font-black text-slate-600">Address <?php echo $__env->make('admin.partials.required-mark', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></label>
                <textarea id="hospitalModalAddress" name="address" rows="3" maxlength="500" placeholder="Street, area, city" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" required></textarea>
            </div>
           
            <p id="hospitalModalError" class="hidden rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700"></p>
            <div class="flex justify-end gap-2 border-t border-slate-100 pt-3">
                <button type="button" id="cancelHospitalModalBtn" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700">Cancel</button>
                <button type="submit" id="saveHospitalModalBtn" class="rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-indigo-700">Save Hospital</button>
            </div>
        </form>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('hospitalModal');
        const openBtn = document.getElementById('openHospitalModalBtn');
        const closeBtn = document.getElementById('closeHospitalModalBtn');
        const cancelBtn = document.getElementById('cancelHospitalModalBtn');
        const form = document.getElementById('hospitalModalForm');
        const saveBtn = document.getElementById('saveHospitalModalBtn');
        const errorBox = document.getElementById('hospitalModalError');
        const nameInput = document.getElementById('hospitalModalName');
        const addressInput = document.getElementById('hospitalModalAddress');
        const jobForm = document.getElementById('adminJobForm');
        const hospitalValue = document.getElementById('hospitalValue');
        const hospitalInput = document.getElementById('hospitalSearchInput');
        const hospitalPanel = document.getElementById('hospitalSearchPanel');
        const hospitalList = document.getElementById('hospitalSearchList');
        const hospitalNoResults = document.getElementById('hospitalNoResults');
        const hospitalToggle = document.getElementById('hospitalDropdownToggle');
        const hospitalWrap = document.getElementById('hospitalSearchWrap');
        if (!modal || !openBtn || !form || !hospitalValue || !hospitalInput || !hospitalPanel || !hospitalList || !hospitalToggle || !hospitalWrap) return;

        let hospitalOptions = <?php echo json_encode($hospitalOptions->values()->all(), 15, 512) ?>;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const storeUrl = <?php echo json_encode(route('admin.jobs.hospitals.store'), 15, 512) ?>;

        const showModal = () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            errorBox?.classList.add('hidden');
            errorBox.textContent = '';
            nameInput?.focus();
        };

        const hideModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            form.reset();
            errorBox?.classList.add('hidden');
            errorBox.textContent = '';
        };

        const addHospitalOption = (name) => {
            const normalized = (name || '').trim();
            if (!normalized) return;
            const exists = hospitalOptions.some((n) => String(n).toLowerCase() === normalized.toLowerCase());
            if (!exists) {
                hospitalOptions.push(normalized);
                hospitalOptions = Array.from(new Set(hospitalOptions.map((n) => String(n).trim()).filter(Boolean))).sort((a, b) => a.localeCompare(b));
            }
            selectHospital(normalized);
        };

        const closeHospitalPanel = () => {
            hospitalPanel.classList.add('hidden');
        };

        const openHospitalPanel = () => {
            hospitalPanel.classList.remove('hidden');
        };

        const selectHospital = (name) => {
            hospitalValue.value = name;
            hospitalInput.value = name;
            hospitalInput.setCustomValidity('');
            closeHospitalPanel();
        };

        const renderHospitalList = (term = '') => {
            const query = String(term || '').trim().toLowerCase();
            const filtered = hospitalOptions.filter((name) => name.toLowerCase().includes(query));
            hospitalList.innerHTML = '';
            if (filtered.length === 0) {
                hospitalNoResults?.classList.remove('hidden');
                return;
            }
            hospitalNoResults?.classList.add('hidden');
            filtered.forEach((name) => {
                const li = document.createElement('li');
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'block w-full px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-slate-50';
                btn.textContent = name;
                btn.addEventListener('click', () => selectHospital(name));
                li.appendChild(btn);
                hospitalList.appendChild(li);
            });
        };

        openBtn.addEventListener('click', showModal);
        closeBtn?.addEventListener('click', hideModal);
        cancelBtn?.addEventListener('click', hideModal);
        modal.addEventListener('click', (e) => { if (e.target === modal) hideModal(); });

        renderHospitalList(hospitalInput.value);

        hospitalInput.addEventListener('focus', () => {
            renderHospitalList(hospitalInput.value);
            openHospitalPanel();
        });
        hospitalInput.addEventListener('input', () => {
            hospitalValue.value = '';
            hospitalInput.setCustomValidity('');
            renderHospitalList(hospitalInput.value);
            openHospitalPanel();
        });
        hospitalToggle.addEventListener('click', () => {
            if (hospitalPanel.classList.contains('hidden')) {
                renderHospitalList(hospitalInput.value);
                openHospitalPanel();
                hospitalInput.focus();
            } else {
                closeHospitalPanel();
            }
        });

        document.addEventListener('click', (e) => {
            if (!hospitalWrap.contains(e.target)) closeHospitalPanel();
        });

        hospitalInput.addEventListener('blur', () => {
            window.setTimeout(() => {
                const typed = hospitalInput.value.trim();
                if (!typed) {
                    hospitalValue.value = '';
                    return;
                }
                const exact = hospitalOptions.find((n) => n.toLowerCase() === typed.toLowerCase());
                if (exact) {
                    selectHospital(exact);
                }
            }, 120);
        });

        jobForm?.addEventListener('submit', (e) => {
            if (!hospitalValue.value) {
                e.preventDefault();
                hospitalInput.setCustomValidity('Please select a hospital from the list.');
                hospitalInput.reportValidity();
                renderHospitalList(hospitalInput.value);
                openHospitalPanel();
                return;
            }
            hospitalInput.setCustomValidity('');
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!csrf) return;

            const fd = new FormData(form);
            fd.append('_token', csrf);
            saveBtn.disabled = true;
            saveBtn.classList.add('opacity-60', 'cursor-not-allowed');
            errorBox?.classList.add('hidden');
            errorBox.textContent = '';

            try {
                const res = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: fd,
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data?.success) {
                    const msg = data?.message || data?.errors?.name?.[0] || data?.errors?.address?.[0] || 'Could not add hospital.';
                    errorBox.textContent = msg;
                    errorBox?.classList.remove('hidden');
                    return;
                }

                addHospitalOption(data.hospital?.name || '');
                hideModal();
            } catch (_) {
                errorBox.textContent = 'Network issue while adding hospital. Please try again.';
                errorBox?.classList.remove('hidden');
            } finally {
                saveBtn.disabled = false;
                saveBtn.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        });
    })();
</script>

<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\jobs\_form.blade.php ENDPATH**/ ?>