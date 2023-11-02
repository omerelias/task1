<?php
/*
Plugin Name: Star Wars Starships
Description: A plugin to display Star Wars starships from the SWAPI. Use the shortcode [star_wars_starships] to display the starships table on any page or post.
Version: 1.0
Author: Omer Elias
*/


function sws_enqueue_scripts() {
    
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
function sws_display_starships(){
    $starships = sws_fetch_starships();
    if (!$starships) {
        return 'Failed to fetch starships data.';
    }
    $data = '<table>';
    $data.= '<tr>
    <th>Name</th>
    <th>Class</th>
    <th>Crew</th>
    <th>Cost</th>
    </tr>';


    foreach ($starships as $starship) {
        $data.='<tr>';
        $data.='<td>'.$starship['name'].'</td>';
        $data.='<td>'.$starship['starship_class'].'</td>';
        $data.='<td>'.$starship['crew'].'</td>';
        $data.='<td>'.$starship['cost_in_credits'].'</td>';
        $data.='</tr>';
    }
    return $data;
}
add_shortcode('star_wars_starships', 'sws_display_starships');

?>