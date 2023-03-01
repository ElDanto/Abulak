<?php

namespace App\Models;

use App\Model;
use App\Db;

class Coincidence
    extends Model
{   
    public $articleId;
    public $title;
    public $content;
    public $count_occurrences;
        
    /**
     * @param string $keyword
     * @return array $coincidences
     */
    public static function findCoincidenceByKeyword(string $keyword) : array
    {
        $keyword = mb_strtolower(trim($keyword));

        $sql = 'SELECT article.title, article.id AS article_id, 
                    article.content, dictionary.count_occurrences
                FROM word 
                JOIN dictionary 
                    ON word.id = dictionary.word_id 
                JOIN article 
                    ON dictionary.article_id = article.id
                WHERE word.word LIKE \'' . $keyword . '\' 
                ORDER BY dictionary.count_occurrences
                DESC';

        $db = Db::Instance();  
        
        $coincidences = $db->query($sql, 'App\Models\Coincidence');
        
        foreach ($coincidences as $coincidence) {
            if ( preg_match( '~[0-9]+~', $keyword ) ) {
                $processedContent = preg_replace( "~(" . $keyword . ")\s~mu", "<mark>$1</mark> ", $coincidence->content ); //Mark keyword
            } else {
                
                if ( mb_substr($keyword, 0, 1) == '+' ) {
                    $processedKeyword = '\\' . $keyword;
                } else {
                    $processedKeyword = mb_substr($keyword, 1);
                }
                $processedContent = preg_replace( "~\b([A-z-А-я]" . $processedKeyword . ")\b~mu", "<mark>$1</mark>", $coincidence->content ); //Mark keyword
                
            }

            $processedContent = preg_replace( "~\s(\.|\,)~mu", "$1", $processedContent ); //Remove extra space
            $coincidence->content = $processedContent;
        }

        return $coincidences;
    }
}