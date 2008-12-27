function __calendar(strFormId, strId) {
	popCal = window.open(qc.phpAssets + "/_core/calendar.php?strFormId=" + strFormId + "&strId=" + strId + "&intTimestamp=" + document.forms[strFormId].elements[strId + "_intTimestamp"].value, "popCal", "width=165,height=228,left=200,top=250");
	if (window.focus)
		popCal.focus();
};

function __resetCalendar(strFormId, strId) {
	document.forms[strFormId].elements[strId + "_intTimestamp"].value = "";
	document.forms[strFormId].elements[strId].value = "";
	if (document.forms[strFormId].elements[strId].onchange)
		document.forms[strFormId].elements[strId].onchange();
};