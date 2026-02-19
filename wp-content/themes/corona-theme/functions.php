<?php
/**
 * Corona Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Corona_Theme
 */

if ( ! defined( 'CORONA_VERSION' ) ) {
	define( 'CORONA_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function corona_theme_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on _s, use a find and replace
		* to change 'corona-theme' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'corona-theme', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in multiple locations.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'corona-theme' ),
			'footer-1' => esc_html__( 'Footer Quick Links', 'corona-theme' ),
			'footer-2' => esc_html__( 'Footer Information', 'corona-theme' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'corona_theme_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'corona_theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function corona_theme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'corona_theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'corona_theme_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function corona_theme_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'corona-theme' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'corona-theme' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'corona_theme_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function corona_theme_scripts() {
	wp_enqueue_style( '_s-style', get_stylesheet_uri(), array(), CORONA_VERSION );
	wp_style_add_data( '_s-style', 'rtl', 'replace' );

	wp_enqueue_script( '_s-navigation', get_template_directory_uri() . '/js/navigation.js', array(), CORONA_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'corona_theme_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Corona Theme: Casino metaboxes
 */
require get_template_directory() . '/inc/metaboxes-casino.php';

/**
 * Corona Theme: Game metaboxes
 */
require get_template_directory() . '/inc/metaboxes-game.php';

/**
 * Corona Theme: Top Casinos List metaboxes
 */
require get_template_directory() . '/inc/metaboxes-review.php';

/**
 * Corona Theme: Mobile App metaboxes
 */
require get_template_directory() . '/inc/metaboxes-app.php';

/**
 * Corona Theme: Homepage metaboxes
 */
require get_template_directory() . '/inc/metaboxes-home.php';

/**
 * Corona Theme: Games Hub metaboxes
 */
require get_template_directory() . '/inc/metaboxes-games-hub.php';

/**
 * Corona Theme: Customizer settings (global affiliate link, related casinos)
 */
require get_template_directory() . '/inc/customizer-settings.php';

/**
 * Corona Theme: Related casinos block
 */
require get_template_directory() . '/inc/related-casinos.php';

/**
 * Register page templates
 */
function corona_theme_register_templates($templates) {
	$templates['page-casino.php'] = 'Casino Review';
	$templates['page-game.php'] = 'Game Page';
	return $templates;
}
add_filter('theme_page_templates', 'corona_theme_register_templates');

/**
 * Register strings for Polylang translation
 */
function corona_theme_register_polylang_strings() {
	if (function_exists('pll_register_string')) {
		// Casino characteristics
		pll_register_string('casino_operator', 'Operator', 'Corona Theme');
		pll_register_string('casino_license', 'License', 'Corona Theme');
		pll_register_string('casino_min_deposit', 'Min Deposit', 'Corona Theme');
		pll_register_string('casino_min_withdrawal', 'Min Withdrawal', 'Corona Theme');

		// Default button texts (from Customizer)
		pll_register_string('btn_play_now', 'Play Now', 'Corona Theme');
		pll_register_string('btn_claim_offer', 'Claim offer and play', 'Corona Theme');
		pll_register_string('btn_play_real', 'Play for Real Money', 'Corona Theme');

		// App page strings
		pll_register_string('app_get', 'Get', 'Corona Theme - App');
		pll_register_string('app_in_app_purchases', 'In-App Purchases', 'Corona Theme - App');
		pll_register_string('app_editors_choice', "Editors' Choice", 'Corona Theme - App');
		pll_register_string('app_ratings', 'Ratings', 'Corona Theme - App');
		pll_register_string('app_in_casino', 'in Casino', 'Corona Theme - App');
		pll_register_string('app_age', 'Age', 'Corona Theme - App');
		pll_register_string('app_screenshots', 'Screenshots', 'Corona Theme - App');
		pll_register_string('app_whats_new', "What's New", 'Corona Theme - App');
		pll_register_string('app_version', 'Version', 'Corona Theme - App');
		pll_register_string('app_ratings_reviews', 'Ratings & Reviews', 'Corona Theme - App');
		pll_register_string('app_out_of_5', 'out of 5', 'Corona Theme - App');
		pll_register_string('app_information', 'Information', 'Corona Theme - App');
		pll_register_string('app_provider', 'Provider', 'Corona Theme - App');
		pll_register_string('app_size', 'Size', 'Corona Theme - App');
		pll_register_string('app_category', 'Category', 'Corona Theme - App');
		pll_register_string('app_games_casino', 'Games: Casino', 'Corona Theme - App');
		pll_register_string('app_compatibility', 'Compatibility', 'Corona Theme - App');
		pll_register_string('app_requires_ios', 'Requires iOS 15.0 or later', 'Corona Theme - App');
		pll_register_string('app_languages', 'Languages', 'Corona Theme - App');
		pll_register_string('app_languages_list', 'English, Russian, Spanish, German', 'Corona Theme - App');
		pll_register_string('app_age_rating', 'Age Rating', 'Corona Theme - App');
		pll_register_string('app_simulated_gambling', 'Simulated Gambling', 'Corona Theme - App');
		pll_register_string('app_copyright', 'Copyright', 'Corona Theme - App');
		pll_register_string('app_inc_suffix', 'Inc.', 'Corona Theme - App');
		pll_register_string('app_back_to_casino', 'Back to Casino Review', 'Corona Theme - App');
		pll_register_string('app_disclaimer', 'Apple, the Apple logo, iPhone, and iPad are trademarks of Apple Inc., registered in the U.S. and other countries and regions. App Store is a service mark of Apple Inc. Google Play and the Google Play logo are trademarks of Google LLC.', 'Corona Theme - App');

		// Game page strings
		pll_register_string('game_ready_to_play', 'Ready to play %s?', 'Corona Theme - Game');
		pll_register_string('game_experience_free', 'Experience the game in free mode before playing for real money.', 'Corona Theme - Game');
		pll_register_string('game_play_real_money', 'Play for Real Money', 'Corona Theme - Game');
		pll_register_string('game_close_demo', 'Close Demo', 'Corona Theme - Game');
		pll_register_string('game_slot_attributes', 'Slot Attributes', 'Corona Theme - Game');
		pll_register_string('game_overview', 'Game Overview', 'Corona Theme - Game');
		pll_register_string('game_where_to_play_titlecase', 'Where to Play', 'Corona Theme - Game');
		pll_register_string('game_where_to_play', 'WHERE TO PLAY', 'Corona Theme - Game');
		pll_register_string('game_for_real_money', 'FOR REAL MONEY', 'Corona Theme - Game');
		pll_register_string('game_play_btn', 'PLAY', 'Corona Theme - Game');

		// Home page strings
		pll_register_string('home_top_casinos', 'Top Casinos', 'Corona Theme - Home');
		pll_register_string('home_popular_games', 'Popular Games', 'Corona Theme - Home');
		pll_register_string('home_view_all_casinos', 'View All Casinos', 'Corona Theme - Home');
		pll_register_string('home_view_all', 'View All', 'Corona Theme - Home');

		// Related casinos
		pll_register_string('related_casinos', 'Related Casinos', 'Corona Theme');
	}
}
add_action('init', 'corona_theme_register_polylang_strings');

/**
 * Set default Polylang translations for app page strings
 */
function corona_theme_set_default_translations() {
	if (!function_exists('pll_languages_list') || !function_exists('PLL')) {
		return;
	}

	// Version check - update this when adding new translations
	$current_version = '1.3';
	if (get_option('corona_polylang_defaults_version') === $current_version) {
		return;
	}

	$translations = array(
		'es' => array(
			// App page
			'Get' => 'Obtener',
			'In-App Purchases' => 'Compras dentro de la app',
			"Editors' Choice" => 'Selección del Editor',
			'Ratings' => 'Valoraciones',
			'in Casino' => 'en Casino',
			'Age' => 'Edad',
			'Screenshots' => 'Capturas de pantalla',
			"What's New" => 'Novedades',
			'Version' => 'Versión',
			'Ratings & Reviews' => 'Valoraciones y Reseñas',
			'out of 5' => 'de 5',
			'Information' => 'Información',
			'Provider' => 'Proveedor',
			'Size' => 'Tamaño',
			'Category' => 'Categoría',
			'Games: Casino' => 'Juegos: Casino',
			'Compatibility' => 'Compatibilidad',
			'Requires iOS 15.0 or later' => 'Requiere iOS 15.0 o posterior',
			'Languages' => 'Idiomas',
			'English, Russian, Spanish, German' => 'Inglés, Ruso, Español, Alemán',
			'Age Rating' => 'Clasificación por edad',
			'Simulated Gambling' => 'Juegos de azar simulados',
			'Copyright' => 'Derechos de autor',
			'Back to Casino Review' => 'Volver a la Reseña del Casino',
			'Apple, the Apple logo, iPhone, and iPad are trademarks of Apple Inc., registered in the U.S. and other countries and regions. App Store is a service mark of Apple Inc. Google Play and the Google Play logo are trademarks of Google LLC.' => 'Apple, el logotipo de Apple, iPhone e iPad son marcas comerciales de Apple Inc., registradas en EE.UU. y otros países. App Store es una marca de servicio de Apple Inc. Google Play y el logotipo de Google Play son marcas comerciales de Google LLC.',
			// Game page
			'Ready to play %s?' => '¿Listo para jugar %s?',
			'Experience the game in free mode before playing for real money.' => 'Experimenta el juego en modo gratuito antes de jugar con dinero real.',
			'Play for Real Money' => 'Jugar con dinero real',
			'Close Demo' => 'Cerrar Demo',
			'Slot Attributes' => 'Atributos del Slot',
			'Game Overview' => 'Descripción del Juego',
			'WHERE TO PLAY' => 'DÓNDE JUGAR',
			'FOR REAL MONEY' => 'CON DINERO REAL',
			'PLAY' => 'JUGAR',
		),
		'el' => array(
			// App page
			'Get' => 'Λήψη',
			'In-App Purchases' => 'Αγορές εντός εφαρμογής',
			"Editors' Choice" => 'Επιλογή Συντακτών',
			'Ratings' => 'Αξιολογήσεις',
			'in Casino' => 'στο Casino',
			'Age' => 'Ηλικία',
			'Screenshots' => 'Στιγμιότυπα',
			"What's New" => 'Τι νέο υπάρχει',
			'Version' => 'Έκδοση',
			'Ratings & Reviews' => 'Αξιολογήσεις & Κριτικές',
			'out of 5' => 'από 5',
			'Information' => 'Πληροφορίες',
			'Provider' => 'Πάροχος',
			'Size' => 'Μέγεθος',
			'Category' => 'Κατηγορία',
			'Games: Casino' => 'Παιχνίδια: Καζίνο',
			'Compatibility' => 'Συμβατότητα',
			'Requires iOS 15.0 or later' => 'Απαιτεί iOS 15.0 ή νεότερο',
			'Languages' => 'Γλώσσες',
			'English, Russian, Spanish, German' => 'Αγγλικά, Ρωσικά, Ισπανικά, Γερμανικά',
			'Age Rating' => 'Ηλικιακή κατάταξη',
			'Simulated Gambling' => 'Προσομοιωμένα τυχερά παιχνίδια',
			'Copyright' => 'Πνευματικά δικαιώματα',
			'Back to Casino Review' => 'Πίσω στην Κριτική Καζίνο',
			'Apple, the Apple logo, iPhone, and iPad are trademarks of Apple Inc., registered in the U.S. and other countries and regions. App Store is a service mark of Apple Inc. Google Play and the Google Play logo are trademarks of Google LLC.' => 'Η Apple, το λογότυπο Apple, το iPhone και το iPad είναι εμπορικά σήματα της Apple Inc., καταχωρημένα στις ΗΠΑ και σε άλλες χώρες. Το App Store είναι σήμα υπηρεσίας της Apple Inc. Το Google Play και το λογότυπο Google Play είναι εμπορικά σήματα της Google LLC.',
			// Game page
			'Ready to play %s?' => 'Έτοιμος να παίξεις %s;',
			'Experience the game in free mode before playing for real money.' => 'Δοκίμασε το παιχνίδι σε δωρεάν λειτουργία πριν παίξεις με πραγματικά χρήματα.',
			'Play for Real Money' => 'Παίξε με Πραγματικά Χρήματα',
			'Close Demo' => 'Κλείσιμο Demo',
			'Slot Attributes' => 'Χαρακτηριστικά Slot',
			'Game Overview' => 'Επισκόπηση Παιχνιδιού',
			'WHERE TO PLAY' => 'ΠΟΥ ΝΑ ΠΑΙΞΕΤΕ',
			'FOR REAL MONEY' => 'ΓΙΑ ΠΡΑΓΜΑΤΙΚΑ ΧΡΗΜΑΤΑ',
			'PLAY' => 'ΠΑΙΞΕ',
		),
	);

	// Get Polylang model
	$polylang = PLL();
	if (!isset($polylang->model) || !method_exists($polylang->model, 'get_languages_list')) {
		return;
	}

	$languages = $polylang->model->get_languages_list();
	$mo_data = get_option('polylang_mo', array());

	foreach ($languages as $language) {
		$lang_slug = $language->slug;
		if (!isset($translations[$lang_slug])) {
			continue;
		}

		$lang_id = $language->term_id;
		if (!isset($mo_data[$lang_id])) {
			$mo_data[$lang_id] = array();
		}

		foreach ($translations[$lang_slug] as $original => $translated) {
			// Only add if not already translated
			if (!isset($mo_data[$lang_id][$original]) || empty($mo_data[$lang_id][$original])) {
				$mo_data[$lang_id][$original] = $translated;
			}
		}
	}

	update_option('polylang_mo', $mo_data);
	update_option('corona_polylang_defaults_version', $current_version);
}
add_action('admin_init', 'corona_theme_set_default_translations');

/**
 * Translate string via Polylang (with fallback)
 */
function corona_pll__($string) {
	return function_exists('pll__') ? pll__($string) : $string;
}

/**
 * Multibyte-safe substring helper with graceful fallback.
 */
if (!function_exists('corona_mb_substr_safe')) {
	function corona_mb_substr_safe($string, $start, $length = null) {
		if (function_exists('mb_substr')) {
			return $length === null ? mb_substr($string, $start) : mb_substr($string, $start, $length);
		}
		return $length === null ? substr($string, $start) : substr($string, $start, $length);
	}
}

/**
 * SVG Icons helper
 */
if (!function_exists('corona_icon')) {
	function corona_icon($name) {
		$icons = [
			'star' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
			'shield-check' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>',
			'check-circle' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
			'x-circle' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
			'external-link' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>',
			'trophy' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>',
			'chevron-right' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>',
			'info' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
			'zap' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>',
			'lock' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>',
			'file-text' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
		];
		return $icons[$name] ?? '';
	}
}

/**
 * Add body classes for casino and game templates
 */
function corona_theme_body_classes($classes) {
	if (is_page_template('page-casino.php')) {
		$classes[] = 'casino-page';
	}
	if (is_page_template('page-game.php')) {
		$classes[] = 'game-page';
		$classes[] = 'casino-page'; // Use same dark theme
	}
	if (is_page_template('page-review.php')) {
		$classes[] = 'casino-page'; // Use same dark theme as casino pages
	}
	if (is_page_template('page-app.php')) {
		$classes[] = 'casino-page'; // Use same dark theme as casino pages
	}
	if (is_page_template('page-home.php')) {
		$classes[] = 'casino-page'; // Use same dark theme as casino pages
		$classes[] = 'home-page';
	}
	if (is_page_template('page-games-hub.php')) {
		$classes[] = 'casino-page'; // Use same dark theme as casino pages
		$classes[] = 'games-hub-page';
	}
	return $classes;
}
add_filter('body_class', 'corona_theme_body_classes');

/**
 * Modify permalink for game pages to use /games/{game-name} structure
 */
function corona_game_permalink($permalink, $post_id) {
	// Handle both post ID and post object
	if (is_object($post_id)) {
		$post = $post_id;
	} else {
		$post = get_post($post_id);
	}

	if (!$post || $post->post_type !== 'page') {
		return $permalink;
	}

	$template = get_page_template_slug($post->ID);
	if ($template !== 'page-game.php') {
		return $permalink;
	}

	// Find games hub page in same language
	$games_hub = corona_get_games_hub_page($post->ID);
	if (!$games_hub) {
		return $permalink;
	}

	// Prevent infinite recursion - temporarily remove filter
	remove_filter('page_link', 'corona_game_permalink', 10);
	$hub_permalink = get_permalink($games_hub);
	add_filter('page_link', 'corona_game_permalink', 10, 2);

	return trailingslashit($hub_permalink) . $post->post_name . '/';
}
add_filter('page_link', 'corona_game_permalink', 10, 2);

/**
 * Get games hub page for a given post (respects language)
 */
function corona_get_games_hub_page($post_id = null) {
	$args = array(
		'post_type' => 'page',
		'posts_per_page' => 1,
		'meta_query' => array(
			array(
				'key' => '_wp_page_template',
				'value' => 'page-games-hub.php',
			),
		),
		'fields' => 'ids',
	);

	// Apply language filter if Polylang is active
	if (function_exists('pll_get_post_language') && $post_id) {
		$lang = pll_get_post_language($post_id);
		if ($lang) {
			$args['lang'] = $lang;
		}
	}

	$pages = get_posts($args);
	return !empty($pages) ? $pages[0] : null;
}

/**
 * Build and cache games hub slugs used by rewrite rules.
 */
function corona_get_games_hubs_rewrite_cache($force_refresh = false) {
	$option_name = 'corona_games_hubs_rewrite_cache';
	if (!$force_refresh) {
		$cached = get_option($option_name, null);
		if (is_array($cached)) {
			return $cached;
		}
	}

	$default_lang = function_exists('pll_default_language')
		? pll_default_language()
		: '';

	$games_hubs = get_posts(array(
		'post_type' => 'page',
		'posts_per_page' => -1,
		'lang' => '',
		'fields' => 'ids',
		'meta_query' => array(
			array(
				'key' => '_wp_page_template',
				'value' => 'page-games-hub.php',
			),
		),
	));

	$rewrite_hubs = array();
	foreach ($games_hubs as $hub_id) {
		$hub = get_post($hub_id);
		if (!$hub || empty($hub->post_name)) {
			continue;
		}

		$hub_lang = function_exists('pll_get_post_language')
			? pll_get_post_language($hub_id)
			: '';

		$rewrite_hubs[] = array(
			'slug' => $hub->post_name,
			'lang' => ($hub_lang && $hub_lang !== $default_lang) ? $hub_lang : '',
		);
	}

	update_option($option_name, $rewrite_hubs, false);
	return $rewrite_hubs;
}

/**
 * Add rewrite rules for games with language prefixes.
 */
function corona_games_rewrite_rules() {
	$games_hubs = corona_get_games_hubs_rewrite_cache();
	foreach ($games_hubs as $hub_data) {
		$hub_slug = isset($hub_data['slug']) ? $hub_data['slug'] : '';
		$hub_lang = isset($hub_data['lang']) ? $hub_data['lang'] : '';
		if ($hub_slug === '') {
			continue;
		}

		if ($hub_lang !== '') {
			add_rewrite_rule(
				'^' . $hub_lang . '/' . $hub_slug . '/([^/]+)/?$',
				'index.php?pagename=$matches[1]&lang=' . $hub_lang,
				'top'
			);
		} else {
			// Default language: /games/game-slug/
			add_rewrite_rule(
				'^' . $hub_slug . '/([^/]+)/?$',
				'index.php?pagename=$matches[1]',
				'top'
			);
		}
	}
}
add_action('init', 'corona_games_rewrite_rules');

/**
 * Schedule single rewrite flush for games URLs.
 */
function corona_schedule_games_rewrite_flush($post_id, $post) {
	if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
		return;
	}

	if (!$post || $post->post_type !== 'page') {
		return;
	}

	$template = get_page_template_slug($post_id);
	if ($template === 'page-games-hub.php' || $template === 'page-game.php') {
		corona_get_games_hubs_rewrite_cache(true);
		update_option('corona_games_rewrite_flush_needed', 1, false);
	}
}
add_action('save_post', 'corona_schedule_games_rewrite_flush', 10, 2);

/**
 * Flush rewrite rules once when scheduled.
 */
function corona_maybe_flush_games_rewrite() {
	if (!get_option('corona_games_rewrite_flush_needed')) {
		return;
	}

	flush_rewrite_rules(false);
	delete_option('corona_games_rewrite_flush_needed');
}
add_action('admin_init', 'corona_maybe_flush_games_rewrite');

/**
 * Ensure rewrite cache and rules are ready after theme activation.
 */
function corona_activate_games_rewrite_rules() {
	corona_get_games_hubs_rewrite_cache(true);
	flush_rewrite_rules(false);
}
add_action('after_switch_theme', 'corona_activate_games_rewrite_rules');

/**
 * Clear games hub providers cache after game or hub updates.
 */
function corona_clear_games_hub_providers_cache($post_id, $post) {
	if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
		return;
	}

	if (!$post || $post->post_type !== 'page') {
		return;
	}

	$template = get_page_template_slug($post_id);
	if ($template !== 'page-game.php' && $template !== 'page-games-hub.php') {
		return;
	}

	$lang_keys = array('default');
	if (function_exists('pll_languages_list')) {
		$languages = pll_languages_list(array('fields' => 'slug'));
		if (is_array($languages)) {
			foreach ($languages as $lang_slug) {
				$lang_keys[] = sanitize_key($lang_slug);
			}
		}
	}

	foreach (array_unique($lang_keys) as $lang_key) {
		delete_transient('corona_games_hub_providers_' . $lang_key);
	}
}
add_action('save_post', 'corona_clear_games_hub_providers_cache', 20, 2);
