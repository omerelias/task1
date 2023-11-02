<?php
/*
Plugin Name: Star Wars Starships
Description: A plugin to display Star Wars starships from the SWAPI. Use the shortcode [star_wars_starships] to display the starships table on any page or post.
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
add_shortcode('star_wars_starships', 'sws_display_starships');

?>