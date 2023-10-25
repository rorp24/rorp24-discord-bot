<?php 
/**
 * @wordpress-plugin
 * Plugin Name:       rorp24 discord bot
 * Version:           0.0.1
 * Plugin URI:        https://un-roliste-flemmard.com
 * Description:       Plugin personnel de bot discord pour le site d'un rôliste flemmard. Le projet n'étant pas de faire un plugin qui ira sur les store de wordpress, je n'ai pas chercher à rendre le truc user friendly (car c'est moi l'user)
 * Author:            Robin Elzeard
 * Author URI:        https://un-roliste-flemmard.com
 * License:           None
 * License URI:       https://un-roliste-flemmard.com
 * Text Domain:       rorp24-discord-bot
 * Domain Path:       /
 */

//includes
include __DIR__ .'/discord-handling/rorp-discord-bot-private.php';
include __DIR__ .'/includes/rorp-discord-bot-menus.php';
include __DIR__ .'/includes/rorp-discord-bot-endpoints.php';