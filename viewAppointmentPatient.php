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
    elseif (!$_SESSION["PrivilegedUser"]->hasPrivilege("Просмотр записей пациента")) {
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
                //Записи пациента, срок приёма которого не истёк
                $userPatientId = $_SESSION["User"]->getInfo()["id_Пациента"]; 
                $sqlAppointment = "SELECT z.`id` AS zID, n.`id` AS nID, z.`Время`, s.`Название`, v.`Имя`, v.`Фамилия`, v.`Отчество`, v.`Кабинет`, s.`По_направлению` FROM `Запись_к_врачу` z 
                        JOIN `Врач` v ON z.`Врач_id`=v.`id`
                        JOIN `Специальность` s ON v.`Специальность_id`=s.`id`
                        JOIN `Направление` n ON z.`Направление_id`=n.`id`
                        WHERE Пациент_id=$userPatientId AND z.`Время`>CURRENT_DATE
                        ORDER BY z.`Время`";
                $findAppointment = $pdo->query($sqlAppointment);
                while ($row = $findAppointment->fetch(PDO::FETCH_ASSOC)){
                    //запись
                    echo 'Запись к врачу: '.$row["Название"].' '.$row["Имя"].' '.$row["Фамилия"].' '.$row["Отчество"]
                        .' Кабинет №'.$row["Кабинет"].' '.$row["Время"].' ';
                    //Кнопка отмены записи
                    $appointmentId = $row["zID"];
                    $directionId = $row["nID"];
                    $onDirection = $row["По_направлению"];
                    echo "<a href = 'actions/deleteAppointment.php/?appointmentId=$appointmentId&directionId=$directionId&onDirection=$onDirection'>Отменить запись</a><br>";
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