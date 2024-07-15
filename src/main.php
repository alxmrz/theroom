<?php

use TR\Game;
use PsyXEngine\Engine;

require_once __DIR__ . '/../vendor/autoload.php';

$engine = new Engine();

$engine->setWindowTitle('The room');
$engine->setWindowWidth(1024);
$engine->setWindowHeight(512);
$engine->displayDebugInfo();

$engine->run(new Game());
