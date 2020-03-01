<?php 
require_once('utilities.php');
$id = $_POST["id"];
$ac = $_POST["ac"];
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
    $stmt = $db->prepare('UPDATE journal SET ac = ?, fu2 = ?, ac_amount = 0.0, rec = "T" WHERE id = ?');
    $stmt->execute(array($ac, $fu2, $id));
    $stmt = $db->prepare('CREATE TEMPORARY TABLE tmpJournal SELECT * FROM Journal WHERE id = ?');
    $stmt->execute(array($id));
    $db->exec('ALTER TABLE tmpJournal drop id');
    $db->exec('UPDATE tmpJournal SET fu_amount = -fu_amount, fu=(@temp:=fu), fu = fu2, fu2 = @temp');
    $db->exec('INSERT INTO Journal SELECT 0, tmpJournal.* FROM tmpJournal');
    $db->exec('DROP TABLE tmpJournal');
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