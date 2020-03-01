<?php 
require_once('utilities.php');
$id = $_POST["id"];
$ac = $_POST["ac"];
$bal = $_POST["bal"];
$ref = $_POST["ref"];
$fu = $_POST["fu"];
$comm = $_POST["comm"];

try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('UPDATE Journal SET ac = ? WHERE id = ?');
    $stmt->execute(array($ac, $id)); 
    $stmt = $db->prepare(SQL_GETROWSBYREF);
    $stmt->execute(array($ref));
    $transaction = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //if transaction has more than one row it is a multiple transaction - do not adjust ML table.
    if (count($transaction) == 1 and $comm != '') {
        //reset guessing data:
        //get guess table name:
        $stmt = $db->prepare("Select matchtable from funds where id = ?;");
        $stmt->execute(array($fu));
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $matchtable = $results[0]['matchtable'];
        $cleancomm = cleanstring($comm,$fu);
        if ($matchtable != '') {
            //get all the matchstrings that are substrings of $cleancomm
            $sql = 'select id, ac from ' . $matchtable . ' where "'. $cleancomm .'" like concat("%", matchstring, "%");';
            $stmt2 = $db->query($sql);
            $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            if (count($result2) == 0) {
                //create a new matchstring
                if ($matchtable == 'Bankmatch') {
                    //check for near matches from bankmatch
                    $sql = 'select id, matchstring from Bankmatch where ac = ? and substring(matchstring,1,10) = ?';
                    $stmt2 = $db->prepare($sql);
                    $stmt2->execute(array($ac, substr($cleancomm,0,10)));
                    $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                    $log = $db->prepare("Insert into DebugLog (txt) values (?)");
                    if (count($result2) > 0) {
                        //find the longest near match;
                        $mlength = 0;
                        for ($j = 0; $j < count($result2); $j++) {
                            $m = min(strlen($cleancomm),strlen($result2[$j]['matchstring']));
                            for ($i = 0; $i < $m; $i++) {
                                if (substr($result2[$j]['matchstring'],$i,1) != substr($cleancomm,$i,1)) {
                                    $m = $i;
                                    break;
                                }
                            }
                            if ($m > $mlength) {
                                $mlength = $m;
                                $id = $result2[$j]['id'];
                            }
                        }
                        // crop matchstring to accommodate new comm
                        $stmt = $db->prepare('Update Bankmatch set matchstring = ? where id = ?');
                        $stmt->execute(array(substr($cleancomm,0,$mlength),$id));
                    } else {
                        // add a new matchstring
                        $stmt = $db->prepare('Insert into Bankmatch (matchstring, ac) values (?,?)');
                        $stmt->execute(array($cleancomm, $ac));
                    }                 
                } else {
                    // add a new matchstring
                    $sql = 'Insert into '. $matchtable .' (matchstring, ac) values (?,?)';
                    $stmt = $db->prepare('Insert into '. $matchtable .' (matchstring, ac) values (?,?)');
                    $stmt->execute(array($cleancomm, $ac));
                    
                }
            } elseif (count($result2) > 1 or $result2[0]['ac'] != $ac) {
            //ambiguous allocation - multiple substrings or different allocation - set matchtable entries to unallocated
                $stmt = $db->prepare("Update " . $matchtable . " set ac = " . AC_UNALLOCATED . " where id = ?;");
                foreach ($result2 as $matchrow) {
                    $stmt->execute(array($matchrow['id']));
                }
            } 
        }
    }
    echo rowHTML($transaction,$bal);
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 
?>