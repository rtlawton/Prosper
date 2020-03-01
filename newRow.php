<?php 
require_once('utilities.php');
$id = $_POST["id"];
$ref = $_POST["ref"];
$bal = $_POST["bal"];
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS); 
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
try {
    $db->beginTransaction();
    $stmt = $db->prepare('UPDATE journal SET rec = "M" WHERE id = ?');
    $stmt->execute(array($id));
    $stmt = $db->prepare('CREATE TEMPORARY TABLE tmpJournal SELECT * FROM Journal WHERE id = ?');
    $stmt->execute(array($id));
    $db->exec('ALTER TABLE tmpJournal drop id');
    $db->exec('UPDATE tmpJournal SET fu_amount = 0.0, ac_amount = 0.0, ac = ' . AC_UNALLOCATED );
    $db->exec('INSERT INTO Journal SELECT 0, tmpJournal.* FROM tmpJournal');
    $db->exec('DROP TABLE tmpJournal');
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