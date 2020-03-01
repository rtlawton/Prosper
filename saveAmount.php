<?php 
require_once('utilities.php');
$id = $_POST["id"];
$am = $_POST["am"];
if (substr($am,0,1) == '-') {
    $ama = substr($am,1);
} else {
    $ama = '-'.$am;
}
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('UPDATE Journal SET fu_amount = ?, ac_amount = ? WHERE id = ?');
    $stmt->execute(array($am, $ama, $id));
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