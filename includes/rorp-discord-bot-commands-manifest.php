<?php

use Discord\BotPrivateData;

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
     * toute les commandes doivent avoir la forme:
     * array(
     *   'name'=>'le mot à taper pour la commande',
     *   'description'=>'la description',
     *   'type'=>un entier, très probablement 1, cf : https://discord.com/developers/docs/interactions/application-commands#application-command-object-application-command-types
     * );
     */
    array(
        'name'=>'test',
        'description'=>'Basic command',
        'type'=>1
    )
    ));
    ?>