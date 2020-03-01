<?php 
require_once('utilities.php');
$am = $_POST["am"];
$aam = $_POST["aam"];
$dt = $_POST["dt"];
$fu = $_POST["fu"];
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
try {
    $db->beginTransaction();
    $stmt = $db->prepare('INSERT INTO Journal (dt, fu, ac, fu2, comm, fu_amount, ac_amount) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute(array($dt, $fu, AC_UNALLOCATED, FU_NONE, "", $am, $aam));
    $id = $db->lastInsertId();
    $stmt = $db->query('Select MAX(ref) as mref from journal');
    $Rmax = intval($stmt->fetchColumn()) + 1;
    $db->exec('UPDATE Journal Set ref = ' .$Rmax. ' WHERE id = ' .$id );
    $db->commit();
}
catch(PDOException $ex) {
    $db->rollBack();
    echo $ex->getMessage();
    var_dump($dt);
}
$stmt = $db->prepare(SQL_GETROWBYID);
$stmt->execute(array($id));
$transaction = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo rowHTML($transaction,'');
$db = null; 

?>