<?php
//A défaut de savoir où mettre le manifest, j'ai décidé de le mettre ici
//cela évitera que le manifest soit redéclaré trop souvent
//Dans l'idéal, il faudrai trouver où le caler pour que le manifest ne se redéclare qu'à l'installation et l'update du pluggin (ou genre qu'une fois par jour)
include __DIR__ .'/../includes/rorp-discord-bot-commands-manifest.php';
use Discord\BotPrivateData;
?>

<h1>TODO</h1>
<p>Faire une vrai page</p>

<a target="_blank" rel="noopener noreferrer" href="https://discord.com/oauth2/authorize?client_id=<?= BotPrivateData::BOT_CLIENT_ID ?>&permissions=3072&scope=bot">inviter le bot sur un serveur</a>