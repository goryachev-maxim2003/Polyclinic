<?php
class User
{
    protected $user_id;
    protected $username;
    protected $password;
    protected $info;
    protected function __construct() {
        $this->info = array();
    }
    public static function getByUsername($username) {
        $pdo = $GLOBALS["pdo"];
        $sql = "SELECT * FROM `users` WHERE `login` = :username";
        $sth = $pdo->prepare($sql);
        $sth->execute(array("username" => $username));
        $result = $sth->fetchAll();
        if (!empty($result)) {
            $user = new User();
            $user->user_id = $result[0]["user_id"];
            $user->username = $username;
            $user->password = $result[0]["pass"];
            //Добавление информации для Врача
            if (isset($result[0]["Врач_id"])){
                //общая информация
                $sqlDoctor = "SELECT * FROM `Врач` WHERE `id` = :id";
                $selectDoctor = $pdo->prepare($sqlDoctor);
                $selectDoctor->execute(array("id" => $result[0]['Врач_id']));
                $resultDoctor = $selectDoctor->fetchAll();

                $user->info["id_Врача"] = $resultDoctor[0]["id"];
                $user->info["Имя"] = $resultDoctor[0]["Имя"];
                $user->info["Фамилия"] = $resultDoctor[0]["Фамилия"];
                $user->info["Отчество"] = $resultDoctor[0]["Отчество"];
                $user->info["Кабинет"] = $resultDoctor[0]["Кабинет"];

                // Специальность
                $sqlSpec = "SELECT `Название` FROM `Специальность` WHERE `id`=:id";
                $selectSpec = $pdo->prepare($sqlSpec);
                $selectSpec->execute(array("id" => $resultDoctor[0]["Специальность_id"]));
                $resultSpec = $selectSpec->fetchAll();
                $user->info["Специальность"] = $resultSpec[0]["Название"];
                
                //График
                $sqlSpec = "SELECT `День_недели`, `Начало_работы`, `Окончание_работы`  FROM `График_работы` WHERE `Врач_id`=:id";
                $selectSpec = $pdo->prepare($sqlSpec);
                $selectSpec->execute(array("id" => $resultDoctor[0]["id"]));
                $days = [1 => "Понедельник", 2 => "Вторник", 3 => "Среда", 4 => "Четверг", 5 => "Пятница", 6 => "Суббота", 7 => "Воскресенье"]; 
                while ($row = $selectSpec->fetch(PDO::FETCH_ASSOC)) {
                    $user->info[$days[$row["День_недели"]]." начало работы"] = $row["Начало_работы"];
                    $user->info[$days[$row["День_недели"]]." окончание работы"] = $row["Окончание_работы"];
                }

                //Поликлиника
                $polyclinic_id = $resultDoctor[0]["Поликлиника_id"];
            }
            //Добавление информации для пациента
            elseif (isset($result[0]["Пациент_id"])){
                //общая информация
                $sqlPatient = "SELECT * FROM `Пациент` WHERE `id` = :id";
                $selectPatient = $pdo->prepare($sqlPatient);
                $selectPatient->execute(array("id" => $result[0]['Пациент_id']));
                $resultPatient = $selectPatient->fetchAll();

                $user->info["id_Пациента"] = $resultPatient[0]["id"];
                $user->info["Имя"] = $resultPatient[0]["Имя"];
                $user->info["Фамилия"] = $resultPatient[0]["Фамилия"];
                $user->info["Отчество"] = $resultPatient[0]["Отчество"];
                $user->info["Дата рождения"] = $resultPatient[0]["Дата_рождения"];
                $user->info["Номер полиса"] = $resultPatient[0]["Номер_полиса"];
                
                //Поликлиника
                $polyclinic_id = $resultPatient[0]["Поликлиника_id"];
            }
            //Добавление информации и для пациента и для врача
            if (isset($result[0]["Пациент_id"]) || isset($result[0]["Врач_id"])){
                //Поликлиника
                $sqlPol = "SELECT p.`id`, p.`Номер`, p.`Адрес`, r.`Название` AS `Район`, g.`Название` AS `Город` FROM `Поликлиника` AS p 
                            JOIN `Район` AS r ON r.`id`=p.`Район_id`
                            JOIN `Город` AS g ON g.`id`=r.`Город_id` 
                            WHERE p.`id`=:id";
                $selectPol = $pdo->prepare($sqlPol);
                $selectPol->execute(array("id" => $polyclinic_id));
                $resultPol = $selectPol->fetchAll();
                $user->info["id поликлиники"] = $resultPol[0]["id"];
                $user->info["Номер поликлиники"] = $resultPol[0]["Номер"];
                $user->info["Адрес поликлиники"] = $resultPol[0]["Адрес"];
                $user->info["Район поликлиники"] = $resultPol[0]["Район"];
                $user->info["Город поликлиники"] = $resultPol[0]["Город"];
            }
            return $user;
        }
        else {
            return false;
        }
    }
    public function getInfo(){
        return $this->info;
    }
}
 ?>