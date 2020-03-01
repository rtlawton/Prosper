<?php 
require_once('utilities.php');
$id = $_POST["id"];
$ac = $_POST["ac"];
$ref = $_POST["ref"];
$bal = $_POST["bal"];
if ($ac == AC_TRANSFER) {
    $fu2 = FU_NONE;
} else {
    $fu2 = FU_CASH;
}
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
try {
    $db->beginTransaction();
    $stmt = $db->prepare('UPDATE Journal SET ac = ?, fu2 = ? WHERE id = ?');
    $stmt->execute(array($ac, $fu2, $id));
    $stmt = $db->prepare('UPDATE Journal SET ac = ?, fu = ? WHERE (id != ?) AND (ref = ?)');
    $stmt->execute(array($ac, $fu2, $id, $ref));
    $db->commit();
}
catch(PDOException $ex) {
    $db->rollBack();
    echo $ex->getMessage();
}
try { 
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