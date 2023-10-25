<?php
// tout les endpoints
include __DIR__ .'/../discord-handling/interaction.php';

use Discord\InteractionType;
use Discord\InteractionResponseType;
use Discord\BotPrivateData;

//Interactions
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
  //error_log('params:'.json_encode($params));
  
  //gestion du cas où discord veux faire une vérification
  $response = new WP_REST_Response();
  $verificationResult = discord_endpoint_verify($headers,file_get_contents('php://input'),BotPrivateData::BOT_PUBLIC_KEY);
  $response->set_data($verificationResult['payload']);
  $response->set_status($verificationResult['status']);

  //handle slash command sent
  if($params['type'] == InteractionType::APPLICATION_COMMAND){
    if(isset($params['data'])){
      $data = $params['data'];
      //error_log("data:".json_encode($params['data']));
      
      $content ='';
      $type = 0;

      switch ($data['name']) {
        case 'roll':
          $options = $data['options'];
          $option_des = $options[array_search('des', array_column($options,'name'))];
          
          //Vu qu'on prend le risque de devoir exécuter un peu de code, on s'assure que l'utilisateur ne nous envoi rien de dangereux avant
          //en restreignant la manière dont il peut nous envoyer les choses et la longueur, évitant quelques pirouettes malhonnête
          if(preg_match('/^[1-9][0-9]*d[1-9][0-9]*([\+\-\*\/][0-9]+)*$/i',$option_des['value']) && strlen($option_des['value']) <101 ){
            $dices = preg_split('/([d\+\-\*\/]+[^d\+\-\*\/]+)/i',$option_des['value'],-1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
            //on considère que si l'utilisateur veux lancer plus de 101 dé, il abuse
            if(intval($dices[0])<101){
              $dices[1] = preg_replace('/d/i','',$dices[1]);
              $parenthesis_content = "(";
              $result=0;
              for ($i=0; $i <intval($dices[0]); $i++) { 
                $dice_result = random_int(1,intval($dices[1]));
                $result += $dice_result;
                if($i>0){
                  $parenthesis_content .= ', ';
                }
                $parenthesis_content .= $dice_result;
              }
              $parenthesis_content .= ")";
  
              //on remet la partie à interpréter sous forme de string
              $interpreter = '';
              for($i=2;$i<count($dices);$i++){
                $interpreter .= $dices[$i];
              }
              $result = 0 + eval('return '.$result.$interpreter.';');
              $content = 'lance '.$option_des['value'].' : '.$result.' '.$parenthesis_content.$interpreter;
            }
            else{
              $content = 'Vous lancez trop de dé à la fois. Merci de ne pas lancer plus de 100 dés. Votre commande: \/roll '.$option_des['value'];
            }
          }
          else{
            $content = "la commande doit être écrite de cette manière:\/roll XdY+Z. Vous l'avez écrit de cette manière, qui ne fonctionne pas: \/roll ".$option_des['value'];
          }
          $type = InteractionResponseType::CHANNEL_MESSAGE_WITH_SOURCE;
          break;
        default:
          $type = InteractionResponseType::CHANNEL_MESSAGE_WITH_SOURCE;
          $content = "Commande non reconnu: ".$data['name'];
          error_log($content);
          break;
      }
    }
    else {
      error_log("qu'est ce qui se passe ici?");
      error_log('params:'.json_encode($params));
    }
    $response->set_data(array(
      'type'=>$type,
      'data'=>array(
        'content'=>$content
      ),
    ));
  }
  return $response;
}

//fonction pour renvoyer la bonne réponse à discord quand il veux tester le nouveau endpoint
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
