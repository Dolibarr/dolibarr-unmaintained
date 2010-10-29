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
 *   	\file       pointofsale/tpl/customer_classical.tpl.php
 *		\ingroup    pointofsale
 *		\brief      Template page that display the name and address of the customer
 *		\version    $Id: customer_classical.tpl.php,v 1.1 2010/10/29 16:40:51 hregis Exp $
 *		\author		dmartin
 */

?>

<p class="customeraddress"><?php print $this->customer->nom ?><br />
<?php
if($_GET['action'] == 'newCustomer') {
	require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php") ;
	$html = new FormCompany($this->db) ;
?>
<form name="form_contact" action="<?php echo $_SERVER['PHP_SELF'] ; ?>" method="post">
<input type="hidden" name="action" value="addContact" />

<table width="100%" class="customerzone">
<tr>
	<td align="right"><?php print $langs->trans("Name") ;?> :</td>
	<td><?php $html->select_civilite('', 'id_civilite') ;?> <input type="text" class="customerzone" name="name" /> </td>
</tr>
<tr>
	<td align="right"><?php print $langs->trans("Address") ;?> :</td>
	<td><input type="text" name="address" class="customerzone" size="30" /></td>
</tr>
<tr>
	<td align="right"><?php print $langs->trans("ZipTown") ;?> :</td>
	<td><input type="text" size="3" class="customerzone" name="zip" /> <input type="text" name="city" class="customerzone" size="20"/></td>
</tr>
<tr>
	<td align="right"><?php print $langs->trans("Phone") ;?> :</td>
	<td><input type="text" name="phone" class="customerzone" /><a class="butAction" style="padding:0.5% 2% ;" href="<?php print $_SERVER['PHP_SELF'] ; ?>">Annuler</a></td>
</tr>
<tr>
	<td align="right"><?php print $langs->trans("Email") ;?> :</td>
	<td><input type="text" name="email" class="customerzone" /> <input type="submit" class="customerzone" value="Valider" /></td>
</tr>
</table>
</form>
<?php 
}
elseif($_GET['action'] == 'findCustomer') {
?>
<form name="form_contact" action="<?php print $_SERVER['PHP_SELF'] ; ?>" method="post">
<input type="hidden" name="action" value="setContact" />
	<?php print $langs->trans("ChoseCustomer") ;?><br />
	<?php $form->select_contacts($conf->global->POS_GENERIC_THIRD, $this->contact->id, 'contactid', 1) ; ?>
	<input type="submit" class="customerzone" value="Valider" />
</form>
</p>
<?php 
}
else {
	if(!$this->contact->id) {
		print $langs->trans('NoAddressFilled')."<br />\n" ;
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/pointofsale/cashdesk.php?action=newCustomer" >'.$langs->trans('NewCustomer').'</a>'."\n" ;
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/pointofsale/cashdesk.php?action=findCustomer" >'.$langs->trans('FindCustomer').'</a></p>'."\n" ;
	} else {
		print $this->contact->getFullName($langs, 1)."<br />\n" ;
		print $this->contact->address." - ".$this->contact->cp." ".$this->contact->ville."<br />\n" ;
		if($this->contact->phone_pro) print $this->contact->phone_pro." &nbsp; " ;
		if($this->contact->email) print $this->contact->email ;
		print "</p>\n" ;
	}
}

