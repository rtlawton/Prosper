<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!--<script src="https://use.fontawesome.com/87faef6942.js"></script>-->
<script type="text/javascript" src="jquery-2.1.3.js"></script>
<script type="text/javascript" src="jquery.number.min.js"></script>
<script type="text/javascript" src="Jscript2.js"></script>
<script type="text/javascript" src="CustomSelect.js"></script>
<title></title>
<link rel="stylesheet" type="text/css" href="StyleSheet2.css"/>
<link href="fontawesome/css/all.min.css" rel="stylesheet"> 
</head>
<body onload="startup()" onclick="closeSelect()" onkeydown="selectKey(event)">
    <form>
        <?php
require_once('config.php');
//
try {
    $db = new PDO(PDO_CONNECT,USER, PSWD, $PDO_OPTIONS);
    echo "<div id='topline'>";
//
//Choose number of records to display
//----------------------
    //echo "<span class='radio'>";
    //echo "<input type='radio' name='limit' value='100' checked onchange='radioclick(); return false'>";
	//echo " Last 100   ";
	//echo "<input type='radio' name='limit' value='All' onchange='radioclick(); return false'>";
	//echo "  All </span>";
 
//
//Choose fund to display
//----------------------
    $db->exec('CALL Recount()');
    $stmt = $db->query('SELECT Funds.id, Funds.name, Funds.protocol, Funds.last_count, Funds.ac_no as identifier FROM Funds ORDER BY Funds.name');
   
    echo "<select id='fundlist' onchange='showFundMain(this)' onmousedown='dochange(this,1)' onfocus='switchfocus(1)' class='fontL'>
    			<option value = 'xxxx'>SELECT ACCOUNT</option>";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='" . $row['id'] . 
        "' data-protocol='" .$row['protocol']. 
        "' data-identifier='" . $row['identifier'] .
        "' data-ct='" . $row['last_count'] .
        "'>" . $row['name'] . "</option>";
    }
    echo "</select>";
//
//Quick fund select
//
    $stmt = $db->query('SELECT Funds.id, Funds.shortname, Funds.protocol, Funds.last_count, Funds.ac_no as identifier FROM Funds WHERE Funds.quick = 1 ORDER BY Funds.shortname');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row) {
        echo "<label class='Quick' data-protocol='" .$row['protocol']."' data-identifier='" . $row['identifier'] .
        "' data-ct='" . $row['last_count'] ."' data-id='". $row['id'] ."' onclick='showFund(this)'>". $row['shortname'] . "</label>";
    };
//
//Choose allocation to display
//----------------------------
    $stmt = $db->query('SELECT Accounts.id, Accounts.name, Accounts.isSpecial, Count(Journal.id) as CT FROM Accounts left join Journal on Accounts.id = Journal.ac WHERE Accounts.archived = 0 GROUP BY Accounts.name, Accounts.id');
    echo "<select id='accountlist' onchange='showAccount()' onfocus='switchfocus(2)' onmousedown='dochange(this,2)' class='fontL'>
    			<option value = 'xxxx'>VIEW ALLOCATIONS</option>";
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($rows as $row) {
        echo "<option value='" . $row['id'] . "' data-count='" .$row['CT'] . "'>" . $row['name'] . "</option>";
    }
    echo "</select>";
//
//Chose tax year to display
//-------------------------
	echo "<select id='taxlist' class='fontL taxW' onchange='showAccount()'><option value='xxxx' selected>TAX YR</option>";
	date_default_timezone_set(TIME_ZONE);
	for ($year = 2013; $year <= intval(date("Y")); ++$year) {
		echo "<option value = '" . $year . "'>Y/E Feb " . $year. "</option>";
	}
	echo "<option value='xxxx'>Normal view</option></select>";
	//echo "<span class='backup'>
    //		<button type='button' onclick='backup(); return false' class='fontM bup'>Back up</button>
    //	</span>
    //</div>";
    $searchaction = "'$(''#searchactionrow'').show()'";
//
//How many records to show?
//
    echo "<span id='range'>Show records: <span id='limit'>100</span></span><span id='range-min' class='minmax'>0</span><input class='howmany' type='range' val='100' min='0' max='200' onchange='slide(this.value)'>";
    echo "<span id='range-max' class='minmax'>200</span>";
//
//Drop down menu
//
    echo "<div class='dropdown'>
            <img src='menuW.png' class='dropbtn' onmousemove='showmenu()'>
            <div class='dropdown-content'>
                <a href='#' id='importbutton' class='menuitem' onclick='importstatements()'>Import statement</a>
                <a href='#' id='manualbutton' class='menuitem' onclick='doManual()'>Manual journal entry</a>
                <a href='#' id='backupbutton' class='menuitem' onclick='backup()'>Back up data</a>
                <a href='#' id='bankreport' class='menuitem' onclick='doBankReport()'>Bank fees report</a>
                <a href='#' id='searchbutton' class='menuitem' onclick='showsearchaction()'>Search for amount</a>
                <a href='#' id='mlbutton' class='menuitem' onclick='doml()'>Update ML <span id='spinner' class='hidden'><i class='fa fa-cog fa-spin fa-lg fa-fw'></i>
                <span class='sr-only'>Updating...</span></span></a>
                
            </div>
        </div></div>";

//
//Template for full allocation selection
//--------------------------------------
    echo "<div id='Templates' class='hidden'><div id='FullAccountsSelect'><div class='CSelect'><div class='upbar' onmouseover='doScroll(this)' onmouseout='stopScroll(this)'><img src='up2.png' class='floatcenter'></div><div class='selectContainer'>";
    foreach($rows as $row) {
        echo "<div class='Citem' data-val='" . $row['id'] . "' onmouseover='msOver(this)'>" . $row['name'] . "</div>";
    }
    echo "</div><div class='downbar' onmouseover='doScroll(this)' onmouseout='stopScroll(this)'><img src='down2.png' class='floatcenter'></div></div></div>";
//
//Template for partial allocation selection
//-----------------------------------------
    echo "<div id='PartAccountsSelect'><div class='CSelect'><div class='upbar' onmouseover='doScroll(this)' onmouseout='stopScroll(this)'><img src='up2.png' class='floatcenter'></div><div class='selectContainer'>";
    foreach($rows as $row) {
    	if (!$row['isSpecial']){
        	echo "<div class='Citem' data-val='" . $row['id'] . "' onmouseover='msOver(this)'>" . $row['name'] . "</div>";
        }
    }
    echo "</div><div class='downbar' onmouseover='doScroll(this)' onmouseout='stopScroll(event)'><img src='down2.png' class='floatcenter'></div></div></div>";
//
//Template for short fund selection
//---------------------------------
    $stmt = $db->query('SELECT Funds.id, Funds.shortname FROM Funds ORDER BY Funds.shortname');
    echo "<div id='ShortFundsSelection'><div class='CSelect'><div class='upbar' onmouseover='doScroll(this)' onmouseout='stopScroll(this)'><img src='up2.png' class='floatcenter'></div><div class='selectContainer'>";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['id'] != '7') {
            echo "<div class='Citem' data-val='" . $row['id'] . "' onmouseover='msOver(this)'>" . $row['shortname'] . "</div>";
        }
    }
    echo "</div><div class='downbar' onmouseover='doScroll(this)' onmouseout='stopScroll(this)'><img src='down2.png' class='floatcenter'></div></div></div></div>";
}
catch(PDOException $ex) {
    echo $ex->getMessage();
}
$db = null; 
        ?>
        
<div id="fundtableheader" class='hidden rowcontainer'>
    <div class='row'>
    	<div class='dateCell'>Date</div
    	><div class='refCell'>Ref</div
    	><div class='amountCell'>Amount</div
    	><div class='allocCellHeader'>Allocation</div
    	><div class='commCell'>Comments</div
    	><div class='balCell'>Balance</div
    	><div class='checkCell'>Check</div
    ></div>
</div>
<div id="cashtableheader" class='hidden rowcontainer'>
	<div class='row'>
    	<div class='cashDateCell'>Date</div
    	><div class='refCell'>Ref</div
    	><div class='cashAmountCell'>Amount</div
    	><div class='allocCellHeader'>Allocation</div
    	><div class='commCell'>Comments</div
	></div>
</div>
<div id="accounttableheader" class='hidden rowcontainer'>
	<div class='row'>		
    	<div class='CdateCell'>Date</div
    	><div class='CrefCell'>Ref</div
    	><div class='CamountCell'>Amount</div
    	><div class='CallocCell'>Account</div
    	><div class='CcommCell'>Comments</div
	></div>
</div>
        
<div id = 'manualactionrow' class='hidden actionrow'>
    <span id='manualdata'>
        Date: <input type='text' class='date'>
        Amount: <input type='text' class='currency'>
        <button id='savemanualbutton' type='button' class='fontM' onclick='saveManual(); return false'>Save</button>
        <button id='killmanualbutton' type='button' class='fontM' onclick='killManual(); return false'>Cancel</button>
    </span>
</div>
<div id = 'importactionrow' class='hidden actionrow'>
    <select id = 'statements' class='fontL' onchange='doimport(this); return false' onblur='killStatements(); return false'>       </select>
    <button type='button' class='fontM hidden' id='buttonsaveimport' onclick='saveimport(this); return false'>Save imported transactions</button>
    <button type='button' id='buttondiscardimport' class='fontM hidden' onclick='discardimport(); return false'>Discard imported transactions</button>
</div>
<div id = 'searchactionrow' class='hidden actionrow'><span class = 'r'>R</span>
    <input id = 'searchamount' class='fontL searchbox' type="number" onclick='setsearch()'>
    <button type='button' class='fontM' id='buttondosearch' onclick='dosearch(); return false'>Search</button>
</div>
<div id = 'importErrorRow' class='hidden errorrow'><span id='errormessage'></span>
    <button type='button' class='fontM' onclick='$("#importErrorRow").addClass("hidden")'>OK</button>
</div>
<div id='breport' class='hidden report'>
    <div class='reportbanner'><img src='delete.png' onclick='closebankreport()' style='float:right'></div>
    <iframe id="report" src="BKRP.pdf" height="600" width="800"></iframe>
</div>
<div id="showLines" class='rowcontainer' onscroll="closeSelect()">
        </div>

    </form>
</body>
</html>