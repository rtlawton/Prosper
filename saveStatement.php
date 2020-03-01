<?php 
require_once('config.php');
require_once('utilities.php');
$fu = $_POST["fu"];
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('Select protocol from funds where id = ?');
    $stmt->execute(array($fu));
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $modeltable = 'Model_' . $result[0]['protocol'];
    $stmt = $db->prepare("Show tables like '" . $modeltable . "'");
    //Has a model been built for allocation for this protocol?
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($result)>0) {
        $stmt = $db->prepare('Select * from ' . $modeltable . ' order by line');
        $stmt->execute();
        $model = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $model = NULL;
    }
    $stmt = $db->query('Select MAX(ref) as mref from journal');
    $newRef = intval($stmt->fetchColumn()) + 1;  
    $stmt = $db->query('SELECT dt, am, comm, duplicate, bal, DATE_FORMAT(dt,"%d-%b-%Y") as fdate FROM Statement ORDER BY id');
    $insert_sql = 'INSERT INTO Journal (ref, dt, fu, fu2, fu_amount, ac, ac_amount, comm, bal, rec, guess) ';
    $insert_sql .= 'VALUES (?, ?, ?, 11, ?, ?, ?, ?, ?, "S", ?)';
    $insert = $db->prepare( $insert_sql);
    //$log = $db->prepare("Insert into DebugLog (txt) values (?)");
    while($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $guess = 0;
        $ac = AC_UNALLOCATED;
        if ($result["duplicate"] == "Y") {
            $ac = AC_DUPLICATE;
        } elseif (! is_null($model)) {
            $frags = preg_split('/#|-|\ +|\*/', $result['comm']);
            $c_line = 0;
            $day = floatval(substr($result['fdate'],0,2));
            while ($c_line < count($model)) {
                $model_row = $model[$c_line];
                if ($model_row["frag"] != "") {
                    $var = (int)in_array($model_row["frag"], $frags);
                } elseif ($model_row["field"] == "day") {
                    $var = $day;
                } else {
                    $var = $result['am'];
                }
                $split_dir = ($var < $model_row['split']) ? "left" : "right";
                if ($model_row[$split_dir] == 0) {
                    $ac = (int)$model_row['value'];
                    break;
                } else {
                    $c_line = $model_row[$split_dir] - 1;
                }
            }         
            if ($ac != AC_UNALLOCATED) {
                $guess = 1;
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
