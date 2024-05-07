<?php 
    session_start();
    if (isset($_SESSION["login"])) {
        header("Location: /profile.php");
    }
    function if_isset($field){
        echo isset($_SESSION["reg_data"]["$field"]) ? $_SESSION["reg_data"]["$field"] : "";
    }
?>
<!DOCTYPE html>
<html> 
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="main">
    <div class="content">
    <form action="actions/reg.php" name = "g" method= "POST" onsubmit ="return f()">
        <label for="login">Логин</label><br>
        <input type="text" name="login" id="login" value=<?php if_isset("login") ?>><br>
        <span style="color:red"><?php if_isset("err_login") ?></span><br>

        <label for="password">Пароль</label><br>
        <input type="password" name="password" id="password" value=<?php if_isset("password") ?>><br><br>
        
        <label for="password2">Подтверждение пароля</label><br>
        <input type="password" name="password2" id="password2" value=<?php if_isset("password") ?>><br><br>

        <label for="polis">Полис(16 цифр)</label><br>
        <input type="text" name="polis" id="polis" pattern="[0-9]{16}" value=<?php if_isset("polis") ?>> <br>
        <span style="color:red"><?php if_isset("err_polis") ?></span><br>

        <input type="submit" value="Зарегистрироваться">
    </form>
    <a href="/">Я уже зарегистрирован</a>
    </div>
    </div>
    <?php 
        $_SESSION["reg_data"] = [];
    ?>
    <script>
        function f(){
            if (document.getElementById("password").value.length<5){
                alert("Пароль должен быть меньше 5 символов!")
                return false
            }
            else if (document.getElementById("password").value.length>30){
                alert("Пароль должен быть больше 30 символов!")
                return false
            }
            else if (document.getElementById("password").value!=document.getElementById("password2").value){
                alert("Пароли не совпадают")
                return false
            }
            else if (document.getElementById("login").value==""){
                alert("Введите логин!")
                return false
            }
            else if (document.getElementById("polis").value==""){
                alert("Введите Полис!")
                return false
            }

            else{
                return true
            }
        }
    </script>
</body>
</html>