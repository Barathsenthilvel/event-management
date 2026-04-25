<?php $__env->startSection('content'); ?>
<?php
    $settingsData = $settings->map(function ($s) {
        return [
            'id'                       => $s->id,
            'subscription_type'        => $s->subscription_type,
            'membership_fee'           => $s->membership_fee,
            'registration_fee'         => $s->registration_fee,
            'registration_fee_enabled' => (bool) $s->registration_fee_enabled,
            'payment_type'             => $s->payment_type,
            'grace_period'             => $s->grace_period,
            'update_url'               => route('admin.memberships.update', $s->id),
            'delete_url'               => route('admin.memberships.destroy', $s->id),
        ];
    });
?>

<div class="h-full flex gap-3 workspace-transition relative p-6" x-data="membershipPage()">
    <div class="flex flex-col gap-3 workspace-transition flex-1 min-w-0" :class="showPanel ? 'w-2/3' : 'w-full'">

        <!-- Header -->
        <div class="flex items-center justify-between mb-2">
            <div>
                <h1 class="text-lg font-bold text-slate-800">Membership Settings</h1>
                <p class="text-xs text-slate-500 mt-1">Manage membership subscription types, fees and payment options.</p>
            </div>
            <div class="shrink-0">
                <span class="inline-flex items-center gap-2 rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-2 text-[11px] font-extrabold text-indigo-700">
                    Total settings:
                    <span class="rounded-xl bg-white px-2 py-0.5 text-indigo-800 border border-indigo-100" x-text="settings.length"></span>
                </span>
            </div>
        </div>

        <!-- Main Content Board -->
        <div class="bg-white flex-1 rounded-[24px] shadow-sm flex flex-col p-6 overflow-hidden relative">

            <!-- Tools Bar -->
            <div class="flex items-center justify-between mb-6 shrink-0">
                <div class="flex items-center gap-2">
                    <input type="text" x-model="search" placeholder="Search settings..."
                        class="pl-4 pr-4 py-2 bg-slate-50 border border-slate-100 rounded-xl text-xs w-48 outline-none focus:ring-2 focus:ring-indigo-500/10">
                    <span class="hidden sm:inline-flex items-center gap-2 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2 text-[11px] font-extrabold text-slate-600">
                        Showing
                        <span class="rounded-lg bg-white px-2 py-0.5 border border-slate-100 text-slate-800" x-text="filtered.length"></span>
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex bg-slate-50 p-1 rounded-xl">
                        <button @click="viewType = 'list'"
                            :class="viewType === 'list' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                            class="p-2 rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <button @click="viewType = 'grid'"
                            :class="viewType === 'grid' ? 'bg-white shadow-sm text-indigo-600' : 'text-slate-400'"
                            class="p-2 rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        </button>
                    </div>
                    <button @click="openPanel('create')"
                        class="bg-[#0f172a] hover:bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-[11px] font-bold transition-all shadow-lg">
                        + Add New Setting
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto custom-scroll pr-2 pb-10">

                <!-- LIST VIEW -->
                <table x-show="viewType === 'list'" class="w-full text-left island-row">
                    <thead class="text-[10px] font-bold text-slate-400 uppercase tracking-widest sticky top-0 bg-white z-10">
                        <tr>
                            <th class="px-6 py-4">Subscription Type</th>
                            <th class="px-6 py-4">Payment Type</th>
                            <th class="px-6 py-4">Membership Fee</th>
                            <th class="px-6 py-4">Registration Fee</th>
                            <th class="px-6 py-4 text-center">Grace Period</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        <template x-for="item in filtered" :key="item.id">
                            <tr class="membership-row transition-all">
                                <td class="px-6 py-4 bg-white border-y border-l border-slate-100 rounded-l-2xl">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center font-bold text-xs"
                                            x-text="item.subscription_type[0]"></div>
                                        <p class="font-bold text-slate-800" x-text="item.subscription_type"></p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 bg-white border-y border-slate-100">
                                    <span class="px-2 py-1 rounded-md bg-indigo-50 text-indigo-600 font-bold text-[9px] uppercase border border-indigo-100"
                                        x-text="paymentLabel(Array.isArray(item.payment_type) ? item.payment_type[0] : item.payment_type)"></span>
                                </td>
                                <td class="px-6 py-4 bg-white border-y border-slate-100 font-semibold text-slate-700">
                                    <span x-text="'₹ ' + Number(item.membership_fee).toFixed(2)"></span>
                                </td>
                                <td class="px-6 py-4 bg-white border-y border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-slate-700" x-text="'₹ ' + Number(item.registration_fee).toFixed(2)"></span>
                                        <span :class="item.registration_fee_enabled ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-slate-50 text-slate-400 border-slate-100'"
                                            class="px-1.5 py-0.5 rounded font-black text-[9px] uppercase border"
                                            x-text="item.registration_fee_enabled ? 'ON' : 'OFF'"></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 bg-white border-y border-slate-100 text-center text-slate-600 font-medium">
                                    <span x-text="item.grace_period ? item.grace_period + ' days' : '—'"></span>
                                </td>
                                <td class="px-6 py-4 bg-white border-y border-r border-slate-100 rounded-r-2xl text-right">
                                    <div class="row-actions flex items-center justify-end gap-1">
                                        <button @click="openPanel('edit', item)"
                                            class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <button @click="confirmDelete(item)"
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filtered.length === 0">
                            <td colspan="6" class="text-center py-16 text-slate-400 text-sm">
                                No membership settings found. Click <strong>+ Add New Setting</strong> to get started.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- GRID VIEW -->
                <div x-show="viewType === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="item in filtered" :key="item.id">
                        <div class="p-5 border border-slate-100 rounded-[20px] hover:shadow-lg hover:-translate-y-1 transition-all bg-white group">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-500 font-bold text-lg group-hover:bg-indigo-500 group-hover:text-white transition-colors"
                                    x-text="item.subscription_type[0]"></div>
                                <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-600 rounded font-bold text-[9px] uppercase"
                                    x-text="paymentLabel(Array.isArray(item.payment_type) ? item.payment_type[0] : item.payment_type)"></span>
                            </div>
                            <h4 class="font-bold text-slate-800 mb-3" x-text="item.subscription_type + ' Membership'"></h4>
                            <div class="grid grid-cols-2 gap-2 text-xs mb-4">
                                <div class="bg-slate-50 rounded-lg p-2">
                                    <p class="text-[9px] text-slate-400 font-bold uppercase">Membership Fee</p>
                                    <p class="font-bold text-slate-700 mt-0.5" x-text="'₹ ' + Number(item.membership_fee).toFixed(2)"></p>
                                </div>
                                <div class="bg-slate-50 rounded-lg p-2">
                                    <p class="text-[9px] text-slate-400 font-bold uppercase">Reg. Fee</p>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <p class="font-bold text-slate-700" x-text="'₹ ' + Number(item.registration_fee).toFixed(2)"></p>
                                        <span :class="item.registration_fee_enabled ? 'text-emerald-500' : 'text-slate-300'"
                                            class="text-[9px] font-bold" x-text="item.registration_fee_enabled ? 'ON' : 'OFF'"></span>
                                    </div>
                                </div>
                                <div class="bg-slate-50 rounded-lg p-2 col-span-2">
                                    <p class="text-[9px] text-slate-400 font-bold uppercase">Grace Period</p>
                                    <p class="font-bold text-slate-700 mt-0.5" x-text="item.grace_period ? item.grace_period + ' days' : '—'"></p>
                                </div>
                            </div>
                            <div class="flex gap-2 pt-3 border-t border-slate-50">
                                <button @click="openPanel('edit', item)"
                                    class="flex-1 py-2 bg-slate-50 text-[10px] font-bold text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-colors">Edit</button>
                                <button @click="confirmDelete(item)"
                                    class="p-2 bg-slate-50 text-slate-400 rounded-xl hover:bg-rose-50 hover:text-rose-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                    <div x-show="filtered.length === 0" class="col-span-3 text-center py-16 text-slate-400 text-sm">
                        No membership settings found.
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ── SIDE PANEL ── -->
    <div x-show="showPanel" x-cloak
        x-transition:enter="transition-all ease-out duration-500"
        x-transition:enter-start="translate-x-full opacity-0"
        x-transition:enter-end="translate-x-0 opacity-100"
        class="w-1/3 bg-white rounded-[24px] shadow-2xl border border-white flex flex-col overflow-hidden">

        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30 shrink-0">
            <div>
                <h3 class="font-bold text-slate-800" x-text="isEditing ? 'Edit Setting' : 'Add New Setting'"></h3>
                <p class="text-[10px] text-slate-400 mt-0.5" x-text="isEditing ? 'Update subscription details' : 'Create a new subscription setting'"></p>
            </div>
            <button @click="closePanel()" class="p-2 text-slate-400 hover:bg-white rounded-xl shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form :action="isEditing ? form.update_url : '<?php echo e(route('admin.memberships.store')); ?>'"
            method="POST"
            @submit="validateForm($event)"
            class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_method" :value="isEditing ? 'PUT' : 'POST'">

            <!-- Subscription Type -->
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">
                    Subscription Type <span class="text-red-500">*</span>
                </label>
                <select name="subscription_type" x-model="form.subscription_type"
                    :class="errors.subscription_type ? 'border-red-400 bg-red-50' : 'border-slate-100 bg-slate-50'"
                    class="w-full rounded-xl px-4 py-3 text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 border transition-all">
                    <option value="">Select Type</option>
                    <option value="New">New</option>
                    <option value="Renewal">Renewal</option>
                </select>
                <p x-show="errors.subscription_type" x-cloak class="mt-1 text-[10px] text-red-600 font-medium" x-text="errors.subscription_type"></p>
            </div>

            <!-- Membership Fee -->
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">
                    Membership Fee <span class="text-red-500">*</span>
                </label>
                <input type="number" name="membership_fee" x-model="form.membership_fee" step="0.01" min="0"
                    placeholder="Enter fee"
                    :class="errors.membership_fee ? 'border-red-400 bg-red-50' : 'border-slate-100 bg-slate-50'"
                    class="w-full rounded-xl px-4 py-3 text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 border transition-all">
                <p x-show="errors.membership_fee" x-cloak class="mt-1 text-[10px] text-red-600 font-medium" x-text="errors.membership_fee"></p>
            </div>

            <!-- Registration Fee (hidden for Renewal) -->
            <div x-show="form.subscription_type !== 'Renewal'" x-cloak>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">
                    Registration Fee <span class="text-red-500">*</span>
                </label>
                <input type="number" name="registration_fee" x-model="form.registration_fee" step="0.01" min="0"
                    placeholder="Enter fee"
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all mb-2">
                <!-- Registration Fee Enable/Disable Toggle Card -->
                <div @click="form.registration_fee_enabled = !form.registration_fee_enabled"
                    :class="form.registration_fee_enabled ? 'border-emerald-200 bg-emerald-50' : 'border-slate-100 bg-slate-50'"
                    class="flex items-center justify-between p-3 rounded-xl border cursor-pointer transition-all select-none">
                    <input type="hidden" name="registration_fee_enabled" :value="form.registration_fee_enabled ? '1' : '0'">
                    <div class="flex items-center gap-2">
                        <div :class="form.registration_fee_enabled ? 'bg-emerald-500' : 'bg-slate-300'"
                            class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-700">Registration Fee</p>
                            <p class="text-[9px] font-bold uppercase tracking-wide"
                                :class="form.registration_fee_enabled ? 'text-emerald-500' : 'text-slate-400'"
                                x-text="form.registration_fee_enabled ? 'Enabled' : 'Disabled'"></p>
                        </div>
                    </div>
                    <!-- Toggle Switch -->
                    <div :class="form.registration_fee_enabled ? 'bg-emerald-500' : 'bg-slate-300'"
                        class="w-10 h-5 rounded-full relative transition-colors shrink-0">
                        <span :class="form.registration_fee_enabled ? 'translate-x-5' : 'translate-x-0.5'"
                            class="absolute top-0.5 w-4 h-4 bg-white rounded-full transition-all shadow-sm"></span>
                    </div>
                </div>
            </div>

            <!-- Payment Type (single select dropdown) -->
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">
                    Payment Type <span class="text-red-500">*</span>
                </label>
                <select name="payment_type" x-model="form.payment_type"
                    @change="errors.payment_type = ''"
                    :class="errors.payment_type ? 'border-red-400 bg-red-50' : 'border-slate-100 bg-slate-50'"
                    class="w-full rounded-xl px-4 py-3 text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 border transition-all">
                    <option value="">-- Select Payment Type --</option>
                    <option value="monthly">Monthly</option>
                    <option value="bi_monthly">Bi - Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="half_yearly">Half - Yearly</option>
                    <option value="yearly">Yearly</option>
                </select>
                <p x-show="errors.payment_type" x-cloak class="mt-1 text-[10px] text-red-600 font-medium" x-text="errors.payment_type"></p>
            </div>

            <!-- Grace Period (shown after payment type is selected) -->
            <div x-show="form.payment_type">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest">
                    Grace Period <span class="font-normal normal-case text-slate-400">(days, optional)</span>
                </label>
                <input type="number" name="grace_period" x-model="form.grace_period" min="0" placeholder="0"
                    class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button type="submit"
                    class="w-full bg-[#0f172a] text-white font-bold py-3.5 rounded-2xl shadow-xl hover:bg-indigo-600 transition-all text-xs">
                    <span x-text="isEditing ? 'Update Setting' : 'Save Setting'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- ── DELETE CONFIRM MODAL ── -->
    <div x-show="showDeleteModal" x-cloak
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-100 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-[32px] p-8 max-w-sm w-full text-center shadow-2xl"
            @click.away="showDeleteModal = false"
            x-transition:enter="transition cubic-bezier(0.34, 1.56, 0.64, 1) duration-500"
            x-transition:enter-start="scale-50 opacity-0" x-transition:enter-end="scale-100 opacity-100">
            <div class="w-14 h-14 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-red-100">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Delete Setting?</h3>
            <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                This will permanently delete the <strong x-text="itemToDelete?.subscription_type"></strong> membership setting. This cannot be undone.
            </p>
            <div class="flex gap-3">
                <button @click="showDeleteModal = false"
                    class="flex-1 py-3 bg-slate-50 hover:bg-slate-100 rounded-2xl font-bold text-slate-600 text-sm transition-colors">
                    Cancel
                </button>
                <form :action="itemToDelete?.delete_url" method="POST" class="flex-1">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit"
                        class="w-full py-3 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-bold text-sm shadow-lg shadow-red-200 transition-all">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ── TOAST NOTIFICATIONS ── -->
    <div class="fixed bottom-10 right-10 z-300 space-y-3 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-5"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-5"
                class="flex items-center gap-4 bg-[#0f172a] text-white px-6 py-4 rounded-2xl shadow-2xl border border-white/10 pointer-events-auto">
                <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center shrink-0">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="text-sm font-bold" x-text="toast.msg"></p>
            </div>
        </template>
    </div>

</div>

<style>
    .membership-row .row-actions { opacity: 0; transition: opacity 0.2s ease; }
    .membership-row:hover .row-actions { opacity: 1; }
</style>

<script>
function membershipPage() {
    return {
        viewType: 'grid',
        showPanel: false,
        showDeleteModal: false,
        isEditing: false,
        search: '',
        itemToDelete: null,
        toasts: [],
        settings: <?php echo json_encode($settingsData, 15, 512) ?>,

        showToast(msg) {
            const id = Date.now();
            this.toasts.push({ id, msg });
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 3500);
        },
        form: {
            subscription_type: '',
            membership_fee: '',
            registration_fee: '',
            registration_fee_enabled: true,
            payment_type: '',
            grace_period: '',
            update_url: ''
        },
        errors: {
            subscription_type: '',
            membership_fee: '',
            payment_type: ''
        },

        paymentOptions: [
            { value: 'monthly',     label: 'Monthly' },
            { value: 'bi_monthly',  label: 'Bi-Monthly' },
            { value: 'quarterly',   label: 'Quarterly' },
            { value: 'half_yearly', label: 'Half-Yearly' },
            { value: 'yearly',      label: 'Yearly' },
        ],

        paymentLabels: {
            monthly:     'Monthly',
            bi_monthly:  'Bi-Monthly',
            quarterly:   'Quarterly',
            half_yearly: 'Half-Yearly',
            yearly:      'Yearly'
        },

        paymentLabel(type) {
            return this.paymentLabels[type] || '—';
        },

        get filtered() {
            if (!this.search) return this.settings;
            const q = this.search.toLowerCase();
            return this.settings.filter(s => {
                const pt = Array.isArray(s.payment_type) ? s.payment_type[0] : (s.payment_type || '');
                return s.subscription_type.toLowerCase().includes(q) || pt.toLowerCase().includes(q);
            });
        },

        openPanel(mode, item = null) {
            this.isEditing = mode === 'edit';
            this.errors = { subscription_type: '', membership_fee: '', payment_type: '' };

            if (this.isEditing && item) {
                this.form = {
                    subscription_type: item.subscription_type,
                    membership_fee: item.membership_fee,
                    registration_fee: item.registration_fee,
                    registration_fee_enabled: item.registration_fee_enabled,
                    payment_type: Array.isArray(item.payment_type) ? (item.payment_type[0] || '') : (item.payment_type || ''),
                    grace_period: item.grace_period || '',
                    update_url: item.update_url
                };
            } else {
                this.form = {
                    subscription_type: '',
                    membership_fee: '',
                    registration_fee: '',
                    registration_fee_enabled: true,
                    payment_type: '',
                    grace_period: '',
                    update_url: ''
                };
            }
            this.showPanel = true;
        },

        closePanel() {
            this.showPanel = false;
        },

        confirmDelete(item) {
            this.itemToDelete = item;
            this.showDeleteModal = true;
        },

        validateForm(e) {
            this.errors = { subscription_type: '', membership_fee: '', payment_type: '' };
            let valid = true;

            if (!this.form.subscription_type) {
                this.errors.subscription_type = 'Please select a subscription type.';
                valid = false;
            }
            if (!this.form.membership_fee && this.form.membership_fee !== 0) {
                this.errors.membership_fee = 'Membership fee is required.';
                valid = false;
            }
            if (!this.form.payment_type) {
                this.errors.payment_type = 'Please select a payment type.';
                valid = false;
            }

            if (!valid) e.preventDefault();
        }
    };
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\admin\memberships\index.blade.php ENDPATH**/ ?>