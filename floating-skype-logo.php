<?php
/*
Plugin Name: Floating Skype Logo
Description: Adds a floating Skype logo to the site with customizable settings.
Version: 1.0
Author: Ramzi Karkoub
*/

// Enqueue the CSS and JavaScript files
function floating_skype_logo_enqueue_scripts() {
    wp_enqueue_style('floating-skype-logo-style', plugin_dir_url(__FILE__) . 'css/floating-skype-logo.css');
    wp_enqueue_script('floating-skype-logo-script', plugin_dir_url(__FILE__) . 'js/floating-skype-logo.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'floating_skype_logo_enqueue_scripts');

// Add the floating Skype logo to the footer
function floating_skype_logo() {
    $options = get_option('floating_skype_logo_options');
    $display = isset($options['display']) ? esc_attr($options['display']) : 'yes';
    if ($display === 'no') {
        return;
    }

    $skype_id = isset($options['skype_id']) ? esc_attr($options['skype_id']) : '';
    $position = isset($options['position']) ? esc_attr($options['position']) : 'bottom-right';
    $vertical_margin = isset($options['vertical_margin']) ? intval($options['vertical_margin']) : 20;
    $horizontal_margin = isset($options['horizontal_margin']) ? intval($options['horizontal_margin']) : 20;
    $logo = isset($options['logo']) ? esc_attr($options['logo']) : 'skype-logo.png';
    $logo_size = isset($options['logo_size']) ? intval($options['logo_size']) : 50;
    $hover_effect = isset($options['hover_effect']) ? esc_attr($options['hover_effect']) : 'scale';

    $position_styles = [
        'top-left' => 'top: ' . $vertical_margin . 'px; left: ' . $horizontal_margin . 'px;',
        'top-right' => 'top: ' . $vertical_margin . 'px; right: ' . $horizontal_margin . 'px;',
        'bottom-left' => 'bottom: ' . $vertical_margin . 'px; left: ' . $horizontal_margin . 'px;',
        'bottom-right' => 'bottom: ' . $vertical_margin . 'px; right: ' . $horizontal_margin . 'px;',
    ];

    $hover_style = $hover_effect === 'rotate' ? 'transform: rotate(15deg);' : 'transform: scale(1.1);';

    echo '<style>
            #floating-skype-logo img {
                width: ' . $logo_size . 'px;
                height: ' . $logo_size . 'px;
            }
            #floating-skype-logo img:hover {
                ' . $hover_style . '
            }
          </style>';
    echo '<div id="floating-skype-logo" style="position: fixed; ' . $position_styles[$position] . '">
            <a href="skype:' . $skype_id . '?call">
                <img src="' . esc_url(plugin_dir_url(__FILE__) . 'images/' . $logo) . '" alt="Skype">
            </a>
          </div>';
}
add_action('wp_footer', 'floating_skype_logo');

// Add settings menu to the admin dashboard
function floating_skype_logo_menu() {
    add_options_page(
        'Floating Skype Logo Settings',
        'Floating Skype Logo',
        'manage_options',
        'floating-skype-logo',
        'floating_skype_logo_settings_page'
    );
}
add_action('admin_menu', 'floating_skype_logo_menu');

// Display settings page
function floating_skype_logo_settings_page() {
    ?>
    <div class="wrap">
        <h1>Floating Skype Logo Settings</h1>
        <h2 class="nav-tab-wrapper">
            <a href="#main-settings" class="nav-tab nav-tab-active">Main Settings</a>
            <a href="#desktop-settings" class="nav-tab">Desktop Settings</a>
            <a href="#mobile-settings" class="nav-tab">Mobile Settings</a>
        </h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('floating_skype_logo_options_group');
            ?>
            <div id="main-settings" class="settings-tab">
                <?php
                do_settings_sections('floating-skype-logo-main');
                ?>
            </div>
            <div id="desktop-settings" class="settings-tab" style="display: none;">
                <?php
                do_settings_sections('floating-skype-logo-desktop');
                ?>
            </div>
            <div id="mobile-settings" class="settings-tab" style="display: none;">
                <?php
                do_settings_sections('floating-skype-logo-mobile');
                ?>
            </div>
            <?php submit_button(); ?>
        </form>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('.nav-tab').click(function(e) {
            e.preventDefault();
            var tab_id = $(this).attr('href');
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            $('.settings-tab').hide();
            $(tab_id).show();
        });
        // Show the first tab by default
        $('.nav-tab-active').trigger('click');
    });
    </script>
    <?php
}

// Register and define the settings
function floating_skype_logo_settings() {
    register_setting('floating_skype_logo_options_group', 'floating_skype_logo_options', 'floating_skype_logo_sanitize_options');

    add_settings_section('floating_skype_logo_main_section', 'Main Settings', null, 'floating-skype-logo-main');
    add_settings_field('skype_id', 'Skype ID or Phone Number', 'floating_skype_logo_id_field', 'floating-skype-logo-main', 'floating_skype_logo_main_section');
    add_settings_field('logo', 'Select Skype Logo', 'floating_skype_logo_logo_field', 'floating-skype-logo-main', 'floating_skype_logo_main_section');

    add_settings_section('floating_skype_logo_desktop_section', 'Desktop Settings', null, 'floating-skype-logo-desktop');
    add_settings_field('display', 'Display Skype Logo', 'floating_skype_logo_display_field', 'floating-skype-logo-desktop', 'floating_skype_logo_desktop_section');
    add_settings_field('position', 'Button Position', 'floating_skype_logo_position_field', 'floating-skype-logo-desktop', 'floating_skype_logo_desktop_section');
    add_settings_field('vertical_margin', 'Vertical Margin (px)', 'floating_skype_logo_vertical_margin_field', 'floating-skype-logo-desktop', 'floating_skype_logo_desktop_section');
    add_settings_field('horizontal_margin', 'Horizontal Margin (px)', 'floating_skype_logo_horizontal_margin_field', 'floating-skype-logo-desktop', 'floating_skype_logo_desktop_section');
    add_settings_field('logo_size', 'Logo Size (px)', 'floating_skype_logo_logo_size_field', 'floating-skype-logo-desktop', 'floating_skype_logo_desktop_section');
    add_settings_field('hover_effect', 'Hover Effect', 'floating_skype_logo_hover_effect_field', 'floating-skype-logo-desktop', 'floating_skype_logo_desktop_section');

    add_settings_section('floating_skype_logo_mobile_section', 'Mobile Settings', null, 'floating-skype-logo-mobile');
    add_settings_field('mobile_display', 'Display Skype Logo', 'floating_skype_logo_mobile_display_field', 'floating-skype-logo-mobile', 'floating_skype_logo_mobile_section');
    add_settings_field('mobile_position', 'Button Position', 'floating_skype_logo_mobile_position_field', 'floating-skype-logo-mobile', 'floating_skype_logo_mobile_section');
    add_settings_field('mobile_vertical_margin', 'Vertical Margin (px)', 'floating_skype_logo_mobile_vertical_margin_field', 'floating-skype-logo-mobile', 'floating_skype_logo_mobile_section');
    add_settings_field('mobile_horizontal_margin', 'Horizontal Margin (px)', 'floating_skype_logo_mobile_horizontal_margin_field', 'floating-skype-logo-mobile', 'floating_skype_logo_mobile_section');
    add_settings_field('mobile_logo_size', 'Logo Size (px)', 'floating_skype_logo_mobile_logo_size_field', 'floating-skype-logo-mobile', 'floating_skype_logo_mobile_section');
    add_settings_field('mobile_hover_effect', 'Hover Effect', 'floating_skype_logo_mobile_hover_effect_field', 'floating-skype-logo-mobile', 'floating_skype_logo_mobile_section');
}
add_action('admin_init', 'floating_skype_logo_settings');

// Sanitize settings
function floating_skype_logo_sanitize_options($input) {
    $output = array();
    $output['skype_id'] = sanitize_text_field($input['skype_id']);
    $output['logo'] = sanitize_text_field($input['logo']);
    $output['display'] = sanitize_text_field($input['display']);
    $output['position'] = sanitize_text_field($input['position']);
    $output['vertical_margin'] = absint($input['vertical_margin']);
    $output['horizontal_margin'] = absint($input['horizontal_margin']);
    $output['logo_size'] = absint($input['logo_size']);
    $output['hover_effect'] = sanitize_text_field($input['hover_effect']);

    // Mobile settings
    $output['mobile_display'] = sanitize_text_field($input['mobile_display']);
    $output['mobile_position'] = sanitize_text_field($input['mobile_position']);
    $output['mobile_vertical_margin'] = absint($input['mobile_vertical_margin']);
    $output['mobile_horizontal_margin'] = absint($input['mobile_horizontal_margin']);
    $output['mobile_logo_size'] = absint($input['mobile_logo_size']);
    $output['mobile_hover_effect'] = sanitize_text_field($input['mobile_hover_effect']);

    return $output;
}

// Field callback functions
function floating_skype_logo_id_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['skype_id']) ? esc_attr($options['skype_id']) : '';
    echo '<input type="text" name="floating_skype_logo_options[skype_id]" value="' . $value . '" />';
}

function floating_skype_logo_logo_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['logo']) ? esc_attr($options['logo']) : '';
    // Display available logos (e.g., through a file picker or hardcoded options)
    echo '<select name="floating_skype_logo_options[logo]">
            <option value="skype-logo.png"' . selected($value, 'skype-logo.png', false) . '>Skype Logo</option>
            <!-- Add more options as needed -->
          </select>';
}

function floating_skype_logo_display_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['display']) ? esc_attr($options['display']) : 'yes';
    echo '<select name="floating_skype_logo_options[display]">
            <option value="yes"' . selected($value, 'yes', false) . '>Yes</option>
            <option value="no"' . selected($value, 'no', false) . '>No</option>
          </select>';
}

function floating_skype_logo_position_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['position']) ? esc_attr($options['position']) : 'bottom-right';
    echo '<select name="floating_skype_logo_options[position]">
            <option value="top-left"' . selected($value, 'top-left', false) . '>Top Left</option>
            <option value="top-right"' . selected($value, 'top-right', false) . '>Top Right</option>
            <option value="bottom-left"' . selected($value, 'bottom-left', false) . '>Bottom Left</option>
            <option value="bottom-right"' . selected($value, 'bottom-right', false) . '>Bottom Right</option>
          </select>';
}

function floating_skype_logo_vertical_margin_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['vertical_margin']) ? absint($options['vertical_margin']) : '';
    echo '<input type="number" name="floating_skype_logo_options[vertical_margin]" value="' . $value . '" />';
}

function floating_skype_logo_horizontal_margin_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['horizontal_margin']) ? absint($options['horizontal_margin']) : '';
    echo '<input type="number" name="floating_skype_logo_options[horizontal_margin]" value="' . $value . '" />';
}

function floating_skype_logo_logo_size_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['logo_size']) ? absint($options['logo_size']) : '';
    echo '<input type="number" name="floating_skype_logo_options[logo_size]" value="' . $value . '" />';
}

function floating_skype_logo_hover_effect_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['hover_effect']) ? esc_attr($options['hover_effect']) : 'scale';
    echo '<select name="floating_skype_logo_options[hover_effect]">
            <option value="scale"' . selected($value, 'scale', false) . '>Scale</option>
            <option value="rotate"' . selected($value, 'rotate', false) . '>Rotate</option>
          </select>';
}

// Mobile settings fields
function floating_skype_logo_mobile_display_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['mobile_display']) ? esc_attr($options['mobile_display']) : 'yes';
    echo '<select name="floating_skype_logo_options[mobile_display]">
            <option value="yes"' . selected($value, 'yes', false) . '>Yes</option>
            <option value="no"' . selected($value, 'no', false) . '>No</option>
          </select>';
}

function floating_skype_logo_mobile_position_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['mobile_position']) ? esc_attr($options['mobile_position']) : 'bottom-right';
    echo '<select name="floating_skype_logo_options[mobile_position]">
            <option value="top-left"' . selected($value, 'top-left', false) . '>Top Left</option>
            <option value="top-right"' . selected($value, 'top-right', false) . '>Top Right</option>
            <option value="bottom-left"' . selected($value, 'bottom-left', false) . '>Bottom Left</option>
            <option value="bottom-right"' . selected($value, 'bottom-right', false) . '>Bottom Right</option>
          </select>';
}

function floating_skype_logo_mobile_vertical_margin_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['mobile_vertical_margin']) ? absint($options['mobile_vertical_margin']) : '';
    echo '<input type="number" name="floating_skype_logo_options[mobile_vertical_margin]" value="' . $value . '" />';
}

function floating_skype_logo_mobile_horizontal_margin_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['mobile_horizontal_margin']) ? absint($options['mobile_horizontal_margin']) : '';
    echo '<input type="number" name="floating_skype_logo_options[mobile_horizontal_margin]" value="' . $value . '" />';
}

function floating_skype_logo_mobile_logo_size_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['mobile_logo_size']) ? absint($options['mobile_logo_size']) : '';
    echo '<input type="number" name="floating_skype_logo_options[mobile_logo_size]" value="' . $value . '" />';
}

function floating_skype_logo_mobile_hover_effect_field() {
    $options = get_option('floating_skype_logo_options');
    $value = isset($options['mobile_hover_effect']) ? esc_attr($options['mobile_hover_effect']) : 'scale';
    echo '<select name="floating_skype_logo_options[mobile_hover_effect]">
            <option value="scale"' . selected($value, 'scale', false) . '>Scale</option>
            <option value="rotate"' . selected($value, 'rotate', false) . '>Rotate</option>
          </select>';
}
