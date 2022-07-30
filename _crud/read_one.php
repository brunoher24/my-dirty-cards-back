<?php

class Read_one_ {
    
    public function exec($instance, $action, $data, $options) {
        try {
            include_once "./utilities.php";

            $result = $instance->$action($data, $options);

            if($result[0] != "error" && $result[0] != "success" && $result[0]->rowCount() == 1) {
                $row    = $result[0]->fetch(PDO::FETCH_ASSOC);
                $item   = EXTRACT_DATA($row); 
            }

            return [$item, $result];
        } catch(PDOException $exception) {
            http_response_code(501);
            echo json_encode(
                array("message" =>"Error in read_one.php file : " . $exception->getMessage())
            );
        }
    }
}
