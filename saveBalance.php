<?php 
require_once('utilities.php');
$id = $_POST["id"];
$am = $_POST["am"];
$bal = $_POST["bal"];
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('UPDATE Journal SET bal = ? WHERE id = ?');
    $stmt->execute(array($am, $id));
    $stmt = $db->prepare(SQL_GETROWBYID);
    $stmt->execute(array($id));
    $transaction = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo rowHTML($transaction,$bal);
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 

?>