<?php

namespace TR;

use PsyXEngine\GameObject;
use PsyXEngine\GameObjects;
use PsyXEngine\KeyPressedEvent;

class Player extends GameObject
{
    /**
     * fieldOfView of the player
     * @var float
     */
    private float $fieldOfView;

    /**
     * 
     * Angle of view direction
     * 
     * @var float
     */
    private float $directionAngel;
    /**
     * player x position
     *
     * @var float
     */
    private float $y;
    /**
     * Player y position 
     *
     * @var float
     */
    private float $x;

    public function __construct()
    {
        $this->x = 3.456;
        $this->y = 2.345;
        $this->directionAngel = 1.523;
        $this->fieldOfView = M_PI / 3.;
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

    public function getDirectionAngel(): float
    {
        return $this->directionAngel;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function getX(): float
    {
        return $this->x;
    }
}