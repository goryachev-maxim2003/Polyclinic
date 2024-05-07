<?php 
try {
    require_once("../class/User.php");
    require_once("../class/Role.php");
    require_once("../class/PrivilegedUser.php");
    require_once('../connect.php');
    session_start();
    function saveData(){
        global $login, $password;
        $_SESSION["enter_data"]["login"] = $login;
        $_SESSION["enter_data"]["password"] = $password;
    }
    $login = $_POST['login'];
    $password = $_POST['password'];

    $found_login = $pdo->query("SELECT `user_id` FROM `users` WHERE `login`='$login'")->fetchAll();
    $found_password = $pdo->query("SELECT `user_id` FROM `users` WHERE `pass`='$password'")->fetchAll();
    if (!$found_login){
        $_SESSION["enter_data"]["err_login"] = "Неверный логин";
        saveData();
        header("Location: /");
    }
    elseif (!$found_password){
        $_SESSION["enter_data"]["err_password"] = "Неверный пароль";
        saveData();
        header("Location: /");
    }
    else{
        $_SESSION["login"] = $login;
        
        //Добавляем пользователя
        $_SESSION["PrivilegedUser"] = PrivilegedUser::getByUsername($_SESSION["login"]);
        $_SESSION["User"] = User::getByUsername($_SESSION["login"]);
        header("Location: /profile.php");
    }
}
catch(PDOException $e) {  
    echo $e->getMessage();  
}
?> 