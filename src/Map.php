<?php

namespace TR;

use PsyXEngine\GameObject;
use PsyXEngine\Image;
use PsyXEngine\PixelTexture;
use SDL2\PixelBuffer;
use SDL2\SDLRect;

class Map extends GameObject
{

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

    public function __construct(Player $player, int $width, int $height, bool $showMap = false)
    {
        $this->player = $player;
        $this->winWidth = $width;
        $this->winHeight = $height;
        $this->showMap = $showMap;

        $this->pixBuff = new PixelBuffer($this->winWidth * $this->winHeight);
        $this->pixBuff->fillWith(255);

        $this->renderType = new PixelTexture($this->pixBuff, $width, $height);


        $nColors = 10;
        $this->colors = [];
        for ($i = 0; $i < $nColors; $i++) {
            $this->colors[$i] = $this->packColor(rand() % 255, rand() % 255, rand() % 255);
        }

    }

    public function update(): void
    {
        $this->generateMap();
    }

    public function generateMap()
    {
        $this->pixBuff->fillWith(255);

        $rectW = (int)($this->winWidth / ($this->mapW * 2));
        $rectH = (int)($this->winHeight / $this->mapH);
        
        if ($this->showMap) {
            $this->drawMap($rectW, $rectH);
        }

        $loops = 0;

        $rayCount = $this->winWidth;
        $greyColor = $this->packColor(160, 160, 160);

        $loopTime = microtime(true);
        for ($i = 0; $i < (int)($rayCount); $i++) {
            $angle = $this->player->getDirectionAngel() - $this->player->getFieldOfView(
                ) / 2 + $this->player->getFieldOfView() * $i / (float)($rayCount);

            $t = 0;

            for (; $t < 20; $t += 0.01) {
                $loops++;
                $cx = $this->player->getX() + $t * cos($angle);
                $cy = $this->player->getY() + $t * sin($angle);


                if ($this->showMap) {
                    $pixX = (int)($cx * $rectW);
                    $pixY = (int)($cy * $rectH);
                    $this->pixBuff->add($pixX + $pixY * $this->winWidth, $greyColor);
                }
                

                if ($this->map[(int)$cx + (int)$cy * $this->mapW] !== ' ') {
                    $colorIndex = $this->map[(int)$cx + (int)$cy * $this->mapW];
                    $color = $this->colors[$colorIndex] ?: $this->packColor(0, 255, 255);

                    $depth = $t * cos($angle - $this->player->getDirectionAngel());

                    if (!$depth) {
                        $columnHeight = 0;
                    } else {
                        $columnHeight = $this->winHeight / ($depth);
                    }
                    $rectY1 = (int)($this->winHeight / 2 - $columnHeight / 2);
                    $this->drawRectangle(
                        $this->winWidth,
                        $this->winHeight,
                        (int)($i), // position of vertical column, was (int)($rayCount + $i) - when map added
                        $rectY1,
                        1,
                        $columnHeight,
                        $color
                    );

                    break;
                }


            }
        }

        $endTime = microtime(true);
        $timeElapsed = round(($endTime - $loopTime) * 1000);

        echo "LOOOPS: " . $loops . "\n";
        echo "LOOOPS TIME: " . $timeElapsed . "\n";
        echo "DA: " . $this->player->getDirectionAngel() . "\n";
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

    public function drawRectangle($width, $height, $rectX, $rectY, $rectW, $rectH, $color)
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


                // $buffer[$cx + $cy * $width] = $color;
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