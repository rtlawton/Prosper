<?php

require_once('config.php');

function rowHTML($transaction, $bal) {
	$row = $transaction[0];
	$rowheight = count($transaction) * 27 - 5;
	if (count($transaction) > 1) {
		$rowType = 'M';
	} elseif (($row['fu'] == FU_CASH) && ($row['ac'] != AC_CASH)) {
        $rowType = 'E';
    } elseif ($row['fu'] == FU_CASH) {
    	$rowType = 'F';
    } elseif ($row['ac'] == AC_CASH) {
        $rowType = 'C';
    } elseif ($row['ac'] == AC_TRANSFER) {
        $rowType = 'T';
    } elseif ($row['ac'] == AC_DUPLICATE) {
        $rowType = 'D';   
    } else {
        $rowType = 'N';
    }
    //
    // DATE, REF and AMOUNT fields
    //----------------------------
   $output = "<div class='row' style='height:" . ($rowheight+4) . "px' data-date='" .$row['dt'] ."'>";
   if ($rowType == 'E') {
       $output .= "<div class='cashDateCell' style='height:" . $rowheight . "px'><input type='text' class='date' value='" . $row['fdate'] . "' onchange='savedate(this); return false'></div>";
	   $output .= "<div class='refCell' style='height:" . $rowheight . "px'>" . $row['ref'] . "</div>";
	   $output .= "<div class='cashAmountCell' style='height:" . $rowheight . "px'><span class= 'floatLeft' style='margin-left:4px'>R</span><input type='text' value='" .$row['fuamount'] . "' class='currency' onchange='saveamount(this); return false'></div>";
   } elseif ($rowType == 'F') {
       $output .= "<div class='cashDateCell' style='height:" . $rowheight . "px'>" . $row['fdate'] . "</div>";
	   $output .= "<div class='refCell' style='height:" . $rowheight . "px'>" . $row['ref'] . "</div>";
	   $output .= "<div class='cashAmountCell' style='height:" . $rowheight . "px'><span class= 'floatLeft' style='margin-left:4px'>R</span><span class='currency'>" .$row['fuamount'] . "</span></div>";
   } else {
       $output .= "<div class='dateCell' style='height:" . $rowheight . "px'>" . $row['fdate'] . "</div>";
	   $output .= "<div class='refCell' style='height:" . $rowheight . "px'>" . $row['ref'] . "</div>";
	   $output .= "<div class='amountCell' style='height:" . $rowheight . "px'>R <span class='currency'>" .$row['fuamount'] . "</span></div>";
   }
    //
    //ALLOCATION field
    //----------------
    
    $output .= "<div class='allocCell' style='background-color:" . $row['style'] . "; height:" . ($rowheight +4) ."px'>";
	$isFirstRow = true;
	foreach ($transaction as $contra) {
		if ($isFirstRow) {
			$output .= "<div class='contra' style='border-top:none' id=" . $contra["id"] . ">";
			if ($rowType == 'N' or $rowType == 'M') {
				$output .= "<img class='floatLeft down2' src='Plus.png' alt='Add row' onclick='addRow(this); return false'>";
			} elseif ($rowType == 'D' or $rowType == 'E') {
				$output .= "<img class='floatLeft' src='delete.png' alt='Remove row' onclick='delRow(this); return false'>";
			}
		} else {
			$output .= "<div class='contra' id=" . $contra["id"] . ">";
			$output .= "<img class='floatLeft' src='delete.png' alt='Remove row' onclick='delRow(this); return false'>";
		}
		if ($rowType == 'M') {
    		$output .= "<span class='fcurr'>R <input type='text' value='" .$contra['acamount'] . "' class='currency' onchange='reNormalize(this); return false'></span>";
			$output .= "<div data-val='" . $contra['ac']. "' data-state='dead' onclick='openselect(event, this, " . '"PartAccountsSelect"' . ", resetSelect)' class='dropdiv'>" . $contra['name'] . "</div>";
    	} elseif ($rowType == 'T') {
    		$output .= "<div data-val='" .$row['fu2']. "' data-state='dead' onclick='openselect(event, this, " . '"ShortFundsSelection"' . ", fresetSelect)' class='fdropdiv'>" .  $row['fname']  . "</div>";
			$output .= "<div data-val='" . $row['ac']. "' data-state='dead' onclick='openselect(event, this, " . '"FullAccountsSelect"' . ", resetSelect)' class='dropdiv gdropdiv'>" . $row['name'] . "</div>";
    	} elseif ($rowType == 'F') {
    		$output .= "<span class='dropdiv'>" .$row['name']. "</span>";
    	} elseif ($row['guess'] == 1) {
			$output .= "<div data-val='" . $row['ac']. "' data-state='dead' onclick='clearguess(this)' class='dropdiv guessed'>" . $row['name'] . "</div>";
    	} else {
			$output .= "<div data-val='" . $row['ac']. "' data-state='dead' onclick='openselect(event, this, " . '"FullAccountsSelect"' . ", resetSelect)' class='dropdiv'>" . $row['name'] . "</div>"; 
        }
		$isFirstRow = false;
        $output .= "</div>";   		
    } 	
    $output .= "</div>"; 	     	
    //
    //COMMENTS field
    //--------------
    $output .= "<div class='commCell' style='height:" . $rowheight . "px'><input type='text' value='" . $row['comm'] . "' class='comments' onchange='saveComm(this); return false'></div>";
    //
    //BALANCE fields
    //--------------
    if ($row['fu'] != FU_CASH){
        $output .= "<div class = 'balCell' style='height:" . $rowheight . "px'>R <span class='bal'>" . $bal . "</span></div>";
        if ($row['fbal'] != 0.0) {
            $output .= "<div class = 'checkCell' style='height:" . $rowheight . "px'>R <input type='text' class='currency' onchange='resetbal(this); return false;' value='" . $row['fbal'] . "'></div>";
        } else {
            $output .= "<div class = 'checkCell' style='height:" . $rowheight . "px'><input type='text' class='currency' onchange='resetbal(this); return false;' value=''></div>";
        }
    } 
    $output .= "</div>";
    return $output;
}
function noComma($N) {
    return str_replace(',','',$N);
}
function cleanstring($stin, $fu) {
    $st = strtoupper(trim($stin));
    if ($fu == 12) {
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
    }
    return $st;
}
function apply_model($model, $res) {
    $frags = preg_split('#|\ +|\*', $res['comm']);
    $c_line = 0;
    $day = float_val(substr($res['fdate'],0,2));
    while ($c_line < count($model)) {
        $model_row = $model[$c_line];
        if ($model_row["frag"] != "") {
            $var = (int)in_array($model_row["frag"], $frags);
        } elseif ($model_row["field"] == "day") {
            $var = $day;
        } else {
            $var = $res['am'];
        }
        $split_dir = ($var < $model_row['split']) ? "left" : "right";
        if ($model_row[$split_dir] == 0) {
            return (int)$model_row['value'];
        } else {
            $c_line = $model_row[$split_dir] - 1;
        }
    }
    return AC_UNALLOCATED;
}
?>