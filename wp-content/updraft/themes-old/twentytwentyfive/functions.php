<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Adds theme support for post formats.
if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

// Enqueues editor-style.css in the editors.
if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

// Enqueues style.css on the front.
if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues style.css on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

// Registers custom block styles.
if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

// Registers block binding callback function for the post format name.
if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;


// Добавьте этот код в functions.php вашей темы

// ==========================================
// SLOT TEMPLATE REGISTRATION - ИСПРАВЛЕННАЯ ВЕРСИЯ
// ==========================================

// 1. Регистрация шаблона для всех версий WordPress
function register_slot_page_template() {
    // Для классического редактора
    add_filter('theme_page_templates', function($templates) {
        $templates['page-slot.php'] = 'Slot Page';
        return $templates;
    });
}
add_action('init', 'register_slot_page_template');

// 2. Для Block Editor (Gutenberg) - принудительная регистрация
function force_slot_template_registration($templates, $wp_theme, $post, $post_type) {
    if ('page' === $post_type) {
        $templates['page-slot.php'] = 'Slot Page';
    }
    return $templates;
}
add_filter('theme_page_templates', 'force_slot_template_registration', 10, 4);

// 3. Убеждаемся что WordPress видит шаблон
function ensure_slot_template_exists($templates) {
    $slot_template_path = get_template_directory() . '/page-slot.php';
    if (file_exists($slot_template_path)) {
        $templates['page-slot.php'] = 'Slot Page';
    }
    return $templates;
}
add_filter('theme_page_templates', 'ensure_slot_template_exists', 15);

// 4. Обновление списка шаблонов при загрузке админки
function refresh_page_templates() {
    if (is_admin()) {
        $templates = wp_get_theme()->get_page_templates();
        $templates['page-slot.php'] = 'Slot Page';
        // Принудительно обновляем кеш
        wp_cache_delete('page_templates-' . md5(get_template() . get_stylesheet()), 'themes');
    }
}
add_action('admin_init', 'refresh_page_templates');

// ==========================================
// SLOT FUNCTIONS - ПРОДОЛЖЕНИЕ
// ==========================================

// 5. Функция рендеринга информационного блока о слоте
function render_slot_info_block($post_id) {
    $slot_image = get_post_meta($post_id, '_slot_image', true);
    $slot_description = get_post_meta($post_id, '_slot_description', true);
    
    ob_start();
    ?>
    <div class="slot-info-container">
        <div class="slot-info-layout">
            
            <!-- Левая часть: изображение слота -->
            <div class="slot-image-section">
                <?php if (!empty($slot_image)): ?>
                    <div class="slot-image-wrapper">
                        <img src="<?php echo esc_url($slot_image); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>" class="slot-image">
                    </div>
                <?php else: ?>
                    <div class="slot-image-placeholder">
                        <div class="placeholder-content">
                            <span class="placeholder-icon">🎰</span>
                            <span class="placeholder-text"><?php echo esc_html(get_the_title($post_id)); ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Кнопки действий -->
                <div class="slot-action-buttons">
                    <button class="slot-play-btn" onclick="alert('Переход к игре на реальные деньги')">
                        ▶ Jugar con dinero real
                    </button>
                    <button class="slot-demo-btn" onclick="alert('Открыть демо версию')">
                        🎮 Jugar Demo
                    </button>
                </div>
            </div>
            
            <!-- Правая часть: информация о слоте -->
            <div class="slot-details-section">
                <h2 class="slot-info-title"><?php echo esc_html(get_the_title($post_id)); ?></h2>
                
                <!-- Таблица с характеристиками -->
                <div class="slot-characteristics">
                    <?php
                    // Стандартные поля
                    $characteristics = array(
                        'Proveedor' => get_slot_meta($post_id, 'provider', 'InOut Games'),
                        'Tipo' => get_slot_meta($post_id, 'type', 'Máquina tragamonedas progresiva'),
                        'Variabilidad' => get_slot_meta($post_id, 'volatility', 'Variable'),
                        'Game Hero rtp' => get_slot_meta($post_id, 'rtp', '98%'),
                        'Tarifa mínima' => get_slot_meta($post_id, 'min_bet', '0,1'),
                        'Máximo compromiso' => get_slot_meta($post_id, 'max_bet', '200'),
                        'Reproducción automática' => get_slot_meta($post_id, 'autoplay', 'Sí'),
                        'Fecha de lanzamiento' => get_slot_meta($post_id, 'release_date', '11.11.2021')
                    );
                    
                    foreach ($characteristics as $label => $value):
                    ?>
                        <div class="characteristic-row">
                            <div class="characteristic-label"><?php echo esc_html($label); ?></div>
                            <div class="characteristic-value"><?php echo esc_html($value); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Описание -->
                <?php if (!empty($slot_description)): ?>
                    <div class="slot-description">
                        <p><?php echo wp_kses_post($slot_description); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
    
    <style>
    .slot-info-container {
        width: 100%;
    }
    
    .slot-info-layout {
        display: grid;
        grid-template-columns: 350px 1fr;
        gap: 30px;
        align-items: start;
    }
    
    .slot-image-section {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    .slot-image-wrapper {
        background: rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    
    .slot-image {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .slot-image-placeholder {
        background: rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        border: 2px dashed rgba(255,255,255,0.3);
    }
    
    .placeholder-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    
    .placeholder-icon {
        font-size: 48px;
    }
    
    .placeholder-text {
        color: rgba(255,255,255,0.8);
        font-weight: 500;
    }
    
    .slot-action-buttons {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .slot-play-btn {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .slot-demo-btn {
        background: rgba(255,255,255,0.1);
        color: white;
        border: 2px solid rgba(255,255,255,0.3);
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .slot-play-btn:hover,
    .slot-demo-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .slot-details-section {
        padding-left: 20px;
    }
    
    .slot-info-title {
        font-size: 2rem;
        font-weight: bold;
        color: white;
        margin: 0 0 25px 0;
    }
    
    .slot-characteristics {
        background: rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .characteristic-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .characteristic-row:last-child {
        border-bottom: none;
    }
    
    .characteristic-label {
        font-weight: 600;
        color: rgba(255,255,255,0.9);
    }
    
    .characteristic-value {
        color: white;
        text-align: right;
    }
    
    .slot-description {
        background: rgba(255,255,255,0.05);
        border-radius: 12px;
        padding: 20px;
        color: rgba(255,255,255,0.9);
        line-height: 1.6;
    }
    
    /* Адаптивность */
    @media (max-width: 768px) {
        .slot-info-layout {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .slot-details-section {
            padding-left: 0;
        }
        
        .slot-info-title {
            font-size: 1.5rem;
        }
        
        .characteristic-row {
            grid-template-columns: 1fr;
            gap: 5px;
        }
        
        .characteristic-value {
            text-align: left;
            font-weight: bold;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}

// 6. Функция рендеринга демо блока
function render_slot_demo_block($post_id) {
    $demo_iframe = get_post_meta($post_id, '_slot_demo_iframe', true);
    
    if (empty($demo_iframe)) {
        return '<div style="text-align: center; padding: 50px; color: #666;"><p>URL iframe не настроен в админке</p></div>';
    }
    
    ob_start();
    ?>
    <div class="slot-demo-container">
        <div class="demo-header">
            <h2 class="demo-title">Демо: <?php echo esc_html(get_the_title($post_id)); ?></h2>
            <div class="demo-controls">
                <button class="demo-toggle active" onclick="showDemo()">Demo</button>
                <button class="demo-toggle" onclick="showInfo()">Info</button>
            </div>
        </div>
        
        <div id="demo-frame-container" class="demo-frame-container">
            <iframe src="<?php echo esc_url($demo_iframe); ?>" class="demo-iframe" frameborder="0" allowfullscreen></iframe>
        </div>
        
        <div id="demo-info-container" class="demo-info-container" style="display: none;">
            <div class="demo-info-content">
                <h3>Информация об игре</h3>
                <p>Здесь отображается информация о слоте вместо демо.</p>
                <?php echo render_slot_info_table($post_id); ?>
            </div>
        </div>
        
        <div class="demo-actions">
            <button class="demo-play-real" onclick="window.open('<?php echo esc_js($demo_iframe); ?>', '_blank')">
                ▶ Jugar con dinero real
            </button>
            <button class="demo-restart" onclick="reloadDemo()">
                ↻ Reiniciar Demo
            </button>
        </div>
    </div>
    
    <style>
    .slot-demo-container {
        color: white;
    }
    
    .demo-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .demo-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
        margin: 0;
    }
    
    .demo-controls {
        display: flex;
        gap: 10px;
    }
    
    .demo-toggle {
        padding: 8px 16px;
        border: 2px solid #8b5cf6;
        background: transparent;
        color: #8b5cf6;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .demo-toggle.active,
    .demo-toggle:hover {
        background: #8b5cf6;
        color: white;
    }
    
    .demo-frame-container {
        width: 100%;
        height: 500px;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    
    .demo-iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    
    .demo-info-container {
        background: rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 20px;
    }
    
    .demo-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
    }
    
    .demo-play-real {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .demo-restart {
        background: rgba(255,255,255,0.1);
        color: white;
        border: 2px solid rgba(255,255,255,0.3);
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .demo-play-real:hover,
    .demo-restart:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    @media (max-width: 768px) {
        .demo-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .demo-frame-container {
            height: 300px;
        }
        
        .demo-actions {
            flex-direction: column;
            align-items: center;
        }
        
        .demo-play-real,
        .demo-restart {
            width: 100%;
            max-width: 300px;
        }
    }
    </style>
    
    <script>
    function showDemo() {
        document.getElementById('demo-frame-container').style.display = 'block';
        document.getElementById('demo-info-container').style.display = 'none';
        document.querySelectorAll('.demo-toggle').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
    }
    
    function showInfo() {
        document.getElementById('demo-frame-container').style.display = 'none';
        document.getElementById('demo-info-container').style.display = 'block';
        document.querySelectorAll('.demo-toggle').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
    }
    
    function reloadDemo() {
        const iframe = document.querySelector('.demo-iframe');
        const currentSrc = iframe.src;
        iframe.src = '';
        setTimeout(() => {
            iframe.src = currentSrc;
        }, 100);
    }
    </script>
    <?php
    return ob_get_clean();
}

// 7. Вспомогательная функция для получения метаданных слота
function get_slot_meta($post_id, $key, $default = '') {
    $value = get_post_meta($post_id, '_slot_' . $key, true);
    return !empty($value) ? $value : $default;
}

// 8. Простая таблица с информацией (для демо блока)
// 8. Простая таблица с информацией (для демо блока)
function render_slot_info_table($post_id) {
    // Используем кастомные характеристики вместо старых полей
    $custom_characteristics = get_post_meta($post_id, '_slot_custom_characteristics', true);
    $characteristics = $custom_characteristics ? json_decode($custom_characteristics, true) : array();
    
    // Если кастомных характеристик нет, используем дефолтные
    if (empty($characteristics)) {
        $characteristics = array(
            array('label' => 'Proveedor', 'value' => 'InOut Games'),
            array('label' => 'RTP', 'value' => '98%'),
            array('label' => 'Volatilidad', 'value' => 'Variable'),
            array('label' => 'Fecha', 'value' => '11.11.2021')
        );
    }
    
    $html = '<div style="background: rgba(255,255,255,0.1); border-radius: 8px; padding: 15px; margin-top: 15px;">';
    foreach ($characteristics as $char) {
        $html .= '<div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">';
        $html .= '<span style="font-weight: 600;">' . esc_html($char['label']) . '</span>';
        $html .= '<span>' . esc_html($char['value']) . '</span>';
        $html .= '</div>';
    }
    $html .= '</div>';
    
    return $html;
}

// ==========================================
// SLOT ADMIN METABOXES - ПОЛНЫЙ ФУНКЦИОНАЛ
// ==========================================

// 9. Добавление метабоксов только для страниц со slot шаблоном
function add_slot_meta_boxes() {
    add_meta_box(
        'slot_content_settings',           // ID метабокса
        'Настройки контента слота',        // Заголовок
        'slot_content_meta_box_callback',  // Функция вывода
        'page',                            // Тип поста (страницы)
        'normal',                          // Расположение (под редактором)
        'high'                             // Приоритет
    );
    
    add_meta_box(
        'slot_characteristics',
        'Характеристики слота', 
        'slot_characteristics_meta_box_callback',
        'page',
        'normal',
        'default'
    );
}

// Показываем метабоксы только для slot шаблона
function show_slot_meta_boxes() {
    global $post;
    if ($post && get_page_template_slug($post->ID) === 'page-slot.php') {
        add_slot_meta_boxes();
    }
}
add_action('add_meta_boxes', 'show_slot_meta_boxes');

// 10. Метабокс для настройки типа контента
function slot_content_meta_box_callback($post) {
    wp_nonce_field('slot_meta_box', 'slot_meta_box_nonce');
    
    $content_type = get_post_meta($post->ID, '_slot_content_type', true) ?: 'title_only';
    $slot_image = get_post_meta($post->ID, '_slot_image', true);
    $demo_iframe = get_post_meta($post->ID, '_slot_demo_iframe', true);
    $slot_description = get_post_meta($post->ID, '_slot_description', true);
    
    // РАЗДЕЛЬНЫЕ настройки кнопок для INFO блока
    $info_play_button_text = get_post_meta($post->ID, '_slot_info_play_button_text', true) ?: 'Jugar con dinero real';
    $info_play_button_url = get_post_meta($post->ID, '_slot_info_play_button_url', true);
    $info_demo_button_text = get_post_meta($post->ID, '_slot_info_demo_button_text', true) ?: 'Jugar Demo';
    $info_demo_button_url = get_post_meta($post->ID, '_slot_info_demo_button_url', true);
    
    // РАЗДЕЛЬНЫЕ настройки кнопок для DEMO блока
    $demo_play_button_text = get_post_meta($post->ID, '_slot_demo_play_button_text', true) ?: 'Jugar con dinero real';
    $demo_play_button_url = get_post_meta($post->ID, '_slot_demo_play_button_url', true);
    $demo_demo_button_text = get_post_meta($post->ID, '_slot_demo_demo_button_text', true) ?: 'Reiniciar Demo';
    $demo_demo_button_url = get_post_meta($post->ID, '_slot_demo_demo_button_url', true);
    ?>
    
    <table class="form-table">
        <!-- Выбор типа контента -->
        <tr>
            <th scope="row">
                <label for="slot_content_type">Тип контента под заголовком</label>
            </th>
            <td>
                <select id="slot_content_type" name="slot_content_type" onchange="toggleSlotContentFields(this.value)">
                    <option value="title_only" <?php selected($content_type, 'title_only'); ?>>Только заголовок H1</option>
                    <option value="info" <?php selected($content_type, 'info'); ?>>Информация о слоте</option>
                    <option value="demo" <?php selected($content_type, 'demo'); ?>>Демо игры</option>
                </select>
            </td>
        </tr>
        
        <!-- Поля для информационного блока -->
        <tbody id="info_fields" style="<?php echo $content_type !== 'info' ? 'display: none;' : ''; ?>">
            <tr>
                <th scope="row">
                    <label for="slot_image">Изображение слота</label>
                </th>
                <td>
                    <div class="slot-image-upload">
                        <input type="hidden" id="slot_image" name="slot_image" value="<?php echo esc_url($slot_image); ?>">
                        <div class="image-preview" id="image-preview">
                            <?php if ($slot_image): ?>
                                <img src="<?php echo esc_url($slot_image); ?>" style="max-width: 200px; height: auto; border-radius: 8px;">
                            <?php else: ?>
                                <div style="width: 200px; height: 150px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: #666;">
                                    Изображение не выбрано
                                </div>
                            <?php endif; ?>
                        </div>
                        <p style="margin: 10px 0;">
                            <button type="button" class="button" id="upload-image-btn">Выбрать изображение</button>
                            <button type="button" class="button" id="remove-image-btn" <?php echo !$slot_image ? 'style="display:none;"' : ''; ?>>Удалить</button>
                        </p>
                    </div>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="slot_description">Описание слота</label>
                </th>
                <td>
                    <textarea id="slot_description" name="slot_description" rows="4" cols="50" class="large-text"><?php echo esc_textarea($slot_description); ?></textarea>
                </td>
            </tr>
            
            <!-- НАСТРОЙКИ КНОПОК ДЛЯ INFO БЛОКА -->
            <tr style="background: #f0f9ff;">
                <th colspan="2" style="padding: 15px; font-weight: bold; color: #0369a1;">
                    🔹 Настройки кнопок для информационного блока
                </th>
            </tr>
            <tr>
                <th scope="row">
                    <label for="slot_info_play_button_text">Текст кнопки "Играть" (INFO)</label>
                </th>
                <td>
                    <input type="text" id="slot_info_play_button_text" name="slot_info_play_button_text" value="<?php echo esc_attr($info_play_button_text); ?>" class="regular-text">
                    <p class="description">Текст основной кнопки в информационном блоке</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="slot_info_play_button_url">Ссылка кнопки "Играть" (INFO)</label>
                </th>
                <td>
                    <input type="url" id="slot_info_play_button_url" name="slot_info_play_button_url" value="<?php echo esc_url($info_play_button_url); ?>" class="large-text">
                    <p class="description">URL для основной кнопки в информационном блоке</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="slot_info_demo_button_text">Текст кнопки "Демо" (INFO)</label>
                </th>
                <td>
                    <input type="text" id="slot_info_demo_button_text" name="slot_info_demo_button_text" value="<?php echo esc_attr($info_demo_button_text); ?>" class="regular-text">
                    <p class="description">Текст демо кнопки в информационном блоке</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="slot_info_demo_button_url">Ссылка кнопки "Демо" (INFO)</label>
                </th>
                <td>
                    <input type="url" id="slot_info_demo_button_url" name="slot_info_demo_button_url" value="<?php echo esc_url($info_demo_button_url); ?>" class="large-text">
                    <p class="description">URL для демо кнопки в информационном блоке</p>
                </td>
            </tr>
        </tbody>
        
        <!-- Поля для демо блока -->
        <tbody id="demo_fields" style="<?php echo $content_type !== 'demo' ? 'display: none;' : ''; ?>">
            <tr>
                <th scope="row">
                    <label for="slot_demo_iframe">URL iframe для демо</label>
                </th>
                <td>
                    <input type="url" id="slot_demo_iframe" name="slot_demo_iframe" value="<?php echo esc_url($demo_iframe); ?>" class="large-text">
                    <p class="description">Ссылка на iframe демо-игры</p>
                </td>
            </tr>
            
            <!-- НАСТРОЙКИ КНОПОК ДЛЯ DEMO БЛОКА -->
            <tr style="background: #fdf2f8;">
                <th colspan="2" style="padding: 15px; font-weight: bold; color: #be185d;">
                    🎮 Настройки кнопок для демо блока
                </th>
            </tr>
            <tr>
                <th scope="row">
                    <label for="slot_demo_play_button_text">Текст кнопки "Играть" (DEMO)</label>
                </th>
                <td>
                    <input type="text" id="slot_demo_play_button_text" name="slot_demo_play_button_text" value="<?php echo esc_attr($demo_play_button_text); ?>" class="regular-text">
                    <p class="description">Текст основной кнопки в демо блоке</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="slot_demo_play_button_url">Ссылка кнопки "Играть" (DEMO)</label>
                </th>
                <td>
                    <input type="url" id="slot_demo_play_button_url" name="slot_demo_play_button_url" value="<?php echo esc_url($demo_play_button_url); ?>" class="large-text">
                    <p class="description">URL для основной кнопки в демо блоке</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="slot_demo_demo_button_text">Текст второй кнопки (DEMO)</label>
                </th>
                <td>
                    <input type="text" id="slot_demo_demo_button_text" name="slot_demo_demo_button_text" value="<?php echo esc_attr($demo_demo_button_text); ?>" class="regular-text">
                    <p class="description">Текст второй кнопки в демо блоке (например "Reiniciar Demo")</p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="slot_demo_demo_button_url">Ссылка второй кнопки (DEMO)</label>
                </th>
                <td>
                    <input type="url" id="slot_demo_demo_button_url" name="slot_demo_demo_button_url" value="<?php echo esc_url($demo_demo_button_url); ?>" class="large-text">
                    <p class="description">URL для второй кнопки в демо блоке (если нужна ссылка вместо JS)</p>
                </td>
            </tr>
        </tbody>
    </table>
    
    <!-- JavaScript -->
    <script>
		function toggleSlotContentFields(value) {
			const infoFields = document.getElementById('info_fields');
			const demoFields = document.getElementById('demo_fields');
			
			// Скрываем все блоки
			if (infoFields) {
				infoFields.style.display = 'none';
			}
			if (demoFields) {
				demoFields.style.display = 'none';
			}
			
			// Показываем нужный блок
			if (value === 'info' && infoFields) {
				infoFields.style.display = 'table-row-group';
			} else if (value === 'demo' && demoFields) {
				demoFields.style.display = 'table-row-group';
			}
			
			// Логирование для отладки
			console.log('Selected content type:', value);
			console.log('Info fields display:', infoFields ? infoFields.style.display : 'not found');
			console.log('Demo fields display:', demoFields ? demoFields.style.display : 'not found');
		}
    
    // Медиа загрузчик
    jQuery(document).ready(function($) {
        let mediaUploader;
        
        $('#upload-image-btn').click(function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: 'Выберите изображение слота',
                button: { text: 'Использовать это изображение' },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#slot_image').val(attachment.url);
                $('#image-preview').html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto; border-radius: 8px;">');
                $('#remove-image-btn').show();
            });
            
            mediaUploader.open();
        });
        
        $('#remove-image-btn').click(function(e) {
            e.preventDefault();
            $('#slot_image').val('');
            $('#image-preview').html('<div style="width: 200px; height: 150px; background: #f0f0f0; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; border-radius: 8px; color: #666;">Изображение не выбрано</div>');
            $(this).hide();
        });
    });
    </script>
    <?php
}



// 11. Метабокс для характеристик слота
function slot_characteristics_meta_box_callback($post) {
    // Получаем кастомные характеристики (JSON)
    $custom_characteristics = get_post_meta($post->ID, '_slot_custom_characteristics', true);
    $custom_characteristics = $custom_characteristics ? json_decode($custom_characteristics, true) : array();
    
    // Дефолтные характеристики если кастомных нет
    if (empty($custom_characteristics)) {
        $custom_characteristics = array(
            array('label' => 'Proveedor', 'value' => 'InOut Games'),
            array('label' => 'Tipo', 'value' => 'Máquina tragamonedas progresiva'),
            array('label' => 'Variabilidad', 'value' => 'Variable'),
            array('label' => 'Game Hero rtp', 'value' => '98%'),
            array('label' => 'Tarifa mínima', 'value' => '0,1'),
            array('label' => 'Máximo compromiso', 'value' => '200'),
            array('label' => 'Reproducción automática', 'value' => 'Sí'),
            array('label' => 'Fecha de lanzamiento', 'value' => '11.11.2021')
        );
    }
    ?>
    
    <div id="characteristics-container">
        <?php foreach ($custom_characteristics as $index => $char): ?>
            <div class="characteristic-row" style="display: flex; gap: 10px; margin-bottom: 15px; align-items: center;">
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">Название:</label>
                    <input type="text" 
                           name="characteristic_labels[]" 
                           value="<?php echo esc_attr($char['label']); ?>" 
                           class="regular-text"
                           style="width: 100%;"
                           placeholder="Название характеристики">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">Значение:</label>
                    <input type="text" 
                           name="characteristic_values[]" 
                           value="<?php echo esc_attr($char['value']); ?>" 
                           class="regular-text"
                           style="width: 100%;"
                           placeholder="Значение">
                </div>
                <div style="flex: 0 0 auto; margin-top: 25px;">
                    <button type="button" class="button button-secondary remove-characteristic" onclick="removeCharacteristic(this)">Удалить</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <p style="margin: 20px 0;">
        <button type="button" class="button button-primary" onclick="addCharacteristic()">+ Добавить характеристику</button>
    </p>
    
    <div style="margin-top: 20px; padding: 15px; background: #f0f9ff; border-left: 4px solid #0ea5e9; border-radius: 4px;">
        <h4 style="margin: 0 0 10px 0; color: #0369a1;">Как использовать:</h4>
        <p style="margin: 5px 0; font-size: 13px; color: #64748b;">
            <strong>Название:</strong> Как будет называться характеристика (например: "Провайдер", "RTP", "Волатильность")<br>
            <strong>Значение:</strong> Значение этой характеристики для данного слота<br>
            Вы можете добавлять/удалять характеристики и менять их названия для каждого слота индивидуально.
        </p>
    </div>
    
    <script>
    function addCharacteristic() {
        const container = document.getElementById('characteristics-container');
        const newRow = document.createElement('div');
        newRow.className = 'characteristic-row';
        newRow.style.cssText = 'display: flex; gap: 10px; margin-bottom: 15px; align-items: center;';
        
        newRow.innerHTML = `
            <div style="flex: 1;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">Название:</label>
                <input type="text" name="characteristic_labels[]" value="" class="regular-text" style="width: 100%;" placeholder="Название характеристики">
            </div>
            <div style="flex: 1;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">Значение:</label>
                <input type="text" name="characteristic_values[]" value="" class="regular-text" style="width: 100%;" placeholder="Значение">
            </div>
            <div style="flex: 0 0 auto; margin-top: 25px;">
                <button type="button" class="button button-secondary remove-characteristic" onclick="removeCharacteristic(this)">Удалить</button>
            </div>
        `;
        
        container.appendChild(newRow);
    }
    
    function removeCharacteristic(button) {
        if (document.querySelectorAll('.characteristic-row').length > 1) {
            button.closest('.characteristic-row').remove();
        } else {
            alert('Должна остаться хотя бы одна характеристика!');
        }
    }
    </script>
    <?php
}

// 12. Сохранение данных метабоксов (ОБНОВЛЕННАЯ ВЕРСИЯ)
function save_slot_meta_boxes($post_id) {
    // Проверка nonce
    if (!isset($_POST['slot_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['slot_meta_box_nonce'], 'slot_meta_box')) {
        return;
    }
    
    // Проверка автосохранения
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Проверка прав
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // ОТЛАДКА - добавим логирование
    error_log('=== SLOT META SAVE DEBUG ===');
    error_log('POST data: ' . print_r($_POST, true));
    
    // ОБНОВЛЕННЫЙ список полей с раздельными кнопками
    $fields = array(
        'slot_content_type',
        'slot_image', 
        'slot_description',
        'slot_demo_iframe',
        // INFO блок кнопки  
        'slot_info_play_button_text',
        'slot_info_play_button_url',
        'slot_info_demo_button_text', 
        'slot_info_demo_button_url',
        // DEMO блок кнопки
        'slot_demo_play_button_text',
        'slot_demo_play_button_url',
        'slot_demo_demo_button_text', 
        'slot_demo_demo_button_url'
    );
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            
            // Отладка каждого поля
            error_log("Saving {$field}: {$value}");
            
            if (strpos($field, '_url') !== false || $field === 'slot_image') {
                update_post_meta($post_id, '_' . $field, esc_url_raw($value));
            } elseif ($field === 'slot_description') {
                update_post_meta($post_id, '_' . $field, sanitize_textarea_field($value));
            } else {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($value));
            }
        } else {
            error_log("Field {$field} not found in POST");
        }
    }
    
    // Сохранение характеристик
	if (isset($_POST['characteristic_labels']) && isset($_POST['characteristic_values'])) {
		$labels = array_map('sanitize_text_field', $_POST['characteristic_labels']);
		$values = array_map('sanitize_text_field', $_POST['characteristic_values']);
		
		$characteristics = array();
		for ($i = 0; $i < count($labels); $i++) {
			if (!empty($labels[$i]) && !empty($values[$i])) {
				$characteristics[] = array(
					'label' => html_entity_decode($labels[$i], ENT_QUOTES, 'UTF-8'),
					'value' => html_entity_decode($values[$i], ENT_QUOTES, 'UTF-8')
				);
			}
		}
		
		update_post_meta($post_id, '_slot_custom_characteristics', json_encode($characteristics, JSON_UNESCAPED_UNICODE));
		error_log('Saved characteristics: ' . json_encode($characteristics));
	}
    
    error_log('=== END SLOT META SAVE DEBUG ===');
}

// ОБЯЗАТЕЛЬНО убедитесь что эта строка есть в конце functions.php:
add_action('save_post', 'save_slot_meta_boxes');


// 13. Подключение медиа-загрузчика в админке
function enqueue_slot_admin_scripts($hook) {
    global $post;
    
    if ($hook !== 'post-new.php' && $hook !== 'post.php') {
        return;
    }
    
    if (!$post || $post->post_type !== 'page') {
        return;
    }
    
    // Подключаем медиа-скрипты WordPress
    wp_enqueue_media();
    wp_enqueue_script('jquery');
}
add_action('admin_enqueue_scripts', 'enqueue_slot_admin_scripts');

// 14. Уведомление для пользователей
function slot_template_admin_notice() {
    global $post;
    
    if (!$post || get_page_template_slug($post->ID) !== 'page-slot.php') {
        return;
    }
    
    $content_type = get_post_meta($post->ID, '_slot_content_type', true);
    
    if (!$content_type || $content_type === 'title_only') {
        ?>
        <div class="notice notice-info is-dismissible">
            <p><strong>Слот-шаблон активен!</strong> Настройте тип контента в блоке "Настройки контента слота" ниже для отображения информации о слоте или демо-игры.</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'slot_template_admin_notice');


// 15. РЕГИСТРАЦИЯ МЕНЮ
function register_slot_menu() {
    register_nav_menus(array(
        'header-menu' => 'Главное меню (Бургер)',
    ));
}
add_action('init', 'register_slot_menu');

// ДЕФОЛТНОЕ МЕНЮ
function default_slot_menu() {
    echo '<ul class="main-menu">';
    echo '<li><a href="' . home_url() . '">🏠 Главная</a></li>';
    echo '<li><a href="' . home_url('/games/') . '">🎰 Игры</a></li>';
    echo '<li><a href="' . home_url('/bonuses/') . '">🎁 Бонусы</a></li>';
    echo '<li><a href="' . home_url('/about/') . '">ℹ️ О нас</a></li>';
    echo '<li><a href="' . home_url('/contact/') . '">📞 Контакты</a></li>';
    echo '</ul>';
}

// НОВЫЕ ФУНКЦИИ - ДОБАВИТЬ
function default_slot_menu_desktop() {
    echo '<ul class="desktop-menu">';
    echo '<li><a href="' . home_url() . '">🏠 Главная</a></li>';
    echo '<li><a href="' . home_url('/games/') . '">🎰 Игры</a></li>';
    echo '<li><a href="' . home_url('/bonuses/') . '">🎁 Бонусы</a></li>';
    echo '<li><a href="' . home_url('/about/') . '">ℹ️ О нас</a></li>';
    echo '<li><a href="' . home_url('/contact/') . '">📞 Контакты</a></li>';
    echo '</ul>';
}

function default_slot_menu_mobile() {
    echo '<ul class="main-menu">';
    echo '<li><a href="' . home_url() . '">🏠 Главная</a></li>';
    echo '<li><a href="' . home_url('/games/') . '">🎰 Игры</a></li>';
    echo '<li><a href="' . home_url('/bonuses/') . '">🎁 Бонусы</a></li>';
    echo '<li><a href="' . home_url('/about/') . '">ℹ️ О нас</a></li>';
    echo '<li><a href="' . home_url('/contact/') . '">📞 Контакты</a></li>';
    echo '</ul>';
}


// ==========================================
// CASINO LIST SHORTCODE
// ==========================================

function casino_list_shortcode($atts) {
    // Атрибуты шорткода с дефолтными значениями
    $atts = shortcode_atts(array(
        'limit' => -1,        // Количество казино (-1 = все)
        'ids' => '',          // Конкретные ID казино через запятую
        'title' => 'Mejores Casinos para Jugar [SLOT_NAME]'  // Заголовок секции
    ), $atts);
    
    // Параметры запроса
    $args = array(
        'post_type' => 'casinos',
        'post_status' => 'publish',
        'posts_per_page' => intval($atts['limit']),
        'orderby' => 'menu_order',
        'order' => 'ASC'
    );
    
    // Если указаны конкретные ID
    if (!empty($atts['ids'])) {
        $ids = explode(',', $atts['ids']);
        $args['post__in'] = array_map('intval', $ids);
        $args['orderby'] = 'post__in';
    }
    
    $casinos = new WP_Query($args);
    
    if (!$casinos->have_posts()) {
        return '<p>No hay casinos disponibles.</p>';
    }
    
    ob_start();
    ?>
    <div class="casino-section">

        
        <div class="casino-list">
            <?php 
            $rank = 1;
            while ($casinos->have_posts()): 
                $casinos->the_post();
                
                // Получаем ACF поля
                $logo_text = get_field('casino_logo_text') ?: get_the_title();
                $bonus = get_field('casino_bonus') ?: '100% Bono de Bienvenida';
                $rating = get_field('casino_rating') ?: '4.5';
                $stars = intval(get_field('casino_stars')) ?: 4;
                $button_text = get_field('casino_button_text') ?: 'JUGAR AHORA';
                $button_url = get_field('casino_button_url') ?: '#';
                $review_url = get_field('casino_review_url');
                
                // Featured Image
                $logo_image = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            ?>
                <div class="casino-card casino-card-v1">
                    <div class="casino-rank"><?php echo $rank; ?></div>
                    
                    <div class="casino-logo">
                        <?php if ($logo_image): ?>
                            <img src="<?php echo esc_url($logo_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="casino-logo-img">
                        <?php else: ?>
                            <?php echo esc_html($logo_text); ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="casino-info">
                        <div class="casino-name"><?php echo esc_html(get_the_title()); ?></div>
                        <div class="casino-bonus">🎁 <?php echo esc_html($bonus); ?></div>
                        <div class="casino-rating">
                            <div class="stars"><?php echo str_repeat('★', $stars) . str_repeat('☆', 5 - $stars); ?></div>
                            <div class="rating-text"><?php echo esc_html($rating); ?>/5</div>
                        </div>
                    </div>
                    
                    <div class="casino-action">
                        <a href="<?php echo esc_url($button_url); ?>" class="casino-button" target="_blank" rel="noopener">
                            <?php echo esc_html($button_text); ?>
                        </a>
                        <?php if ($review_url): ?>
                            <a href="<?php echo esc_url($review_url); ?>" class="casino-review" target="_blank" rel="noopener">Leer la reseña</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
                $rank++;
            endwhile; 
            wp_reset_postdata();
            ?>
        </div>
    </div>
    
    <style>
    .casino-section {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 0px;
        margin: 40px 0;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        backdrop-filter: blur(10px);
    }
    
    .casino-section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 30px;
        text-align: center;
        border-bottom: 3px solid #667eea;
        padding-bottom: 15px;
    }
    
    .casino-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .casino-card-v1 {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        display: grid;
        grid-template-columns: 50px 200px 1fr auto;
        gap: 20px;
        align-items: center;
    }
    
    .casino-card-v1:hover {
        border-left-color: #667eea;
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .casino-rank {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        flex-shrink: 0;
    }
    
    .casino-logo {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        height: 60px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        overflow: hidden;
        flex-shrink: 0;
    }
    
    .casino-logo-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .casino-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
    }
    
    .casino-name {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2d3748;
        line-height: 1.2;
    }
    
    .casino-bonus {
        color: #0369a1;
        font-weight: 500;
        font-size: 14px;
        line-height: 1.3;
    }
    
    .casino-rating {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }
    
    .stars {
        color: #fbbf24;
        font-size: 16px;
    }
    
    .rating-text {
        color: #6b7280;
        font-weight: 500;
    }
    
    .casino-action {
        min-width: 180px;
        flex-shrink: 0;
    }
    
    .casino-button {
        width: 100%;
        background: linear-gradient(135deg, #ff6b35, #f7931e);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
        margin-bottom: 8px;
        text-decoration: none;
        display: block;
        text-align: center;
        line-height: 1.2;
    }
    
    .casino-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .casino-review {
        display: block;
        text-align: center;
        color: #6b7280;
        text-decoration: underline;
        font-size: 12px;
        transition: color 0.3s ease;
    }
    
    .casino-review:hover {
        color: #667eea;
        text-decoration: underline;
    }
    
    /* Адаптивность */
    @media (max-width: 768px) {
        .casino-section {
            padding: 0px;
            margin: 20px 0;
        }
        
        .casino-section-title {
            font-size: 1.5rem;
        }
        
        .casino-card-v1 {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 15px;
            padding: 15px;
        }
        
        .casino-action {
            min-width: auto;
        }
        
        .casino-logo {
            width: 120px;
            height: 60px;
            margin: 0 auto;
        }
    }
    </style>
    <?php
    
    return ob_get_clean();
}
add_shortcode('casino_list', 'casino_list_shortcode');

// Дополнительный шорткод с атрибутами для удобства
function casino_top_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 5,
    ), $atts);
    
    return casino_list_shortcode($atts);
}
add_shortcode('casino_top', 'casino_top_shortcode');
?>