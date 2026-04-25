<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Member sign in — GNAT Association'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo $__env->make('home.partials.styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: "DM Sans", system-ui, sans-serif; }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } }
        .ml-page-bg {
            background-color: #f8f6fc;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(150, 89, 149, 0.18), transparent),
                radial-gradient(ellipse 60% 40% at 100% 0%, rgba(253, 220, 106, 0.15), transparent),
                radial-gradient(ellipse 50% 30% at 0% 100%, rgba(53, 28, 66, 0.08), transparent);
            min-height: 100vh;
        }
        .ml-glass-header {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(53, 28, 66, 0.08);
        }
        .ml-nav-link {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #5c5a6b;
            transition: color 0.2s ease;
        }
        .ml-nav-link:hover { color: #351c42; }
        body.ml-menu-open { overflow: hidden; }
        .ml-mobile-panel { max-height: 0; overflow: hidden; transition: max-height 0.35s ease; }
        .ml-mobile-panel.is-open { max-height: 28rem; }
        .ml-tabs-track {
            display: flex;
            gap: 0.25rem;
            padding: 0.35rem;
            border-radius: 9999px;
            background: rgba(53, 28, 66, 0.06);
        }
        [data-auth-tab] {
            flex: 1;
            border-radius: 9999px;
            padding: 0.65rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: background 0.25s ease, color 0.25s ease, box-shadow 0.25s ease, transform 0.2s ease;
        }
        [data-auth-tab][aria-selected="false"] { color: #351c42; background: transparent; }
        [data-auth-tab][aria-selected="false"]:hover { background: rgba(255, 255, 255, 0.7); }
        [data-auth-tab][aria-selected="true"] {
            background: linear-gradient(135deg, #351c42 0%, #4a2660 100%);
            color: #fff;
            box-shadow: 0 4px 20px rgba(53, 28, 66, 0.35);
        }
        .ml-label { display: block; font-size: 0.8125rem; font-weight: 600; color: rgba(53, 28, 66, 0.78); margin-bottom: 0.5rem; }
        .ml-inp {
            width: 100%;
            border-radius: 1rem;
            border: 1px solid rgba(53, 28, 66, 0.1);
            background: rgba(255, 255, 255, 0.85);
            padding: 0.8125rem 1.125rem;
            font-size: 0.9375rem;
            color: #351c42;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .ml-inp::placeholder { color: rgba(53, 28, 66, 0.32); }
        .ml-inp:hover { border-color: rgba(150, 89, 149, 0.25); background: #fff; }
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
        /* Single combined field: +91 prefix + 10-digit input (one border, full width for digits) */
        .ml-inp-phone-wrap {
            display: flex;
            align-items: stretch;
            width: 100%;
            border-radius: 1rem;
            border: 1px solid rgba(53, 28, 66, 0.1);
            background: rgba(255, 255, 255, 0.85);
            overflow: hidden;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }
        .ml-inp-phone-wrap:hover {
            border-color: rgba(150, 89, 149, 0.25);
            background: #fff;
        }
        .ml-inp-phone-wrap:focus-within {
            border-color: rgba(150, 89, 149, 0.55);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(150, 89, 149, 0.14);
        }
        .ml-inp-phone-wrap.is-invalid {
            border-color: rgba(220, 38, 38, 0.55);
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12);
            background: #fff;
        }
        .ml-inp-phone-prefix {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            padding: 0.8125rem 0.5rem 0.8125rem 1rem;
            font-size: 0.9375rem;
            font-weight: 600;
            color: rgba(53, 28, 66, 0.55);
            border-right: 1px solid rgba(53, 28, 66, 0.1);
            background: rgba(53, 28, 66, 0.035);
            user-select: none;
            pointer-events: none;
        }
        .ml-inp-phone-field {
            flex: 1;
            min-width: 0;
            border: none;
            background: transparent;
            padding: 0.8125rem 1.125rem 0.8125rem 0.75rem;
            font-size: 0.9375rem;
            color: #351c42;
            outline: none;
        }
        .ml-inp-phone-field::placeholder { color: rgba(53, 28, 66, 0.32); }
        .ml-card-elevated {
            box-shadow: 0 4px 6px -1px rgba(53, 28, 66, 0.06), 0 24px 48px -12px rgba(53, 28, 66, 0.14);
        }
        .ml-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border-radius: 9999px;
            padding: 0.875rem 1.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            background: linear-gradient(135deg, #351c42 0%, #4d2a5c 100%);
            color: #fddc6a;
            box-shadow: 0 8px 24px rgba(53, 28, 66, 0.28);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            border: none;
            cursor: pointer;
        }
        .ml-btn-primary:hover { filter: brightness(1.06); box-shadow: 0 12px 28px rgba(53, 28, 66, 0.32); transform: translateY(-1px); }
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
            cursor: pointer;
        }
        .ml-btn-secondary:hover { background: rgba(53, 28, 66, 0.04); border-color: rgba(53, 28, 66, 0.22); }
        .ml-password-wrap { position: relative; }
        .ml-password-wrap .ml-inp { padding-right: 3rem; }
        .ml-password-toggle {
            position: absolute;
            right: 0.65rem;
            top: 50%;
            transform: translateY(-50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            border: none;
            border-radius: 0.65rem;
            background: transparent;
            color: rgba(53, 28, 66, 0.45);
            cursor: pointer;
            transition: color 0.15s ease, background 0.15s ease;
        }
        .ml-password-toggle:hover { color: #965995; background: rgba(150, 89, 149, 0.1); }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="ml-page-bg text-[#351c42] antialiased" data-auth-default="<?php echo e($defaultTab ?? 'signin'); ?>">
    <?php echo $__env->make('member.partials.public-site-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="mx-auto w-full max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:py-20">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <script>
        (() => {
            const toggle = document.querySelector("[data-hamburger]");
            const overlay = document.querySelector("[data-drawer-overlay]");
            const drawer = document.getElementById("site-drawer");
            const closeBtn = document.querySelector("[data-drawer-close]");
            if (!toggle || !overlay || !drawer) return;

            function setOpen(open) {
                overlay.classList.toggle("is-open", open);
                drawer.classList.toggle("is-open", open);
                document.body.classList.toggle("site-drawer-open", open);
                toggle.setAttribute("aria-expanded", open ? "true" : "false");
                toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
                overlay.setAttribute("aria-hidden", open ? "false" : "true");
                drawer.setAttribute("aria-hidden", open ? "false" : "true");
            }

            function closeDrawer() { setOpen(false); }
            function openDrawer() { setOpen(true); }

            toggle.addEventListener("click", () => {
                if (drawer.classList.contains("is-open")) closeDrawer();
                else openDrawer();
            });
            overlay.addEventListener("click", closeDrawer);
            closeBtn?.addEventListener("click", closeDrawer);
            drawer.querySelectorAll("a[href*='#']").forEach((a) => {
                const href = a.getAttribute("href") || "";
                if (href.length > 1) a.addEventListener("click", () => closeDrawer());
            });
            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") closeDrawer();
            });
        })();
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>

<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/member/layouts/auth.blade.php ENDPATH**/ ?>