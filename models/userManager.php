<?php
include_once '_database.php';

class UserManager extends Database {

    public $table_name = "user";

    public function create($data) {
        $stmt = $this->create_($data);
        return $stmt;     
    }

    public function read($data, $option = NULL) {
        if ($option == "_search") {
            $query = "SELECT u.id, u.id_push, alias";
            $query .= " FROM " . $this->table_name . " u";
            $query .= " WHERE MATCH(alias) AGAINST ('" . $data["searchText"] . "' IN BOOLEAN MODE) AND u.id != ?";
        } 
            
        $stmt = $this->read_($query, ["userId" => $data["userId"]]);
        
        return $stmt;     
    }

    public function read_one($data, $option = NULL) {

        $query = "";

        $stmt = $this->read_one_($query, $data);
        return $stmt;     
    }


    public function update($data) {
        $stmt = $this->update_($data);
        return $stmt;     
    }
    
	public function delete($data) {
        $stmt = $this->delete_($data);
        return $stmt;     
    }
}