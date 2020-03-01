<?php 
require_once('utilities.php');
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
try {
    $db->beginTransaction();
    $c = $db->exec('INSERT INTO Journal (dt, fu, ac, fu2, comm) VALUES (CURDATE(), ' . FU_CASH . ', ' . AC_UNALLOCATED . ', ' .FU_NONE. ', "")');
    $id = $db->lastInsertId();
    $stmt = $db->query('Select MAX(ref) as mref from journal');
    $Rmax = intval($stmt->fetchColumn()) + 1;
    $db->exec('UPDATE Journal Set ref = ' .$Rmax. ' WHERE id = ' .$id );
    $db->commit();
}
catch(PDOException $ex) {
    $db->rollBack();
    echo $ex->getMessage();
}
$stmt = $db->prepare(SQL_GETROWBYID);
$stmt->execute(array($id));
$transaction = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo rowHTML($transaction,'');
$db = null; 

?>