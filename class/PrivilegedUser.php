<?php
class PrivilegedUser extends User
{
    private $roles;
 
    public function __construct() {
        parent::__construct();
    }
    // Изменяем метод класса User
    public static function getByUsername($username) {
        $pdo = $GLOBALS["pdo"];
        $sql = "SELECT * FROM `users` WHERE `login` = :username";
        $sth = $pdo->prepare($sql);
        $sth->execute(array("username" => $username));
        $result = $sth->fetchAll();
        if (!empty($result)) {
            $privUser = new PrivilegedUser();
            $privUser->user_id = $result[0]["user_id"];
            $privUser->username = $username;
            $privUser->password = $result[0]["pass"];
            $privUser->initRoles();
            return $privUser;
        } 
        else {
                return false;
        }
    }
 
    // Наполняем объект roles соответствующими разрешениями
    protected function initRoles() {
        $pdo = $GLOBALS["pdo"];
        $this->roles = array();
        $sql = "SELECT t1.role_id, t2.role_name FROM user_role as t1
                JOIN roles as t2 ON t1.role_id = t2.role_id
                WHERE t1.user_id = :user_id";
        $sth = $pdo->prepare($sql);
        $sth->execute(array("user_id" => $this->user_id));
 
        while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
            $this->roles[$row["role_name"]] = Role::getRolePerms($row["role_id"]);
        }
    }
 
    // Проверяем, обладает ли пользователь нужными разрешениями
    public function hasPrivilege($perm) {
        foreach ($this->roles as $role) {
            if ($role->hasPerm($perm)) {
                return true;
            }
        }

        return false;
    }
}
?>