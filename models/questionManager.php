<?php
include_once '_database.php';

class QuestionManager extends Database {

    public $table_name = "question";

    public function create($data) {
        $stmt = $this->create_($data);
        return $stmt;     
    }

    public function read($data, $option = NULL) {
        $query = "SELECT q.id, q.text";
        
        $query .= " FROM " . $this->table_name . " q"; 
        
        if ($option == "_by_user_id") {
            $query .= " WHERE q.user_id = ? ORDER BY created_at DESC";
        }
        
        $stmt = $this->read_($query, $data);
        
        return $stmt;     
    }

    // public function read_one($data, $option = NULL) {

    //     if($option == "_account_infos") {
    //         $query = "SELECT id, email, firstname, name, passport, department_nbr FROM " . $this->table_name;
    //         $query .= " WHERE id = ?";
    //     } else {
    //         $query = "SELECT u.id, ce_id, discount,  email, firstname, u.is_active, u.name, passport, ce.name AS ce_name";
    //         $query .= " FROM " . $this->table_name . " u"; 
    //         $query .= " INNER JOIN ce ON ce.id = u.ce_id";
    //         $query .= " WHERE u.id = ?";
    //     }

    //     $stmt = $this->read_one_($query, $data);
    //     return $stmt;     
    // }


    public function update($data) {
        $stmt = $this->update_($data);
        return $stmt;     
    }
    
	public function delete($data) {
        $stmt = $this->delete_($data);
        return $stmt;     
    }
}