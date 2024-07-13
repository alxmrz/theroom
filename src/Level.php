<?php

namespace TR;

use PsyXEngine\GameObjects;
use SDL2\PixelBuffer;
use SDL2\SDLColor;
use SDL2\SDLRect;

class Level
{
    private string $level1 = "
*****************|
*SSSSSSS*SSSSSSS*|
*S*S***S*S***S*S*|
*SSSSSSSSSSSSSSS*|
*S*S**S***S**S*S*|
*SSS*SSSSSSS*SSS*|
***S*S**S**S*S***|
SSSSSS*SSS*SSSSSS|
***S*S*****S*S***|
*SSS*SSSSSSS*SSS*|
*S*S**S***S**S*S*|
*SSSSSSSPSSSSSSS*|
*S*S***S*S***S*S*|
*SSSSSSS*SSSSSSS*|
*****************|
";

    private $map = "0000222222220000" .
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
    private array $buffer;
    /**
     * @var array|mixed
     */
    private array $colors;


    public function initialize(Player $player): void
    {
        $this->player = $player;
        $this->winWidth = 256;
        $this->winHeight = 256;
        $this->pixBuff = new PixelBuffer($this->winWidth * $this->winHeight);
        $this->buffer = array_fill(0, $this->winWidth * $this->winHeight, 255);

        $nColors = 10;
        $this->colors = [];
        for ($i = 0; $i < $nColors; $i++) {
            $this->colors[$i] = $this->packColor(rand()%255, rand()%255, rand()%255);
        }

    }

    public function generateMap()
    {
        $this->buffer = array_fill(0, $this->winWidth * $this->winHeight, 255);
        $this->pixBuff->fillWith(255);

        $rectW = (int)($this->winWidth / ($this->mapW * 2));
        $rectH = (int)($this->winHeight / $this->mapH);
        for ($y = 0; $y < $this->mapH; $y++) {
            for ($x = 0; $x < $this->mapW; $x++) {
                if ($this->map[$x + $y * $this->mapW] === ' ') {
                    continue;
                }

                $rectX = $x * $rectW;
                $rectY = $y * $rectH;

                $colorIndex = $this->map[$x + $y * $this->mapW];
                $color = $this->colors[$colorIndex] ?:  $this->packColor(0, 255, 255);


                $this->drawRectangle(
                    $this->buffer,
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
            $this->buffer,
            $this->winWidth,
            $this->winHeight,
            (int)($this->player->getX() * $rectW),
            (int)($this->player->getY() * $rectH),
            5,
            5,
            $this->packColor(255, 255, 255)
        ); // draw player
        $loops = 0;
        for ($i = 0; $i < (int)($this->winWidth / 2); $i++) {
            $angle = $this->player->getDirectionAngel() - $this->player->getFieldOfView(
                ) / 2 + $this->player->getFieldOfView() * $i / (float)($this->winWidth / 2);

            for ($t = 0; $t < 20; $t += .05) {
                $cx = $this->player->getX() + $t * cos($angle);
                $cy = $this->player->getY() + $t * sin($angle);

                $pixX = (int)($cx * $rectW);
                $pixY = (int)($cy * $rectH);

                $this->buffer[$pixX + $pixY * $this->winWidth] = $this->packColor(160, 160, 160);
                $this->pixBuff->add($pixX + $pixY * $this->winWidth, $this->packColor(160, 160, 160));

                if ($this->map[(int)$cx + (int)$cy * $this->mapW] !== ' ') {

                    $colorIndex = $this->map[(int)$cx + (int)$cy * $this->mapW];
                    $color = $this->colors[$colorIndex] ?:  $this->packColor(0, 255, 255);

                    $columnHeight = $this->winHeight / $t;
                    $this->drawRectangle(
                        $this->buffer,
                        $this->winWidth,
                        $this->winHeight,
                        (int)($this->winWidth / 2 + $i),
                        (int)($this->winHeight / 2 - $columnHeight / 2),
                        1,
                        $columnHeight,
                        $color
                    );

                    break;
                }

                $loops++;
            }
        }
        echo "LOOOPS: " . $loops . "\n";
        echo "DA: " . $this->player->getDirectionAngel() . "\n";

        //$this->saveImage(__DIR__ . "/../resources/imgkk.png", $buffer, $winWidth, $winHeight);
        //$this->pixBuff = $buffer;
    }

    public function drawRectangle(&$buffer, $width, $height, $rectX, $rectY, $rectW, $rectH, $color)
    {
        for ($x = 0; $x < $rectW; $x++) {
            for ($y = 0; $y < $rectH; $y++) {
                $cx = $rectX + $x;
                $cy = $rectY + $y;

                if ($cx >= $width || $cy >= $height) {
                    continue;
                }

                $buffer[$cx + $cy * $width] = $color;
                $this->pixBuff->add($cx + $cy * $width, $color);
            }
        }
    }

    public function createImageBuffer(int $width, int $height): array
    {
        $buffer = [];
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $r = 255 * $y / $height; // varies between 0 and 255 as j sweeps the vertical
                $g = 255 * $x / $width; // varies between 0 and 255 as i sweeps the horizontal
                $b = 0;
                //imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, $r, $g, $b, 0));
                //$buffer[$x + $y * $width] = $this->packColor((int)$r, (int)$g, (int)$b);
                $buffer[$x + $y * $width] = $this->packColor(255, 255, 255);
                $this->pixBuff->add($x + $y * $width, $this->packColor(255, 255, 255));
            }
        }



        return $buffer;
    }

    private function saveImage(string $path, array $buffer, int $width, int $height): bool
    {
        $image = imagecreatetruecolor($width, $height);

        $redCounts = 0;

        $this->pixBuff = new PixelBuffer($width * $height);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                imagesetpixel($image, $x, $y, $buffer[$x + $y * $width]);
                //$this->pixBuff[$x + $y * $width] = $buffer[$x + $y * $width];
                $this->pixBuff->add($x + $y * $width, $buffer[$x + $y * $width]);
            }
        }

        echo $redCounts . ' red' . PHP_EOL;

        return imagepng($image, $path);
    }

    private function packColor(int $r, int $g, int $b, int $a = 0): int
    {
        return ($a << 24) + ($r << 16) + ($g << 8) + $b;
        //return imagecolorallocatealpha($image, $r, $g, $b, 0))
    }

    private function unpackColor(int &$color, int &$r, int &$g, int &$b, int &$a)
    {
        $b = ($color >> 0) & 255;
        $g = ($color >> 8) & 255;
        $r = ($color >> 16) & 255;
        $a = ($color >> 24) & 255;
    }
}