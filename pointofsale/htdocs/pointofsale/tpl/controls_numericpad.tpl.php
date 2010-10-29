<?php
/* Copyright (C) 2010 Denis Martin  <denimartin@hotmail.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *   	\file       htdocs/pointofsale/tpl/controls_numericpad.tpl.php
 *		\ingroup    pointofsale
 *		\brief      Template page for controls zone in the cashdesk page with fat buttons (for touchscreen)
 *		\version    $Id: controls_numericpad.tpl.php,v 1.1 2010/10/29 16:40:51 hregis Exp $
 *		\author		Denis Martin
 */


?>
<script type="text/javascript">

function productSelected() {
	//alert('product selected') ;
}

function updateField(value) {
	window.document.form_cashdesk.numField.value += value ;
}

function emptyField() {
	window.document.form_cashdesk.numField.value = "" ;
}

</script>

<table width="100%" height="100%" >
	<tr height="25%">
		<td align="center"><input type="button" class="cashdeskinput" value="1" onclick="updateField(this.value);"/></td>
		<td align="center"><input type="button" class="cashdeskinput" value="2" onclick="updateField(this.value);"/></td>
		<td align="center"><input type="button" class="cashdeskinput" value="3" onclick="updateField(this.value);"/></td>
		
		<td width="33%" align="center" colspan="2"><input type="text" id="numField" class="cashdesktext" name="numField" value="<?php if($this->statut == BASKET_VALIDATED) print price2num($this->total_ttc-$this->amount_paid, 'MT') ; ?>"/></td>
		
		<td width="33%" align="center">
			<input type="submit" class="cashdeskinput" name="action" value="<?php echo $langs->trans("PaymentCard"); ?>"<?php if($this->statut == 0 || $this->statut == 2) echo ' disabled="disabled"'; ?>/>
		</td>
	</tr>
	
	<tr height="25%" align="center">
		<td align="center"><input type="button" class="cashdeskinput" value="4" onclick="updateField(this.value);"/></td>
		<td align="center"><input type="button" class="cashdeskinput" value="5" onclick="updateField(this.value);"/></td>
		<td align="center"><input type="button" class="cashdeskinput" value="6" onclick="updateField(this.value);"/></td>
		
		<td width="23%" align="center">
			<input type="submit" class="cashdeskinput" name="action" value="<?php echo $langs->trans("AddLine"); ?>"<?php if($this->statut != 0) echo ' disabled="disabled"'; ?>/>
		</td>
		
		<td width="10%" align="center">
			<input type="submit" class="cashdeskinput" name="action" value="<?php echo $langs->trans("Offered"); ?>"<?php if($this->statut != 0) echo ' disabled="disabled"'; ?>/>
		</td>
		
		<td width="33%" align="center">
			<input type="submit" class="cashdeskinput" name="action" value="<?php print $langs->trans("PaymentCheck"); ?>"<?php if($this->statut == 0 || $this->statut == 2) echo ' disabled="disabled"'; ?>/>
		</td>
	</tr>
	<tr height="25%" align="center">
		<td align="center"><input type="button" class="cashdeskinput" value="7" onclick="updateField(this.value);"/></td>
		<td align="center"><input type="button" class="cashdeskinput" value="8" onclick="updateField(this.value);"/></td>
		<td align="center"><input type="button" class="cashdeskinput" value="9" onclick="updateField(this.value);"/></td>
		
		<td width="33%" align="center" colspan="2"><input type="submit" class="cashdeskinput" name="action" value="<?php print $langs->trans(($this->statut != 0)?"DeletePayment":"DeleteLine") ;?>" /></td>
		
		<td width="33%">
			<input type="submit" class="cashdeskinput" name="action" value="<?php echo $langs->trans("PaymentCash"); ?>"<?php if($this->statut == 0 || $this->statut == 2) echo ' disabled="disabled"'; ?>/>
		</td>
	</tr>
	<tr height="25%" align="center">
		<td align="center"><input type="button" class="cashdeskinput" value="." onclick="updateField(this.value);"/></td>
		<td align="center"><input type="button" class="cashdeskinput" value="0" onclick="updateField(this.value);"/></td>
		<td align="center"><input type="button" class="cashdeskinput" value="C" onclick="emptyField();"/></td>
		
		<td width="33%" colspan="2"><input type="submit" class="cashdeskinput" name="action" value="<?php print $langs->trans(($this->statut == 0)?"ValidateOrder":"BackToOrder") ; ?>" /></td>
		
		<td width="33%" align="center">
			<input type="submit" id="btn" class="cashdeskinput" name="action" value="<?php echo $langs->trans("ValidateAndPrint"); ?>"<?php if($this->statut == 0) print ' disabled="disabled"'; ?>/>
		</td>
	</tr>
</table>
