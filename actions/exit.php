<?php 
try {
    session_start();
    $_SESSION = [];
    session_destroy();
    header("Location: /");
}
catch(PDOException $e) {  
    echo $e->getMessage();  
}
?>