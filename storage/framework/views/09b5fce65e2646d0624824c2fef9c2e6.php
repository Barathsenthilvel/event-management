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

<script>
    (() => {
        const viewport = document.getElementById("bannerCarouselViewport");
        const track = document.getElementById("bannerCarouselTrack");
        const prevBtn = document.getElementById("bannerCarouselPrev");
        const nextBtn = document.getElementById("bannerCarouselNext");
        const dotsWrap = document.getElementById("bannerCarouselDots");
        const slideLabel = document.getElementById("bannerSlideLabel");
        const lightbox = document.getElementById("bannerImageLightbox");
        const lightboxImg = document.getElementById("bannerLightboxImg");
        const lightboxLink = document.getElementById("bannerLightboxLink");
        const lightboxBackdrop = lightbox?.querySelector("[data-banner-lightbox-backdrop]");
        const lightboxCloseBtn = lightbox?.querySelector("[data-banner-lightbox-close]");
        if (!viewport || !track || !prevBtn || !nextBtn || !dotsWrap) return;

        const slides = () => Array.from(track.querySelectorAll(".banner-slide"));
        let index = 0;
        let autoplayId = null;
        const AUTOPLAY_MS = 5500;
        let touchStartX = null;
        let lightboxOpen = false;

        function openBannerLightbox(slideEl) {
            if (!lightbox || !lightboxImg || !slideEl) return;
            const img = slideEl.querySelector("img[data-banner-photo]") || slideEl.querySelector("img");
            if (!img) return;
            lightboxOpen = true;
            stopAutoplay();
            lightboxImg.src = img.currentSrc || img.src;
            lightboxImg.alt = img.alt || "";
            const href = slideEl.getAttribute("href") || slideEl.getAttribute("data-banner-href") || "#";
            if (lightboxLink) {
                lightboxLink.href = href;
                lightboxLink.style.display = href === "#" ? "none" : "inline-flex";
            }
            lightbox.classList.remove("hidden");
            lightbox.classList.add("flex");
            lightbox.setAttribute("aria-hidden", "false");
            document.body.style.overflow = "hidden";
            lightboxCloseBtn?.focus();
        }

        function closeBannerLightbox() {
            if (!lightbox || !lightboxOpen) return;
            lightboxOpen = false;
            lightbox.classList.add("hidden");
            lightbox.classList.remove("flex");
            lightbox.setAttribute("aria-hidden", "true");
            document.body.style.overflow = "";
            if (lightboxImg) {
                lightboxImg.src = "";
                lightboxImg.alt = "";
            }
            restartAutoplay();
        }

        track.addEventListener("click", (e) => {
            const expand = e.target.closest("[data-banner-expand]");
            if (!expand) return;
            e.preventDefault();
            e.stopPropagation();
            const slide = expand.closest(".banner-slide");
            if (slide) openBannerLightbox(slide);
        });

        lightboxCloseBtn?.addEventListener("click", () => closeBannerLightbox());
        lightboxBackdrop?.addEventListener("click", () => closeBannerLightbox());
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && lightboxOpen) closeBannerLightbox();
        });

        function gapPx() {
            const s = window.getComputedStyle(track);
            const g = parseFloat(s.gap || s.columnGap || "0");
            return Number.isFinite(g) ? g : 0;
        }

        function pad2(i) { return String(i).padStart(2, "0"); }

        function updateSlideLabel() {
            if (!slideLabel) return;
            const n = slides().length;
            if (n === 0) return;
            slideLabel.innerHTML =
                `<span class="text-[#fddc6a]">${pad2(index + 1)}</span>` +
                `<span class="text-white/50"> / </span>` +
                `<span class="text-white/75">${pad2(n)}</span>`;
        }

        function layout() {
            const n = slides().length;
            if (n === 0) return;
            const vw = viewport.getBoundingClientRect().width;
            const gap = gapPx();
            const slideW = Math.max(1, vw);
            slides().forEach((el) => {
                el.style.width = `${slideW}px`;
                el.style.flex = "0 0 auto";
            });
            const step = slideW + gap;
            track.style.transform = `translateX(${-index * step}px)`;
            dotsWrap.querySelectorAll("[data-banner-dot]").forEach((btn, i) => {
                const on = i === index;
                btn.setAttribute("aria-current", on ? "true" : "false");
                btn.className =
                    "group relative h-2 rounded-full transition-all duration-300 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-[#fddc6a] focus-visible:ring-offset-2 focus-visible:ring-offset-transparent " +
                    (on ? "w-8 bg-[#fddc6a] shadow-[0_0_16px_rgba(253,220,106,0.45)]" : "w-2 bg-white/35 hover:bg-white/55");
            });
            updateSlideLabel();
        }

        function renderDots() {
            const n = slides().length;
            dotsWrap.innerHTML = "";
            for (let i = 0; i < n; i++) {
                const btn = document.createElement("button");
                btn.type = "button";
                btn.setAttribute("data-banner-dot", "");
                btn.setAttribute("aria-label", `Show banner ${i + 1} of ${n}`);
                btn.addEventListener("click", () => {
                    index = i;
                    track.style.transition = "transform 520ms cubic-bezier(0.22, 1, 0.36, 1)";
                    layout();
                    restartAutoplay();
                });
                dotsWrap.appendChild(btn);
            }
        }

        function go(delta) {
            const n = slides().length;
            if (n === 0) return;
            index = (index + delta + n) % n;
            track.style.transition = "transform 520ms cubic-bezier(0.22, 1, 0.36, 1)";
            layout();
        }

        function stopAutoplay() {
            if (autoplayId) clearInterval(autoplayId);
            autoplayId = null;
        }

        function restartAutoplay() {
            stopAutoplay();
            if (window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
            autoplayId = window.setInterval(() => go(1), AUTOPLAY_MS);
        }

        prevBtn.addEventListener("click", () => { go(-1); restartAutoplay(); });
        nextBtn.addEventListener("click", () => { go(1); restartAutoplay(); });

        const chrome = document.querySelector(".banner-carousel-chrome");
        viewport.addEventListener("mouseenter", () => { if (!lightboxOpen) stopAutoplay(); });
        viewport.addEventListener("mouseleave", () => { if (!lightboxOpen) restartAutoplay(); });
        chrome?.addEventListener("mouseenter", () => { if (!lightboxOpen) stopAutoplay(); });
        chrome?.addEventListener("mouseleave", () => { if (!lightboxOpen) restartAutoplay(); });

        viewport.addEventListener("touchstart", (e) => { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
        viewport.addEventListener("touchend", (e) => {
            if (touchStartX == null) return;
            const dx = e.changedTouches[0].screenX - touchStartX;
            touchStartX = null;
            if (Math.abs(dx) < 40) return;
            if (dx < 0) go(1); else go(-1);
            restartAutoplay();
        }, { passive: true });

        renderDots();
        layout();
        if (!window.matchMedia || !window.matchMedia("(prefers-reduced-motion: reduce)").matches) restartAutoplay();

        let resizeTimer = null;
        window.addEventListener("resize", () => {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(() => layout(), 120);
        });
    })();
</script>

<script>
    (() => {
        const stackScroll = document.getElementById("volunteerStackScroll");
        const stack = document.getElementById("volunteerStack");
        const cards = Array.from(stack?.querySelectorAll(".stack-card") ?? []);
        if (!stackScroll || !stack || cards.length === 0) return;

        const prefersReduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        let cardH = 320;
        let maxT = cards.length - 1;
        let currentT = 0;
        const stepDurationMs = 3000;
        let autoStart = 0;
        let started = false;
        let rafId = 0;

        function measure() {
            const rect = cards[0].getBoundingClientRect();
            if (rect.height) cardH = rect.height;
            maxT = Math.max(0, cards.length - 1);
            stackScroll.style.height = "100px";
        }

        function renderFromT(t) {
            if (prefersReduced) {
                cards.forEach((c, i) => {
                    c.style.transform = `translateY(${i === 0 ? 0 : 100}%)`;
                    c.style.opacity = i === 0 ? "1" : "0";
                });
                return;
            }
            cards.forEach((card, i) => {
                const dist = i - t;
                const y = dist * 90;
                const scale = 1 - Math.abs(dist) * 0.08;
                const blur = Math.abs(dist) > 0.5 ? 2 : 0;
                card.style.transform = `translateY(${y}%) scale(${scale})`;
                card.style.filter = `blur(${blur}px)`;
                let opacity = 1;
                if (dist > 1.2) opacity = 0;
                else if (dist < -0.8) opacity = 0;
                else opacity = Math.max(0, 1 - Math.abs(dist) * 0.4);
                card.style.opacity = String(opacity);
                card.style.zIndex = String(1000 - Math.abs(dist) * 10);
                card.style.pointerEvents = opacity === 0 ? "none" : "auto";
            });
        }

        function startAutoLoop() {
            if (started) return;
            started = true;
            autoStart = performance.now();
            measure();
            if (maxT <= 0) {
                currentT = 0;
                renderFromT(0);
                return;
            }
            const totalSpanMs = Math.max(1, maxT * stepDurationMs);
            currentT = 0;
            renderFromT(0);
            (function autoTick(now) {
                const elapsed = now - autoStart;
                const cycleMs = totalSpanMs * 2;
                const p = (elapsed % cycleMs) / totalSpanMs;
                const t = p <= 1 ? p * maxT : (2 - p) * maxT;
                currentT = t;
                renderFromT(t);
                rafId = requestAnimationFrame(autoTick);
            })(performance.now());
        }

        function boot() {
            requestAnimationFrame(() => requestAnimationFrame(startAutoLoop));
        }

        if ("IntersectionObserver" in window) {
            const obs = new IntersectionObserver((entries) => {
                if (entries.some((e) => e.isIntersecting)) {
                    obs.disconnect();
                    boot();
                }
            }, { threshold: 0.2 });
            obs.observe(stackScroll);
        }
        if (document.readyState === "complete" || document.readyState === "interactive") boot();
        else window.addEventListener("DOMContentLoaded", boot, { once: true });
        window.addEventListener("load", boot, { once: true });

        window.addEventListener("resize", () => {
            measure();
            renderFromT(Math.min(maxT, currentT));
        });
    })();
</script>

<script>
    (() => {
        const stackScroll = document.getElementById("testimonialStackScroll");
        const stack = document.getElementById("testimonialStack");
        const cards = Array.from(stack?.querySelectorAll(".stack-card") ?? []);
        if (!stackScroll || !stack || cards.length === 0) return;

        const prefersReduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        let maxT = cards.length - 1;
        let currentT = 0;
        const stepDurationMs = 3000;
        let autoStart = 0;
        let started = false;

        function measure() {
            maxT = Math.max(0, cards.length - 1);
        }

        function renderFromT(t) {
            if (prefersReduced) {
                cards.forEach((c, i) => {
                    c.style.transform = `translateY(${i === 0 ? 0 : 100}%)`;
                    c.style.opacity = i === 0 ? "1" : "0";
                });
                return;
            }
            const TOTAL = maxT + 1;
            const center = ((Math.round(t) % TOTAL) + TOTAL) % TOTAL;
            const MAX_VISIBILITY = 0.45;
            cards.forEach((card, i) => {
                const distRaw = i - t;
                const dist = distRaw < 0 ? distRaw + TOTAL : distRaw;
                const delta = -dist;
                const offset = delta / 3;
                const absOffset = Math.abs(delta) / 3;
                card.style.transform = `rotateY(${offset * 50}deg) scaleY(${1 - absOffset * 0.4}) translateZ(${-absOffset * 300}px) translateX(${offset * -80}px)`.trim();
                card.style.opacity = absOffset >= MAX_VISIBILITY ? "0" : "1";
                card.style.filter = `blur(${absOffset * 5}px)`;
                card.style.zIndex = String(TOTAL - Math.abs(delta));
                card.style.pointerEvents = i === center ? "auto" : "none";
                card.style.display = absOffset >= MAX_VISIBILITY ? "none" : "block";
            });
        }

        function startAutoLoop() {
            if (started) return;
            started = true;
            autoStart = performance.now();
            measure();
            if (maxT <= 0) {
                currentT = 0;
                renderFromT(0);
                return;
            }
            const totalSpanMs = Math.max(1, maxT * stepDurationMs);
            (function autoTick(now) {
                const elapsed = now - autoStart;
                const cycleMs = totalSpanMs * 2;
                const p = (elapsed % cycleMs) / totalSpanMs;
                const t = p <= 1 ? p * maxT : (2 - p) * maxT;
                currentT = t;
                renderFromT(t);
                requestAnimationFrame(autoTick);
            })(performance.now());
        }

        function boot() {
            requestAnimationFrame(() => requestAnimationFrame(startAutoLoop));
        }

        if ("IntersectionObserver" in window) {
            const obs = new IntersectionObserver((entries) => {
                if (entries.some((e) => e.isIntersecting)) {
                    obs.disconnect();
                    boot();
                }
            }, { threshold: 0.2 });
            obs.observe(stackScroll);
        } else {
            if (document.readyState === "complete" || document.readyState === "interactive") boot();
            else window.addEventListener("DOMContentLoaded", boot, { once: true });
        }

        window.addEventListener("resize", () => {
            measure();
            const n = maxT + 1;
            renderFromT(currentT % n);
        });
    })();
</script>

<script>
    (() => {
        const testimonials = <?php echo json_encode($testimonials ?? config('homepage.testimonials', []), 512) ?>;
        const profileImg = <?php echo json_encode(asset($testimonial_profile_image ?? config('homepage.testimonial_profile_image', 'images/testimonials-images/thumb-10.2.webp')), 512) ?>;

        const viewport = document.getElementById("carousel-viewport");
        const track = document.getElementById("carousel-track");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const dotsWrap = document.getElementById("dots");

        if (!viewport || !track || !prevBtn || !nextBtn || !dotsWrap) return;

        let activeIndex = 0;
        let visibleCardFraction = 1;
        const CLONE_SLIDES = 3;
        let clonesBefore = 0;
        let clonesAfter = 0;
        let pos = 0;
        let isTransitioning = false;
        let autoplayId = null;
        let paused = false;
        const AUTOPLAY_MS = 4000;

        function getVisibleCardFraction() {
            return 1;
        }

        function getGapPx() {
            const style = window.getComputedStyle(track);
            const gap = parseFloat(style.gap || style.columnGap || "0");
            return Number.isFinite(gap) ? gap : 24;
        }

        function computeCardWidth() {
            const viewportW = viewport.getBoundingClientRect().width;
            const gapPx = getGapPx();
            const frac = visibleCardFraction;
            const w = frac <= 1 ? viewportW : (viewportW - gapPx) / frac;
            return Math.max(120, w);
        }

        function signedDistance(realIndex, activeIdx, n) {
            const delta = realIndex - activeIdx;
            const wrapped = ((delta % n) + n) % n;
            return wrapped > n / 2 ? wrapped - n : wrapped;
        }

        function buildExtendedSlides() {
            const n = testimonials.length;
            clonesBefore = CLONE_SLIDES;
            clonesAfter = CLONE_SLIDES;
            const before = [];
            for (let k = 0; k < clonesBefore; k++) before.push((n - clonesBefore + k) % n);
            const middle = [];
            for (let i = 0; i < n; i++) middle.push(i);
            const after = [];
            for (let k = 0; k < clonesAfter; k++) after.push(k % n);
            return before.concat(middle, after);
        }

        function renderDots() {
            const n = testimonials.length;
            dotsWrap.innerHTML = "";
            for (let i = 0; i < n; i++) {
                const btn = document.createElement("button");
                btn.type = "button";
                btn.className = "h-2.5 w-2.5 rounded-full transition-all duration-300 ease-in-out";
                btn.style.background = i === activeIndex ? "rgb(147 51 234)" : "rgb(209 213 219)";
                btn.style.transform = i === activeIndex ? "scale(1.2)" : "scale(1)";
                btn.setAttribute("aria-label", `Go to testimonial ${i + 1}`);
                btn.addEventListener("click", () => {
                    activeIndex = i;
                    pos = clonesBefore + activeIndex;
                    updateCarousel();
                });
                dotsWrap.appendChild(btn);
            }
        }

        function applyCardStyles() {
            const n = testimonials.length;
            Array.from(track.children).forEach((slide) => {
                const realIndex = Number(slide.getAttribute("data-real-index"));
                const delta = signedDistance(realIndex, activeIndex, n);
                const abs = Math.abs(delta);
                slide.style.opacity = "1";
                slide.style.transform = "scale(1)";
                slide.style.pointerEvents = abs === 0 ? "auto" : "none";
                slide.style.border = abs === 0 ? "1px solid rgba(147, 51, 234, 0.35)" : "1px solid transparent";
            });
        }

        function updateCarousel(stepOverride, centerSlotOverride) {
            const cardW = computeCardWidth();
            const gapPx = getGapPx();
            const step = stepOverride ?? (cardW + gapPx);
            const centerSlot = centerSlotOverride ?? 1;
            const translateX = -(pos - centerSlot) * step;
            track.style.transform = `translateX(${translateX}px)`;
            applyCardStyles();
            renderDots();
        }

        function renderSlides() {
            track.innerHTML = "";
            const extended = buildExtendedSlides();
            const n = testimonials.length;
            const centerSlot = 1;
            pos = clonesBefore + activeIndex;
            extended.forEach((realIndex) => {
                const t = testimonials[realIndex];
                const slide = document.createElement("div");
                slide.className = "bg-white rounded-xl shadow-md p-6 transition-all duration-500 ease-in-out transform-gpu";
                slide.setAttribute("data-real-index", String(realIndex));
                slide.style.flex = "0 0 auto";
                slide.style.width = `${computeCardWidth()}px`;
                slide.style.border = "1px solid transparent";
                const starsHtml = Array.from({ length: 5 }).map((_, k) => {
                    const filled = t.stars && k < t.stars;
                    return `<svg width="16" height="16" viewBox="0 0 24 24" fill="${filled ? "#f59e0b" : "#fde68a"}" aria-hidden="true" class="shrink-0"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>`;
                }).join("");
                slide.innerHTML = `
                  <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-full overflow-hidden border border-purple-100 bg-purple-50 flex items-center justify-center">
                      <img src="${profileImg}" alt="${t.name}" class="h-full w-full object-cover" />
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-gray-900 truncate">${t.name}</div>
                      <div class="text-sm text-gray-500 mt-0.5 truncate">${t.role}</div>
                      <div class="mt-2 flex items-center gap-1">${starsHtml}</div>
                    </div>
                  </div>
                  <p class="mt-4 text-gray-700 text-sm leading-6">${t.text}</p>`;
                track.appendChild(slide);
            });
            updateCarousel(computeCardWidth() + getGapPx(), centerSlot);
        }

        function next(manual = false) {
            if (isTransitioning) return;
            isTransitioning = true;
            activeIndex = (activeIndex + 1) % testimonials.length;
            pos += 1;
            track.style.transition = "transform 500ms ease-in-out";
            updateCarousel();
            if (!manual) restartAutoplay();
            window.setTimeout(() => { isTransitioning = false; }, 520);
        }

        function prev(manual = false) {
            if (isTransitioning) return;
            isTransitioning = true;
            activeIndex = (activeIndex - 1 + testimonials.length) % testimonials.length;
            pos -= 1;
            track.style.transition = "transform 500ms ease-in-out";
            updateCarousel();
            if (!manual) restartAutoplay();
            window.setTimeout(() => { isTransitioning = false; }, 520);
        }

        track.addEventListener("transitionend", () => {
            const n = testimonials.length;
            if (pos >= clonesBefore + n) {
                pos = clonesBefore;
                activeIndex = 0;
                track.style.transition = "none";
                updateCarousel();
                void track.offsetHeight;
                track.style.transition = "transform 500ms ease-in-out";
            } else if (pos < clonesBefore) {
                pos = clonesBefore + (n - 1);
                activeIndex = n - 1;
                track.style.transition = "none";
                updateCarousel();
                void track.offsetHeight;
                track.style.transition = "transform 500ms ease-in-out";
            }
        });

        function stopAutoplay() {
            if (autoplayId) clearInterval(autoplayId);
            autoplayId = null;
        }

        function restartAutoplay() {
            stopAutoplay();
            if (paused) return;
            autoplayId = setInterval(() => next(false), AUTOPLAY_MS);
        }

        function startAutoplay() {
            stopAutoplay();
            if (paused) return;
            autoplayId = setInterval(() => next(false), AUTOPLAY_MS);
        }

        viewport.addEventListener("mouseenter", () => { paused = true; stopAutoplay(); });
        viewport.addEventListener("mouseleave", () => { paused = false; startAutoplay(); });

        prevBtn.addEventListener("click", () => prev(true));
        nextBtn.addEventListener("click", () => next(true));

        function init() {
            visibleCardFraction = getVisibleCardFraction();
            renderSlides();
            startAutoplay();
        }

        let resizeTimer = null;
        window.addEventListener("resize", () => {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(() => {
                visibleCardFraction = getVisibleCardFraction();
                renderSlides();
                startAutoplay();
            }, 150);
        });

        init();
    })();
</script>

<?php
    $__donationPrefill = auth()->check()
        ? array_filter([
            'name' => auth()->user()->name ?? '',
            'email' => auth()->user()->email ?? '',
            'contact' => auth()->user()->mobile ?? '',
        ])
        : [];
    $__donationConfig = ['isAuthenticated' => auth()->check()];
?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    (() => {
        const viewport = document.querySelector("[data-donate-viewport]");
        const track = document.querySelector("[data-donate-track]");
        const prevBtn = document.querySelector("[data-donate-prev]");
        const nextBtn = document.querySelector("[data-donate-next]");

        const GOAL = <?php echo e((int) $donate['goal']); ?>;
        const DEFAULT_AMT = <?php echo e((int) $donate['default_amount']); ?>;
        const DONATION_MAX_INR = 5_000_000;

        const DONATION_ORDER_URL = <?php echo json_encode(route('donations.payment.order'), 15, 512) ?>;
        const DONATION_VERIFY_URL = <?php echo json_encode(route('donations.payment.verify'), 15, 512) ?>;
        const donationPrefill = <?php echo json_encode($__donationPrefill, 15, 512) ?>;
        const DONATION_CONFIG = <?php echo json_encode($__donationConfig, 15, 512) ?>;
        window.__DONATION_CONFIG = DONATION_CONFIG;

        function donateCsrfToken() {
            const m = document.querySelector('meta[name="csrf-token"]');
            return m ? m.getAttribute("content") : "";
        }

        function formatDonateInr(n) {
            const v = Number(n || 0);
            return "₹" + v.toLocaleString("en-IN", { maximumFractionDigits: 2 });
        }

        const donateSuccessModal = document.getElementById("donate-payment-success-modal");
        const donateErrorModal = document.getElementById("donate-payment-error-modal");

        function openDonatePaymentSuccess(payload) {
            if (!donateSuccessModal) return;
            const msgEl = document.getElementById("donate-payment-success-message");
            const amtEl = document.getElementById("donate-payment-success-amount");
            const payEl = document.getElementById("donate-payment-success-payment-id");
            if (msgEl) msgEl.textContent = payload?.message || "Thank you for supporting GNAT Association!";
            if (amtEl) amtEl.textContent = formatDonateInr(payload?.amount);
            if (payEl) {
                const pid = payload?.razorpay_payment_id;
                if (pid) {
                    payEl.textContent = "Payment ID: " + pid;
                    payEl.classList.remove("hidden");
                } else {
                    payEl.textContent = "";
                    payEl.classList.add("hidden");
                }
            }
            donateSuccessModal.classList.remove("hidden");
            donateSuccessModal.setAttribute("aria-hidden", "false");
            syncDonateBodyScrollLock();
        }

        function closeDonatePaymentSuccess() {
            if (!donateSuccessModal) return;
            donateSuccessModal.classList.add("hidden");
            donateSuccessModal.setAttribute("aria-hidden", "true");
            syncDonateBodyScrollLock();
        }

        function openDonatePaymentError(title, message, detail) {
            if (!donateErrorModal) return;
            const t = document.getElementById("donate-payment-error-title");
            const m = document.getElementById("donate-payment-error-message");
            const d = document.getElementById("donate-payment-error-detail");
            if (t) t.textContent = title || "Something went wrong";
            if (m) m.textContent = message || "";
            if (d) {
                d.textContent = detail || "";
                d.classList.toggle("hidden", !detail);
            }
            donateErrorModal.classList.remove("hidden");
            donateErrorModal.setAttribute("aria-hidden", "false");
            syncDonateBodyScrollLock();
        }

        function closeDonatePaymentError() {
            if (!donateErrorModal) return;
            donateErrorModal.classList.add("hidden");
            donateErrorModal.setAttribute("aria-hidden", "true");
            syncDonateBodyScrollLock();
        }

        donateSuccessModal?.querySelectorAll("[data-close-donate-payment-success]").forEach((el) => {
            el.addEventListener("click", () => closeDonatePaymentSuccess());
        });
        donateErrorModal?.querySelectorAll("[data-close-donate-payment-error]").forEach((el) => {
            el.addEventListener("click", () => closeDonatePaymentError());
        });

        const donateModal = document.getElementById("donate-modal");

        function syncDonateBodyScrollLock() {
            const anyOpen =
                (donateModal && !donateModal.classList.contains("hidden")) ||
                (donateSuccessModal && !donateSuccessModal.classList.contains("hidden")) ||
                (donateErrorModal && !donateErrorModal.classList.contains("hidden"));
            document.body.style.overflow = anyOpen ? "hidden" : "";
        }

        function closeDonateModal() {
            if (!donateModal) return;
            donateModal.classList.add("hidden");
            donateModal.setAttribute("aria-hidden", "true");
            syncDonateBodyScrollLock();
        }

        function openDonateModal(opts) {
            if (!donateModal) return;
            const options = opts || {};
            const donationIdRaw = options.donationId;
            const hid = document.getElementById("donate-context-donation-id");
            if (hid) {
                if (donationIdRaw != null && donationIdRaw !== "") {
                    hid.value = String(donationIdRaw);
                } else {
                    hid.value = "";
                }
            }

            const details = document.getElementById("donate-step-details");
            const amountsWrap = document.getElementById("donate-step-amounts-wrapper");
            const auth = Boolean(DONATION_CONFIG?.isAuthenticated);
            if (auth) {
                details?.classList.add("hidden");
                amountsWrap?.classList.remove("hidden");
            } else {
                details?.classList.remove("hidden");
                amountsWrap?.classList.add("hidden");
            }

            const homeRoot = document.getElementById("home-donate-amounts");
            const modalRoot = document.getElementById("modal-donate-amounts");
            const homeInput = homeRoot?.querySelector("[data-donate-input]");
            const modalInput = modalRoot?.querySelector("[data-donate-input]");
            if (homeInput && modalInput) {
                modalInput.value = homeInput.value;
                modalInput.dispatchEvent(new Event("input", { bubbles: true }));
            } else if (modalInput) {
                modalInput.value = String(DEFAULT_AMT);
                modalInput.dispatchEvent(new Event("input", { bubbles: true }));
            }
            donateModal.classList.remove("hidden");
            donateModal.setAttribute("aria-hidden", "false");
            syncDonateBodyScrollLock();
            if (auth) {
                modalInput?.focus({ preventScroll: true });
            } else {
                document.querySelector("[data-donate-detail=\"name\"]")?.focus({ preventScroll: true });
            }
        }

        function buildDonationOrderPayload(amountInr) {
            const body = { amount: amountInr };
            const hid = document.getElementById("donate-context-donation-id");
            const did = hid?.value?.trim();
            if (did) {
                const n = parseInt(did, 10);
                if (Number.isFinite(n) && n > 0) body.donation_id = n;
            }
            if (!DONATION_CONFIG?.isAuthenticated) {
                body.donor_name = document.querySelector('[data-donate-detail="name"]')?.value?.trim() || "";
                body.donor_email = document.querySelector('[data-donate-detail="email"]')?.value?.trim() || "";
                body.donor_mobile = document.querySelector('[data-donate-detail="mobile"]')?.value?.trim() || "";
                body.wants_membership = Boolean(document.querySelector("[data-donate-wants-member]")?.checked);
            }
            return body;
        }

        function razorpayPrefillForPayload(payload) {
            if (DONATION_CONFIG?.isAuthenticated) return donationPrefill;
            return {
                name: payload.donor_name || "",
                email: payload.donor_email || "",
                contact: payload.donor_mobile || "",
            };
        }

        async function startDonationCheckout(amountInr) {
            if (typeof window.Razorpay === "undefined") {
                openDonatePaymentError(
                    "Payment unavailable",
                    "The payment form could not load. Refresh the page and try again, or contact us.",
                    ""
                );
                return;
            }

            const orderPayload = buildDonationOrderPayload(amountInr);
            if (!DONATION_CONFIG?.isAuthenticated) {
                if (!orderPayload.donor_name) {
                    openDonatePaymentError("Details required", "Please enter your full name.", "");
                    return;
                }
                if (!orderPayload.donor_email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(orderPayload.donor_email)) {
                    openDonatePaymentError("Details required", "Please enter a valid email address.", "");
                    return;
                }
                const mob = String(orderPayload.donor_mobile).replace(/\D/g, "");
                if (mob.length < 10) {
                    openDonatePaymentError("Details required", "Please enter a valid mobile number (at least 10 digits).", "");
                    return;
                }
                orderPayload.donor_mobile = mob;
            }

            let razorpayOutcome = "idle";

            try {
                const res = await fetch(DONATION_ORDER_URL, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": donateCsrfToken(),
                        Accept: "application/json",
                    },
                    body: JSON.stringify(orderPayload),
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    openDonatePaymentError(
                        "Payment could not start",
                        data?.message || "Please try again in a moment.",
                        ""
                    );
                    return;
                }

                const options = {
                    key: data.key,
                    amount: Math.round(Number(data.amount || 0) * 100),
                    currency: "INR",
                    name: "GNAT Association",
                    description: "Charitable donation",
                    order_id: data.order_id,
                    prefill: razorpayPrefillForPayload(orderPayload),
                    handler: async function (response) {
                        razorpayOutcome = "processing";
                        try {
                            const verifyRes = await fetch(DONATION_VERIFY_URL, {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": donateCsrfToken(),
                                    Accept: "application/json",
                                },
                                body: JSON.stringify({
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_signature: response.razorpay_signature,
                                }),
                            });
                            const payload = await verifyRes.json().catch(() => ({}));
                            if (!verifyRes.ok || !payload?.success) {
                                razorpayOutcome = "failed";
                                openDonatePaymentError(
                                    "Verification failed",
                                    payload?.message ||
                                        "We could not confirm this payment. If money was debited, contact us with your payment ID.",
                                    ["Order: " + (response.razorpay_order_id || "—"), "Payment: " + (response.razorpay_payment_id || "—")]
                                        .join("\n")
                                );
                                return;
                            }
                            razorpayOutcome = "success";
                            closeDonateModal();
                            openDonatePaymentSuccess(payload);
                        } catch (err) {
                            console.error(err);
                            razorpayOutcome = "failed";
                            openDonatePaymentError(
                                "Something went wrong",
                                "Payment may have completed but we could not verify it. Please contact support.",
                                String(err?.message || err)
                            );
                        }
                    },
                    theme: { color: "#351c42" },
                    modal: {
                        ondismiss: function () {
                            if (razorpayOutcome === "success" || razorpayOutcome === "processing" || razorpayOutcome === "failed") {
                                return;
                            }
                            openDonatePaymentError(
                                "Payment not completed",
                                "The payment window was closed before finishing.",
                                "You can tap Donate again when you are ready."
                            );
                        },
                    },
                };

                const rzp = new window.Razorpay(options);
                rzp.on("payment.failed", function (resp) {
                    razorpayOutcome = "failed";
                    const err = resp?.error || {};
                    const desc = err.description || err.reason || "The payment was declined or failed.";
                    const code = err.code ? "Code: " + err.code : "";
                    const step = err.step ? "Step: " + err.step : "";
                    openDonatePaymentError("Payment failed", desc, [code, step].filter(Boolean).join("\n"));
                });
                rzp.open();
            } catch (e) {
                console.error(e);
                openDonatePaymentError(
                    "Unable to open payment",
                    "Please check your connection and try again.",
                    String(e?.message || e)
                );
            }
        }

        function wireDonateAmounts(root) {
            if (!root) return;
            const input = root.querySelector("[data-donate-input]");
            const bar = root.querySelector("[data-donate-bar]");
            const amtBtns = root.querySelectorAll("[data-donate-amt]");
            const customBtn = root.querySelector("[data-donate-custom]");
            const submitBtn = root.querySelector("[data-donate-submit]");
            if (!input || !bar) return;

            function syncBarFromInput() {
                const v = Math.min(GOAL, Math.max(0, Number(input.value) || 0));
                const pct = Math.min(100, Math.round((v / GOAL) * 100));
                bar.style.width = `${Math.max(8, pct)}%`;
            }

            function setActiveAmt(amt) {
                amtBtns.forEach((b) => {
                    const n = b.getAttribute("data-donate-amt");
                    b.classList.toggle("is-selected", amt !== "" && n === String(amt));
                });
            }

            amtBtns.forEach((btn) => {
                btn.addEventListener("click", () => {
                    const v = btn.getAttribute("data-donate-amt");
                    if (v) {
                        input.value = v;
                        syncBarFromInput();
                        setActiveAmt(v);
                    }
                });
            });

            customBtn?.addEventListener("click", () => {
                input.focus();
                input.select();
            });

            input.addEventListener("input", () => {
                syncBarFromInput();
                setActiveAmt("");
            });

            submitBtn?.addEventListener("click", async () => {
                const n = Number(input.value) || 0;
                if (n < 1) {
                    openDonatePaymentError("Invalid amount", "Please enter at least ₹1.", "");
                    return;
                }
                if (n > DONATION_MAX_INR) {
                    openDonatePaymentError(
                        "Amount too large",
                        "For very large donations, please contact us directly.",
                        ""
                    );
                    return;
                }
                if (!DONATION_CONFIG?.isAuthenticated && root.id === "home-donate-amounts") {
                    openDonateModal({ donationId: null });
                    const modalInput = document.querySelector("#modal-donate-amounts [data-donate-input]");
                    if (modalInput) {
                        modalInput.value = String(n);
                        modalInput.dispatchEvent(new Event("input", { bubbles: true }));
                    }
                    return;
                }
                submitBtn.disabled = true;
                try {
                    await startDonationCheckout(n);
                } finally {
                    submitBtn.disabled = false;
                }
            });

            setActiveAmt(String(input.value || DEFAULT_AMT));
            syncBarFromInput();
        }

        wireDonateAmounts(document.getElementById("home-donate-amounts"));
        wireDonateAmounts(document.getElementById("modal-donate-amounts"));

        document.querySelector("[data-donate-continue-details]")?.addEventListener("click", () => {
            const name = document.querySelector('[data-donate-detail="name"]')?.value?.trim();
            const email = document.querySelector('[data-donate-detail="email"]')?.value?.trim();
            const mobile = String(document.querySelector('[data-donate-detail="mobile"]')?.value || "").replace(/\D/g, "");
            if (!name) {
                openDonatePaymentError("Details required", "Please enter your full name.", "");
                return;
            }
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                openDonatePaymentError("Details required", "Please enter a valid email address.", "");
                return;
            }
            if (mobile.length < 10) {
                openDonatePaymentError("Details required", "Please enter a valid mobile number (at least 10 digits).", "");
                return;
            }
            document.getElementById("donate-step-details")?.classList.add("hidden");
            document.getElementById("donate-step-amounts-wrapper")?.classList.remove("hidden");
            document.querySelector("#modal-donate-amounts [data-donate-input]")?.focus({ preventScroll: true });
        });

        document.querySelector("[data-donate-back-details]")?.addEventListener("click", () => {
            document.getElementById("donate-step-details")?.classList.remove("hidden");
            document.getElementById("donate-step-amounts-wrapper")?.classList.add("hidden");
            document.querySelector('[data-donate-detail="name"]')?.focus({ preventScroll: true });
        });

        document.querySelectorAll("[data-open-donate-modal]").forEach((el) => {
            el.addEventListener("click", (e) => {
                e.preventDefault();
                document.querySelector("[data-drawer-close]")?.click();
                const raw = el.getAttribute("data-donation-id");
                const donationId = raw && /^\d+$/.test(String(raw).trim()) ? parseInt(String(raw).trim(), 10) : null;
                openDonateModal({ donationId });
            });
        });
        donateModal?.querySelectorAll("[data-close-donate-modal]").forEach((el) => {
            el.addEventListener("click", () => closeDonateModal());
        });

        document.addEventListener("keydown", (e) => {
            if (e.key !== "Escape") return;
            if (donateSuccessModal && !donateSuccessModal.classList.contains("hidden")) {
                closeDonatePaymentSuccess();
                return;
            }
            if (donateErrorModal && !donateErrorModal.classList.contains("hidden")) {
                closeDonatePaymentError();
                return;
            }
            if (donateModal && !donateModal.classList.contains("hidden")) closeDonateModal();
        });

        if (viewport && track) {
            const originals = Array.from(track.querySelectorAll(".donation-slide"));
            const slideCount = originals.length;
            if (slideCount > 0) {
                originals.forEach((node) => {
                    const clone = node.cloneNode(true);
                    clone.setAttribute("aria-hidden", "true");
                    track.appendChild(clone);
                });
                let pos = 0;
                const gap = 16;
                function slideWidth() {
                    const w = viewport.getBoundingClientRect().width;
                    const twoUp = window.matchMedia("(min-width: 1024px)").matches;
                    return twoUp ? Math.max(280, (w - gap) / 2) : w;
                }
                function applySlideSizes() {
                    const sw = slideWidth();
                    track.querySelectorAll(".donation-slide").forEach((el) => {
                        el.style.flex = `0 0 ${sw}px`;
                        el.style.width = `${sw}px`;
                    });
                }
                function getStep() {
                    const first = track.children[0];
                    if (!first) return 0;
                    const second = track.children[1];
                    if (!second) return first.getBoundingClientRect().width + gap;
                    return second.offsetLeft - first.offsetLeft;
                }
                function getSetWidth() {
                    const firstClone = track.children[slideCount];
                    return firstClone ? firstClone.offsetLeft : 0;
                }
                function setTransformPx(x, instant) {
                    if (instant) track.style.transition = "none";
                    else track.style.transition = "";
                    track.style.transform = `translateX(${-x}px)`;
                    if (instant) {
                        void track.offsetWidth;
                        track.style.transition = "";
                    }
                }
                function wrapPosInstant() {
                    const w = getSetWidth();
                    if (w <= 0) return;
                    let wrapped = false;
                    while (pos >= w) { pos -= w; wrapped = true; }
                    while (pos < 0) { pos += w; wrapped = true; }
                    if (wrapped) setTransformPx(pos, true);
                }
                function render() {
                    applySlideSizes();
                    wrapPosInstant();
                    setTransformPx(pos, false);
                }
                function go(delta) {
                    const step = getStep();
                    if (step <= 0) return;
                    pos += delta * step;
                    const w = getSetWidth();
                    if (w > 0) {
                        let wrapped = false;
                        while (pos >= w) { pos -= w; wrapped = true; }
                        while (pos < 0) { pos += w; wrapped = true; }
                        if (wrapped) setTransformPx(pos, true);
                    }
                    setTransformPx(pos, false);
                }
                prevBtn?.addEventListener("click", () => go(-1));
                nextBtn?.addEventListener("click", () => go(1));
                let resizeT;
                window.addEventListener("resize", () => {
                    clearTimeout(resizeT);
                    resizeT = setTimeout(() => { pos = 0; render(); }, 120);
                });
                render();
            }
        }
    })();
</script>

<script>
    (() => {
        const root = document.querySelector("[data-gallery-root]");
        if (!root) return;
        const buttons = root.querySelectorAll("[data-gallery-filter]");
        const items = root.querySelectorAll("[data-gallery-item]");
        function applyFilter(btn) {
            const f = btn.getAttribute("data-gallery-filter") || "all";
            buttons.forEach((b) => b.setAttribute("aria-pressed", b === btn ? "true" : "false"));
            items.forEach((el) => {
                const cat = el.getAttribute("data-cat") || "";
                el.classList.toggle("hidden", f !== "all" && cat !== f);
            });
        }
        buttons.forEach((btn) => btn.addEventListener("click", () => applyFilter(btn)));
        const allBtn = root.querySelector('[data-gallery-filter="all"]');
        if (allBtn) applyFilter(allBtn);
    })();
</script>

<script>
    (() => {
        const STORAGE_KEY = "gnat-home-welcome-dismissed";
        const el = document.getElementById("welcome-bottom-card");
        const dismissBtn = el?.querySelector("[data-welcome-dismiss]");
        if (!el || !dismissBtn) return;

        const hide = () => {
            el.classList.remove("is-visible");
            el.classList.add("is-dismissed");
            el.setAttribute("aria-hidden", "true");
            try { sessionStorage.setItem(STORAGE_KEY, "1"); } catch (_) {}
            window.setTimeout(() => { el.hidden = true; }, 500);
        };

        dismissBtn.addEventListener("click", hide);

        let dismissed = false;
        try { dismissed = sessionStorage.getItem(STORAGE_KEY) === "1"; } catch (_) {}
        if (dismissed) return;

        const show = () => {
            el.hidden = false;
            el.setAttribute("aria-hidden", "false");
            requestAnimationFrame(() => el.classList.add("is-visible"));
        };

        const delayMs = window.matchMedia("(prefers-reduced-motion: reduce)").matches ? 200 : 1200;
        window.setTimeout(show, delayMs);
    })();
</script>

<script>
    (() => {
        const fy = document.getElementById("footer-year");
        if (fy) fy.textContent = String(new Date().getFullYear());
        document.querySelector("[data-footer-newsletter]")?.addEventListener("submit", (e) => {
            e.preventDefault();
            alert("Thank you for subscribing to GNAT Association!");
        });
    })();
</script>

<script>
    (() => {
        const viewport = document.querySelector("[data-blog-viewport]");
        const track = document.querySelector("[data-blog-track]");
        const progress = document.querySelector("[data-blog-progress]");
        if (!viewport || !track || !progress) return;

        const originals = Array.from(track.children);
        const slideCount = originals.length;
        if (slideCount === 0) return;

        originals.forEach((node) => {
            const clone = node.cloneNode(true);
            clone.setAttribute("aria-hidden", "true");
            track.appendChild(clone);
        });

        let pos = 0;
        let timer = null;
        let isDragging = false;
        let dragStartX = 0;
        let dragStartPos = 0;

        function getStep() {
            const items = track.children;
            if (items.length < 2) return items[0].offsetWidth;
            const gap = items[1].offsetLeft - items[0].offsetLeft - items[0].offsetWidth;
            return items[0].offsetWidth + Math.max(0, gap);
        }

        function getSetWidth() {
            const firstClone = track.children[slideCount];
            return firstClone ? firstClone.offsetLeft : 0;
        }

        function setTransformPx(x, instant) {
            if (instant) track.style.transition = "none";
            else track.style.transition = "";
            track.style.transform = `translateX(${-x}px)`;
            if (instant) {
                void track.offsetWidth;
                track.style.transition = "";
            }
        }

        function wrapPosInstant() {
            const w = getSetWidth();
            if (w <= 0) return;
            let wrapped = false;
            while (pos >= w) { pos -= w; wrapped = true; }
            while (pos < 0) { pos += w; wrapped = true; }
            if (wrapped) setTransformPx(pos, true);
        }

        function updateProgress() {
            const w = getSetWidth();
            if (w <= 0) return;
            const p = ((pos % w) + w) % w;
            const pct = (p / w) * 100;
            progress.style.width = `${Math.min(100, Math.max(6, pct + 6))}%`;
        }

        function render() {
            wrapPosInstant();
            setTransformPx(pos, false);
            updateProgress();
        }

        function next() {
            pos += getStep();
            const w = getSetWidth();
            if (w > 0) {
                let wrapped = false;
                while (pos >= w) { pos -= w; wrapped = true; }
                if (wrapped) setTransformPx(pos, true);
                setTransformPx(pos, false);
            }
            updateProgress();
        }

        function start() {
            stop();
            timer = setInterval(next, 2800);
        }

        function stop() {
            if (timer) clearInterval(timer);
            timer = null;
        }

        viewport.addEventListener("mouseenter", stop);
        viewport.addEventListener("mouseleave", () => { if (!isDragging) start(); });
        window.addEventListener("resize", () => { pos = 0; render(); });

        viewport.addEventListener("pointerdown", (e) => {
            isDragging = true;
            dragStartX = e.clientX;
            dragStartPos = pos;
            stop();
            viewport.setPointerCapture(e.pointerId);
        });
        viewport.addEventListener("pointermove", (e) => {
            if (!isDragging) return;
            const delta = e.clientX - dragStartX;
            pos = dragStartPos - delta;
            wrapPosInstant();
            setTransformPx(pos, true);
            updateProgress();
        });
        function endDrag() {
            if (!isDragging) return;
            isDragging = false;
            const step = getStep();
            if (step > 0) pos = Math.round(pos / step) * step;
            wrapPosInstant();
            render();
            start();
        }
        viewport.addEventListener("pointerup", endDrag);
        viewport.addEventListener("pointercancel", endDrag);

        render();
        start();
    })();
</script>

<script>
    (() => {
        const homeRoot = document.getElementById("home-events-accordion");
        if (!homeRoot) return;

        const items = Array.from(homeRoot.querySelectorAll("[data-events-accordion-item]"));
        if (items.length === 0) return;

        let openItem = null;

        function setIconsForState(item, isOpen) {
            const plusIcons = item.querySelectorAll("[data-events-plus]");
            const minusIcons = item.querySelectorAll("[data-events-minus]");
            plusIcons.forEach((el) => el.classList.toggle("hidden", isOpen));
            minusIcons.forEach((el) => el.classList.toggle("hidden", !isOpen));
        }

        function closeItem(item) {
            item.removeAttribute("data-events-open");
            const trigger = item.querySelector("[data-events-accordion-trigger]");
            const panel = item.querySelector("[data-events-accordion-panel]");
            if (trigger) trigger.setAttribute("aria-expanded", "false");
            if (panel) panel.classList.add("hidden");
            setIconsForState(item, false);
        }

        function openOnly(itemToOpen) {
            if (openItem && openItem !== itemToOpen) closeItem(openItem);
            const trigger = itemToOpen.querySelector("[data-events-accordion-trigger]");
            const panel = itemToOpen.querySelector("[data-events-accordion-panel]");
            if (trigger) trigger.setAttribute("aria-expanded", "true");
            if (panel) panel.classList.remove("hidden");
            setIconsForState(itemToOpen, true);
            itemToOpen.setAttribute("data-events-open", "true");
            openItem = itemToOpen;
        }

        items.forEach((item) => {
            const trigger = item.querySelector("[data-events-accordion-trigger]");
            if (!trigger) return;
            closeItem(item);
            trigger.addEventListener("click", (e) => {
                e.preventDefault();
                if (openItem === item) {
                    return;
                }
                openOnly(item);
            });
        });

        openOnly(items[0]);
    })();
</script>

<script>
    (() => {
        const modal = document.getElementById("read-more-modal");
        if (!modal) return;

        const titleEl = document.getElementById("read-more-modal-title");
        const metaEl = document.getElementById("read-more-modal-meta");
        const bodyEl = document.getElementById("read-more-modal-body");
        const backdrop = modal.querySelector("[data-read-more-backdrop]");
        const closeEls = modal.querySelectorAll("[data-close-read-more]");

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

        function openFromTrigger(btn) {
            if (!btn) return;
            lastActive = btn;
            const title = btn.getAttribute("data-read-more-title") || "Details";
            const content = btn.getAttribute("data-read-more-content") || "";
            const metaRaw = btn.getAttribute("data-read-more-meta") || "";
            let meta = [];
            if (metaRaw) {
                try {
                    const parsed = JSON.parse(metaRaw);
                    if (Array.isArray(parsed)) meta = parsed;
                } catch (_) {
                    meta = [];
                }
            }
            if (titleEl) titleEl.textContent = title;
            if (metaEl) {
                const rows = meta
                    .filter((item) => item && item.label && item.value)
                    .map((item) => {
                        const row = document.createElement("div");
                        row.className = "flex items-start justify-between gap-3 border-b border-[#351c42]/10 pb-2 last:border-0 last:pb-0";

                        const dt = document.createElement("dt");
                        dt.className = "shrink-0 font-bold uppercase tracking-wide text-[#965995]";
                        dt.textContent = String(item.label);

                        const dd = document.createElement("dd");
                        dd.className = "text-right font-semibold text-[#351c42]";
                        dd.textContent = String(item.value);

                        row.appendChild(dt);
                        row.appendChild(dd);
                        return row;
                    });
                metaEl.innerHTML = "";
                if (rows.length > 0) {
                    rows.forEach((row) => metaEl.appendChild(row));
                    metaEl.classList.remove("hidden");
                } else {
                    metaEl.classList.add("hidden");
                }
            }
            if (bodyEl) bodyEl.textContent = content;
            setOpen(true);
            (closeEls[0] || modal).focus?.({ preventScroll: true });
        }

        function close() { setOpen(false); }

        document.addEventListener("click", (e) => {
            const btn = e.target.closest("[data-read-more]");
            if (btn) {
                e.preventDefault();
                openFromTrigger(btn);
                return;
            }
            if (!modal.classList.contains("hidden")) {
                if (e.target.closest("[data-close-read-more]")) close();
                else if (backdrop && e.target === backdrop) close();
            }
        });

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && !modal.classList.contains("hidden")) close();
        });
    })();
</script>
<?php /**PATH C:\Users\acer\OneDrive\Desktop\projects\event-management\resources\views\home\partials\scripts.blade.php ENDPATH**/ ?>