# marvinbaptista.com — Recipe Blog & Affiliate Store

A full-stack recipe blog built from scratch with Laravel 12, featuring a custom admin panel,
AI-assisted content enhancement via the Anthropic Claude API, and an Amazon Associates
affiliate store with geo-aware product routing. The project is production-ready and
deliberately avoids admin scaffolding packages to keep full control over the UX and data layer.

---

## Overview

marvinbaptista.com is a content-driven web application for publishing culinary recipes,
organized by categories and tags, with rich structured data for SEO and a curated book store
for monetization. The admin panel supports a full editorial workflow — from importing recipes
in bulk via CSV to refining content with AI — making it practical as a solo-operated
publishing platform or a small editorial team.

The public frontend presents recipes with step-by-step instructions, ingredient lists, FAQs,
and schema-compliant JSON-LD markup that feeds directly into Google's rich result ecosystem.

---

## Key Features

- **Custom Admin Panel** — Built without Filament or Nova. Includes a 7-tab recipe editor,
  category/tag management, book listings, page CMS, and site settings — all with role-based
  access control (`super_admin`, `admin`, `editor`).
- **AI Content Enhancement** — A dedicated `RecipeEnhancer` service calls
  `claude-sonnet-4-6` (Anthropic API) to improve recipe descriptions, ingredient phrasing,
  and FAQ content directly from the admin UI.
- **Bulk CSV Import** — Recipes can be imported in batches via CSV. Processing is handled
  asynchronously using Laravel Jobs and the `Batchable` interface, keeping the UI responsive
  during large imports.
- **Schema.org Structured Data** — Every recipe page emits `Recipe`, `FAQPage`, and
  `BreadcrumbList` JSON-LD via a reusable `HasRecipeSchema` trait, optimized for Google
  Search rich results.
- **Amazon Affiliate Store** — Book listings link to Amazon Associates with country-aware
  routing. ASINs are stored per-book and links are resolved at render time based on the
  visitor's region.
- **Sitemap & SEO Infrastructure** — XML sitemap auto-generated via `spatie/laravel-sitemap`
  covering recipes, pages, categories, and store entries. `robots.txt` is also managed
  through the application.
- **Vanilla JS Modules** — 11 custom JavaScript modules handle interactivity (ingredient
  scaling, tab navigation, search, etc.) without pulling in a frontend framework.
- **Queue-Driven Architecture** — Background jobs handle CSV processing and view count
  increments (`IncrementRecipeViewCount`) to keep request cycles lean.

---

## Tech Stack

### Backend
- **PHP 8.3** + **Laravel 12**
- **Spatie Laravel Sluggable** — automatic slug generation per model
- **Spatie Laravel Sitemap** — programmatic sitemap generation
- **Intervention Image 3** — image processing and optimization on upload
- **League CSV** — CSV parsing for bulk recipe imports
- **GuzzleHTTP** — HTTP client for Anthropic API integration
- **Laravel Queue** — async job processing (CSV chunks, view increments)

### Frontend
- **Vanilla JavaScript** — 11 modular ES modules, no framework dependency
- **Trix Editor** — rich text editing for recipe steps and page content
- **Vite 7** — asset bundling with HMR in development

### Database
- **SQLite** (development) → **MySQL** (production)
- 14 migrations: `recipes`, `recipe_ingredients`, `recipe_steps`, `recipe_faqs`,
  `categories`, `tags`, `ingredients_index`, `amazon_books`, `recipe_books`, `pages`,
  `settings`, plus standard Laravel system tables

### Styling / UI
- **Tailwind CSS 4** — utility-first, configured via the Vite plugin (no config file)
- Custom admin sidebar with `zinc-900` design system
- Responsive public frontend with component-based Blade templates

### Tooling / Other
- **Anthropic Claude API** (`claude-sonnet-4-6`) — AI content enhancement service
- **Amazon Associates** — affiliate link management with country routing
- **Laravel Pail** — real-time log tailing in development
- **Laravel Pint** — code style enforcement (PSR-12)
- **PHPUnit 11** — unit and feature test suite

---

## My Role

I designed and built this project end-to-end as the sole developer — including architecture
decisions, database schema design, backend services, frontend implementation, and SEO
strategy.

Specific contributions:

- Designed the full relational schema (14 tables) from scratch, with soft deletes,
  polymorphic relationships, and observer-driven side effects.
- Built a custom authentication system with multi-role RBAC without relying on Breeze or
  Jetstream, to keep full control over session handling and middleware.
- Integrated the Anthropic Claude API into a standalone `RecipeEnhancer` service with prompt
  engineering tailored to culinary content.
- Implemented the CSV import pipeline using Laravel Batches and the `Batchable` job
  interface, allowing chunked async processing with progress tracking.
- Authored a `HasRecipeSchema` trait that generates Schema.org-compliant JSON-LD for three
  structured data types, injected server-side per recipe.
- Wrote all 11 vanilla JS modules without a frontend framework, keeping the bundle lean and
  the dependency count minimal.

---

## Screenshots

> _Screenshots coming soon. The project is currently in active development._

<!--
![Home page](./docs/screenshots/home.png)
![Recipe detail](./docs/screenshots/recipe-detail.png)
![Admin dashboard](./docs/screenshots/admin-dashboard.png)
![Recipe editor](./docs/screenshots/recipe-editor.png)
-->

---

## Local Setup

**Requirements:** PHP 8.3, Composer, Node.js 20+, SQLite or MySQL.

```bash
# 1. Clone the repository and install dependencies
git clone https://github.com/your-username/marvinbaptista.git
cd marvinbaptista
composer install
npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Set required environment variables in .env
#    DB_CONNECTION=sqlite               (or mysql for production)
#    ANTHROPIC_API_KEY=sk-ant-...       (required for AI enhancement)
#    AMAZON_AFFILIATE_TAG=your-tag-20   (optional for affiliate store)

# 4. Run migrations and start development servers
php artisan migrate
composer run dev   # starts Laravel + Queue + Pail + Vite concurrently
```

Access the admin panel at `/admin/login` after creating a user with `super_admin` role via
`php artisan tinker`.

---

## Technical Highlights

- **Zero admin package dependency** — the entire admin panel (sidebar, tabs, forms, tables,
  permissions) is hand-built in Blade and Tailwind, which means no black-box abstraction
  between the data layer and the UI.
- **Schema.org as a trait** — `HasRecipeSchema` cleanly separates structured data
  generation from controllers and views, making it testable and reusable across recipe
  contexts (show page, sitemap, API responses).
- **Batchable CSV import** — the import pipeline splits uploaded CSV files into chunks,
  dispatches them as batched jobs, and handles failures per-chunk without rolling back the
  entire import. This pattern scales to thousands of recipes without blocking the web process.
- **Minimal JS footprint** — interactive features (ingredient unit scaling, AJAX search,
  tabbed navigation, print layout) are implemented as independent ES modules loaded
  conditionally per page, keeping the total JS payload small.
- **AI integration as a service layer** — the Anthropic API call is encapsulated in
  `RecipeEnhancer.php` with its own prompt templates, decoupled from controllers. The
  service is swappable and independently testable.
- **Country-aware affiliate routing** — Amazon Associates links resolve dynamically at
  render time based on a stored routing map, avoiding hardcoded locale-specific URLs in the
  database.

---

## Future Improvements

- **Search with full-text index** — replace the current LIKE-based search with MySQL
  `FULLTEXT` or integrate Meilisearch via Laravel Scout.
- **Recipe ratings and comments** — add a lightweight user-facing interaction layer with
  moderation tools in the admin panel.
- **GA4 + Search Console integration** — surface traffic and ranking data inside the admin
  dashboard without leaving the app.
- **Image CDN pipeline** — offload image storage to S3-compatible storage and serve via
  a CDN with on-the-fly resizing.
- **REST API layer** — expose recipes as a JSON API to support a potential mobile app or
  third-party integrations.
- **Automated content scheduling** — queue recipe publishing by date/time from the admin
  editor.

---

## Notes

This project reflects my approach to building maintainable, dependency-conscious Laravel
applications: custom where it matters, opinionated where it speeds things up, and always
structured for long-term clarity over short-term convenience. It is actively maintained and
open to feedback from the community.
