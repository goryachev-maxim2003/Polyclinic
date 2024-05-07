<?php

try {  
  # MS SQL Server и Sybase через PDO_DBLIB  
  // $DBH = new PDO("mssql:host=$host;dbname=$dbname", $user, $pass);  
  // $DBH = new PDO("sybase:host=$host;dbname=$dbname", $user, $pass);  
  
  // # MySQL через PDO_MYSQL  
  // $DBH = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);  
  
  // # SQLite  
  // $DBH = new PDO("sqlite:my/database/path/database.db");  

}  
catch(PDOException $e) {  
    echo $e->getMessage();  
}

require_once('connect.php');
$string = 'Nice';
print "Неэкранированная строка: $string\n";
print "Экранированная строка: " . $pdo->quote($string) . "\n";

//Запись
$add = $pdo->prepare("INSERT INTO `users` SET `login` = :log, `pass` = :pass, `Пациент_id` = :pat_id");
$add->execute(array('log' => $login, 'pass' => $password, 'pat_id' => 1));

//Запрос

$sql = "SELECT ";
$find = $pdo->query($sql);
while ($row = $find->fetch(PDO::FETCH_ASSOC)){
    $row["id"];
}


$sql = "SELECT  WHERE `id` = :id";
$select = $pdo->prepare($sql);
$select->execute(array("id" => $result[0]['Врач_id']));
$result = $select->fetchAll();


$found_polis = $pdo->query("SELECT `id` FROM `Пациент` WHERE `Номер_полиса`=$polis")->fetchAll();


// ?>


 <!-- <!DOCTYPE html> -->
<!-- // <html lang="en">
// <head>
//   <meta charset="UTF-8">
//   <meta http-equiv="X-UA-Compatible" content="IE=edge">
//   <meta name="viewport" content="width=device-width, initial-scale=1.0">
//   <title>Document</title>
// </head>
// <body>
//   <form action="">
//     <select name="a" id="1">
//       <?php 
//         try{
//           echo "<option value='asd'>";
//           $a = "asdas";
//           echo "asdasewssd";
//           echo "</option>";
//       ?>
//     </select>
//   </form>

// <?php 
//     echo $a;
//   }
//   catch(PDOException $e) {  
//       echo $e->getMessage();  
//   }
// ?>
// </body>
// </html> -->
