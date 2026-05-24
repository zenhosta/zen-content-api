<?php
/**
 * Plugin Name: Zen Content API
 * Description: Adds reusable public API endpoints for posts, categories, site info, search, and API documentation.
 * Version: 1.1.6
 * Author: ZenHosta.com
 * Author URI: https://zenhosta.com
 * License: GPL-2.0-or-later
 * Text Domain: zen-content-api
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WEBKULO_WP_API_NAMESPACE', 'content/v1' );
define( 'WEBKULO_WP_API_DEFAULT_PER_PAGE', 10 );
define( 'WEBKULO_WP_API_MAX_PER_PAGE', 100 );
define( 'WEBKULO_WP_API_MIN_SEARCH_LENGTH', 2 );

function webkulo_wp_api_add_rewrite_rules() {
	add_rewrite_rule( '^api/posts/?$', 'index.php?webkulo_wp_api=posts', 'top' );
	add_rewrite_rule( '^api/posts/([0-9]+)/([a-zA-Z0-9_-]+)/?$', 'index.php?webkulo_wp_api=post&webkulo_wp_api_post_id=$matches[1]', 'top' );
	add_rewrite_rule( '^api/posts/([0-9]+)/?$', 'index.php?webkulo_wp_api=post&webkulo_wp_api_post_id=$matches[1]', 'top' );
	add_rewrite_rule( '^api/categories/?$', 'index.php?webkulo_wp_api=categories', 'top' );
	add_rewrite_rule( '^api/categories/([0-9]+)/?$', 'index.php?webkulo_wp_api=category&webkulo_wp_api_category_id=$matches[1]', 'top' );
	add_rewrite_rule( '^api/categories/([0-9]+)/posts/?$', 'index.php?webkulo_wp_api=category_posts&webkulo_wp_api_category_id=$matches[1]', 'top' );
	add_rewrite_rule( '^api/pages/?$', 'index.php?webkulo_wp_api=pages', 'top' );
	add_rewrite_rule( '^api/site/?$', 'index.php?webkulo_wp_api=site', 'top' );
	add_rewrite_rule( '^api/search/?$', 'index.php?webkulo_wp_api=search', 'top' );
	add_rewrite_rule( '^api/docs/?$', 'index.php?webkulo_wp_api=docs', 'top' );
}
add_action( 'init', 'webkulo_wp_api_add_rewrite_rules', 1 );

function webkulo_wp_api_query_vars( $vars ) {
	$vars[] = 'webkulo_wp_api';
	$vars[] = 'webkulo_wp_api_post_id';
	$vars[] = 'webkulo_wp_api_category_id';

	return $vars;
}
add_filter( 'query_vars', 'webkulo_wp_api_query_vars' );

function webkulo_wp_api_mod_rewrite_rules( $rules ) {
	$api_rules  = "RewriteRule ^api/posts/?$ index.php?webkulo_wp_api=posts [QSA,L]\n";
	$api_rules .= "RewriteRule ^api/posts/([0-9]+)/([a-zA-Z0-9_-]+)/?$ index.php?webkulo_wp_api=post&webkulo_wp_api_post_id=$1 [QSA,L]\n";
	$api_rules .= "RewriteRule ^api/posts/([0-9]+)/?$ index.php?webkulo_wp_api=post&webkulo_wp_api_post_id=$1 [QSA,L]\n";
	$api_rules .= "RewriteRule ^api/categories/?$ index.php?webkulo_wp_api=categories [QSA,L]\n";
	$api_rules .= "RewriteRule ^api/categories/([0-9]+)/?$ index.php?webkulo_wp_api=category&webkulo_wp_api_category_id=$1 [QSA,L]\n";
	$api_rules .= "RewriteRule ^api/categories/([0-9]+)/posts/?$ index.php?webkulo_wp_api=category_posts&webkulo_wp_api_category_id=$1 [QSA,L]\n";
	$api_rules .= "RewriteRule ^api/pages/?$ index.php?webkulo_wp_api=pages [QSA,L]\n";
	$api_rules .= "RewriteRule ^api/site/?$ index.php?webkulo_wp_api=site [QSA,L]\n";
	$api_rules .= "RewriteRule ^api/search/?$ index.php?webkulo_wp_api=search [QSA,L]\n";
	$api_rules .= "RewriteRule ^api/docs/?$ index.php?webkulo_wp_api=docs [QSA,L]\n";

	if ( false !== strpos( $rules, 'webkulo_wp_api=posts' ) ) {
		return $rules;
	}

	return $api_rules . $rules;
}
add_filter( 'mod_rewrite_rules', 'webkulo_wp_api_mod_rewrite_rules' );

function webkulo_wp_api_activate() {
	webkulo_wp_api_add_rewrite_rules();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'webkulo_wp_api_activate' );

function webkulo_wp_api_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'webkulo_wp_api_deactivate' );

function webkulo_wp_api_plugin_row_meta( $links, $file ) {
	if ( plugin_basename( __FILE__ ) !== $file ) {
		return $links;
	}

	$links[] = '<a href="' . esc_url( home_url( '/api/docs' ) ) . '">View Details</a>';
	$links[] = '<a href="' . esc_url( home_url( '/wp-json/' . WEBKULO_WP_API_NAMESPACE . '/docs' ) ) . '">API Docs</a>';

	return $links;
}
add_filter( 'plugin_row_meta', 'webkulo_wp_api_plugin_row_meta', 10, 2 );

function webkulo_wp_api_send_json( $data, $status_code = 200 ) {
	status_header( $status_code );
	header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
	echo wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
}

function webkulo_wp_api_allowed_docs_html() {
	return array(
		'!doctype' => array(),
		'html'     => array(),
		'head'     => array(),
		'meta'     => array(
			'charset' => true,
			'name'    => true,
			'content' => true,
		),
		'title'    => array(),
		'style'    => array(
			'id' => true,
		),
		'body'     => array(),
		'h1'       => array(),
		'p'        => array(),
		'code'     => array(),
		'pre'      => array(),
		'table'    => array(),
		'thead'    => array(),
		'tbody'    => array(),
		'tr'       => array(),
		'th'       => array(),
		'td'       => array(),
		'a'        => array(
			'href' => true,
		),
	);
}

function webkulo_wp_api_get_docs_css() {
	return 'body{font-family:Arial,sans-serif;max-width:1200px;margin:40px auto;padding:0 20px;line-height:1.5;color:#1f2937}code{background:#f3f4f6;padding:2px 5px;border-radius:4px;word-break:break-all}pre{max-width:420px;margin:0;overflow:auto;background:#111827;color:#f9fafb;padding:12px;border-radius:6px;font-size:12px;line-height:1.45}pre code{background:transparent;color:inherit;padding:0;border-radius:0;word-break:normal}table{width:100%;border-collapse:collapse;margin-top:20px}th,td{border:1px solid #d1d5db;padding:10px;text-align:left;vertical-align:top;word-break:break-word}th{background:#f9fafb}';
}

function webkulo_wp_api_enqueue_docs_assets() {
	wp_register_style( 'zen-content-api-docs', false, array(), '1.1.6' );
	wp_enqueue_style( 'zen-content-api-docs' );
	wp_add_inline_style( 'zen-content-api-docs', webkulo_wp_api_get_docs_css() );
}

function webkulo_wp_api_get_docs_head() {
	webkulo_wp_api_enqueue_docs_assets();

	ob_start();
	wp_print_styles( 'zen-content-api-docs' );

	return ob_get_clean();
}

function webkulo_wp_api_send_html( $html, $status_code = 200 ) {
	status_header( $status_code );
	header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
	echo wp_kses( $html, webkulo_wp_api_allowed_docs_html() );
	exit;
}

function webkulo_wp_api_rest_response( $data, $status_code = 200 ) {
	$response = rest_ensure_response( $data );
	$response->set_status( $status_code );

	return $response;
}

function webkulo_wp_api_get_int_param( $key, $default = 0 ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only API query parameter.
	$value = isset( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : $default;

	return absint( $value );
}

function webkulo_wp_api_normalize_per_page( $per_page, $default = WEBKULO_WP_API_DEFAULT_PER_PAGE ) {
	$per_page = absint( $per_page );

	if ( ! $per_page ) {
		$per_page = $default;
	}

	return min( $per_page, WEBKULO_WP_API_MAX_PER_PAGE );
}

function webkulo_wp_api_format_categories( $post_id ) {
	$categories = get_the_category( $post_id );

	if ( empty( $categories ) || is_wp_error( $categories ) ) {
		return array();
	}

	return array_map( 'webkulo_wp_api_format_category', $categories );
}

function webkulo_wp_api_format_category( $category ) {
	return array(
		'id'          => (int) $category->term_id,
		'name'        => $category->name,
		'slug'        => $category->slug,
		'description' => $category->description,
		'count'       => (int) $category->count,
	);
}

function webkulo_wp_api_format_post( $post, $include_detail = false ) {
	$post_id        = (int) $post->ID;
	$featured_image = get_the_post_thumbnail_url( $post_id, 'full' );

	$data = array(
		'id'             => $post_id,
		'id_posts'       => $post_id,
		'title'          => get_the_title( $post_id ),
		'slug'           => $post->post_name,
		'image'          => $featured_image ? $featured_image : null,
		'author'         => get_the_author_meta( 'display_name', (int) $post->post_author ),
		'categories'     => webkulo_wp_api_format_categories( $post_id ),
		'featured_image' => $featured_image ? $featured_image : null,
	);

	if ( $include_detail ) {
		$data['date']    = get_the_date( DATE_W3C, $post_id );
		$data['excerpt'] = wp_strip_all_tags( get_the_excerpt( $post_id ) );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Applying WordPress core content filters intentionally.
		$data['content'] = apply_filters( 'the_content', $post->post_content );
	}

	return $data;
}

function webkulo_wp_api_get_posts_data( $args = array(), $include_detail = false ) {
	$query_args = wp_parse_args(
		$args,
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => WEBKULO_WP_API_DEFAULT_PER_PAGE,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$query = new WP_Query( $query_args );
	$posts = array();

	foreach ( $query->posts as $post ) {
		$posts[] = webkulo_wp_api_format_post( $post, $include_detail );
	}

	wp_reset_postdata();

	return array(
		'data'       => $posts,
		'pagination' => array(
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
			'page'        => isset( $query_args['paged'] ) ? (int) $query_args['paged'] : 1,
			'per_page'    => isset( $query_args['posts_per_page'] ) ? (int) $query_args['posts_per_page'] : -1,
		),
	);
}

function webkulo_wp_api_get_post_data( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post || 'post' !== $post->post_type || 'publish' !== $post->post_status ) {
		return new WP_Error( 'post_not_found', 'Post not found.', array( 'status' => 404 ) );
	}

	return webkulo_wp_api_format_post( $post, true );
}

function webkulo_wp_api_get_categories_data() {
	$categories = get_categories(
		array(
			'hide_empty' => true,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $categories ) ) {
		return $categories;
	}

	return array_map( 'webkulo_wp_api_format_category', $categories );
}

function webkulo_wp_api_get_category_data( $category_id ) {
	$category = get_category( $category_id );

	if ( ! $category || is_wp_error( $category ) ) {
		return new WP_Error( 'category_not_found', 'Category not found.', array( 'status' => 404 ) );
	}

	return webkulo_wp_api_format_category( $category );
}

function webkulo_wp_api_get_pages_data( $page = 1, $per_page = WEBKULO_WP_API_DEFAULT_PER_PAGE ) {
	$per_page = webkulo_wp_api_normalize_per_page( $per_page );

	$query = new WP_Query(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
		)
	);

	$pages = array();

	foreach ( $query->posts as $page_post ) {
		$pages[] = array(
			'id'      => (int) $page_post->ID,
			'title'   => get_the_title( $page_post->ID ),
			'slug'    => $page_post->post_name,
			'excerpt' => wp_strip_all_tags( get_the_excerpt( $page_post->ID ) ),
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Applying WordPress core content filters intentionally.
			'content' => apply_filters( 'the_content', $page_post->post_content ),
		);
	}

	wp_reset_postdata();

	return array(
		'data'       => $pages,
		'pagination' => array(
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
			'page'        => $page,
			'per_page'    => $per_page,
		),
	);
}

function webkulo_wp_api_get_site_data() {
	$custom_logo_id = get_theme_mod( 'custom_logo' );

	return array(
		'name'        => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'url'         => home_url( '/' ),
		'language'    => get_bloginfo( 'language' ),
		'logo'        => $custom_logo_id ? wp_get_attachment_image_url( $custom_logo_id, 'full' ) : null,
	);
}

function webkulo_wp_api_get_docs_examples() {
	$category = array(
		'id'          => 1,
		'name'        => 'News',
		'slug'        => 'news',
		'description' => 'Latest updates.',
		'count'       => 12,
	);

	$post = array(
		'id'             => 1,
		'id_posts'       => 1,
		'title'          => 'Hello World',
		'slug'           => 'hello-world',
		'image'          => 'https://example.com/wp-content/uploads/hello-world.jpg',
		'author'         => 'Admin',
		'categories'     => array( $category ),
		'featured_image' => 'https://example.com/wp-content/uploads/hello-world.jpg',
	);

	$detailed_post = array_merge(
		$post,
		array(
			'date'    => '2026-05-24T10:00:00+00:00',
			'excerpt' => 'Short post summary.',
			'content' => '<p>Full post content.</p>',
		)
	);

	$list_response = array(
		'data'       => array( $post ),
		'pagination' => array(
			'total'       => 1,
			'total_pages' => 1,
			'page'        => 1,
			'per_page'    => 10,
		),
	);

	return array(
		'/posts'                 => $list_response,
		'/posts/{id}'            => $detailed_post,
		'/posts/{id}/{slug}'     => $detailed_post,
		'/categories'            => array( $category ),
		'/categories/{id}'       => $category,
		'/categories/{id}/posts' => $list_response,
		'/pages'                 => array(
			'data'       => array(
				array(
					'id'      => 2,
					'title'   => 'About',
					'slug'    => 'about',
					'excerpt' => 'About page summary.',
					'content' => '<p>About page content.</p>',
				),
			),
			'pagination' => array(
				'total'       => 1,
				'total_pages' => 1,
				'page'        => 1,
				'per_page'    => 10,
			),
		),
		'/site'                  => array(
			'name'        => 'Example Site',
			'description' => 'Example site description.',
			'url'         => 'https://example.com/',
			'language'    => 'en-US',
			'logo'        => 'https://example.com/wp-content/uploads/logo.png',
		),
		'/search'                => $list_response,
		'/docs'                  => array(
			'name'        => 'Zen Content API',
			'version'     => '1.1.6',
			'base_url'    => 'https://example.com/wp-json/content/v1',
			'fallback'    => 'https://example.com/?rest_route=/content/v1',
			'pretty_alias' => 'https://example.com/api',
			'endpoints'   => array(),
		),
	);
}

function webkulo_wp_api_get_docs_data() {
	$base_url     = home_url( '/wp-json/' . WEBKULO_WP_API_NAMESPACE );
	$fallback_url = home_url( '/?rest_route=/' . WEBKULO_WP_API_NAMESPACE );
	$alias_url    = home_url( '/api' );
	$examples     = webkulo_wp_api_get_docs_examples();
	$endpoints    = array(
		array( 'method' => 'GET', 'path' => '/posts', 'url' => $base_url . '/posts', 'fallback_url' => $fallback_url . '/posts', 'alias_url' => $alias_url . '/posts', 'query' => array( 'page', 'per_page', 'category', 'search' ) ),
		array( 'method' => 'GET', 'path' => '/posts/{id}', 'url' => $base_url . '/posts/1', 'fallback_url' => $fallback_url . '/posts/1', 'alias_url' => $alias_url . '/posts/1' ),
		array( 'method' => 'GET', 'path' => '/posts/{id}/{slug}', 'url' => $base_url . '/posts/1/hello-world', 'fallback_url' => $fallback_url . '/posts/1/hello-world', 'alias_url' => $alias_url . '/posts/1/hello-world' ),
		array( 'method' => 'GET', 'path' => '/categories', 'url' => $base_url . '/categories', 'fallback_url' => $fallback_url . '/categories', 'alias_url' => $alias_url . '/categories' ),
		array( 'method' => 'GET', 'path' => '/categories/{id}', 'url' => $base_url . '/categories/1', 'fallback_url' => $fallback_url . '/categories/1', 'alias_url' => $alias_url . '/categories/1' ),
		array( 'method' => 'GET', 'path' => '/categories/{id}/posts', 'url' => $base_url . '/categories/1/posts', 'fallback_url' => $fallback_url . '/categories/1/posts', 'alias_url' => $alias_url . '/categories/1/posts', 'query' => array( 'page', 'per_page' ) ),
		array( 'method' => 'GET', 'path' => '/pages', 'url' => $base_url . '/pages', 'fallback_url' => $fallback_url . '/pages', 'alias_url' => $alias_url . '/pages', 'query' => array( 'page', 'per_page' ) ),
		array( 'method' => 'GET', 'path' => '/site', 'url' => $base_url . '/site', 'fallback_url' => $fallback_url . '/site', 'alias_url' => $alias_url . '/site' ),
		array( 'method' => 'GET', 'path' => '/search', 'url' => $base_url . '/search?s=keyword', 'fallback_url' => $fallback_url . '/search&s=keyword', 'alias_url' => $alias_url . '/search?s=keyword', 'query' => array( 's', 'page', 'per_page' ) ),
		array( 'method' => 'GET', 'path' => '/docs', 'url' => $base_url . '/docs', 'fallback_url' => $fallback_url . '/docs', 'alias_url' => $alias_url . '/docs' ),
	);

	foreach ( $endpoints as $key => $endpoint ) {
		$endpoints[ $key ]['example_response'] = isset( $examples[ $endpoint['path'] ] ) ? $examples[ $endpoint['path'] ] : null;
	}

	return array(
		'name'        => 'Zen Content API',
		'version'     => '1.1.6',
		'base_url'    => $base_url,
		'fallback'    => $fallback_url,
		'pretty_alias' => $alias_url,
		'note'        => 'Native /wp-json endpoints are portable after plugin activation. /api aliases require working WordPress rewrites on the server. Public list endpoints use per_page default 10 and max 100. Search requires at least 2 characters. Example responses follow native /wp-json format; /api aliases may return list data arrays directly.',
		'endpoints'   => $endpoints,
	);
}

function webkulo_wp_api_render_docs_html() {
	$docs = webkulo_wp_api_get_docs_data();
	$rows = '';
	$head = webkulo_wp_api_get_docs_head();

	foreach ( $docs['endpoints'] as $endpoint ) {
		$query   = isset( $endpoint['query'] ) ? implode( ', ', $endpoint['query'] ) : '-';
		$example = isset( $endpoint['example_response'] ) ? wp_json_encode( $endpoint['example_response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) : '';
		$rows   .= '<tr><td>' . esc_html( $endpoint['method'] ) . '</td><td><code>' . esc_html( $endpoint['path'] ) . '</code></td><td><a href="' . esc_url( $endpoint['url'] ) . '">' . esc_html( $endpoint['url'] ) . '</a></td><td><a href="' . esc_url( $endpoint['alias_url'] ) . '">' . esc_html( $endpoint['alias_url'] ) . '</a></td><td><code>' . esc_html( $endpoint['fallback_url'] ) . '</code></td><td>' . esc_html( $query ) . '</td><td><pre><code>' . esc_html( $example ) . '</code></pre></td></tr>';
	}

	return '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Zen Content API Docs</title>' . $head . '</head><body><h1>Zen Content API</h1><p>Base URL native: <code>' . esc_html( $docs['base_url'] ) . '</code></p><p>Fallback tanpa pretty permalink: <code>' . esc_html( $docs['fallback'] ) . '</code></p><p>Alias pretty: <code>' . esc_html( $docs['pretty_alias'] ) . '</code></p><p>' . esc_html( $docs['note'] ) . '</p><table><thead><tr><th>Method</th><th>Path</th><th>Native URL</th><th>Alias /api</th><th>Fallback</th><th>Query</th><th>Example Response</th></tr></thead><tbody>' . $rows . '</tbody></table></body></html>';
}

function webkulo_wp_api_build_posts_query_args_from_request( $request = null ) {
	$page     = $request instanceof WP_REST_Request ? absint( $request->get_param( 'page' ) ) : webkulo_wp_api_get_int_param( 'page', 1 );
	$per_page = $request instanceof WP_REST_Request ? $request->get_param( 'per_page' ) : webkulo_wp_api_get_int_param( 'per_page', WEBKULO_WP_API_DEFAULT_PER_PAGE );
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only API query parameter.
	$category = $request instanceof WP_REST_Request ? sanitize_text_field( (string) $request->get_param( 'category' ) ) : ( isset( $_GET['category'] ) ? sanitize_text_field( wp_unslash( $_GET['category'] ) ) : '' );
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only API query parameter.
	$search   = $request instanceof WP_REST_Request ? sanitize_text_field( (string) $request->get_param( 'search' ) ) : ( isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '' );

	$args = array(
		'paged'          => max( 1, $page ),
		'posts_per_page' => webkulo_wp_api_normalize_per_page( $per_page ),
	);

	if ( $category ) {
		$args['category_name'] = $category;
	}

	if ( $search ) {
		if ( strlen( $search ) < WEBKULO_WP_API_MIN_SEARCH_LENGTH ) {
			$args['s'] = '__zen_content_api_no_results__';

			return $args;
		}

		$args['s'] = $search;
	}

	return $args;
}

function webkulo_wp_api_register_rest_routes() {
	register_rest_route(
		WEBKULO_WP_API_NAMESPACE,
		'/posts',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => function ( WP_REST_Request $request ) {
				return webkulo_wp_api_rest_response( webkulo_wp_api_get_posts_data( webkulo_wp_api_build_posts_query_args_from_request( $request ) ) );
			},
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		WEBKULO_WP_API_NAMESPACE,
		'/posts/(?P<id>\d+)/(?P<slug>[a-zA-Z0-9_-]+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => function ( WP_REST_Request $request ) {
				$data = webkulo_wp_api_get_post_data( absint( $request['id'] ) );

				return is_wp_error( $data ) ? $data : webkulo_wp_api_rest_response( $data );
			},
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		WEBKULO_WP_API_NAMESPACE,
		'/posts/(?P<id>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => function ( WP_REST_Request $request ) {
				$data = webkulo_wp_api_get_post_data( absint( $request['id'] ) );

				return is_wp_error( $data ) ? $data : webkulo_wp_api_rest_response( $data );
			},
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		WEBKULO_WP_API_NAMESPACE,
		'/categories',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => function () {
				return webkulo_wp_api_rest_response( webkulo_wp_api_get_categories_data() );
			},
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		WEBKULO_WP_API_NAMESPACE,
		'/categories/(?P<id>\d+)',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => function ( WP_REST_Request $request ) {
				$data = webkulo_wp_api_get_category_data( absint( $request['id'] ) );

				return is_wp_error( $data ) ? $data : webkulo_wp_api_rest_response( $data );
			},
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		WEBKULO_WP_API_NAMESPACE,
		'/categories/(?P<id>\d+)/posts',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => function ( WP_REST_Request $request ) {
				$category = webkulo_wp_api_get_category_data( absint( $request['id'] ) );

				if ( is_wp_error( $category ) ) {
					return $category;
				}

				$args                = webkulo_wp_api_build_posts_query_args_from_request( $request );
				$args['cat']         = absint( $request['id'] );
				unset( $args['category_name'] );

				return webkulo_wp_api_rest_response( webkulo_wp_api_get_posts_data( $args ) );
			},
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		WEBKULO_WP_API_NAMESPACE,
		'/pages',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => function ( WP_REST_Request $request ) {
				return webkulo_wp_api_rest_response( webkulo_wp_api_get_pages_data( max( 1, absint( $request->get_param( 'page' ) ) ), webkulo_wp_api_normalize_per_page( $request->get_param( 'per_page' ) ) ) );
			},
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		WEBKULO_WP_API_NAMESPACE,
		'/site',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => function () {
				return webkulo_wp_api_rest_response( webkulo_wp_api_get_site_data() );
			},
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		WEBKULO_WP_API_NAMESPACE,
		'/search',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => function ( WP_REST_Request $request ) {
				$keyword = sanitize_text_field( (string) $request->get_param( 's' ) );

				if ( strlen( $keyword ) < WEBKULO_WP_API_MIN_SEARCH_LENGTH ) {
					return webkulo_wp_api_rest_response(
						array(
							'data'       => array(),
							'pagination' => array(
								'total'       => 0,
								'total_pages' => 0,
								'page'        => max( 1, absint( $request->get_param( 'page' ) ) ),
								'per_page'    => webkulo_wp_api_normalize_per_page( $request->get_param( 'per_page' ) ),
							),
						)
					);
				}

				$args      = webkulo_wp_api_build_posts_query_args_from_request( $request );
				$args['s'] = $keyword;

				return webkulo_wp_api_rest_response( webkulo_wp_api_get_posts_data( $args ) );
			},
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		WEBKULO_WP_API_NAMESPACE,
		'/docs',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => function () {
				return webkulo_wp_api_rest_response( webkulo_wp_api_get_docs_data() );
			},
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'webkulo_wp_api_register_rest_routes' );

function webkulo_wp_api_template_redirect() {
	$request = get_query_var( 'webkulo_wp_api' );

	if ( 'posts' === $request ) {
		webkulo_wp_api_send_json( webkulo_wp_api_get_posts_data( webkulo_wp_api_build_posts_query_args_from_request() )['data'] );
	}

	if ( 'post' === $request ) {
		$data = webkulo_wp_api_get_post_data( absint( get_query_var( 'webkulo_wp_api_post_id' ) ) );
		webkulo_wp_api_send_json( is_wp_error( $data ) ? array( 'code' => $data->get_error_code(), 'message' => $data->get_error_message() ) : $data, is_wp_error( $data ) ? 404 : 200 );
	}

	if ( 'categories' === $request ) {
		webkulo_wp_api_send_json( webkulo_wp_api_get_categories_data() );
	}

	if ( 'category' === $request ) {
		$data = webkulo_wp_api_get_category_data( absint( get_query_var( 'webkulo_wp_api_category_id' ) ) );
		webkulo_wp_api_send_json( is_wp_error( $data ) ? array( 'code' => $data->get_error_code(), 'message' => $data->get_error_message() ) : $data, is_wp_error( $data ) ? 404 : 200 );
	}

	if ( 'category_posts' === $request ) {
		$category_id = absint( get_query_var( 'webkulo_wp_api_category_id' ) );
		$category    = webkulo_wp_api_get_category_data( $category_id );

		if ( is_wp_error( $category ) ) {
			webkulo_wp_api_send_json( array( 'code' => $category->get_error_code(), 'message' => $category->get_error_message() ), 404 );
		}

		$args        = webkulo_wp_api_build_posts_query_args_from_request();
		$args['cat'] = $category_id;
		unset( $args['category_name'] );
		webkulo_wp_api_send_json( webkulo_wp_api_get_posts_data( $args )['data'] );
	}

	if ( 'pages' === $request ) {
		webkulo_wp_api_send_json( webkulo_wp_api_get_pages_data( webkulo_wp_api_get_int_param( 'page', 1 ), webkulo_wp_api_get_int_param( 'per_page', WEBKULO_WP_API_DEFAULT_PER_PAGE ) )['data'] );
	}

	if ( 'site' === $request ) {
		webkulo_wp_api_send_json( webkulo_wp_api_get_site_data() );
	}

	if ( 'search' === $request ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only API query parameter.
		$keyword = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

		if ( strlen( $keyword ) < WEBKULO_WP_API_MIN_SEARCH_LENGTH ) {
			webkulo_wp_api_send_json( array() );
		}

		$args      = webkulo_wp_api_build_posts_query_args_from_request();
		$args['s'] = $keyword;
		webkulo_wp_api_send_json( webkulo_wp_api_get_posts_data( $args )['data'] );
	}

	if ( 'docs' === $request ) {
		webkulo_wp_api_send_html( webkulo_wp_api_render_docs_html() );
	}
}
add_action( 'template_redirect', 'webkulo_wp_api_template_redirect' );
