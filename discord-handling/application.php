<?php

namespace Discord;

abstract class ApplicationCommandType {
    const CHAT_INPUT = 1;
    const USER = 2;
    const MESSAGE = 3;
}

abstract class ApplicationCommandOptionType {
    const SUB_COMMAND = 1;
    const SUB_COMMAND_GROUP = 2;
    const STRING = 3;
    const INTEGER = 4;
    const BOOLEAN = 5;
    const USER = 6;
    const CHANNEL = 7;
    const ROLE = 8;
    const MENTIONABLE = 9;
    const NUMBER = 10;
    const ATTACHMENT = 11;
}