# JETS Travel & Tourism — WordPress Theme

> **Integrated Travel Solutions, Global Standards.**
> A bespoke, bilingual (English / Arabic) WordPress theme for JETS, a travel management and destination-services company. It is built around storytelling and large imagery, and works fully in both LTR and RTL.

![Version](https://img.shields.io/badge/version-0.1.0-12767E)
![WordPress](https://img.shields.io/badge/WordPress-6.4%2B-0B4A50)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-16414A)
![License](https://img.shields.io/badge/license-GPLv2%2B-A6ABAE)

---

## ✨ Highlights

- **Scroll-driven homepage.** Ten sections in narrative order, opening with a pinned, scroll-scrubbed *"Flight Path"* hero (GSAP + ScrollTrigger).
- **Arabic as a first-class language.** English (LTR) and Arabic (RTL) via Polylang. Layout uses CSS logical properties throughout, so there is no separate mirrored stylesheet.
- **Services as content.** A `service` custom post type with ACF fields drives the services archive, single-service pages, and the homepage spotlight.
- **Built-in inquiry management.** Contact and CTA forms submit over AJAX into a custom `jets_inquiries` table, which you browse from a dedicated **Inquiries** screen in wp-admin (`WP_List_Table`).
- **Considered motion.** Reveals, clip-path image wipes, subtle parallax and SVG line-draws. Everything respects `prefers-reduced-motion`.
- **Performance and privacy.** Fonts and vendor libraries are self-hosted (no Google Fonts or CDN calls) and imagery is WebP.
- **SEO and accessibility.** Meta and Open Graph tags, a social-share image resolver, real 404 status codes, semantic markup and keyboard-friendly navigation.
- **International phone input.** `intl-tel-input` on forms, with country flags and validation.

## 🏠 Homepage story

| # | Section | Purpose |
|---|---------|---------|
| 1 | **Hero** | Pinned scrollytelling "Flight Path" sequence |
| 2 | **Philosophy** | Brand statement with text reveal |
| 3 | **About** | Who JETS is |
| 4 | **Why JETS** | The six pillars |
| 5 | **Services** | Horizontal scroll-snap carousel (Swiper) |
| 6 | **Experiences** | Culture, Adventure, Cruises, Luxury, Leisure (parallax panels) |
| 7 | **Delivery** | Corporate, Group & Events, Transportation, Airport, Visa |
| 8 | **Technology** | Digital travel solutions |
| 9 | **Network** | Partners and who we serve |
| 10 | **Final CTA** | Full-bleed inquiry panel |

## 🎨 Design system

| Token | Value | Use |
|-------|-------|-----|
| `--color-primary` | `#12767E` | JETS teal (from the logo mark) |
| `--color-primary-dark` | `#0B4A50` | Hover / emphasis |
| `--color-secondary` | `#0E2A30` | Deep navy-teal dark panels |
| `--color-accent-silver` | `#A6ABAE` | Sparing silver accent |

**Typography:** Plus Jakarta Sans (headings) and Inter (body) for English; Cairo (headings) and Tajawal (body) for Arabic. All sizes scale fluidly with `clamp()`.

All tokens live in [`assets/css/variables.css`](assets/css/variables.css).

## 📋 Requirements

- WordPress **6.4+**
- PHP **8.0+**
- Plugins:
  - [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) (free): service fields, Homepage Content and Theme Options pages
  - [Polylang](https://wordpress.org/plugins/polylang/) (free): English / Arabic content, menus and string translation

## 🚀 Installation

1. Clone or download this repository into `wp-content/themes/`:
   ```bash
   cd wp-content/themes
   git clone https://github.com/Muhammedraouf92/jets-theme.git
   ```
2. Install and activate **ACF** and **Polylang**.
3. In **Appearance → Themes**, activate **JETS Travel & Tourism**. Activation creates the inquiries database table.
4. In **Languages**, add English (default) and Arabic.
5. Create the pages and assign their templates by slug: `about`, `partners`, `contact`.
6. Set a static front page under **Settings → Reading**.
7. Assign menus to the `primary` (header) and `footer` locations for each language.
8. Fill in **Homepage Content** and **Theme Options**, then add your **Services**.

## 🗂️ Structure

```
jets-theme/
├── style.css                # Theme header only
├── functions.php            # Slim bootstrap — requires inc/*
├── front-page.php           # Homepage narrative
├── page-about.php · page-partners.php · page-contact.php
├── single-service.php · archive-service.php
├── search.php · 404.php · page.php · index.php
├── header.php · footer.php
├── inc/
│   ├── setup.php            # Theme supports, menus, image sizes
│   ├── enqueue.php          # Styles & scripts
│   ├── custom-post-types.php
│   ├── custom-fields.php    # ACF field groups (local PHP)
│   ├── i18n.php             # Polylang helpers & string registration
│   ├── inquiries.php        # Inquiries table, AJAX handler, admin page
│   ├── class-jets-inquiries-list-table.php
│   ├── seo.php              # Meta / Open Graph
│   └── helpers.php
├── template-parts/
│   ├── header/              # Desktop nav, mobile nav, language switcher
│   ├── homepage/            # One file per homepage section
│   └── components/          # Service card, search form
└── assets/
    ├── css/                 # variables, base, layout, components, animations, responsive
    ├── js/                  # main, navigation, animations, hero, parallax, carousel, forms
    ├── fonts/               # Self-hosted variable fonts
    ├── images/              # WebP imagery, logo, favicons
    └── vendor/              # intl-tel-input
```

## 🧰 Tech stack

- Hand-coded PHP templates (no page builder)
- Modular vanilla CSS with design tokens and logical properties
- Vanilla JS with `IntersectionObserver`; [GSAP](https://gsap.com/) + ScrollTrigger for the hero only
- [Swiper](https://swiperjs.com/) for the services carousel
- [intl-tel-input](https://github.com/jackocnr/intl-tel-input) for phone fields

## 📱 Responsive behaviour

| Breakpoint | Behaviour |
|------------|-----------|
| ≥ 1440px | Full storytelling layout |
| 1024–1439px | Fluid desktop layout |
| 768–1023px | Single-column sections, touch-drag tracks |
| ≤ 767px | Stacked layout, full-screen overlay menu, parallax off |

## 📄 License

Licensed under the [GNU General Public License v2 or later](https://www.gnu.org/licenses/gpl-2.0.html), like WordPress itself. Third-party libraries in `assets/js/vendor/` and `assets/vendor/` keep their own licenses.
