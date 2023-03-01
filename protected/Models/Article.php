<?php
namespace App\Models;

use App\Model;
use App\Db;
use \App\ApiWiki;

class Article 
    extends Model
{
    protected static $table = 'article';
    
    public $page_id;
    public $title;
    public string $content;
    public $link;
    public $size;
    public $wordcount;

    public static function getByTitle(string $title): \App\Models\Article
    {
        $params = [
                    "action" => "query",
                    "format" => "json",
                    "list" => "search",
                    "srsearch" => $title,
                    "srlimit" => "1",
                ];
        $data = ApiWiki::query($params);

        $article = new \App\Models\Article();

        $data = $data['search'][0];
        if (self::checkMatchByPageId($data['pageid'])) {
            $article->fill($data);
        }

        return $article;
    }

    public function getContentById(): string
    {
        $params = [
            "action" => "query",
            "format" => "json",
            "prop" => "extracts",
            "pageids" => $this->page_id,
            "formatversion" => "2",
            "exlimit" => "1",
            "explaintext" => 1
        ];


        $data = ApiWiki::query($params);

        if(key_exists('pages', $data)) {
            $data = $data['pages'];

            return $this->processingContent($data[0]['extract']);
        }

    }

    public function processingContent(string $content): string
    {
        $processedContent = preg_replace('~\s(=){2,5}\s~m', ' ', $content); //Remove header's decoration
        $processedContent = preg_replace('~\n~m', ' ', $processedContent); //Remove lines brake
        $processedContent = preg_replace('~\s(\s)+\s~m', ' ', $processedContent); //Remove extra space
        $processedContent = preg_replace('~([0-9])(?= [0-9]) ~m', '$1', $processedContent); //Remove space between numbers
        return $processedContent;
    }

    public function fill(array $data): void
    {
        foreach($data as $key => $value) {
            if($key == 'pageid') {
                $this->page_id = $value;
                continue;
            }
            if(!key_exists($key, get_object_vars($this))) {
                continue;
            }
            if($key == 'size') {
                $this->size = round(((int) $value / 1024 ), 2 ) . 'Kb';
                continue;
            }
            
            $this->$key = $value;
        }

        $this->link = "https://ru.wikipedia.org/wiki/" . $this->title;
        $this->content = $this->getContentById($this->page_id);
    }

    public static function checkMatchByPageId($pageId)
    {
        $sql = 'SELECT *
        FROM ' . self::$table . ' 
        WHERE "page_id" = ' . $pageId;

        $db = Db::Instance();
        $result = $db->query($sql, self::class);

        if(empty($result)) {
            return true;
        } else {
            throw new \App\Exceptions\ImportException("Ошибка импорта. Статья уже скопирована.");
        }
    }
}