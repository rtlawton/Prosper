<?php 
require_once('utilities.php');
$ref = $_POST["ref"];
$comm = $_POST["comm"];
$bal = $_POST["bal"];
$id = $_POST["id"];
$alloc = $_POST["alloc"];
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('UPDATE Journal SET comm = ? WHERE ref = ?');
    $stmt->execute(array($comm, $ref));
    if ($alloc == AC_TRANSFER or $alloc == AC_CASH) {
        $stmt = $db->prepare(SQL_GETROWBYID);
        $stmt->execute(array($id));
    } else {
        $stmt = $db->prepare(SQL_GETROWSBYREF);
        $stmt->execute(array($ref));
    }
    $transaction = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo rowHTML($transaction,$bal);
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 

?>