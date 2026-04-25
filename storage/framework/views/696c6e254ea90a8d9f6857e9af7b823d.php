<?php $__env->startSection('title', 'Member profile — GNAT Association'); ?>

<?php $__env->startPush('styles'); ?>
<style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
        .ml-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            color: rgba(53, 28, 66, 0.78);
            margin-bottom: 0.5rem;
        }
        .ml-inp {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid rgba(53, 28, 66, 0.1);
            background: rgba(255, 255, 255, 0.9);
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            color: #351c42;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .ml-inp:focus {
            border-color: rgba(150, 89, 149, 0.55);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(150, 89, 149, 0.14);
        }
        .ml-inp.is-invalid {
            border-color: rgba(220, 38, 38, 0.55);
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12);
            background: #fff;
        }
        .ml-help {
            margin-top: 0.4rem;
            min-height: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #dc2626;
        }
        .ml-card-soft {
            border-radius: 1rem;
            border: 1px solid rgba(53, 28, 66, 0.08);
            background: linear-gradient(180deg, rgba(150, 89, 149, 0.03), rgba(255, 255, 255, 0.9));
            padding: 1rem;
        }
        .ml-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            background: linear-gradient(135deg, #351c42 0%, #4d2a5c 100%);
            color: #fddc6a;
            box-shadow: 0 8px 24px rgba(53, 28, 66, 0.28);
        }
        .ml-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            background: #fff;
            color: #351c42;
            border: 1px solid rgba(53, 28, 66, 0.14);
            text-decoration: none;
        }
        .ml-upload-zone {
            border-radius: 1rem;
            border: 2px dashed rgba(150, 89, 149, 0.28);
            background: linear-gradient(180deg, rgba(150, 89, 149, 0.04) 0%, rgba(255, 255, 255, 0.6) 100%);
        }
        .ml-upload-zone input[type="file"]::file-selector-button {
            margin-right: 0.75rem;
            border: 0;
            border-radius: 9999px;
            background: linear-gradient(135deg, #351c42, #5c3570);
            color: #fddc6a;
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.75rem;
            cursor: pointer;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $isApproved = (bool) $user->is_approved;
        $profileLocked = (bool) $user->profile_completed || $isApproved;
        $pendingProfileDocs = $pendingProfileDocs ?? [];
        $stateOptions = [
            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh', 'Goa', 'Gujarat',
            'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka', 'Kerala', 'Madhya Pradesh',
            'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
            'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand',
            'West Bengal', 'Andaman and Nicobar Islands', 'Chandigarh', 'Dadra and Nagar Haveli and Daman and Diu',
            'Delhi', 'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry',
        ];
    ?>
    <div class="rounded-2xl border border-[#351c42]/10 bg-white/90 p-6 shadow-md sm:p-8">
                <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#965995]">Account</p>
                        <h1 class="mt-1 text-2xl font-extrabold tracking-tight sm:text-3xl">My profile</h1>
                        <p class="mt-2 text-sm text-[#351c42]/65"><?php echo e($profileLocked ? 'Your submitted details are shown below (read-only).' : 'Complete your details to activate your membership.'); ?></p>
                    </div>
                    <span class="rounded-full bg-[#351c42] px-4 py-2 text-xs font-bold text-[#fddc6a]">Required fields marked *</span>
                </div>

                <?php if(session('success')): ?>
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

                <?php if($errors->any()): ?>
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <ul class="list-inside list-disc">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if($isApproved): ?>
                    <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900">
                        Your profile is already approved and cannot be updated now. Please contact admin for any correction.
                    </div>
                <?php elseif($user->profile_completed): ?>
                    <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900">
                        Please wait for admin approval. You can review your submitted profile details below.
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('member.profile.update')); ?>" enctype="multipart/form-data" class="space-y-8" id="member-profile-form" <?php if($profileLocked): ?> data-profile-locked <?php endif; ?>>
                    <?php echo csrf_field(); ?>

                    <div class="space-y-8">
                        <section class="space-y-5">
                            <h3 class="flex items-center gap-2 border-b border-[#351c42]/10 pb-3 text-xs font-bold uppercase tracking-widest text-[#965995]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Personal
                            </h3>
                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                <div>
                                    <label class="ml-label">First name <span class="text-red-500">*</span></label>
                                    <input name="first_name" value="<?php echo e(old('first_name', $user->first_name)); ?>" required class="ml-inp" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                </div>
                                <div>
                                    <label class="ml-label">Last name <span class="text-red-500">*</span></label>
                                    <input name="last_name" value="<?php echo e(old('last_name', $user->last_name)); ?>" required class="ml-inp" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                </div>
                                <div>
                                    <label class="ml-label">Email ID</label>
                                    <input value="<?php echo e($user->email); ?>" disabled class="ml-inp bg-slate-100 text-slate-500" />
                                </div>
                                <div>
                                    <label class="ml-label">Mobile <span class="text-red-500">*</span></label>
                                    <input name="mobile" value="<?php echo e(old('mobile', $user->mobile)); ?>" required class="ml-inp" inputmode="numeric" pattern="[0-9]{10}" minlength="10" maxlength="10" data-validate="required|digits:10" data-label="Mobile" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                    <p class="ml-help" data-error-for="mobile"></p>
                                </div>
                                <div>
                                    <label class="ml-label">DOB <span class="text-red-500">*</span></label>
                                    <input type="date" name="dob" value="<?php echo e(old('dob', optional($user->dob)->format('Y-m-d'))); ?>" required class="ml-inp" max="<?php echo e(now()->format('Y-m-d')); ?>" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                </div>
                                <div>
                                    <label class="ml-label">Gender <span class="text-red-500">*</span></label>
                                    <?php ($gender = old('gender', $user->gender)); ?>
                                    <select name="gender" required class="ml-inp" <?php if($profileLocked): echo 'disabled'; endif; ?>>
                                        <option value="">Select</option>
                                        <option value="Male" <?php if($gender === 'Male'): echo 'selected'; endif; ?>>Male</option>
                                        <option value="Female" <?php if($gender === 'Female'): echo 'selected'; endif; ?>>Female</option>
                                        <option value="Other" <?php if($gender === 'Other'): echo 'selected'; endif; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                        </section>

                        <section class="space-y-5">
                            <h3 class="flex items-center gap-2 border-b border-[#351c42]/10 pb-3 text-xs font-bold uppercase tracking-widest text-[#965995]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Professional &amp; address
                            </h3>
                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                <div>
                                    <label class="ml-label">Type of profile <span class="text-red-500">*</span></label>
                                    <?php ($profileType = old('profile_type', $user->profile_type)); ?>
                                    <select name="profile_type" required class="ml-inp" data-profile-type-select <?php if($profileLocked): echo 'disabled'; endif; ?>>
                                        <option value="">Select</option>
                                        <option value="registered_nurse" <?php if($profileType === 'registered_nurse'): echo 'selected'; endif; ?>>Registered Nurse</option>
                                        <option value="student_nurse" <?php if($profileType === 'student_nurse'): echo 'selected'; endif; ?>>Student Nurse</option>
                                        <option value="volunteer" <?php if($profileType === 'volunteer'): echo 'selected'; endif; ?>>Volunteer</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="ml-label">Referred by</label>
                                    <?php ($refBy = old('referred_by_user_id', $user->referred_by_user_id)); ?>
                                    <select name="referred_by_user_id" class="ml-inp" <?php if($profileLocked): echo 'disabled'; endif; ?>>
                                        <option value="">Select</option>
                                        <?php $__currentLoopData = ($referrers ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($r->id); ?>" <?php if((string)$refBy === (string)$r->id): echo 'selected'; endif; ?>><?php echo e($r->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="ml-label">Qualification <span class="text-red-500">*</span></label>
                                    <?php ($qualification = old('qualification', $user->qualification)); ?>
                                    <select name="qualification" required class="ml-inp" <?php if($profileLocked): echo 'disabled'; endif; ?>>
                                        <option value="">Select</option>
                                        <option value="Diploma" <?php if($qualification === 'Diploma'): echo 'selected'; endif; ?>>Diploma</option>
                                        <option value="B.Sc" <?php if($qualification === 'B.Sc'): echo 'selected'; endif; ?>>B.Sc</option>
                                        <option value="B.Tech" <?php if($qualification === 'B.Tech'): echo 'selected'; endif; ?>>B.Tech</option>
                                        <option value="M.Sc" <?php if($qualification === 'M.Sc'): echo 'selected'; endif; ?>>M.Sc</option>
                                        <option value="M.Tech" <?php if($qualification === 'M.Tech'): echo 'selected'; endif; ?>>M.Tech</option>
                                        <option value="PhD" <?php if($qualification === 'PhD'): echo 'selected'; endif; ?>>PhD</option>
                                        <option value="Other" <?php if($qualification === 'Other'): echo 'selected'; endif; ?>>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="ml-label">Blood group <span class="text-red-500">*</span></label>
                                    <?php ($blood = old('blood_group', $user->blood_group)); ?>
                                    <select name="blood_group" required class="ml-inp" <?php if($profileLocked): echo 'disabled'; endif; ?>>
                                        <option value="">Select</option>
                                        <?php $__currentLoopData = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($bg); ?>" <?php if($blood === $bg): echo 'selected'; endif; ?>><?php echo e($bg); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="ml-label">RNRM No <span class="text-red-500" data-profile-required="registered_nurse">*</span></label>
                                    <input name="rnrm_number_with_date" value="<?php echo e(old('rnrm_number_with_date', $user->rnrm_number_with_date)); ?>" class="ml-inp" data-profile-show="registered_nurse" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                </div>
                                <div>
                                    <label class="ml-label">Student ID <span class="text-red-500" data-profile-required="student_nurse">*</span></label>
                                    <input name="student_id" value="<?php echo e(old('student_id', $user->student_id)); ?>" class="ml-inp" data-profile-show="student_nurse" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                </div>
                                <div>
                                    <label class="ml-label">College name <span class="text-red-500">*</span></label>
                                    <input name="college_name" value="<?php echo e(old('college_name', $user->college_name)); ?>" required class="ml-inp" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                </div>
                                <div>
                                    <label class="ml-label">Door no <span class="text-red-500">*</span></label>
                                    <input name="door_no" value="<?php echo e(old('door_no', $user->door_no)); ?>" required class="ml-inp" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                </div>
                                <div>
                                    <label class="ml-label">Locality / area <span class="text-red-500">*</span></label>
                                    <input name="locality_area" value="<?php echo e(old('locality_area', $user->locality_area)); ?>" required class="ml-inp" data-validate="required|min:3" data-label="Locality / area" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                    <p class="ml-help" data-error-for="locality_area"></p>
                                </div>
                                <div>
                                    <label class="ml-label">State <span class="text-red-500">*</span></label>
                                    <?php ($selectedState = old('state', $user->state)); ?>
                                    <select name="state" required class="ml-inp" data-validate="required" data-label="State" <?php if($profileLocked): echo 'disabled'; endif; ?>>
                                        <option value="">Select state</option>
                                        <?php $__currentLoopData = $stateOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($state); ?>" <?php if($selectedState === $state): echo 'selected'; endif; ?>><?php echo e($state); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <p class="ml-help" data-error-for="state"></p>
                                </div>
                                <div>
                                    <label class="ml-label">Country <span class="text-red-500">*</span></label>
                                    <input type="hidden" name="country" value="India" />
                                    <select class="ml-inp" disabled>
                                        <option value="India" selected>India</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="ml-label">Pin code <span class="text-red-500">*</span></label>
                                    <input name="pin_code" value="<?php echo e(old('pin_code', $user->pin_code)); ?>" required maxlength="6" inputmode="numeric" pattern="[0-9]*" class="ml-inp" data-validate="required|digits:6" data-label="Pin code" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                    <p class="ml-help" data-error-for="pin_code"></p>
                                </div>
                                <div>
                                    <label class="ml-label">Council state <span class="text-red-500">*</span></label>
                                    <input name="council_state" value="<?php echo e(old('council_state', $user->council_state)); ?>" required class="ml-inp" data-validate="required|min:2" data-label="Council state" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                    <p class="ml-help" data-error-for="council_state"></p>
                                </div>
                                <div class="md:col-span-2 xl:col-span-3">
                                    <label class="ml-label">Currently working</label>
                                    <textarea name="currently_working" rows="3" class="ml-inp" placeholder="Role, hospital/clinic, and experience (optional)" <?php if($profileLocked): echo 'disabled'; endif; ?>><?php echo e(old('currently_working', $user->currently_working)); ?></textarea>
                                </div>
                            </div>
                        </section>

                        <section class="space-y-5">
                            <h3 class="flex items-center gap-2 border-b border-[#351c42]/10 pb-3 text-xs font-bold uppercase tracking-widest text-[#965995]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#965995]"></span> Documents
                            </h3>
                            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                <div class="ml-upload-zone p-4">
                                    <label class="ml-label">
                                        Educational certificate
                                        <span class="text-red-500" data-profile-required="student_nurse,volunteer">*</span>
                                    </label>
                                    <?php if($user->educational_certificate_path): ?>
                                        <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="<?php echo e(asset('storage/' . $user->educational_certificate_path)); ?>">View current</a>
                                    <?php endif; ?>
                                    <?php if(!empty($pendingProfileDocs['educational_certificate'])): ?>
                                        <p class="mb-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-900">
                                            New file saved from your last attempt — it will be kept when you fix other fields.
                                            <a class="ml-1 text-[#965995] underline" target="_blank" href="<?php echo e(asset('storage/' . $pendingProfileDocs['educational_certificate'])); ?>">Preview</a>
                                        </p>
                                    <?php endif; ?>
                                    <input type="file" name="educational_certificate" class="w-full text-sm" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                    <?php $__errorArgs = ['educational_certificate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs font-semibold text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="ml-upload-zone p-4" data-profile-show="registered_nurse">
                                    <label class="ml-label">RNRM certificate copy <span class="text-red-500" data-profile-required="registered_nurse">*</span></label>
                                    <?php if($user->rnrm_certificate_path): ?>
                                        <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="<?php echo e(asset('storage/' . $user->rnrm_certificate_path)); ?>">View current</a>
                                    <?php endif; ?>
                                    <?php if(!empty($pendingProfileDocs['rnrm_certificate'])): ?>
                                        <p class="mb-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-900">
                                            New file saved from your last attempt — it will be kept when you fix other fields.
                                            <a class="ml-1 text-[#965995] underline" target="_blank" href="<?php echo e(asset('storage/' . $pendingProfileDocs['rnrm_certificate'])); ?>">Preview</a>
                                        </p>
                                    <?php endif; ?>
                                    <input type="file" name="rnrm_certificate" class="w-full text-sm" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                    <?php $__errorArgs = ['rnrm_certificate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs font-semibold text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="ml-upload-zone p-4" data-profile-show="student_nurse">
                                    <label class="ml-label">Student ID (card) <span class="text-red-500" data-profile-required="student_nurse">*</span></label>
                                    <?php if($user->student_id_card_path): ?>
                                        <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="<?php echo e(asset('storage/' . $user->student_id_card_path)); ?>">View current</a>
                                    <?php endif; ?>
                                    <?php if(!empty($pendingProfileDocs['student_id_card'])): ?>
                                        <p class="mb-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-900">
                                            New file saved from your last attempt — it will be kept when you fix other fields.
                                            <a class="ml-1 text-[#965995] underline" target="_blank" href="<?php echo e(asset('storage/' . $pendingProfileDocs['student_id_card'])); ?>">Preview</a>
                                        </p>
                                    <?php endif; ?>
                                    <input type="file" name="student_id_card" class="w-full text-sm" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                    <?php $__errorArgs = ['student_id_card'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs font-semibold text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="ml-upload-zone p-4">
                                    <label class="ml-label">Aadhar card <span class="text-red-500">*</span></label>
                                    <?php if($user->aadhar_card_path): ?>
                                        <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="<?php echo e(asset('storage/' . $user->aadhar_card_path)); ?>">View current</a>
                                    <?php endif; ?>
                                    <?php if(!empty($pendingProfileDocs['aadhar_card'])): ?>
                                        <p class="mb-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-900">
                                            New file saved from your last attempt — it will be kept when you fix other fields.
                                            <a class="ml-1 text-[#965995] underline" target="_blank" href="<?php echo e(asset('storage/' . $pendingProfileDocs['aadhar_card'])); ?>">Preview</a>
                                        </p>
                                    <?php endif; ?>
                                    <input type="file" name="aadhar_card" class="w-full text-sm" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                    <?php $__errorArgs = ['aadhar_card'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs font-semibold text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="ml-upload-zone p-4">
                                    <label class="ml-label">Passport size photo <span class="text-red-500">*</span></label>
                                    <?php if($user->passport_photo_path): ?>
                                        <a class="mb-2 inline-block text-xs font-semibold text-[#965995]" target="_blank" href="<?php echo e(asset('storage/' . $user->passport_photo_path)); ?>">View current</a>
                                    <?php endif; ?>
                                    <?php if(!empty($pendingProfileDocs['passport_photo'])): ?>
                                        <p class="mb-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-900">
                                            New file saved from your last attempt — it will be kept when you fix other fields.
                                            <a class="ml-1 text-[#965995] underline" target="_blank" href="<?php echo e(asset('storage/' . $pendingProfileDocs['passport_photo'])); ?>">Preview</a>
                                        </p>
                                    <?php endif; ?>
                                    <input type="file" name="passport_photo" accept="image/*" class="w-full text-sm" <?php if($profileLocked): echo 'disabled'; endif; ?> />
                                    <?php $__errorArgs = ['passport_photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-xs font-semibold text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                        </section>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-[#351c42]/10 pt-6 sm:flex-row sm:justify-end sm:gap-4">
                        <?php if($profileLocked): ?>
                            <button type="button" disabled class="ml-btn-primary w-full sm:w-auto cursor-not-allowed opacity-55" aria-disabled="true">Save &amp; continue</button>
                        <?php else: ?>
                            <button type="submit" class="ml-btn-primary w-full sm:w-auto">Save &amp; continue</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
        (() => {
            const form = document.getElementById("member-profile-form");
            if (!form) return;
            const isProfileLocked = form.hasAttribute("data-profile-locked");

            const typeSelect = form.querySelector("[data-profile-type-select]");
            const showEls = Array.from(form.querySelectorAll("[data-profile-show]"));
            const reqEls = Array.from(form.querySelectorAll("[data-profile-required]"));

            function setVisibility(type) {
                showEls.forEach((el) => {
                    const allowed = (el.getAttribute("data-profile-show") || "").split(",").map(s => s.trim()).filter(Boolean);
                    const shouldShow = allowed.length === 0 || allowed.includes(type);
                    el.classList.toggle("hidden", !shouldShow);
                });

                // Mark only visible fields as required (client-side UX)
                reqEls.forEach((mark) => {
                    const allowed = (mark.getAttribute("data-profile-required") || "").split(",").map(s => s.trim()).filter(Boolean);
                    const active = allowed.includes(type);
                    mark.classList.toggle("hidden", !active);
                });
            }

            setVisibility(typeSelect?.value || "");
            typeSelect?.addEventListener("change", () => setVisibility(typeSelect.value));

            // Keep type-based visibility active even in read-only mode.
            if (isProfileLocked) return;

            const fields = Array.from(form.querySelectorAll("[data-validate]"));
            const getErrorEl = (name) => form.querySelector(`[data-error-for="${name}"]`);

            const checkRules = (field) => {
                const rules = (field.dataset.validate || "").split("|");
                const label = field.dataset.label || "This field";
                const value = (field.value || "").trim();

                for (const rule of rules) {
                    if (rule === "required" && !value) return `${label} is required.`;
                    if (rule.startsWith("min:")) {
                        const min = Number(rule.split(":")[1] || 0);
                        if (value && value.length < min) return `${label} must be at least ${min} characters.`;
                    }
                    if (rule.startsWith("digits:")) {
                        const count = Number(rule.split(":")[1] || 0);
                        if (!new RegExp(`^\\d{${count}}$`).test(value)) return `${label} must be ${count} digits.`;
                    }
                }
                return "";
            };

            const paintValidation = (field, message) => {
                const errorEl = getErrorEl(field.name);
                field.classList.toggle("is-invalid", Boolean(message));
                field.setAttribute("aria-invalid", message ? "true" : "false");
                if (errorEl) errorEl.textContent = message;
            };

            fields.forEach((field) => {
                ["input", "change", "blur"].forEach((eventName) => {
                    field.addEventListener(eventName, () => {
                        if (field.name === "pin_code") field.value = field.value.replace(/\D/g, "").slice(0, 6);
                        if (field.name === "mobile") field.value = field.value.replace(/\D/g, "").slice(0, 10);
                        paintValidation(field, checkRules(field));
                    });
                });
            });

            form.addEventListener("submit", (e) => {
                let hasError = false;
                fields.forEach((field) => {
                    if (field.name === "pin_code") field.value = field.value.replace(/\D/g, "").slice(0, 6);
                    if (field.name === "mobile") field.value = field.value.replace(/\D/g, "").slice(0, 10);
                    const message = checkRules(field);
                    paintValidation(field, message);
                    if (message) hasError = true;
                });
                if (hasError) {
                    e.preventDefault();
                    form.querySelector(".is-invalid")?.focus();
                }
            });
        })();
    </script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('member.layouts.gnat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/member/profile/edit.blade.php ENDPATH**/ ?>