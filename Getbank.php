<!DOCTYPE html>
<html>
<head></head>
<body>
    
<?php
require_once('utilities.php');
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('select comm, ac, count(id) as ct from journal where fu > 12 and (ac < 40 or ac > 47) and comm != "" group by comm, ac order by comm;');
    $stmt->execute(array());
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = 0;
    $stmt2 = $db->prepare('Insert into Bankmatch (matchstring, ac) values (?, ?);');
    $c = count($result);
    $r = 0;
    while ($r < $c) {
        $row = $result[$r];
        if ($r == $c - 1) {
            $stmt2->execute(array($row['comm'], $row['ac']));
            $count++;
            if ($count > 1000) {
                break;
            }
            $r++;
        } else {
            $k = 1;
            while ($r + $k < $c) {
                if ($result[$r+$k]['comm'] != $row['comm']) {
                    break;
                }
                $k++;
            }
            if ($k > 1) {
                $stmt2->execute(array($row['comm'], 40));
                $count++;
                if ($count > 1000) {
                    break;
                }
                $r += $k;
            } else {
                $k = 1;
                while ($r + $k < $c) {
                    $row2 = $result[$r+$k];
                    if (($row2['ac'] != $row['ac']) or (substr($row2['comm'],0,10) != substr($row['comm'],0,10))) {
                        break;
                    }
                    $k++;
                }
                if ($k > 1) {
                    $match = $row['comm'];
                    for ($i = 1; $i < $k; $i++) {
                        $match2 = $result[$r+$i]['comm'];
                        $newmatch = '';
                        for ($j = 0; $j < strlen($match); $j++) {
                            if (substr($match,$j,1) == substr($match2,$j,1)) {
                                $newmatch .= substr($match,$j,1);
                            } else {
                                break;
                            }
                        }
                        $match = $newmatch;
                    }
                    $stmt2->execute(array($match, $row['ac']));
                    $count++;
                    if ($count > 1000) {
                        break;
                    }
                    $r += $k; 
                } else {
                    $stmt2->execute(array($row['comm'], $row['ac']));
                    $count++;
                    if ($count > 1000) {
                        break;
                    }
                    $r++;
                }
            }
        } 
    }
   
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
echo $count;
$db = null; 

?>
</body>