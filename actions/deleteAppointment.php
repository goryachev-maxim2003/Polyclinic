<?php 
try {
    require_once("../class/User.php");
    require_once("../class/Role.php");
    require_once("../class/PrivilegedUser.php");
    require_once('../connect.php');
    session_start();
    $appointmentId = $_GET["appointmentId"];
    $directionId = $_GET["directionId"];
    $onDirection = $_GET["onDirection"];
    if ($onDirection){ //Если по направлению, делаем направление опять действительным
        $returnDirection = $pdo->prepare("UPDATE `Направление` n SET n.`Использовано` = '0' WHERE n.`id` = :directionId");
        $returnDirection->execute(array('directionId' => $directionId));
    }
    //Удаляем запись
    $deleteAppointment = $pdo->prepare("DELETE FROM `Запись_к_врачу` z WHERE z.`id` = :appointmentId");
    $deleteAppointment->execute(array('appointmentId' => $appointmentId));
    header('location: /viewAppointmentPatient.php');
}
catch(PDOException $e) {  
    echo $e->getMessage();  
}
?>