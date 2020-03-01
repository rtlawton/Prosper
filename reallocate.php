<?php 
require_once('utilities.php');
$id = $_POST["id"];
$ref = $_POST["ref"];
$acamount = $_POST["acamount"];
$bal = $_POST["bal"];
$con = mysqli_connect('localhost','root','MYrtl2504', 'Prosper');
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS); 
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
try {
    $db->beginTransaction();
    $stmt = $db->prepare('UPDATE Journal SET ac_amount = ? WHERE id = ?');
    $stmt->execute(array($acamount, $id));
    $stmt = $db->prepare('SELECT id, fu_amount, ac_amount FROM Journal WHERE ref = ? ORDER BY id');
    $stmt->execute(array($ref));
    $tempBal = 0.0;
    $lastid = 0;
    $nextlastid = 0;
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tempBal += $row['fu_amount'] + $row['ac_amount'];
        $nextlastid = $lastid;
        $lastid = $row['id'];
    }
    if ($lastid == $id) {
        $lastid = $nextlastid;
    }
    $stmt = $db->prepare('UPDATE Journal SET ac_amount = ac_amount - ? WHERE id = ?');
    $stmt->execute(array($tempBal, $lastid));
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
    echo rowHTML($transaction,$bal);
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 


?>