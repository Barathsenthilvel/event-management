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
        const vertCurrent = document.getElementById("bannerVertCurrent");
        const vertNext = document.getElementById("bannerVertNext");
        if (!viewport || !track || !prevBtn || !nextBtn || !dotsWrap) return;

        const slides = () => Array.from(track.querySelectorAll(".banner-slide"));
        let index = 0;
        let autoplayId = null;
        const AUTOPLAY_MS = 5500;
        let touchStartX = null;

        function gapPx() {
            const s = window.getComputedStyle(track);
            const g = parseFloat(s.gap || s.columnGap || "0");
            return Number.isFinite(g) ? g : 16;
        }

        function peekFraction() {
            const w = window.innerWidth;
            if (w >= 1024) return 1.18;
            if (w >= 640) return 1.2;
            return 1.12;
        }

        function pad2(i) { return String(i).padStart(2, "0"); }

        function updateVerticalNav() {
            if (!vertCurrent || !vertNext) return;
            const n = slides().length;
            if (n === 0) return;
            vertCurrent.textContent = pad2(index + 1);
            vertNext.textContent = pad2(((index + 1) % n) + 1);
        }

        function layout() {
            const n = slides().length;
            if (n === 0) return;
            const vw = viewport.getBoundingClientRect().width;
            const gap = gapPx();
            const frac = peekFraction();
            const slideW = Math.max(200, (vw - gap) / frac);
            slides().forEach((el) => { el.style.width = `${slideW}px`; });
            const step = slideW + gap;
            track.style.transform = `translateX(${-index * step}px)`;
            dotsWrap.querySelectorAll("[data-banner-dot]").forEach((btn, i) => {
                const on = i === index;
                btn.style.background = on ? "rgb(147 51 234)" : "rgb(209 213 219)";
                btn.style.transform = on ? "scale(1.2)" : "scale(1)";
            });
            updateVerticalNav();
        }

        function renderDots() {
            const n = slides().length;
            dotsWrap.innerHTML = "";
            for (let i = 0; i < n; i++) {
                const btn = document.createElement("button");
                btn.type = "button";
                btn.setAttribute("data-banner-dot", "");
                btn.className = "h-2.5 w-2.5 rounded-full transition-all duration-300 ease-in-out";
                btn.style.background = i === index ? "rgb(147 51 234)" : "rgb(209 213 219)";
                btn.style.transform = i === index ? "scale(1.2)" : "scale(1)";
                btn.setAttribute("aria-label", `Show banner ${i + 1} of ${n}`);
                btn.addEventListener("click", () => {
                    index = i;
                    track.style.transition = "transform 500ms ease-out";
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
            track.style.transition = "transform 500ms ease-out";
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

        const navEl = prevBtn.closest(".banner-vertical-nav");
        viewport.addEventListener("mouseenter", stopAutoplay);
        viewport.addEventListener("mouseleave", restartAutoplay);
        navEl?.addEventListener("mouseenter", stopAutoplay);
        navEl?.addEventListener("mouseleave", restartAutoplay);

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
        const testimonials = @json($testimonials);
        const profileImg = @json(asset($testimonial_profile_image));

        const viewport = document.getElementById("carousel-viewport");
        const track = document.getElementById("carousel-track");
        const prevBtn = document.getElementById("prevBtn");
        const nextBtn = document.getElementById("nextBtn");
        const dotsWrap = document.getElementById("dots");

        if (!viewport || !track || !prevBtn || !nextBtn || !dotsWrap) return;

        let activeIndex = 0;
        let visibleCardFraction = 1.5;
        const CLONE_SLIDES = 3;
        let clonesBefore = 0;
        let clonesAfter = 0;
        let pos = 0;
        let isTransitioning = false;
        let autoplayId = null;
        let paused = false;
        const AUTOPLAY_MS = 4000;

        function getVisibleCardFraction() {
            return window.innerWidth >= 768 ? 1.5 : 1;
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

<script>
    (() => {
        const viewport = document.querySelector("[data-donate-viewport]");
        const track = document.querySelector("[data-donate-track]");
        const prevBtn = document.querySelector("[data-donate-prev]");
        const nextBtn = document.querySelector("[data-donate-next]");
        const input = document.querySelector("[data-donate-input]");
        const bar = document.querySelector("[data-donate-bar]");
        const amtBtns = document.querySelectorAll("[data-donate-amt]");
        const customBtn = document.querySelector("[data-donate-custom]");
        const submitBtn = document.querySelector("[data-donate-submit]");

        const GOAL = {{ (int) $donate['goal'] }};

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

        function syncBarFromInput() {
            if (!input || !bar) return;
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
                if (input && v) {
                    input.value = v;
                    syncBarFromInput();
                    setActiveAmt(v);
                }
            });
        });

        customBtn?.addEventListener("click", () => {
            input?.focus();
            input?.select();
        });

        input?.addEventListener("input", () => {
            syncBarFromInput();
            setActiveAmt("");
        });

        submitBtn?.addEventListener("click", () => {
            const n = Number(input?.value) || 0;
            if (n < 1) {
                alert("Please enter a valid amount.");
                return;
            }
            alert(`Thank you for supporting GNAT Donation! $${n} — connect this button to your payment flow.`);
        });

        setActiveAmt(String({{ (int) $donate['default_amount'] }}));
        syncBarFromInput();
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
            alert("Thank you for subscribing to GNAT Donation!");
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
        const items = Array.from(document.querySelectorAll("[data-events-accordion-item]"));
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
                    closeItem(item);
                    openItem = null;
                    return;
                }
                openOnly(item);
            });
            item.querySelectorAll("[data-events-panel-icon]").forEach((icon) => {
                icon.style.cursor = "pointer";
                icon.addEventListener("click", (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (openItem === item) {
                        closeItem(item);
                        openItem = null;
                        return;
                    }
                    openOnly(item);
                });
            });
        });

        openOnly(items[0]);
    })();
</script>
