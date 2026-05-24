# Zen Content API

Zen Content API is a WordPress plugin that adds public read-only JSON endpoints for published posts, categories, pages, site information, search, and API documentation.

It is useful for frontend integrations, mobile apps, external websites, headless WordPress projects, or services that need published WordPress content in JSON format.

## Features

- Public posts endpoint.
- Public post detail endpoint by ID or ID and slug.
- Public categories endpoint.
- Public category detail endpoint.
- Public posts-by-category endpoint.
- Public pages endpoint.
- Public site information endpoint.
- Public post search endpoint.
- API documentation endpoint.
- Pagination with `page` and `per_page` parameters.
- Default `per_page` value of `10` and maximum of `100`.
- Minimum search keyword length of `2` characters.
- Pretty `/api` URL aliases and native WordPress REST API routes.
- Example responses in API documentation for easier integration.

## Requirements

- WordPress 5.0 or newer.
- PHP 7.4 or newer.
- WordPress permalinks for `/api` aliases.

## Installation

1. Upload this plugin folder to `/wp-content/plugins/zen-content-api`.
2. Activate `Zen Content API` from the WordPress Plugins screen.
3. Open `Settings > Permalinks` and click `Save Changes` to refresh rewrite rules.
4. Open `/api/docs` or `/wp-json/content/v1/docs` to view API documentation.

## Base URLs

Native WordPress REST API base URL:

```text
https://example.com/wp-json/content/v1
```

Fallback URL when permalinks are not active:

```text
https://example.com/?rest_route=/content/v1
```

Pretty URL alias:

```text
https://example.com/api
```

The `/api` alias requires active WordPress permalinks and working server rewrite rules.

## Endpoints

| Method | Native endpoint | Pretty alias | Description |
| --- | --- | --- | --- |
| `GET` | `/wp-json/content/v1/posts` | `/api/posts` | List published posts. |
| `GET` | `/wp-json/content/v1/posts/{id}` | `/api/posts/{id}` | Get post detail by ID. |
| `GET` | `/wp-json/content/v1/posts/{id}/{slug}` | `/api/posts/{id}/{slug}` | Get post detail by ID and slug. |
| `GET` | `/wp-json/content/v1/categories` | `/api/categories` | List categories with published content. |
| `GET` | `/wp-json/content/v1/categories/{id}` | `/api/categories/{id}` | Get category detail by ID. |
| `GET` | `/wp-json/content/v1/categories/{id}/posts` | `/api/categories/{id}/posts` | List posts in category. |
| `GET` | `/wp-json/content/v1/pages` | `/api/pages` | List published pages. |
| `GET` | `/wp-json/content/v1/site` | `/api/site` | Get basic site information. |
| `GET` | `/wp-json/content/v1/search?s=keyword` | `/api/search?s=keyword` | Search published posts. |
| `GET` | `/wp-json/content/v1/docs` | `/api/docs` | View API documentation. |

The `/api/docs` HTML page shows native URLs, `/api` aliases, fallback URLs, query parameters, and example responses for every endpoint.

## Query Parameters

### Posts

- `page`: Page number.
- `per_page`: Number of posts per page, default `10`, maximum `100`.
- `category`: Category slug.
- `search`: Search keyword.

Example:

```text
https://example.com/wp-json/content/v1/posts?page=1&per_page=10
```

### Category Posts

- `page`: Page number.
- `per_page`: Number of posts per page, default `10`, maximum `100`.

### Pages

- `page`: Page number.
- `per_page`: Number of pages per page, default `10`, maximum `100`.

### Search

- `s`: Search keyword, minimum `2` characters.
- `page`: Page number.
- `per_page`: Number of posts per page, default `10`, maximum `100`.

## Response Data

Native `/wp-json` list endpoints return an object with `data` and `pagination` keys. Pretty `/api` list aliases may return the list data array directly.

Post list data includes:

- `id`: Post ID.
- `id_posts`: Post ID.
- `title`: Post title.
- `slug`: Post slug.
- `image`: Featured image URL.
- `author`: Author display name.
- `categories`: Post categories.
- `featured_image`: Featured image URL.

Post detail data also includes:

- `date`: Post date in W3C format.
- `excerpt`: Post excerpt.
- `content`: Post content after WordPress `the_content` filters.

Category data includes:

- `id`: Category ID.
- `name`: Category name.
- `slug`: Category slug.
- `description`: Category description.
- `count`: Number of posts in category.

Site data includes basic site metadata such as name, description, URL, language, and logo.

## Security Notes

- Endpoints are public and read-only.
- Endpoints only return posts or pages with `publish` status.
- Category lists only include categories that have published content.
- List endpoints use default `per_page` value of `10` and maximum of `100` to reduce large responses.
- Search endpoints require at least `2` characters to reduce expensive queries.
- If frontend renders HTML from `content`, make sure only trusted users can publish content or add frontend sanitization.

## WordPress Plugin Metadata

- Version: `1.1.6`
- Author: `ZenHosta.com`
- License: `GPL-2.0-or-later`
- Text domain: `zen-content-api`

## License

GPL-2.0-or-later. See <https://www.gnu.org/licenses/gpl-2.0.html>.
