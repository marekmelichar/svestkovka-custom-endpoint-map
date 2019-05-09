<?php

// https://www.sitepoint.com/creating-custom-endpoints-for-the-wordpress-rest-api/
// https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
// https://stackoverflow.com/questions/53126137/wordpress-rest-api-custom-endpoint-with-url-parameter






// get request for fetch train departures:
// https://svestkovka.marekmelichar.cz/wp-json/svestkovka/v1/map?from=1&to=8&date=2019-05-05


add_action('rest_api_init', function () {
  register_rest_route('svestkovka/v1', '/map', array(
    'methods' => 'GET',
    'callback' => 'get_map_func',
    'args' => array(
      
    ),
    'permission_callback' => function () {
      // return current_user_can( 'edit_others_posts' );
      return true;
    }
  ));
});

function get_map_func($data)
{
  $response = array();

  global $wpdb;

  $queryPois = "SELECT * FROM poi";
  $result_pois = $wpdb->get_results($queryPois);
  $responsePois = array();

  foreach ($result_pois as $item) {
    // var_dump($item);
    $responsePois[] = array(
      'id' => $item->id,
      'latitude' => $item->latitude,
      'longitude' => $item->longitude
    );
  }

  // get Stations:
  $queryStations = "CALL dejPlatneStaniceProDatum(null);";
  $result_stations = $wpdb->get_results($queryStations);
  $responseStations = array();

  foreach ($result_stations as $item) {
    // var_dump($item);
    $responseStations[] = array(
      'id' => $item->idStanice,
      'name' => $item->nazevStanice,
      'latitude' => $item->latitude,
      'longitude' => $item->longitude
    );
  }

  $response = array(
    'stops' => $responseStations,
    'places' => $responsePois
  );




  return new WP_REST_Response( $response, 200 );

  // echo json_encode($response);

  // echo "asdasdasd";

	wp_die(); // this is required to terminate immediately and return a proper response
}