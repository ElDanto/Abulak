<?php

namespace App\Models;

use \App\Model;
use \App\Db;
use \App\Models\Word;

class Dictionary 
    extends Model
{
    protected static $table = 'dictionary';
    public $word_id; 
    public $article_id;
    public $count_occurrences;
    
    /**
     * fillDictionary
     *
     * @param array $processedWords
     * @param Article $article
     */
    public static function fillDictionary(array $processedWords, Article $article )
    {
        $newDictionaries = [];
        if(!empty($processedWords)){ 
            foreach ($processedWords as $word) {
                $word = quotemeta($word);

                $dictionary = new self;

                $sql = 'SELECT word.id FROM word WHERE word.word LIKE \'' . $word . '\'';

                $wordObj = Db::Instance()->query($sql, Word::class);

                $dictionary->word_id = ($wordObj[0])->id;

                $dictionary->article_id = $article->id;
                $flagPlus = false;

                if (preg_match('~[0-9]+~m', $word)) {
                    $dictionary->count_occurrences = preg_match_all('~\b' . $word . '\b~mu', $article->content);
                    
                } else {
                    
                    if ( !$flagPlus) {
                        if (mb_strlen($word) > 1) {
                            $substrWord = mb_substr($word, 1);
                            $dictionary->count_occurrences = preg_match_all('~\b[A-z-А-я]' . $substrWord . '\b~mu', $article->content);
                        } else {
                            $dictionary->count_occurrences = preg_match_all('~\b' . $word . '\b~mu', $article->content);
                        }  
                    }

                    
                }
                
                $newDictionaries[] = $dictionary;
                
            }
        }
        if ( !empty ($newDictionaries) ){
            static::insertAll($newDictionaries);
        }   
    }
}
