=== Zen Content API ===
Contributors: webkulo
Tags: api, rest-api, posts, categories, headless
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.1.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds public JSON endpoints for posts, categories, pages, site information, search, and API documentation.

== Description ==

Zen Content API provides reusable public API endpoints for WordPress content.

Use it for frontend integrations, mobile apps, external websites, headless projects, or other services that need published WordPress content in JSON format.

After activation, data is available through native WordPress REST API endpoints and the pretty `/api` alias.

On the WordPress Plugins screen, the plugin adds `View Details` and `API Docs` links. `View Details` opens HTML documentation at `/api/docs`, and `API Docs` opens JSON documentation at `/wp-json/content/v1/docs`.

Main features:

* Public endpoint for posts.
* Public endpoint for post details by ID.
* Public endpoint for categories.
* Public endpoint for posts by category.
* Public endpoint for pages.
* Public endpoint for basic site information.
* Public endpoint for post search.
* API documentation endpoint.
* Pagination through `page` and `per_page` parameters.
* Default `per_page` limit of 10 and maximum of 100.
* Minimum search length of 2 characters.
* Fallback REST URL support when pretty permalinks are not available.
* Example responses in API documentation for easier integration.

== Basic Usage ==

Native WordPress REST API base URL:

`https://example.com/wp-json/content/v1`

Fallback URL when permalinks are not active:

`https://example.com/?rest_route=/content/v1`

Pretty URL alias:

`https://example.com/api`

Note: the `/api` alias requires active WordPress permalinks and working server rewrite rules.

== Available Endpoints ==

= Posts =

Returns published posts.

`GET /wp-json/content/v1/posts`

Alias:

`GET /api/posts`

Available query parameters:

* `page` - Page number.
* `per_page` - Number of posts per page, default 10 and maximum 100.
* `category` - Category slug.
* `search` - Search keyword.

Example:

`https://example.com/wp-json/content/v1/posts?page=1&per_page=10`

= Post Detail =

Returns post details by ID.

`GET /wp-json/content/v1/posts/{id}`

Alias:

`GET /api/posts/{id}`

The endpoint also supports ID and slug format:

`GET /wp-json/content/v1/posts/{id}/{slug}`

Example:

`https://example.com/wp-json/content/v1/posts/1/hello-world`

= Categories =

Returns categories that have published content.

`GET /wp-json/content/v1/categories`

Alias:

`GET /api/categories`

= Category Detail =

Returns category details by ID.

`GET /wp-json/content/v1/categories/{id}`

Alias:

`GET /api/categories/{id}`

= Category Posts =

Returns posts by category ID.

`GET /wp-json/content/v1/categories/{id}/posts`

Alias:

`GET /api/categories/{id}/posts`

Available query parameters:

* `page` - Page number.
* `per_page` - Number of posts per page, default 10 and maximum 100.

= Pages =

Returns published pages.

`GET /wp-json/content/v1/pages`

Alias:

`GET /api/pages`

Available query parameters:

* `page` - Page number.
* `per_page` - Number of pages per page, default 10 and maximum 100.

= Site Info =

Returns basic site information such as site name, description, URL, language, and logo.

`GET /wp-json/content/v1/site`

Alias:

`GET /api/site`

= Search =

Searches published posts by keyword.

`GET /wp-json/content/v1/search?s=keyword`

Alias:

`GET /api/search?s=keyword`

Available query parameters:

* `s` - Search keyword.
* Search keywords must be at least 2 characters.
* `page` - Page number.
* `per_page` - Number of posts per page, default 10 and maximum 100.

= API Docs =

Returns endpoint documentation in JSON format, including example responses for each endpoint.

`GET /wp-json/content/v1/docs`

HTML documentation alias:

`GET /api/docs`

The HTML documentation shows native URLs, `/api` aliases, fallback URLs, query parameters, and example responses.

= Plugin Details =

On the WordPress Plugins screen, click `View Details` to open HTML API documentation.

Click `API Docs` to open JSON API documentation.

== Response Data ==

Native `/wp-json` list endpoints return an object with `data` and `pagination` keys. Pretty `/api` list aliases may return the list data array directly.

Post data includes:

* `id` - Post ID.
* `id_posts` - Post ID.
* `title` - Post title.
* `slug` - Post slug.
* `image` - Featured image URL.
* `author` - Author display name.
* `categories` - Post categories.
* `featured_image` - Featured image URL.

Post detail data also includes:

* `date` - Post date in W3C format.
* `excerpt` - Post excerpt.
* `content` - Post content after WordPress `the_content` filters.

Category data includes:

* `id` - Category ID.
* `name` - Category name.
* `slug` - Category slug.
* `description` - Category description.
* `count` - Number of posts in the category.

== Security Notes ==

Endpoints in this version are read-only and only return posts or pages with `publish` status.

Category lists only include categories that have published content.

Post and page list endpoints use a default `per_page` value of 10 and a maximum of 100 to reduce large responses.

Search endpoints require at least 2 characters to reduce expensive queries.

If HTML from the `content` field is rendered in a frontend, make sure only trusted users can publish content or add frontend sanitization as needed.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate the plugin through the WordPress Plugins screen.
3. Open Settings > Permalinks and click Save Changes to refresh rewrite rules.
4. Access API documentation at `/api/docs` or `/wp-json/content/v1/docs`.

== Frequently Asked Questions ==

= Do the endpoints require authentication? =

No. All endpoints are public and only return published content.

= Why does the /api URL not work? =

Make sure WordPress permalinks are active. Open Settings > Permalinks and click Save Changes. If it still does not work, use the native `/wp-json/content/v1` endpoint or the `?rest_route=/content/v1` fallback.

= Does this plugin modify posts or categories? =

No. The plugin only provides read-only GET endpoints.

= Are drafts returned? =

No. The plugin only returns posts and pages with `publish` status.

== Changelog ==

= 1.1.6 =
* Adds example responses to API documentation.
* Updates HTML API documentation table with response examples.

= 1.1.4 =
* Adds hardening for public endpoints with default `per_page` 10 and maximum 100.
* Limits search to at least 2 characters.
* Shows only categories with published content.
* Adds security notes to documentation.

= 1.1.3 =
* Changes native REST API namespace from `webkulo/v1` to `content/v1`.
* Updates the main plugin file name and author.

= 1.1.2 =
* Adds and stabilizes posts, categories, pages, site, search, and docs endpoints.
* Provides native REST API URLs, fallback REST routes, and the `/api` pretty alias.
* Adds pagination and basic filters on selected endpoints.

== Upgrade Notice ==

= 1.1.6 =
This version adds example responses to API documentation.

= 1.1.4 =
This version adds public endpoint hardening and security documentation.
