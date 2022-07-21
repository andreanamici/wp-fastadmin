<?php


function fa_encrypt($string){
    if(!WP_FA_ENCRYPT_SECRET_KEY){
        throw new \LogicException('Constant WP_FA_ENCRYPT_SECRET_KEY is not setted in configs/config.php');
    }
    if(!WP_FA_ENCRYPT_SECRET_IV){
        throw new \LogicException('Constant WP_FA_ENCRYPT_SECRET_IV is not setted in configs/config.php');
    }
    $secret_key = WP_FA_ENCRYPT_SECRET_KEY;
    $secret_iv  = WP_FA_ENCRYPT_SECRET_IV;
    $key = hash('sha256',$secret_key);
    $iv = substr(hash('sha256',$secret_iv),0,16);
    return base64_encode(openssl_encrypt($string,"AES-256-CBC",$key,0,$iv));
}

function fa_decrypt($string){
    if(!WP_FA_ENCRYPT_SECRET_KEY){
        throw new \LogicException('Constant WP_FA_ENCRYPT_SECRET_KEY is not setted in configs/config.php');
    }
    if(!WP_FA_ENCRYPT_SECRET_IV){
        throw new \LogicException('Constant WP_FA_ENCRYPT_SECRET_IV is not setted in configs/config.php');
    }
    $secret_key = WP_FA_ENCRYPT_SECRET_KEY;
    $secret_iv  = WP_FA_ENCRYPT_SECRET_IV;
    $key = hash('sha256',$secret_key);
    $iv = substr(hash('sha256',$secret_iv),0,16);
    return openssl_decrypt(base64_decode($string),"AES-256-CBC",$key,0,$iv);
}