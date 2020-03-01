<?php 
require_once('config.php');
require_once('utilities.php');
$fu = $_POST["fu"];
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('Select matchtable from funds where id = ?');
    $stmt->execute(array($fu));
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $matchtable = $result[0]['matchtable'];
    $stmt = $db->query('Select MAX(ref) as mref from journal');
    $newRef = intval($stmt->fetchColumn()) + 1;  
    $stmt = $db->query('SELECT dt, am, comm, duplicate, bal FROM Statement ORDER BY id');
    $insert = $db->prepare('INSERT INTO Journal (ref, dt, fu, fu2, fu_amount, ac, ac_amount, comm, bal, rec, guess) VALUES (?, ?, ?, 11, ?, ?, ?, ?, ?, "S", ?)');
    //$log = $db->prepare("Insert into DebugLog (txt) values (?)");
    while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $guess = 0;
        $ac = AC_UNALLOCATED;
        if ($result["duplicate"] == "Y") {
            $ac = AC_DUPLICATE;
        } elseif ($matchtable != '') {
            $sql = 'select ac from ' . $matchtable . ' where "'. cleanstring($result["comm"],$fu) .'" like concat("%", matchstring, "%");';
            $stmt2 = $db->query($sql);
            $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            //$log->execute(array(cleanstring($result["comm"],$fu) . " " . strval(count($result2)) . " matches"));
            if (count($result2) == 1) {
                $ac = $result2[0]["ac"];
                if ($ac != AC_UNALLOCATED) {
                    $guess = 1;
                }
            } 
        }
        $insert->execute(array($newRef, $result["dt"], $fu, $result["am"], $ac, -$result["am"], $result["comm"], $result["bal"],$guess));
        ++$newRef;
    }   
    $db->exec("DELETE FROM Statement;");
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 
?>
