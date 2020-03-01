<!DOCTYPE html>
<html>
<head></head>
<body>
    
<?php
require_once('utilities.php');
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('Select comm, ac FROM journal WHERE (fu = 12) and (ac < 40 or ac > 47);');
    $stmt->execute(array());
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = 0;
    $stmt2 = $db->prepare('Insert into Merchants (name, ac) values (?,?);');
    foreach ($result as $row) {
        $st = $row['comm'];
        $st = strtoupper(trim($st));
        $c = substr($st,1,1);
        if ($c == '*' or $c == '#' or $c == ' ') {
            $st = substr($st,2);
        }
        if (is_numeric(substr($st,-1))){
            $st = trim(substr($st,0,strrpos($st, ' ') - 2));
            $n = strpos($st, '  ');
            if ($n > 0) {
                $st = substr($st,0,$n);
            }    
        }    
        $stmt2->execute(array($st, $row['ac']));
        $count++;      
    }    
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
echo $count;
$db = null; 
?>
</body>