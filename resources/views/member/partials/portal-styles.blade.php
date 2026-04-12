<style>
    html { scroll-behavior: smooth; }
    body { font-family: "DM Sans", system-ui, sans-serif; }
    [x-cloak] { display: none !important; }
    .md-page-bg {
        background-color: #f8f6fa;
        background-image:
            radial-gradient(ellipse 70% 45% at 50% -15%, rgba(53, 28, 66, 0.09), transparent),
            radial-gradient(ellipse 50% 35% at 100% 20%, rgba(150, 89, 149, 0.12), transparent);
        min-height: 100vh;
    }
    .md-glass-header {
        background: rgba(255, 255, 255, 0.78);
        backdrop-filter: blur(14px);
        border-bottom: 1px solid rgba(53, 28, 66, 0.07);
    }
    .site-header-main {
        background: linear-gradient(180deg, #f5f3f9 0%, #eae7f3 100%);
        border-bottom: 1px solid rgba(53, 28, 66, 0.09);
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.6) inset;
    }
    .md-nav-link {
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #5c5a6b;
        transition: color 0.2s ease;
    }
    .md-nav-link:hover { color: #351c42; }
    body.md-drawer-open { overflow: hidden; }
    .md-sidebar-link {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        border-radius: 0.875rem;
        padding: 0.65rem 0.9rem;
        font-size: 0.8125rem;
        font-weight: 600;
        color: rgba(53, 28, 66, 0.72);
        transition: background 0.2s, color 0.2s;
    }
    .md-sidebar-link:hover { background: rgba(53, 28, 66, 0.06); color: #351c42; }
    .md-sidebar-link.is-active {
        background: linear-gradient(135deg, rgba(53, 28, 66, 0.12), rgba(150, 89, 149, 0.1));
        color: #351c42;
        box-shadow: inset 0 0 0 1px rgba(53, 28, 66, 0.08);
    }
    .md-btn-interest {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9999px;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        font-weight: 700;
        background: #351c42;
        color: #fddc6a;
        transition: transform 0.15s ease, filter 0.2s ease;
    }
    .md-btn-interest:hover { filter: brightness(1.06); transform: translateY(-1px); }
    .md-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 110;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(8px);
    }
    .md-modal-overlay.is-open { display: flex; }
</style>
