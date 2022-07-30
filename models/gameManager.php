<?php
include_once '_database.php';

class GameManager extends Database {

    public $table_name = "game";

    public function create($data) {
        $stmt = $this->create_($data);
        return $stmt;
        // try {
        //     $db = $this->db();
        //     $query = "INSERT INTO game (players_nbr, round, score) VALUES (0,1,0)";
        //     $stmt = $db->query($query);
        //     $id = $db->lastInsertId();
        //     return ['success', $id];
        // } catch (Exception $ex) {
        //     return ['error', $ex->getMessage()];
        // }   

    }

    public function read($data, $option = NULL) {
        
        $query = "";
        if ($option == "by_user_id") {
            $query .= "SELECT game_player.game_id, user.alias, game.started";
            $query .= " FROM game_player";
            $query .= " INNER JOIN game ON game.id = game_player.game_id";
            $query .= " INNER JOIN user ON user.id = game_player.user_id";
            $query .= " WHERE game_player.game_id =";
            $query .= " (SELECT MAX(game_id) FROM game_player WHERE user_id = ?)";  
        } 
        $stmt = $this->read_($query, $data);
        
        return $stmt;     
    }

    public function read_one($data, $option = NULL) {

        if($option == "_account_infos") {
            $query = "SELECT id, email, firstname, name, passport, department_nbr FROM " . $this->table_name;
            $query .= " WHERE id = ?";
        } else {
            $query = "SELECT u.id, ce_id, discount,  email, firstname, u.is_active, u.name, passport, ce.name AS ce_name";
            $query .= " FROM " . $this->table_name . " u"; 
            $query .= " INNER JOIN ce ON ce.id = u.ce_id";
            $query .= " WHERE u.id = ?";
        }

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