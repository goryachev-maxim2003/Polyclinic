<?php
//Фит
    $host = "std-mysql";//  имя  хоста
    $db = "std_1878_polyclinic";// имя бд
    $user = "std_1878_polyclinic";//имя пользователя
    $pass = "basso1marsu";//пароль к бд
    $charset = 'utf8'; //кодировка юникод (поддерживает кирилицу)
    
//localhost
    // $host = "localhost";//  имя  хоста
    // $db = "std_1878_polyclinic";// имя бд
    // $user = "root";//имя пользователя
    // $pass = "";//пароль к бд
    // $charset = 'utf8'; //кодировка юникод (поддерживает кирилицу)

// формируем данные для одключения
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";// mysql – название СУБД
    //Формируем переменную со служебными характеристиками //подключения
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,//правила обработки ошибок
        // PDO::FETCH_ASSOC - ассоциативные
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,//вид формируемых списков по умолчанию
        PDO::ATTR_EMULATE_PREPARES   => false, //отключаем эмуляцию
    ];
    //формируем подключение к БД
    $pdo = new PDO($dsn, $user, $pass, $opt);
?>
