<?php 

require_once('config.php');

$ac = $_POST["ac"];
$y = $_POST["y"];
$m = $_POST["m"];
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    if ($m == 0) {
        $stmt = $db->prepare('SELECT dt, journal.id as jid, DATE_FORMAT(Journal.dt,"%d-%b-%Y") as fdate, ac_amount, FORMAT(ac_amount,2) AS acamount, shortname, comm, ref FROM Journal INNER JOIN funds ON Journal.fu = FUnds.id WHERE (ac = ? AND YEAR(dt) = ?) ORDER BY dt, ref');
        $stmt->execute(array($ac, $y));
    } else {
        $stmt = $db->prepare('SELECT dt, journal.id as jid, DATE_FORMAT(Journal.dt,"%d-%b-%Y") as fdate, ac_amount, FORMAT(ac_amount,2) AS acamount, shortname, comm, ref FROM Journal INNER JOIN funds ON Journal.fu = FUnds.id WHERE (ac = ? AND YEAR(dt) = ? AND MONTH(dt) = ?) ORDER BY dt, ref');
        $stmt->execute(array($ac, $y, $m));
    }
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	echo "<div class='row' data-id='" .$row['jid']. "'>";
		echo "<div class='CdateCell'>" .$row['fdate'] . "</div>";
		echo "<div class='CrefCell'>" .$row['ref']. "</div>";
		echo "<div class='CamountCell'>R " .$row["acamount"] . "</div>";
		echo "<div class='CallocCell'>" .$row['shortname']. "</div>";
		echo "<div class='CcommCell'><input type='text' value='" . $row['comm'] . "' class='comments' onchange='saveAcComm(this); return false'></div>";
		echo "<div class='CreallocateCell' ";
		echo "><div data-val='" . $ac. "' data-state='dead' onclick='openselect(event, this, " . '"PartAccountsSelect"' . ", doReallocate)'>reallocate</div></div></div>";
    	
    }
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 

?>