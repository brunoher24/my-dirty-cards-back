<?php
include_once 'models/_database.php';
 
class Middleware_auth extends Database 
{    
    public function authChecker($auth_object) {
        switch ($auth_object) {
            case "CeManager_read_one":
            case "OfferManager_read_by_ce":
                $authLevel = "user";
                break;
            case "CeManager_read_in_select": 
            case "Offer_ceManager_create":
                $authLevel = "admin";
                break;
            default :
                $authLevel = "none";    
        }
    
        if($authLevel != "none") {
            $apache_request_headers         = apache_request_headers()["Authorization"];
            $headersAuthorizationDecoded    = base64_decode(explode(" ", $apache_request_headers)[1]);
            $authInfos                      = explode(":", $headersAuthorizationDecoded);
            $userId                         = $authInfos[0];
            $sessionToken                   = $authInfos[1];
            $hasCorrectAccess               = $this->checkAccess($userId, $sessionToken, $authLevel == "admin");
            
            if($hasCorrectAccess) {
                return true;
            } else {
                http_response_code(403);
                $headers = apache_request_headers();
                $test = "";
                foreach($headers as $key => $value) {
                    $test .= "$key: $value <br />\n";
                }

                echo json_encode(
                    array(
                        "message"   => "Can't access to this route.", 
                        "debug"     => array(
                            "auth_object"                   => $auth_object, 
                            "hasCorrectAccess"              => $hasCorrectAccess, 
                            "userId"                        => $userId, 
                            "sessionToken"                  => $sessionToken, 
                            "authLevel"                     => $authLevel, 
                            "authInfos"                     => $authInfos,
                            "headersAuthorizationDecoded"   => $headersAuthorizationDecoded,
                            "headers"                       => $test
                        )
                    )
                );
                return false;
            }
        } 
        // var_dump($auth_object);
        return true;
    }

    public function checkAccess($id, $token, $adminAccess)
    {
        $query1  = "SELECT session_account.id FROM session_account";
        $query2 .= " WHERE (account_id = ?) AND (session_account.id = ?) AND (created_at >= (NOW() - INTERVAL 1 DAY))";
        
        if ($adminAccess) {
            $query1 .= " INNER JOIN ce ON ce.id = session_account.account_id";
            $query2 .= " AND (ce.is_admin = 1)";
        }
        try {
            $db     = $this->db();

            $stmt   = $db->prepare($query1 . $query2);
            $stmt->execute([$id, $token]);
            // return [$query1 . $query2, $id, $token];
            return $stmt->rowCount() >= 1;
        } catch (PDOException $e) {
            throw new Exception('Database query error while checking access right for user');
        }
    }
}
