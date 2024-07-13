<?php

namespace TR;

use PsyXEngine\GameObject;
use PsyXEngine\Image;
use SDL2\SDLRect;

class Map3D extends GameObject
{
    public function __construct()
    {
        $this->renderType = new Image(
            __DIR__ . '/../resources/img.png',
            new SDLRect(520, 10, 512, 512)
        );
    }
}