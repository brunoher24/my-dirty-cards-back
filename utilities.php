<?php

function SANITIZE_DATA($data) {
    // var_dump($data);
    $output = [];
    foreach ($data as $key => $value) {
        if (substr($key,0,1) == '_') {
            continue;
        } else if (!empty($value->isBoolean)) {
            $output[$key]   = !empty($value->val) ? 1 : 0;
        } else if (!empty($value->isHtmlEntity)) {
            $output[$key]   = !empty($value->val) ? "htmlentity" . htmlentities($value->val) : NULL;
        } else if (!empty($value->isImg)) {
            if(!empty($value->val)) {
                $filePath       = "./images/" . GENERATE_RANDOM_STRING(15) . ".jpeg";
                file_put_contents($filePath, file_get_contents($value->val));
                $filePath       = substr($filePath, 2);
                $output[$key]   = $filePath;
            } else {
                $output[$key] = NULL;
            }

            if(!empty($value->oldImg)) {
                unlink("./" . $value->oldImg);
            }
        } else if (!empty($value->shouldEncrypt)) {
            $output[$key]   = password_hash($value->val, PASSWORD_DEFAULT);
        } else {
            $output[$key]   = !empty($value->val) ? htmlspecialchars($value->val) : NULL;
        }
    }
    return $output;
}

function EXTRACT_DATA($data) {
    $output = [];
    foreach ($data as $key => $value) {
        if ($key == "img") {
            $output[$key] = base64_encode($value) || 'no-image';
        } else if(substr($value, 0, 10) == "htmlentity") {
            $decoded = html_entity_decode($value);
            $output[$key] = substr($decoded, 10);
            // var_dump($output[$key]);
        } else {
            $output[$key] = $value ?  $value : "";
        }
    }
    return $output;
}

function GENERATE_RANDOM_STRING($length){
    $chars = "acdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $charsLength = strlen($chars);
    $output = "_";
    while (strlen($output) <= $length) {
        $rand_index = rand(0, $charsLength - 1);
        $output .= $chars[$rand_index];
    }
    return $output;
}

function ENCRYPT($toEncrypt) {
   // Store the cipher method
    $ciphering = "AES-128-CTR";
    
    // Use OpenSSl Encryption method
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    
    // Non-NULL Initialization Vector for encryption
    $encryption_iv = '1234567891011121';
    
    // Store the encryption key
    $encryption_key = "davDADappCElyDadi";
    
    // Use openssl_encrypt() function to encrypt the data
    return openssl_encrypt($toEncrypt, $ciphering,
            $encryption_key, $options, $encryption_iv);
}

function DECRYPT($toDecrypt) {
    $ciphering = "AES-128-CTR";
    $options = 0;
    
   // Non-NULL Initialization Vector for decryption
    $decryption_iv = '1234567891011121';
  
    // Store the decryption key
    $decryption_key = "davDADappCElyDadi";
  
    // Use openssl_decrypt() function to decrypt the data
    return openssl_decrypt ($toDecrypt, $ciphering, 
        $decryption_key, $options, $decryption_iv);
}
