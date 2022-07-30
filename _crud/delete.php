<?php

class Delete_ {
    
    public function exec($manager, $action, $data = NULL, $options = NULL) {
        try {
            
            $result = $manager->$action($data);
            return $result;
            
        } catch(PDOException $exception) {
            http_response_code(501);
            echo json_encode(
                array("message" =>"Error in delete.php file : " . $exception->getMessage())
            );
        }
    }
}
