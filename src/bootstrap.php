<?php

function dd(...$vars){
    foreach($vars as $var){
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }


}

function get_pdo(){
    return new PDO("mysql:host=localhost;dbname=tennis","root","",[
        PDO ::ATTR_ERRMODE => PDO :: ERRMODE_EXCEPTION,
        PDO :: ATTR_DEFAULT_FETCH_MODE =>PDO::FETCH_ASSOC
    ]);

}

function h(?string $value){
    if($value===null){
        return '';
    }

    return html_entity_decode($value);

}

function e404(){
    require '../public/404.php';
    exit();
}