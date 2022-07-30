<?php

class Read_ {
    
    public function exec($instance, $action, $data, $options) {
        try {
            include_once "./utilities.php";

            $result = $instance->$action($data, $options);


            if($result[0] == "error") {
                return $result;
            }

            $items = [];
            if($result[0]->rowCount() > 0) {
                
                while ($row = $result[0]->fetch(PDO::FETCH_ASSOC)){
                    $item = EXTRACT_DATA($row);
                    array_push($items, $item);
                }
            }

            return $items;
        } catch(PDOException $exception) {
            http_response_code(501);
            echo json_encode(
                array("message" =>"Error in read.php file : " . $exception->getMessage())
            );
        }
    }
}
