<?php

namespace TR;

use PsyXEngine\GameObject;
use PsyXEngine\Image;
use SDL2\SDLRect;

class Map extends GameObject
{
    public function __construct()
    {
        $this->renderType = new Image(
            __DIR__ . '/../resources/img.png',
            new SDLRect(10, 10, 1024, 512)
        );
    }
}