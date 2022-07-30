<?php
     
class Count_ {
    
    public function exec($instance, $action, $data, $options) {
        try {
            $result = $instance->$action($data, $options);

            $total = 0;
            if($result[0]->rowCount() == 1) {
                
                $row    = $result[0]->fetch(PDO::FETCH_ASSOC);
                $total  = $row["num"];
            }

            http_response_code(200);
                
            echo json_encode(array(
                "records" => $total,
                "test"    => $result 
            ));
        } catch(PDOException $exception) {
            http_response_code(501);
            echo json_encode(
                array("message" =>"Error in count.php file : " . $exception->getMessage())
            );
        }
    }
}

