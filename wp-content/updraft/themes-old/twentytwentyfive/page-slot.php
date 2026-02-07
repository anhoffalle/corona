<?php
/*
Template Name: Slot Page
*/

// Получаем настройки из админки
$content_type = get_post_meta(get_the_ID(), '_slot_content_type', true) ?: 'title_only';
$slot_image = get_post_meta(get_the_ID(), '_slot_image', true);
$slot_description = get_post_meta(get_the_ID(), '_slot_description', true);
$demo_iframe = get_post_meta(get_the_ID(), '_slot_demo_iframe', true);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .slot-page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px; /* УБРАЛ большой верхний отступ */
        }

        /* Заголовок */
        .slot-title {
            text-align: center;
            color: white;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 50px;
            text-shadow: 0 4px 20px rgba(0,0,0,0.3);
            letter-spacing: -1px;
        }

        /* Информационный блок */
        .slot-info-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }

        .slot-info-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 40px;
            align-items: start;
        }

        .slot-image-section {
            text-align: center;
        }

        .slot-image-wrapper {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .slot-image {
            width: 100%;
            max-width: 250px;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .slot-image-placeholder {
            width: 250px;
            height: 250px;
            background: rgba(255,255,255,0.2);
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 3px dashed rgba(255,255,255,0.5);
            margin: 0 auto;
        }

        .placeholder-icon {
            font-size: 64px;
            margin-bottom: 15px;
        }

        .placeholder-text {
            color: white;
            font-weight: 600;
            font-size: 18px;
            text-align: center;
        }

        .slot-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .btn-play {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
        }

        .btn-demo {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
            padding: 15px 25px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-play:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.6);
        }

        .btn-demo:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .slot-details {
            padding-left: 20px;
        }

        .slot-name {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 30px;
            line-height: 1.2;
        }

        .slot-characteristics {
            background: #f8fafc;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }

        .characteristic-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .characteristic-row:last-child {
            border-bottom: none;
        }

        .characteristic-label {
            font-weight: 600;
            color: #4a5568;
            font-size: 15px;
        }

        .characteristic-value {
            font-weight: 500;
            color: #2d3748;
            font-size: 15px;
        }

        .slot-description {
            background: linear-gradient(135deg, #667eea10, #764ba210);
            border-radius: 15px;
            padding: 25px;
            color: #4a5568;
            line-height: 1.8;
            font-size: 16px;
            border: 1px solid #e2e8f0;
        }

        /* Demo блок */
        .slot-demo-section {
            background: rgba(26, 26, 46, 0.95);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            color: white;
        }

        .demo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .demo-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
        }

        .demo-controls {
            display: flex;
            gap: 10px;
        }

        .demo-toggle {
            padding: 10px 20px;
            border: 2px solid #8b5cf6;
            background: transparent;
            color: #8b5cf6;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .demo-toggle.active,
        .demo-toggle:hover {
            background: #8b5cf6;
            color: white;
            transform: translateY(-2px);
        }

        .demo-iframe-container {
            width: 100%;
            height: 600px;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        }

        .demo-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .demo-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .demo-btn {
            padding: 15px 30px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 16px;
        }

        .demo-btn-primary {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            border: none;
        }

        .demo-btn-secondary {
            background: transparent;
            color: #8b5cf6;
            border: 2px solid #8b5cf6;
        }

        .demo-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        /* Контент страницы */
        .slot-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }

        .slot-content h2,
        .slot-content h3 {
            color: #2d3748;
            margin-bottom: 20px;
        }

        .slot-content p {
            color: #4a5568;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        /* Адаптивность */
        @media (max-width: 768px) {
            .slot-page-container {
                padding: 80px 15px;
            }

            .slot-title {
                font-size: 2rem;
                margin-bottom: 30px;
            }

            .slot-info-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .slot-details {
                padding-left: 0;
            }

            .slot-name {
                font-size: 2rem;
                text-align: center;
            }

            .characteristic-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .demo-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .demo-iframe-container {
                height: 400px;
            }

            .demo-actions {
                flex-direction: column;
                align-items: center;
            }

            .demo-btn {
                width: 100%;
                max-width: 300px;
            }

                .site-header {
                padding: 15px 20px;
                margin-bottom: 20px;
            }
            
            .slot-page-container {
                padding: 15px;
            }
            
            /* СКРЫВАЕМ ДЕСКТОПНОЕ МЕНЮ */
            .desktop-menu {
                display: none;
            }
            
            /* ПОКАЗЫВАЕМ БУРГЕР И МОБИЛЬНОЕ МЕНЮ */
            .menu-toggle {
                display: flex;
            }
            
            .mobile-menu {
                display: block;
            }
            
            .slot-title {
                font-size: 2rem;
                margin-bottom: 30px;
            }
        }

        /* Скрытые элементы */
        .hidden {
            display: none;
        }

        .slot-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .slot-content table th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 15px;
        }

        .slot-content table td {
            padding: 12px 20px;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
        }

        .slot-content table tbody tr:hover {
            background: #f8fafc;
        }

        .slot-content table tbody tr:last-child td {
            border-bottom: none;
        }

        /* АВТОСТИЛИ ДЛЯ ВСЕХ СПИСКОВ */
        .slot-content ul,
        .slot-content ol {
            margin: 20px 0;
            padding-left: 30px;
        }

        .slot-content ul li,
        .slot-content ol li {
            margin-bottom: 10px;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            list-style: none;
            position: relative;
        }

        .slot-content ul li::before {
            content: "•";
            color: #667eea;
            font-size: 18px;
            font-weight: bold;
            position: absolute;
            left: -20px;
            top: 15px;
        }

        .slot-content ol {
            counter-reset: item-counter;
        }

        .slot-content ol li {
            counter-increment: item-counter;
        }

        .slot-content ol li::before {
            content: counter(item-counter);
            background: #667eea;
            color: white;
            font-weight: bold;
            font-size: 12px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            left: -25px;
            top: 12px;
        }

        /* ПРОСТАЯ АДАПТИВНОСТЬ */
        @media (max-width: 768px) {
            .slot-content table {
                font-size: 14px;
            }
            
            .slot-content table th,
            .slot-content table td {
                padding: 10px 12px;
            }
            
            .slot-content ul,
            .slot-content ol {
                padding-left: 20px;
            }
            
            .slot-content ul li,
            .slot-content ol li {
                padding: 10px 12px;
                margin-bottom: 8px;
            }
        }

        .site-header {
            position: relative;
            top: auto; /* УБРАНО */
            left: auto; /* УБРАНО */
            right: auto; /* УБРАНО */
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 20px 40px; /* УВЕЛИЧИЛ padding */
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px; /* ДОБАВИЛ отступ снизу */
        }

        /* ГОРИЗОНТАЛЬНОЕ МЕНЮ ДЛЯ ДЕСКТОПА */
        .desktop-menu {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 40px;
        }

        .desktop-menu li {
            position: relative;
        }

        .desktop-menu a {
            color: #4a5568;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .desktop-menu a:hover,
        .desktop-menu a.current-menu-item {
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .desktop-menu a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: #667eea;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .desktop-menu a:hover::after,
        .desktop-menu a.current-menu-item::after {
            width: 100%;
        }

        /* ПОДМЕНЮ ДЛЯ ДЕСКТОПА */
        .desktop-menu .sub-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            min-width: 200px;
            padding: 10px 0;
            margin-top: 10px;
        }

        .desktop-menu li:hover .sub-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .desktop-menu .sub-menu a {
            padding: 12px 20px;
            font-size: 14px;
            color: #6b7280;
            display: block;
        }

        .desktop-menu .sub-menu a:hover {
            background: #f8fafc;
            color: #667eea;
        }

        /* БУРГЕР КНОПКА (СКРЫТА НА ДЕСКТОПЕ) */
        .menu-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px;
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 30px;
            transition: all 0.3s ease;
        }

        .menu-toggle span {
            display: block;
            height: 3px;
            width: 100%;
            background: #667eea;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }

        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }

        /* МОБИЛЬНОЕ МЕНЮ (СКРЫТО НА ДЕСКТОПЕ) */
        .mobile-menu {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            margin: 0 20px; /* УБРАЛ верхний отступ */
            padding: 0; /* УБРАЛ отступы по умолчанию */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-height: 0; /* СКРЫТО ПО УМОЛЧАНИЮ */
            opacity: 0; /* ДОБАВИЛ */
            visibility: hidden; /* ДОБАВИЛ */
            transition: all 0.3s ease;
            display: none; /* СКРЫТО НА ДЕСКТОПЕ */
        }

        .mobile-menu.active {
            max-height: 500px;
            opacity: 1; /* ДОБАВИЛ */
            visibility: visible; /* ДОБАВИЛ */
            padding: 30px 20px;
        }

        .mobile-menu ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .mobile-menu li {
            margin-bottom: 5px;
        }

        .mobile-menu a {
            display: block;
            padding: 15px 20px;
            color: #4a5568;
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .mobile-menu a:hover,
        .mobile-menu a.current-menu-item {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            transform: translateX(10px);
        }

        .mobile-menu .sub-menu {
            padding-left: 20px;
            margin-top: 10px;
        }

        .mobile-menu .sub-menu a {
            font-size: 16px;
            padding: 12px 15px;
            color: #6b7280;
        }


        /* АДАПТИВНОСТЬ */
        @media (max-width: 768px) {


            .site-header {
                padding: 15px 20px;
                margin-bottom: 20px;
            }
            
            /* СКРЫВАЕМ ДЕСКТОПНОЕ МЕНЮ */
            .desktop-menu {
                display: none;
            }
            
            /* ПОКАЗЫВАЕМ БУРГЕР И МОБИЛЬНОЕ МЕНЮ */
            .menu-toggle {
                display: flex;
            }
            
            .mobile-menu {
                display: block; /* ТОЛЬКО НА МОБИЛЬНОМ */
            }
        }

    </style>
</head>

<body <?php body_class('slot-template-page'); ?>>
    <header class="site-header">
        <!-- ГОРИЗОНТАЛЬНОЕ МЕНЮ ДЛЯ ДЕСКТОПА -->
        <nav class="desktop-nav">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'header-menu',
                'menu_class' => 'desktop-menu',
                'container' => false,
                'fallback_cb' => 'default_slot_menu_desktop'
            ));
            ?>
        </nav>
        
        <!-- БУРГЕР КНОПКА ДЛЯ МОБАЙЛА -->
        <button class="menu-toggle" id="menu-toggle" aria-label="Toggle Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </header>

    <!-- МОБИЛЬНОЕ МЕНЮ -->
    <nav class="mobile-menu" id="mobile-menu">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'header-menu',
            'menu_class' => 'main-menu',
            'container' => false,
            'fallback_cb' => 'default_slot_menu_mobile'
        ));
        ?>
    </nav>


    <div class="slot-page-container">
        
        <?php while (have_posts()) : the_post(); ?>
            
            <!-- Заголовок страницы -->
            <h1 class="slot-title"><?php the_title(); ?></h1>

            <?php if ($content_type === 'info'): ?>
                <!-- Информационный блок о слоте -->
                <section class="slot-info-section">
                    <div class="slot-info-grid">
                        
                        <!-- Левая часть: изображение и кнопки -->
                        <div class="slot-image-section">
                            <?php if (!empty($slot_image)): ?>
                                <div class="slot-image-wrapper">
                                    <img src="<?php echo esc_url($slot_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="slot-image">
                                </div>
                            <?php else: ?>
                                <div class="slot-image-wrapper">
                                    <div class="slot-image-placeholder">
                                        <span class="placeholder-icon">🎰</span>
                                        <span class="placeholder-text"><?php echo esc_html(get_the_title()); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php
                            $info_play_button_text = get_post_meta(get_the_ID(), '_slot_info_play_button_text', true) ?: 'Jugar con dinero real';
                            $info_play_button_url = get_post_meta(get_the_ID(), '_slot_info_play_button_url', true) ?: '#';
                            $info_demo_button_text = get_post_meta(get_the_ID(), '_slot_info_demo_button_text', true) ?: 'Jugar Demo';
                            $info_demo_button_url = get_post_meta(get_the_ID(), '_slot_info_demo_button_url', true) ?: '#';
                            ?>

                            <div class="slot-buttons">
                                <a href="<?php echo esc_url($info_play_button_url); ?>" class="btn-play" target="_blank" rel="noopener">
                                    ▶ <?php echo esc_html($info_play_button_text); ?>
                                </a>
                                <a href="<?php echo esc_url($info_demo_button_url); ?>" class="btn-demo" target="_blank" rel="noopener">
                                    🎮 <?php echo esc_html($info_demo_button_text); ?>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Правая часть: информация -->
                        <div class="slot-details">
                            
                            <!-- Характеристики -->
                            <div class="slot-characteristics">
                                <?php
                                $custom_characteristics = get_post_meta(get_the_ID(), '_slot_custom_characteristics', true);
                                $characteristics = $custom_characteristics ? json_decode($custom_characteristics, true) : array();
                                
                                if (empty($characteristics)) {
                                    // Дефолтные если не настроено
                                    $characteristics = array(
                                        array('label' => 'Proveedor', 'value' => 'InOut Games'),
                                        array('label' => 'RTP', 'value' => '98%'),
                                    );
                                }
                                
                                foreach ($characteristics as $char):
                                ?>
                                    <div class="characteristic-row">
                                        <div class="characteristic-label"><?php echo esc_html($char['label']); ?></div>
                                        <div class="characteristic-value"><?php echo esc_html($char['value']); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if (!empty($slot_description)): ?>
                                <div class="slot-description">
                                    <?php echo wp_kses_post($slot_description); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                </section>
            
            <?php elseif ($content_type === 'demo' && !empty($demo_iframe)): ?>
                <!-- Демо блок -->
                <section class="slot-demo-section">
                    <div class="demo-header">

                        <div class="demo-controls">
                            <button class="demo-toggle active" onclick="showDemo()">Demo</button>
                            <button class="demo-toggle" onclick="showInfo()">Info</button>
                        </div>
                    </div>
                    
                    <div id="demo-frame-container" class="demo-iframe-container">
                        <iframe src="<?php echo esc_url($demo_iframe); ?>" class="demo-iframe" frameborder="0" allowfullscreen></iframe>
                    </div>
                    
                    <div id="demo-info-container" class="hidden">
                        <div class="slot-characteristics">
                            <?php
                            // Используем те же характеристики что и в info блоке
                            $custom_characteristics = get_post_meta(get_the_ID(), '_slot_custom_characteristics', true);
                            $characteristics = $custom_characteristics ? json_decode($custom_characteristics, true) : array();
                            
                            if (empty($characteristics)) {
                                // Дефолтные если не настроено
                                $characteristics = array(
                                    array('label' => 'Proveedor', 'value' => 'InOut Games'),
                                    array('label' => 'RTP', 'value' => '98%'),
                                    array('label' => 'Volatilidad', 'value' => 'Variable'),
                                    array('label' => 'Fecha', 'value' => '11.11.2021')
                                );
                            }
                            
                            foreach ($characteristics as $char):
                            ?>
                                <div class="characteristic-row">
                                    <div class="characteristic-label"><?php echo esc_html($char['label']); ?></div>
                                    <div class="characteristic-value"><?php echo esc_html($char['value']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <?php
                    
                    $demo_play_button_text = get_post_meta(get_the_ID(), '_slot_demo_play_button_text', true) ?: 'Jugar con dinero real';
                    $demo_play_button_url = get_post_meta(get_the_ID(), '_slot_demo_play_button_url', true) ?: '#';
                    $demo_demo_button_text = get_post_meta(get_the_ID(), '_slot_demo_demo_button_text', true) ?: 'Reiniciar Demo';
                    $demo_demo_button_url = get_post_meta(get_the_ID(), '_slot_demo_demo_button_url', true);
                    ?>

                    <div class="demo-actions">
                        <a href="<?php echo esc_url($demo_play_button_url); ?>" class="btn-play" target="_blank" rel="noopener">
                            ▶ <?php echo esc_html($demo_play_button_text); ?>
                        </a>
                        
                        <?php if (!empty($demo_demo_button_url)): ?>
                            <!-- Если есть URL - делаем ссылку -->
                            <a href="<?php echo esc_url($demo_demo_button_url); ?>" class="btn-demo" target="_blank" rel="noopener">
                                <?php echo esc_html($demo_demo_button_text); ?>
                            </a>
                        <?php else: ?>
                            <!-- Если нет URL - используем JS функцию для перезагрузки iframe -->
                            <button class="btn-demo" onclick="reloadDemo()">
                                <?php echo esc_html($demo_demo_button_text); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Основной контент страницы -->
            <?php if (get_the_content()): ?>
                <section class="slot-content">
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </section>
            <?php endif; ?>

        <?php endwhile; ?>
        
    </div>

    <script>
        function showDemo() {
            document.getElementById('demo-frame-container').classList.remove('hidden');
            document.getElementById('demo-info-container').classList.add('hidden');
            document.querySelectorAll('.demo-toggle').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }
        
        function showInfo() {
            document.getElementById('demo-frame-container').classList.add('hidden');
            document.getElementById('demo-info-container').classList.remove('hidden');
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
            
            event.target.textContent = '⟳ Reiniciando...';
            setTimeout(() => {
                event.target.textContent = '↻ Reiniciar Demo';
            }, 2000);
        }
    </script>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            function toggleMenu() {
                const isActive = mobileMenu.classList.contains('active');
                
                if (isActive) {
                    mobileMenu.classList.remove('active');
                    menuToggle.classList.remove('active');
                } else {
                    mobileMenu.classList.add('active');
                    menuToggle.classList.add('active');
                }
            }

            if (menuToggle) {
                menuToggle.addEventListener('click', toggleMenu);
            }

            // Закрытие при клике на ссылку
            const menuLinks = mobileMenu ? mobileMenu.querySelectorAll('a') : [];
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.remove('active');
                    menuToggle.classList.remove('active');
                });
            });
        });
    </script>


    <?php wp_footer(); ?>
</body>
</html>