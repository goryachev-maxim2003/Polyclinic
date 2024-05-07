<?php
try {
    require_once('../connect.php');
    session_start();
    function saveData(){
        global $login, $password, $polis; 
        $_SESSION["reg_data"]["login"] = $login;
        $_SESSION["reg_data"]["password"] = $password;
        $_SESSION["reg_data"]["polis"] = $polis;
    }
    $login = $_POST['login'];
    $password = $_POST['password'];
    $polis = $_POST['polis'];
    $found_polis = $pdo->query("SELECT `id` FROM `Пациент` WHERE `Номер_полиса`=$polis")->fetchAll();
    $same_login = $pdo->query("SELECT `user_id` FROM `users` WHERE `login`='$login'")->fetchAll();
    $same_polis = $pdo->query("SELECT u.`user_id` FROM `users` u JOIN `Пациент` p ON u.`Пациент_id`=p.`id` AND p.`Номер_полиса`=$polis")->fetchAll();
    if ($same_login || $same_polis){
        if ($same_login){
            $_SESSION["reg_data"]["err_login"] = "Пользователь с таким логином уже существует";
            saveData();
            header("Location: /registration.php");
        }
        if ($same_polis){
            $_SESSION["reg_data"]["err_polis"] = "Пользователь с данным полисом уже зарегистрирован";
            saveData();
            header("Location: /registration.php");
        }
    }
    elseif ($found_polis){
        //добавляем пользователя
        $addUser = $pdo->prepare("INSERT INTO `users` SET `login` = :log, `pass` = :pass, `Пациент_id` = :pat_id");
        $addUser->execute(array('log' => $login, 'pass' => $password, 'pat_id' => $found_polis[0]["id"]));
        //добавляем роль
        $find_user = $pdo->query("SELECT `user_id` FROM users WHERE `login`='$login'")->fetchAll();
        $user_id = $find_user[0]["user_id"];
        $addUserRole = $pdo->prepare("INSERT INTO `user_role` SET `role_id` = :role_id, `user_id` = :user_id");
        $addUserRole->execute(array('role_id' => 2, 'user_id' => $user_id));//role_id = 2 - Пациент
        header("Location: /");
    }
    if (!$found_polis){
        $_SESSION["reg_data"]["err_polis"] = "Данного полиса не существует";
        saveData();
        header("Location: /registration.php");
    }
}
catch(PDOException $e) {  
    echo $e->getMessage();  
}
?>