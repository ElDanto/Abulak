<?php
namespace App;

class Db 
{
    protected $dbh;
    protected static $instanse = null;

    private function __construct()
    {
        $config = include_once __DIR__ . '/conf/DbConf.php';
        $dbs = $config['DATABASE_DRIVER'] . 
            ':host=' . $config['DATABASE_HOST'] . ';' .
            (!empty($config['DATABASE_POSRT']) ? 'port=' . $config['DATABASE_POSRT'] . ';' : '') . 
            'dbname=' . $config['DATABASE_NAME']; 
        try {
            $this->dbh = new \PDO($dbs, $config['DATABASE_USERNAME'], $config['DATABASE_PASSWORD']);
        } catch (\PDOException $e) {
            throw new \App\Exceptions\DbConnectionException("Ошибка соединения с БД");
        }
        
    }

    public static function Instance()
    {
        if (self::$instanse === null) {
            self::$instanse = new Db();
        }
        return self::$instanse;
    }

    public function query($sql, $class, $data = []){
        try {
            $sth = $this->dbh->prepare($sql);
            $sth->execute($data);
        } catch (\PDOException $e) {
            throw new \App\Exceptions\DbQueryException("Ошибка выполнения SQl запроса: " . $sql);
        }

        $data = $sth->fetchAll(\PDO::FETCH_CLASS, $class);
        
        return $data;
    }

    public function execute($query, $params=[])
    {   
        try {
            $sth = $this->dbh->prepare($query);
            return $sth->execute($params);
        } catch (\PDOException $e) {
            throw new \App\Exceptions\DbQueryException("Ошибка выполнения SQl запроса: " . $query);
        }
        
    }
    
    public function lastInsertId()
    {
        $data = $this->dbh->lastInsertId();
        return $data; 
    }
}