<?php 
require_once('config.php');

$id = $_POST["id"];

try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('DELETE FROM Journal WHERE id = ?');
    $stmt->execute(array($id));
} 
catch(PDOException $ex) {
    echo $ex->getMessage();
}
try {
    $stmt = $db->prepare(SQL_GETROWBYID);
    $stmt->execute(array($id));
    $transaction = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if (count($transaction) == 0) {
		echo "";
	} else {
    echo rowHTML($transaction,$bal);		
	}
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 

?>