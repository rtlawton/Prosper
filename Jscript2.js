/*jslint browser: true*/
/*jslint plusplus: true */
/*global $, expandAccounts, alert, confirm */
/* eslint no-unused-vars: "off" */

var AC_UNALLOCATED = '40',
    AC_DUPLICATE = '42',
    AC_TRANSFER = '43',
    AC_CASH = '47',
    FU_NONE = '11',
    FU_CASH = '7',
    LIMIT = '100',
    MODE = 'Fundview',
    FU_SELECTED = '0',
    SEARCH_AMOUNT = 0;

function noComma(n) {
	//UPDATED
    "use strict";
    return n.replace(',', '');
}
function switchfocus(n) {
	//UPDATED
    "use strict";
    //$("#importbutton").removeClass("hidden");
    $("#importactionrow").addClass("hidden");
    $("#manualactionrow").addClass("hidden");
    //$("#statements").empty();
    //$("#buttonsaveimport").addClass("hidden");
    //$("#buttondiscardimport").addClass("hidden");
    //$('#newcashbutton').addClass("hidden");
    if (n === 2) {
        $("#fundlist").addClass('grey');
        $("#accountlist").removeClass('grey');
        //$(".radio").addClass('grey2');
        //$(":radio").prop("disabled", true);
        $("#taxlist").removeClass('grey');
        $("#taxlist").prop("disabled", false);
        $(".Quickselected").removeClass('Quickselected');
        MODE = 'Accountview';
    } else {
        $("#fundlist").removeClass('grey');
        $("#accountlist").addClass('grey');
        //$(".radio").removeClass('grey2');
        //$(":radio").prop("disabled", false);
        $("#taxlist").addClass('grey');
        $("#taxlist").prop("disabled", true);
        MODE = 'Fundview';
    }
}
function dochange(e, n) {
	//UPDATED
    "use strict";
    $(e).change();
    switchfocus(n);
}
function doManual() {
    "use strict";
    if (FU_SELECTED !== '0') {
        $("#manualactionrow").removeClass("hidden");
    }
}
function killManual() {
    "use strict";
    $("#manualactionrow").addClass("hidden");
}
function showFund(e) {
	//UPDATED
    "use strict";
    $("#importactionrow").addClass("hidden");
    $("#manualactionrow").addClass("hidden");
    if (MODE !== 'Fundview') {
    //if ($(":radio").prop("disabled") === true) {
        switchfocus();
    }
    $(".Quickselected").removeClass('Quickselected');
    $(e).addClass('Quickselected');
    $("#fundlist").val($(e).data('id'));
    //if ($(e).html() === 'Cash') {
        //$("#importbutton").addClass("hidden");
        //$("#newcashbutton").removeClass("hidden");
        //$("#manualbutton").addClass("hidden");
    //} else {
        //$("#importbutton").removeClass("hidden");
        //$("#newcashbutton").addClass("hidden");
        //$("#manualbutton").removeClass("hidden");
    //}
    //$("#statements").addClass("hidden");
    //$("#statements").empty();
    //$("#buttonsaveimport").addClass("hidden");
    //$("#buttondiscardimport").addClass("hidden");
    if ($(e).prop('selectedIndex') === 0) {
        return false;
    }
    //var //limit = 100, //$(":radio:checked").val(),
        //str = $(e).val();
        //str = $(e).data('id');
    FU_SELECTED = $(e).data('id').toString();
    if (FU_SELECTED === FU_CASH) {
		$("#fundtableheader").addClass('hidden');
		$("#cashtableheader").removeClass('hidden');
    } else {
		$("#fundtableheader").removeClass('hidden');
		$("#cashtableheader").addClass('hidden');
    }
    $("#accounttableheader").addClass('hidden');
    //alert("showFund.php?q=" + str + "&limit=" + limit);
    $("#range-max").html($(e).data("ct"));
    LIMIT = Math.min(100,$("#range-max").html());
    $("#range-min").html(Math.min(100,Math.ceil($("#range-max").html()/2)));
    $(".howmany").attr("min",$("#range-min").html());
    $(".howmany").attr("max",$("#range-max").html());
    $(".howmany").val($("#range-min").html());
    $(".howmany").val(LIMIT);
    $("#limit").html(LIMIT);
    $("#showLines").load("showFund.php?q=" + FU_SELECTED + "&limit=" + LIMIT + "&amount=" + SEARCH_AMOUNT);
    SEARCH_AMOUNT = 0;
}
function showFundMain(e) {
    "use strict";
    $("#importactionrow").addClass("hidden");
    $("#manualactionrow").addClass("hidden");
    if (MODE !== 'Fundview') {
    //if ($(":radio").prop("disabled") === true) {
        switchfocus();
    }
    $(".Quickselected").removeClass('Quickselected');
    //$("#statements").addClass("hidden");
    //$("#statements").empty();
    //$("#buttonsaveimport").addClass("hidden");
//    $("#buttondiscardimport").addClass("hidden");
    if (e.selectedIndex === 0) {
        return false;
    }
    //var //limit = 100,//$(":radio:checked").val(),
        //str = $(e).val();
    FU_SELECTED = $(e).val().toString();
    if (FU_SELECTED === FU_CASH) {
		$("#fundtableheader").addClass('hidden');
		$("#cashtableheader").removeClass('hidden');
    } else {
		$("#fundtableheader").removeClass('hidden');
		$("#cashtableheader").addClass('hidden');
    }
    $("#accounttableheader").addClass('hidden');
    //alert("showFund.php?q=" + str + "&limit=" + limit);
    $("#range-max").html($(e).find(":selected").data("ct"));
    LIMIT = Math.min(100,$("#range-max").html());
    $("#range-min").html(Math.min(100,Math.ceil($("#range-max").html()/2)));
    $(".howmany").attr("min",$("#range-min").html());
    $(".howmany").attr("max",$("#range-max").html());
    $(".howmany").val($("#range-min").html());
    $(".howmany").val(LIMIT);
    $("#limit").html(LIMIT);
    $("#showLines").load("showFund.php?q=" + FU_SELECTED + "&limit=" + LIMIT + "&amount=" + SEARCH_AMOUNT);
}
function radioclick(n) {
	//UPDATED
    "use strict";
    var e = $(".Quickselected:first");
    LIMIT = n;
    showFund(e);
    //$("#fundlist").change();
}
function showmenu() {
    "use strict";
    $(".dropdown-content").show();
}
function resetSelect(e, newAc) {
	//UPDATED
    "use strict";
    var oldAc = $(e).attr('data-val'),
        row = $(e).parent().parent().parent(),
        ref = $(row).find(".refCell:first").text(),
        id = $(e).parent().attr("id"),
        bal = 'blank',
        comm = $(row).find(".commCell:first").find("input:first").val(),
        phpCall;
    if ($("#fundlist").val() !== FU_CASH) {
        bal = $(row).find(".bal:first").text();
    }
    if (oldAc !== AC_TRANSFER && oldAc !== AC_CASH) {
        if (newAc !== AC_TRANSFER && newAc !== AC_CASH) {
            phpCall = "update.php";
        } else {
            phpCall = "newTransfer.php";
        }
    } else if (newAc !== AC_TRANSFER && newAc !== AC_CASH) {
        phpCall = "noTransfer.php";
    } else {
        phpCall = "swapTransfer.php";
    }
    $.post(phpCall, "id=" + id + "&ac=" + newAc + "&ref=" + ref + "&bal=" + bal + "&fu=" + $("#fundlist").val() + "&comm=" + comm, function (newRow) {
        $(row).replaceWith(newRow);
    });
}
function fresetSelect(e, newFu) {
	//UPDATED
    "use strict";
    var row = $(e).parent().parent().parent(),
        ref = $(row).find(".refCell:first").text(),
        id = $(e).parent().attr("id"),
        bal = 'blank',
        am = $(row).find(".currency:first").text(),
        dt = $(row).attr("data-date");
    if ($("#fundlist").val() !== FU_CASH) {
        bal = $(row).find(".bal:first").text();
    }
    $.post("fundswap.php", "id=" + id + "&fu2=" + newFu + "&ref=" + ref + "&bal=" + bal + "&dt=" + dt + "&am=" + am + "&fu=" + $("#fundlist").val(), function (newRow) {
        $(row).replaceWith(newRow);
        
    });
}
function showAccount() {
    "use strict";
    var e = $("#accountlist");
    $("#importactionrow").addClass("hidden");
    $("#manualactionrow").addClass("hidden");
    if ($(e).prop('selectedIndex') === 0) {
        return false;
    }
    //$("#importbutton").removeClass("hidden");
    //$("#statements").addClass("hidden");
    //$("#statements").empty();
    //$("#buttonsaveimport").addClass("hidden");
    //$("#buttondiscardimport").addClass("hidden");
    $("#fundtableheader").addClass('hidden');
    $("#cashtableheader").addClass('hidden');
    if ($("#taxlist").val() === "xxxx") {
		$("#taxlist option")[0].selected = 'selected';
		$("#accounttableheader").removeClass('hidden');
		$("#showLines").load("showAccountTotals.php?q=" + $(e).val() + "&n=" + $(e).find('option:selected').attr('data-count'));
    } else {
		$("#accounttableheader").addClass('hidden');
		$("#showLines").load("showTax.php?q=" + $(e).val() + "&y=" + $("#taxlist").val());
    }
}
function escape(e) {
    "use strict";
	//UPDATED
    $(e).parent().html($(e).parent().attr('data-oldName'));
}
function reNormalize(e) {
	//UPDATED
    "use strict";
    var am = noComma($(e).val()),
        row = $(e).parent().parent().parent().parent(),
        id = $(e).parent().parent().attr("id"),
        ref = $(row).find(".refCell:first").text(),
        bal = 'blank';
    if ($("#fundlist").val() !== FU_CASH) {
        bal = $(row).find(".bal:first").text();
    }
    $.post("reallocate.php", "id=" + id + "&acamount=" + am + "&ref=" + ref + "&bal=" + bal, function (newRow) {
        $(row).replaceWith(newRow);
    });
    return false;
}
function saveComm(e) {
	//UPDATED
    "use strict";
    var row = $(e).parent().parent(),
        ref = $(row).find(".refCell:first").text(),
        bal = 'blank',
        id,
        alloc;
    if ($("#fundlist").val() !== FU_CASH) {
        bal = $(row).find(".bal:first").text();
    }
    id = $(row).find(".contra:first").attr('id');
    alloc = $(row).find(".dropdiv:first").data('val');
    $.post("updateComm.php", "ref=" + ref + "&comm=" + $(e).val().substring(0, 100) + "&bal=" + bal + "&id=" + id + "&alloc=" + alloc, function (newRow) {
        $(row).replaceWith(newRow);
    });
    return false;
}
function saveAcComm(e) {
	//UPDATED
    "use strict";
    var row = $(e).parent().parent(),
        ref = $(row).find(".CrefCell:first").text();
    $.post("updateAcComm.php", "ref=" + ref + "&comm=" + $(e).val().substring(0, 100) + "&id=" + $(row).attr("data-id"), function (newRow) {
        $(row).replaceWith(newRow);
    });
    return false;
}

function addRow(e) {
	//UPDATED
    "use strict";
    var row = $(e).parent().parent().parent(),
        ref = $(row).find(".refCell:first").text(),
        id = $(e).parent().attr("id"),
        bal = 'blank';
    if ($("#fundlist").val() !== FU_CASH) {
        bal = $(row).find(".bal:first").text();
    }
    $.post("newRow.php", "id=" + id + "&ref=" + ref + "&bal=" + bal, function (newRow) {
        $(row).replaceWith(newRow);
    });
}
function delSRow(row, id) {
	//UPDATED
    "use strict";
    var bal, prevrow, am, i, priorRows;
    if (confirm("This transaction will be permanently deleted. Do you want to continue?")) {
		if ($("#fundlist").val() === FU_CASH) {
			am = noComma($(row).find(".currency:first").val());
		} else {
			am = noComma($(row).find(".currency:first").text());
		}
        $.post("delSRow.php", "id=" + id, function (deletedRow) {
			if ($("#fundlist").val() !== FU_CASH) {
				priorRows = $(row).prevAll();
				for (i = 0; i < priorRows.length; i++) {
					bal = noComma($(priorRows[i]).find(".bal:first").text());
					$(priorRows[i]).find(".bal:first").text($.number(bal - am, 2, '.', ','));
				}
			}
            $(row).replaceWith(deletedRow);
        });
    }
}
function delRow(e) {
	//UPDATED
    "use strict";
    var row = $(e).parent().parent().parent(),
        ref = $(row).find(".refCell:first").text(),
        id = $(e).parent().attr("id"),
        bal = 'blank';
    if ($(e).parent().siblings().length === 0) {
        delSRow(row, id);
        return;
    }
    if ($("#fundlist").val() !== FU_CASH) {
        bal = $(row).find(".bal:first").text();
    }
    $.post("delRow.php", "id=" + id + "&ref=" + ref + "&bal=" + bal, function (newRow) {
        $(row).replaceWith(newRow);
    });
}
function collapse(e) {
    "use strict";
    var nextrow = $(e).parent().parent().next(),
        thisrow;
    while ($(nextrow).hasClass('row')) {
        thisrow = nextrow;
        nextrow = $(nextrow).next();
        $(thisrow).remove();
    }
    $(e).attr('src', 'right.png');
}
function expandAccounts(e) {
    "use strict";
    if ($(e).attr('src') === 'down.png') {
        collapse(e);
    } else {
        var y = $(e).parent().parent().attr("data-y"),
            m = $(e).parent().parent().attr("data-m"),
            ac = $("#accountlist").val(),
			newHeight = 23 * $(e).parent().parent().attr("data-n"),
            missing;
        $.post("expandRow.php", "ac=" + ac + "&y=" + y + "&m=" + m, function (newRows) {
            $(e).parent().parent().after(newRows);
            $(e).attr('src', 'down.png');
            missing = newHeight + $(e).position().top - $("#showLines").height();
            if (missing > 0) {
				$("#showLines").stop().animate({scrollTop: ($("#showLines").scrollTop() + missing)}, 800);
            }
        });
    }
}
function backup() {
	//UPDATED
    "use strict";
    $.post("backup.php", "", function (val) {
        if (val === '0') {
            alert("back up succeeded");
        } else {
            alert("failed: " + val);
        }
    });
}
function parsedate(raw) {
    "use strict";
    var mnames = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'],
        bits,
        sep,
        year,
        month,
        day,
        i,
        n0,
        n1;
    if (raw.indexOf("/") !== -1) {
        sep = "/";
    } else if (raw.indexOf("-") !== -1) {
        sep = "-";
    } else {
        alert('Must use "-" or "/" as separator');
        return '';
    }
    bits = raw.split(sep);
    year = 0;
    for (i = 0; i < 3; i += 1) {
        if (bits[i].length === 4) {
            if (isNaN(bits[i])) {
                alert("Invalid year");
                return '';
            }
            year = Number(bits[i]);
            if ((year < 2010) || (year > 2050)) {
                alert("Invalid year");
                return '';
            }
            bits.splice(i, 1);
            i = 3;
        }
    }
    if (year === 0) {
        alert("Invalid year");
        return '';
    }
    n0 = Number(bits[0]);
    n1 = Number(bits[1]);
    if (isNaN(n0)) {
        month = mnames.indexOf(bits[0].toLowerCase()) + 1;
        if (month === 0) {
            alert('Invalid month');
            return '';
        }
        day = n1;
        if (isNaN(day) || day < 1 || day > 31) {
            alert('Invalid day');
            return '';
        }
    } else if (isNaN(n1)) {
        month = mnames.indexOf(bits[1].toLowerCase()) + 1;
        if (month === 0) {
            alert('Invalid month');
            return '';
        }
        day = n0;
        if (day < 1 || day > 31) {
            alert('Invalid day');
            return '';
        }
    } else {
        if (n0 > 12 && n0 < 32) {
            if (n1 > 0 && n1 < 13) {
                day = n0;
                month = n1;
            } else {
                alert("Invalid date");
                return '';
            }
        } else if (n0 > 0 && n0 < 13) {
            if (n1 > 12 && n1 < 32) {
                day = n1;
                month = n0;
            } else if (n1 > 0 && n1 < 13) {
                day = n0;
                month = n1;
            } else {
                alert("Invalid date");
                return '';
            }
        } else {
            alert("Invalid date");
            return '';
        }
    }
    return year + '-' + month + '-' + day;
}
function savedate(e) {
	//UPDATED
    "use strict";
    var raw = $(e).val(),
        row = $(e).parent().parent(),
        id = $(row).find(".contra:first").attr("id"),
        mnames = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'],
        bits,
        sep,
        year,
        month,
        day,
        i,
        n0,
        n1;
    if (raw.indexOf("/") !== -1) {
        sep = "/";
    } else if (raw.indexOf("-") !== -1) {
        sep = "-";
    } else {
        alert('Must use "-" or "/" as separator');
        return false;
    }
    bits = raw.split(sep);
    year = 0;
    for (i = 0; i < 3; i += 1) {
        if (bits[i].length === 4) {
            if (isNaN(bits[i])) {
                alert("Invalid year");
                return false;
            }
            year = Number(bits[i]);
            if ((year < 2010) || (year > 2050)) {
                alert("Invalid year");
                return false;
            }
            bits.splice(i, 1);
            i = 3;
        }
    }
    if (year === 0) {
        alert("Invalid year");
        return false;
    }
    n0 = Number(bits[0]);
    n1 = Number(bits[1]);
    if (isNaN(n0)) {
        month = mnames.indexOf(bits[0].toLowerCase()) + 1;
        if (month === 0) {
            alert('Invalid month');
            return false;
        }
        day = n1;
        if (isNaN(day) || day < 1 || day > 31) {
            alert('Invalid day');
            return false;
        }
    } else if (isNaN(n1)) {
        month = mnames.indexOf(bits[1].toLowerCase()) + 1;
        if (month === 0) {
            alert('Invalid month');
            return false;
        }
        day = n0;
        if (day < 1 || day > 31) {
            alert('Invalid day');
            return false;
        }
    } else {
        if (n0 > 12 && n0 < 32) {
            if (n1 > 0 && n1 < 13) {
                day = n0;
                month = n1;
            } else {
                alert("Invalid date");
                return false;
            }
        } else if (n0 > 0 && n0 < 13) {
            if (n1 > 12 && n1 < 32) {
                day = n1;
                month = n0;
            } else if (n1 > 0 && n1 < 13) {
                day = n0;
                month = n1;
            } else {
                alert("Invalid date");
                return false;
            }
        } else {
            alert("Invalid date");
            return false;
        }
    }
    $.post("saveDate.php", "id=" + id + "&y=" + year + "&m=" + month + "&d=" + day, function (newRow) {
        $(row).replaceWith(newRow);
    });
    return false;
}
function saveamount(e) {
	//UPDATED
    "use strict";
    var raw = $(e).val(),
        row = $(e).parent().parent(),
        id = $(row).find(".contra:first").attr("id"),
        amount;
    if (isNaN(raw)) {
        alert("Invalid number");
        return false;
    }
    amount = Number(raw);
    if (amount > 0) {
        if (!confirm("A Positive amount is cash RECEIVED. Proceed?")) {
            return false;
        }
    }
    $.post("saveAmount.php", "id=" + id + "&am=" + amount, function (newRow) {
        $(row).replaceWith(newRow);
    });
}
function newcash(e) {
	//UPDATED
    "use strict";
    $.post("newCash.php", "", function (newRow) {
        $("#showLines").prepend(newRow);
    });
}
function importstatements(g) {
	//UPDATED
    "use strict";
    if (FU_SELECTED !== '0' && FU_SELECTED !== FU_CASH) {
        $("#importactionrow").removeClass("hidden");
        $("#statements").empty();
        $.post("importlist.php", "fu=" + FU_SELECTED, function (options) {
        //$("#statements").removeClass("hidden");
            $("#statements").html(options);
            $("#statements").focus();
        });
    }
    //var e = $(".Quickselected:first"),
}
function killStatements() {
	"use strict";
	//$("#statements").empty();
	$("#simportactionrow").addClass("hidden");
}
function doimport(e) {
	//UPDATED
    "use strict";
    var g = $(".Quickselected:first"),
        protocol = $(g).data('protocol'),
        identifier = $(g).data('identifier'),
        fu = $(g).data('id');
    //alert("Importing2 fpath=" + $(e).val() + "&protocol=" + protocol + "&identifier=" + identifier + "&fu=" + fu);
    $.post("statement.php", "fpath=" + $(e).val() + "&protocol=" + protocol + "&identifier=" + identifier + "&fu=" + fu, function (newRows) {
        //$("#importbutton").addClass("hidden");
        //$("#statements").addClass("hidden");
        if (newRows.substr(0,3)=== '!!!') { 
            $("#importactionrow").addClass("hidden");
            $("#errormessage").html(newRows);
            $("#importErrorRow").removeClass("hidden");
        } else {
            $("#buttonsaveimport").removeClass("hidden");
            $("#buttondiscardimport").removeClass("hidden");
            //$("#importactionrow").addClass("hidden");
            $("#showLines").prepend(newRows);
        }
    });
}
function saveimport(e) {
	//UPDATED
    "use strict";
    var str = $(".Quickselected:first").data('id');
    $.post("saveStatement.php", "fu=" + str, function (garbage) {
        $(e).after(garbage);
        //$("#importbutton").removeClass("hidden");
        //$("#statements").addClass("hidden");
        //$("#buttonsaveimport").addClass("hidden");
        $("#importactionrow").addClass("hidden");
        showFund($(".Quickselected:first"));
    });
     
}
function discardimport() {
	//UPDATED
    "use strict";
    //$("#importbutton").removeClass("hidden");
    //$("#statements").addClass("hidden");
    //$("#buttonsaveimport").addClass("hidden");
    $("#importactionrow").addClass("hidden");
    showFund($(".Quickselected:first"));
}
function resetbal(e) {
	//UPDATED
    "use strict";
    var raw = noComma($(e).val()),
        row = $(e).parent().parent(),
        id = $(row).find(".contra:first").attr("id"),
        bal = $(row).find(".bal:first").text();
    if (raw === "") {
        raw = "0.0";
    }
    if (isNaN(raw)) {
        alert("Invalid number");
        return false;
    }
    $.post("saveBalance.php", "id=" + id + "&am=" + raw + "&bal=" + bal, function (newRow) {
        $(row).replaceWith(newRow);
    });
}
function toggleTax(e) {
	"use strict";
	var currTot = parseFloat(noComma($("#taxAmount").text().substr(2)), 10),
		thisAm = parseFloat(noComma($($(e).children()[2]).text().substr(2)), 10);
	if ($(e).hasClass("notincluded")) {
		currTot += thisAm;
		$(e).removeClass("notincluded");
	} else {
		currTot -= thisAm;
		$(e).addClass("notincluded");
	}
	$("#taxAmount").text("R " + $.number(currTot, 2, '.', ','));
}
function taxExport(e) {
	"use strict";
	var taxFile = "",
		title = $("#accountlist").find('option:selected').text().replace(/ /g, "_"),
		tot = noComma($(e).parent().find("#taxAmount:first").text().substr(2));
	title = title.replace(/:|,|\.|&|-/g, "");
	$("#showLines .taxdatarow").each(function (i, g) {
		if (!$(g).hasClass("notincluded")) {
			taxFile += $(g).find(".CdateCell:first").text() + ', ';
			taxFile += $(g).find(".CrefCell:first").text() + ', ';
			taxFile += noComma($(g).find(".CamountCell:first").text().substr(2)) + ', ';
			taxFile += encodeURIComponent($(g).find(".CcommCell:first").text());
			taxFile += '\n';
		}
	});
    $.post("taxExport.php", "yr=" + $(e).parent().attr("data-year") + "&ac=" + title + "&ff=" + taxFile + "&tot=" + tot, function (txt) {
        alert(txt);
	
	});
}
function startup() {
	"use strict";
	$("#taxlist").val("xxxx");
}
function doReallocate(e, newAc) {
	"use strict";
	var currTot, thisAm, row, totalRow, amCell;
	row = $(e).parent().parent();
	$.post("changeAllocation.php", "id=" + $(row).attr("data-id") + "&ac=" + newAc, function () {
		if ($("#taxlist").val() === "xxxx") {
			totalRow = row;
			while (!$(totalRow).hasClass("accountTotal")) {
				totalRow = $(totalRow).prev();
            }
			amCell = $(totalRow).find(".CamountCell:first");
			currTot = parseFloat(noComma($(amCell).text().substr(2)), 10);
			thisAm = parseFloat(noComma($($(row).children()[2]).text().substr(2)), 10);
			currTot -= thisAm;
			$(amCell).text("R " + $.number(currTot, 2, '.', ','));
        } else {
            if (!$(row).hasClass("notincluded")) {
				currTot = parseFloat(noComma($("#taxAmount").text().substr(2)), 10);
				thisAm = parseFloat(noComma($($(row).children()[2]).text().substr(2)), 10);
				currTot -= thisAm;
				$("#taxAmount").text("R " + $.number(currTot, 2, '.', ','));
            }
        }
        $(row).remove();
    });
	return false;
}
function saveManual() {
    "use strict";
    var dt,
        am,
        amount,
        aam,
        fu;
    dt = parsedate($("#manualdata").find(".date").val());
    if (dt === '') {
        return false;
    }
    am = $("#manualdata").find(".currency").val();
    if (isNaN(am)) {
        alert("Invalid amount");
        return false;
    }
    amount = Number(am);
    aam = -amount;
    fu = $("#fundlist").val();
    $.post("manual.php", "aam=" + aam + "&am=" + amount + "&dt=" + dt + "&fu=" + fu, function (newRow) {
        $("#showLines").prepend(newRow);
    });
    killManual();
}
function doBankReport() {
    "use strict";
    $.get("bankReport.php", function () {
        $("#breport").removeClass("hidden");
        $("#report")[0].contentWindow.location.reload(true);
    });
}
function closebankreport() {
    "use strict";
    $("#breport").addClass("hidden");
}
function clearguess(e) {
    "use strict";
    var ref = $(e).parent().parent().prev().prev().html();
    $(e).removeClass('guessed');
    $(e).click(function (event) {
        openselect(event, e, "FullAccountsSelect", resetSelect);
    });
    
    $.post("clearget.php", "ref=" + ref);
}
function showsearchaction() {
    "use strict";
    $("#searchactionrow").removeClass("hidden");
    $('#searchamount').val('');
    $('#searchamount').focus();
}
function dosearch() {
    "use strict";
    var e = $(".Quickselected:first");
    if ($("#buttondosearch").html() === "Search") {
        LIMIT = 0;
        SEARCH_AMOUNT = $('#searchamount').val();
        $("#buttondosearch").html("Clear");
    } else {
        LIMIT = 100;
        SEARCH_AMOUNT = 0;
        $("#searchactionrow").hide();
    }
    showFund(e);
}
function setsearch() {
    "use strict";
    $("#buttondosearch").html("Search");
}
function slide(v) {
    "use strict";
    $('#limit').html(v);
    LIMIT = v;
    $("#showLines").load("showFund.php?q=" + FU_SELECTED + "&limit=" + LIMIT + "&amount=" + SEARCH_AMOUNT);
}
function doml() {
    "use strict";
    var pr = $('#fundlist option:selected').attr('data-protocol');
    if (pr == '') {
        alert("There is no machine learning for this fund");
        return;
    }
    $("#spinner").removeClass("hidden");
    $.post("updateML.php", "pr=" + pr, function(response) {
        alert(response);
        $("#spinner").addClass("hidden"); 
    })
}