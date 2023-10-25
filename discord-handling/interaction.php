<?php
/**
 * Etant donné que je ne peux pas interagir directement avec le serveur pour
 * installer une lib de gestion des interactions discord, je suis obliger
 * de recréer ma copie de cette lib pour ce plug-in
 */
namespace Discord;

abstract class InteractionResponseFlags {
    const EPHEMERAL = 1 << 6;
}

abstract class InteractionResponseType {
  const PONG = 1;
  const CHANNEL_MESSAGE_WITH_SOURCE = 4;
  const DEFERRED_CHANNEL_MESSAGE_WITH_SOURCE = 5;
  const DEFERRED_UPDATE_MESSAGE = 6;
  const UPDATE_MESSAGE = 7;
  const APPLICATION_COMMAND_AUTOCOMPLETE_RESULT = 8;
  const MODAL = 9;
}

abstract class InteractionType {
  const PING = 1;
  const APPLICATION_COMMAND = 2;
  const MESSAGE_COMPONENT = 3;
  const APPLICATION_COMMAND_AUTOCOMPLETE = 4;
  const MODAL_SUBMIT = 5;
}