# Adding Features

This backend is organized by domain (Home, About, Services, Projects, Skills, Blog, FAQ, Contact) and follows a consistent pattern:
- Migration creates tables
- Model defines fillable fields and casts
- Controller provides public fetch and admin update endpoints
- Routes are added under `routes/api.php`
- Cache is used to avoid repeated database work

## Step-by-step pattern

1) Database
- Add a migration in `database/migrations`.
- Include `sort_order` if the frontend needs ordered lists.

2) Model
- Add a model in `app/Models` with `fillable` and `casts`.
- Define relationships if needed.

3) Controller
- Add or update a controller in `app/Http/Controllers/v1`.
- Provide:
  - `getXData()` (public) using cache
  - `getXDataFromDatabase()` (private or protected) to rebuild cache
  - `updateX...()` (admin) with validation
- Use `successResponse()` and `errorResponse()` helpers for consistency.
- Invalidate relevant cache keys after updates.

4) Routes
- Add public routes under `/api/v1`.
- Add admin routes inside the `admin.jwt` group.

5) Cache refresh
- Update `app/Jobs/RefreshAllCachesJob.php` to include the new domain cache.

6) Seeders (optional)
- If the feature needs defaults, add a seeder in `database/seeders` and include it in `DatabaseSeeder`.

7) Docs
- Update `docs/ENDPOINTS.md` with the new endpoints and request fields.
- Update `backend/README.md` with any high-level changes.

## Structure to follow

- `app/Http/Controllers/v1` for API controllers.
- `app/Models` for Eloquent models.
- `database/migrations` for schema.
- `database/seeders` for default data.
- `routes/api.php` for endpoint wiring.
- `app/Jobs/RefreshAllCachesJob.php` for cache rebuilds.
- `docs/ENDPOINTS.md` for endpoint definitions.

## Cache key convention

Existing cache keys:
- `home_page_data`
- `about_page_data`
- `services_page_data`
- `projects_page_data`
- `skills_page_data`
- `blog_page_data`
- `faq_page_data`
- `contact_page_data`
- `site_configurations`
- `themes`
- `icons`
- `social_icons`

For new domains, use `{domain}_page_data` and keep the cache TTL consistent (1 year).