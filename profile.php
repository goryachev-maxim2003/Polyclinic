<?php 
try {
    require_once("class/User.php");
    require_once("class/Role.php");
    require_once("class/PrivilegedUser.php");
    session_start();
    require_once("connect.php");
    if (empty($_SESSION["login"])){
        header("Location: /");
    }
}
catch(PDOException $e) {  
    echo $e->getMessage();  
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="main">
    <header><?php include 'header.php';?></header>
    <div class="content">
    <?php
    echo "Логин: ".$_SESSION["login"]."<br>";
    foreach($_SESSION["User"]->getInfo() as $key => $value)
    {
        if (mb_substr($key, 0, 2, 'UTF-8')!="id"){
            echo $key . ': ' . $value . '<br>';	
        }
    }
    ?>
    <a href="/actions/exit.php">Выйти</a>
    </div>
    </div>
</body>
</html>