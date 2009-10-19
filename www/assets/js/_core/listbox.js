function __resetListBox(strFormId, strControlId) {
	var objListBox = document.forms[strFormId].elements[strControlId];
	objListBox.selectedIndex = -1;
	if (objListBox.onchange)
		objListBox.onchange();
};