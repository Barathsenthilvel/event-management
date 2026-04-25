<?php
    $p = $polling ?? null;
    $isEdit = $p !== null;
    $action = $isEdit ? route('admin.pollings.update', $p->id) : route('admin.pollings.store');
    $oldPositions = old('positions');
    $rows = is_array($oldPositions)
        ? $oldPositions
        : ($isEdit
            ? $p->positions->map(fn ($row) => [
                'position' => $row->position,
                'candidate_ids' => $row->candidates->pluck('id')->values()->all(),
            ])->values()->all()
            : [['position' => '', 'candidate_ids' => []]]);

    $defaultPollingFrom = $isEdit && $p->polling_from
        ? \Illuminate\Support\Carbon::parse($p->polling_from)->format('H:i')
        : '';
    $defaultPollingTo = $isEdit && $p->polling_to
        ? \Illuminate\Support\Carbon::parse($p->polling_to)->format('H:i')
        : '';

    $initialCoverUrl = $isEdit && !empty($p->cover_image_path)
        ? asset('storage/' . ltrim($p->cover_image_path, '/'))
        : null;
    $initialBannerUrl = $isEdit && !empty($p->banner_image_path)
        ? asset('storage/' . ltrim($p->banner_image_path, '/'))
        : null;

    $membersJson = $members->map(fn ($m) => [
        'id' => $m->id,
        'name' => $m->name,
        'detail' => $m->mobile ?: $m->email,
    ])->values();
?>

<form method="POST" action="<?php echo e($action); ?>" enctype="multipart/form-data" class="space-y-6" x-data="pollingForm()">
    <?php echo csrf_field(); ?>
    <?php if($isEdit): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-black text-slate-600 mb-2">Polling Title</label>
                <input type="text" name="title" value="<?php echo e(old('title', $p?->title ?? '')); ?>" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm">
            </div>
            <div @click.outside="closeSelect()" class="relative">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-black text-slate-600">Positions & candidates</label>
                    <button type="button" @click.stop="addRow()" class="text-xs font-black text-indigo-600">+ Add</button>
                </div>
                <p class="text-[11px] text-slate-500 mb-2">For each position, search and select one or more interested members (from nomination submissions) who will appear on the ballot.</p>
                <template x-for="(row, idx) in rows" :key="row._key">
                    <div class="mb-3 rounded-xl border border-slate-200 bg-slate-50/40 p-3">
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-12 sm:items-start">
                            <div class="sm:col-span-5">
                                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Position</label>
                                <input class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white text-xs" :name="`positions[${idx}][position]`" x-model="row.position" placeholder="Position title">
                            </div>
                            <div class="relative sm:col-span-6">
                                <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Candidates</label>
                                <button type="button" @click.stop="toggleSelect(idx)" class="flex w-full items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left text-xs font-semibold text-slate-800 shadow-sm">
                                    <span class="truncate" x-text="selectSummary(row)"></span>
                                    <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div
                                    x-show="openSelectIdx === idx"
                                    x-cloak
                                    x-transition
                                    @click.stop
                                    class="absolute left-0 right-0 top-full z-[100] mt-1 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
                                >
                                    <div class="border-b border-slate-100 p-2">
                                        <div class="relative">
                                            <span class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            </span>
                                            <input
                                                type="search"
                                                x-model="selectSearch"
                                                placeholder="Type to search..."
                                                class="w-full rounded-lg border border-indigo-200 py-2 pl-9 pr-2 text-xs outline-none ring-0 focus:border-indigo-400"
                                                @click.stop
                                            >
                                        </div>
                                    </div>
                                    <ul class="max-h-52 overflow-y-auto py-1">
                                        <template x-for="m in filteredMembers()" :key="m.id">
                                            <li>
                                                <button type="button" @click="toggleCandidate(row, m.id)" class="flex w-full items-start gap-2 px-3 py-2 text-left text-xs hover:bg-indigo-50" :class="isSelected(row, m.id) ? 'bg-indigo-50' : ''">
                                                    <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded border" :class="isSelected(row, m.id) ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300'">
                                                        <span x-show="isSelected(row, m.id)" class="text-[10px] leading-none text-white">✓</span>
                                                    </span>
                                                    <span>
                                                        <span x-text="m.name" class="font-semibold text-slate-800"></span>
                                                        <span class="block text-[10px] text-slate-500" x-text="m.detail"></span>
                                                    </span>
                                                </button>
                                            </li>
                                        </template>
                                    </ul>
                                    <p class="border-t border-slate-100 px-3 py-2 text-[10px] text-slate-500" x-text="members.length + ' members available'"></p>
                                </div>
                                <template x-for="cid in row.candidate_ids" :key="'hid-' + idx + '-' + cid">
                                    <input type="hidden" :name="`positions[${idx}][candidate_ids][]`" :value="cid">
                                </template>
                            </div>
                            <div class="flex justify-end sm:col-span-1">
                                <button type="button" @click.stop="removeRow(idx)" class="mt-6 rounded-lg px-2 py-1 text-rose-600 font-black sm:mt-0" title="Remove row">×</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-1">Polling window</p>
                <p class="text-[10px] text-slate-500 mb-3">Set <span class="font-semibold">From date</span> and <span class="font-semibold">To date</span> (leave To date empty for a single day). Times use 24-hour <span class="font-mono">HH:MM</span>.</p>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">From date</label>
                        <input type="date" name="polling_date" value="<?php echo e(old('polling_date', $p?->polling_date?->format('Y-m-d'))); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">To date</label>
                        <input type="date" name="polling_date_to" value="<?php echo e(old('polling_date_to', $p?->polling_date_to?->format('Y-m-d') ?? '')); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs" title="Optional — defaults to From date">
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Start time</label>
                        <input type="time" name="polling_from" step="60" value="<?php echo e(old('polling_from', $defaultPollingFrom)); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    </div>
                    <div>
                        <label class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-500">End time</label>
                        <input type="time" name="polling_to" step="60" value="<?php echo e(old('polling_to', $defaultPollingTo)); ?>" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                    </div>
                </div>
                <?php $__errorArgs = ['polling_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['polling_date_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['polling_from'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['polling_to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="rounded-xl border border-slate-200 p-4">
                <p class="text-xs font-black text-slate-700 mb-2">Images</p>
                <p class="text-[10px] text-slate-500 mb-3">Preview updates when you choose a new file.</p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-3">
                        <p class="text-xs font-semibold text-slate-700 mb-2">Cover Image</p>
                        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                            <template x-if="coverUrl">
                                <img x-bind:src="coverUrl" alt="Cover preview" class="h-36 w-full object-cover">
                            </template>
                        </div>
                        <label class="mt-2 flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-indigo-700 hover:bg-indigo-50">
                            <span x-text="coverUrl ? 'Change cover image' : 'Upload cover image'"></span>
                            <input type="file" name="cover_image" class="hidden" accept="image/*" @change="pickCover($event)">
                        </label>
                        <?php $__errorArgs = ['cover_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/50 p-3">
                        <p class="text-xs font-semibold text-slate-700 mb-2">Banner Image</p>
                        <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                            <template x-if="bannerUrl">
                                <img x-bind:src="bannerUrl" alt="Banner preview" class="h-36 w-full object-cover">
                            </template>
                        </div>
                        <label class="mt-2 flex cursor-pointer items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] font-bold text-indigo-700 hover:bg-indigo-50">
                            <span x-text="bannerUrl ? 'Change banner image' : 'Upload banner image'"></span>
                            <input type="file" name="banner_image" class="hidden" accept="image/*" @change="pickBanner($event)">
                        </label>
                        <?php $__errorArgs = ['banner_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-[11px] text-red-600 mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <?php if($isEdit): ?>
                    <label class="text-xs"><input type="checkbox" name="promote_front" value="1" <?php echo e(old('promote_front', $p?->promote_front ?? false) ? 'checked' : ''); ?>> Promote Front</label>
                <?php else: ?>
                    <input type="hidden" name="promote_front" value="0">
                <?php endif; ?>
                <label class="text-xs"><input type="checkbox" name="show_stats" value="1" <?php echo e(old('show_stats', $p?->show_stats ?? true) ? 'checked' : ''); ?>> Show Stats</label>
                <div>
                    <?php $publish = old('publish_status', $p?->publish_status ?? 'published'); ?>
                    <label class="block text-xs font-black text-slate-600 mb-1">Publish Status</label>
                    <select name="publish_status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                        <option value="na" <?php echo e($publish === 'na' ? 'selected' : ''); ?>>N/A</option>
                        <option value="pending" <?php echo e($publish === 'pending' ? 'selected' : ''); ?>>Pending</option>
                        <option value="published" <?php echo e($publish === 'published' ? 'selected' : ''); ?>>Published</option>
                    </select>
                </div>
                <div>
                    <?php $status = old('polling_status', $p?->polling_status ?? 'live'); ?>
                    <label class="block text-xs font-black text-slate-600 mb-1">Polling Status</label>
                    <select name="polling_status" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
                        <option value="live" <?php echo e($status === 'live' ? 'selected' : ''); ?>>In Live</option>
                        <option value="ends" <?php echo e($status === 'ends' ? 'selected' : ''); ?>>Ends</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="<?php echo e(route('admin.pollings.index')); ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-700">Cancel</a>
        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-700 text-white text-sm font-bold"><?php echo e($isEdit ? 'Update' : 'Create'); ?></button>
    </div>
</form>

<style>[x-cloak]{display:none!important}</style>

<script>
function pollingForm() {
    const newRowKey = () =>
        (typeof crypto !== 'undefined' && crypto.randomUUID)
            ? crypto.randomUUID()
            : 'row-' + Date.now() + '-' + Math.random().toString(16).slice(2);
    return {
        rows: <?php echo json_encode($rows, 15, 512) ?>,
        members: <?php echo json_encode($membersJson, 15, 512) ?>,
        coverUrl: <?php echo json_encode($initialCoverUrl, 15, 512) ?>,
        bannerUrl: <?php echo json_encode($initialBannerUrl, 15, 512) ?>,
        openSelectIdx: null,
        selectSearch: '',
        init() {
            this.rows = this.rows.map((r) => ({
                position: r.position ?? '',
                candidate_ids: Array.isArray(r.candidate_ids) ? [...r.candidate_ids] : [],
                _key: r._key || newRowKey(),
            }));
        },
        addRow() {
            this.rows.push({ position: '', candidate_ids: [], _key: newRowKey() });
        },
        removeRow(idx) {
            if (this.rows.length <= 1) return;
            const open = this.openSelectIdx;
            if (open === idx) {
                this.openSelectIdx = null;
                this.selectSearch = '';
            } else if (open !== null && open > idx) {
                this.openSelectIdx = open - 1;
            }
            this.rows.splice(idx, 1);
        },
        closeSelect() {
            this.openSelectIdx = null;
            this.selectSearch = '';
        },
        toggleSelect(idx) {
            if (this.openSelectIdx === idx) {
                this.closeSelect();
            } else {
                this.openSelectIdx = idx;
                this.selectSearch = '';
            }
        },
        filteredMembers() {
            const q = (this.selectSearch || '').trim().toLowerCase();
            if (!q) return this.members;
            return this.members.filter(m =>
                (m.name || '').toLowerCase().includes(q) ||
                (m.detail || '').toLowerCase().includes(q) ||
                String(m.id).includes(q)
            );
        },
        isSelected(row, id) {
            return row.candidate_ids.includes(id);
        },
        toggleCandidate(row, id) {
            const i = row.candidate_ids.indexOf(id);
            if (i >= 0) {
                row.candidate_ids.splice(i, 1);
            } else {
                row.candidate_ids.push(id);
            }
        },
        selectSummary(row) {
            const n = row.candidate_ids.length;
            if (n === 0) return 'Select…';
            if (n === 1) {
                const m = this.members.find(x => x.id === row.candidate_ids[0]);
                return m ? m.name : '1 selected';
            }
            return n + ' selected';
        },
        pickCover(e) { this.setImagePreview(e, 'coverUrl'); },
        pickBanner(e) { this.setImagePreview(e, 'bannerUrl'); },
        setImagePreview(e, key) {
            const f = e.target.files && e.target.files[0];
            if (!f || !f.type.startsWith('image/')) return;
            const r = new FileReader();
            r.onload = () => { this[key] = r.result; };
            r.readAsDataURL(f);
        }
    };
}
</script>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/admin/pollings/_form.blade.php ENDPATH**/ ?>