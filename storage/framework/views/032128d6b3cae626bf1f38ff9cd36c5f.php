<style>
    html {
        scroll-behavior: smooth;
    }

    @media (prefers-reduced-motion: reduce) {
        html {
            scroll-behavior: auto;
        }
    }

    body {
        font-family: "DM Sans", sans-serif;
    }
    .reveal-item {
        opacity: 0;
        transform: translateY(140px) scale(0.985);
        transition: opacity 600ms ease, transform 700ms ease;
        transition-delay: calc(var(--i, 0) * 140ms);
        will-change: opacity, transform;
    }
    .reveal-item.is-visible {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .stack-card {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        will-change: transform, opacity;
        transform: translateY(0%);
        transition:
            transform 0.7s cubic-bezier(0.22, 1, 0.36, 1),
            opacity 0.5s ease,
            filter 0.4s ease;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(10px);
    }

    #testimonialStack {
        perspective: 1000px;
        transform-style: preserve-3d;
    }
    #testimonialStack .stack-card {
        transform-style: preserve-3d;
        transform-origin: center top;
    }

    .click-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0;
        padding: 0 22px 0 60px;
        height: 60px;
        border-radius: 9999px;
        border: 2px solid #fddc6a;
        text-align: center;
        font-size: 15px;
        font-weight: 600;
        color: #311742;
        background-color: #fddc6a;
        text-decoration: none;
        box-sizing: border-box;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
    }

    .btn-style506 {
        position: relative;
        isolation: isolate;
        z-index: 0;
    }

    .btn-style506:hover .click-btn__label {
        color: #ffffff !important;
    }

    .click-btn__label {
        position: relative;
        z-index: 2;
    }

    .click-btn__icon {
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        text-align: left;
        z-index: 1;
        width: 40px;
        height: 40px;
        border-radius: 100px;
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        padding-left: 4px;
        background-color: #351c42;
        color: #ffffff;
        flex-shrink: 0;
        transition: all 0.35s cubic-bezier(0.65, 0, 0.076, 1);
    }

    .btn-style506:hover .click-btn__icon {
        width: calc(100% - 18px);
        background-color: #351c42;
        color: #ffffff !important;
    }

    .click-btn__icon svg {
        display: block;
        margin: 0;
    }

    button.click-btn {
        font-family: inherit;
        appearance: none;
    }

    .click-btn--sm {
        height: 46px;
        padding: 0 18px 0 50px;
        font-size: 14px;
    }

    .click-btn--sm .click-btn__icon {
        width: 32px;
        height: 32px;
        left: 8px;
        padding-left: 3px;
    }

    .btn-style506:hover .click-btn--sm .click-btn__icon {
        width: calc(100% - 16px);
    }

    .carousel-nav-btn {
        width: 44px;
        height: 44px;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fddc6a;
        background-color: #ffffff;
        color: #351c42;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(53, 28, 66, 0.1);
        transition:
            background-color 0.35s cubic-bezier(0.65, 0, 0.076, 1),
            color 0.35s ease,
            border-color 0.35s ease,
            box-shadow 0.35s ease,
            transform 0.2s ease;
    }

    .carousel-nav-btn:hover {
        background-color: #351c42;
        color: #ffffff;
        border-color: #351c42;
        box-shadow: 0 8px 22px rgba(53, 28, 66, 0.22);
        transform: translateY(-1px);
    }

    .carousel-nav-btn:active {
        transform: translateY(0);
    }

    @keyframes about2-bump-main {
        0%, 100% { transform: scale(1) translateY(0); }
        50% { transform: scale(1.035) translateY(-8px); }
    }

    @keyframes about2-bump-accent {
        0%, 100% { transform: scale(1) translateY(0); }
        50% { transform: scale(1.06) translateY(-6px); }
    }

    .about2-img-main {
        transform-origin: center center;
        animation: about2-bump-main 3s ease-in-out infinite;
        will-change: transform;
    }

    .about2-img-accent {
        transform-origin: center center;
        animation: about2-bump-accent 2.4s ease-in-out infinite;
        animation-delay: -1.2s;
        will-change: transform;
    }

    @media (prefers-reduced-motion: reduce) {
        .about2-img-main,
        .about2-img-accent {
            animation: none;
        }
    }

    .donate-amt-btn.is-selected {
        background-color: #fddc6a !important;
        color: #351c42;
        border-color: #fddc6a;
    }

    .site-header-main {
        background: linear-gradient(180deg, #f5f3f9 0%, #eae7f3 100%);
        border-bottom: 1px solid rgba(53, 28, 66, 0.09);
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.6) inset;
    }

    /* Desktop primary nav — clear hover / focus affordance */
    .site-nav-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        padding: 0.45rem 0.85rem;
        border-radius: 0.65rem;
        color: #3d4d5c;
        transition:
            color 0.2s ease,
            background-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .site-nav-link:hover {
        color: #351c42;
        background-color: rgba(53, 28, 66, 0.07);
        box-shadow: 0 1px 0 rgba(150, 89, 149, 0.25);
    }

    .site-nav-link:focus-visible {
        outline: none;
        color: #351c42;
        background-color: rgba(150, 89, 149, 0.12);
        box-shadow: 0 0 0 2px rgba(150, 89, 149, 0.45);
    }

    .site-nav-link::after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: 0.2rem;
        width: 0;
        height: 2px;
        border-radius: 2px;
        background: linear-gradient(90deg, #965995, #351c42);
        transform: translateX(-50%);
        transition: width 0.22s ease;
        pointer-events: none;
    }

    .site-nav-link:hover::after,
    .site-nav-link:focus-visible::after {
        width: calc(100% - 1.2rem);
    }

    @media (prefers-reduced-motion: reduce) {
        .site-nav-link,
        .site-nav-link::after {
            transition: none;
        }
    }

    .click-btn--nav {
        height: 50px;
        padding: 0 16px 0 52px;
        font-size: 14px;
    }

    .click-btn--nav .click-btn__icon {
        width: 36px;
        height: 36px;
        left: 8px;
    }

    .btn-style506:hover .click-btn--nav .click-btn__icon {
        width: calc(100% - 16px);
    }

    body.site-drawer-open {
        overflow: hidden;
    }

    .site-drawer-overlay {
        position: fixed;
        inset: 0;
        z-index: 60;
        background: rgba(18, 10, 28, 0.55);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.35s ease, visibility 0.35s ease;
    }

    .site-drawer-overlay.is-open {
        opacity: 1;
        visibility: visible;
    }

    .site-drawer {
        position: fixed;
        top: 0;
        right: 0;
        z-index: 70;
        height: 100%;
        max-height: 100dvh;
        width: min(100%, 420px);
        background: #351c42;
        box-shadow: -16px 0 48px rgba(0, 0, 0, 0.25);
        transform: translateX(100%);
        transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
        display: flex;
        flex-direction: column;
    }

    .site-drawer.is-open {
        transform: translateX(0);
    }

    .hamburger-inner {
        display: flex;
        flex-direction: column;
        gap: 6px;
        width: 26px;
    }

    .hamburger-line {
        display: block;
        height: 2px;
        width: 100%;
        border-radius: 2px;
        background: #351c42;
        transition: transform 0.3s ease, opacity 0.25s ease;
        transform-origin: center;
    }

    [data-hamburger][aria-expanded="true"] .hamburger-inner .hamburger-line:nth-child(1) {
        transform: translateY(8px) rotate(45deg);
    }
    [data-hamburger][aria-expanded="true"] .hamburger-inner .hamburger-line:nth-child(2) {
        opacity: 0;
    }
    [data-hamburger][aria-expanded="true"] .hamburger-inner .hamburger-line:nth-child(3) {
        transform: translateY(-8px) rotate(-45deg);
    }

    .member-login-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 9999px;
        background: #e4e7f2;
        color: #351c42;
        flex-shrink: 0;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 1px 2px rgba(53, 28, 66, 0.06);
    }

    .member-login-btn:hover {
        background: #351c42;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(53, 28, 66, 0.2);
    }

    .member-login-btn:focus-visible {
        outline: 2px solid #fddc6a;
        outline-offset: 2px;
    }

    .member-login-btn svg {
        width: 22px;
        height: 22px;
    }

    .back-to-top-floating {
        position: fixed;
        right: 0;
        bottom: max(5.5rem, 18vh);
        z-index: 55;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.65rem;
        padding: 0.75rem 0.35rem 0.75rem 0.5rem;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(53, 28, 66, 0.12);
        border-right: none;
        border-radius: 0.75rem 0 0 0.75rem;
        box-shadow: -4px 4px 20px rgba(53, 28, 66, 0.12);
        color: #351c42;
        text-decoration: none;
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .back-to-top-floating:hover {
        background: #351c42;
        color: #fddc6a;
    }

    .back-to-top-floating:hover .back-to-top-floating__bar {
        background: #fddc6a;
    }

    .back-to-top-floating__bar {
        width: 3px;
        height: 2.25rem;
        border-radius: 9999px;
        background: #fddc6a;
        transition: background 0.2s ease;
    }

    .back-to-top-floating__text {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .welcome-bottom-card {
        position: fixed;
        left: max(1rem, env(safe-area-inset-left));
        bottom: max(1rem, env(safe-area-inset-bottom));
        z-index: 60;
        width: min(22rem, calc(100vw - 2rem));
        transform: translateY(calc(100% + 1.5rem));
        opacity: 0;
        pointer-events: none;
        transition:
            transform 0.5s cubic-bezier(0.22, 1, 0.36, 1),
            opacity 0.4s ease;
    }

    .welcome-bottom-card.is-visible {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .welcome-bottom-card.is-dismissed {
        transform: translateY(calc(100% + 1.5rem));
        opacity: 0;
        pointer-events: none;
    }

    @media (prefers-reduced-motion: reduce) {
        .welcome-bottom-card {
            transition: opacity 0.2s ease;
            transform: translateY(0);
        }
        .welcome-bottom-card:not(.is-visible) {
            opacity: 0;
            pointer-events: none;
        }
        .welcome-bottom-card.is-visible {
            opacity: 1;
            pointer-events: auto;
        }
        .welcome-bottom-card.is-dismissed {
            opacity: 0;
        }
    }

    .banner-vertical-nav {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 0.4rem 0.85rem 0.65rem;
        background: rgba(22, 16, 36, 0.88);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: 9999px 0 0 9999px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-right: none;
        box-shadow: -6px 0 28px rgba(0, 0, 0, 0.18);
    }

    .banner-vertical-nav__btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 9999px;
        border: 1px solid rgba(255, 255, 255, 0.45);
        background: transparent;
        color: #ffffff;
        cursor: pointer;
        transition:
            background-color 0.2s ease,
            border-color 0.2s ease,
            transform 0.15s ease;
    }

    .banner-vertical-nav__btn:hover {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(255, 255, 255, 0.75);
    }

    .banner-vertical-nav__btn:focus-visible {
        outline: 2px solid #c084fc;
        outline-offset: 2px;
    }

    .banner-vertical-nav__btn:active {
        transform: scale(0.96);
    }

    .banner-vertical-nav__line {
        width: 2px;
        height: 2.35rem;
        border-radius: 9999px;
        background: linear-gradient(180deg, #a855f7, #6366f1);
        box-shadow: 0 0 14px rgba(168, 85, 247, 0.55);
    }

    .banner-vertical-nav__num {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.12em;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }

    [data-events-accordion-item][data-events-open="true"] [data-events-header-summary] {
        display: none !important;
    }
    [data-events-accordion-item][data-events-open="true"] [data-events-trigger-icon] {
        display: none !important;
    }
    [data-events-accordion-item][data-events-open="true"] [data-events-accordion-trigger] {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    #gallery [data-gallery-filter] {
        border-radius: 9999px;
        border: 2px solid rgba(53, 28, 66, 0.15);
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(8px);
        padding: 0.5rem 1.1rem;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #351c42;
        transition: border-color 0.2s ease, background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 1px 0 rgba(53, 28, 66, 0.04);
    }
    @media (min-width: 640px) {
        #gallery [data-gallery-filter] {
            padding: 0.55rem 1.35rem;
            font-size: 0.8rem;
        }
    }
    #gallery [data-gallery-filter][aria-pressed="true"] {
        border-color: #351c42;
        background: #351c42;
        color: #fff;
        box-shadow: 0 6px 20px rgba(53, 28, 66, 0.22);
    }
    #gallery [data-gallery-filter][aria-pressed="false"]:hover {
        border-color: #965995;
        color: #965995;
    }
</style>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views/home/partials/styles.blade.php ENDPATH**/ ?>