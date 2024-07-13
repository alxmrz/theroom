<?php

namespace TR;

use PsyXEngine\GameObject;
use PsyXEngine\GameObjects;
use PsyXEngine\KeyPressedEvent;

class Player extends GameObject
{
    private float $fieldOfView;
    private float $directionAngel;
    private float $y;
    private float $x;

    public function __construct()
    {
        $this->x = 3.456; // player x position
        $this->y = 2.345; // player y position
        $this->directionAngel = 1.523; // player view direction
        //$this->directionAngel = 0.0; // player view direction
        $this->fieldOfView = M_PI / 3.;
    }

    public function update(): void
    {
        //$this->directionAngel+= 2*M_PI/360;
        //$this->directionAngel+= 0.5;
        if ($this->directionAngel>= 8) {
        //    $this->directionAngel= 0;
        }
    }

    public function onButtonPressed(KeyPressedEvent $event, GameObjects $gameObjects): void
    {
        if ($event->isLeftArrowKeyPressed()) {
            $this->directionAngel -= 0.5;
        } elseif ($event->isRightArrowKeyPressed()) {
            $this->directionAngel += 0.5;
        } elseif ($event->isUpArrowKeyPressed()) {
            $this->x = $this->x + cos($this->directionAngel) * 0.5;
            $this->y = $this->y + sin($this->directionAngel) * 0.5;
        } elseif ($event->isDownArrowKeyPressed()) {
            $this->x = $this->x - cos($this->directionAngel) * 0.5;
            $this->y = $this->y - sin($this->directionAngel) * 0.5;
        }
    }

    public function isDisplayable(): bool
    {
        return false;
    }

    public function getFieldOfView(): float
    {
        return $this->fieldOfView;
    }

    public function setFieldOfView(float $fieldOfView): void
    {
        $this->fieldOfView = $fieldOfView;
    }

    public function getDirectionAngel(): float
    {
        return $this->directionAngel;
    }

    public function setDirectionAngel(float $directionAngel): void
    {
        $this->directionAngel = $directionAngel;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function setY(float $y): void
    {
        $this->y = $y;
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function setX(float $x): void
    {
        $this->x = $x;
    }
}