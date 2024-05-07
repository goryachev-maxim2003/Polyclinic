<?php 
try {
    require_once("class/User.php");
    require_once("class/Role.php");
    require_once("class/PrivilegedUser.php");
    require_once("connect.php");
    session_start();
    $getSpec = $_GET["spec"];
    $specNotChange = $getSpec==$_SESSION["preGetParameters"]["spec"];
    $getDoctor_id = $_GET["doctor_id"];
    $doctorNotChange = $getDoctor_id==$_SESSION["preGetParameters"]["doctor_id"];
    $getDay = $_GET["day"];
    $dayNotChange = $getDay==$_SESSION["preGetParameters"]["day"];
    $getTime = $_GET["time"];
    if (empty($_SESSION["login"])) {
        header("Location: /");
    }
    elseif (!$_SESSION["PrivilegedUser"]->hasPrivilege("Запись к врачу")) {
        header("Location: /profile.php");
    }
    //Если записались, отправляем к просмотру записей
    //если (специальность и доктор и день не поменялись ) и выбрано время 
    if ($specNotChange && $doctorNotChange && $dayNotChange && isset($getTime)){
        header("Location: /viewAppointmentPatient.php");
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
    <?php
    try{
    //Специальность
    //Гет специальность  
    echo '<form action="" method="GET" name="F">';
        echo '<p>Выберите врача из вашей поликлиники</p>';
        echo '<select name="spec">';
        //Выводим тех врачей к кому есть (поверяем не истёк ли срок и не использовано ли) направление либо к кому оно не требуется
                $userPolyclinicId = $_SESSION["User"]->getInfo()["id поликлиники"];
                $userPatientId = $_SESSION["User"]->getInfo()["id_Пациента"];     
                $findDirection = $pdo->query("SELECT `id` FROM `Направление` n WHERE n.`Пациент_id`=$userPatientId")->fetchAll();
                
                echo isset($findDirection);
                if  (!$findDirection){//Если нет направлений у пациента выводим только врачей к которым оно не нужно
                            $sqlSpec = "SELECT DISTINCT `Название` FROM `Специальность` s
                            JOIN `Врач` v ON v.`Специальность_id`=s.`id`
                            JOIN `Поликлиника` p ON v.`Поликлиника_id`=p.`id`
                            AND p.`id`=$userPolyclinicId 
                            AND s.`По_направлению`=0";   
                }
                else{//Если есть направление
                    $sqlSpec = "SELECT DISTINCT `Название` FROM `Специальность` s
                            JOIN `Врач` v ON v.`Специальность_id`=s.`id`
                            JOIN `Поликлиника` p ON v.`Поликлиника_id`=p.`id`
                            JOIN `Направление` n
                            WHERE n.`Пациент_id`=$userPatientId 
                            AND p.`id`=$userPolyclinicId 
                            AND (s.`По_направлению`=0 OR n.`Специальность_id`=s.`id` AND DATE_ADD(n.`Дата_выдачи`, INTERVAL n.`Срок_действия` DAY)>CURRENT_DATE AND n.`Использовано`=0)
                            ";
                            //доступные всем врачи или есть направление(не просроченное, не использованное) - последняя строка запроса
                            
                }
                $findSpec = $pdo->query($sqlSpec);
                while ($row = $findSpec->fetch(PDO::FETCH_ASSOC)){
                    //Запоминаем выбор
                    $selected = ($getSpec==$row["Название"]) ? "selected" : "";
                    //Выводим option
                    echo '<option '.$selected.' value="'.$row["Название"].'">'.$row["Название"].'</option>';
                }
        echo '</select>';
        echo '<br><br>';      

    //Врачи
        //если выбрана специальность
        if (isset($getSpec)){
            echo "Врачи специальность: ".$getSpec."<br>";
            echo '<select name="doctor_id">';
                $sqlDoctor = "SELECT v.`id`,  v.`Имя`, v.`Фамилия`, v.`Отчество`, v.`Кабинет` FROM `Врач` v 
                            JOIN `Специальность` s ON v.`Специальность_id`=s.`id`
                            JOIN `Поликлиника` p ON v.`Поликлиника_id`=p.`id`
                            WHERE p.`id`=$userPolyclinicId AND s.`Название`='$getSpec'";
                $findDoctor = $pdo->query($sqlDoctor);
                
                while ($row = $findDoctor->fetch(PDO::FETCH_ASSOC)){
                    //Запоминаем выбор
                    $selected = ($getDoctor_id==$row["id"]) ? "selected" : "";
                    //Выводим option
                    echo '<option '.$selected.' value="'.$row["id"].'">'.
                    $row["Имя"].' '.$row["Фамилия"].' '.$row["Отчество"].' Кабинет №'.$row["Кабинет"]
                    .'</option>';
                }
                echo "</select>";
                echo '<br><br>';
        }
    // Приём
        //если специальность не поменяли и выбран доктор
        if ($specNotChange && isset($getDoctor_id)){
            //Вывод врача
            echo "Выбор приёма: ";
            $findDoctor = $pdo->query($sqlDoctor);
            while ($row = $findDoctor->fetch(PDO::FETCH_ASSOC)){
                if ($doctor_id==$row["id"]){
                    echo $row["Имя"].' '.$row["Фамилия"].' '.$row["Отчество"].' специальность: '.$getSpec.', Кабинет №'.$row["Кабинет"].'<br>';
                }
            }
            
            //Для вывода даты
            date_default_timezone_set('UTC+3');
            $months = array(
                '01' => 'Января', '02' => 'Февраля', '03' => 'Марта', '04' => 'Апреля',
                '05' => 'Мая', '06' => 'Июня', '07' => 'Июля', '08' => 'Августа',
                '09' => 'Сентября', '10' => 'Октября', '11' => 'Ноября', '12' => 'Декабря'
            );
            $days = array(
                1 => 'Понедельник', 2 => 'Вторник', 3 => 'Среда',
                4 => 'Четверг', 5 => 'Пятница', 6 => 'Суббота', 7 => 'Воскресенье'
            );
            
            // День
            echo"<br>";
                echo '<select name="day">';
                    $sqlPriem = "SELECT `День_недели`, `Начало_работы`, `Окончание_работы` FROM `График_работы` WHERE `Врач_id`=$getDoctor_id";
                    $findPriem = $pdo->query($sqlPriem);
                    $resultPriem = $findPriem->fetchAll(PDO::FETCH_ASSOC);
                    $quantityDaysForAppointment = 30;
                    for ($i=0; $i<$quantityDaysForAppointment; $i++){
                        $time = time()+24*60*60*$i;
                        $day_of_week = $days[date("N", $time)];
                        $day = date("j", $time);
                        $month = $months[date("m", $time)];
                        $year = date("o", $time);
                        $value_date = $day_of_week.' '.$day.' '.$month.' '.$year;
                        
                        //Проверка есть ли день
                        $dayInTimetable = false;
                        foreach ($resultPriem as $row){
                            if ($row["День_недели"]==date("N", $time)){
                                $dayInTimetable = true;
                            }
                        }                        
                        if ($dayInTimetable){
                            $selected = "";
                            //Запоминаем выбор
                            if ($getDay==$i){
                                $selected = "selected";
                                $selectedDay = $value_date;
                            };
                            //Выводим option
                            echo '<option '.$selected.' value="'.$i.'">'.$value_date.'</option>';

                        }
                        
                    }
                echo "</select>";
            
        }
    //Время приёма
        //если (специальность и доктор не поменялись ) и выбран день 
        if ($specNotChange && $doctorNotChange && isset($getDay)){
            //Вывод времени
            echo "<br><br>Возможные приёмы в : ".$selectedDay.'<br>';
                $i = $getDay;
                $time = time()+24*60*60*$i;
                $day_of_week = date("N", $time);
                $day = date("j", $time);
                $monthIndex = date("m", $time);
                $month = $months[$monthIndex];
                $year = date("o", $time);
                $priem_duration = $pdo->query("SELECT `Длительность_приёма` FROM `Специальность` WHERE `Название`='$getSpec'")->fetchAll()[0]["Длительность_приёма"];
                
                
                $sqlTime = "SELECT g.`День_недели`, 
                            date_format(g.`Начало_работы`, '%H') AS `Начало_час`, date_format(g.`Начало_работы`, '%i') AS `Начало_минута`, 
                            date_format(g.`Окончание_работы`, '%H') AS `Окончание_час`, date_format(g.`Окончание_работы`, '%i') AS `Окончание_минута`
                            FROM `График_работы` g 
                            JOIN `Врач` v ON g.`Врач_id` = v.`id`
                            WHERE v.`id`= $getDoctor_id AND g.`День_недели`=$day_of_week";
                $findTime = $pdo->query($sqlTime);
                $resultTime = $findTime->fetchAll();
                $startTimeHour = $resultTime[0]["Начало_час"];
                $startTimeMinute = $resultTime[0]["Начало_минута"];
                $endTimeHour = $resultTime[0]["Окончание_час"];
                $endTimeMinute = $resultTime[0]["Окончание_минута"];

                $startTime = strtotime("$day.$monthIndex.$year $startTimeHour:$startTimeMinute");
                $endTime = strtotime("$day.$monthIndex.$year $endTimeHour:$endTimeMinute");
                //Находим занятое время
                $sqlBusyTimes = "SELECT z.`Время` FROM `Запись_к_врачу` z WHERE z.`Врач_id`=$getDoctor_id 
                                                    AND date_format(z.`Время`,'%e')=$day
                                                    AND date_format(z.`Время`,'%m')=$monthIndex
                                                    AND date_format(z.`Время`,'%Y')=$year";
                $busyTimes = array();
                $findBusyTimes = $pdo->query($sqlBusyTimes);
                while ($row = $findBusyTimes->fetch(PDO::FETCH_ASSOC)){
                    $busyTimes[] = strtotime($row["Время"]);
                }

                
                if (time()>$endTime){//Если текущее время больше последнего сеанса
                    echo "К сожалению на данную дату сейчас нет записей";
                }
                else{
                    echo '<select name="time">';
                        //Выводим время за сегодня пока оно меньше окончания работы и если оно больше текущего времени
                        $priemTime = $startTime;
                        $i = 0;
                        while ($priemTime<$endTime){
                            if ($priemTime>=time()){
                                $value_time =  date("H:i", $priemTime);
                                //Запоминаем выбор
                                $selected = "";
                                if ($getTime==$i){
                                    $selected = "selected";
                                    $selectedTime = $priemTime;
                                }
                                //Проверяем не занято ли время
                                $gray = "";
                                $disabled="";
                                foreach($busyTimes as $value ) {
                                    echo $value;
                                    if ($priemTime==$value){
                                        $gray = 'style="background-color: #b8c0c2;" ';
                                        $disabled = 'disabled="disabled" ';
                                        break;
                                    }
                                }
                                //Выводим option
                                echo '<option '.$gray.$disabled.$selected.' value="'.$i.'">'.$value_time.'</option>';
                            }
                            $priemTime+=$priem_duration*60;
                            $i++;
                        }   
                    echo "</select>";
                    echo '<br><br>';
                }   

        }
            $submitText = (isset($getDay)) ? "Записаться" : "Найти";
            echo '<br><br><input type="submit" value="'.$submitText.'"><br><br>';
        echo '</form>';


        //Запись в бд
        //если (специальность и доктор и день не поменялись ) и выбрано время
        if ($specNotChange && $doctorNotChange && $dayNotChange && isset($getTime)){
            $findSpec = $pdo->query("SELECT `id`, `По_направлению` FROM `Специальность` WHERE `Название`='$getSpec'")->fetchAll();
            $needDirection = $findSpec[0]["По_направлению"];
            $specId = $findSpec[0]["id"];
            //ищем направление
            $findDirections = $pdo->query("SELECT n.`id` FROM `Направление` n JOIN `Специальность` s ON n.`Специальность_id`=s.`id` WHERE s.`id`=$specId  AND n.`Пациент_id`=$userPatientId")->fetchAll();
            if (!$needDirection && !$findDirections){ //Если направление не требовалось и его ещё не создавали в бд
                //добавляем направление
                
                $addDirection = $pdo->prepare("INSERT INTO `Направление` SET 
                                               `Дата_выдачи` = :data, 
                                               `Срок_действия` = 0,
                                               `Использовано` = 0,
                                               `Специальность_id` = :specId,
                                               `Пациент_id` = :patientId");
                $addDirection->execute(array('data' => date("Y-m-d H:i:s", time()), 'specId' => $specId, 'patientId' => $userPatientId));
                
            }
            
            $findDirections = $pdo->query("SELECT n.`id` FROM `Направление` n JOIN `Специальность` s ON n.`Специальность_id`=s.`id` WHERE s.`id`=$specId  AND n.`Пациент_id`=$userPatientId")->fetchAll();
            //добавляем запись
            $addDirection = $pdo->prepare("INSERT INTO `Запись_к_врачу` SET 
                                               `Время` = :data, 
                                               `На_дом` = 0,
                                               `Направление_id` = :appointmentId,
                                               `Врач_id` = :doctorId");
            $addDirection->execute(array('data' => date("Y-m-d H:i:s", $selectedTime), 'appointmentId' => $findDirections[0]['id'], 'doctorId' => $getDoctor_id));
            
            
            
            //Делаем направление использованным, если запись была по направлению
                //Проверяем была ли запись по направлению
                $sqlSpec = $pdo->query("SELECT `По_направлению` FROM `Специальность` WHERE `Название` = '$getSpec'")->fetchAll();
                if ($sqlSpec[0]["По_направлению"]){//Если выбрана специальность по направлению
                    //Находим направление по которому это можно сделать
                    $sqlDirection = "SELECT  n.`id` FROM `Направление` n 
                            JOIN `Специальность` s ON n.`Специальность_id`=s.`id`
                            WHERE n.`Пациент_id` = $userPatientId AND s.`Название` = '$getSpec'
                            AND DATE_ADD(n.`Дата_выдачи`, INTERVAL n.`Срок_действия` DAY)>CURRENT_DATE AND n.`Использовано`=0
                            ";
                            //есть направление(не просроченное, не использованное) - последняя строка запроса
                    $usedDirectionId = $pdo->query($sqlDirection)->fetchAll()[0]['id'];
                    
                    //Делаем использованным
                    $makeUsed = $pdo->prepare("UPDATE `Направление` SET `Использовано`=1 WHERE `id`=$usedDirectionId");
                    $makeUsed->execute();
                } 
        }



        //Массив с предыдущии гет парамерами для того, чтобы убрать формы уровнем ниже при изменении запроса формы уровня выше
        $_SESSION["preGetParameters"]["spec"] = $getSpec;
        $_SESSION["preGetParameters"]["doctor_id"] = $getDoctor_id;
        $_SESSION["preGetParameters"]["day"] = $getDay;
    }
    catch(PDOException $e) {  
        echo $e->getMessage();  
    }
    ?>
    </div>
    </div>
</body>
</html>