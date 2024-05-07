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
    elseif (!$_SESSION["PrivilegedUser"]->hasPrivilege("Просмотр направлений для пациента")) {
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
    <title>Просмотр направлений</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="main">
    <header><?php include 'header.php';?></header>
    <div class="content">
        <?php
            try{
                echo "Список Направлений:<br>";
                //Направления ко врачам, которые требуют направление (Срок не истёк и направление не использовано)
                $userPatientId = $_SESSION["User"]->getInfo()["id_Пациента"]; 
                $sqlDirection = "SELECT s.`Название`, n.`Дата_выдачи`, n.`Срок_действия` FROM `Направление` n
                        JOIN `Специальность` s ON n.`Специальность_id`=s.`id` 
                        WHERE n.`Пациент_id`= $userPatientId AND s.`По_направлению` = 1 AND n.`Использовано` = 0
                        AND DATE_ADD(n.`Дата_выдачи`, INTERVAL n.`Срок_действия` DAY)>CURRENT_DATE";
                $findDirection = $pdo->query($sqlDirection);
                while ($row = $findDirection->fetch(PDO::FETCH_ASSOC)){
                    $endData = strtotime($row["Дата_выдачи"])+24*60*60*$row['Срок_действия']; 
                    $d = $endData-time();
                    $daysToEnd = floor($d/24/60/60);
                    $hoursToEnd = floor(($d-$daysToEnd*24*60*60)/60/60);
                    $minutesToEnd = floor(($d-$daysToEnd*24*60*60-$hoursToEnd*60*60)/60);
                    echo 'Направление ко врачу: '.$row["Название"].', выдано:'.$row["Дата_выдачи"].', истекает '
                        .date("Y-m-d", $endData).', осталось до истечения срока:'.'Дни:'.$daysToEnd.', часы:'
                        .$hoursToEnd.', минуты:'.$minutesToEnd.'<br>';

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