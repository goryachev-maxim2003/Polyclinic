<?php
    session_start();
    if (isset($_SESSION["login"])) {
        header("Location: /profile.php");
    }
    function if_isset($field){
        echo isset($_SESSION["enter_data"]["$field"]) ? $_SESSION["enter_data"]["$field"] : "";
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="main">
    <div class="content">
    Вход в аккаунт поликлиники
    <br>
    <form action="actions/enter.php" name = "g" method= "POST">
        <label for="login">Логин</label><br>
        <input type="text" name="login" id="login" value=<?php if_isset("login") ?>><br>
        <span style="color:red"><?php if_isset("err_login") ?></span><br><br>
        
        <label for="password">Пароль</label><br>
        <input type="password" name="password" id="password" value=<?php if_isset("password") ?>> <br>
        <span style="color:red"><?php if_isset("err_password") ?></span><br><br>

        <input type="submit" value="Войти"><br><br>

        <input type="reset">
    </form>
    <?php 
        $_SESSION["enter_data"] = [];
    ?>
    <a href="registration.php">Регистрация</a>
    </div>
    </div>
</body>
</html>