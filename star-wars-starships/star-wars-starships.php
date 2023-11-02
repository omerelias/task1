<?php
/*
Plugin Name: Star Wars Starships
Description: A plugin to display Star Wars starships from the SWAPI.
Version: 1.0
Author: Omer Elias
*/


function sws_enqueue_scripts() {
    
}
add_action('wp_enqueue_scripts', 'sws_enqueue_scripts');

function sws_fetch_starships(){
    $url='https://swapi.dev/api/starships/';
    $response = wp_remote_get($url);
    $api_data = wp_remote_retrieve_body($response);
    var_dump($api_data);
}
add_action('init','sws_fetch_starships'); 
?>