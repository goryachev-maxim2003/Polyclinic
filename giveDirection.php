<?php
try {
    require_once("class/User.php");
    require_once("class/Role.php");
    require_once("class/PrivilegedUser.php");
    require_once("connect.php");
    session_start();
    if (empty($_SESSION["login"])) {
        header("Location: /");
    }
    elseif (!$_SESSION["PrivilegedUser"]->hasPrivilege("Выдать направление")) {
        header("Location: /profile.php");
    }
    function if_isset($field){
        return isset($_SESSION["giveDirect_data"]["$field"]) ? $_SESSION["giveDirect_data"]["$field"] : "";
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
    <title>Запись к врачу</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="main">
    <header><?php include 'header.php';?></header>
    <div class="content">
    <form action="actions/giveDirect.php" method="POST" onsubmit ="return f()">
        <?php
            try{
                $userPolyclinicId = $_SESSION["User"]->getInfo()["id поликлиники"];
                //Пациент
                echo '<p>Введите полис пациента из вашей поликлиники</p>';
                echo '<input type="text" name="patientPolis" pattern="[0-9]{16}"><br>';
                $ifIsset = if_isset("err_patientPolis");
                echo "<span style='color:red'>$ifIsset</span>";
                echo '<br><br>'; 
                //Врач
                echo '<p>Выберите врача к которому хотите дать направление</p>';
                echo '<select name="specId">';    
                    //только те специальности к которым нужны направления, и только те которые есть в поликлинике 
                    $sqlSpec = "SELECT DISTINCT s.`id`, s.`Название` FROM `Специальность` s
                                JOIN `Врач` v ON v.`Специальность_id`=s.`id`
                                JOIN `Поликлиника` p ON v.`Поликлиника_id`=p.`id`
                                WHERE p.`id`=$userPolyclinicId AND s.`По_направлению`=1";
                    $findSpec = $pdo->query($sqlSpec);
                    while ($row = $findSpec->fetch(PDO::FETCH_ASSOC)){
                        //Выводим option
                        echo '<option value="'.$row["id"].'">'.$row["Название"].'</option>';
                    }
                echo '</select>';
                echo '<br><br>'; 
        ?>
        <p>Введите длительность действия направления (дней)</p>
        <input type="number" name="validity" id="validity">  
        <br><br>
        <input type="submit" value="Выдать направление">
    </form>
    <?php
        $_SESSION["giveDirect_data"] = [];
    }
    catch(PDOException $e){
        echo $e->getMessage();
    }
    ?>
    </div>
    </div>
    <script>
        function f(){
            if (document.getElementById("validity").value == ""){
                alert("Введите полис")
                return false
            }
            if (document.getElementById("patientPolis").value == ""){
                alert("Введите полис")
                return false
            }
        }
    </script>
</body>
</html>