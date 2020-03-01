<!DOCTYPE html>
<html>
<head></head>
<body>

<?php
require_once('utilities.php');
$ac = intval($_GET['q']);
$n = intval($_GET['n']);
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    if ($n > 150) {
        $stmt = $db->prepare('Select MONTH(dt) as m, MONTHNAME(dt) as mn, YEAR(dt) as y, COUNT(id) AS ct, FORMAT(SUM(ac_amount),2) as am from journal where ac = ? group by YEAR(dt) DESC, MONTH(dt) DESC, MONTHNAME(dt)');
    } else {
        $stmt = $db->prepare('Select YEAR(dt) as y, COUNT(id) AS ct, FORMAT(SUM(ac_amount),2) as am from journal where ac = ? group by YEAR(dt) DESC');
    }
    $stmt->execute(array($ac));
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($n > 150) {
            $dt = substr($row['mn'],0,3) . ' ' . $row['y'];
            $m = $row['m'];
			
        } else {
            $dt = $row['y'];
            $m = 0;
        }
        echo "<div class='accountTotal' data-y='". $row['y'] . "' data-m='" . $m . "' data-n='" . $row['ct'] . "'>";
        echo "<div class='CdateCell noborder'><img class='floatLeft' src='right.png' alt='Expand' onclick='expandAccounts(this); return false'>" .$dt . "</div>";
        echo "<div class='CrefCell noborder'></div>";
        echo "<div class='CamountCell noborder'>R " .$row["am"] . "</div>";
        echo "<div class='CallocCell noborder'></div>";
        echo "<div class='CcommCell noborder'></div></div>";
    }
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 
?>
</body>