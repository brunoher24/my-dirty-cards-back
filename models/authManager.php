<?php
include_once '_database.php';
include_once "./utilities.php";



class AuthManager extends Database
{
    public $authenticated = false;

    public function create($data)
    {
        $result = $this->create_($data, "user");
        return $result;
    }

    public function read_one($data)
    {
        $result = $this->login($data);
        return $result;
      
    }

    public function login($data)
    {
        $db = $this->db();

        $query = "SELECT user.id, user.pwd, user.is_active FROM user WHERE user.alias = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$data["alias"]]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user["is_active"] == 0) {
                return ["error", "Compte momentanément désactivé", $loggedin, null, 403];
            }

            $pwdIsCorrect = password_verify($data["pwd"], $user["pwd"]);
            
            if ($pwdIsCorrect) {
                $session_id         = $this->registerLoginSession($user["id"]);
                // $role_name          = $user["role_id"] == 2 ? "admin" : "user";

                return ["success", $user["id"], $session_id];
            } else {
                return ["error", "Mot de passe incorrect", $pwd, $user["pwd"], 403];
            }
        } else {
            return ["error", "Identifiant non reconnu", $query, $data["alias"], 403];
        }
    }

    private function loggout($account_id) 
    {
        try {
            $db = $this->db();
            $query = 'DELETE FROM session_account WHERE account_id = ?';
            $stmt = $db->prepare($query);
            $stmt->execute([$account_id]);
        } catch (PDOException $e) {
            throw new Exception('Database query error while deleting session_account');
        }
    }

    private function registerLoginSession($account_id)
    {
        try {
            $this->loggout($account_id);

           
            $randomId = GENERATE_RANDOM_STRING(24);
            
            $query  = "INSERT INTO session_account (id, account_id)"; 
            $query  .= " VALUES(?, ?)";

            $db     = $this->db();
            $stmt   = $db->prepare($query);
            $stmt->execute([$randomId, $account_id]);
            
            return $randomId;
        } catch (PDOException $e) {
            throw new Exception('Database query error while registering session');
        }
    }
}
