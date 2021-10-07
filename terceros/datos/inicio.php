<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require '../../vendor/autoload.php';

$app = new \Slim\App;

require 'src/rutas/opciones_terceros.php';

$app->run();
