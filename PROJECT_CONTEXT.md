# PROJECT_CONTEXT.md — JETS Travel & Tourism WordPress Theme

> Source of truth for this project. Update whenever a major architectural or design decision is made. Read this file before starting any new phase.

**Last updated:** 2026-08-19 — Phase 14 (SEO + Accessibility) complete.

---

## 0. STATUS

- **Completed phases:** Phase 0 (Project Context & Plan), Phase 1 (Project Foundation), Phase 2 (WordPress Theme Architecture), Phase 3 (Global Design System — layout.css/components.css), Phase 4 (Header + Navigation), Phase 5 (Homepage Storytelling Structure — scaffold), Phase 6 (Hero Section — final, scrollytelling), Phase 7 (Homepage Content Sections — Philosophy, About, Why JETS), Phase 8 (Services / Solutions / Experience Sections), Phase 9 (Internal Pages + Homepage close), Phase 10 (Arabic + RTL), Phase 11 (Animations + Interactions polish pass), Phase 12 (Responsive Optimization), Phase 14 (SEO + Accessibility)
- **Current phase:** Ready to begin Phase 15 (Final QA). **Phase 13 (Performance) was deliberately skipped for now at the user's request** — Phase 14 didn't depend on it, but it's still open and worth returning to before shipping.
- **Blocking questions for the user:** see [§13 Open Questions](#13-open-questions--content-required-log)
- **Do not restart the project.** Build on top of the existing theme skeleton.

**Phase 1 summary:** Theme skeleton created and activated in wp-admin (confirmed working, no errors). Files in place: `style.css`, `functions.php`, `inc/setup.php`, `inc/enqueue.php`, `header.php`, `footer.php`, `index.php`, `assets/css/{variables,base,layout,components,animations,responsive}.css`, `assets/js/main.js`, six self-hosted variable/static woff2 font files in `assets/fonts/` (Plus Jakarta Sans, Inter, Cairo, Tajawal — downloaded from Google Fonts and self-hosted, not CDN-loaded). Verified live: correct stylesheet cascade order with filemtime cache-busting, deferred script loading, all fonts return HTTP 200, no PHP notices/warnings in output.

**Phase 2 summary:** ACF (free) and Polylang (free) downloaded into `wp-content/plugins/` and activated by the user in wp-admin. `service` and `testimonial` CPTs registered (`inc/custom-post-types.php`), ACF field groups for `service`/Contact/Theme-Options registered as local PHP (`inc/custom-fields.php` — version-controlled with the theme, not edited via the ACF UI), Polylang string-translation scaffolding and a graceful-degradation language-switcher helper added (`inc/i18n.php`), and shared template helpers added (`inc/helpers.php`, including `jets_dev_image()` — the single placeholder-image registry from §12, currently only mapping `hero` to `placeholder.jpg`). 13 `service` posts seeded with real PDF copy (see Decisions Log). All verified live via the REST API and a read-only ACF check.

**Phase 3 summary:** `layout.css` (container/section-padding utilities, 2/3-col + staggered grid scaffolds, full-bleed panel pattern) and `components.css` (buttons, badge, form field, card base, process-step node, section-heading pattern) built out. An internal `/style-guide/` preview page (`page-styleguide.php`, page ID 20, not linked in nav) demonstrates every component. During QA, found and fixed a token violation: `layout.css`'s panel-scrim gradients hardcoded `rgba(14, 42, 48, …)` instead of deriving from a token — added a `--color-secondary-rgb` channel variable to `variables.css` and switched the scrim gradients and `--shadow-*` tokens to use it. Verified live via Playwright screenshots: LTR render, a manually-forced RTL flip (`dir="rtl"`/`lang="ar"`) confirming card order/icons/timeline numbering/form alignment/dark-panel content all mirror correctly, and a visible `:focus-visible` ring on interactive elements.

**Phase 4 summary:** Header built out with `template-parts/header/{nav-desktop,nav-mobile,language-switcher}.php` and `assets/js/navigation.js`. Transparent-over-hero → solid-on-scroll behavior (`.site-header--transparent`/`--scrolled`, applied only on `is_front_page()` since interior pages have no hero to sit transparently over — see header.php's own doc comment); note the transparent state's light-on-dark text is only meaningfully visible once Phase 6's hero exists behind it, which is expected, not a bug, on today's hero-less front page. Mobile gets a full-screen slide-in overlay panel (right in LTR, left in RTL) with a focus-trapped, Esc-closable menu and the language switcher pinned above it. Language switcher (`jets_get_language_switcher_items()`, already scaffolded in Phase 2) renders nothing while Polylang has fewer than 2 languages configured, rather than a useless single-item switcher — full EN⇄AR verification deferred to Phase 10. Verified live via Playwright using a temporary WP-CLI-created nav menu (deleted after testing, so no placeholder nav items are live) — scroll-triggered solid transition, hover underline, focus-trap tab-cycling, Esc-to-close, and RTL panel-direction/text-alignment mirroring all confirmed.

**Phase 5 summary:** `front-page.php` assembles the 10 `template-parts/homepage/section-*.php` files in narrative order. Six sections (Hero, Philosophy, About, Why JETS, Network, Final CTA) pull from a new "Homepage Content" ACF options page (`inc/custom-fields.php`'s `jets_register_homepage_content_fields()`, read via a new `jets_home_option()` helper); Services and Delivery pull directly from the `service` CPT; Experience-categories and Technology reuse the existing "Tours & Excursions" / "Digital Travel Solutions" `service` posts' own fields (`jets_get_service_by_slug()`) rather than duplicating that data in options. All copy for the newly-needed sections (Philosophy's supporting paragraph, all 6 Why-JETS pillar descriptions, Partnerships/Our-Network/Clients-&-Partners text, and the Final CTA copy) was sourced by rendering `jets.pdf`'s page images directly (via Ghostscript — the PDF has no extractable text layer) and reading the exact wording, since it wasn't yet captured verbatim in this document. Seeded via a one-time CLI script (same pattern as Phase 2). Verified live via Playwright: full-page screenshot confirming correct section order and alternating light/dark/alt rhythm, a forced RTL flip (logical-property `.split`/grid layouts mirror correctly with no manual overrides needed), and no mobile horizontal overflow.

**Phase 6 summary:** The hero (`section-hero.php`) is now a pinned, scroll-scrubbed 5-stage sequence on desktop/tablet (`assets/js/hero-scrollytelling.js`, GSAP 3.15 + ScrollTrigger self-hosted in `assets/js/vendor/` — the theme's one deliberate GSAP dependency per §8). `gsap.matchMedia()` gates it to `(min-width: 1024px) and (prefers-reduced-motion: no-preference)` and cleanly reverts if that stops matching live, not just at page load. All hidden pre-scrub states are established in plain CSS (not `gsap.set()`), so a slow/blocked JS load never flashes visible-then-hidden and a no-JS visitor still gets a correct static hero. Mobile and reduced-motion both get the calmer non-pinned fallback originally speced, confirmed via `gsap.matchMedia()` never registering for those cases. **Bug found and fixed:** the Phase 4 header's solid-on-scroll logic used a flat `scrollY > 40px` threshold, which flipped it solid mid-pin (GSAP's pin-spacer inflates `scrollY` well before the hero visually finishes) — `navigation.js` now checks `#hero`'s bounding-rect bottom instead when the header is in its transparent (front-page) state, leaving interior pages' simple-threshold behavior untouched. **Known caveat for Phase 10:** testing the per-word stagger reveal under a forced `dir="rtl"` with the existing *English* copy visibly reordered the words (a browser bidi quirk for LTR text forced into an RTL wrapper) — must be re-verified against real Arabic `hero_heading` content once Phase 10 adds it, not assumed safe from the English-forced-RTL test.

**Phase 6 redesign (same day, 2026-08-17):** the user rejected the first pass's *visual design* on sight (functionality was fine) — centered-text-over-photo with a generic mountain-lake image and a stock-style Earth render read as templated, not premium. Ran a full art-direction pass (creative-director framing: understand → 3 directions → choose → redesign → self-critique) and landed on **"Flight Path"**: an asymmetric, left-anchored editorial composition (not centered) inside an inset hairline frame, replacing both images with curated, color-matched aviation photography — a wing-at-sunset shot (ascent) cross-fading to a runway-at-dusk shot (the path opening forward, literally mirroring "Opening the World to You") — both sourced from Unsplash and given a consistent cool-shadow grade so they read as one visual world rather than two unrelated stock photos. Added a scroll-progress rail as the composition's signature element (a genuine progress indicator, not decoration) and a new `hero_eyebrow` ACF field carrying real, previously-unused PDF copy ("Your Trusted Partner in Travel, Tourism & Destination Management" — the second half of page 1's tagline). Removed the original swoosh-line SVG entirely once the photography itself carried that motif better — restraint over decoration. Assets: `assets/images/hero-arrival.jpg` (wing/sunset) and `assets/images/hero-runway.jpg` (renamed from `hero-world.jpg` once it stopped being an Earth graphic), both sourced/cropped/graded via Pillow, registered in `jets_dev_image()`. **Bug found and fixed:** GSAP's `yPercent` cannot reliably infer a starting value from a plain CSS percentage `transform` it never set itself — a word-mask reveal technique (`translateY(115%)` in CSS, animated via `yPercent` in JS) silently no-op'd because of this; simplified to the same proven opacity+pixel-offset technique used elsewhere rather than fighting the framework. Verified live via Playwright across all scroll stages, mobile, reduced-motion, and RTL.

**Phase 7 summary (2026-08-18):** Philosophy, About, and Why JETS got their final interaction pass, built on one small reusable mechanism instead of bespoke per-section JS: any `[data-reveal]` element gets `.is-visible` added once by a single `IntersectionObserver` in `main.js` (vanilla — GSAP stays Hero-only per §8); the actual animation (fade, line-draw, clip-path wipe, word cascade) lives entirely in `animations.css`, staggered via a `--reveal-i` custom property, all gated under `prefers-reduced-motion: no-preference`. Philosophy's statement now cascades word-by-word (same `explode()`-into-spans technique as the Hero's `.hero__word`) behind a new animated connecting-line motif; About's image does a real `clip-path` wipe with copy settling in after, plus a new `.split--connected` vertical line carrying that same connecting-line motif into §3 without repeating it identically; Why JETS' 6 pillar cards cascade in on a stagger with a small icon draw-in layered on the existing hover accent-bar. **Scope note:** the storyboard's literal "Philosophy panel lightens as the next section's imagery fades in underneath" cross-fade (coordinated *between* two sections) was skipped as disproportionate to build without scroll-linked JS spanning both — the existing dark→white section adjacency already reads as a clean handoff. **Same-day, ahead of schedule:** at user request, the same `[data-reveal]` mechanism was also extended to the remaining homepage sections' scroll-reveal (Services, Experience categories, How We Deliver, Technology, Network, Final CTA) — this covers only their *reveal* polish, not the rest of Phase 8/9's scope (horizontal-track interactions, parallax panels, tabbed timeline, archive/single templates, Contact form, inquiries system). Also same-day, the footer was fully rebuilt (full-bleed photo panel, nav/logo/tagline/contact/social columns, copyright/credit/legal row) — out of sequence relative to its informal "Phase 9" placement; see the Decisions Log and IMPLEMENTATION_PLAN.md's "Out-of-sequence: Footer rebuild" note for the architecture change (hardcoded content, no ACF Theme Options page) and the placeholder values still pending real data. **Not verified this session:** no browser/Playwright access was available, so all of the above was confirmed via `curl` against the live site (correct markup, HTTP 200, no PHP warnings) and code-level reasoning rather than an actual visual/scroll/RTL check — carry a live verification pass into Phase 10's RTL audit or before shipping.

**Interim fix (2026-08-18, between Phase 7 and 8): `value_badges`/`process_steps` weren't actually working.** The user discovered these two `service` CPT fields showed as non-functional in wp-admin — they're ACF Repeater fields, and this site only has free ACF (not PRO), which doesn't include the Repeater admin UI (it can still *store/display* repeater data written directly via `update_field()`, which is why already-seeded content like the Why JETS pillars kept rendering — it just couldn't be edited in wp-admin). Converted both fields from `repeater` to plain `textarea` (`inc/custom-fields.php`) — one badge/step per line, steps as `Title` or `Title | Description` — and updated `jets_get_service_meta()` (`inc/helpers.php`) to parse the new format into the exact same array shape the old repeater code produced, so no template changed. While investigating, found the underlying badge/step *data* was empty in the live DB for all 13 services despite Phase 2's notes claiming it was seeded — re-seeded it from `jets.pdf` pages 6–14 via a one-time webroot script (same pattern as Phase 2/5, deleted immediately after running). See the Decisions Log for the field-type change and the "5 title-only services stay empty" rule.

**Phase 9 summary (2026-08-19):** Built the three remaining static pages and finished the homepage's last three sections. `inc/inquiries.php` + `inc/class-jets-inquiries-list-table.php` implement the `jets_inquiries` table (created via `dbDelta()` on `after_switch_theme`, with an `admin_init` safety net since this theme was already active when the file was added) and a `manage_options`-only admin "Inquiries" page using a `WP_List_Table` subclass (sortable/paginated, per-row delete with nonce). The AJAX handler (`wp_ajax(_nopriv)_jets_submit_inquiry`) validates server-side independently of the client, honeypot-drops bot submissions with a fake success response, and always tries `wp_mail()` after a successful DB insert — storage and email are independent, matching the Decisions Log. `page-about.php`/`page-partners.php` deliberately reuse the existing homepage template parts (`section-about`+`section-why`; `section-network`) rather than duplicating their content source, since §5's IA for both pages is essentially "the same content, on its own page." `page-contact.php` is new: conditionally-rendered contact details (all currently empty per §13, so nothing fabricated shows) plus a working inquiry form (`assets/js/forms.js`, vanilla — reuses the Phase 3 style guide's `.field`/`.form__status` classes verbatim). Two new ACF fields — `mission_text`/`vision_text` — were added to the existing Homepage Content options page (About tab) rather than a new screen, seeded with the exact wording already captured in §1. The site-wide **WhatsApp button** (one of exactly two CTA mechanisms per §4, its placement explicitly deferred to "Phase 4/9") was finally added — header, mobile overlay, footer, and Contact page — reading a single new `whatsapp_number` Theme Options field via `jets_get_whatsapp_url()`; left unset by default (renders nothing) rather than seeded with a DUMMY value like the footer's earlier phone/email precedent, since a fake WhatsApp number would be clickable/functional-looking in a way a placeholder footer phone number isn't. Homepage close: Technology (§8) got a device→pillar SVG connector diagram that draws in via `stroke-dasharray`/`stroke-dashoffset` (the `pathLength="1"` trick, node positions computed from the actual badge count); Network's (§9) existing decorative backdrop SVG got the same connector-draw treatment instead of just fading in as a static image; Final CTA's (§10) headline switched from a plain fade to the same word-cascade (`.reveal-words`/`.reveal-word`) technique Philosophy's brand statement already uses, so the two dark full-bleed "emotional bookend" panels share the same text-reveal language. **Also fixed, out of the plan's own file list:** neither the `primary` nor `footer` nav menu location had a real menu assigned yet (Phase 4 shipped with none by design; the footer's had been a documented empty known-issue since Phase 7/8) — the one-time seed script created both (Home/About/Services/Partners & Network/Contact) so the site is actually navigable end-to-end, not just reachable by direct URL. **Not verified this session:** no browser/Playwright access — everything was confirmed via `curl`, direct `admin-ajax.php` calls (including a real end-to-end submission, a validation-error case, a honeypot case, and a bad-nonce case, each checked against expected JSON/HTTP responses), a direct one-time DB read (same seed-script-then-delete pattern as every prior phase), and `php -l` syntax-linting all 16 touched files. The `WP_List_Table` admin screen itself and the three scroll-triggered reveal animations (tech-diagram/network connector-draw, CTA word-cascade) were verified by code/markup review, not by actually looking at them in wp-admin or watching them animate — carry into Phase 10/12 alongside the other browser-verification items already flagged there.

**Phase 8 summary (2026-08-18):** Two scope calls were checked with the user before building rather than assumed: kept the Delivery section's existing arrow-chain timeline instead of rebuilding it as the originally-planned tabbed/accordion (it was already a considered decision from earlier work, not a placeholder); the Services carousel turned out to already be fully built (Swiper, with keyboard/a11y already enabled) — nothing left to do there. What was actually built: a shared `template-parts/components/card-service.php` partial (extracted from the previously-inlined carousel markup) now powers both the homepage carousel and the new `archive-service.php` grid; `single-service.php` (new) is a full-bleed dark hero (icon/title/short-description/badges) + `the_content()` body (needed its own "prose" CSS restoring margins/list-styles, since `base.css` zeroes those globally and every other section uses controlled ACF fields instead of the post editor) + the same `.delivery-chain` process-step treatment, gracefully omitted when a service has none; Experience categories (§6) went from plain badge pills to 5 full-bleed image panels with a subtle desktop-only parallax drift (`assets/js/experience-parallax.js`, vanilla rAF scroll listener — not a GSAP exception, just a plain listener), using 5 newly-curated Unsplash photos chosen to read as one moody/dusk-toned set (two initial candidates were rejected first: an over-saturated crowded Colosseum shot, and a cruise-ship photo with a competitor's brand name legible on the hull). **Not verified this session:** same caveat as Phase 7 — no browser access, confirmed via `curl` (markup, HTTP 200, pagination behavior, graceful-empty-state behavior) rather than a live visual/keyboard/parallax check.

**Phase 10 summary (2026-08-19):** Made Arabic a fully first-class, navigable language rather than a mirrored afterthought. **Discovery that reshaped the whole phase:** `jets.pdf` turned out to be genuinely bilingual on every one of its 19 pages — real Arabic marketing copy runs alongside the English throughout, not just an English deck as earlier phases' Ghostscript-extraction notes implied. All Arabic content this phase is transcribed directly from those pages (rendered with PyMuPDF and read visually), satisfying the plan's own "not machine-translated" bar for real. Went into this phase discovering Polylang, despite being active since Phase 2, had never actually been configured (`pll_languages_list()` was empty, no post had a language) — the user added English (default) and Arabic (RTL) via wp-admin and bulk-tagged existing content as English before the scripted work began. Built: Arabic translations of all 13 `service` posts and the About/Partners/Contact pages, linked via Polylang's own translation-group APIs; 67 UI-chrome string translations via Polylang's `PLL_MO` class; a second ACF options page (`options_ar`) since free ACF has no native Polylang support for Options Pages — `jets_home_option()` now branches on `pll_current_language()` (see §14, and the deferred-decision note this closes out); Arabic primary/footer nav menus wired through Polylang's actual per-language `nav_menus` option (not the theme_mod directly — that only ever holds the current language's flat map on the frontend). Two real, pre-existing bugs were found and fixed while auditing `footer.php` for English leaks: the copyright line's "All rights reserved." used a raw `esc_html_e()` call that bypassed Polylang string translation entirely, and the developer-credit line called `jets_translate( 'Made by %s' )` while `inc/i18n.php` had registered a differently-worded string (`'Site by %s'`) — a silent name mismatch. Verified live via `curl` and a UTF-8-safe file-diff check (no browser/Playwright access this session, same caveat as Phases 7–9): zero PHP errors across every EN/AR URL tested, `<html dir/lang>` correct on both, the language switcher correctly bidirectional on every page pair, and — closing out the Phase 6/7 known issues — the Hero heading and Philosophy/CTA word-cascades confirmed rendering genuine Arabic text correctly through the existing per-word stagger spans.

**Phase 11 summary (2026-08-19):** A small, deliberately narrow polish pass rather than a rebuild — the animation system was already in good shape from Phases 6–9. Moved the `[data-reveal]`/`IntersectionObserver` scroll-reveal mechanism out of `main.js` into a new, dedicated `assets/js/animations.js` (site-wide enqueue, right after `jets-main`); `main.js` now only handles the `no-js`→`js` class swap. Evaluated extending GSAP beyond the Hero to the Services carousel and the Network/Technology connector-draws (the phase's other named task) and decided against it — Swiper already has full native touch/keyboard/a11y support, and the connector-draws are a plain, already reduced-motion-correct CSS `stroke-dashoffset` transition; neither is "demonstrably insufficient" (the task's own bar), so GSAP stays Hero-only per §8's original policy (see §14). Found and fixed two stray hardcoded millisecond values that had drifted from the `--dur-*` token system in `variables.css`: `.reveal-media img`'s clip-path wipe (was `900ms`) now uses the existing `--dur-reveal` (800ms); the Network backdrop's connector-draw (`1400ms`) kept its deliberately slower duration but is now a named `--dur-slowest` token instead of a magic number. Audited reduced-motion coverage across every animated section (not just Hero) and confirmed it was already thorough — every transition is gated under `@media (prefers-reduced-motion: no-preference)` with explicit `reduce` fallbacks forcing connector-draw SVGs to their fully-drawn end state. **Verified live** via `curl` (script load order, HTTP 200 for the new file), `node -c`/`php -l` on every touched file, and confirming the homepage's `[data-reveal]` element count was unchanged (45) before/after — this was a reorganization of already-tested logic, not new animation behavior, so it didn't introduce a new browser-verification gap. Same standing caveat as every phase since 7: no Playwright/browser access this session, so the retimed clip-path wipe and connector-draw weren't watched animating directly.

**Phase 12 summary (2026-08-19):** Triggered by a real bug report rather than a cold audit — the user flagged that the homepage's Services (Swiper) carousel caused horizontal scroll on mobile. Root cause: `.card--service`'s base width (`min(21rem, 82vw)`, correct only for the standalone style-guide preview) was fighting Swiper's own computed slide width instead of filling it, so on narrow phones the card could be wider than the slide Swiper actually sized. Fixed by extending the existing `.services-grid .card--service { width: 100% }` pattern to the carousel context too. Also added `overflow-x: hidden` to `body` as an explicit, documented defense-in-depth safety net, and fixed two touch targets below the theme's own 44px standard (mobile nav toggle, mobile nav overlay close button), plus gave the always-visible header language switcher a real hit area instead of bare-text sizing. See IMPLEMENTATION_PLAN.md's Phase 12 entry for the full audit trail, including the footer's oversized decorative wordmark, which was checked for the same bug pattern and confirmed already safe (contained by `.panel--full-bleed`'s existing `overflow: hidden`, not a second instance of the bug). **Not verified this session:** no browser/Playwright or real-device access, same standing caveat as every phase since 7 — fixes were reasoned from CSS computed-value math and DOM-structure checks (confirming the new selectors actually match the real markup), not by resizing an actual viewport.

**Same-day correction:** the user then tested on a real phone and confirmed the horizontal scroll was still happening — the actual (or at least primary) culprit was `.carousel__controls` (the arrow/pagination row), which sits as a *sibling* of the Swiper track, not a descendant, so it was never inside Swiper's own `overflow: hidden` to begin with; up to 13 unwrapped pagination bullets plus two 44px arrow buttons in one `justify-content: center` row can exceed a phone's viewport on their own. Fixed by making the controls desktop-only (`min-width: 1024px`, matching the header nav's existing mobile/desktop line) rather than the card-width fix above — see IMPLEMENTATION_PLAN.md's Phase 12 entry for the full follow-up note. The `.card--service` width fix stands as a separate, real fix, just not the one the user was actually hitting.

**Second same-day follow-up (Phase 12), 2026-08-19:** separately, the user flagged the Technology diagram's SVG connector lines touching their node dots on desktop but missing them on mobile. Cause: the connector `<svg>` draws each path to a fixed x-position assuming a single evenly-spaced row of nodes, but `.tech-diagram__nodes` used the generic `.grid--2/3/4` scaffold, which collapses to fewer columns below 1024px/600px — once nodes wrapped into multiple rows on mobile, their real positions stopped matching the SVG paths. User-directed fix: pinned `.tech-diagram__nodes` to its node-count's own column count (a single row) at every breakpoint via a component-scoped override, plus a small mobile-only font-size/gap reduction so a forced 4-column row of labels still reads comfortably on a narrow phone.

**Phase 14 summary (2026-08-19):** Run ahead of Phase 13 (Performance, deliberately skipped for now) at the user's request. A full manual audit — no axe/Lighthouse/browser access this session — against the same categories those tools check: heading hierarchy (already fully clean across every template — one H1 per page, correct H2/H3 nesting, no fixes needed) and alt text (already fully clean too — every image is thoughtfully either `alt=""` decorative with meaning carried by adjacent text, or carries real alt text where there's no adjacent text to lean on, like the footer logo). Two real, previously-unaudited gaps were found and fixed: decorative icon `<svg>`s in `card-service.php` (the theme's single most-repeated component — homepage carousel + services archive), `section-why.php` (6 pillar icons), and `section-network.php` (3 block icons) were missing `aria-hidden="true"`, unlike the equivalent already-correct pattern in `single-service.php`/`page-contact.php`; and the Contact form had no ARIA wiring at all for validation — no live region on the status message, no `id`/`aria-describedby` linking error text to its field, no `aria-invalid`, and no focus movement on failure, meaning a screen-reader user submitting an invalid form got no indication anything happened. Fixed all of it (`role="status" aria-live="polite"`, matching `aria-describedby` ids, `aria-invalid` toggling in `forms.js`, and a new `focusFirstInvalidField()` called after both client- and server-side validation failures). Confirmed no SEO/meta conflicts: the theme relies entirely on `add_theme_support('title-tag')` + `wp_head()`, never outputting its own title/description/OG tags anywhere — safe for a future Rank Math/Yoast install. **Not verified this session:** no browser, axe, Lighthouse, or screen-reader access — fixes are correct by ARIA-spec/markup inspection, not by actually hearing them announced; worth a real VoiceOver/NVDA pass before shipping.

**Same-day addition (2026-08-19), ahead of Phase 15:** the user asked for the logo to show up when the site's link is shared on WhatsApp/Facebook/Twitter — link previews on all three read Open Graph (and, for Twitter/X, Twitter Card) meta tags, which the theme wasn't outputting at all. New `inc/seo.php` adds `og:*`/`twitter:*` tags via `wp_head`, with `jets_has_seo_plugin()` making it stand down automatically the moment Yoast/Rank Math/AIOSEO is detected (`WPSEO_VERSION`/`RankMath`/`AIOSEO_VERSION`), so this never fights a real SEO plugin later — consistent with the "no conflicting meta from the theme" principle Phase 14 already established. This also finally wires up the Theme Options "Default Share (OG) Image" field, which Phase 14 had noted existed but was completely inert. Image resolution priority: post featured image → a `service` single's `hero_image` → the Theme Options default share image → the site's actual logo (Customizer logo if set, else `logo.jpeg`) — so a share preview always shows something recognizable, which is the literal thing that was asked for; today, since no featured images/hero images/default share image are set anywhere yet, every page currently falls all the way through to the logo, exactly matching the ask. **Verified live** via `curl`: correct `og:title`/`og:description`/`og:url`/`og:image` (with real width/height)/`twitter:*` tags render on the homepage, an internal page, and a service single, in both languages (confirmed the Arabic `og:description` is genuinely UTF-8-correct, not just terminal-garbled), with zero PHP errors.

**Same-day addition, 2026-08-19:** built real `404.php` and `search.php` templates — both previously fell back to `index.php`'s bare "Nothing found." block, the one visibly unfinished spot on an otherwise fully-designed site. New shared `template-parts/components/search-form.php` (reused by both) matches the existing `.field`/`.btn` form language exactly rather than inventing new form styling. 404 gets a friendly message plus two CTAs (home, services archive); search results reuse the plain `.card` pattern with a post-type label per result (so a `service` result reads differently from a plain page) and the same empty-state-with-fallback-CTA approach already used on the Services archive. 10 new UI strings registered in `inc/i18n.php` and translated into Arabic via the same `PLL_MO` mechanism as Phase 10 — both templates are fully bilingual from the start, not an English-only afterthought. **Verified live:** the 404 template correctly returns a real HTTP 404 status (not a 200 with "not found" text, which matters for SEO) in both languages; search correctly returns results (found the `service` "Hotel Reservations" and the `page` "Terms of Use" for a test query, each with its own type label) and the empty-state fallback for a query with no matches; zero PHP errors throughout.

**Same-day housekeeping, 2026-08-19:** the user asked to clean up the theme's file structure — six images (`favicon.ico`, `favicon-16x16.png`, `favicon-32x32.png`, `apple-touch-icon.png`, `logo.jpeg`, `placeholder.jpg`) were sitting loose at the theme root instead of under `assets/`, alongside the two files that must stay at root by WordPress convention (`style.css`, the theme header) and `jets.pdf` (a reference source doc, not a theme asset). Moved all six into the already-established `assets/images/` directory (not a new `assets/img/`, to avoid fragmenting the one images convention every other real photo in the theme already uses) and updated every reference: the four favicon `<link>` tags and the logo-mark comment in `header.php`, the footer logo `src` in `footer.php`, the `placeholder.jpg` fallback URL in `jets_dev_image()` (`inc/helpers.php`), the logo fallback URL in the new social-share-image resolver (`inc/seo.php`), and the favicon doc comment in `inc/setup.php`. Verified live: all six files serve HTTP 200 from their new `assets/images/` URLs, the old root-level URLs correctly 404 (confirming an actual move, not a copy), and every page tested (home, About, a service single, both languages) renders with zero PHP errors and the new paths in its markup.

---

## 1. COMPANY OVERVIEW

Source: `jets.pdf` (company profile, 19 pages). This is the **only** source of truth for company facts.

- **Company name:** JETS (شركة جتس)
- **Tagline (EN):** "Integrated Travel Solutions. Global Standards." / "Your Trusted Partner in Travel, Tourism & Destination Management"
- **Tagline (AR):** "حلول سفر متكاملة... بمعايير عالمية" / "شريكك الموثوق في خدمات السفر والسياحة وإدارة الوجهات"
- **Description:** JETS is a specialized travel and tourism company providing integrated solutions for individuals, businesses, and travel partners. It combines industry expertise, a strong partner network, advanced technology, and market knowledge to deliver flexible and reliable travel experiences covering accommodation, transportation, visa assistance, excursions, and VIP services.
- **Brand statement:** "We do not simply provide travel services. We build long-term partnerships based on trust, efficiency, and attention to every detail."
- **Company history / founding date:** [CONTENT REQUIRED]
- **Mission:** Deliver high-quality travel and tourism services that combine efficiency, flexibility, technology, and a personalized approach; provide competitive and reliable solutions that help clients and partners achieve their goals while maintaining the highest standards of service, quality, and professionalism.
- **Vision:** Become the preferred and most trusted travel partner for clients and partners by delivering innovative solutions, exceptional experiences, and integrated services that evolve with the global travel industry; build a strong international network and achieve sustainable growth through innovation, trust, and excellence.

### Services (Integrated Travel Services)
1. Hotel Reservations
2. Flight Bookings
3. Travel Packages & Programs
4. Ground Transportation
5. Airport Services
6. VIP Services
7. Visa Assistance
8. Tours & Excursions
9. Corporate & Group Travel
10. Events & Travel Management
11. Tailor-Made Travel Solutions

### Signature Solutions (detailed, each with its own process/steps in the PDF)
- **Corporate Travel:** Flights → Accommodation → Transportation → VIP Services → Support. Values: Speed, Precision, Flexibility, Reliable Support.
- **Group Travel & Events:** Group Travel Programs → Conferences → Meetings → Incentive Travel → Corporate Events → Special Events → Full Logistical Coordination.
- **Transportation Services:** Private Cars, VIP Vehicles, Coaches & Mini Buses, Airport Transfers, Group Transportation, Tailor-Made Transportation Solutions. Values: Comfort, Safety, Reliability, Seamless Experience.
- **Airport Services:** Arrival → Meet & Greet → Arrival & Departure Assistance → VIP Airport Services → Porter Services → Airport Coordination → Departure. Values: Smooth Arrival, Comfort, Efficiency, Reliable Support.
- **Hotel Accommodation:** Wide range of options tailored to needs/budgets, balancing Location, Quality, Value, Service. Tagline: "The Right Stay for Every Journey."
- **Tours & Excursions:** Categories — Culture, Adventure, Cruises, Luxury, Leisure. Tagline: "Experience More. Discover Further."
- **Visa Services:** Guidance → Documentation → Destination Requirements → Seamless Process. Tagline: "Clearer. Simpler. Ready to Travel."
- **Digital Travel Solutions:** Access, Booking, Connectivity, Efficiency. Tagline: "Connected Travel. Smarter Operations."

### Why JETS (6 pillars)
Specialized Expertise · Integrated Solutions · Flexibility · Strong Network · Advanced Technology · Attention to Detail

### Destinations
No named destinations, offices, or regional coverage are given in the PDF — only a stylized world-map graphic ("Markets," "Destinations," "Partners," "Service Network"). **[CONTENT REQUIRED]** — actual destination list/coverage.

### Target audience / Clients & Partners
Travel Agencies · Tour Operators · Lifestyle Management Companies · Corporations & Institutions · Individual Travelers · Luxury Travel Clients · Group & Event Organizers. Tagline: "One Partner. Multiple Travel Needs."

### Partnerships & Network
JETS builds long-term relationships with travel agencies, tour operators, corporations, service providers, and strategic partners worldwide; open to new collaboration opportunities. Taglines: "Stronger Together," "Connected Markets. Global Possibilities." No named partners are given. **[CONTENT REQUIRED]**

### Unique selling points
- Integrated, one-stop travel ecosystem (vs. single-service vendors)
- Deep specialization + strong partner network
- Flexible, tailor-made solutions
- Technology-enabled booking/operations
- Attention to detail as an explicit brand pillar

### Contact information
[CONTENT REQUIRED] — no phone, email, address, or office locations provided in the PDF.

### Social media
[CONTENT REQUIRED] — none provided.

### Statistics / numbers (years in business, clients served, destinations count, etc.)
[CONTENT REQUIRED] — none provided.

### Certifications / awards / licenses
[CONTENT REQUIRED] — none provided (contrast: the reference site prominently displays a tourism license number; JETS has none to show yet).

### Testimonials / team / case studies
[CONTENT REQUIRED] — none provided. Per the content rule, **no testimonials will be fabricated**; the theme will include a Testimonial CPT that stays empty/hidden until real content exists.

### Provided visual assets
- `logo.jpeg` — JETS logo (teal "J"/jet-swoosh mark + silver plane, wordmark "Jets"), square, white background.
- `placeholder.jpg` — generic mountain-lake travel photo (Lago di Braies style), landscape, to be used only as a temporary stand-in during development, not final brand photography.
- `jets.pdf` cover and section imagery are stock-style 3D-rendered graphics (globe, compass, world map, hotel render, hot-air balloon, passport) — moodful but generic; usable as low-priority filler, not primary hero material.

---

## 2. BRAND ANALYSIS

- **Personality:** Professional, precise, trustworthy, detail-obsessed, quietly premium. More "expert operator" than "fun adventure brand."
- **Tone of voice:** Confident, concise, service-oriented. Bilingual copy in the PDF is formal and business-appropriate in both languages, not casual.
- **Visual identity:** Teal + silver/gray mark on white; dark navy-teal used for cover/hero-style moments; clean light-gray/white for content pages. Iconography is minimal, flat/line-style, consistently paired with short value statements (3–4 word taglines per section, bilingual).
- **Image style:** Polished, corporate-travel photography and 3D renders — globes, compasses, glass hotel towers, airport/passport motifs. Not beach-holiday snapshots; leans B2B / VIP / destination-management.
- **Positioning:** JETS reads as a **travel management company (TMC) and destination-management partner** — serving corporates, agencies, and high-value individual/VIP travelers — rather than a discount leisure OTA. This closely parallels the reference site's own TMC + holidays + expertise positioning.
- **Premium vs. casual:** Premium, corporate-polished, understated — not flashy/budget, not playful.
- **Emotional qualities to evoke:** Confidence, reliability, global reach, effortlessness ("we handle every detail so you don't have to").

---

## 3. REFERENCE WEBSITE ANALYSIS — goldendestination.net

*(Inspiration only — see Reference Website Rule. No code, copy, imagery, or branding is reused.)*

- **Header/nav:** Horizontal bar, logo left, primary items (Home, Online Booking, TMC, Holidays, Expertise, More), language toggle top-right, sticky behavior implied by prominent booking CTA.
- **Hero:** No large cinematic hero — opens directly into a hashtag/intro line flowing into service cards. Prioritizes fast access to core offerings over aspirational full-bleed imagery.
- **Section flow:** Nav → intro line → featured service cards (Flights/Hotels/Car Rental) → business-verticals grid → services/portfolio carousel → newsletter CTA → destinations → expertise modules → Instagram feed → footer.
- **Color:** Deep navy/dark blue structural + text, gold/amber accent, white background, grayscale secondary text.
- **Typography:** Sans-serif, bold headers, regular body, clear hierarchy.
- **Cards:** Consistent template (thumbnail, label, headline, preview text), 3-column and masonry arrangements, rounded corners, overlay text on destination cards.
- **Distinctive UX:** Modular repeatable card sections (easy to update from CMS), WhatsApp contact link in header/footer, newsletter CTA mid-page, multilingual EN/AR with RTL, multiple paths into booking.
- **Footer:** Multi-column — license/legal info, service links by category, social links, copyright.
- **Overall positioning:** Corporate-yet-approachable TMC for the Middle East market; both leisure and business travelers; multilingual and WhatsApp-first UX signals a Gulf audience.

### What JETS will take from this vs. do differently
| Take as inspiration | Do differently |
|---|---|
| Corporate-travel + leisure duality (TMC feel) | JETS has **no card-wall homepage** — homepage is a single connected narrative (per brief), reference's homepage is card-driven |
| Bilingual EN/AR with RTL as first-class | JETS uses a cinematic full-bleed hero (reference has none) since the brief calls for strong visual storytelling |
| Modular, CMS-editable card sections for services | JETS palette is teal/navy/silver, not navy/gold — a distinct identity |
| Sticky header with smooth scroll transition | JETS will not clone the specific nav item set or card dimensions verbatim |
| Multiple entry points toward an inquiry/contact action (in lieu of reference's "Online Booking") | No Instagram feed/newsletter block cloned as-is; only used if/when the brand actually has these channels |

---

## 4. WEBSITE GOALS

- **Primary goal:** Generate qualified inquiries — position JETS as a credible, premium, integrated travel partner and drive visitors to submit a contact/inquiry request (booking/back-office systems are out of scope for v1).
- **Secondary goals:** Communicate the breadth of integrated services; build trust for both B2C (individual/VIP travelers) and B2B (corporates, agencies, tour operators) audiences; support future SEO growth for travel-related search terms.
- **Target visitors:** (1) Corporate travel buyers/admins, (2) travel agencies & tour operators evaluating a partner, (3) individual/luxury travelers, (4) event/group organizers.
- **Desired user actions:** Scroll through the story → understand the service breadth → land on a relevant service or the Contact page → submit an inquiry, or click straight through to WhatsApp.
- **Conversion points:** the site has exactly two CTA mechanisms — (1) the inquiry form (Contact page + any homepage/service CTA that opens it), which both emails the site admin *and* stores the submission in a custom DB table, reviewable via a dedicated wp-admin "Inquiries" table page (see §10); (2) a WhatsApp button (header/footer/Contact — placement finalized in Phase 4/9) that opens a `wa.me` deep link with the JETS WhatsApp number, no on-site chat widget. No other conversion path (no phone tel: link, no booking engine) is planned for v1.
- **Important content:** About/mission/vision, the 11 integrated services, the deep-dive solutions (Corporate, Group & Events, Transportation, Airport, Visa, Hotel, Tours, Digital), partnerships/network, Contact.
- **Emotional impression:** "This company has global reach and obsesses over every detail of my journey — I can hand this off with confidence."

---

## 5. INFORMATION ARCHITECTURE

Kept deliberately lean — only what the PDF content supports.

| Page | Template | Notes |
|---|---|---|
| Home | `front-page.php` | Full narrative homepage (§6) |
| About | `page-about.php` | Who we are, Mission, Vision, Why JETS |
| Services (index) | `archive-service.php` | Grid of all 11 services, links into single |
| Single Service | `single-service.php` | Reused for both simple services and the deep-dive "process" solutions (Corporate Travel, Group Travel & Events, Transportation, Airport Services, Visa Services, Hotel Accommodation, Tours & Excursions, Digital Solutions) via optional ACF process-steps field |
| Partners & Network | `page-partners.php` | Partnerships + Our Network + Clients & Partners (no card-wall — narrative treatment matching brand) |
| Contact | `page-contact.php` | Inquiry form + contact details (marked [CONTENT REQUIRED] until provided) |
| 404 | `404.php` | On-brand not-found |
| Search results | `search.php` | Minimal, on-brand |
| Generic fallback | `page.php`, `index.php`, `archive.php` | WP-required fallbacks |

**Explicitly not building now (no content/requirement to support them):** Blog/News, individual Destination pages, Team page, Testimonials page, Booking engine. Architecture stays extensible for all of these (see §10 and Future Features).

---

## 6. HOMEPAGE STORY

The homepage is one continuous narrative, not a stack of independent components. Each section is visually and emotionally tied to its neighbors (shared background bleed, consistent motion direction, connecting graphic elements/lines echoing the logo's swoosh motif).

**Narrative arc:**
`Arrival (Hero)` → `Philosophy (small details)` → `Who We Are` → `Why JETS` → `What We Offer (Services)` → `Where We Can Take You (Experience categories)` → `How We Deliver (Corporate/Group/VIP spotlight)` → `Technology` → `Network & Who We Serve` → `Final CTA`

### 1. HERO — scrollytelling "Flight Path" sequence
- **Purpose:** Immediate identity + emotional hook; establish "global, premium, detail-driven." Unlike every other section (which reveals once on scroll-into-view), the hero is a **pinned, scroll-scrubbed** mini-narrative — the user's scroll *is* the timeline, not just a trigger.
- **Art direction ("Flight Path"):** asymmetric, left-anchored editorial composition — not centered text over a full-bleed photo (the generic default the first pass shipped with and the user explicitly rejected). Two curated, color-matched aviation photographs (Unsplash, cool-shadow graded so they read as one visual world) carry the story: a wing-at-sunset shot (ascent, establishing) cross-fades to a runway-at-dusk shot (the path opening forward — literally mirroring the headline). No decorative elements that don't earn their place: the original swoosh-line SVG motif was cut once the photography itself carried that idea better.
- **Text is not part of the scroll sequence.** The eyebrow, headline, sub-headline, and CTA fade in once on page load (a plain CSS animation, staggered, unaffected by breakpoint or scroll position) and then simply stay put — the user asked for this explicitly after the first redesign pass, having found scroll-gated text less legible/predictable than a straightforward reveal. Only the **imagery and the progress rail** are scroll-scrubbed:
  1. **0–~45% of the pin:** a continuous, restrained scrubbed Ken-Burns drift on the arrival (wing/sunset) image.
  2. **~45–70%:** cross-fades to the runway image (arrival image fades to 30% opacity beneath it).
  3. **Throughout:** the progress rail fills via a direct scroll-progress binding (not staged tweens) — a genuine progress indicator, not decoration.
  4. **End of pin:** hero unpins; scrim deepens, color-bridging directly into the Philosophy section's dark panel (no hard cut) as normal scroll resumes.
- **Content:** eyebrow = "Your Trusted Partner in Travel, Tourism & Destination Management" (PDF page 1's second tagline line, previously unused anywhere in the theme). H1 = "Opening the World to You" (from PDF closing statement, repurposed as hero promise), sub = "Integrated Travel Solutions. Global Standards." All three plus the CTA labels live in the "Homepage Content" ACF options page (§10), not hardcoded.
- **Implementation:** This is the one place in the theme that adopts GSAP + ScrollTrigger (`pin` + `scrub`) rather than vanilla `IntersectionObserver` — precise multi-property scroll-scrubbing while pinning a section is exactly the case vanilla/CSS struggles with (§8). Everything outside the hero stays vanilla/CSS per §8's default.
- **Reduced motion:** `prefers-reduced-motion: reduce` disables pin + scrub entirely — the hero renders as a single static, fully-composed frame (wing/sunset image, all text visible), no scroll-jacking. Since text was already decoupled from scroll, this mainly means the imagery stops drifting/cross-fading; the text's own on-load fade only plays under `prefers-reduced-motion: no-preference` in the first place.
- **Mobile/tablet (<1024px):** no pin/scrub — the `gsap.matchMedia()` gate is `(min-width: 1024px)`, so this covers phones and tablets alike, not just phones. The image keeps its own autoplay Ken-Burns at a single viewport height; text uses the same on-load fade as desktop — scroll-jacking on touch devices is a known UX/performance risk (dynamic browser-chrome viewport height, scroll fighting the pin), so mobile/tablet get the calmer, proven pattern instead of a scaled-down pin.
- **Transition out:** Background darkens/fades into the philosophy section's solid dark-navy panel — a deliberate color-bridge, not a hard cut.
- **CTA:** "Explore Our Services" (scrolls to §5) / "Plan Your Journey" (→ Contact).

### 2. PHILOSOPHY / BRAND STATEMENT
- **Purpose:** Set emotional register before facts — this is the "why," stated once, boldly.
- **Visual:** Dark navy-teal full-width panel (matches PDF's "Small Details Create Extraordinary Journeys" page), large centered bilingual-aware typographic statement, thin animated line motif.
- **Content:** "Small details create extraordinary journeys." + supporting sentence from PDF.
- **Interaction:** Text reveals word-by-word/line-by-line on scroll-into-view; connecting line animates left→right (or right→left in RTL).
- **Transition:** Panel lightens back toward off-white as the next section's imagery (globe) fades in underneath — visual "sunrise" cue.
- **CTA:** none (pure emotional beat).

### 3. WHO WE ARE (About)
- **Purpose:** Introduce the company — expertise, network, technology, market knowledge.
- **Visual:** Two-column layout: globe/plane imagery left (or right in RTL — mirrored, not just flipped), About copy right, with a thin vertical connecting line motif carried from §2.
- **Content:** About paragraph + brand statement ("We do not simply provide travel services...").
- **Interaction:** Image reveals via clip-path wipe on scroll-into-view; paragraph fades/translates in after image settles (sequenced, not simultaneous).
- **Transition:** Layout hands off into a 3-column pillar grid (§4) via a shared background tone.
- **CTA:** none, or subtle "Learn more about JETS" → About page.

### 4. WHY JETS
- **Purpose:** Build credibility through the 6 stated pillars.
- **Visual:** 3×2 (desktop) icon-led grid, generous whitespace, thin divider lines matching the PDF's page-5 layout, on light background.
- **Content:** Specialized Expertise, Integrated Solutions, Flexibility, Strong Network, Advanced Technology, Attention to Detail (bilingual labels + one-line descriptions from PDF).
- **Interaction:** Staggered fade/translate-up on scroll, icons get a subtle draw-in.
- **Transition:** Grid narrows into a full-width services strip — visual rhythm shift from grid to horizontal flow.
- **CTA:** none.

### 5. WHAT WE OFFER (Services)
- **Purpose:** Communicate the full breadth of the integrated service ecosystem.
- **Visual:** Not a flat card wall — a horizontally-scrollable / staggered showcase of the 11 services with icon + label, large section heading "One partner. Every part of the journey." Alternate large "feature" tiles (Corporate Travel, Group Travel, VIP) with smaller icon-only tiles for the rest.
- **Content:** 11 services pulled live from the `service` CPT.
- **Interaction:** Horizontal scroll-snap track on desktop (mouse-wheel/drag), stacks vertically on mobile; hover/tap reveals short description.
- **Transition:** Ends on the "Tours & Excursions" tile, which expands/bleeds into the next section's full-width imagery — services → desire.
- **CTA:** "View All Services" → Services archive.

### 6. WHERE WE CAN TAKE YOU (Experience categories)
- **Purpose:** Create desire — this is the site's aspirational, image-forward moment (no named destinations exist yet, so it's framed around experience *types* from the PDF: Culture, Adventure, Cruises, Luxury, Leisure).
- **Visual:** Full-bleed image panels (one per category) in a subtle horizontal parallax/overlap arrangement; bold overlay labels.
- **Content:** 5 categories + tagline "Experience More. Discover Further."
- **Interaction:** Parallax drift on scroll (desktop only), simple crossfade on mobile.
- **Transition:** Rightmost/last panel narrows and slides to reveal the Corporate/Group spotlight section — desire → capability.
- **CTA:** "Explore Tours & Excursions" → relevant service page.

### 7. HOW WE DELIVER (Corporate / Group / VIP spotlight)
- **Purpose:** Reassure B2B visitors (corporates, agencies, event organizers) this isn't just leisure fluff — show operational rigor via the PDF's process timelines.
- **Visual:** Numbered horizontal process timeline (styled after PDF pages 7–10: Corporate Travel, Group Travel & Events, Transportation, Airport Services), tabs or an accordion to switch between the four flows without leaving the section.
- **Content:** Steps + value badges (Speed/Precision/Flexibility/Reliable Support, etc.) drawn from `service` CPT process-steps fields.
- **Interaction:** Timeline nodes draw in sequentially as the section enters view; switching tabs cross-fades content.
- **Transition:** Timeline's final node ("Support"/"Departure") visually anchors into the next section's technology diagram.
- **CTA:** "See how we support your business" → Services archive filtered/anchored to Corporate.

### 8. TECHNOLOGY (Digital Travel Solutions)
- **Purpose:** Signal modern, connected operations — differentiator vs. traditional agencies.
- **Visual:** Device/dashboard-style graphic (echoing PDF page 14) with connecting nodes to Access/Booking/Connectivity/Efficiency labels.
- **Content:** Digital Travel Solutions copy + 4 pillars.
- **Interaction:** Connector lines animate/draw from device outward on scroll-into-view.
- **Transition:** Node lines extend outward into the world-map graphic of the next section — tech → global network.
- **CTA:** none (supports §9).

### 9. NETWORK & WHO WE SERVE
- **Purpose:** Global-reach social proof (in the absence of testimonials/stats) + audience recognition ("this company understands people like me").
- **Visual:** Stylized world-map/connection-line graphic (Markets/Destinations/Partners/Service Network) paired with a radial/segmented display of the 7 client & partner types.
- **Content:** Partnerships + Our Network + Clients & Partners sections combined.
- **Interaction:** Map connection lines animate drawing on scroll; client-type segments fade in radially.
- **Transition:** Map recedes into a dark full-bleed panel for the closing CTA — bookending the dark hero from §1.
- **CTA:** none directly (leads into §10).

### 10. FINAL CTA
- **Purpose:** Convert. Close the emotional loop opened by the hero.
- **Visual:** Full-bleed dark panel (space/earth-curvature style imagery, echoing PDF's closing page), large bilingual headline.
- **Content:** "Opening the World to You." / "Let's Go Further Together." + short supporting line.
- **Interaction:** Headline reveals via clip/mask animation; button has a premium hover state (fill sweep).
- **Transition:** none needed (footer follows).
- **CTA:** "Start Planning" / "Contact Us" → Contact page (primary conversion action of the whole homepage).

**Design principle carried throughout:** every section shares at least one visual thread with its neighbor (color bleed, a continued line/swoosh motif, a receding/expanding image) so scrolling feels like one continuous journey, not ten independent blocks.

---

## 7. DESIGN SYSTEM

### Colors
Derived from the JETS logo (teal + silver mark) and the PDF's cover/section treatments. No gold/amber is used (that belongs to the reference site's brand, not JETS's).

```css
:root {
  /* Brand */
  --color-primary: #12767E;        /* JETS teal — from logo mark */
  --color-primary-dark: #0B4A50;   /* deep teal, hover/emphasis */
  --color-primary-light: #4FA9AF;  /* tints, subtle accents */

  --color-secondary: #0E2A30;      /* deep navy-teal — dark section bg (cover/CTA panels) */
  --color-secondary-light: #16414A;

  --color-accent-silver: #A6ABAE;  /* logo plane/silver accent — sparing use */

  /* Neutrals */
  --color-bg: #FFFFFF;
  --color-bg-alt: #F5F6F7;         /* light section background */
  --color-bg-dark: #0E2A30;        /* dark section background */
  --color-border: #E1E4E5;

  /* Text */
  --color-text: #14282B;
  --color-text-muted: #5B6A6D;
  --color-text-on-dark: #F4F6F6;
  --color-text-on-dark-muted: #AEBEC0;
}
```

### Typography
```css
:root {
  --font-heading-en: "Plus Jakarta Sans", sans-serif;
  --font-body-en: "Inter", sans-serif;
  --font-heading-ar: "Cairo", sans-serif;
  --font-body-ar: "Tajawal", sans-serif;

  --fw-regular: 400;
  --fw-medium: 500;
  --fw-semibold: 600;
  --fw-bold: 700;

  --fs-h1: clamp(2.25rem, 4vw + 1rem, 4.5rem);
  --fs-h2: clamp(1.75rem, 2.5vw + 1rem, 3rem);
  --fs-h3: clamp(1.25rem, 1.2vw + 1rem, 1.75rem);
  --fs-body-lg: clamp(1.05rem, 0.4vw + 1rem, 1.25rem);
  --fs-body: 1rem;
  --fs-small: 0.875rem;
}
```
`html[lang="ar"]` swaps `--font-heading-en`/`--font-body-en` references for the Arabic stack via a body-level font-family switch (see §11). Self-hosted font files (not a Google Fonts CDN call) for performance and to avoid FOUC/privacy concerns.

### Spacing
```css
:root {
  --container-width: 1280px;
  --container-padding: clamp(1.25rem, 4vw, 4rem);
  --section-padding-block: clamp(4rem, 8vw, 8rem);
  --grid-gap: clamp(1rem, 2vw, 2rem);
  --radius-sm: 6px;
  --radius-md: 14px;
  --radius-lg: 28px;
}
```

### Components (defined, built in later phases)
- **Buttons:** primary (filled teal, fill-sweep hover), secondary (outline, inverts on dark), text-link with animated underline.
- **Cards:** service tile, process-step node, category panel — minimal shadow, `--radius-md`, teal accent border-left or icon badge, used only where §"Visual Storytelling" calls for a card (not as the default layout).
- **Navigation:** transparent-over-hero → solid-on-scroll header; underline/dot indicator for active/hover.
- **Forms:** inquiry form (Contact), floating-label inputs, teal focus ring, inline validation states.
- **Badges:** small pill labels for value words (Speed, Precision, Comfort, Safety, etc.).
- **Sliders/tracks:** horizontal scroll-snap track (Services section), simple dot/line progress indicator.
- **Image blocks:** clip-path reveal wrapper, consistent `aspect-ratio` + `object-fit: cover`.
- **CTA sections:** full-bleed dark panel pattern reused for hero-echo moments (§2, §10).

---

## 8. ANIMATION SYSTEM

**Philosophy:** every animation either (a) reveals content as it becomes relevant, or (b) visually bridges one section into the next. Nothing animates just to move.

| Type | Where | Behavior |
|---|---|---|
| Scrollytelling pin + scrub | Hero only (desktop/tablet) | GSAP ScrollTrigger `pin` + `scrub`; 5 scroll-progress-keyed stages (image → text → line-draw → CTAs → unpin) — see §6.1. Not IntersectionObserver-based; content is tied to scroll position, not a one-time trigger |
| Page/section entrance | All other major sections | `IntersectionObserver`-triggered fade + translate-Y(24px), 500–700ms ease-out, once per element |
| Text reveal | Philosophy statement, Final CTA headline (hero text reveal is scroll-scrubbed, see above) | Line/word stagger via wrapped spans, 40–60ms stagger step |
| Image reveal | About, Experience categories, service feature tiles | `clip-path: inset()` wipe from 100%→0%, 700–900ms |
| Parallax | Experience-category panels, hero background only | Transform-based (`translateY` tied to scroll position via `requestAnimationFrame`), capped subtle range (≤40px), desktop only |
| Horizontal scroll track | Services showcase | CSS `scroll-snap` + native scroll (no heavy JS scrolljacking) |
| Sticky header transition | Header | Background/opacity/height transition at scroll threshold, 250ms |
| Hover interactions | Buttons, cards, nav links | Transform/opacity only (`transform: scale`, fill-sweep pseudo-element), 200–300ms |
| Line/connector draw | Philosophy divider, Network map, Technology diagram | `stroke-dasharray`/`stroke-dashoffset` SVG animation on scroll-into-view |
| CTA | Final CTA button | Subtle scale + shadow-lift on hover/focus |

**Implementation approach:** Vanilla JS + CSS by default (`IntersectionObserver` for scroll-triggering, CSS transitions/keyframes for the motion itself), with one deliberate exception: **the hero adopts GSAP + ScrollTrigger in Phase 6** (pin + scrub, §6.1) because precise multi-property scroll-scrubbing while pinning a section is genuinely beyond vanilla/CSS. This pulls the GSAP dependency forward from what was originally an "if needed" Phase 11 decision — once GSAP is already in the theme for the hero, Phase 11 should re-evaluate whether the Services horizontal-track and/or Network map connector-draw also adopt it for consistency, rather than re-litigating "GSAP or not" from zero.

**Reduced motion:** `@media (prefers-reduced-motion: reduce)` disables parallax and Ken-Burns drift entirely and shortens all reveal transitions to near-instant opacity fades. No motion-only content (nothing is ever *only* conveyed via animation).

**Mobile:** parallax disabled; horizontal scroll-snap tracks become vertical stacks; entrance animations keep translate distance but shorten duration (~350–450ms) to avoid feeling sluggish on touch scroll.

---

## 9. RESPONSIVE STRATEGY

| Breakpoint | Range | Key behavior |
|---|---|---|
| Large desktop | ≥1440px | Full storytelling layout, max content width capped via `--container-width`, generous whitespace |
| Desktop | 1024–1439px | Same layout, fluid via `clamp()`, horizontal service track visible |
| Tablet | 768–1023px | Two-column sections collapse to single column with reordered stacking (image above/below text per section intent); horizontal track still scroll-snap but touch-drag |
| Mobile | ≤767px | Full single-column stack; hero text/CTA repositioned lower-third; header collapses to logo + hamburger; process timelines become vertical steppers; parallax/Ken-Burns off; category panels become simple stacked cards |

Navigation: desktop shows full inline menu + language switcher + CTA; mobile uses a full-screen overlay menu (slide/fade in) with the same items, language switcher pinned near the top.

Images: all section imagery uses `aspect-ratio` + `object-fit: cover` with art-directed `<picture>`/`srcset` where a section's crop genuinely differs on mobile (e.g., hero).

Typography scales fluidly via `clamp()` (§7) rather than fixed per-breakpoint overrides, reducing duplicated CSS.

---

## 10. WORDPRESS ARCHITECTURE

```
jets-theme/
├── style.css                 # theme header + minimal reset import
├── functions.php             # slim bootstrap, requires inc/*
├── front-page.php            # homepage narrative
├── page.php                  # generic page fallback
├── page-about.php
├── page-partners.php
├── page-contact.php
├── single-service.php
├── archive-service.php
├── search.php
├── 404.php
├── index.php                 # required WP fallback
├── header.php
├── footer.php
├── assets/
│   ├── css/
│   │   ├── variables.css     # design tokens (§7)
│   │   ├── base.css          # reset, typography, RTL-aware base
│   │   ├── layout.css        # container, grid/section scaffolding
│   │   ├── components.css    # buttons, cards, nav, forms, badges
│   │   ├── animations.css    # keyframes, transition utility classes
│   │   └── responsive.css    # breakpoint overrides not covered by clamp()
│   ├── js/
│   │   ├── main.js           # init/orchestration
│   │   ├── navigation.js     # sticky header, mobile menu
│   │   ├── animations.js     # IntersectionObserver reveals, parallax, SVG draw
│   │   └── forms.js          # contact form UX (client-side only; submission via WP)
│   ├── images/                # local fallback/dev imagery
│   └── fonts/                 # self-hosted font files
├── inc/
│   ├── setup.php              # theme supports, menus, image sizes
│   ├── enqueue.php             # scripts/styles registration
│   ├── custom-post-types.php   # `service`, `testimonial` (future, hidden until populated)
│   ├── custom-fields.php       # ACF field group registration (php or json)
│   ├── i18n.php                # Polylang integration helpers, string translation registration
│   ├── inquiries.php           # jets_inquiries table (dbDelta), AJAX form handler, admin "Inquiries" menu page
│   ├── class-jets-inquiries-list-table.php  # WP_List_Table subclass, required only from the admin page render callback
│   └── helpers.php             # template helper functions (get_service_meta, etc.)
└── template-parts/
    ├── header/                 # nav-desktop.php, nav-mobile.php, language-switcher.php
    ├── footer/                 # footer-columns.php, footer-legal.php
    ├── homepage/                # section-hero.php, section-philosophy.php, section-about.php, section-why.php, section-services.php, section-experiences.php, section-delivery.php, section-technology.php, section-network.php, section-cta.php
    └── components/              # card-service.php, process-timeline.php, button.php, badge.php
```

### Custom Post Types
- **`service`** — powers all 11 integrated services *and* the deep-dive solution pages (Corporate Travel, Group Travel & Events, Transportation, Airport Services, Hotel Accommodation, Tours & Excursions, Visa Services, Digital Travel Solutions). One CPT avoids duplicating templates for content that is structurally identical (title, short/long description, icon, hero image, optional process steps, optional value badges).
  - Why one CPT instead of several: every one of these entries is "a service with an optional process timeline" — splitting into `service` + `solution` CPTs would duplicate the single-template logic for no content-modeling benefit.
- **`testimonial`** — created now (empty), not queried/displayed on the front end until at least one real testimonial exists, per the no-fabrication content rule. Keeps the architecture ready without inventing content.

Contact/CTA form submissions are **not** a CPT — see "Inquiries storage" below.

### Inquiries storage (Contact/CTA form submissions)
A custom table `{$wpdb->prefix}jets_inquiries` (columns: `id`, `name`, `email`, `phone`, `message`, `source_page`, `created_at`), created/versioned via `dbDelta()` on theme activation (`inc/inquiries.php`). A top-level wp-admin page ("Inquiries", own menu icon, capability `manage_options`) renders submissions as a sortable/paginated table using core's `WP_List_Table`, plus a detail/delete action per row. Chosen over a CPT so submissions live in their own table (no `wp_posts`/`wp_postmeta` bloat, no accidental exposure via REST/queries/search) while still getting a native-feeling admin table UI via `WP_List_Table`. Built in Phase 9 alongside the form handler: on submit, the handler both inserts a row into this table *and* sends the existing `wp_mail()` notification to the admin — storage and email are independent, so a failed email never loses the submission.

### Custom Fields (ACF — see §11 for plugin choice)
On `service`:
- `short_description` (text) — grid/teaser copy
- `full_description` (WYSIWYG) — single-service body
- `hero_image` (image)
- `value_badges` (repeater: `label`) — e.g., Speed/Precision/Flexibility
- `process_steps` (repeater: `step_title`, `step_description`) — optional; only rendered if populated
- `is_featured_on_homepage` (true/false) + `homepage_order` (number) — controls §6 spotlight selection without hardcoding

On Contact page: simple ACF fields for phone/email/address/WhatsApp/hours, each individually markable as empty (template conditionally hides unset fields rather than printing blank labels).

### Theme options
A single **Theme Options** page (via ACF Options Page, no separate custom admin UI needed) for: default OG image, contact fallback values, social links, footer legal text — avoids scattering settings across multiple screens.

### Menus
Two registered nav menus: `primary` (header) and `footer`. Each is registered per-language automatically once Polylang is active (Polylang creates language-specific menu locations).

### Widgets
None planned — footer and sidebars are hand-built template parts, not widget areas, since content is structured (services/contact) rather than freeform. Revisit only if a future blog is added.

---

## 11. MULTILINGUAL ARCHITECTURE

**Recommendation: Polylang (free) + Polylang Pro only if a paid feature becomes necessary later.**

| Option | Verdict |
|---|---|
| **Polylang** | **Chosen.** Free, integrates cleanly with a hand-coded theme (no page-builder dependency), first-class ACF field translation support (duplicates fields per translated post; string translation table for theme-hardcoded strings), full CPT/taxonomy/menu translation, lightweight — no bloat for a site this size. |
| WPML | More powerful (translation memory, XLIFF, WooCommerce), but paid, heavier, and its extra machinery (translation editor, TM) isn't needed for a ~10-page bilingual brochure/service site. Reconsider only if the client later wants professional translation-workflow tooling. |
| TranslatePress | Best for automatic/visual translation of an *existing* English site with minimal developer involvement. Weaker fit here because content is structured (CPT + ACF fields) from day one and a human is authoring both languages directly, not machine-translating a finished site. |

**Architecture:**
- Two languages: English (`en`, LTR, default) and Arabic (`ar`, RTL).
- Every page, the `service` CPT, and both menus are Polylang-translatable (one post per language, linked as a translation group).
- ACF fields are entered natively per language (Polylang duplicates the post structure; each language's `service` entry has its own field values — no runtime string-splitting).
- Theme-hardcoded UI strings (button labels, "Read more," form labels) go through Polylang's **String Translation** screen, not raw PHP string literals, so they're editable without a developer.
- Language switcher: template part in the header, reads `pll_the_languages()`, renders flag-free text switcher (EN / عربي) that links to the current page's translation (falls back to the language's homepage if no translation exists yet).
- `<html lang="…" dir="…">` set via `inc/i18n.php` based on `pll_current_language()` / `is_rtl()`.
- RTL stylesheet: **not** a separate mirrored stylesheet. CSS logical properties (`margin-inline-start`, `padding-inline-end`, `inset-inline-start`, etc.) are used throughout `assets/css/*` so the same rules serve both directions; only a small `rtl-overrides` block in `base.css` handles the few things logical properties can't cover (e.g., specific icon mirroring — arrows, the horizontal-scroll direction of the Services track, and the diagonal direction of the hero's decorative swoosh line).
- Content-missing fallback: if a page/service isn't yet translated into Arabic, Polylang is configured to fall back to showing the default-language content flagged, rather than a broken link — acceptable for the Phase-0 content gaps in §13.

---

## 12. ASSET STRATEGY

| Asset need | Source |
|---|---|
| Logo | `logo.jpeg` (provided) — will be re-exported/cleaned (transparent PNG/SVG) during Header phase; not redrawn or reinterpreted |
| Hero imagery | Phase 6 ("Flight Path" redesign) uses two curated, color-matched Unsplash photographs: `assets/images/hero-arrival.jpg` (a wing-at-sunset shot, stage 1) and `assets/images/hero-runway.jpg` (a runway-at-dusk shot, stage 3's cross-fade) — both cropped/graded via Pillow and registered in `jets_dev_image()`. `placeholder.jpg` and the jets.pdf render graphics are no longer used for the hero; other full-bleed moments still to come will follow the same curated-photography approach rather than defaulting to the PDF's generic 3D renders |
| Service icons | Custom-built minimal line-icon set (SVG, inline) matching the flat icon style of the PDF, rather than a generic icon-font library, so RTL mirroring and color theming are simple |
| PDF's own render graphics (globe, compass, hotel tower, balloon, passport, world map) | Usable as low-priority secondary imagery/inspiration for section motifs (e.g., the connector-line world map in §9), not as primary hero material — they read as generic stock-render rather than brand photography |
| Experience-category imagery (Culture/Adventure/Cruises/Luxury/Leisure) | External free sources (Unsplash/Pexels/Pixabay), curated for visual consistency (color grading, consistent aspect ratio), swappable for real destination photography later |
| All external images during dev | Referenced via a single `inc/dev-images.php` (or ACF default values) so every placeholder URL lives in one place and can be swapped for uploaded Media Library assets without hunting through templates |
| Fonts | Self-hosted (Plus Jakarta Sans, Inter, Cairo, Tajawal — all open-license Google Fonts, downloaded and served from `assets/fonts/`) |

---

## 13. OPEN QUESTIONS / [CONTENT REQUIRED] LOG

These are things the PDF does not provide. The theme will render sensible empty/hidden states for these rather than inventing content:

1. Contact details — phone, email, physical address(es), WhatsApp number, business hours, Google Maps embed URL
2. Social media profile links
3. Company founding year / history narrative
4. Any statistics (years of experience, clients served, bookings/year, etc.)
5. Certifications, licenses, or awards
6. Named destinations / regional coverage / office locations
7. Named partners (travel agencies, tour operators, hotel chains, airlines)
8. Testimonials or client case studies
9. Team/leadership bios
10. Final logo file in vector/transparent form (currently only `logo.jpeg` on white)

**Action:** flagged inline in templates as conditionally-hidden blocks (never as visible "[CONTENT REQUIRED]" text in production UI — that placeholder text is for this document only, per the brief's instruction that it "may be used temporarily" — the live theme will simply omit the block or show a graceful minimal fallback such as a working inquiry form even without a listed phone number).

**Update, 2026-08-18 (footer only):** items #1 (phone/email) and #2 (social links) now render in the footer with obvious DUMMY placeholder values (`+1 (555) 123-4567`, `info@example.com`, `#`-href Instagram/LinkedIn/Facebook) rather than being conditionally hidden — a deliberate exception since the footer's whole layout is built around that content existing, per user direction to hardcode rather than wait. Real values still needed; see `footer.php`'s `// TODO` comments.

---

## 14. DECISIONS LOG

| Decision | Rationale |
|---|---|
| One `service` CPT for both simple services and deep-dive solutions | Avoids duplicate templates for structurally identical content (title/description/icon/optional steps) |
| Polylang over WPML/TranslatePress | Best fit for a hand-built, CPT/ACF-driven theme at this content scale; free; no page-builder lock-in |
| CSS logical properties instead of a mirrored RTL stylesheet | Single source of truth for spacing/alignment; less drift between language versions over time |
| No blog/news in v1 | No content in the PDF supports it; architecture stays extensible (§10) but nothing is built prematurely |
| No fabricated testimonials/stats | Explicit content rule; `testimonial` CPT scaffolded but not rendered until populated |
| GSAP adopted early, specifically for the Hero (Phase 6), instead of deferred to Phase 11 for everything | User-specified, 2026-08-17: hero must be a scroll-scrubbed, pinned "scrollytelling" sequence, not a load-triggered staged entrance — that requires ScrollTrigger's pin+scrub, which vanilla `IntersectionObserver` cannot do. Everything else stays vanilla/CSS by default; Phase 11 revisits whether other sections adopt GSAP too now that it's already a dependency |
| Homepage = 10 connected sections, not a generic 6-block template | Matches the brief's storytelling requirement and reflects the actual breadth of PDF content without bloating into an 20-section scroll |
| `full_description` dropped as a separate ACF field | `service` CPT already supports the native content editor — a duplicate WYSIWYG field would be redundant. Long-form body now lives in `post_content`. |
| "Corporate & Group Travel" (11-list item #9) seeded as two posts: *Corporate Travel* + *Group Travel & Events* | The PDF gives each its own full page with a distinct process timeline (5 steps vs. 7 steps) — splitting preserves that structure instead of flattening two different processes into one |
| 5 of the 13 seeded services (Flight Bookings, Travel Packages & Programs, VIP Services, Events & Travel Management, Tailor-Made Travel Solutions) seeded title-only | PDF page 6 lists these by name only, with no accompanying paragraph — inventing teaser copy would violate the content rule. Templates must render these gracefully without empty-looking teaser text (Phase 8). **Superseded 2026-08-19 — see below.** |
| Original marketing copy written for those same 5 title-only services (short_description, value_badges, post_content) | User-specified, 2026-08-19: explicitly asked for original content here, overriding the no-fabrication default above for this one case. A deliberate, requested exception — not a unilateral content decision — and scoped narrowly: no process_steps were invented (the other 8 services' process timelines are all sourced from real PDF pages; these 5 never had one to draw from, so none was made up), and no company facts/stats/taglines were touched, only descriptive service copy in the same voice as the other 8 real entries. See the seeded copy directly in each post's `short_description`/`value_badges`/`post_content` (wp-admin), not duplicated here. |
| Content seeded via a one-time CLI script calling `wp_insert_post()`/ACF `update_field()`, then deleted | Same WordPress APIs a human editor uses via wp-admin — no direct SQL — kept out of the theme's file tree since it's a one-time operation, not theme logic |
| Site-wide CTAs are exactly two mechanisms: inquiry form, and a WhatsApp button → `wa.me` deep link | User-specified, 2026-08-17. No phone tel: link, no live chat widget, no booking engine for v1 — matches §16 Future Improvements scope |
| Inquiry form submissions are stored in a custom DB table (`{$wpdb->prefix}jets_inquiries`, via `dbDelta()`) and shown on a dedicated wp-admin table page (`WP_List_Table`) *and* emailed via `wp_mail()` — not email-only | User-specified, 2026-08-17 (revised same day from an initial CPT-based approach): submissions must be reviewable from wp-admin, not just land in an inbox. A plain custom table was preferred over a CPT to keep submissions out of `wp_posts`/`wp_postmeta` and away from REST/search/query exposure, while `WP_List_Table` still gives a native-feeling sortable/paginated admin UI |
| Hero redesigned same-day from a centered-text-over-photo layout to the asymmetric "Flight Path" direction, with the mountain-lake/Earth-render imagery replaced by curated aviation photography | User rejected the first pass's visual design outright (functionality was fine) as generic/templated. Ran a full creative-director-style art-direction pass (3 directions considered, one chosen and justified) rather than iterating on the rejected layout — see Phase 6 redesign notes above |
| Hero text (eyebrow/headline/sub/CTA) decoupled from the scroll-scrub entirely — plain on-load fade, no longer gated by scroll progress; only imagery + progress rail stay scroll-driven | User-specified, 2026-08-17, as a follow-up refinement to the "Flight Path" redesign: text should always be readable immediately regardless of scroll |
| All Phase 7+ scroll-reveal animation (Philosophy/About/Why JETS, and later extended to the rest of the homepage) built as one generic `[data-reveal]` + `IntersectionObserver` mechanism in `main.js`, with every actual effect defined in CSS via a `--reveal-i` stagger variable | Avoids bespoke per-section reveal JS; matches §8's vanilla-by-default policy (GSAP stays Hero-only); one mechanism scales cleanly to new sections without new JS |
| Footer rebuilt with hardcoded content in `footer.php` instead of a new ACF Theme Options screen | User-specified, 2026-08-18: "this is a private theme for this company only... you could hardcode the data." Real Privacy Policy link still reads core WP (`get_the_privacy_policy_link()`); social links, phone, email, Terms of Use URL, and developer credit are DUMMY placeholders marked `// TODO` in `footer.php` pending real values — see §13/§15 |
| Whether to also hardcode the "Homepage Content" ACF options page (Hero/Philosophy/About/Why JETS/Network/Final CTA) deferred to Phase 10, not decided now | User-specified, 2026-08-18: same instinct as the footer decision above, but ACF Options Pages + Polylang currently handle EN/AR field duplication for free — hardcoding now would mean designing a bilingual PHP structure (e.g. a `pll_current_language()`-switched array) ahead of Phase 10 actually needing it. Explicitly deferred so it's designed once, alongside real Arabic content, instead of built now and reworked later |
| `service` CPT's `value_badges`/`process_steps` switched from ACF Repeater to plain `textarea` (one item per line; steps as `Title` or `Title \| Description`) | This site only has free ACF, not PRO — Repeater fields have no admin editing UI without PRO (data could still be read/displayed, just never edited in wp-admin). `jets_get_service_meta()` parses the new format into the identical array shape, so no template needed to change |
| 5 of 13 services (Flight Bookings, Travel Packages & Programs, VIP Services, Events & Travel Management, Tailor-Made Travel Solutions) intentionally left with empty `value_badges`/`process_steps` | Same content rule as elsewhere (§13): `jets.pdf` lists these 5 by name only, with no dedicated page/process/badges to source from — inventing them would violate the no-fabrication rule. **`value_badges` (and `short_description`/`post_content`) since populated with original copy at the user's explicit request, 2026-08-19 — see the entry above. `process_steps` deliberately left empty still — no process timeline was invented for these, matching the other 8 services' `process_steps` all being real PDF content.** |
| Delivery section (§7) kept its existing arrow-chain timeline rather than being rebuilt as the tabbed/accordion originally specced for Phase 8 | Checked with the user directly, 2026-08-18: the arrow-chain was already a considered decision from earlier work (documented in the file's own comment), not an unfinished placeholder — treated as settled rather than redone to match the original plan |
| Experience-category images (§6) curated fresh for Phase 8 rather than reusing any existing set | No per-category image field/source exists; needed 5 new photos. Two rejected before downloading: an over-saturated, crowd-heavy Colosseum shot (clashed with the site's cool/moody palette) and a cruise-ship photo with a competitor's brand name ("Princess Cruises") legibly printed on the hull |
| Contact page's phone field uses intl-tel-input v23.8.0, self-hosted under `assets/vendor/intl-tel-input/`, rather than a hand-rolled country-code dropdown | Country-code/dial-code formatting and validation is exactly the kind of fiddly, well-solved problem GSAP/Swiper were already adopted for elsewhere (§8) rather than reinventing it in vanilla JS. Pinned to v23.8.0 deliberately — v24+ dropped the classic UMD build in favor of ESM-only, which doesn't fit this theme's plain-`<script>`, no-build-step loading pattern the way every other vendor script here works. No `geoIpLookup` auto-detect (that would mean a silent third-party network call on every page load); `initialCountry` is fixed to Egypt, matching the real Cairo address already entered for this site |
| WhatsApp number (`whatsapp_number` Theme Options field) left unset by default rather than seeded with a DUMMY placeholder like the footer's phone/email | The footer's phone/email are visibly "fake" placeholder text a reader recognizes as such; a fake WhatsApp number would render as a real, clickable `wa.me` link that actually opens a chat with a wrong/nonexistent number — a materially worse failure mode than a placeholder label, so this one stays genuinely hidden (per §13's no-fabrication rule) until a real number is set |
| About and Partners & Network pages reuse existing homepage template parts (`section-about`+`section-why`; `section-network`) instead of new content/markup | §5's IA for both pages is essentially "the same content as its homepage section, on its own page" — building a second copy would duplicate both the ACF field source and the markup for no benefit, same reasoning as the `service` CPT's single-template decision |
| Mission/Vision added as two new fields on the existing "Homepage Content" options page (About tab), not a new options screen or a duplicate of `about_description` | They're About-page-only content (§6's homepage narrative doesn't include a Mission/Vision section), but the wording was already sourced from `jets.pdf` and captured in §1 — reusing the same options-page pattern keeps one place for all PDF-sourced company copy rather than starting a second content-source convention |
| Phase 9's one-time seed script also created real `primary`/`footer` WordPress nav menus (Home/About/Services/Partners & Network/Contact), beyond what the phase's task list asked for | Neither menu location had anything assigned yet (Phase 4 shipped with none by design, pending real destination pages; the footer's had been a documented empty known-issue since Phase 7/8) — now that About/Partners/Contact are real pages, leaving navigation unassigned would make this phase's work unreachable except by direct URL |
| A second ACF options page (`options_ar`, wp-admin submenu of "Homepage Content") holds the Arabic values for the homepage's Hero/Mission/Vision/Philosophy/Why-JETS/Partnerships/Network/Clients/Final-CTA copy, rather than a single bilingual options page | Resolves the Phase 9 deferred decision (see the entry above it). Free ACF has no native Polylang integration for Options Pages — Polylang only duplicates real posts/pages per language. `jets_home_option()` (`inc/helpers.php`) now reads `options_ar` when `pll_current_language() === 'ar'`, falling back to the English options page for any field left empty, so the homepage never silently blanks out mid-rollout |
| Arabic `service`/page translations created via `wp_insert_post()` + `pll_set_post_language()` + `pll_save_post_translations()` in a one-time PHP script (not left in the theme), rather than entered by hand in wp-admin | Same reasoning as every prior phase's seed-script pattern (Phase 2/5/9): these are the same public WordPress/Polylang APIs a human editor would use, just scripted for the volume of content (13 services + 3 pages) — with no `wp` CLI installed on this machine, the script ran via Local's bundled PHP binary against the site's own `wp-load.php` |
| Arabic UI-chrome string translations (nav labels, buttons, form copy) written via Polylang's own `PLL_MO` class (`import_from_db()`/`add_entry()`/`export_to_db()`) instead of entered through the Strings Translation wp-admin screen | `PLL_MO` is the exact class the admin screen's own save handler uses internally (`table-string.php`'s `save_translations()`) — using it directly is equivalent to the admin UI, just scriptable for 67 strings at once |
| Arabic content is transcribed directly from `jets.pdf`'s real Arabic copy (present on all 19 pages), not machine-translated | This phase's own bar, restated from the original plan — and achievable for real once it became clear the PDF is genuinely bilingual (see the Phase 10 summary above), correcting Phase 5's earlier assumption that the deck was English-only |
| GSAP not extended to the Services carousel or the Network/Technology connector-draws in Phase 11, despite already being a theme dependency | Phase 11's own task explicitly gated this on CSS/vanilla being "demonstrably insufficient" first. It isn't: Swiper already provides full native touch/keyboard/a11y support for the carousel (Phase 8), and the connector-draws are a plain `stroke-dashoffset` transition that already respects `prefers-reduced-motion` correctly. Adding GSAP to either would be new dependency surface without a matching gain — GSAP stays Hero-only, per §8's original policy |
| `assets/css/responsive.css` stayed an empty stub through Phase 12 rather than becoming a real breakpoint-override layer | Every responsive bug found (the Swiper card-width overflow, two undersized touch targets) belonged with the component it was fixing — `.card--service`, `.site-header__toggle`, `.nav-mobile__close` — in `components.css`, not as a separately-maintained set of breakpoint overrides that could drift out of sync with the rule they're overriding. `clamp()`/logical properties already handle the vast majority of responsive behavior inline per §7/§9, which is exactly why this file was never needed |
| Phase 14 (SEO + Accessibility) run before Phase 13 (Performance), out of the plan's own numeric order | User-specified, 2026-08-19. Phase 14's own dependency line only names Phases 4–12, not 13 — nothing in a heading/alt-text/ARIA/meta-conflict audit actually needs the performance pass to exist first, so skipping ahead cost nothing. Phase 13 is still open and should be picked up before shipping |

---

## 15. KNOWN ISSUES

- ~~Hero word-stagger reveal RTL / real Arabic content~~ — **Resolved in Phase 10.** Verified live with genuine Arabic `hero_heading` copy: the per-word spans render and reorder correctly under real `dir="rtl"` (unlike the earlier English-forced-RTL test, which was a browser bidi artifact, not representative).
- ~~Phase 7's `[dir="rtl"]` overrides not live-verified~~ — **Resolved in Phase 10** to the extent `curl`/markup verification allows: confirmed rendering with real Arabic content and a genuine `dir="rtl"` document. The underlying clip-path wipe / line-draw *animations* still haven't been watched in an actual browser (see the new Phase 10 entry below) — that narrower gap carries forward.
- `footer.php` ships with DUMMY placeholder values for social links, phone, email, and the developer credit link/name (`// TODO` comments mark each) — real values still needed. Terms of Use and Privacy Policy both resolve to real pages (Terms via `get_page_by_path( 'terms-of-use' )`, Privacy via Settings > Privacy), each with a `#` fallback only if unset. The "footer" nav menu location is registered but unassigned — the nav column stays empty until a menu is assigned in Appearance > Menus.
- **New in Phase 10:** the Arabic translations of `.reveal-media` clip-path wipes, `.reveal-line` draws, the Services carousel's Swiper `rtl: true` behavior, and the Technology/Network connector-draw SVGs were all confirmed via rendered markup/CSS review, not by actually watching them animate in a real Arabic-language browser session — no Playwright/browser access this session, same standing caveat as every phase since 7. Worth a live pass before shipping.
- **New in Phase 10:** `privacy-policy`, `terms-of-use`, `sample-page`, and `style-guide` were deliberately left English-only (legal-text translation and an internal dev tool are both outside this phase's scope) — the footer's Terms of Use link on Arabic pages correctly falls back to the English URL rather than 404ing, but a fully bilingual site would eventually want at least the two legal pages translated.
- **New in Phase 10:** the Arabic `options_ar` ACF options page (Homepage Content — Arabic) has the same free-ACF limitation already accepted for `service`'s `value_badges`/`process_steps` — its repeater fields (`why_jets_pillars`, `network_categories`, `client_types`) were seeded via `update_field()` and display correctly, but have no working add/remove/reorder UI in wp-admin without ACF PRO. Editing existing values works; adding new rows doesn't.
- Phase 8's Services carousel keyboard-nav (Swiper `keyboard: { enabled: true }`) and the Experience-panel parallax (`experience-parallax.js`) were not live-tested this session (no browser access) — confirmed only by reading the config/code, not by actually pressing arrow keys or scrolling in a browser. Worth a real check before shipping.
- `single-service.php`'s `.single-service__content` "prose" styling has only been tested against the 8 services that currently have real `post_content`/badges/steps — not stress-tested against unusual WYSIWYG content (embedded images, tables, nested lists) that a future editor might add via the block editor.
- The Inquiries admin page (`inc/inquiries.php` + `inc/class-jets-inquiries-list-table.php`) was verified via `php -l` lint, code review, and a direct DB read confirming a real submission lands correctly — not by actually opening the `WP_List_Table` screen in wp-admin (no admin credentials available this session). Worth a real look before shipping.
- Contact page details (`contact_phone`/`contact_whatsapp`/`contact_email`/`contact_address`/`contact_hours`) and the site-wide `whatsapp_number` Theme Options field are all still unset (§13, items #1/#2) — the Contact page, header, footer, and mobile nav all correctly render nothing for them right now, but none of it is visible until an admin fills in the real values.
- The three new scroll-triggered reveal animations from Phase 9 (Technology's device→pillar connector-draw, Network's backdrop connector-draw, the Final CTA headline's word-cascade) were verified by reading the rendered markup/CSS, not by watching them actually animate in a browser — no Playwright/browser access this session, same caveat as Phases 7/8.
- The header/footer/mobile-nav WhatsApp button reads a single global `whatsapp_number` Theme Options field, deliberately separate from the Contact page's own `contact_whatsapp` field (site-wide default vs. page-specific value — see the Decisions Log). The user has since filled in `contact_whatsapp` (so the Contact page's own WhatsApp button now shows), but not yet the global Theme Options field — so the header/footer/mobile-nav buttons still render nothing. Not a bug, just something to flag: fill in Theme Options → WhatsApp Number too if the button should also appear site-wide, not just on Contact.
- Contact page's photo panel (`.contact-hero`, `assets/images/contact-hero.jpg`) was verified via `curl`/markup review against the site's real, now-populated contact content — not visually screenshotted in a browser (no Playwright access this session, same caveat as the rest of Phase 9).
- The Contact page's phone field country-code dropdown (intl-tel-input, self-hosted under `assets/vendor/intl-tel-input/`) was verified via `curl` (all vendor CSS/JS/flag-image files return HTTP 200, correctly enqueued only on the Contact template) and `node -c`/`php -l` syntax checks — not by actually opening the dropdown in a browser. Worth a real click-through before shipping.
- Contact page map (`contact_map_embed`, new ACF `url` field on the Contact Details group): renders a lazy-loaded `<iframe>` in its own section below the details/form grid, hidden entirely when unset (same optional-field convention as §13 item #1). The admin pastes the `src="..."` URL from Google Maps' Share → Embed a map dialog. Still unset, so nothing renders yet — not visually verified in a browser (no Playwright access this session).

## 16. FUTURE IMPROVEMENTS (explicitly out of scope for now)

Per brief: online booking engine, tour/destination filtering, CRM integration, Google Maps embed, on-site search UI beyond default, newsletter signup, blog, advanced multilingual content (beyond EN/AR). Architecture (CPTs, ACF, Polylang) is chosen so these can be added later without restructuring the theme.
