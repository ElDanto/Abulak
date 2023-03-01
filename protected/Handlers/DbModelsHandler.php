<?php

namespace App\Handlers;

abstract class DbModelsHandler 
{
    public static function arraySliceHandler(array $modelsData)
    {
        $countLoops = (int)(count($modelsData) / 1000)+1;
        for($i = 1; $i <= $countLoops;  $i++){
            $slicedArray = array_slice(
                $modelsData,
                ($i -1) * 1000,
                1000
            );
            
            yield $slicedArray;
        }
    }

    public static function insertPrepareHandler(array $modelsData)
    {
        $bindings = [];
        $values = [];
        $counter = 1;

        foreach(self::arraySliceHandler($modelsData) as $arrayObjects) {
            foreach ($arrayObjects as $object) {
                $objectVars = get_object_vars($object);
                $bindingsItem = [];
                
                foreach ($objectVars as $key => $value) {
                    if ($key == 'id' || $key == 'db') {
                        continue;
                    }
                    $bindingsItem[] = ':'. $key . $counter;
                    $values[':'. $key . $counter] = $value;
                    
                }

                $bindings[] = '( ' . implode( ',',$bindingsItem ) . ' )';
                $counter++;
            }

            $result = [
                "bindings" => $bindings,
                "values"   => $values,
            ];

            yield $result;
        }
    }

}
