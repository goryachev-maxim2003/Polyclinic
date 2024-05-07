<?php 
    require_once("class/User.php");
    require_once("class/Role.php");
    require_once("class/PrivilegedUser.php");
    session_start();
    if (isset($_SESSION["login"])) {
        $PrivilegedUser = PrivilegedUser::getByUsername($_SESSION["login"]);
    }
?>
<nav>
    <?php if ($PrivilegedUser->hasPrivilege("Запись к врачу")):?>
        <a class="nav_link" href="appointment.php">Запись к врачу</a>
    <?php endif;?>

    <?php if ($PrivilegedUser->hasPrivilege("Выдать направление")):?>
        <a class="nav_link" href="giveDirection.php">Выдать направление</a>
    <?php endif;?>

    <?php if ($PrivilegedUser->hasPrivilege("Просмотр направлений для пациента")):?>
        <a class="nav_link" href="viewDirectionPatient.php">Просмотр направлений</a>
    <?php endif;?>
    <?php if ($PrivilegedUser->hasPrivilege("Просмотр записей пациента")):?>
        <a class="nav_link" href="viewAppointmentPatient.php">Просмотр записей</a>
    <?php endif;?>
    
    <?php if ($PrivilegedUser->hasPrivilege("Просмотр записей ко врачу")):?>
        <a class="nav_link" href="viewAppointmentDoctor.php">Просмотр записей</a> 
    <?php endif;?>
    
    <?php if (isset ($_SESSION["login"])):?>
        <a class="nav_link" href="profile.php">Профиль</a>
    <?php else: ?>
        <a class="nav_link" href="index.php">Вход</a>
    <?php endif;?>
</nav>
