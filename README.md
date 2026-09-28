# Backend README

This backend is a Laravel API that powers the portfolio site. It exposes public endpoints for the frontend, admin-only endpoints for content management, and utility endpoints for caching, session keys, and replay sessions. Most responses use a shared JSON response shape from `app/Helpers/global.php`.

## Quick map

-   Entry points: `bootstrap/app.php`
-   Controllers: `app/Http/Controllers` and `app/Http/Controllers/v1`
-   Models: `app/Models`
-   Migrations and seeders: `database/migrations`, `database/seeders`
-   Mail templates: `resources/views/emails`
-   Storage for uploads: `storage/app/public` (served via `public/storage`)

## Response format

Helpers in `app/Helpers/global.php` standardize responses:

-   `successResponse(data, message, status)`
-   `errorResponse(message, status, errors)`

Controllers generally return `{ success, message, data }` or `{ success, message, errors }`.

## Authentication and middleware

-   Admin authentication uses JWT via the `admin-api` guard.
-   `app/Http/Middleware/AdminJwtAuth.php` blocks admin routes unless `auth('admin-api')` is valid.
-   Admin routes are grouped under `/api/v1` and protected by `admin.jwt`.

## API routes (high level)

Routes are defined in `routes/api.php` under `/api/v1`.

Public endpoints:

-   `POST /api/v1/site/configurations` -> site configuration payload
-   `POST /api/v1/site/configurations/get/themes` -> theme list and palettes
-   `POST /api/v1/ui/social-icons` and `POST /api/v1/ui/icons` -> icon registries
-   `POST /api/v1/home/meta`, `POST /api/v1/about`, `POST /api/v1/services`, `POST /api/v1/projects`, `POST /api/v1/skills`, `POST /api/v1/blog`, `POST /api/v1/faq`, `POST /api/v1/contact`
-   `GET /api/v1/skills/cv/download` -> download CV file
-   Blog: `POST /api/v1/blog/article/{slug}`, `POST /api/v1/blog/subscribe`, `POST /api/v1/blog/unsubscribe`, `GET /api/v1/blog/unsubscribe`
-   Contact form: `POST /api/v1/contact/submit`
-   Key management (public): `POST /api/v1/user`, `POST /api/v1/app`
-   Replay session ingestion: `POST /api/v1/replay/start`, `POST /api/v1/replay/append`
-   Meta pages: `POST /api/v1/meta/pages/{page}`

Admin auth endpoints:

-   `POST /api/v1/admin/auth/login`, `POST /api/v1/admin/auth/me`, `POST /api/v1/admin/auth/logout`

Admin endpoints (JWT protected):

-   Site configuration updates (full name, contact info, social links, themes, maintenance)
-   Content updates for home/about/services/projects/skills/blog/faq/contact
-   Cache management: `POST /api/v1/cache/clear`, `POST /api/v1/cache/refresh`
-   Meta page updates: `POST /api/v1/meta/pages/{page}/update`
-   Replay admin: list, fetch, delete sessions
-   Key admin: list, update, delete user/app keys

## Controllers and responsibilities

### `MainController`

-   Serves icon registries and meta pages.
-   Caches social icons and icons for one year.
-   Meta pages are localized by `locale` and cached per page/locale.

### `HomeController`

-   Serves home hero and featured projects.
-   Admin can update hero and featured projects, and delete items.
-   Uses one-year cache (`home_page_data`).

### `AboutController`

-   Serves hero, stats, introduction (with avatar upload), services, work process, and values.
-   Many updates replace lists in a transaction (truncate + reinsert with sort order).
-   Uses one-year cache (`about_page_data`).

### `ServicesController`

-   Serves hero, service items, why-choose-me items, and deliverables.
-   Updates replace list data in a transaction.
-   Uses one-year cache (`services_page_data`).

### `ProjectsController`

-   Serves projects hero and project list.
-   Admin can create or update projects; supports base64 image upload to `storage/app/public/uploads/projects`.
-   Updates clear projects and home caches.

### `SkillsController`

-   Serves skills hero, categories with skills, learning focus, and social links.
-   Supports base64 upload for CV file and download via `GET /skills/cv/download`.
-   Updates clear `skills_page_data` cache.

### `BlogController`

-   Serves blog hero, author, and posts list.
-   Supports base64 image uploads for posts and author avatar.
-   Generates slugs, excerpt, and read time.
-   Subscriptions support subscribe/unsubscribe (GET/POST) with base64 email in links.
-   Sends queued email notifications for new posts.
-   Uses one-year cache (`blog_page_data`).

### `FAQController`

-   Serves FAQ hero and items.
-   Updates replace list items in a transaction.
-   Uses one-year cache (`faq_page_data`).

### `ContactController`

-   Serves contact hero, contact info, social links, and a static form schema.
-   Stores submissions and emails all admins.
-   Admin endpoints list submissions and mark them read.
-   Uses one-year cache (`contact_page_data`).

### `SiteConfigurations`

-   Manages site settings (name, contact info, maintenance mode), social links, themes, and theme palettes.
-   Supports activating palettes, restoring defaults, and toggling event themes.
-   Returns a combined payload for frontend: site config, personal info, theme data.

### `AdminAuthController`

-   JWT login, current admin (`me`), logout, and refresh.

### `CacheController`

-   Clears cached page data and icon caches.
-   Queues a full cache refresh job.

### `SessionController`

-   Initializes session keys `u` (user) and `a` (app) with validation.
-   Keys can come from request or are generated by backend.

### `UserKeyController` and `AppKeyController`

-   Store, list, update, and delete keys.
-   Keys must be exactly 4 uppercase letters.
-   `AppKeyController` prevents deleting the default `UNKN` key.

### `ReplayController`

-   Creates replay sessions and appends rrweb events.
-   Admin endpoints list sessions, fetch all events, and delete sessions.

## Jobs

`app/Jobs/RefreshAllCachesJob.php` rebuilds cache for:

-   All page data (home/about/services/projects/skills/blog/faq/contact)
-   Site config and themes
-   Icon registries

## Mail

-   `ContactFormSubmissionMail` uses `resources/views/emails/contact-form-submission.blade.php`.
-   `NewBlogPostMail` uses `resources/views/emails/new-blog-post.blade.php` and generates an unsubscribe link.
-   Blog notifications are queued, contact submissions are sent immediately per admin.

## Models (by domain)

Common:

-   `SiteSetting`, `Theme`, `ThemePalette`, `Icon`, `SocialMediaIcon`, `SocialLink`, `MetaPage`, `Admin`

Home:

-   `HomeHero`, `HomeFeaturedProject`

About:

-   `AboutHero`, `AboutStat`, `AboutIntroduction`, `AboutService`, `AboutWorkProcess`, `AboutValue`

Services:

-   `ServicesHero`, `ServiceItem`, `WhyChooseMeItem`, `DeliverableItem`

Projects:

-   `ProjectsHero`, `Project`

Skills:

-   `SkillsHero`, `SkillsSocialLink`, `SkillCategory`, `Skill`, `LearningFocus`

Blog:

-   `BlogHero`, `BlogAuthor`, `BlogPost`, `BlogSubscription`

FAQ:

-   `FAQHero`, `FAQItem`

Contact:

-   `ContactHero`, `ContactInfo`, `ContactSocialLink`, `ContactSubmission`

Keys and replay:

-   `UserKey`, `AppKey`, `SessionEvent`, `ReplaySession`, `ReplayEvent`

Many models cast JSON fields (for example `BlogPost.content`, `BlogPost.tags`, `SkillCategory.skills`, `LearningFocus.topics`) and keep list ordering using a `sort_order` column.

## Database schema overview

Core tables:

-   `site_settings`, `themes`, `theme_palettes`, `icons`, `social_media_icons`, `social_links`, `meta_pages`, `admins`

Page data tables:

-   Home: `home_heroes`, `home_featured_projects`
-   About: `about_heroes`, `about_stats`, `about_introductions`, `about_services`, `about_work_processes`, `about_values`
-   Services: `services_heroes`, `service_items`, `why_choose_me_items`, `deliverable_items`
-   Projects: `projects_heroes`, `projects`
-   Skills: `skills_heroes`, `skills_social_links`, `skill_categories`, `skills`, `learning_focus`
-   Blog: `blog_heroes`, `blog_authors`, `blog_posts`, `blog_subscriptions`
-   FAQ: `faq_heroes`, `faq_items`
-   Contact: `contact_heroes`, `contact_infos`, `contact_social_links`, `contact_submissions`

Keys and replay tables:

-   `user_keys`, `app_keys`, `session_events`, `replay_sessions`, `replay_events`

Default Laravel tables:

-   `users`, `password_reset_tokens`, `sessions`, `jobs`, `job_batches`, `failed_jobs`, `cache`, `cache_locks`

## Storage and uploads

Uploads are stored on the public disk and referenced via absolute URLs:

-   About introduction avatar: `storage/app/public/uploads/about`
-   Project images: `storage/app/public/uploads/projects`
-   Blog images and author avatar: `storage/app/public/uploads/blog` and `uploads/blog/author`
-   CV files: `storage/app/public/uploads/cv`

## Caching strategy

Most page data is cached for one year under these keys:

-   `home_page_data`, `about_page_data`, `services_page_data`, `projects_page_data`, `skills_page_data`, `blog_page_data`, `faq_page_data`, `contact_page_data`
-   `site_configurations`, `themes`, `icons`, `social_icons`
-   `meta_page_{page}_{locale}`

Admin updates invalidate relevant caches, and `RefreshAllCachesJob` can rebuild them.

## Seeders

-   `AdminSeeder` creates admin accounts.
-   `ThemeSeeder` and `ThemePaletteSeeder` set up default themes and palette.
-   `IconSeeder` and `SocialMediaIconSeeder` seed icon registries.

## Default admins (remove on deploy)

`database/seeders/AdminSeeder.php` seeds two default admins for local testing:

-   `admin1@example.com` / `password123`
-   `admin2@example.com` / `password123`

Before deploying, remove these users or delete the seeder entries and re-seed with real credentials.

## Docs

-   `docs/ENDPOINTS.md` lists all endpoints and accepted fields.
-   `docs/ADDING_FEATURES.md` describes how to add new features and follow the project structure.
-   `docs/postman_collection.json` is a Postman collection for quick testing.

## Tests

Only default example tests are present in `tests/Feature` and `tests/Unit`.

## Notes

-   JWT and other app config live under `config/` (for example `config/jwt.php`, `config/cors.php`, `config/mail.php`, `config/queue.php`).
