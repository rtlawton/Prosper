<!DOCTYPE html>
<html>
<head></head>
<body>

<?php
require_once('config.php');
$ac = $_GET['q'];
$year = intval($_GET['y']);
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
	$stmt = $db->prepare('select FORMAT(sum(ac_amount),2) as total from journal where ac = ? and dt >= ? and dt < ? group by ac');
	$stmt->execute(array($ac, ($year-1) . "-03-01", $year . "-03-01"));
	$total = $stmt->fetchColumn();
	echo "<div class='taxrow' data-year='". $year ."'><div class='taxDateHead'>Tax Y/E Feb ". $year ."</div><div id='taxAmount' class='taxAmountHead'>R " . $total. "</div>";
	echo "<button type='button' onclick='taxExport(this); return false' class='fontM floatright downabit'>Export selected transactions</button></div>";
	echo "<div class='row'>";	
    echo "<div class='CdateCell'>Date</div>";
    echo "<div class='CrefCell'>Ref</div>";
    echo "<div class='CamountCell'>Amount</div>";
    echo "<div class='CcommCell'>Comments</div></div>";
	$sql = 'select dt, DATE_FORMAT(Journal.dt,"%d-%b-%Y") as fdate, Journal.ref, FORMAT(ac_amount,2) AS acamount, comm, id, (dt >= ? and dt < ?) as included from journal where ac = ? and (Year(dt) = ? or (Year(dt) = ? and Month(dt) < 4)) order by dt';
    $stmt = $db->prepare($sql);
    $stmt->execute(array(($year-1) . "-03-01", $year . "-03-01",$ac, $year-1, $year));
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    	if ($row['included']) {
    		echo "<div class='taxdatarow' onclick='toggleTax(this); return false;' data-id='" .$row['id']. "'>";
    	}else {
    		echo "<div class='taxdatarow notincluded' onclick='toggleTax(this); return false;' data-id='" .$row['id']. "'>";
    	}
		echo "<div class='CdateCell'>" .$row['fdate'] . "</div>";
		echo "<div class='CrefCell'>" .$row['ref']. "</div>";
		echo "<div class='CamountCell'>R " .$row["acamount"] . "</div>";
		echo "<div class='CcommCell'>" .preg_replace("/\r|\n/","", $row['comm']). "</div>";
		echo "<div class='CreallocateCell' ";
		echo "><div data-val='" . $ac. "' data-state='dead' onclick='openselect(event, this, " . '"PartAccountsSelect"' . ", doReallocate)'>reallocate</div></div></div>";

    }
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 
?>
</body>