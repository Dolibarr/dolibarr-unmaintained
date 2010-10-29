<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Denis Martin <denimartin@hotmail.fr>
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
 *   	\file       pointofsale/cashdesk.php
 *		\ingroup    pointofsale
 *		\brief      This pages manages the cashdesk
 *		\version    $Id: cashdesk.php,v 1.1 2010/10/29 16:40:50 hregis Exp $
 *		\author		dmartin
 */

require("../main.inc.php");

require_once(DOL_DOCUMENT_ROOT."/pointofsale/class/cashdesk.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/functions.lib.php") ;

$langs->load("companies");
$langs->load("other");

$langs->load('@pointofsale') ;

// Get parameters
$action = isset($_POST["action"])?$_POST["action"]:'' ;
$action = isset($_GET["action"])?$_GET["action"]: $action ;

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

if(!$user->rights->pointofsale->cashdesk->use) {
	accessforbidden() ;
}


/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

//Create cashdesk object that will load the existing basket from DB
$cashdesk = new Cashdesk($db, $user) ;

if($action == 'newsale') {
	$cashdesk->newBasket($user) ;
}
elseif($action == $langs->trans("AddLine")) {
	$errors = array() ;
	$productId = "" ;
	$qty = "" ;
	
	//Add line to basket
	if($_POST['selectedProductId']){
		$productId = $_POST['selectedProductId'] ;
	}
	else {
		$errors[] = $langs->trans("NoProductSelected") ;
	}
	
	if($_POST['numField']) {
		$qty = $_POST['numField'] ;
	}
	else {
		$errors[] = $langs->trans("QtyUndefined") ;
	}
	
	if(count($errors) == 0) {
		$cashdesk->addLine($user, $productId, $qty) ;
	}
	
}
elseif($action == $langs->trans("Offered")) {
	$errors = array() ;
	$productId = "" ;
	$qty = "" ;
	
	//Add line to basket
	if($_POST['selectedProductId']){
		$productId = $_POST['selectedProductId'] ;
	}
	else {
		$errors[] = $langs->trans("NoProductSelected") ;
	}
	
	if($_POST['numField']) {
		$qty = $_POST['numField'] ;
	}
	else {
		$errors[] = $langs->trans("QtyUndefined") ;
	}
	
	if(count($errors) == 0) {
		$cashdesk->addLine($user, $productId, $qty, 100) ;
	}
}
elseif($action == $langs->trans("DeleteLine")) {
	$errors = array() ;
	$selectedLine = 0 ;
	
	if($_POST['selectedLine']) {
		$selectedLine = $_POST['selectedLine'] ;
	}
	else {
		$errors[] = $langs->trans("NoLineSelected") ;
	}
	
	if(count($errors) == 0) {
		$cashdesk->deleteLine($user, $selectedLine) ;
	}
}
elseif($action == $langs->trans("ValidateOrder")) {
	$cashdesk->statut = BASKET_VALIDATED ; //1 ;
	$cashdesk->updateBasket($user) ;
}
elseif($action == $langs->trans("BackToOrder")) {
	$cashdesk->statut = BASKET_DRAFT ; // 0 ;
	foreach($cashdesk->paiements as $paiement) {
		$paiement->delete($user) ;
	}
	$cashdesk->paiements = array() ;
	$cashdesk->amount_paid = 0 ;
	$cashdesk->updateBasket($user) ;
}
elseif($action == $langs->trans("PaymentCard") || $action == $langs->trans('PaymentCheck') || $action == $langs->trans('PaymentCash')) {
	if($_POST['numField']) {
		$cashdesk->addPaiement($user, $action, $_POST['numField']) ;
	}
	else {
		$errors[] = $langs->trans("NoAmountForPayment") ;
	}
}
elseif($action == $langs->trans("DeletePayment")) {
	if($_POST['selectedPaiement']) {
		$cashdesk->deletePaiement($user, $_POST['selectedPaiement']) ;
	}
	else {
		$errors[] = $langs->trans("NoPaymentSelected") ;
	}
}
elseif($_POST['action'] == 'addContact') {
	$error = array() ;
	if(!$_POST["name"]) {
		$errors[] = $langs->trans("ErrorFieldRequired",$langs->transnoentities("Lastname")) ;
	}
	if($cashdesk->addContact($user, $_POST["name"], $_POST["id_civilite"], $_POST["address"], $_POST["zip"], $_POST["city"], $_POST["phone"], $_POST["email"]) < 0)
		$errors[] = $langs->trans("ErrorAddingContact") ;
}
elseif($_POST['action'] == 'setContact') {
	if(!$_POST["contactid"]) {
		$errors[] = $langs->trans("NoContactSelected") ;
	} else {
		if($cashdesk->setContact($user, $_POST["contactid"]) != 1)
			$errors[] = $langs->trans("ErrorSettingContact") ;
	}
}
elseif($_POST['action'] == $langs->trans('ValidateAndPrint')) {
	$invoiceCreated = $cashdesk->createInvoice($user) ;

}


/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('PointOfSale'),'', '', 0, 0, '', array("/pointofsale/pointofsale.css"));

$form=new Form($db);

if($invoiceCreated > 0) {
?>

<script type="text/javascript">
var p = window.open('<?php print DOL_URL_ROOT.'/document.php?modulepart=facture&file='.$cashdesk->ref.'/'.$cashdesk->ref.'.pdf' ; ?>', '') ;
p.print() ;
p.close() ;
</script>

<?php
	
	$cashdesk->newBasket($user) ;
}

if(count($errors) != 0 || count($cashdesk->errors) != 0) {
?>
<!-- JS Popup to display errors and other messages -->
<div id="dialog-message" title="<?php print $langs->trans("Information") ;?>">
<?php 
	if(count($errors) != 0) {
		foreach($errors as $err) {
			print '<p style="font-size:18px;">'.img_warning().' '.$err.'</p>' ;
		}
	} else {
		print "<hr />" ;
		foreach($cashdesk->errors as $err) {
			print '<p style="font-size:18px;">'.img_warning().' '.$err.'</p>' ;
		}
	}
?>
</div>
<script type="text/javascript">
			    jQuery("#dialog-message").dialog({
			        autoOpen: true,
			        resizable: false,
			        height:250,
			        width:590,
			        modal: true,
			        closeOnEscape: true,
			    });

			    $(document).click(function() {
			    	$("#dialog-message").dialog("close") ;
			    }) ;
</script>
<!-- End of JS popup -->
<?php } ?>


<form name="form_cashdesk" action="<?php print $_SERVER['PHP_SELF'] ; ?>" method="post">

<!--  Main div -->
<div style="height:600px; background-color:#EEEEEE;">
<!-- Main table -->
<table width="100%" height="100%" border="solid">

<tr height="13%">
	<td width="70%">
	<!-- Title cell -->
<?php 

$cashdesk->displayTitle() ;

?>
	<!-- End of title cell -->
	</td>
	<td align="center" width="30%">
<?php 

$cashdesk->displayCustomer() ;

?>
	</td>
</tr>

<tr height="90%">

	<td>
	
	<table height="100%" width="100%">
		<tr height="50%"><td>
		<!-- Cell to display product selection table -->		
<?php

$cashdesk->displayProducts() ;

?>
		<!-- End of cell to display product selection table -->			
		</td></tr>


		<tr height="50%"><td>
		<!-- Cell to display control buttons -->
<?php 

$cashdesk->displayControls() ;

?>
		<!-- End of cell to display control buttons -->
		</td></tr>
	</table>
	
	</td>
	
	<td>
	<!-- Cell to display basket -->
<?php 

$cashdesk->displayBasket() ;

?>
	<!-- End of cell to display basket -->
	</td>
</tr>

<!-- End of main table -->
</table>
<!-- End of main div -->
</div>

</form>

<?php


// End of page
$db->close();
llxFooter('$Date: 2010/10/29 16:40:50 $ - $Revision: 1.1 $');
?>
