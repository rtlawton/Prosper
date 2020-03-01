<?php 
require_once('config.php');
ini_set("auto_detect_line_endings", true);
$fpath = $_POST["fpath"];
$protocol = $_POST["protocol"];
$identifier = $_POST["identifier"];
$fu = $_POST["fu"];
$monthNumber = array("Jan"=>1, "Feb"=>2, "Mar"=>3, "Apr"=>4, "May"=>5, "Jun"=>6, "Jul"=>7, "Aug"=>8, "Sep"=>9, "Oct"=>10, "Nov"=>11, "Dec"=>12);

try {
    $db = new PDO(PDO_CONNECT, USER, PSWD, $PDO_OPTIONS);
    $stmt = $db->prepare('SELECT FieldDelimiter, IDfield, IDLine, OpeningBalanceLine, OpeningBalanceField, ClosingBalanceLine, ClosingBalanceField, TopLines, BottomLines, FieldCount, DateField, DayStart, MonthStart, MonthLength, YearStart, YearLength, CommentaryField, TextDelimiter, CommentaryField2, AmountField FROM protocol WHERE name = ?');
    //$stmt = $db->prepare('SELECT * FROM protocol WHERE name = ?');
    $stmt->execute(array($protocol)); 
    $pro = $stmt->fetch(PDO::FETCH_ASSOC);
    $db->exec("DELETE FROM Statement;");
}
catch(PDOException $ex) {
    echo "!!! Error reading protocol: " . $protocol;
    return;
}

//die("fielddelimiter = " . $pro["FieldDelimiter"]);
$statement = file($fpath) or die("Unable to open file!");
//check account id
//----------------
if ($pro["IDLine"] != 0) {
    $record = explode($pro["FieldDelimiter"], rtrim($statement[intval($pro["IDLine"]) - 1]));
    if ($record[intval($pro["IDfield"]) - 1] != $identifier) {
        echo "!!! Account identification on statement does not match protocol identifier: " . $identifier;
        return;
    }
}
//get opening balance
//-------------------
if (intval($pro["OpeningBalanceLine"]) != 0) {
    $record = explode($pro["FieldDelimiter"], rtrim($statement[intval($pro["OpeningBalanceLine"]) - 1]));
    $openingbalance = floatval($record[intval($pro["OpeningBalanceField"]) - 1]);
} else {
    $openingbalance = 0;
}
//get closing balance
//-------------------
if (intval($pro["ClosingBalanceLine"]) != 0) {
    $record = explode($pro["FieldDelimiter"], rtrim($statement[count($statement) - intval($pro["ClosingBalanceLine"])]));
    $closingbalance = floatval($record[intval($pro["ClosingBalanceField"]) - 1]);
} else {
    $closingbalance = 0;
}
//process statements
//------------------

  for ($line=intval($pro["TopLines"]); $line < count($statement) - intval($pro["BottomLines"]); ++$line) {
    $record = explode($pro["FieldDelimiter"], rtrim($statement[$line]));
    if (count($record) != intval($pro["FieldCount"])) {
        echo "!!! Parsing error on line " . (1+$line) . " of " . $fpath;
        return;
    }
//date field
//--------------
    $DateString = trim($record[intval($pro["DateField"]) - 1],'"');
    if ($pro["TextDelimiter"] != "") {
        $DateString = substr($DateString,1,strlen($DateString) - 2);
    }
    $day = substr($DateString,intval($pro["DayStart"]) - 1,2);
    $month = substr($DateString,intval($pro["MonthStart"]) - 1,intval($pro["MonthLength"]));
    if (intval($pro["MonthLength"] == 3)) {
        $month = $monthNumber[$month];
    }
    $year = substr($DateString,intval($pro["YearStart"]) - 1,intval($pro["YearLength"]));
    $Field_dt = $year . '-' . $month . '-' .$day;
//commentary field
//----------------
    $CommString = trim($record[intval($pro["CommentaryField"]) - 1],'"');
    if ($pro["TextDelimiter"] != "") {
        $CommString = substr($CommString,1,strlen($CommString) - 2);
    }
    if (intval($pro["CommentaryField2"]) != 0) {
        $Comm2 = $record[intval($pro["CommentaryField2"]) - 1];
        if ($pro["TextDelimiter"] != "") {
            $Comm2 = substr($Comm2,1,strlen($comm2) - 2);
        }
        $Field_comm = rtrim($CommString) . ' ' . rtrim($Comm2);
    } else {
        $Field_comm = rtrim($CommString);
    }
    $Field_comm = substr($Field_comm,0,50);
//amount field
//------------
    $Field_am = trim($record[intval($pro["AmountField"]) - 1],'"');
    if ($pro["TextDelimiter"] != "") {
        $Field_am = substr($Field_am,1,strlen($Field_am) - 2);
    }
    
//check for duplicate
//-------------------
    $stmt = $db->prepare('SELECT count(id) FROM JOURNAL WHERE YEAR(dt) = ?  AND MONTH(dt) = ? AND DAY(dt) = ? AND fu_amount = ? AND fu = ?');
    $stmt->execute(array($year, $month, $day, $Field_am, $fu));
    if ($stmt->fetchColumn() != 0) {
        $Field_duplicate = 'Y';
    } else {
        $Field_duplicate = 'N';
    }
//balance field
//------------
    if ($line == intval($pro["TopLines"]) && $openingbalance != 0) {
        $Field_bal = $openingbalance + $Field_am;
    } elseif ($line == count($statement) - intval($pro["BottomLines"]) - 1 && $closingbalance != 0) {
        $Field_bal = $closingbalance;
    } else {
        $Field_bal = 0.0;
    }
//save record
//-----------
    try {
    $stmt = $db->prepare('INSERT INTO Statement (dt, am, comm, duplicate, bal) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute(array($Field_dt, $Field_am, $Field_comm, $Field_duplicate, $Field_bal)); 
    }
    catch(PDOException $ex) {
    echo $ex->getMessage();
    echo $Field_dt;
    }
}
//import into display
//-------------------

$stmt = $db->query('SELECT DATE_FORMAT(Statement.dt,"%d-%b-%Y") as fdate, FORMAT(am,2) AS fuamount, comm, duplicate, FORMAT(bal,2) AS fbal from Statement ORDER BY id DESC');
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
	$output = "<div class='imported' style='height:26px'>";
	$output .= "<div class='dateCell imported' style='height:21px; border-bottom:none'>" . $row['fdate'] . "</div><div class='refCell imported' style='height:21px; border-bottom:none'></div>";
	$output .= "<div class='amountCell imported' style='height:21px; border-bottom:none'>R <span class='currency'>" .$row['fuamount'] . "</span></div>";
    if ($row['duplicate'] == 'Y') {
    	$output .= "<div class='allocCell' style='background-color:#DFBFC0; height:26px'><div class='contra' style='border-top:none'><span class='dropdiv'>DUPLICATE?</span></div></div>";
    } else {
		$output .= "<div class='allocCell' style='background-color:#FFF555; height:26px'><div class='contra' style='border-top:none'><span class='dropdiv'>UNALLOCATED</span></div></div>";
    }
	$output .= "<div class='commCell' style='height:26px'><input type='text' class='comments imported' style='font-size:11px; border-bottom:none'value='". $row['comm'] ."' disabled></div>";
	$output .= "<div class = 'balCell' style='height:26px'></div>";
    if ($row["fbal"] != 0.0) {
    	$output .= "<div class = 'checkCell' style='height:26px'>R ".$row['fbal']."</div></div>";
    } else {
        $output .= "<div class = 'checkCell' style='height:26px'></div></div>";
    }
    echo $output;
}
$db = null;
return 0;
?>
