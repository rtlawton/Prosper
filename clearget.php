<?php 
require_once('config.php');
$ref = $_POST["ref"];
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('Update journal set guess = 0 where ref = ?;');
    $stmt->execute(array($ref));
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 
?>
