<?php 
require_once('utilities.php');
$id = $_POST["id"];
$fu = $_POST["fu"];
$fu2 = $_POST["fu2"];
$ref = $_POST["ref"];
$bal = $_POST["bal"];
$dt = $_POST["dt"];
$am = $_POST["am"];

try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
try {
    $db->beginTransaction();
    $stmt = $db->prepare('UPDATE Journal SET fu2 = ? WHERE id = ?');
    $stmt->execute(array($fu2, $id));
    $stmt = $db->prepare('UPDATE Journal SET fu = ? WHERE (id != ?) AND (ref = ?)');
    $stmt->execute(array($fu2, $id, $ref));
	$sql = "SELECT id, dt, ABS(DATEDIFF(dt, '" . $dt. "')) AS D FROM Journal WHERE fu = ? AND fu_amount = ? AND ABS(DATEDIFF(dt, '" . $dt. "')) < 4 AND ac = ? ORDER BY D";
    $stmt = $db->prepare($sql);
    $arr = array($fu2, $am, AC_UNALLOCATED);
    $stmt->execute($arr);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($results) > 0) {
        $stmt = $db->prepare('UPDATE Journal SET ac = ?  WHERE id = ?');
        $stmt->execute(array(AC_DUPLICATE, $results[0]["id"]));
    }   
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