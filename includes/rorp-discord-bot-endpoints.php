<?php
// tout les endpoints
//TODO: changer les endpoint pour des endpoint qui interagissent avec le bot

//Toutes les fonctions de sécurité
//demande l'utilisation du plugin de JWT pour fonctionner
function security_callback( \WP_REST_Request $request) {
  if( is_user_logged_in() ){
    $user = wp_get_current_user();
    $allowed_roles = array( 'editor', 'administrator');
    if ( array_intersect( $allowed_roles, $user->roles ) ) {
      return true;
    }
    else {
      return new \WP_Error( 'access_denied', 'cet utilisateur n\'a pas le droit de faire ça' );
    }
  }
  else{
    return new \WP_Error( 'access_denied', 'utilisateur non connecté' );
  }
}

function string_sanatizer($value){
	$clean = htmlspecialchars( $value );
  foreach (array("select", "where", "insert", "update", "delete", "alter", "drop") as $val){
    $clean = str_ireplace($val,"",$clean);
  }
  return $clean;
}

//Names
add_action( 'rest_api_init', function () {
  register_rest_route( 'rorp24-blocks/v1', '/name_generator/get_names', array(
    'methods' => 'GET',
    'callback' => 'rorp_api_name_generator_get_name',
    'permission_callback' => '__return_true',
    'args' => [
  		'race' => [
        'default'=>'all',
        'sanitize_callback' => 'string_sanatizer',
      ],
    	'gender' => [
        'default'=>3,
        'sanitize_callback' => 'string_sanatizer',
      ],
    	'offset' => [
        'default'=>0,
        'sanitize_callback' => 'string_sanatizer',
      ],
    	'limit' => [
        'default'=>10,
        'sanitize_callback' => 'string_sanatizer',
      ],
    	'random' => [
        'default'=>false,
        'sanitize_callback' => 'string_sanatizer',
      ],
      'tag'=> [
        'default'=>false,
        'sanitize_callback' => 'string_sanatizer',
      ],
  	]
  ) );
} );

function rorp_api_name_generator_get_name(\WP_REST_Request $request){
  global $wpdb;
  //gestion des paramètres
  $race = $request->get_param( 'race' );
  $gender = $request->get_param( 'gender' );
	$offset = $request->get_param( 'offset' );
  $limit = $request->get_param( 'limit' );
  $random = $request->get_param( 'random' );
  $tag = $request->get_param( 'tag' );
  
  $query = "SELECT * FROM rorp_API_generator_name ";
  //9 == angel
  if($race == 9 && $tag == 'generated'){
    return generate_angel_name($gender,$limit) ;
  }
  if($race != 'all' || $gender < 3){
    $query =  $query . "WHERE ";
    if($race != 'all'){
      $query =  $query . " race_id = " . $race . " ";
    }
    if($race != 'all' && $gender < 3){
      $query =  $query . " AND ";
    }
    if($gender < 3){
      $query =  $query . " (gender = '" . $gender . "' OR gender = '2')";
    }
  }

  if($random){
    $query = $query . " ORDER BY RAND() ";
  }
  $query = $query . " LIMIT " . strval($limit);
  if($offset != 0){
    $query = $query . " OFFSET " .  strval($offset) * strval($limit);
  }
	$results = $wpdb->get_results($query);
  return $results;
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'rorp24-blocks/v1', '/name_generator/create_name', array(
    'methods' => 'POST',
    'callback' => 'rorp_api_name_generator_create_name',
    'permission_callback' => 'security_callback',
    'args' =>[
  		'name' =>[
        'validate_callback' => function ( $value, \WP_REST_Request $request, $key ) {
            if ( ! is_string( $value ) ) {
              return new \WP_Error( 'empty', 'Not a string.' );
            }
    				if(strlen($value) < 1){
            	return new \WP_Error( 'empty', 'string is empty' );
            }
    				if(strlen($value) > 30){
            	return new \WP_Error( 'empty', 'It\'s a name not a fucking sentence!' );
            }

            return true;
          },
        'sanitize_callback' => 'string_sanatizer',
  		],
    	'race' =>[
        'validate_callback' => function ( $value, \WP_REST_Request $request, $key ) {
    				//TODO confirmer que la race existe en base
    				if ( ! is_numeric( $value ) ) {
              return new \WP_Error( 'post_id_invalid_format', 'Post ID should only contain digits.' );
            }
            return true;
          },
        'sanitize_callback' => 'string_sanatizer',
  		],
      'gender' =>[
        'validate_callback' => function ( $value, \WP_REST_Request $request, $key ) {
    				//TODO confirmer que le genre existe en base
    				if ( ! is_numeric( $value ) ) {
              return new \WP_Error( 'post_id_invalid_format', 'Post ID should only contain digits.' );
            }
            return true;
          },
          'sanitize_callback' => 'string_sanatizer',
  		],
  	]
  ) );
} );

function rorp_api_name_generator_create_name(\WP_REST_Request $request){
  global $wpdb;
  $name = $request->get_param( 'name' );
  $race = $request->get_param( 'race' );
  $gender = $request->get_param( 'gender' );

  $query = "INSERT INTO `rorp_API_generator_name`(`race_id`,`gender`,`name`) VALUES (" . strval($race) . ", '" . strval($gender) . "', '" . strval($name) . "')";
  $results = $wpdb->get_results($query);
  return $results;
}

?>
