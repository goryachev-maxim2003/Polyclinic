<?php 
try {
    require_once("../class/User.php");
    require_once("../class/Role.php");
    require_once("../class/PrivilegedUser.php");
    require_once('../connect.php');
    session_start();
    $userPolyclinicId = $_SESSION["User"]->getInfo()["id поликлиники"];
    $patientPolis = $_POST['patientPolis'];
    $patientId = $pdo->query("SELECT `id` FROM `Пациент` WHERE `Номер_полиса`=$patientPolis AND `Поликлиника_id`=$userPolyclinicId")->fetchAll()[0]['id'];
    if (isset($patientId)){
        $add = $pdo->prepare("INSERT INTO `Направление` SET `Дата_выдачи` = :data, `Срок_действия` = :validity, `Использовано` = 0, `Пациент_id` = :pat_id, `Специальность_id` = :spec_id ");
        $add->execute(array('data' => date("Y-m-d H:i:s", time()), 'validity' => $_POST['validity'], 'pat_id'=>$patientId, 'spec_id' => $_POST['specId']));
    }
    else{
        $_SESSION["giveDirect_data"]["err_patientPolis"] = "Пациент с таким полисом не найден";
    }
    header('location: /giveDirection.php');
}
catch(PDOException $e) {  
    echo $e->getMessage();  
}
?>