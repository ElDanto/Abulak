<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use \App\Controller;
use \App\Models\Article;
use \App\Models\Word;
use App\Models\Dictionary;
use App\Models\Coincidence;

class Handler
    extends Controller
{    
    public function import(Request $request, Response $response, array $args)
    {
        $params = (array)$request->getParsedBody();
        $data = quotemeta($params['data']);

        
        $findedArticle = Article::getByTitle($data);
        $findedArticle->insert();
        
        $processedWords = Word::processingData($findedArticle->content);
        Word::addNewWord($processedWords);



        Dictionary::fillDictionary($processedWords, $findedArticle);

        
        
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render(
            $response,
            'findedArticle.twig',
            [
                'findedArticle' => $findedArticle,
                'articles'      => Article::findAll(),
            ]
        );       
    }
    
    public function search(Request $request, Response $response, array $args)
    {
        $params = (array)$request->getParsedBody();
        $data = quotemeta($params['data']);
        if ( !empty($data) ) {
            $coincidences = Coincidence::findCoincidenceByKeyword($data);
            $view = \Slim\Views\Twig::fromRequest($request);
            return $view->render(
                $response,
                'findedCoincidence.twig',
                [
                    'coincidences' => $coincidences, 
                ]
            );   
        } else {
            throw new \Exception("Ошибка поиска");
        }
        
    }
}