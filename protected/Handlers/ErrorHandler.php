<?php

namespace App\Handlers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ErrorHandler
{
    protected object $app;

    public function __construct(\Slim\App $app)
    {
        $this->app = $app;
    }

    public function default(Request $request, \Throwable $exception)
    {
        $response = $this->app->getResponseFactory()->createResponse();

        switch ($exception::class) {
            case 'App\Exceptions\DbConnectionException':
                $template = 'dbErrors.twig';
                break;
            case 'App\Exceptions\DbQueryException':
                $template = 'queryErrors.twig';
                break;
            
            default:
                $template = "queryErrors.twig";
                break;
        }
        $view = Twig::fromRequest($request);
        return $view->render(
            $response,
            $template,
            ['errorMessage' => $exception->getMessage()]
        );
    }
}