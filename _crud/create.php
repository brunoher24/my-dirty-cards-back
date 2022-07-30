<?php

class Create_ {
    
    public function exec($instance, $action, $data, $options) {
        try {      
            
            $result = $instance->$action($data);
            return $result;
            
        } catch(PDOException $exception) {
            http_response_code(501);
            echo json_encode(
                array("message" =>"Error in create.php file : " . $exception->getMessage())
            );
        }
    }

}