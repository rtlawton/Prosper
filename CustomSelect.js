/**
 * @author Richard Lawton
 */
/*jslint browser: true*/
/*global $, alert, confirm */
var scrollBorder = 9,
	windowHeight = window.innerHeight,
	selecting = false,
	itemHeight,
	hostingDiv,
	selDiv,
	selDivTop,
	selDivBottom,
	scrollingDiv,
	scrollingHeight,
	newSelected,
	oldSelected,
	keyAction;
function closeSelect() {
	"use strict";
	$("*[data-state='live']").each(function () {
		$(this).html($(this).attr("data-oldtxt"));
		$(this).attr("data-state", "dead");
	});
	selecting = false;
}
function findpos(element) {
	"use strict";
	var curtop = 0,
		curleft = 0;
	if (element.offsetParent) {
		do {
			curtop += element.offsetTop;
			curleft += element.offsetLeft;
		} while (element === element.offsetParent);
	}
	return [curleft, curtop];
}
function select(item, action) {
	"use strict";
	$(oldSelected).removeClass("SelectedItem");
	$(item).addClass("SelectedItem");
	var val = $(item).attr("data-val");
	setTimeout(function () {
		$(hostingDiv).attr("data-state", "dead");
		if ($(hostingDiv).attr("data-val") !== val) {
			action(hostingDiv, val);
		} else {
			$(hostingDiv).html($(item).text());
		}
	},300);
}
function checkScrollBars(){
	"use strict";
	if ($(scrollingDiv).scrollTop() > 0) {
		$(".upbar").css({ 'opacity' : '1.0'});
	} else {
		$(".upbar").css({ 'opacity' : '0.0'});
	}
	if ($(scrollingDiv).children().length * itemHeight > $(scrollingDiv).scrollTop() + $(scrollingDiv).height()) {
		$(".downbar").css({ 'opacity' : '1.0'});
	} else {
		$(".downbar").css({ 'opacity' : '0.0'});
	}
}
function openselect(event, hostDiv, Source, action) {
"use strict";
	var curpos,
		curtop,
		r,
		scrollGap,
		scroll;
	event.stopPropagation();
	if ($(hostDiv).attr("data-state") === "dead"){
		closeSelect();
		hostingDiv = hostDiv;
		curpos = findpos(hostDiv);
		curtop = curpos[1] - $("#showLines").scrollTop() - $(document).scrollTop();
		$(hostDiv).attr("data-oldtxt",$(hostDiv).text());
		$(hostDiv).html($("#"+Source).html());
		selDiv = $(hostDiv).find(".CSelect:first");
		oldSelected = $(selDiv).find(".Citem[data-val = '" + $(hostDiv).attr("data-val")+ "']");
		newSelected = oldSelected;
		if (Source === "ShortFundsSelection"){
			$(selDiv).find(".Citem[data-val = '" + $("#fundlist").val() + "']").remove();
		}
		$(hostDiv).attr("data-state","live");
		$(oldSelected).addClass("SelectedItem");
		$(selDiv).find(".Citem").click(function() {
			select(this,action);
		});
		itemHeight = $(oldSelected).outerHeight();
		r = $(oldSelected).index() * itemHeight;
		scrollGap = curtop - r - scrollBorder;
		if (scrollGap > 0) {
			selDivTop = scrollGap;
			scroll = 0;
		} else {
			scroll = -scrollGap;
			selDivTop = 0;
		}
		scrollingDiv =  $(oldSelected).parent()[0];
		scrollingHeight = scrollingDiv.scrollHeight;
		scrollGap = curtop + scrollingHeight - r + scrollBorder;
		if (scrollGap > windowHeight) {
			selDivBottom = windowHeight;
			if ((selDivBottom - selDivTop) < 6 * itemHeight){
				selDivTop = selDivBottom - 6*itemHeight;
			}
		} else {
			selDivBottom = scrollGap;
		}
		$(scrollingDiv).height(selDivBottom - selDivTop - 2*scrollBorder);
		$(scrollingDiv).scrollTop(scroll);
		$(selDiv).height(selDivBottom - selDivTop - 4);
		$(selDiv).css({left:curpos[0], top:selDivTop});
		selecting = true;
		keyAction = false;
		checkScrollBars();
	}
}
function doScroll(e){
	"use strict";
	var st,
		sm;
	if ($(e).hasClass("upbar")){
		st = $(scrollingDiv).scrollTop();
		if (st > 0){
			$(scrollingDiv).animate({scrollTop:"0px"},2*st, function(){
				checkScrollBars();
				$(newSelected).removeClass("newSelected");
				$(scrollingDiv).children().first().addClass("newSelected");
			});			
		} else {
			checkScrollBars();			
		}
	} else {
		st = $(scrollingDiv).scrollTop();
		sm = scrollingHeight - $(scrollingDiv).height();
		if (sm > st){
			$(scrollingDiv).animate({scrollTop:sm + "px"},2*(sm - st), function(){
				checkScrollBars();
				$(newSelected).removeClass("newSelected");
				$(scrollingDiv).children().last().addClass("newSelected");
			});			
		} else {
			checkScrollBars();
		}
	}
	return;
}
function stopScroll(e){
	"use strict";
	$(scrollingDiv).stop();
	checkScrollBars();
}
function selectKey(ev) {
	"use strict";
	var findChar,
		optList,
		ind,
		i,
		newNewSelected;
	if (!selecting){
		return false;
	}
	if (ev.keyCode ===13) {
		$(newSelected).click();
	} else if (ev.keyCode ===27) {
		closeSelect();
	} else if ((ev.keyCode > 64 && ev.keyCode < 91) || (ev.keyCode > 96 && ev.keyCode < 123)) {
		findChar = String.fromCharCode(ev.keyCode).toUpperCase();
		optList = $(scrollingDiv).find(".Citem").filter(function(ind,ele){
			return ($(ele).text().substr(0,1) === findChar);
		});
		if (optList.length > 0) {
			newNewSelected = optList[0];
			for (i=0; i < optList.length; ++i) {
				if ($(optList[i]).hasClass("newSelected")) {
					if (i < optList.length - 1) {
						newNewSelected = optList[i+1];
					}					
					break;	
				}
			}
			$(newSelected).removeClass("newSelected");
			newSelected = newNewSelected;
			keyAction = true;
			ind = $(scrollingDiv).children().index(newSelected);
			if ($(scrollingDiv).scrollTop() > ind * itemHeight) {
				$(scrollingDiv).scrollTop(ind * itemHeight);
			} else if ((ind + 1) * itemHeight > $(scrollingDiv).scrollTop() + $(scrollingDiv).height()) {
				$(scrollingDiv).scrollTop((ind + 1) * itemHeight - $(scrollingDiv).height());
			}
			$(newSelected).addClass("newSelected");
			checkScrollBars();
			setTimeout(function (){
				keyAction = false;
			},500);
		}
	}
	
}
function msOver(e) {
	"use strict";
	if (!keyAction){
		$(newSelected).removeClass("newSelected");
		$(e).addClass("newSelected");
		newSelected = e;
	}
}
