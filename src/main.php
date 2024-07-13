<?php

use TR\Game;
use PsyXEngine\Engine;

require_once __DIR__ . '/../vendor/autoload.php';

$engine = new Engine();

$engine->setWindowTitle('The room');
$engine->setWindowWidth(900);
$engine->setWindowHeight(600);
$engine->displayDebugInfo();

$engine->run(new Game());
