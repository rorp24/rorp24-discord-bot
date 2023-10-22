<?php
// tout les endpoints
include __DIR__ .'/../discord-handling/interaction.php';

use Discord\InteractionType;
use Discord\InteractionResponseType;
use Discord\BotPrivateData;

//Interactions
//PS: OUBLIE PAS QUE C'EST DU POST ET DONC QUE TU DOIT UTILISER POSTMAN, ABRUTI
add_action( 'rest_api_init', function () {
  register_rest_route( 'rorp24-discord-bot/v1', '/interactions', array(
    'methods' => 'POST',
    'callback' => 'rorp_discord_bot_interaction_endpoint',
    'permission_callback' => '__return_true',
  ) );
} );

function rorp_discord_bot_interaction_endpoint(\WP_REST_Request $request){
  // global $wpdb;
  //gestion des paramètres
  $params = $request->get_params();
  $headers = $request->get_headers();
  error_log('params:'.json_encode($params));
  
  //gestion du cas où discord veux faire une vérification
  $response = new WP_REST_Response();
  $verificationResult = discord_endpoint_verify($headers,file_get_contents('php://input'),BotPrivateData::BOT_PUBLIC_KEY);
  $response->set_data($verificationResult['payload']);
  $response->set_status($verificationResult['status']);

  //handle slash command sent
  if($params['type'] == InteractionType::APPLICATION_COMMAND){
    if(isset($params['data'])){
      $data = $params['data'];
      error_log("data:".json_encode($params['data']));

      if($data['name']=='test'){
        $response->set_data(array(
          'type'=>InteractionResponseType::CHANNEL_MESSAGE_WITH_SOURCE,
          'data'=>array(
            'content'=>"le bot du rôliste flemmard fonctionne!"
          ),
        ));
      }
    }
    else {
      error_log("qu'est ce qui se passe ici?");
    }
  }

  return $response;
}

function discord_endpoint_verify(array $headers, string $payload, string $publicKey): array
{
    if (
        !isset($headers['x_signature_ed25519'])
        || !isset($headers['x_signature_timestamp'])
    ){
      return ['status' => 401, 'payload' => null];
    }

    $signature = $headers['x_signature_ed25519'][0];
    $timestamp = $headers['x_signature_timestamp'][0];

    if (!trim($signature, '0..9A..Fa..f') == '')
        return ['status' => 401, 'payload' => null];

    $message = $timestamp . $payload;
    $binarySignature = sodium_hex2bin($signature);
    $binaryKey = sodium_hex2bin($publicKey);

    if (!sodium_crypto_sign_verify_detached($binarySignature, $message, $binaryKey))
        return ['status' => 401, 'payload' => null];

    $payload = json_decode($payload, true);
    switch ($payload['type']) {
        case InteractionType::PING:
            return ['status' => 200, 'payload' => ['type' => InteractionResponseType::PONG]];
        case InteractionType::APPLICATION_COMMAND:
            return ['status' => 200, 'payload' => ['type' => 2]];
        default:
            return ['status' => 400, 'payload' => null];
    }
}

?>
