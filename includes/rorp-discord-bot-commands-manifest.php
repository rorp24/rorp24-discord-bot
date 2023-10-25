<?php

include __DIR__ .'/../discord-handling/application.php';

use Discord\BotPrivateData;
use Discord\ApplicationCommandType;
use Discord\ApplicationCommandOptionType;

// Simple test command

function rorp_discord_bot_install_all_command(string $app_id, array $commands_array){
    $request = wp_remote_request('https://discord.com/api/v10/applications/'.$app_id.'/commands',array(
        'method'=>'PUT',
        'headers'=> array(
            'Authorization'=>'Bot '.BotPrivateData::BOT_TOKEN,
            'Content-Type'=>'application/json; charset=UTF-8'
        ),
        'body'=>json_encode($commands_array)
        //'user-agent'=>'DiscordBot (https://github.com/discord/discord-example-app, 1.0.0)'
    ));
    error_log('résultat enregistrement commandes'.json_encode($request));
}

rorp_discord_bot_install_all_command(BotPrivateData::BOT_CLIENT_ID,array(
    /**
     * https://discord.com/developers/docs/interactions/application-commands#application-command-object-application-command-option-structure
     */
    array(
        'name'=>'roll',
        'description'=>'Lance des dés pour fournir un résultat aléatoire.',
        'type'=>1,
        'options'=>array(
            array(
                'name'=>'des',
                'description'=>'Les dés, écrit sous la forme XdY ou XdY+Z, où X = nombre de dé, Y = type de dé, et Z = modificateur',
                'type'=>ApplicationCommandOptionType::STRING,
                'required' =>true
            )
        )
    ),
    ));
    ?>