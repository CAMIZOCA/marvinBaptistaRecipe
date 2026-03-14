# Product Requirements Document — marvinbaptista.com

## 1. Overview

**Product name:** Marvin Baptista — Blog de Recetas
**URL (dev):** http://marvinbaptista.test
**Stack:** Laravel 12, PHP 8.3, SQLite (dev) / MySQL (prod), Tailwind CSS 4, Vite 7
**Type:** Recipe content website with SEO, affiliate monetization, and AI-powered content management

marvinbaptista.com is a Latin American / Ecuadorian / Mediterranean recipe blog that generates revenue through Amazon Associates affiliate links embedded on cookbook recommendation pages. The site competes with paulinacocina.net by offering high-quality SEO (Schema.org rich snippets), a modern content management system, and AI-assisted recipe writing via the Anthropic Claude API.

---

## 2. User Roles & Authentication

| Role | Access | Description |
|---|---|---|
| `super_admin` | Full access | Can manage users, settings, all content |
| `admin` | Content + books + settings | Cannot manage users |
| `editor` | Own content only | Can create/edit recipes; cannot publish/delete |
| Guest (public) | Read-only public site | No login required |

**Auth endpoints:**
- `GET /admin/login` — Login form
- `POST /admin/login` — Authenticate (checks `is_active`, role)
- `POST /admin/logout` — Logout

**Default test credentials:**
- Email: `admin@marvinbaptista.com`
- Password: `Admin2025!`
- Role: `super_admin`

---

## 3. Public-Facing Features

### 3.1 Homepage (`GET /`)
- Hero section with featured recipes (top 3 by `view_count`)
- Latest recipes grid (6 most recent published)
- Quick recipes section (total time ≤ 30 min)
- Featured categories grid (parent categories with published recipes)
- Random featured Amazon book widget

**Acceptance criteria:**
- Only recipes with `is_published = 1` AND `published_at IS NOT NULL` appear
- Categories section uses `withCount` subquery (no `HAVING` on non-aggregate for SQLite)
- Page renders in < 2s with cache

### 3.2 Recipe Listing (`GET /recetas`)
- Paginated list (12/page) of all published recipes
- Filter by: category, tag, difficulty (easy/medium/hard), time (≤30 min)
- Sort by: newest, most viewed, alphabetical

**Acceptance criteria:**
- Pagination preserves active filters
- No unpublished recipes visible to guests

### 3.3 Recipe Detail (`GET /{slug}`)
- Full recipe page: featured image, meta bar (time, servings, difficulty)
- Ingredients with interactive checkboxes (localStorage persistence)
- Serving adjuster (multiplies ingredient amounts, shows fractions: ½ ¼ ¾)
- Step-by-step instructions with optional timers
- FAQ accordion section
- Related recipes sidebar
- Amazon book recommendations
- Schema.org JSON-LD: `Recipe`, `FAQPage`, `BreadcrumbList`

**Acceptance criteria:**
- `/{slug}` returns 404 for unpublished recipes
- JSON-LD scripts render correctly (no Blade `@type` parsing errors)
- View count increments via queued job (not blocking)
- Page cached for 24h; cache invalidated on recipe save/delete

### 3.4 Category Pages
- `GET /recetas/{category}` — Category hub with subcategories + recipes
- `GET /recetas/{category}/{subcategory}` — Subcategory recipe listing

**Acceptance criteria:**
- Only published recipes shown
- Breadcrumb navigation present

### 3.5 Store (`GET /tienda`)
- Grid of active Amazon books (`is_active = 1`)
- Each book shows: cover, title, author, cuisine type
- CTA button links to country-routed Amazon affiliate URL

**Country routing for affiliate links:**
- Ecuador (`EC`), Mexico (`MX`), Chile (`CL`) → amazon.com.mx
- Spain (`ES`) → amazon.es
- Argentina (`AR`) → amazon.com.ar
- Default → amazon.com

**Acceptance criteria:**
- Affiliate tag appended to all Amazon URLs
- Inactive books not shown

### 3.6 Book Detail (`GET /tienda/{book}`)
- Full book page with description and affiliate buy button

### 3.7 Static Pages (`GET /pagina/{slug}`)
- Content from `pages` table: privacidad, cookies, terminos, sobre-mi, contacto, aviso-legal

### 3.8 SEO Utilities
- `GET /sitemap.xml` — All published recipes + categories + pages
- `GET /robots.txt` — Disallow /admin, allow all else

---

## 4. Admin Panel Features

**Base URL:** `/admin`
**Middleware:** `auth` + `is_active` + role check (editor or above)

### 4.1 Dashboard (`GET /admin`)
- 4 stat cards: total recipes, published, draft, total views
- Latest 10 recipes table
- Top 5 recipes by view count
- Quick action links

### 4.2 Recipe CRUD

| Method | URL | Description |
|---|---|---|
| GET | /admin/recetas | List all recipes with search + filter |
| GET | /admin/recetas/crear | Create form |
| POST | /admin/recetas | Store new recipe |
| GET | /admin/recetas/{id}/editar | Edit form (7 tabs) |
| PUT | /admin/recetas/{id} | Update recipe |
| DELETE | /admin/recetas/{id} | Soft delete |

**7-tab recipe editor:**
1. **Básico** — title, subtitle, slug (auto-generated via spatie/laravel-sluggable), description, featured image, origin country/region
2. **Ingredientes** — drag-sortable list, each row: amount, unit, ingredient name, group
3. **Pasos** — drag-sortable steps, each: title + rich text (Trix editor), optional timer
4. **Tiempos** — prep_time_minutes, cook_time_minutes, rest_time_minutes, servings, difficulty
5. **SEO** — seo_title (char counter 60), seo_description (char counter 160), seo_keywords, live SERP preview
6. **Historia** — story (Trix), tips_secrets (Trix)
7. **FAQ** — question/answer pairs for Schema.org + Google featured snippets

**Acceptance criteria:**
- Slug auto-generated on title input, editable
- SEO char counters: green ≤ 60/160, yellow warning, red > limit
- Categories: multiple selection with one marked as `is_primary`
- Tags: typeahead multi-select
- Ingredients/steps persist order via `order_position`/`step_number` columns
- `is_published` + `published_at` set atomically on publish action

### 4.3 AI Recipe Enhancement

| Method | URL | Description |
|---|---|---|
| POST | /admin/recetas/{id}/mejorar-ia | Trigger Anthropic API enhancement |
| POST | /admin/recetas/{id}/mejorar-ia/guardar | Save accepted AI fields |

**Rate limit:** 10 calls/hour per user (Laravel RateLimiter)

**AI output (JSON from Claude):**
```json
{
  "seo_title": "...",
  "seo_description": "...",
  "story": "...",
  "tips_secrets": "...",
  "faq": [{"question": "...", "answer": "..."}],
  "amazon_keywords": ["..."],
  "internal_link_suggestions": ["..."]
}
```

**Acceptance criteria:**
- Diff view shows before/after for each field
- User selects which fields to accept before saving
- Rate limit error returns 429 with retry-after info
- `ANTHROPIC_API_KEY` must be set in `.env`

### 4.4 CSV Recipe Import

| Method | URL | Description |
|---|---|---|
| GET | /admin/recetas/importar | Import form |
| POST | /admin/recetas/importar | Upload CSV, dispatch batch jobs |
| GET | /admin/recetas/importar/progreso/{batchId} | Poll progress (JSON) |

**CSV format:**
```
title,subtitle,description,prep_time_minutes,cook_time_minutes,servings,difficulty,
origin_country,ingredients,steps,categories,tags
```
- `ingredients`: `"amount|unit|name|group; amount|unit|name|group"`
- `steps`: `"title|description; title|description"`
- Batch size: 50 rows per job

**Acceptance criteria:**
- Progress bar updates via polling every 2s
- Invalid rows skipped with error log, valid rows imported
- Duplicate slugs auto-suffixed

### 4.5 Categories (`/admin/categorias`)
- Hierarchical: parent categories → subcategories (`parent_id`)
- Fields: name, slug, description, image, sort_order
- CRUD with parent selector

### 4.6 Tags (`/admin/etiquetas`)
- Simple name/slug CRUD

### 4.7 Amazon Books (`/admin/libros`)
- Fields: ASIN (unique), title, author, cover_image_url, amazon URLs per country (us/mx/es/ar), cuisine_type, description, keywords_match (JSON), is_active

### 4.8 Ingredients Index (`/admin/ingredientes`)
- Global ingredient index (for autocomplete in recipe editor)

### 4.9 Static Pages (`/admin/paginas`)
- Fields: title, slug, content (Trix), is_published, meta_title, meta_description

### 4.10 User Management (`/admin/usuarios`) — super_admin only
- CRUD for users with roles: super_admin, admin, editor
- Toggle `is_active` (deactivated users cannot log in)
- Upload avatar

### 4.11 Settings (`/admin/ajustes`)
- Key-value store: site_name, site_description, site_logo, google_analytics_id, amazon_affiliate_tag, recipes_per_page, default_country
- Cached with `Cache::rememberForever`

---

## 5. Database Schema (Key Tables)

### `users`
`id, name, email, password, role (super_admin|admin|editor), avatar, bio, is_active, email_verified_at, remember_token, timestamps`

### `recipes`
`id, user_id(FK), slug(unique), title, subtitle, description(text), origin_country, origin_region, prep_time_minutes, cook_time_minutes, rest_time_minutes, servings, difficulty(easy|medium|hard), featured_image, image_alt, video_url, story(longText), tips_secrets(longText), seo_title, seo_description, seo_keywords, schema_rating_value, schema_rating_count, is_published(bool), published_at, view_count, ai_enhanced_at, deleted_at, timestamps`

### `recipe_ingredients`
`id, recipe_id(FK), ingredient_name, amount, unit, notes, group_name, order_position, timestamps`

### `recipe_steps`
`id, recipe_id(FK), step_number, title, description(text), image, duration_minutes, timestamps`

### `recipe_faqs`
`id, recipe_id(FK), question, answer(text), sort_order, timestamps`

### `categories`
`id, parent_id(FK nullable), name, slug(unique), description, image, sort_order, timestamps`

### `recipe_category` (pivot)
`recipe_id, category_id, is_primary(bool)`

### `tags`
`id, name, slug(unique), timestamps`

### `amazon_books`
`id, asin(unique), slug, title, author, cover_image_url, amazon_url_us, amazon_url_mx, amazon_url_es, amazon_url_ar, cuisine_type, description, keywords_match(JSON), is_active, timestamps`

### `pages`
`id, title, slug(unique), content(longText), is_published, meta_title, meta_description, timestamps`

### `settings`
`id, key(unique), value(text), timestamps`

---

## 6. Performance & Caching

- Recipe detail pages cached 24h via `Cache::remember("recipe_page:{$slug}", 86400, ...)`
- Cache uses `database` driver (SQLite-compatible, no `Cache::tags()` in dev)
- Cache invalidated via `RecipeObserver` on `saved`, `deleted`, `restored`
- View count via `IncrementRecipeViewCount` queued job (non-blocking)
- All public routes avoid N+1 via eager loading

---

## 7. SEO Requirements

- Every recipe page must render 3 `<script type="application/ld+json">` blocks:
  1. `Recipe` schema (name, description, image, prepTime, cookTime, recipeIngredient, recipeInstructions, aggregateRating)
  2. `BreadcrumbList` schema
  3. `FAQPage` schema (if FAQs present)
- All JSON-LD generated via PHP `json_encode()` (never inline Blade `@type` tokens)
- Canonical URL on every page
- Open Graph + Twitter Card meta tags on recipe pages
- Sitemap includes: all published recipes, parent categories, static pages

---

## 8. Key User Flows for Testing

### Flow 1: Guest reads a recipe
1. Open `/` → see recipe cards
2. Click recipe card → navigate to `/{slug}`
3. Verify Schema.org JSON-LD present in `<head>`
4. Adjust servings → ingredient amounts update
5. Check off ingredients → state persists on page reload

### Flow 2: Admin creates and publishes a recipe
1. Login at `/admin/login` with `admin@marvinbaptista.com` / `Admin2025!`
2. Navigate to `/admin/recetas/crear`
3. Fill required fields (title, description)
4. Add 3 ingredients and 2 steps
5. Set SEO title/description (verify char counters)
6. Toggle `is_published` ON
7. Save → verify recipe appears on homepage and `/recetas`

### Flow 3: Admin uses AI enhancement
1. Go to `/admin/recetas/{id}/editar`
2. Click "Mejorar con IA"
3. Verify diff view renders with before/after
4. Accept selected fields → click "Guardar cambios"
5. Verify fields updated in DB

### Flow 4: Admin imports recipes via CSV
1. Navigate to `/admin/recetas/importar`
2. Upload valid CSV file (10+ rows)
3. Verify progress bar advances
4. Verify imported recipes appear in recipe list

### Flow 5: Guest visits store
1. Navigate to `/tienda`
2. Verify only `is_active = 1` books shown
3. Click "Comprar en Amazon" → URL contains affiliate tag

### Flow 6: Super admin manages users
1. Navigate to `/admin/usuarios`
2. Create new editor user
3. Log out, log in as new user
4. Verify editor cannot access `/admin/usuarios`
5. Super admin deactivates user → user cannot log in

---

## 9. Error Handling & Edge Cases

- Unpublished recipe slug → 404
- Invalid category slug → 404
- `ANTHROPIC_API_KEY` not set → AI enhancement returns 503 with user message
- CSV with malformed rows → skip row, continue import, report errors
- Rate limit exceeded (AI) → 429 with retry countdown
- Deactivated user login attempt → redirect to login with error message
- SQLite does not support `Cache::tags()` → fallback to `Cache::forget()`

---

## 10. Tech Constraints

- **PHP:** 8.3 (path: `/d/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe`)
- **DB (dev):** SQLite at `database/database.sqlite`
- **Queue driver (dev):** `sync` or `database`
- **Cache driver:** `database` (SQLite-compatible, no tag support)
- **No frontend framework:** vanilla JS modules (`resources/js/modules/`)
- **Two Vite entry points:** `resources/css/app.css` + `resources/js/app.js` (public), `resources/css/admin.css` + `resources/js/admin/app.js` (admin)
- **Blade JSON-LD:** always use `@php` + `json_encode()`, never inline `"@type"` strings in Blade templates
