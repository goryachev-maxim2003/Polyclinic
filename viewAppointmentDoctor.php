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
    elseif (!$_SESSION["PrivilegedUser"]->hasPrivilege("Просмотр записей ко врачу")) {
        header("Location: /profile.php");
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
    <title>Просмотр записей</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="main">
    <header><?php include 'header.php';?></header>
    <div class="content">
        <?php
            try{
                echo "Список записей: <br>";
                //Выводим записи, приёмы которых ещё не прошли
                $userDoctorId = $_SESSION["User"]->getInfo()["id_Врача"]; 
                $sqlAppointment = "SELECT z.`Время`, p.`Имя`, p.`Фамилия`, p.`Отчество`, p.`Дата_рождения`, p.`Номер_полиса` FROM `Запись_к_врачу` z 
                        JOIN `Направление` n ON z.`Направление_id`=n.`id`
                        JOIN `Пациент` p ON n.`Пациент_id`=p.`id`
                        WHERE z.`Врач_id`=$userDoctorId AND z.`Время`>CURRENT_DATE
                        ORDER BY z.`время`";
                $findAppointment = $pdo->query($sqlAppointment);
                while ($row = $findAppointment->fetch(PDO::FETCH_ASSOC)){
                    echo 'Записан пациент: '.$row["Имя"].' '.$row["Фамилия"].' '.$row["Отчество"].' Полис:'.$row["Номер_полиса"]
                        .' Время:'.$row["Время"].'<br>';
                }
            }
            catch(PDOException $e){
                echo $e->getMessage();
            }
        ?>
    </div>
    </div>
</body>
</html>