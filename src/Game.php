<?php

namespace TR;

use PsyXEngine\Engine;
use PsyXEngine\Event;
use PsyXEngine\GameInterface;
use PsyXEngine\GameObjects;

class Game implements GameInterface
{
    private Level $level;

    public function init(GameObjects $gameObjects): void
    {
        $player = new Player();
        $this->level = new Level();
        $this->level->initialize($player);
        //$image = imagecreatefrompng(__DIR__ . "/../resources/img.png");
        //$width = imagesx($image);
        //$height = imagesy($image);
        /*for($x = 0; $x < $width; $x++) {
            for($y = 0; $y < $height; $y++) {
                // pixel color at (x, y)
                $rgba = imagecolorat($image, $x, $y);
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;
                $a = ($rgba & 0x7F000000) >> 24;

            }
        }*/

        Engine::$pixBuff =  $this->level->pixBuff;

        $gameObjects->add($player);
        //$gameObjects->add(new Map());
       // $gameObjects->add(new Map3D());
    }



    public function update(Event $event = null): void
    {
        $this->level->generateMap();
    }
}
