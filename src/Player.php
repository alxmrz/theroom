<?php

namespace TR;

use PsyXEngine\GameObject;

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
        $this->fieldOfView = M_PI / 3.;
    }

    public function update(): void
    {

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