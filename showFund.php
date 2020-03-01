<!DOCTYPE html>
<html>
<head></head>
<body>
    
<?php
require_once('utilities.php');
$fu = intval($_GET['q']);
$limit = $_GET['limit'];
$amount = $_GET['amount'];
try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    if ($fu != FU_CASH) {
        $stmt = $db->prepare('SELECT (funds.opening_bal + sum(journal.fu_amount)) AS GBal, Funds.id FROM Funds inner join Journal on Funds.id = Journal.fu WHERE Funds.id = ? GROUP BY Funds.id');
        $stmt->execute(array($fu));
        $bal = noComma($stmt->fetchColumn());
    }
    $sql = 'select Journal.id, 
    Journal.dt, 
    Journal.fu, 
    DATE_FORMAT(Journal.dt,"%d-%b-%Y") as fdate, 
    Journal.rec, 
    Journal.ref, 
    Journal.ac, 
    FORMAT(Journal.bal,2) as fbal, 
    Journal.fu2, 
    Funds.shortname as fname, 
    Accounts.name, 
    Accounts.style, 
    FORMAT(ac_amount,2) AS acamount, 
    FORMAT(fu_amount,2) AS fuamount, 
    Journal.comm,
    Journal.guess,
    CASE fu_amount WHEN 0.00 THEN 1 ELSE 0 END as subsid 
    from Journal inner join Accounts on Journal.ac = Accounts.id inner join Funds on Journal.fu2 = Funds.id 
    where (Journal.fu = ?)';
    if ($amount != 0) {
        $sql .= ' AND (fu_amount = ' . $amount . ' OR fu_amount = -' . $amount . ') ';
    }
    $sql .= 'order by Journal.dt DESC, Journal.ref DESC, subsid, journal.id';
    if ($limit != '0') {
        $sql .= " LIMIT " . $limit;
    }        
    $stmt = $db->prepare($sql);
    $stmt->execute(array($fu));
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($limit == "100" && count($result) == 100) {
        $index = count($result) - 1;
        while ($result[$index]['rec'] == "M") {
            array_pop($result);
            --$index;
        }
    }
	for ($i=0; $i<count($result); ++$i) {
		$transaction = array();
		$ref = $result[$i]['ref'];
		while ($i < count($result) && $result[$i]['ref'] == $ref ) {
			array_push($transaction, $result[$i]);
			++$i;
		}
		--$i;
        if ($fu != FU_CASH) {
            echo rowHTML($transaction, number_format((float)$bal,2,'.',','));
            $bal = $bal - noComma($transaction[0]["fuamount"]);
        } else {
            echo rowHTML($transaction, "");
        }
    };
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 
?>
</body>