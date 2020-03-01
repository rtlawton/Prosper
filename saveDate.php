<?php 
require_once('utilities.php');
$id = $_POST["id"];
$y = $_POST["y"];
$m = $_POST["m"];
$d = $_POST["d"];
var_dump($y.'-'.$m.'-'.$d);
kill;
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('UPDATE Journal SET dt = ? WHERE id = ?');
    $stmt->execute(array($y.'-'.$m.'-'.$d, $id));
    $stmt = $db->prepare(SQL_GETROWBYID);
    $stmt->execute(array($id));
    $transaction = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo rowHTML($transaction,'');
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 

?>