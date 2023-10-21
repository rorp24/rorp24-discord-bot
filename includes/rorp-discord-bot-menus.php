<?php

//Rassembleur de menu

function page_gestion_rorp_discord-bot() {
  add_menu_page('rorp discord-bot','rorp discord-bot','manage_options','rorp-discord-bot-accueil','montrer_accueil_page',''/*une image*/,24);
  add_submenu_page("rorp-discord-bot-accueil", "Accueil", "Accueil", "manage_options", "rorp-discord-bot-accueil", "montrer_accueil_page");
  //TODO changer la page
  add_submenu_page("rorp-discord-bot-accueil", "discord-bot générateur de noms", "discord-bot générateur de noms", "manage_options", "rorp-discord-bot-name-generator", "montrer_generateur_de_noms_page");
}

add_action( 'admin_menu', 'page_gestion_rorp_discord_bot' );

function montrer_accueil_page(){
  include_once __DIR__ .'/../views/accueil_plugin.php';
}

function montrer_generateur_de_noms_page(){
  //regarder https://github.com/owthub/wp-next-plugin/blob/master/wp-next-plugin.php
  include_once __DIR__ .'/../views/generateur_de_nom_page.php';
}