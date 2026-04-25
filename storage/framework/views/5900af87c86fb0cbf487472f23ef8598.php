<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Verify OTP — GNAT Association</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: "DM Sans", system-ui, sans-serif; }
        .ml-page-bg {
            background-color: #f8f6fc;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(150, 89, 149, 0.18), transparent),
                radial-gradient(ellipse 60% 40% at 100% 0%, rgba(253, 220, 106, 0.15), transparent);
            min-height: 100vh;
        }
        .otp-box {
            width: 3.25rem;
            height: 3.5rem;
            text-align: center;
            font-size: 1.375rem;
            font-weight: 700;
            border-radius: 1rem;
            border: 1px solid rgba(53, 28, 66, 0.12);
            background: linear-gradient(180deg, #fff 0%, #faf8fc 100%);
            box-shadow: 0 2px 8px rgba(53, 28, 66, 0.06);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
        }
        .otp-box:focus {
            border-color: #965995;
            box-shadow: 0 0 0 4px rgba(150, 89, 149, 0.18);
            transform: translateY(-1px);
            outline: none;
        }
        @media (min-width: 480px) {
            .otp-box { width: 3.75rem; height: 3.75rem; font-size: 1.5rem; }
        }
        .ml-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.875rem 1.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            background: linear-gradient(135deg, #351c42 0%, #4d2a5c 100%);
            color: #fddc6a;
            box-shadow: 0 8px 24px rgba(53, 28, 66, 0.28);
            border: none;
            cursor: pointer;
        }
        .ml-btn-primary:hover { filter: brightness(1.06); transform: translateY(-1px); }
        .ml-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            padding: 0.875rem 1.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            background: #fff;
            color: #351c42;
            border: 1px solid rgba(53, 28, 66, 0.14);
            text-decoration: none;
            cursor: pointer;
        }
        .ml-card-elevated {
            box-shadow: 0 4px 6px -1px rgba(53, 28, 66, 0.06), 0 24px 48px -12px rgba(53, 28, 66, 0.14);
        }
    </style>
</head>
<body class="ml-page-bg text-[#351c42] antialiased">
    <main class="mx-auto flex min-h-screen max-w-lg flex-col justify-center px-4 py-12">
        <a href="<?php echo e(route('home')); ?>" class="mb-8 inline-flex items-center gap-2 text-sm font-semibold text-[#965995] hover:text-[#351c42]">
            ← GNAT Association home
        </a>

        <div class="overflow-hidden rounded-3xl border border-white/70 bg-white/80 p-8 shadow-2xl shadow-[#351c42]/12 backdrop-blur-md sm:p-10 ml-card-elevated">
            <div class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#965995]/20 to-[#fddc6a]/30">
                <svg class="h-7 w-7 text-[#351c42]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-center text-xl font-bold tracking-tight text-[#351c42] sm:text-2xl">Verify your identity</h1>
            <p class="mx-auto mt-3 max-w-sm text-center text-sm leading-relaxed text-[#351c42]/65">
                Enter the code sent to <span class="font-semibold text-[#351c42]"><?php echo e($maskedMobile); ?></span>
            </p>

            <?php if($generatedOtp): ?>
                <div class="mt-5 rounded-2xl border border-[#965995]/20 bg-gradient-to-br from-[#965995]/8 to-transparent px-4 py-3 text-center" aria-live="polite">
                    <p class="text-[0.65rem] font-bold uppercase tracking-widest text-[#965995]">Demo / dev code</p>
                    <p class="mt-1 font-mono text-xl font-bold tracking-[0.35em] text-[#351c42]"><?php echo e($generatedOtp); ?></p>
                </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-center text-sm font-semibold text-emerald-800">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('member.otp.verify')); ?>" class="mt-8" id="otp-form">
                <?php echo csrf_field(); ?>
                <p class="text-center text-sm font-semibold text-[#351c42]">Enter 4-digit code</p>
                <div class="mt-5 flex justify-center gap-2.5 sm:gap-3.5" role="group" aria-label="One-time code digits">
                    <input type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-otp-digit="0" class="otp-box text-[#351c42]" aria-label="Digit 1" autocomplete="one-time-code" />
                    <input type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-otp-digit="1" class="otp-box text-[#351c42]" aria-label="Digit 2" />
                    <input type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-otp-digit="2" class="otp-box text-[#351c42]" aria-label="Digit 3" />
                    <input type="text" inputmode="numeric" maxlength="1" pattern="[0-9]*" data-otp-digit="3" class="otp-box text-[#351c42]" aria-label="Digit 4" />
                </div>
                <input type="hidden" name="code" id="otp-code" value="" />
                <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-3 text-center text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                <div class="mt-8 text-center">
                    <button type="submit" form="resend-form" class="text-sm font-semibold text-[#965995] transition hover:text-[#351c42]">Resend code</button>
                </div>

                <div class="mt-10 flex flex-col-reverse gap-3 sm:flex-row sm:justify-center sm:gap-4">
                    <a href="<?php echo e(route('member.login')); ?>" class="ml-btn-secondary w-full justify-center sm:w-auto">Cancel</a>
                    <button type="submit" class="ml-btn-primary w-full min-w-[10rem] justify-center sm:w-auto">Verify</button>
                </div>
            </form>

            <form id="resend-form" method="POST" action="<?php echo e(route('member.otp.resend')); ?>" class="hidden">
                <?php echo csrf_field(); ?>
            </form>
        </div>
    </main>

    <script>
        (() => {
            const otpInputs = Array.from(document.querySelectorAll("[data-otp-digit]"));
            const hidden = document.getElementById("otp-code");
            const form = document.getElementById("otp-form");

            function sync() {
                if (hidden) hidden.value = otpInputs.map((i) => i.value).join("");
            }

            otpInputs.forEach((input, idx) => {
                input.addEventListener("input", () => {
                    input.value = input.value.replace(/\D/g, "").slice(0, 1);
                    sync();
                    if (input.value && idx < otpInputs.length - 1) otpInputs[idx + 1].focus();
                });
                input.addEventListener("keydown", (ev) => {
                    if (ev.key === "Backspace" && !input.value && idx > 0) otpInputs[idx - 1].focus();
                });
            });

            otpInputs[0]?.addEventListener("paste", (ev) => {
                const t = (ev.clipboardData || window.clipboardData).getData("text").replace(/\D/g, "").slice(0, 4);
                if (t.length) {
                    ev.preventDefault();
                    t.split("").forEach((ch, i) => { if (otpInputs[i]) otpInputs[i].value = ch; });
                    sync();
                    otpInputs[Math.min(t.length, 3)]?.focus();
                }
            });

            form?.addEventListener("submit", (e) => {
                sync();
                if ((hidden?.value || "").length !== 4) {
                    e.preventDefault();
                    alert("Please enter the 4-digit code.");
                }
            });

            otpInputs[0]?.focus();
        })();
    </script>
</body>
</html>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\auth\otp.blade.php ENDPATH**/ ?>