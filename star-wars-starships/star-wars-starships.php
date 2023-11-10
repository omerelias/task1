<?php
/*
Plugin Name: Star Wars Starships
Description: A plugin to display Star Wars starships from the SWAPI.
Version: 1.0
Author: Omer Elias
*/

// Enqueue scripts and styles
function sws_enqueue_scripts() {
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
}
add_action('wp_enqueue_scripts', 'sws_enqueue_scripts'); 

// Fetch Starships data
function sws_fetch_starships(){
    $url='https://swapi.dev/api/starships/';
    $response = wp_remote_get($url);
    $api_data = wp_remote_retrieve_body($response);
    
    if (is_wp_error($api_data)) {
        return null;
    }

    $starships = json_decode($api_data, true)['results'];
    return $starships;
}

// Display Fetched data
function sws_display_starships(){
    $starships = sws_fetch_starships();
    if (!$starships) {
        return 'Failed to fetch starships data.';
    }

    $data = '<table class="table table-bordered table-hover">';
    $data .= '<thead class="thead-light"><tr><th>Name</th><th>Class</th><th>Crew</th><th>Cost in Credits</th></tr></thead>';
    $data .= '<tbody>';

    foreach ($starships as $ship) {
        $data .= '<tr>';
        $data .= '<td>' . esc_html($ship['name']) . '</td>';
        $data .= '<td>' . esc_html($ship['starship_class']) . '</td>';
        $data .= '<td>' . esc_html($ship['crew']) . '</td>';
        $data .= '<td>' . esc_html($ship['cost_in_credits']) . '</td>';
        $data .= '</tr>';
    }

    $data .= '</tbody></table>';
    return $data;
}

// Admin menu settings
function sws_add_admin_menu() {
    add_menu_page('Star Wars Starships Settings', 'Star Wars Settings', 'manage_options', 'star_wars_starships', 'sws_settings_page');
}
add_action('admin_menu', 'sws_add_admin_menu');

function sws_settings_page() {
    ?>
    <div class="wrap">
    <h1>Star Wars Plugin Settings</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('sws_plugin_settings');
        do_settings_sections('sws_plugin_settings');
        submit_button();
        ?>
    </form>
    </div>
    <?php
}

function sws_settings_init() {
    register_setting('sws_plugin_settings', 'sws_settings');

    add_settings_section(
        'sws_plugin_settings_section',
        __('Select Page to Display Starships', 'wordpress'),
        'sws_settings_section_callback',
        'sws_plugin_settings'
    );

    add_settings_field(
        'sws_select_page',
        __('Select Page', 'wordpress'),
        'sws_select_page_render',
        'sws_plugin_settings',
        'sws_plugin_settings_section'
    );
    
    add_settings_field(
        'sws_select_type',
        __('Select data location:', 'wordpress'),
        'sws_select_type_render',
        'sws_plugin_settings',
        'sws_plugin_settings_section'
    );
}
add_action('admin_init', 'sws_settings_init');

function sws_settings_section_callback() {
    echo __('Please select the page where you want to display the Starships data.', 'wordpress');
}

function sws_select_page_render() {
    $options = get_option('sws_settings');
    ?>
    <select name="sws_settings[sws_select_page]">
        <?php
        $pages = get_pages();
        foreach ($pages as $page) {
            $selected = (isset($options['sws_select_page']) && $options['sws_select_page'] === $page->ID) ? 'selected' : '';
            echo '<option value="' . $page->ID . '" ' . $selected . '>' . $page->post_title . '</option>';
        }
        ?>
    </select>
    <?php
}

function sws_select_type_render() {
    $options = get_option('sws_settings');
    $type_options=['shortcode'=>'As a shortcode','content'=>'After The content'];
    echo '<select name="sws_settings[sws_select_type]">';
    foreach ($type_options as $value => $label) {
    $selected = (isset($options['sws_select_type']) && $options['sws_select_type'] === $value) ? 'selected' : '';
    echo '<option value="' . $value . '" '.$selected.'>' . $label . '</option>';
    }
    echo '</select>';
}


function sws_insert_starships_into_page($content) {
    $options = get_option('sws_settings');
    if (is_page($options['sws_select_page'])) {
        $starships_table = sws_display_starships();
        $content .= $starships_table;
    }
    return $content;
}

$type_option = get_option('sws_settings')['sws_select_type'];

switch ($type_option) {
    case 'content':
        add_filter('the_content', 'sws_insert_starships_into_page');
        break;
    case 'shortcode':
        add_shortcode('star_wars_starships', 'sws_display_starships');
        break;
    default:
        break;
}


?>
