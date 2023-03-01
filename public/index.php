<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../protected/autoload.php';

$app = AppFactory::create();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(function(Request $request, \Throwable $exception) use ($app) {
    $errorHandler = new \App\Handlers\ErrorHandler($app);
    return $errorHandler->default($request, $exception);
});

$twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', \App\Controllers\Main::class . ':home');

$app->get('/foo/{data}', function(Request $request, Response $response, array $args){
    var_dump($args);
});

$app->post('/import/', \App\Controllers\Handler::class . ":import");

$app->post('/search/', \App\Controllers\Handler::class . ":search");

$app->run();
