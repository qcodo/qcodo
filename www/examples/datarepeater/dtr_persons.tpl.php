<div class="data_repeater_example">
	<b>Person #<?php _p($_ITEM->Id); ?></b><br/>
	First Name: <b><?php _p($_ITEM->FirstName); ?></b><br/>
	Last Name: <b><?php _p($_ITEM->LastName); ?></b>
</div>

<?php
	if ((($_CONTROL->CurrentItemIndex % 2) != 0) ||
		($_CONTROL->CurrentItemIndex == count($_CONTROL->DataSource) - 1))
		_p('<br style="clear:both;"/>', false);
?>