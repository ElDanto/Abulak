<?php

namespace App\Models;

use App\Model;

class Word 
    extends Model
{
    protected static $table = 'word';
    public $id;
    public $word;

    /**
     * @param  string $content
     * @return array $filteredWords
     */
    public static function processingData(string $content) : array
    {   
        $processedData = preg_replace('~\.|\,|\:|\;|\(|\)|\«|\»|\—|\!|\?|[^а-я]\/;|\[|\]|\{|\}~m', " ", $content); //Remove extra symbols
        $processedData = preg_replace('~\s{2,100}~m', " ", $processedData); //Remove extra space

        $wordsArray = explode(" ", quotemeta( trim($processedData)));

        foreach ($wordsArray as $key => $word) {
            $wordsArray[$key] = mb_strtolower( $word );
        }

        $filteredWords = array_unique($wordsArray);

        return $filteredWords;

    }
    
    /**
     * @param  array $dictionary
     */
    public static function addNewWord(array $dictionary)
    {   
         
        $havingDictionary = static::findAll();
        $tempArray= [];
        $newWords = [];

        foreach ($havingDictionary as $item) {

            $tempArray[] = $item->word;
        }
        
        $dictionary = array_diff($dictionary, $tempArray);

        foreach ($dictionary as $item) {
            $wordObj = new Word;
            $wordObj->word = $item;
            $newWords[] = $wordObj;
        }

        if(!empty($newWords)){
            static::insertAll($newWords);
        } 
    }
}