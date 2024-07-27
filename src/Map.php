<?php

namespace TR;

use PsyXEngine\GameObject;
use PsyXEngine\PixelTexture;
use SDL2\PixelBuffer;

class Map extends GameObject
{
    private const MAX_VIEW_DEPTH = 20;
    private const VIEW_DEPTH_INCREMENT = 0.01;
    private const EMPTY_FIELD = ' ';
    private string $map = "0000222222220000" .
    "1              0" .
    "1      11111   0" .
    "1     0        0" .
    "0     0  1110000" .
    "0     3        0" .
    "0   10000      0" .
    "0   0   11100  0" .
    "0   0   0      0" .
    "0   0   1  00000" .
    "0       1      0" .
    "2       1      0" .
    "0       0      0" .
    "0 0000000      0" .
    "0              0" .
    "0002222222200000";
    private int $mapW = 16;
    private int $mapH = 16;
    public ?PixelBuffer $pixBuff = null;
    private Player $player;
    private int $winWidth;
    private int $winHeight;
    /**
     * @var array|mixed
     */
    private array $colors;
    private bool $showMap;
    private int $rectW = 0;
    private int $rectH = 0;
    private int $grayColor = 0;
    private int $loops = 0;

    public function __construct(Player $player, int $width, int $height, bool $showMap = true)
    {
        $this->player = $player;
        $this->winWidth = $width;
        $this->winHeight = $height;
        $this->showMap = $showMap;

        $this->pixBuff = new PixelBuffer($this->winWidth * $this->winHeight);
        $this->pixBuff->fillWith(255);

        $this->renderType = new PixelTexture($this->pixBuff, $width, $height);

        $this->colors = $this->createRandomColors();

        $this->rectW = (int)($this->winWidth / ($this->mapW * 2));
        $this->rectH = (int)($this->winHeight / $this->mapH);
        $this->grayColor = $this->packColor(160, 160, 160);
    }

    private function createRandomColors(): array
    {
        $result = [];

        for ($i = 0; $i < 10; $i++) {
            $result[$i] = $this->packColor(rand() % 255, rand() % 255, rand() % 255);
        }

        return $result;
    }

    public function update(): void
    {
        $this->generateMap();
    }

    private function generateMap()
    {
        $this->clear();
        
        if ($this->showMap) {
            // TODO: it can be generated once and separately from 3d view and 2d cone
            $this->drawMap($this->rectW, $this->rectH);
        }

        $this->loops = 0;

        $loopTime = microtime(true);
        $raysCount = $this->getRaysCount();
        for ($i = 0; $i < (int)$raysCount; $i++) {
            $angle = $this->player->getDirectionAngel()
                - ($this->player->getFieldOfView() / 2)
                + ($this->player->getFieldOfView() * $i / $raysCount);

            $this->drawObjectsInAngleRange($angle, $i);
        }
        $endTime = microtime(true);

        echo "LOOOPS: " . $this->loops . "\n";
        echo "LOOOPS TIME: " . round(($endTime - $loopTime) * 1000) . "\n";
        echo "DA: " . $this->player->getDirectionAngel() . "\n";
    }

    private function getRaysCount(): float
    {
        if ($this->showMap) {
            return $this->winWidth / 2;
        }

        return (float) $this->winWidth;
    }


    private function clear(): void
    {
        $this->pixBuff->fillWith(255);
    }

    private function drawMap(int $rectW, int $rectH)
    {
        for ($y = 0; $y < $this->mapH; $y++) {
            for ($x = 0; $x < $this->mapW; $x++) {
                if ($this->map[$x + $y * $this->mapW] === ' ') {
                    continue;
                }

                $rectX = $x * $rectW;
                $rectY = $y * $rectH;

                $colorIndex = $this->map[$x + $y * $this->mapW];
                $color = $this->colors[$colorIndex] ?: $this->packColor(0, 255, 255);

                $this->drawRectangle(
                    $this->winWidth,
                    $this->winHeight,
                    $rectX,
                    $rectY,
                    $rectW,
                    $rectH,
                    $color
                );
            }
        }

        $this->drawRectangle(
            $this->winWidth,
            $this->winHeight,
            (int)($this->player->getX() * $rectW),
            (int)($this->player->getY() * $rectH),
            5,
            5,
            $this->packColor(255, 0, 0)
        ); // draw player
    }

    private function drawObjectsInAngleRange(float $angle, int $rangeIndex)
    {
        for ($currentViewDepth = 0; $currentViewDepth < self::MAX_VIEW_DEPTH; $currentViewDepth += self::VIEW_DEPTH_INCREMENT) {
            $this->loops++;

            // origin forumula is: cos($angle) = c/a => a = c * cos(angle);
            // where a is offset for X coord of x line of ray in current view Depth 
            // and c = $currentViewDepth
            $cx = $this->player->getX() + $currentViewDepth * cos($angle);

            // origin forumula is: sin(angle) = b/c => a = c * sin(angle);
            // where a is offset for Y coord of y line of ray in current view Depth  
            // and c = $currentViewDepth
            $cy = $this->player->getY() + $currentViewDepth * sin($angle);


            if ($this->showMap) {
                $this->draw2DConePixel($cx, $cy);
            }
            
            if ($this->hasWallInCoord((int) $cx, (int) $cy)) {
                $this->drawColumn((int)$cx, (int)$cy, $currentViewDepth, $angle, $rangeIndex);

                break;
            }
        }
    }

    private function draw2DConePixel(float $cx, float $cy): void
    {
        $pixX = (int)($cx * $this->rectW);
        $pixY = (int)($cy * $this->rectH);
        $this->pixBuff->add($pixX + $pixY * $this->winWidth, $this->packColor(160, 160, 160));
    }

    private function hasWallInCoord(int $cx, int $cy): bool
    {
        return $this->map[$cx + $cy * $this->mapW] !== self::EMPTY_FIELD;
    }

    private function drawColumn(int $cx, int $cy, float $currentViewDepth, float $angle, int $i): void
    {
        $colorIndex = $this->map[$cx + $cy * $this->mapW];
        $color = $this->colors[$colorIndex] ?: $this->packColor(0, 255, 255);

        // Calc depth for column drawing with fixing fish eye problem (* cos($angle - $this->player->getDirectionAngel()))
        $columnDepth = $currentViewDepth * cos($angle - $this->player->getDirectionAngel());

        $columnHeight = !$columnDepth
            ? 0
            : $this->winHeight / $columnDepth;
        
        $rectY = (int)($this->winHeight / 2 - $columnHeight / 2);
        $this->drawRectangle(
            $this->winWidth,
            $this->winHeight,
            (int)($this->calcColumnXOffset() + $i), // position of vertical column, was (int)($rayCount + $i) - when map added
            $rectY,
            1,
            $columnHeight,
            $color
        );
    }

    private function calcColumnXOffset(): int
    {
        if ($this->showMap) {
            return $this->getRaysCount();
        }

        return 0;
    }

    private function drawRectangle($width, $height, $rectX, $rectY, $rectW, $rectH, $color)
    {
        for ($x = 0; $x < $rectW; $x++) {
            for ($y = 0; $y < $rectH; $y++) {
                $cx = $rectX + $x;
                $cy = $rectY + $y;

                if ($cx >= $width || $cy >= $height) {
                    continue;
                }

                $index = $cx + $cy * $width;
                // index is zero when height of window less than height of column for drawing (in very close distance)
                if ($index < 0) {
                    $index = 0;
                }


                $this->pixBuff->add($index, $color);
            }
        }
    }

    private function packColor(int $r, int $g, int $b, int $a = 0): int
    {
        return ($a << 24) + ($r << 16) + ($g << 8) + $b;
    }

    private function unpackColor(int &$color, int &$r, int &$g, int &$b, int &$a)
    {
        $b = ($color >> 0) & 255;
        $g = ($color >> 8) & 255;
        $r = ($color >> 16) & 255;
        $a = ($color >> 24) & 255;
    }
}