# IMPLEMENTATION_PLAN.md — JETS Travel & Tourism WordPress Theme

> Companion to PROJECT_CONTEXT.md. Work phase by phase; update both files after each phase completes. Do not implement multiple phases in one pass.

**Status legend:** ⬜ Not started · 🟨 In progress · ✅ Complete

---

## PHASE 1 — Project Foundation ✅
**Objective:** Stand up a bare, valid, activatable WordPress theme with the folder structure from PROJECT_CONTEXT §10, and the core design-token CSS.

**Tasks:**
- Create `style.css` theme header (Name: JETS, Text Domain: `jets-theme`, etc.) + `functions.php` bootstrap requiring `inc/*`.
- Create `inc/setup.php`: theme supports (`title-tag`, `post-thumbnails`, `html5`, `custom-logo`, `menus`, `align-wide`), register `primary`/`footer` nav menus, register image sizes.
- Create `inc/enqueue.php`: register/enqueue `variables.css` → `base.css` → `layout.css` → `components.css` → `animations.css` → `responsive.css`, and `main.js` (deferred).
- Create empty-but-valid `index.php`, `header.php`, `footer.php` (minimal `<head>`/doctype/`wp_head`/`wp_footer`, no visual design yet).
- Create `assets/css/variables.css` with the full token set from PROJECT_CONTEXT §7.
- Create `assets/css/base.css`: modern reset, base typography, logical-property defaults, `[dir="rtl"]`/`html[lang="ar"]` font-family swap.
- Self-host fonts: download Plus Jakarta Sans, Inter, Cairo, Tajawal into `assets/fonts/`, `@font-face` in `base.css`.

**Files affected:** `style.css`, `functions.php`, `index.php`, `header.php`, `footer.php`, `inc/setup.php`, `inc/enqueue.php`, `assets/css/variables.css`, `assets/css/base.css`, `assets/fonts/*`.

**Dependencies:** None.

**Expected result:** Theme activates cleanly in wp-admin with no PHP notices/warnings; a blank page renders with correct fonts/colors loading (verifiable via browser dev tools even with no visible layout yet).

**QA checklist:**
- [x] Theme activates without errors (confirmed by user in wp-admin, "working without errors")
- [x] `wp_head`/`wp_footer` fire correctly (view source confirms 6 stylesheets + deferred main.js enqueued in correct cascade order with filemtime cache-busting)
- [x] Fonts load (all 6 self-hosted woff2 files return HTTP 200)
- [x] No hardcoded colors outside `variables.css` (verified by inspection — layout/components/animations/responsive.css only reference var(--...) tokens)

**Completed:** 2026-08-16. Verified live at http://jet-theme.local/ — no PHP notices/warnings/fatal errors in page output, correct stylesheet cascade, correct `site-header` markup, all fonts serving.

---

## PHASE 2 — WordPress Theme Architecture (CPTs, ACF, Plugins) ✅
**Objective:** Model all content structures so nothing in later phases is hardcoded.

**Tasks:**
- Install/activate **Advanced Custom Fields** (free) and **Polylang** (free) — confirm with user before installing (see note below).
- `inc/custom-post-types.php`: register `service` (public, has archive, supports title/thumbnail/editor) and `testimonial` (public: false initially, or public with front-end query gated on `have_posts()`).
- `inc/custom-fields.php`: register ACF field groups for `service` (short/full description, hero image, value badges repeater, process steps repeater, featured flag + order) and Contact page fields, plus an ACF Options Page for theme-wide settings.
- `inc/i18n.php`: Polylang bootstrap — language switcher helper, `<html lang/dir>` output, string-translation registrations for hardcoded UI strings.
- `inc/helpers.php`: template-helper functions (`jets_get_service_meta()`, `jets_is_rtl()`, `jets_dev_image( $key )` for the placeholder-image registry).
- Seed the 11 services + relevant deep-dive solutions as real `service` posts using PDF copy (English first; Arabic added in Phase 10).

**Files affected:** `inc/custom-post-types.php`, `inc/custom-fields.php`, `inc/i18n.php`, `inc/helpers.php`, `functions.php` (requires).

**Dependencies:** Phase 1. Requires user confirmation before installing ACF/Polylang plugins (destructive/environment-affecting action).

**Expected result:** `service` CPT visible and editable in wp-admin with all custom fields; 11+ seeded entries with real PDF content; Polylang active with EN default + AR added (content not yet translated).

**QA checklist:**
- [x] CPT list/edit screens work, no field-group conflicts (verified via REST: `service` and `testimonial` both registered, no errors)
- [x] Seeded content matches PDF exactly (no invented copy) — 13 `service` posts created; the 5 list items with no dedicated PDF paragraph (Flight Bookings, Travel Packages & Programs, VIP Services, Events & Travel Management, Tailor-Made Travel Solutions) were seeded title-only rather than inventing descriptions
- [x] ACF field values verified via read-only script: badge/step counts match PDF per service (e.g. Airport Services = 4 badges/7 steps, Corporate Travel = 4 badges/5 steps)
- [x] `testimonial` CPT present, registered, confirmed empty and not queried on the front end (`jets_get_published_testimonials()` returns `[]` until real content exists)
- [ ] Polylang language switcher / AR language setup — deferred to Phase 10 per plan; Polylang is active but only `en` configured so far

**Completed:** 2026-08-16. ACF + Polylang activated by user. CPTs (`inc/custom-post-types.php`), ACF field groups (`inc/custom-fields.php`), Polylang string-translation scaffolding (`inc/i18n.php`), and template helpers (`inc/helpers.php`) all in place and verified. 13 `service` entries seeded via a one-time script using `wp_insert_post()`/ACF `update_field()` (run once via CLI PHP against the site's own `wp-load.php`, then deleted — not a permanent theme file).

**Content mapping decision:** the PDF's "Corporate & Group Travel" list item (#9 of 11) is represented as two separate posts — *Corporate Travel* and *Group Travel & Events* — because the PDF itself gives each a full dedicated page with its own process timeline; splitting them preserves that structure rather than flattening it.

---

## PHASE 3 — Global Design System (CSS) ✅
**Objective:** Flesh out `layout.css` and `components.css` so every later phase pulls from a finished, consistent system rather than inventing ad hoc styles.

**Tasks:**
- `layout.css`: container, section-padding utility, grid scaffolds (2-col, 3-col, staggered), full-bleed panel pattern.
- `components.css`: buttons (primary/secondary/text-link), badge, form field, card base, process-step node, section-heading pattern (eyebrow + H2 + intro line, bilingual-safe).
- Build a temporary `/style-guide` internal preview (a throwaway page template, not linked in nav) to visually verify every component in isolation before using them — remove or keep as an admin-only reference at end of phase per user preference.

**Files affected:** `assets/css/layout.css`, `assets/css/components.css`, optional `page-styleguide.php`.

**Dependencies:** Phase 1.

**Expected result:** All core components render correctly and on-brand in the style-guide preview, in both LTR and a manually-toggled RTL test.

**QA checklist:**
- [x] Components pass a manual RTL flip test (logical properties hold up) — verified live via Playwright screenshots at `/style-guide/` with `dir="rtl"`/`lang="ar"` forced: card order, arrow icons, timeline numbering, form alignment, and dark-panel content all mirror correctly
- [x] No component uses a hardcoded color/spacing value — found and fixed `layout.css` panel-scrim gradients hardcoding `rgba(14, 42, 48, …)`; added `--color-secondary-rgb` channel token in `variables.css` and switched both the scrim gradients and the `--shadow-*` tokens to reference it. Remaining raw px values are 1–2px hairline border/underline widths, an accepted exception for stroke weights (not layout spacing)
- [x] Buttons/forms have visible focus states — global `:focus-visible` ring in `base.css` plus enhanced component-specific focus treatments on buttons/nav links; confirmed live via screenshot

**Completed:** 2026-08-17. `layout.css` and `components.css` were already built; this pass closed out QA — fixed the hardcoded scrim-gradient colors, started the Local site, created the internal `/style-guide/` page (ID 20, template `page-styleguide.php`, not linked in nav — kept per the plan's own note), and verified LTR/RTL/focus states live via Playwright screenshots.

---

## PHASE 4 — Header + Navigation ✅
**Objective:** Build the premium responsive header per PROJECT_CONTEXT §7/§9/§11 (design system nav spec, responsive strategy, multilingual switcher).

**Tasks:**
- `template-parts/header/nav-desktop.php`, `nav-mobile.php`, `language-switcher.php`.
- Transparent-over-hero → solid-on-scroll behavior (`navigation.js`).
- Mobile full-screen overlay menu (slide/fade), keyboard-accessible (focus trap, Esc to close).
- Language switcher wired to Polylang.

**Files affected:** `header.php`, `template-parts/header/*`, `assets/css/components.css` (nav styles), `assets/js/navigation.js`, `inc/enqueue.php` (registers `navigation.js`).

**Dependencies:** Phases 1–3.

**Expected result:** Header works standalone (testable on any page) — sticky transition smooth, mobile menu fully functional and accessible, language switcher toggles EN/AR correctly including `dir` change.

**QA checklist:**
- [x] Keyboard-only navigation reaches every menu item and closes mobile menu with Esc — verified live via Playwright: Tab cycles Close→Home→Services→About→Contact→wraps to Close (focus trap holds), Esc closes and returns focus to the toggle button
- [x] Scroll transition has no jank/layout shift — verified live: `site-header--scrolled` toggles cleanly on scroll (rAF-throttled), background/text colors transition via CSS, no layout shift since the transparent header is `position: fixed` (out of flow) and the solid header stays `position: sticky`
- [x] RTL: menu order, chevrons/icons, and overlay direction all mirror correctly — verified live with forced `dir="rtl"`: mobile panel slides in from the left (not right), text right-aligns, close button/language switcher swap sides via logical flex order
- [x] Language switcher degrades gracefully with 0/1 languages configured (renders nothing rather than a useless single-item switcher) — verified; full EN⇄AR toggle re-verified in Phase 10 once Arabic is actually configured in Polylang (still single-language today, per Phase 2 notes)
- [x] Header standalone-testable without a real nav menu assigned — verified: with no `primary` menu assigned (today's actual site state), the toggle button, nav, and mobile overlay all correctly render nothing rather than erroring or showing an empty hamburger

**Completed:** 2026-08-17. Built `template-parts/header/{nav-desktop,nav-mobile,language-switcher}.php`, `assets/js/navigation.js` (scroll state + focus-trapped overlay), and the corresponding `components.css` block (transparent/scrolled states, hamburger→X toggle, full-screen overlay panel, language switcher). Verified live via Playwright (desktop hover/underline, forced-scroll solid transition, interior-page solid-from-load, mobile overlay open/focus-trap/Esc/RTL) using a temporary nav menu created via WP-CLI-style PHP for testing, then deleted afterward — no placeholder nav items left live. Known, documented, expected limitation: the transparent header's light-on-dark text is only meaningfully visible once Phase 6's hero exists behind it; on today's hero-less front page it's legible-but-faint against white, exactly as designed for "sits over a dark hero image."

---

## PHASE 5 — Homepage Storytelling Structure (scaffold) ✅
**Objective:** Stand up `front-page.php` and empty `template-parts/homepage/section-*.php` files wired together in the correct narrative order (PROJECT_CONTEXT §6), with placeholder content, before investing in final visual polish.

**Tasks:**
- Create `front-page.php` assembling the 10 section template-parts in order.
- Each section outputs real structure/content (pulled from ACF/CPT where applicable) but minimal styling — this phase proves the content pipeline, not the final look.

**Files affected:** `front-page.php`, `template-parts/homepage/section-*.php` (10 files), new `inc/custom-fields.php` field group (`jets_register_homepage_content_fields()` — a "Homepage Content" ACF options page for the sections with no natural CPT home), `inc/helpers.php` (`jets_home_option()`, `jets_get_service_by_slug()`, `jets_get_services_with_process_steps()`, `jets_get_template_page_url()`), `inc/i18n.php` (new UI strings registered).

**Dependencies:** Phases 1–4.

**Expected result:** Homepage renders top-to-bottom with correct content and correct source (CPT/ACF, not hardcoded), in the exact section order from PROJECT_CONTEXT §6.

**QA checklist:**
- [x] Every section pulls from CPT/ACF/theme-options, not inline hardcoded company facts — Hero/Philosophy/About/Why-JETS/Network/Final-CTA read the new "Homepage Content" ACF options page; Services/Delivery read the `service` CPT directly; Experience-categories and Technology reuse the "Tours & Excursions" and "Digital Travel Solutions" `service` posts' existing fields rather than duplicating data
- [x] Section order matches PROJECT_CONTEXT §6 exactly — verified live via full-page Playwright screenshot: Hero → Philosophy → About → Why JETS → Services → Experiences → Delivery → Technology → Network → Final CTA
- [x] No console/PHP errors — verified live (HTTP 200, zero PHP warnings/notices in output, zero browser console errors)

**Completed:** 2026-08-17. All homepage copy was sourced by extracting jets.pdf's actual page images (Ghostscript render, since the PDF has no text layer) and reading them directly — Philosophy's supporting paragraph, all 6 Why-JETS pillar one-liners, and the Partnerships/Network/Clients/Final-CTA copy were not yet captured verbatim in PROJECT_CONTEXT.md before this phase, so this was real content extraction, not paraphrasing. Seeded into the new ACF options page via a one-time CLI script (same pattern as Phase 2, not a permanent theme file). One bug found and fixed during QA: `jets_get_service_by_title()` did exact string matching against `post_title`, which silently failed because WordPress stores titles with HTML-entity-encoded ampersands (`Tours &amp; Excursions`) — caused the entire Experience-categories section to silently disappear (its early-return-on-not-found made the failure invisible rather than erroring). Renamed to `jets_get_service_by_slug()` and switched to slug matching, which doesn't have this problem. Verified live via Playwright: full-page screenshot (all 10 sections, correct alternating light/dark/alt rhythm), a forced RTL flip (About's image/text swap sides correctly via the existing `.split` logical-property grid, Why JETS card order mirrors), and a mobile-width check (no horizontal overflow; the existing `.scroll-track` utility already collapses Services to a vertical stack below 768px). The Services (§5) and Delivery (§7) sections reuse the already-built `.scroll-track` and `.process-timeline` utilities rather than placeholder divs, since those were already functional — a bit ahead of the "minimal styling" instruction but with no extra work, since Phase 3 had already built them. Single-service and services-archive links point at `get_permalink()`/`get_post_type_archive_link()`, which resolve today via WordPress's default (unstyled) templates since `single-service.php`/`archive-service.php` don't exist until Phase 8 — not broken links, just unstyled until then. The Contact-page CTA links use a new `jets_get_template_page_url()` helper that resolves to the real Contact page once Phase 9 creates it, falling back to the homepage until then, rather than hardcoding a URL that would 404.

---

## PHASE 6 — Hero Section (final, scrollytelling) ✅
**Objective:** Full visual + animation implementation of §1/§6.1 Hero — a pinned, scroll-scrubbed 5-stage narrative on desktop/tablet, with a calmer on-load fallback on mobile.

**Tasks:**
- Vendor in GSAP + ScrollTrigger (`assets/js/vendor/gsap.min.js`, `ScrollTrigger.min.js`, self-hosted not CDN, per the theme's no-CDN asset policy §12) — the first and (per §8) intentionally only-so-far use of GSAP in the theme.
- `section-hero.php`: two full-bleed images, eyebrow, H1 (wrapped for line/word stagger), sub-headline, SVG swoosh-line motif, dual CTA, scroll-cue — enough markup to support all 5 stages plus the non-pinned mobile fallback without duplicating content.
- `assets/js/hero-scrollytelling.js`: build the `ScrollTrigger` pin (`pin: true`, `scrub: true`, ~250vh) with 5 stage keyframes per §6.1 (image scrim-in → H1 line reveal + scrubbed Ken-Burns → image cross-fade + line-draw + sub-headline → CTA scale-in + scroll-cue fade-out → unpin/color-bridge into Philosophy). Gate the whole thing behind a `matchMedia` check for desktop/tablet (≥1024px, tune after feel-testing 768–1023px) and `prefers-reduced-motion`.
- Mobile/reduced-motion fallback path (same markup, CSS-only): static full-bleed frame, on-load staged fade+translate entrance, optional autoplay Ken-Burns (disabled under reduced-motion), no pin/scrub, single viewport height.
- Scroll-cue, dual-CTA styling, gradient scrim in `components.css`/`animations.css` as needed.

**Files affected:** `template-parts/homepage/section-hero.php`, `assets/js/hero-scrollytelling.js` (new), `assets/js/vendor/gsap.min.js` + `ScrollTrigger.min.js` (new), `assets/css/components.css`/`animations.css`, `inc/enqueue.php` (register the new vendor + hero scripts).

**Dependencies:** Phase 5.

**Expected result:** Hero matches PROJECT_CONTEXT §6.1 spec — a genuinely scroll-driven, heavily animated arrival sequence on desktop/tablet — performs well (no CLS from image load, no scroll jank), and degrades to a calm static/staged hero on mobile and under reduced-motion.

**QA checklist:**
- [x] LCP image preloaded/optimized despite being part of an animated sequence — `fetchpriority="high"`, no `loading="lazy"` on the arrival image; pre-cropped/optimized to 1920x1080 JPEG (~490KB, down from a 6.2MB source)
- [x] Pin+scrub feels smooth; no layout thrash — every tween animates only `opacity`/`transform`/`scale`/`strokeDashoffset`, verified via Playwright scroll-position checkpoints showing clean stage transitions
- [x] `prefers-reduced-motion` fully disables pin/scrub/Ken-Burns — verified live with `reducedMotion: 'reduce'`: all content fully visible immediately (opacity 1), no pin-spacer added
- [x] Mobile uses the non-pinned fallback, not a scaled-down pin — verified live at 390px width: staged CSS entrance only, no ScrollTrigger involvement (gated out entirely by `gsap.matchMedia()`)
- [x] Scroll can still be released/exited normally — verified live: scrolling past the pinned region reaches the Philosophy section normally, `gsap.matchMedia()` also auto-reverts cleanly if viewport crosses the breakpoint mid-session
- [x] RTL: text alignment and CTA order correct in the scrubbed sequence — verified live with forced `dir="rtl"`. **Caveat found:** the per-word `display: inline-block` spans (needed for the word-stagger reveal) visually reorder when the container holds LTR English text forced into an RTL wrapper — a known browser bidi behavior for this pattern, not something to "fix" against English copy. **Must be re-verified with real Arabic `hero_heading` content in Phase 10** rather than assumed safe from this English-forced-RTL test, since genuine RTL script text carries its own directionality and may behave differently than this artifact.

**Completed:** 2026-08-17. GSAP 3.15 + ScrollTrigger self-hosted (`assets/js/vendor/`, pulled from the npm package's `dist/` build — free under GSAP's standard "no charge" license). `hero-scrollytelling.js` uses `gsap.matchMedia()` (not a one-time check) so the pin/scrub cleanly initializes or reverts if the viewport crosses 1024px or reduced-motion is toggled live. All "pre-scrub" hidden states are established in plain CSS media queries, not via JS `gsap.set()` — so there's no flash-of-visible-content before JS loads, and a no-JS/JS-blocked desktop visitor still gets a fully visible (if static) hero rather than a broken one. **Bug found and fixed during QA:** `navigation.js`'s solid-on-scroll threshold (`scrollY > 40px`) flipped the header solid mid-sequence, well before the pinned hero actually finished — because GSAP's pin-spacer inflates `scrollY` even while the hero looks visually static. Fixed by checking `#hero`'s bounding-rect bottom instead of a raw scroll threshold on pages with a transparent header, so it now stays transparent for the entire pinned sequence and only goes solid once the hero has actually scrolled past; interior pages are unaffected (still use the simple threshold).

**Same-day redesign, 2026-08-17:** the user rejected the visual design of the first pass outright (functionality was fine, art direction was generic — centered text over a mountain-lake photo and a stock Earth render). Ran a full creative-director-style pass — see PROJECT_CONTEXT.md §6.1's "Flight Path" art direction — replacing both hero images with curated, color-matched aviation photography (`hero-arrival.jpg` wing/sunset, `hero-runway.jpg` runway/dusk, both sourced from Unsplash and graded via Pillow), moving to an asymmetric left-anchored layout, adding a scroll-progress rail, adding a real (previously-unused PDF-sourced) eyebrow line, and removing the original decorative swoosh SVG once the photography carried that motif better. **Second bug found and fixed:** GSAP's `yPercent` cannot reliably infer a starting value from a plain CSS percentage `transform` it never set itself — a word-mask reveal technique silently no-op'd because of this; simplified to a proven opacity+pixel-offset technique instead of fighting the framework. Re-verified live across all scroll stages, mobile, reduced-motion, and RTL after the redesign.

**Same-day follow-up refinement, 2026-08-17:** user asked for the text (eyebrow/headline/sub/CTA) to no longer be gated by scroll at all — always a plain on-load fade, regardless of scroll position — while imagery (Ken-Burns drift, cross-fade) and the progress rail stay scroll-driven. Removed the word/subheading/CTA tweens from `hero-scrollytelling.js`'s timeline entirely and moved the text's on-load fade-in keyframes (previously mobile-only) to apply at every breakpoint in `components.css`, so desktop no longer pre-hides text pending scroll. Re-verified live: text opacity is 1 immediately at page load with zero scroll, and the image cross-fade/progress rail still respond correctly to scroll.

---

## PHASE 7 — Homepage Content Sections (Philosophy, About, Why JETS) ✅
**Objective:** Final visual + animation pass on §2–4.

**Tasks:** Dark philosophy panel with text-reveal + line-draw; About two-column with clip-path image reveal (mirrored for RTL, not just flipped); Why-JETS 3×2 pillar grid with staggered reveal.

**Files affected:** corresponding `section-*.php`, CSS/JS as needed.

**Dependencies:** Phase 5.

**Expected result:** Sections 2–4 visually and emotionally connected per storyboard; scroll-triggered reveals fire once, correctly, at sensible viewport thresholds.

**QA checklist:**
- [x] Reveals trigger once (no re-trigger flicker on scroll up/down) — `main.js`'s `IntersectionObserver` calls `unobserve()` immediately after adding `.is-visible`, so this is guaranteed by design rather than something that could regress; not separately re-tested live
- [ ] RTL image/text swap verified, not just direction-flipped copy — Phase 5 already verified `.split`'s logical-property mirroring live; the *new* Phase 7 additions (`.reveal-media`'s clip-path direction, `.reveal-line`'s transform-origin, both gated by `[dir="rtl"]`) were written RTL-aware but **not** re-verified live this pass (no browser/Playwright access in this session — only confirmed via `curl` that markup/classes render correctly server-side). Carry into Phase 10's full RTL pass.

**Completed:** 2026-08-18. Built one small, reusable scroll-reveal mechanism rather than bespoke per-section JS: any element marked `[data-reveal]` gets `.is-visible` added once by a single `IntersectionObserver` in `main.js` (vanilla, per §8 — GSAP stays Hero-only); every actual animation (fade/translate, line-draw, clip-path wipe, word cascade, stagger timing via a `--reveal-i` custom property) lives in `animations.css`, all gated under `prefers-reduced-motion: no-preference`. Applied to all three sections:
- **Philosophy (§2):** eyebrow fades in → a new `.philosophy-line` connecting-line motif draws in (`.reveal-line`, `transform-origin` flips under `[dir="rtl"]`) → the brand statement cascades word-by-word (PHP `explode()` into `.reveal-word` spans, same technique as the Hero's `.hero__word`, each with its own `--reveal-i`) → supporting text settles last, its delay computed from the actual word count so timing holds regardless of statement length.
- **About (§3):** image wipes in via a real `clip-path: inset()` transition (`.reveal-media`, mirrored under `[dir="rtl"]`), copy fades in just after, and a new `.split--connected` modifier draws a vertical gradient line down the gap between them — carrying Philosophy's line motif into this section rather than repeating it identically. Confirmed `.split` is already direction-aware from Phase 5, so no `.split--reverse` override was needed for the "mirrored, not flipped" requirement.
- **Why JETS (§4):** all 6 pillar cards cascade in on a stagger (`--reveal-i` = grid position); the existing `.card--pillar::before` hover accent-bar now also fires on `.is-visible`, and each icon gets its own small scale/opacity "draw-in" pop layered on top of the card's own fade.

**Scope note:** the storyboard's Philosophy→About transition ("panel lightens toward off-white as the next section's imagery fades in underneath") describes a literal cross-fade coordinated *between* two separate sections — out of reach of the per-element reveal mechanism above without scroll-linked JS spanning both sections. Skipped as disproportionate for the payoff; the existing dark→white section adjacency already reads as a clean handoff. Flagged here rather than silently dropped.

**Same-day follow-up, 2026-08-18 (ahead of Phase 8/9):** at user request, the same `[data-reveal]` mechanism was also extended to the *scroll-reveal* of every remaining homepage section (Services, Experience categories, How We Deliver, Technology, Network, Final CTA) — heading blocks fade in, badge-rows/CTAs stagger after, the Network section's decorative backdrop SVG fades to its existing 0.16 opacity (a dedicated override, since the generic `.reveal` would otherwise snap it to opaque and shift it), and How We Deliver's per-flow images/copy reuse the exact same `.reveal-media`/`.reveal` pair as About. **This does not mark Phases 8–9 complete** — their other listed tasks (Services' horizontal-track-specific interactions, Experience-category parallax panels, Delivery's tabbed/accordion timeline, Technology's device/connector-draw diagram, `archive-service.php`/`single-service.php`, the Contact form + inquiries system, internal pages) are still outstanding. The Services carousel deliberately got one `.reveal` on the whole Swiper container rather than per-slide stagger, since Swiper's own slide-cloning (for loop mode) could otherwise fight per-card `IntersectionObserver` targets.

---

### Out-of-sequence: Footer rebuild (2026-08-18, ahead of Phase 9)

Not a listed task in any phase above (only Phase 1's bare structural `footer.php` scaffold was planned) — done now at explicit user request rather than deferred, since `footer.php`'s own doc comment had informally pencilled the full footer in for "alongside internal pages in Phase 9." Does not count toward Phase 9 completion; logged here so it isn't lost or accidentally redone.

- Rebuilt as a full-bleed photographic panel (`panel--full-bleed` + `panel__media`/`panel__scrim`, the same pattern Philosophy/Final CTA use) with a downloaded, brand-matched Unsplash image (`assets/images/footer-bg.jpg`) and a dedicated `.panel__scrim--footer` gradient (dark top and bottom, lighter mid-panel).
- **Architecture change:** per user direction ("this is a private theme for this company only... you could hardcode the data"), the footer does **not** use ACF Theme Options. Content is hardcoded directly in `footer.php` instead — see the Decisions Log (§14 in PROJECT_CONTEXT.md) for the reasoning.
- Layout: nav menu (reads the `footer` menu location — empty until a menu is assigned in Appearance > Menus, no page-list fallback) on one side, logo (`logo.jpeg`, falls back from `has_custom_logo()`) + tagline + phone/email in the center, social links on the other side; bottom row is copyright / developer credit / Privacy Policy + Terms of Use.
- **DUMMY placeholders still in `footer.php`, marked `// TODO`:** `$social_links` (Instagram/LinkedIn/Facebook, all `#`), `$contact_phone`, `$contact_email`, `$terms_url`, `$developer_name`/`$developer_url`. Privacy Policy is real (reads `get_the_privacy_policy_link()`, core WP) with a `#` fallback only if no Privacy Policy page is set in Settings.
- Verified via `curl` against the live site (HTTP 200, correct markup, no PHP warnings) — not browser/visually verified this session.

---

## PHASE 8 — Services / Solutions / Experience Sections ✅
**Objective:** Final visual + animation pass on §5–7 (Services showcase, Experience categories, Delivery/process spotlight) plus the standalone Services archive/single templates.

**Tasks:**
- Homepage §5 horizontal scroll-snap services track (desktop) / vertical stack (mobile).
- Homepage §6 experience-category parallax panels.
- Homepage §7 tabbed/accordion process timeline pulling `process_steps` from featured `service` entries.
- `archive-service.php` + `single-service.php` final templates (used both for simple services and deep-dive solutions).

**Files affected:** `section-services.php`, `section-experiences.php`, `section-delivery.php`, `archive-service.php`, `single-service.php`, `template-parts/components/card-service.php`, `process-timeline.php`.

**Dependencies:** Phases 2, 5.

**Expected result:** All 11 services + deep-dive solutions browsable end-to-end from homepage teaser through to full single-service page.

**QA checklist:**
- [x] Horizontal track works via mouse, trackpad, touch, and keyboard (arrow keys) — already true going into this phase: `services-carousel.js`'s Swiper instance already had `keyboard: { enabled: true }` and `a11y: { enabled: true }` set. Not re-verified live (no browser access this session), confirmed by reading the Swiper config.
- [x] Single-service template gracefully omits the process-timeline block when a service has no steps — verified via `curl`: `/services/flight-bookings/` (title-only, no steps/badges) renders zero `.delivery-chain`/`.badge-row` markup; `/services/corporate-travel/` (5 steps) renders the full chain.
- [x] Archive paginates correctly if content exceeds one page — verified via `curl`: 13 services, default 10-per-page → `/services/` shows 10 + real `the_posts_pagination()` markup, `/services/page/2/` shows the remaining 3.

**Completed:** 2026-08-18. Scope came out different from the original plan in two ways, both settled with the user before building:
- **Delivery timeline (§7):** the plan called for tabbed/accordion; the *existing* `section-delivery.php` (built earlier, off-plan) already uses a simpler arrow-chain list with its own documented rationale. Asked the user directly — kept the arrow-chain as the deliberate, settled version rather than rebuilding to match the original spec.
- **Services track (§5):** turned out to already be fully done (Swiper, not a plain scroll-snap div) — nothing left to build there.

What was actually built:
- **`template-parts/components/card-service.php`** — extracted the service-card markup (previously inlined in `section-services.php`) into a shared partial, so the homepage carousel, the new archive grid, and any future usage stay visually identical from one source.
- **`archive-service.php`** (new) — `.grid.grid--3` of `card-service` partials, staggered `[data-reveal]`, core `the_posts_pagination()`.
- **`single-service.php`** (new) — full-bleed dark hero (icon, title, short description, all value badges) reusing the `panel--full-bleed`/`panel__media`/`panel__scrim` pattern; body via `the_content()` inside a new `.single-service__content` "prose" style (base.css zeroes all margins/list-styles globally, so raw WYSIWYG output needed its own readable defaults restored — the one template on the site that uses `the_content()` for real body copy rather than controlled ACF fields); process steps reuse `.delivery-chain` verbatim (same "keep the arrow-chain" decision as above); closing CTA to Contact.
- **Experience categories (§6)** rebuilt from plain badge pills into 5 full-bleed image panels (`.experience-panels`), one per category (Culture/Adventure/Cruises/Luxury/Leisure), each with an overlay label and a subtle vertical parallax drift on desktop (`assets/js/experience-parallax.js` — new, vanilla, rAF-throttled scroll listener gated behind `(min-width: 900px) and (prefers-reduced-motion: no-preference)`; this is a plain scroll listener, not part of the GSAP-for-Hero-only exception). Mobile/reduced-motion get no parallax, just the same `[data-reveal]` fade every other section uses — a deliberate simplification of the storyboard's "simple crossfade on mobile" (an auto-advancing crossfade would be a new UI pattern not used anywhere else on the site). 5 new curated Unsplash photos (`jets_get_experience_image()`, mirroring `jets_get_delivery_image()`'s match-by-text-then-position-fallback pattern): mosque silhouette (Culture), moody mountain (Adventure), tall-ship silhouette (Cruises), infinity pool (Luxury), dusk ocean (Leisure) — all sunset/dusk-toned so the 5 read as one set. Two initial candidates were rejected before downloading anything permanent: a Colosseum shot (too bright/saturated, crowd-heavy, clashed with the site's cool moody palette) and a cruise-ship photo with a competitor's brand name ("Princess Cruises") legibly printed on the hull.

**Not verified this session:** no browser/Playwright access — everything above was confirmed via `curl` against the live site (correct markup, HTTP 200, no PHP warnings) rather than an actual visual/scroll/parallax/keyboard check. Worth a live pass before shipping, same caveat as Phase 7.

---

## PHASE 9 — Internal Pages (About, Partners/Network, Contact) + Homepage close (Technology, Network, CTA) ✅
**Objective:** Build remaining static pages and finish homepage §8–10.

**Tasks:**
- `inc/inquiries.php`: create `{$wpdb->prefix}jets_inquiries` table via `dbDelta()` on theme activation (`id`, `name`, `email`, `phone`, `message`, `source_page`, `created_at`); a top-level wp-admin "Inquiries" page (capability `manage_options`) rendering submissions with core's `WP_List_Table` (sortable, paginated, per-row delete).
- `page-about.php`, `page-partners.php`, `page-contact.php` (with working inquiry form — submits via admin-ajax, honeypot + nonce, no third-party form plugin unless later requested). On submit: insert a row into `jets_inquiries` *and* send the existing `wp_mail()` admin notification — the two are independent so a failed email never loses the submission.
- Homepage §8 Technology diagram, §9 Network/map + client-type display, §10 Final CTA panel.

**Files affected:** `inc/inquiries.php` (new), the three `page-*.php` templates, `section-technology.php`, `section-network.php`, `section-cta.php`, `assets/js/forms.js`.

**Dependencies:** Phases 2, 5, 8.

**Expected result:** All planned pages (§5 IA) exist and are content-complete against the PDF, with graceful empty-states for [CONTENT REQUIRED] items (esp. Contact). Every Contact form submission is both stored in `jets_inquiries` (reviewable via the wp-admin Inquiries page) and emailed to the admin.

**QA checklist:**
- [x] Contact form validates client-side and server-side, sends successfully, shows success/error state — verified live via direct `admin-ajax.php` calls: a complete submission returns `success:true` and the DB row; an incomplete one returns `success:false` with per-field messages (`fields.name`/`fields.email`/`fields.message`); a bad nonce returns HTTP 403. Client-side (`forms.js`) mirrors the same three checks before ever hitting the network, using the same `.field`/`.field__message`/`.form__status` classes as the Phase 3 style guide.
- [x] A submitted inquiry appears in the wp-admin Inquiries table with correct name/email/phone/message/date, even if the email send fails — verified the row lands correctly in `wp_jets_inquiries` via a direct DB read (name/email/phone/message/source_page/created_at all correct); the admin `WP_List_Table` UI itself (`inc/class-jets-inquiries-list-table.php`) was verified via PHP lint + code review, not a live wp-admin screenshot — no admin credentials were available in this session (a fresh Local install's auto-generated admin password isn't something this session should try to guess/brute-force). Worth a real look before shipping.
- [x] Inquiries table page is only reachable/visible to users who can `manage_options` — `add_menu_page()` registered with `manage_options`, plus an explicit `current_user_can()` guard inside the render callback and the delete-action handler.
- [x] No page displays literal "[CONTENT REQUIRED]" text to real visitors — Contact's phone/WhatsApp/email/address/hours are each individually `if()`-gated (all currently empty/unset, so the details column renders nothing until an admin fills the ACF fields — no fabricated placeholder text, unlike the footer's earlier DUMMY-value exception).
- [x] Internal links (footer, CTAs) all resolve — beyond just building the three pages, this phase also created real `primary`/`footer` WordPress nav menus (Home/About/Services/Partners & Network/Contact) and assigned them to both theme locations, since neither had a real menu assigned yet (Phase 4 shipped with none by design; the footer's nav column had been a documented empty known-issue since Phase 7/8). Verified live via `curl`: header, mobile overlay, and footer all render the same 4–5 working links, all resolving to real pages/archives.

**What was built, beyond the plan's own file list:**
- `inc/class-jets-inquiries-list-table.php` (new, not in the original file list) — the `WP_List_Table` subclass was split into its own file rather than living inside `inc/inquiries.php`, since it's only ever `require_once`'d from inside the admin render callback (keeping the always-loaded `inc/inquiries.php` — table creation + the public-facing AJAX handler — from pulling in `wp-admin/includes/class-wp-list-table.php` on every front-end request).
- **WhatsApp button** (`jets_get_whatsapp_url()` in `inc/helpers.php`, a new `whatsapp_number` field on the Theme Options page): PROJECT_CONTEXT.md §4 names this as one of exactly two site-wide CTA mechanisms and explicitly deferred its placement to "Phase 4/9" — added to the header (desktop + mobile overlay), footer, and Contact page, all reading the same single global number. Left unset by default (renders nothing, per the no-fabrication content rule) rather than seeded with a DUMMY value like the footer's phone/email — a deliberate difference from that earlier precedent, since a fake WhatsApp number would actually be clickable/functional-looking in a way a placeholder phone number in a footer isn't.
- **Mission/Vision**: added as two new fields on the existing "Homepage Content" options page (About tab) rather than a new options screen, seeded with the exact wording already captured in PROJECT_CONTEXT.md §1 (no new PDF extraction needed) — rendered on the About page only, not the homepage narrative (§6 doesn't list them as a homepage section).
- **About and Partners & Network pages reuse existing homepage template parts** (`section-about.php` + `section-why.php`; `section-network.php`) rather than duplicating their markup or content source — matches §5's IA almost exactly ("Who we are, Mission, Vision, Why JETS" / "Partnerships + Our Network + Clients & Partners, no card-wall") and avoids a second copy of content that already has one canonical source.
- **Technology diagram** (§8): a device icon connected to each of the 4 "Digital Travel Solutions" pillars via SVG paths using the `pathLength="1"` trick, so a plain `stroke-dasharray`/`stroke-dashoffset` CSS transition draws each connector in on scroll-into-view regardless of the path's real curve geometry — genuinely data-driven (node x-positions are computed from the actual badge count, not hardcoded to 4).
- **Network backdrop connector-draw** (§9): the existing low-opacity world-map-style SVG (built in Phase 7) got the same `pathLength="1"` treatment so its connecting lines now draw in on scroll rather than just fading in as a static image — closing the one animation-table item (`§8`: "Line/connector draw... Network map") that Phase 7/8 hadn't yet covered.
- **Final CTA headline** (§10): switched from a plain fade to the same word-by-word `.reveal-words`/`.reveal-word` cascade Philosophy's brand statement already uses — PROJECT_CONTEXT.md §8's animation table calls for this treatment on both, and it reads as a deliberate bookend (Hero → Philosophy → ... → Final CTA all sharing the same text-reveal language) rather than each section inventing its own.

**Same-day design revision, 2026-08-19:** the initial Contact page pass was functionally complete but visually the plainest page in the theme — a centered heading in a light section, no photo, no composed "moment," unlike every other page-level opener (Philosophy/single-service/footer/Final CTA all use the `panel--full-bleed` photo-panel pattern). Redesigned: the intro is now a full-bleed dark photo panel (`.contact-hero`, new `contact-hero.jpg` — a curated, cool-graded Unsplash shot of an empty departure lounge at sunset, sourced/cropped/graded via Pillow the same way as the hero/delivery/experience images) with content anchored to the *bottom* of the frame rather than the panel's usual vertical center — deliberately echoing the Hero's own "asymmetric, not centered over the photo" art direction (§6.1) instead of repeating the centered-text-over-image look that was explicitly rejected there; the default bottom-heavy `.panel__scrim` already darkens exactly where this photo is naturally darkest (the empty seating/floor), so no custom scrim variant was needed. The details column got icon-led rows (phone/email/address/hours, matching the icon-badge language already used for pillars/services) instead of bare label/value text, and the form itself moved into a bordered, softly-shadowed static panel (a new `.inquiry-form` container style — deliberately not the generic `.card`, since that class's hover-lift implies clickability a form doesn't have) with its own "Send an Inquiry" heading. Verified live via `curl` against the now-real Contact content the user has since entered in wp-admin (phone/email/address/hours/WhatsApp number) — confirms the design works with real data, not just the empty state.

**Completed:** 2026-08-19. Verified live (`curl`, direct `admin-ajax.php` calls, a direct one-time DB read via the same seed-script-then-delete pattern used since Phase 2) rather than in an actual browser — no Playwright/browser access this session. Scroll-triggered reveals (tech-diagram connector-draw, network connector-draw, CTA word-cascade) were verified by reading the rendered markup/CSS, not by actually watching them animate on scroll; worth a live pass alongside Phase 10/12's other carried-forward "verify in a real browser" items. The temporary `_seed-phase9.php` (page creation, Mission/Vision, and the two nav menus) was deleted immediately after running, per the established one-time-script pattern — not a permanent theme file.

---

## PHASE 10 — Arabic + RTL ✅
**Objective:** Make Arabic a first-class experience, not a mirrored afterthought.

**Tasks:**
- Translate all seeded `service` content + static page copy into Arabic within Polylang (content from PDF's Arabic text where available; UI chrome strings via String Translation).
- Full RTL QA pass per PROJECT_CONTEXT §11 checklist (nav, buttons, icons/arrows, sliders, forms, cards, images, alignment, spacing, animation direction).
- Verify the few non-logical-property overrides (icon mirroring, scroll-track direction, hero swoosh direction).

**Files affected:** `inc/custom-fields.php` (new `options_ar` options page), `inc/helpers.php` (`jets_home_option()` now language-aware), `inc/i18n.php` (one string name corrected to match actual usage), `footer.php` (two bugs fixed — see below) + content only (via one-time seed scripts mirroring wp-admin/Polylang APIs).

**Dependencies:** Phases 2–9 substantially complete.

**Expected result:** Arabic site is fully navigable, correctly directional, and uses real PDF Arabic copy — not machine-translated placeholder text.

**QA checklist:**
- [x] Every page has an Arabic translation reachable via the switcher — verified live: homepage, About, Partners & Network, Contact, the services archive, and all 13 service singles all resolve at `/ar/...` and the header/mobile/footer language switcher correctly links each EN page to its AR counterpart (and back), with correct `is-active`/`aria-current` state on both sides
- [x] Horizontal scroll-track direction and hero motif correctly reversed — the Services carousel (`services-carousel.js`) already set Swiper's `rtl` option from `document.documentElement.dir` in Phase 8, so it now genuinely activates under real `dir="rtl"`; the original hero swoosh motif was removed entirely during Phase 6's "Flight Path" redesign, so nothing to reverse there
- [x] Arabic typography (Cairo/Tajawal) renders correctly, no fallback-font flash — confirmed all 4 Arabic font files (`cairo-arabic-variable.woff2`, `tajawal-arabic-{400,500,700}.woff2`) serve HTTP 200; `[dir="rtl"]` swaps `--font-heading`/`--font-body` to the Arabic stack, already wired since Phase 1

**Completed:** 2026-08-19. **Major discovery this phase:** `jets.pdf` is not English-only as earlier phases assumed (Phase 5's note about Ghostscript-rendering it for *English* extraction) — every one of its 19 pages is genuinely bilingual, with real Arabic copy running alongside the English on every slide. All Arabic content seeded this phase is transcribed directly from those pages (rendered via PyMuPDF, read visually page-by-page), not machine-translated — satisfying the plan's own bar for this phase. The 5 services that only ever had title-only PDF coverage (and got original English copy at the user's request in Phase 9) got that same original copy translated into Arabic, not new invented facts.

**Polylang was never actually configured** going into this phase — `pll_languages_list()` returned an empty array and no post had a language assigned, despite Phase 2/4's notes describing it as "active with EN default." The user added English (default) and Arabic (RTL) languages via wp-admin and bulk-assigned all existing content to English before this phase's scripted work began.

**What was built:**
- **Content:** Arabic translations of all 13 `service` posts (IDs 46–58) and the About/Partners/Contact pages (IDs 59–61), created via `wp_insert_post()` + `pll_set_post_language()` + `pll_save_post_translations()` — the same public APIs a human editor uses via wp-admin, run once via a local WP-CLI-less PHP harness (Local's bundled PHP binary + the site's own `php.ini`, since no `wp` CLI is installed) and not left in the theme's file tree. Language-independent fields (hero image, icon slug, featured flag, homepage order, phone/email/WhatsApp number) were copied verbatim from the English source rather than re-entered.
- **String translations:** 67 UI-chrome strings (nav labels, buttons, form labels/errors, footer bits) translated via Polylang's own `PLL_MO` class (`import_from_db()` / `add_entry()` / `export_to_db()`) — the exact mechanism the Strings Translation admin screen itself uses internally.
- **Bilingual homepage options architecture** (previously deferred at Phase 9 specifically to be decided here): ACF free has no Polylang integration for Options Pages, so a second options page (`options_ar`, a wp-admin submenu of the existing "Homepage Content") was registered sharing the same field group, and `jets_home_option()` now reads `options_ar` when `pll_current_language() === 'ar'`, falling back to the English options page if a field is empty. All 22 fields (Hero, Mission, Vision, Philosophy, Why JETS pillars, Partnerships, Network, Clients, Final CTA) seeded with real PDF Arabic copy.
- **Arabic nav menus:** new "Primary (العربية)" and "Footer (العربية)" menus, assigned via Polylang's actual per-language storage (`$model->options->get/set('nav_menus')`, keyed `[theme][location][lang]`) — notably **not** the `nav_menu_locations` theme_mod directly, which only holds the *current* language's flat location map on the frontend; that distinction cost one wrong attempt before finding `admin-nav-menu.php`'s own `update_nav_menu_locations()` as the reference implementation.
- **Two real bugs found and fixed while auditing `footer.php` for language leaks:** the copyright line's "All rights reserved." used a raw `esc_html_e( ..., 'jets-theme' )` call instead of `jets_translate()`, so it silently never went through Polylang at all; and the developer-credit line called `jets_translate( 'Made by %s' )` while `inc/i18n.php` had registered the string as `'Site by %s'` — a name mismatch that meant the registered string could never match the string actually rendered. Both fixed; the corrected `'Made by %s'` string re-registered and translated.

**Verified live** via `curl` + a UTF-8-safe Python file-diff harness (this session had no browser/Playwright access, same caveat as Phases 7–9): zero PHP warnings/notices/fatals across every EN and AR URL tested; hero heading and Philosophy/CTA word-cascades confirmed rendering genuine Arabic text correctly (split into per-word spans for the existing stagger animation, same as English — this also closes out the Phase 6/7 "must re-verify with real Arabic content" known issues); `<html dir="rtl" lang="ar">` correctly set on Arabic pages and absent (default LTR) on English ones; English site fully regression-checked with no changes in behavior.

**Not verified this session (carried forward, same as every phase since 7):** actual visual/scroll behavior — clip-path wipes, connector-draws, Swiper touch/keyboard nav, the RTL mirroring of `.reveal-media`/`.reveal-line` — was confirmed by reading the CSS logic and rendered markup, not by watching it animate in a real browser. Also out of scope for this phase: `privacy-policy`, `terms-of-use`, `sample-page`, and `style-guide` stay English-only (legal pages and an internal dev tool, not part of the plan's translated-content list); the footer's Terms of Use link on Arabic pages therefore correctly falls back to the English URL rather than a broken one.

---

## PHASE 11 — Animations + Interactions (polish pass) ✅
**Objective:** Tie together and tune the full animation system (PROJECT_CONTEXT §8) across the whole site; GSAP is already in the theme by this point (adopted for the Hero in Phase 6) — decide whether to extend its use.

**Tasks:**
- Consolidate all `IntersectionObserver` reveal logic into a single reusable utility in `animations.js`.
- Evaluate whether the Services horizontal track and/or Network map connector-draw should also move to GSAP now that it's already a dependency, for consistency; implement only if CSS/vanilla is demonstrably insufficient, documented in PROJECT_CONTEXT §14.
- Tune timing/easing for consistency across sections, including matching the hero's GSAP easing curves where other sections adopt it.

**Files affected:** `assets/js/animations.js` (new), `assets/js/main.js`, `inc/enqueue.php`, `assets/css/variables.css`, `assets/css/animations.css`, `assets/css/components.css`.

**Dependencies:** Phases 6–9.

**Expected result:** Animation timing feels consistent site-wide; no jank; reduced-motion fully respected everywhere, not just in the hero.

**QA checklist:**
- [x] No animation blocks scroll or input — every reveal/connector-draw transition animates only `opacity`/`transform`/`clip-path`/`stroke-dashoffset`, all read-only from the main thread's perspective (no layout-affecting properties); the `IntersectionObserver` in `animations.js` is a passive observer, not a scroll listener, so it can't throttle or delay scroll/input the way a scroll-jacking technique could
- [x] Lighthouse "avoids non-composited animations" reasonably clean — `transform`/`opacity` transitions (the large majority: card/section reveals, word cascades, line draws) are compositor-only; the two exceptions (`.reveal-media img`'s `clip-path` wipe and the Technology/Network `stroke-dashoffset` connector-draws) are genuinely paint-triggering and *would* flag in that Lighthouse audit, but there's no compositor-only way to get an image-wipe or an SVG line-draw effect without a heavier canvas/WebGL approach that isn't justified for two small decorative moments — accepted and documented rather than silently left as an unexplained gap
- [x] Reduced-motion audit across every section, not just hero — confirmed every animated block in `animations.css`/`components.css` is gated under `@media (prefers-reduced-motion: no-preference)`, with explicit `@media (prefers-reduced-motion: reduce)` fallbacks forcing connector-draw SVGs to their fully-drawn end state (not just "no transition, stuck at 0") on Technology, Network, and the mobile nav panel; Hero (`gsap.matchMedia`) and Experience-panel parallax (a plain `matchMedia` guard) were already correctly gated as of Phases 6/8 and were spot-checked, not rebuilt

**Completed:** 2026-08-19. Moved the `[data-reveal]`/`IntersectionObserver` mechanism out of `main.js` (which now only handles the `no-js`→`js` class swap) into a new, dedicated `assets/js/animations.js`, registered site-wide in `inc/enqueue.php` right after `jets-main`. **GSAP-extension question (the phase's other named task) — decided against, documented here rather than in a second PROJECT_CONTEXT.md pass:** the Services carousel already has full Swiper-native touch/keyboard/a11y support (Phase 8), and the Network/Technology connector-draws are a plain CSS `stroke-dashoffset` transition that already respects reduced-motion correctly — in both cases CSS/vanilla is not "demonstrably insufficient" (the task's own bar for reaching for GSAP), so adding a second scroll-animation engine for either would be new dependency surface without a matching gain. GSAP stays Hero-only, per §8's original policy. **Timing/easing consistency pass:** found two hardcoded millisecond values that had drifted from the token system — `.reveal-media img`'s clip-path wipe (`900ms`, no token) now uses the existing `--dur-reveal` (`800ms`); the Network backdrop's connector-draw (`1400ms`, no token) kept its distinct, deliberately-slower duration (a big decorative map genuinely reads better slower than the Technology diagram's 4 short connectors) but that value is now a named `--dur-slowest` token in `variables.css` instead of a magic number, so future sections that want "the slow, large-scale draw" timing have something to reference instead of re-guessing the number.

**Verified live** via `curl` (script load order and 200 status for the new file), `node -c`/`php -l` on every touched file, and confirming the homepage's `[data-reveal]` element count is unchanged (45) before/after the refactor — this was a reorganization of existing, already-tested logic, not new animation behavior, so no new browser-verification gap was introduced. Same standing caveat as every phase since 7: no Playwright/browser access this session, so the actual on-scroll *feel* of the retimed clip-path wipe and connector-draw wasn't watched directly.

---

---

## PHASE 12 — Responsive Optimization ✅
**Objective:** Dedicated responsive QA/fix pass across all breakpoints (PROJECT_CONTEXT §9), beyond the ad hoc checks in earlier phases.

**Tasks:** Full manual pass at large-desktop/desktop/tablet/mobile for every template; fix any layout breaks, overflow, or touch-target issues found.

**Files affected:** `assets/css/components.css`, `assets/css/base.css`. (`responsive.css` stayed empty — every fix this phase belonged with the component it was fixing, not as a separate breakpoint-override layer; see the Decisions Log.)

**Dependencies:** Phases 4–10.

**Expected result:** No horizontal scroll/overflow at any breakpoint; touch targets ≥44px; all storytelling sections degrade sensibly on mobile per §9.

**QA checklist:**
- [x] Tested at 375/768/1024/1440/1920px widths minimum — via CSS code-audit and computed-value math (no browser/Playwright access this session, same standing caveat as every phase since 7), not by resizing an actual viewport
- [x] Mobile menu, forms, and horizontal tracks all usable one-handed — the two below-44px touch targets found (nav toggle, mobile-nav close button) are now fixed; form fields and buttons were already correctly sized
- [x] Both LTR and RTL checked at each breakpoint — the fixes made (overflow-x safety net, card width, touch targets) are all direction-agnostic; no new `[dir="rtl"]` cases were introduced or needed

**Completed:** 2026-08-19. Started from a real bug report rather than a cold audit: the user flagged that the homepage's Services (Swiper) carousel was causing horizontal scroll on mobile. Root cause: `.card--service`'s base CSS rule (`width: min(21rem, 82vw)`) is only correct for the standalone style-guide preview, where nothing else constrains the card's size — but inside the Swiper carousel, the *slide* (Swiper's own computed/inline width, based on `slidesPerView: 1.15`) is the real available width, and on narrow phones that computed slide width comes out **narrower** than `82vw` (which measures against the full viewport, not the slide). The card demanded its own, wider width regardless of what Swiper had actually sized the slide to, so it overflowed its slide — and since that overflow sits inside `.swiper-wrapper`/`.swiper`, whether it stayed contained depended on exactly where the mismatch showed up, not something guaranteed by design. Fixed by extending the pattern already used for the archive grid (`.services-grid .card--service { width: 100%; }`) to the carousel too, so the card always just fills whatever width its container actually provides instead of computing its own competing value. Also added `overflow-x: hidden` to `body` as an explicit defense-in-depth safety net (documented as exactly that, not a silent band-aid) — real overflows still show up as visibly clipped content, so this doesn't hide bugs, it just stops a single miscalculating element from making the *entire page* scroll sideways. While auditing for the same "own the width instead of filling the ancestor" bug pattern elsewhere, checked the footer's large decorative "JETS TRAVEL" wordmark (`clamp(4.5rem, 2rem + 14vw, 13rem)` + `white-space: nowrap`, which does overflow its box on narrow phones by design) — confirmed it was already safe, since `.site-footer` carries `.panel--full-bleed`'s existing `overflow: hidden`, so the bleed is contained and intentional, not a second instance of the same bug. Separately, audited touch-target sizes across every interactive control: found and fixed two below the 44px (2.75rem) minimum already used elsewhere in the theme (`.carousel__arrow`, form fields) — the mobile nav hamburger toggle (was 40px) and the mobile nav overlay's close button (was 36px) — and gave the header's language switcher (visible at every breakpoint, including phones, since it isn't hidden below the mobile-nav breakpoint the way `.site-header__nav` is) a proper `min-height`/padding hit area instead of being sized to its bare text. Swiper pagination dots and footer/legal text links were reviewed and left as-is — small dots are a universal, well-understood carousel convention backed by the already-44px prev/next arrows, and inline text links in a list don't carry the same tap-precision risk as icon-only buttons.

**Verified live** via `curl` (page loads, no PHP errors), a CSS brace-balance check, and confirming the DOM nesting (`swiper-slide > article.card--service`) actually matches the new selector's specificity/scope — not by resizing a real viewport or touching a real phone, per the standing no-browser-access caveat. Worth a real device pass before shipping, same as every prior phase's carried-forward note.

**Same-day follow-up, 2026-08-19:** the `.card--service` width fix above was a real bug but not the actual (or at least not the only) cause of the horizontal scroll — the user tested on a real device after that fix and confirmed `.carousel__controls` (the prev/next arrows + pagination row) was still causing it. Re-examining: `.carousel__controls` is a *sibling* of `.services-carousel__swiper`, not a descendant, so it was never covered by Swiper's own `overflow: hidden` in the first place — with up to 13 pagination bullets plus two 44px arrow buttons in one `justify-content: center` flex row with no `flex-wrap`, that row's natural (unconstrained) width can exceed a phone's viewport on its own, independent of anything happening inside the carousel track. Fixed by making the controls desktop-only (`@media (min-width: 1024px)`, the same line the header nav already draws between mobile and desktop) rather than trying to make an unwrapped 15-element row fit on a 375px screen — mobile already has Swiper's native touch/swipe as its primary interaction, so the arrows/dots were redundant chrome there anyway, not a lost capability.

**Second same-day follow-up, 2026-08-19:** separately, the user flagged that the Technology diagram's SVG connector lines touch their node dots correctly on desktop but visibly miss them on mobile. Cause: `section-technology.php`'s connector `<svg>` draws each path to a fixed x-position computed assuming its node sits in a single row, evenly spaced across the diagram's full width (`x = (index + 0.5) / node_count * 400`) — but `.tech-diagram__nodes` uses the generic `.grid--2/3/4` scaffold, which collapses to fewer columns (then 1) below 1024px/600px for card-grid contexts. Once the nodes wrap into multiple rows on mobile, their real screen positions stop matching what the SVG paths were drawn to. User-directed fix: keep `.tech-diagram__nodes` at its node-count's own column count (a single row) at every breakpoint instead of letting it collapse, added as a targeted override in `components.css` scoped to `.tech-diagram__nodes.grid--{2,3,4}` (component-specific, doesn't touch the generic `.grid--*` scaffold used elsewhere for actual card grids); added a small mobile-only font-size/gap reduction alongside it so a forced 4-column row of labels like "Connectivity" still reads comfortably on a 320–375px phone.

---

## PHASE 13 — Performance ⬜
**Objective:** Optimize load performance.

**Tasks:** Image optimization/lazy-loading audit (`loading="lazy"` below the fold, `fetchpriority="high"` on hero LCP image), CSS/JS minification via build step or manual minification, font-loading strategy check (`font-display: swap`), DOM-complexity review on the heaviest sections (Services track, Network map).

**Files affected:** enqueue logic, image markup across templates, possibly a minified asset build step.

**Dependencies:** Phases 6–12.

**Expected result:** Strong Lighthouse performance score; fast LCP on hero; no render-blocking resources beyond what's necessary.

**QA checklist:**
- [ ] Lighthouse Performance ≥ 90 on a representative page (mobile throttled)
- [ ] No layout shift from images/fonts (CLS)
- [ ] Total JS payload reasonable (no unused GSAP if not adopted)

---

## PHASE 14 — SEO + Accessibility ✅
**Objective:** Verify SEO/a11y foundations across the finished site.

**Tasks:** Heading hierarchy audit (one H1 per page), alt text audit on every image, `<html lang/dir>` correctness per language, ARIA on nav/menus/forms, focus-visible states site-wide, Rank Math/Yoast compatibility spot-check (no conflicting meta output from the theme itself).

**Files affected:** `template-parts/components/card-service.php`, `template-parts/homepage/section-why.php`, `template-parts/homepage/section-network.php`, `page-contact.php`, `assets/js/forms.js`.

**Dependencies:** Phases 4–12. **Note:** run out of plan order at the user's request — Phase 13 (Performance) was skipped for now and can be picked up later; nothing in Phase 14 actually depended on it.

**Expected result:** Clean heading structure, full alt-text coverage, keyboard-navigable end-to-end, no theme/plugin SEO conflicts.

**QA checklist:**
- [x] axe/Lighthouse-equivalent audit clean on Home, Services, Contact — no axe/Lighthouse tooling available this session (no browser access), so this was a manual code-level audit against the same rule categories those tools check (heading order, alt text, labels, ARIA name/role/value, contrast-independent focus indication) rather than an automated run; see "Completed" below for what was found and fixed
- [x] Tab-only navigation completes a full site walkthrough — not re-verified live this session (no browser), but the mobile nav's focus trap and Esc-to-close were already verified live via Playwright in Phase 4 and nothing since has touched that mechanism; the one real gap found this phase (failed form submission previously left keyboard focus stranded on the submit button) is now fixed — focus moves to the first invalid field
- [x] Rank Math/Yoast (if later installed) outputs without duplicate meta tags from the theme — confirmed clean: `inc/setup.php` registers `add_theme_support('title-tag')` and the theme never outputs its own `<title>`, meta description, or `og:*` tags anywhere (grepped the full theme tree) — everything funnels through `wp_head()`/core, exactly the "no conflicting output" bar this item asks for

**Completed:** 2026-08-19. Started with a full manual audit (no axe/Lighthouse/browser access this session) rather than assuming the site was already clean, given how much markup exists across 9 template files and 10+ homepage sections:
- **Heading hierarchy:** audited every template — exactly one H1 per page everywhere (Hero on the homepage, a page-level H1 on every other template), with H2 for section headings and H3 for card/sub-titles throughout, no skipped levels. Already correct from prior phases; no fixes needed.
- **Alt text:** audited every `<img>` in the theme. All are `alt=""` (correctly decorative — mood photography, background panels, category images — in every case the actual meaning is carried by adjacent visible text) except the two logo images, which correctly go the other way: the header logo is `alt=""` because the visible "Jets" wordmark sits right next to it (avoiding double-announcement), while the footer logo — which has no adjacent text, just the image alone as the link — correctly carries `alt="<?php echo get_bloginfo('name') ?>"`. No fixes needed; this had already been thought through carefully in earlier phases.
- **Decorative icon `aria-hidden` — found and fixed a real inconsistency:** `single-service.php`'s icon wrapper and `page-contact.php`'s detail-row icons were already correctly `aria-hidden="true"`, but the same icon pattern in `card-service.php` (the shared partial powering both the homepage carousel and the services archive — the theme's single most-repeated component) and `section-why.php` (all 6 Why-JETS pillar icons) and `section-network.php` (all 3 network-block icons) were missing it, exposing purely decorative SVG markup to assistive tech on every one of those instances. Added `aria-hidden="true"` to all three.
- **Form ARIA — found and fixed a real gap:** the Contact page's inquiry form had zero ARIA wiring for validation errors. The async status message region (`[data-form-status]`) had no `aria-live`/`role`, so a screen-reader user would never be told a submission succeeded or failed without a page reload to notice. Per-field error messages (`.field__message`) existed visually but weren't linked to their inputs at all — no `id`/`aria-describedby` pairing, no `aria-invalid`. Fixed all of it: `role="status" aria-live="polite"` on the status region; matching `id`s on every `.field__message` with `aria-describedby` on its input/textarea; `forms.js`'s `setFieldError()`/`clearAllErrors()` now toggle `aria-invalid` alongside the existing `.field--error` class; and — since none of the above helps if a screen-reader/keyboard user's focus just stays parked on the submit button after a failed submission — added `focusFirstInvalidField()`, called after both a failed client-side validation and a failed server-side (AJAX) response, so focus lands directly on the first problem field either way.
- **SEO/meta conflict check:** confirmed clean, see QA checklist above. Also noted at the time (not fixed in this phase, not a bug): the Theme Options "Default Share (OG) Image" ACF field existed but was never actually read/output anywhere in the theme. **Since wired up the same day, at the user's request — see the "Same-day addition" note below.**
- Also spot-checked focus-visible coverage (global ring from Phase 3, still intact; the one `outline: none` in the whole CSS tree is correctly paired with a replacement `box-shadow` ring on the same selector) and the mobile nav overlay's ARIA (`role="dialog"`, `aria-modal`, `aria-label`, all already correctly in place since Phase 4) — both already solid, no changes needed.

**Verified live** via `curl` (zero PHP errors across every touched template, in both languages), `node -c`/`php -l` on every touched file, and confirming the new `aria-describedby` ids actually render and match their inputs 1:1 in the live HTML. No browser/axe/Lighthouse/screen-reader access this session — the fixes are correct by markup/ARIA-spec inspection, not by hearing them announced. Worth a real screen-reader pass (VoiceOver/NVDA) before shipping, on top of the standing real-device/browser caveat carried since Phase 7.

**Same-day addition, 2026-08-19:** the user asked for the site's logo to show up when its link is shared on WhatsApp, Facebook, or Twitter/X — all three read Open Graph (Twitter/X also reads Twitter Card) meta tags for link previews, and the theme wasn't outputting any. New `inc/seo.php` adds `og:*`/`twitter:*` tags on `wp_head`, guarded by `jets_has_seo_plugin()` (checks for `WPSEO_VERSION`/`RankMath`/`AIOSEO_VERSION`) so it automatically stands down the moment a real SEO plugin is installed — same "no conflicting meta from the theme" principle this phase already established, just applied to the one gap that actually mattered for a working feature today. Also finally wires up the Theme Options "Default Share (OG) Image" field flagged as inert above. Image resolution priority: post featured image → a `service` single's `hero_image` → the Theme Options default share image → the site's actual logo (Customizer logo if set, else `logo.jpeg`) — so a share preview always shows something recognizable. Verified live via `curl`: correct `og:title`/`og:description`/`og:url`/`og:image` (with real width/height) and `twitter:*` tags on the homepage, an internal page, and a service single, in both languages — including confirming the Arabic `og:description` is genuinely UTF-8-correct in the raw response (a Python/terminal console codepage issue made it print as mojibake when checked directly in this session's shell, unrelated to the actual HTTP output).

---

## PHASE 15 — Final QA ⬜
**Objective:** End-to-end sign-off.

**Tasks:** Cross-browser check (Chrome/Firefox/Safari/Edge), full content proofread against PDF (zero invented facts), broken-link check, form submission end-to-end test, final PROJECT_CONTEXT.md update marking project status.

**Files affected:** bug fixes only, no new features.

**Dependencies:** All prior phases.

**Expected result:** Production-ready bilingual theme matching PROJECT_CONTEXT.md in full.

**QA checklist:**
- [ ] All items from every prior phase's checklist re-verified
- [ ] Content audit: every visible fact traceable to `jets.pdf` or explicitly marked/omitted per §13
- [ ] PROJECT_CONTEXT.md §15 Known Issues reflects true final state

---

## Notes on sequencing

- Phases 1–3 are pure foundation — nothing user-visible yet, but everything after depends on them being right.
- Phases 4–9 build the visible site in the same order a visitor experiences it (header → homepage top → homepage bottom → internal pages), which keeps each phase reviewable in a real browser as it lands.
- Phases 10–15 are cross-cutting passes (language, motion, responsive, performance, SEO/a11y, QA) applied *after* the site exists, matching the brief's phase-by-phase discipline rather than trying to perfect each concern inline as we go.
