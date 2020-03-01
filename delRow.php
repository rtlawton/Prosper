<?php 
require_once('utilities.php');
$id = $_POST["id"];
$ref = $_POST["ref"];
$xbal = $_POST["bal"];
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
try {
    $db->beginTransaction();
    $stmt = $db->prepare('DELETE FROM Journal WHERE id = ?');
    $stmt->execute(array($id));
    $stmt = $db->prepare('SELECT id, fu_amount, ac_amount FROM Journal WHERE ref = ? ORDER BY id');
    $stmt->execute(array($ref));
    if ($stmt->rowCount() == 1) {
        $stmt = $db->prepare('UPDATE Journal SET rec = "S", ac_amount = -fu_amount WHERE ref = ?');
        $stmt->execute(array($ref));
    } else {
        $bal = 0.0;
        $lastid = 0;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bal += $row['fu_amount'] + $row['ac_amount'];
            $lastid = $row['id'];
        }
        $stmt = $db->prepare('UPDATE Journal SET ac_amount = ac_amount - ? WHERE id = ?');
        $stmt->execute(array($bal, $lastid));
    }
    $db->commit();
}
catch(PDOException $ex) {
    $db->rollBack();
    echo $ex->getMessage();
}
try {
    $stmt = $db->prepare(SQL_GETROWSBYREF);
    $stmt->execute(array($ref));
    $transaction = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo rowHTML($transaction,$xbal);
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 
?>