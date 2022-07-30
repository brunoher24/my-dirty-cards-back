<?php
include_once "_database.php";
include_once "__notificationService.php";

class GamePlayerManager extends Database {

    public $table_name = "game_player";

    public function create($data) {
        try {
            $db = $this->db();
            $query = "INSERT INTO " . $this->table_name . " (game_id, user_id, accepted) VALUES";
            $user_ids = explode(",", $data["user_ids"]);
            $params = [];
            $i = 0;
            $notification = new NotificationService();

            foreach ($user_ids as $value) {
                $query .= " (?,?,?),";
                if($i > 0) {
                    $notification->send([
                        "content"       => ["message" => "Tu as reçu une nouvelle invitation.", "gameId" => $data["game_id"]],
                        "chanelName"    => "user-" . $value,
                        "eventName"     => "invitation"
                    ]);
                    $accepted = 0;
                } else {
                    $accepted = 1;
                }
                
                array_push($params, $data["game_id"], $value, $accepted);
                
                $i++;
            }
            
            $query = substr($query, 0, -1);

            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $id = $db->lastInsertId();
            
            return ["success", $id, $query, $params];
        } catch (Exception $ex) {
            return ["error", $ex->getMessage(), null, null, 501];
        }         
    }

    public function read($data, $option = NULL) {  
        $query = "";
        if ($option == "_by_user_id") { // get game player created himself or accepted invitation for
            $query .=  "SELECT game_id";
            $query .= " FROM game_player";
            $query .= " WHERE user_id = ? AND accepted = 1";
        } else if ($option == "_by_game_id") { // get current game infos (players, status, ...)
            $query .= "SELECT user.alias, game.user_id AS game_master_id, game_player.accepted";
            $query .= " FROM game_player";
            $query .= " INNER JOIN user ON user.id = game_player.user_id";
            $query .= " INNER JOIN game ON game.id = game_player.game_id";
            $query .= " WHERE game_player.game_id = ?";
        } else if ($option == "_invitations") { // get invitations not accepted by user
            $query .= "SELECT game_player.id AS id, game_player.user_id AS user_id, game_player.game_id AS game_id, user.alias AS user_alias";
            $query .= " FROM game_player";
            $query .= " INNER JOIN user ON user.id = game.user_id";
            $query .= " WHERE game_player.user_id = ? AND game_player.accepted = 0";
        } 
        $stmt = $this->read_($query, $data);
        
        return $stmt;     
    }

    public function update($data) {
        $stmt = $this->update_($data);
        return $stmt;     
    }
    
	public function delete($data) {

        $query = 'DELETE FROM ' . $this->table_name . ' WHERE game_id = ? AND user_id = ?';
        $db = $this->db();
        $game_id = $data["game_id"];
        $user_id = $data["user_id"];
        $response = $db->prepare($query);
        // var_dump($response, $game_id, $user_id);
        if ($response->execute([$game_id, $user_id])) {
            return true;
        } else {
            return false;
        }     
    }
}