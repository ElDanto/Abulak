<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use \App\Models\Article;

class Main 
    extends \App\Controller
{
    
    public function home(ServerRequestInterface $request, ResponseInterface $response) 
    {
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render(
            $response,
            'index.twig', 
            [
                "articles" => Article::findAll(),
            ]
        );
    }
}