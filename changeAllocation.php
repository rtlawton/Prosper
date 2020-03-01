<?php 
require_once('config.php');
$id = $_POST["id"];
$ac = $_POST["ac"];
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('UPDATE Journal SET ac = ? WHERE id = ?');
    $stmt->execute(array($ac, $id));
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 

?>