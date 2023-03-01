<?php

namespace App;

use \App\Db;
use \App\Handlers\DbModelsHandler;

abstract class Model
{
    protected static $table = null;
    public $id;

    public static function findAll()
    {
        $sql = 'SELECT * FROM ' . static::$table;

        return Db::Instance()->query($sql, static::class);
    }

    public static function findById($id)
    {
        $sql = 'SELECT * FROM ' . static::$table . ' WHERE id=' . $id;

        $result = Db::Instance()->query($sql, static::class);
        
        if (!empty($result)) {
            return $result;
        } else {
            return false;
        }
        
    }

    public function insert()
    {
        $data = get_object_vars($this);
        
        $bindings = [];
        $columns = []; 
        $values = [];

        foreach($data as $key => $value){
            if($key == 'id' || $key == 'db'){
                continue;
            }
            
            $columns[] = $key;
            $bindings[] = ':'. $key;
            $values[':'. $key] = $value;
        }

        $sql = 'INSERT INTO ' . static::$table . 
        ' ('. implode(',', $columns) .') 
        VALUES ('.implode(',',$bindings).')';

        Db::Instance()->execute($sql, $values);
        
        if(static::class != \App\Models\Dictionary::class){
            $this->id = Db::Instance()->lastInsertId();
        }
    }

    public function update($id)
    {
        $data = get_object_vars($this);

        $bindings = [];
        $values = [];

        foreach($data as $key => $value){
            if($key == 'id'){
                continue;
            }
            $bindings[] = '`' . $key . '`= :' . $key;  
            $values[':'. $key] = $value;
        }
        $values[':id'] = $id;

        $sql = 'UPDATE ' . static::$table . '
                SET ' . implode(',',$bindings) . ' 
                WHERE `id` = :id';
        return Db::Instance()->execute($sql, $values);     

    }

    public function save()
    {
        $id = $this->id;
        if(!empty($id)){
            $this->update($id);
        }else{
            $this->insert();
        }
    }

    public function delete($id = '')
    {
        if(empty($id)){
            $id = $this->id;
        }

        $sql = 'DELETE 
                FROM ' . static::$table . 
              ' WHERE `id`=:id';
        $args = [
            ':id' => $id
        ];
        Db::Instance()->execute($sql, $args); 
    }

    public static function insertAll(array $data)
    {   
        $columnsVars = get_class_vars(static::class);
        $columns = []; 

        foreach ($columnsVars as $key => $value) {
            if($key == 'id' || $key == 'db' || $key == 'table') {
                continue;
            }
            $columns[] = $key;
        }

        foreach (DbModelsHandler::insertPrepareHandler($data) as $params) {
            
            $sql = 'INSERT INTO ' . static::$table . 
            ' ('. implode(', ' , $columns) .') 
            VALUES '.implode(', ' , $params["bindings"]);
        
            Db::Instance()->execute($sql, $params["values"]);
        }   
    }
}
