# API Endpoints

Base path: `/api/v1`

This backend expects JSON bodies for POST/PUT/DELETE unless noted.
Authentication:
- Public endpoints are open.
- Admin endpoints require the `admin.jwt` middleware (JWT bearer token).

## Public endpoints

### Site and UI
- `POST /site/configurations`
  - Body: none
- `POST /site/configurations/get/themes`
  - Body: none
- `POST /ui/social-icons`
  - Body: none
- `POST /ui/icons`
  - Body: none

### Home
- `POST /home/meta`
  - Body: none

### About
- `POST /about`
  - Body: none

### Services
- `POST /services`
  - Body: none

### Projects
- `POST /projects`
  - Body: none

### Skills
- `POST /skills`
  - Body: none
- `GET /skills/cv/download`
  - Body: none

### Blog
- `POST /blog`
  - Body: none
- `POST /blog/article/{slug}`
  - Body: none
- `POST /blog/subscribe`
  - Body:
    - `email` (string, required, email)
- `POST /blog/unsubscribe`
  - Body:
    - `email` (string, required) or base64 email
- `GET /blog/unsubscribe`
  - Query:
    - `email` (string, required) or base64 email

### FAQ
- `POST /faq`
  - Body: none

### Contact
- `POST /contact`
  - Body: none
- `POST /contact/submit`
  - Body:
    - `name` (string, required)
    - `email` (string, required, email)
    - `company` (string, optional)
    - `reason` (string, required)
    - `budget` (string, required)
    - `timeline` (string, required)
    - `subject` (string, optional)
    - `message` (string, required)

### Keys (public)
- `POST /user`
  - Body:
    - `userKey` (string, required, exactly 4 uppercase letters)
- `POST /app`
  - Body:
    - `appKey` (string, required, exactly 4 uppercase letters)

### Replay (public ingest)
- `POST /replay/start`
  - Body:
    - `userKey` (string, required)
    - `appKey` (string, required)
- `POST /replay/append`
  - Body:
    - `sessionId` (string, required)
    - `userKey` (string, required)
    - `appKey` (string, required)
    - `events` (array, required)
    - `ts` (integer, required, milliseconds)

### Meta pages
- `POST /meta/pages/{page}`
  - Body:
    - `locale` (string, optional, in: `en`, `ar`)

## Admin auth endpoints

- `POST /admin/auth/login`
  - Body:
    - `email` (string, required)
    - `password` (string, required)
- `POST /admin/auth/me`
  - Body: none
- `POST /admin/auth/logout`
  - Body: none

## Admin endpoints (JWT required)

### Site configuration
- `POST /site/configurations/update/full-name`
  - Body:
    - `full_name` (string, required)
- `POST /site/configurations/update/contact-information`
  - Body:
    - `contact_email` (string, required, email)
    - `contact_phone` (string, required)
- `POST /site/configurations/add/social-links`
  - Body:
    - `platform` (string, required)
    - `url` (string, required)
    - `icon_key` (string, required)
    - `sort_order` (integer, required)
- `POST /site/configurations/update/social-links`
  - Body:
    - `id` (integer, required)
    - `platform` (string, required)
    - `url` (string, required)
    - `icon_key` (string, required)
- `DELETE /site/configurations/delete/social-links`
  - Body:
    - `id` (integer, required)
- `POST /site/configurations/order/social-links`
  - Body:
    - `ids` (array of integers, required)
- `POST /site/configurations/theme-palette/update`
  - Body:
    - `name` (string, required)
    - `palette` (object, required)
- `POST /site/configurations/theme-palette/restore-default`
  - Body: none
- `POST /site/configurations/theme-palette/create`
  - Body:
    - `name` (string, required)
    - `palette` (object, required)
- `POST /site/configurations/theme-palette/activate`
  - Body:
    - `palette_id` (integer, required)
- `POST /site/configurations/event-theme/toggle`
  - Body:
    - `theme_key` (string, required)
- `POST /site/configurations/maintenance-mode/toggle`
  - Body:
    - `site_mode` (string, required, `normal` or `maintenance`)
    - `maintenance_hours` (integer, optional)
    - `maintenance_minutes` (integer, optional)

### Home
- `POST /home/hero/update`
  - Body:
    - `status_text` (string, required)
    - `status_active` (boolean, required)
    - `full_name` (string, required)
    - `role_title` (string, required)
    - `headline` (string, required)
    - `subheadline` (string, required)
- `POST /home/featured-projects/update`
  - Body:
    - `id` (integer, optional)
    - `title` (string, required)
    - `description` (string, required)
    - `image_type` (string, required: `emoji`, `url`, or `icon`)
    - `image` (string, required if `image_type` is `emoji` or `url`)
    - `icon_key` (string, required if `image_type` is `icon`)
    - `tech` (string, required)
    - `sort_order` (integer, required)
- `POST /home/featured-projects/delete`
  - Body:
    - `id` (integer, required)

### About
- `POST /about/hero/update`
  - Body: `title` (string), `subtitle` (string, optional), `description` (string, optional)
- `POST /about/stats/update`
  - Body:
    - `stats` (array, required)
    - `stats.*.key` (string)
    - `stats.*.value` (string)
    - `stats.*.label` (string)
- `POST /about/introduction/update`
  - Body:
    - `avatarImage` (string, optional, base64 or URL)
    - `availabilityActive` (boolean, required)
    - `availabilityText` (string, optional)
    - `fullName` (string, required)
    - `roleTitle` (string, required)
    - `paragraphs` (array, required)
    - `techStack` (array, required)
- `POST /about/services/update`
  - Body:
    - `services` (array, required)
    - `services.*.title` (string)
    - `services.*.description` (string)
    - `services.*.features` (array)
- `POST /about/work-process/update`
  - Body:
    - `steps` (array, required)
    - `steps.*.step` (string)
    - `steps.*.title` (string)
    - `steps.*.description` (string)
- `POST /about/values/update`
  - Body:
    - `values` (array, required)
    - `values.*.key` (string)
    - `values.*.title` (string)
    - `values.*.description` (string)

### Services
- `POST /services/hero/update`
  - Body: `title` (string), `subtitle` (string, optional), `description` (string, optional)
- `POST /services/items/update`
  - Body:
    - `services` (array, required)
    - `services.*.title` (string)
    - `services.*.description` (string)
    - `services.*.icon_key` (string)
    - `services.*.color` (string, optional)
    - `services.*.features` (array)
- `POST /services/why-choose-me/update`
  - Body:
    - `why_choose_me` (array, required)
    - `why_choose_me.*.title` (string)
    - `why_choose_me.*.description` (string)
    - `why_choose_me.*.icon_key` (string)
- `POST /services/deliverables/update`
  - Body:
    - `deliverables` (array, required)
    - `deliverables.*.title` (string)
    - `deliverables.*.description` (string)
    - `deliverables.*.icon_key` (string)

### Projects
- `POST /projects/hero/update`
  - Body: `title` (string), `subtitle` (string, optional), `description` (string, optional)
- `POST /projects/items/update`
  - Body:
    - `id` (integer, optional)
    - `title` (string, required)
    - `description` (string, required)
    - `image` (string, required; base64 or URL)
    - `tech_stack` (array, required)
    - `github_url` (string, optional)
    - `live_url` (string, optional)
    - `contact_email` (string, optional)
    - `is_featured` (boolean, required)
    - `sort_order` (integer, required)
- `POST /projects/items/delete`
  - Body:
    - `id` (integer, required)

### Skills
- `POST /skills/hero/update`
  - Body:
    - `title` (string, required)
    - `subtitle` (string, optional)
    - `description` (string, optional)
    - `cv_label` (string, required)
    - `cv_file` (string, optional, base64)
- `POST /skills/social-links/update`
  - Body:
    - `socials` (array, required)
    - `socials.*.platform` (string)
    - `socials.*.url` (string)
    - `socials.*.icon_key` (string)
- `POST /skills/categories/update`
  - Body:
    - `skill_categories` (array, required)
    - `skill_categories.*.key` (string)
    - `skill_categories.*.icon_key` (string)
    - `skill_categories.*.title` (string)
    - `skill_categories.*.description` (string, optional)
    - `skill_categories.*.skills` (array of strings)
- `POST /skills/learning-focus/update`
  - Body:
    - `title` (string, required)
    - `description` (string, required)
    - `topics` (array, required)

### Blog
- `POST /blog/hero/update`
  - Body: `title` (string), `subtitle` (string, optional), `description` (string, optional)
- `POST /blog/author/update`
  - Body:
    - `name` (string, required)
    - `avatar` (string, optional, base64 or URL)
    - `bio` (string, optional)
    - `social_links` (array, optional)
- `POST /blog/posts/add`
  - Body:
    - `title` (string, required)
    - `content` (array, optional)
    - `image` (string, optional)
    - `media_type` (string, optional: `image`, `emoji`, `icon`)
    - `category` (string, optional)
    - `tags` (array, optional)
    - `published_at` (date, optional)
- `POST /blog/posts/update`
  - Body:
    - `id` (integer, required)
    - `title` (string, required)
    - `content` (array, optional)
    - `image` (string, optional)
    - `media_type` (string, optional: `image`, `emoji`, `icon`)
    - `category` (string, optional)
    - `tags` (array, optional)
    - `published_at` (date, optional)
- `POST /blog/posts/delete`
  - Body:
    - `id` (integer, required)

### FAQ
- `POST /faq/hero/update`
  - Body: `title` (string), `subtitle` (string, optional), `description` (string, optional)
- `POST /faq/items/update`
  - Body:
    - `items` (array, required)
    - `items.*.question` (string)
    - `items.*.answer` (string)

### Contact
- `POST /contact/hero/update`
  - Body: `title` (string), `subtitle` (string, optional), `description` (string, optional)
- `POST /contact/infos/update`
  - Body:
    - `infos` (array, required)
    - `infos.*.label` (string)
    - `infos.*.value` (string)
    - `infos.*.icon_key` (string)
    - `infos.*.type` (string)
- `POST /contact/social-links/update`
  - Body:
    - `links` (array, required)
    - `links.*.label` (string)
    - `links.*.url` (string)
    - `links.*.icon_key` (string)
- `POST /contact/submissions`
  - Body: none
- `POST /contact/submissions/mark-read`
  - Body:
    - `id` (integer, required)

### Cache
- `POST /cache/clear`
  - Body: none
- `POST /cache/refresh`
  - Body: none

### Meta pages
- `POST /meta/pages/{page}/update`
  - Body:
    - `title` (string, required)
    - `description` (string, required)
    - `locale` (string, required, `en` or `ar`)
    - `keywords` (array, required)

### Replay (admin)
- `GET /replay/sessions`
  - Query:
    - `userKey` (string, optional)
    - `appKey` (string, optional)
    - `limit` (integer, optional)
    - `page` (integer, optional)
- `GET /replay/session/{sessionId}`
  - Query: none
- `DELETE /replay/session/{sessionId}`
  - Body: none

### Keys (admin)
- `GET /keys/users`
  - Query:
    - `search` (string, optional)
    - `limit` (integer, optional)
    - `page` (integer, optional)
- `PUT /keys/users/{id}`
  - Body:
    - `label` (string, required)
- `DELETE /keys/users/{id}`
  - Body: none
- `GET /keys/apps`
  - Query:
    - `search` (string, optional)
    - `limit` (integer, optional)
    - `page` (integer, optional)
- `PUT /keys/apps/{id}`
  - Body:
    - `source` (string, required)
- `DELETE /keys/apps/{id}`
  - Body: none

## Notes

- File uploads are sent as base64 strings for images and CVs.
- Many list updates truncate and reinsert items with `sort_order` in a transaction.
- Caches are refreshed in `RefreshAllCachesJob` or via admin cache endpoints.