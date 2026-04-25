<script>
    (() => {
        const sidebar = document.getElementById("md-sidebar");
        const toggle = document.querySelector("[data-md-sidebar-toggle]");
        const backdrop = document.getElementById("md-sidebar-backdrop");

        function closeSidebar() {
            sidebar?.classList.add("-translate-x-full");
            backdrop?.classList.add("hidden");
            toggle?.setAttribute("aria-expanded", "false");
            document.body.classList.remove("md-drawer-open");
        }
        function openSidebar() {
            sidebar?.classList.remove("-translate-x-full");
            backdrop?.classList.remove("hidden");
            toggle?.setAttribute("aria-expanded", "true");
            document.body.classList.add("md-drawer-open");
        }

        toggle?.addEventListener("click", () => {
            const open = toggle.getAttribute("aria-expanded") === "true";
            if (open) closeSidebar();
            else openSidebar();
        });
        backdrop?.addEventListener("click", closeSidebar);
        window.addEventListener("resize", () => {
            if (window.innerWidth >= 1024) closeSidebar();
        });

        document.querySelectorAll("[data-md-nav]").forEach((a) => {
            a.addEventListener("click", () => {
                if (window.innerWidth < 1024) closeSidebar();
                document.querySelectorAll("[data-md-nav]").forEach((l) => l.classList.remove("is-active"));
                a.classList.add("is-active");
            });
        });
    })();
</script>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\member\partials\member-portal-drawer-script.blade.php ENDPATH**/ ?>