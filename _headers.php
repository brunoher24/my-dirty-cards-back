<?php

if(!empty($_GET["env"]) && !empty($_GET["method"]) && !empty($_GET["action"]) && !empty($_GET["className"])) {

    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    switch ($_GET['env']) {
        case "devApp":
            header("Access-Control-Allow-Origin: http://localhost:8080");
            break;
        case "devBo":
            header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
            break;
        case "prod":
            header("Access-Control-Allow-Origin: *");
            break;
        default:
            http_response_code(403);
            echo json_encode(
                array("message" => "Invalid parameter 'env' in URL.")
            );
            return;
    }

    $method                 = htmlspecialchars($_GET["method"]); // POST, GET, UPDATE, DELETE
    $action                 = htmlspecialchars($_GET["action"]); // count, read, read_one, update, delete, ..
    $option                 = !empty($_GET["option"]) ? htmlspecialchars($_GET["option"]) : ""; // query options

    $crudClassName          = ucfirst($action) . "_"; // Count_, Read_, ...
    
    $fileName               = htmlspecialchars($_GET["className"]) . "Manager"; // ex : authManager, userManager, offerManager, ...
    $className              = ucfirst($fileName); // ex : AuthManager     
    
    $data                   = NULL;

    /* <--start--> MIDDLEWARES */
    include_once "./middleware_auth.php";
    $auth_object        = $className . "_" . $action . $option;
    $middelware_auth    = new Middleware_auth();
    $hasAccess          = $middelware_auth->authChecker($auth_object);
    if(!$hasAccess)  {
        return;
    }
    /* <--end--> MIDDLEWARES */

    
    header("Access-Control-Allow-Methods: " . $method);
    header("Content-Type: application/json; charset=UTF-8");

    include_once "./utilities.php";

    if($method == "POST") {
        $data = json_decode(file_get_contents("php://input"));
        $sanitized = SANITIZE_DATA($data);
    }


    // $dir_path = dirname(dirname(__FILE__)); // Get path to current working directory 
    $pathToManager = "./models".DIRECTORY_SEPARATOR.$fileName.".php";
    // ex : ./models/authManager.php
     
    include_once $pathToManager;
    include_once "./_crud/" . $action . ".php";

    // http_response_code(200);
    // echo json_encode(
    //     array(
    //         "foo"=> "bar", 
    //         "className" => $className,
    //         "crudClassName" => $crudClassName
    //     )
    // );
    // return;
    
    $manager        = new $className();
    $crudManager    = new $crudClassName();


    $result = $crudManager->exec($manager, $action, $sanitized, $option);
    
    // if($result[0] == "error"){
    //     http_response_code(500); 
    // } elseif($result[1][0] == "error") {
    //     http_response_code(200); 
    // }
    // else {
    //     http_response_code(200);
    // }
    
    http_response_code(200);
    echo json_encode(
        array(
            "records" => $result
        )
    );
    return;

    // // http_response_code(200);
    // // echo json_encode(array(
    // //     "auth_object"       => $auth_object,
    // //     "hasAccess"         => $hasAccess,
    // //     "headers"           => $headers,
    // //     "action"            => $action,
    // //     "crudClassName"     => $crudClassName,
    // //     "fileName"          => $fileName,
    // //     "className"         => $className,
    // //     "pathToManager"     => $pathToManager,
    // //     "sanitizedData"     => $sanitized,
    // //     "method"            => $method
    // // ));
} else {
    http_response_code(403);

    echo json_encode(
        array(
            "message"   => "Invalid parameters in URL.", 
            "URLParams" => $GET
        )
    );
}
