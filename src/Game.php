<?php

namespace TR;

use PsyXEngine\Event;
use PsyXEngine\GameInterface;
use PsyXEngine\GameObjects;

class Game implements GameInterface
{
    public function init(GameObjects $gameObjects): void
    {
        $player = new Player();

        $gameObjects->add($player);
        $gameObjects->add(new Map($player, 1024, 512));
    }

    public function update(?Event $event = null): void
    {
    }
}
