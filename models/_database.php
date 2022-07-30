<?php
class Database
{
    // OVH
    // private $host = "brunoherkvdirty.mysql.db";
    // private $db_name = "brunoherkvdirty";
    // private $username = "brunoherkvdirty";
    // private $pwd = "Mezmerize24";

    // localhost
    private $host = "localhost";
    private $db_name = "brunoherkvdirty";
    private $username = "root";
    private $pwd = "root";
    
    
    public function db()
    {
        $db = null;
 
        try {
            $db = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->pwd, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            $db->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $db;
    }

    public function count_($query, $data = [])
    {
        try {
            $params = [];
            foreach ($data as $value) {
                array_push($params, $value);
            }

            $db     = $this->db();
            $stmt   = $db->prepare($query);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key + 1, $value, PDO::PARAM_INT);
            }

            $stmt->execute();

            return [$stmt, $params];
        } catch (Exception $ex) {
            return ['error', $ex->getMessage()];
        }
    }


    public function create_($data, $table_name = NULL)
    {
        $table_name = !$table_name ? $this->table_name : $table_name;
        try {
            $db = $this->db();
            $query = "INSERT INTO " . $table_name . " (";
            $queryValues = " VALUES (";
            $params = [];

            foreach ($data as $key => $value) {
                if($key != "img")
                $query          .= $key . ",";
                $queryValues    .= "?,";
                array_push($params, $value);
            }
            $query          = substr($query, 0, -1) . ")";
            $queryValues    = substr($queryValues, 0, -1) . ")";
            $query          .= $queryValues;

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $id = $db->lastInsertId();
            return ['success', $id, $query, $params];
        } catch (Exception $ex) {
            return ['error', $ex->getMessage()];
        }
    }

    public function read_($query, $data = [])
    {
        try {
            $data['startIndex'] = !empty($data['startIndex']) ? $data['startIndex'] : 0;
            $data['to']         = !empty($data['to']) ? $data['to'] : 100;

            $params = [];
            foreach ($data as $value) {
                array_push($params, $value);
            }

            $query .= " LIMIT ?, ?";

            $db     = $this->db();
            $stmt   = $db->prepare($query);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key + 1, $value, PDO::PARAM_INT);
            }

            $stmt->execute();
            // var_dump($stmt, $params);
            return [$stmt, $params];
        } catch (Exception $ex) {
            return ['error', $ex->getMessage()];
        }
    }

    public function read_one_($query, $data)
    {
        try {
            $db     = $this->db();
            $stmt   = $db->prepare($query);

            $params = [];
            foreach ($data as $value) {
                array_push($params, $value);
            }

            foreach ($params as $key => $value) {
                if(!is_numeric($value)) {
                    $stmt->bindValue($key + 1, $value, PDO::PARAM_STR);     
                } else {
                    $stmt->bindValue($key + 1, $value, PDO::PARAM_INT);
                }   
            }

            $stmt->execute();

            return [$stmt, $params];
        } catch (Exception $ex) {
            return ['error', $ex->getMessage()];
        }
    }

    public function update_($data, $table_name = null, $should_set_update_time = true)
    {
        $table_name = !$table_name ? $this->table_name : $table_name;
        try {
            $db = $this->db();
            $query = "UPDATE " . $table_name . " SET ";
            $params = [];
            foreach ($data as $key => $value) {
                if($key != "id") {
                    $query .= $key . " = ? ,";
                    array_push($params, $value);
                }
            }
            if($should_set_update_time) {
                $query .= "updated_at = NOW()";
            } else {
                $query = substr($query, 0, -1);
            }            

            array_push($params, $data["id"]);
            $query          .= " WHERE id = ?";

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            
            return [$stmt->rowCount() == 1, $query, $params];
        } catch (Exception $ex) {
            return ['error', $ex->getMessage()];
        }
    }

    public function delete_($data)
    {
        $db = $this->db();
        $id = $data["id"];
        $response = $db->prepare('DELETE FROM ' . $this->table_name . ' WHERE id = ?');
        
        if ($response->execute([$id])) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_multi_($data)
    {
        $db         = $this->db();
        $query      = "DELETE FROM " . $this->table_name . " WHERE ";
        $query      .= $data["table_name"] . "_id = ?";
        $stmt       = $db->prepare($query);
        
        if ($stmt->execute([$data["id"]])) {
            return true;
        } else {
            return [$stmt, $data["id"]];
        }
    }

    // public function remove_current_imgs($data, $id)
    // {
    //     $output = [];

    //     $query = "SELECT ";

    //     foreach($data as $value) {
    //         $query .= $value . ",";
    //     }
    //     $query = substr($query, 0, -1);
    //     $query .= " FROM " . $this->table_name . " WHERE id = ?";
        

    //     try {
    //         $db     = $this->db();
    //         $stmt   = $db->prepare($query);
    //         $stmt->execute([$id]);
    //         $row    = $stmt->fetch(PDO::FETCH_ASSOC);
    //         foreach ($row as $filePath) {
    //             $fileRemoved = unlink("../" . $filePath);
    //         }
    //     } catch (Exception $ex) {
    //         return ['error', $ex->getMessage()];
    //     }
    // }
}
