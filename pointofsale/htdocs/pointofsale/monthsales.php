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
 *   	\file       pointofsale/monthsales.php
 *		\ingroup    pointofsale
 *		\brief      Page that displays a invoice regrouping sales of a month.
 *		\version    $Id: monthsales.php,v 1.1 2010/11/20 13:50:43 denismartin Exp $
 *		\author		dmartin
 */

require("../main.inc.php");

require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php") ;
require_once(DOL_DOCUMENT_ROOT."/pointofsale/class/cashdesk.class.php") ;
require_once(DOL_DOCUMENT_ROOT."/lib/functions.lib.php") ;

$langs->load("companies");
$langs->load("other");

$langs->load('@pointofsale') ;

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

$cashdesk = new Cashdesk($db, $user) ;

if($conf->global->POS_GENERIC_THIRD) {
	
	$facnummask = substr($cashdesk->getNextNumRef(''), 0, 6) ;
	
	$arraytest = array() ;
	$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."facture WHERE fk_soc=" ;
	$sql.= $conf->global->POS_GENERIC_THIRD ;
	$sql.= " AND facnumber LIKE '".$facnummask."%'" ;
	
	dol_syslog("pointofsale/monthsales.php sql=".$sql, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql) ;
		$i = 0 ;
		while ($i < $num) {
			$obj = $db->fetch_object($resql) ;
			$listfactures[] = $obj->rowid ;
			$i++ ;
		}
		$db->free($resql);
	}
	else {
		$error="Error ".$db->lasterror();
		dol_syslog("pointofsale/monthsales.php ".$error, LOG_ERR);
	}
	
	$facture = new Facture($db) ;
	$monthsales = array() ;
	
	foreach($listfactures as $idfac) {
		$facture = new Facture($db) ;
		$facture->fetch($idfac) ;
		foreach($facture->lines as $line) {
			$monthsales[$line->fk_product]['qty'] += $line->qty ;
			$monthsales[$line->fk_product]['total_ttc'] += $line->total_ttc ;
			$monthsales[$line->fk_product]['subprice'][] = $line->subprice ;
		}
		
	}
	
	$sql = "SELECT p.rowid, p.amount, p.fk_paiement, f.facnumber " ;
	$sql.= " FROM ".MAIN_DB_PREFIX."facture f " ;
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."paiement_facture pf ON pf.fk_facture=f.rowid " ;
	$sql.= " INNER JOIN ".MAIN_DB_PREFIX."paiement p ON p.rowid=pf.fk_paiement " ;
	$sql.= " WHERE f.facnumber LIKE '".$facnummask."%' " ;
	$sql.= " AND f.fk_soc=".$conf->global->POS_GENERIC_THIRD ;
	
	$monthpaiements = array() ;
	dol_syslog("pointofsale/monthsales.php sql=".$sql, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql) {
		$num = $db->num_rows($resql) ;
		$i = 0 ;
		while ($i < $num) {
			$obj = $db->fetch_object($resql) ;
			$monthpaiements[$obj->fk_paiement] += $obj->amount ;
			$i++ ;
		}
		$db->free($resql);
	}
	else {
		$error="Error ".$db->lasterror();
		dol_syslog("pointofsale/monthsales.php ".$error, LOG_ERR);
	}
	
	/*
	$facmonthsales = new Facture('') ;
	$monthline ;
	
	foreach($monthsales as $idprod => $line) {
		$monthline = new FactureLigne('') ;
		$monthline->fk_product = $idprod ;
	}*/
	
	

} else {
	//TODO POS_GENERIC_THIRD not defined
}



/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('MonthSalesTitle'),'');

print_fiche_titre($langs->trans('MonthSales')) ;

$form=new Form($db);

// Put here content of your page
// ...
?>
<!-- Main div -->
<div style="height:500px; background-color:#EEEEEE;">


<div align="center" style="height:10%;padding-top:2%">
<?php
foreach($monthsales as $idprod => $line) {
	print $idprod.' : '.$line['qty'].' x '.$line['subprice'][0].' = '. $line['total_ttc'] ; 	
	print '<br />' ;
}
?>
</div>

<?php 
/**
 * Invoice content
 */
	$now=dol_now();
	
?>

<table class="border" width="100%">

<tr><td width="20%"><?php print $langs->trans('Ref')?></td>
<td colspan="5">MonthSales</td>
</tr>

<?php // Third party ?>
<tr><td><?php print$langs->trans('Company') ?></td>
<td colspan="5"><?php print $cashdesk->customer->getNomUrl(1,'compta') ;?>
 &nbsp; (<a href="<?php print DOL_URL_ROOT.'/compta/facture.php?socid='.$object->socid?>"><?php print $langs->trans('OtherBills')?></a>)</td>
</tr>


<tr><td>
<table class="nobordernopadding" width="100%"><tr><td>
<?php print $langs->trans('Date'); ?>
</td>
</tr></table>
</td><td colspan="3">
MonthDate
</td>

<?php 
	/*
	 * List of payments
	 */

	$nbrows=8;
?>

<td rowspan="<?php print $nbrows?>" colspan="2" valign="top">

<table class="nobordernopadding" width="100%">

<tr class="liste_titre">
<td><?php print $langs->trans('Payments') ; ?></td>
<td><?php print $langs->trans('Type') ; ?></td>
<td align="right"><?php print $langs->trans('Amount') ; ?></td>
<td width="18">&nbsp;</td>
</tr>

<?php
	$var=true;
	$i = 0 ;

	foreach($monthpaiements as $fk_paiement => $amount)
	{
		$var=!$var;
		print '<tr '.$bc[$var].'><td>';
		print 'MonthPayments</td>';
		$label=$langs->trans("PaymentType".$fk_paiement);
		print '<td>'.$label.'</td>';
		print '<td align="right">'.price($amount).' &euro;</td>';
		print '<td>&nbsp;</td>';
		print '</tr>';
		$i++;
	}

/*	if ($object->type != 2)
	{
		// Total already paid
		print '<tr><td colspan="2" align="right">';
		if ($object->type != 3) print $langs->trans('AlreadyPaidNoCreditNotesNoDeposits');
		else print $langs->trans('AlreadyPaid');
		print ' :</td><td align="right">'.price($totalpaye).'</td><td>&nbsp;</td></tr>';

		$resteapayeraffiche=$resteapayer;

		// Loop on each credit note or deposit amount applied
		$creditnoteamount=0;
		$depositamount=0;
		$sql = "SELECT re.rowid, re.amount_ht, re.amount_tva, re.amount_ttc,";
		$sql.= " re.description, re.fk_facture_source, re.fk_facture_source";
		$sql.= " FROM ".MAIN_DB_PREFIX ."societe_remise_except as re";
		$sql.= " WHERE fk_facture = ".$object->id;
		$resql=$db->query($sql);
		if ($resql)
		{
			$num = $db->num_rows($resql);
			$i = 0;
			$invoice=new Facture($db);
			while ($i < $num)
			{
				$obj = $db->fetch_object($resql);
				$invoice->fetch($obj->fk_facture_source);
				print '<tr><td colspan="2" align="right">';
				if ($invoice->type == 2) print $langs->trans("CreditNote").' ';
				if ($invoice->type == 3) print $langs->trans("Deposit").' ';
				print $invoice->getNomUrl(0);
				print ' :</td>';
				print '<td align="right">'.price($obj->amount_ttc).'</td>';
				print '<td align="right">';
				print '<a href="'.$_SERVER["PHP_SELF"].'?facid='.$object->id.'&action=unlinkdiscount&discountid='.$obj->rowid.'">'.img_delete().'</a>';
				print '</td></tr>';
				$i++;
				if ($invoice->type == 2) $creditnoteamount += $obj->amount_ttc;
				if ($invoice->type == 3) $depositamount += $obj->amount_ttc;
			}
		}
		else
		{
			dol_print_error($db);
		}

		// Paye partiellement 'escompte'
		if (($object->statut == 2 || $object->statut == 3) && $object->close_code == 'discount_vat')
		{
			print '<tr><td colspan="2" align="right" nowrap="1">';
			print $html->textwithpicto($langs->trans("Escompte").':',$langs->trans("HelpEscompte"),-1);
			print '</td><td align="right">'.price($object->total_ttc - $creditnoteamount - $depositamount - $totalpaye).'</td><td>&nbsp;</td></tr>';
			$resteapayeraffiche=0;
		}
		// Paye partiellement ou Abandon 'badcustomer'
		if (($object->statut == 2 || $object->statut == 3) && $object->close_code == 'badcustomer')
		{
			print '<tr><td colspan="2" align="right" nowrap="1">';
			print $html->textwithpicto($langs->trans("Abandoned").':',$langs->trans("HelpAbandonBadCustomer"),-1);
			print '</td><td align="right">'.price($object->total_ttc - $creditnoteamount - $depositamount - $totalpaye).'</td><td>&nbsp;</td></tr>';
			//$resteapayeraffiche=0;
		}
		// Paye partiellement ou Abandon 'product_returned'
		if (($object->statut == 2 || $object->statut == 3) && $object->close_code == 'product_returned')
		{
			print '<tr><td colspan="2" align="right" nowrap="1">';
			print $html->textwithpicto($langs->trans("ProductReturned").':',$langs->trans("HelpAbandonProductReturned"),-1);
			print '</td><td align="right">'.price($object->total_ttc - $creditnoteamount - $depositamount - $totalpaye).'</td><td>&nbsp;</td></tr>';
			$resteapayeraffiche=0;
		}
		// Paye partiellement ou Abandon 'abandon'
		if (($object->statut == 2 || $object->statut == 3) && $object->close_code == 'abandon')
		{
			print '<tr><td colspan="2" align="right" nowrap="1">';
			$text=$langs->trans("HelpAbandonOther");
			if ($object->close_note) $text.='<br><br><b>'.$langs->trans("Reason").'</b>:'.$object->close_note;
			print $html->textwithpicto($langs->trans("Abandoned").':',$text,-1);
			print '</td><td align="right">'.price($object->total_ttc - $creditnoteamount - $depositamount - $totalpaye).'</td><td>&nbsp;</td></tr>';
			$resteapayeraffiche=0;
		}

		// Billed
		print '<tr><td colspan="2" align="right">'.$langs->trans("Billed").' :</td><td align="right" style="border: 1px solid;">'.price($object->total_ttc).'</td><td>&nbsp;</td></tr>';

		// Remainder to pay
		print '<tr><td colspan="2" align="right">';
		if ($resteapayeraffiche >= 0) print $langs->trans('RemainderToPay');
		else print $langs->trans('ExcessReceived');
		print ' :</td>';
		print '<td align="right" style="border: 1px solid;" bgcolor="#f0f0f0"><b>'.price($resteapayeraffiche).'</b></td>';
		print '<td nowrap="nowrap">&nbsp;</td></tr>';
	}
	else
	{
		// Sold credit note
		print '<tr><td colspan="2" align="right">'.$langs->trans('TotalTTCToYourCredit').' :</td>';
		print '<td align="right" style="border: 1px solid;" bgcolor="#f0f0f0"><b>'.price(abs($object->total_ttc)).'</b></td><td>&nbsp;</td></tr>';
	}

	print '</table>';

	print '</td></tr>';

	// Date payment term
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td>';
	print $langs->trans('DateMaxPayment');
	print '</td>';
	if ($object->type != 2 && $_GET['action'] != 'editpaymentterm' && $object->brouillon && $user->rights->facture->creer) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editpaymentterm&amp;facid='.$object->id.'">'.img_edit($langs->trans('SetDate'),1).'</a></td>';
	print '</tr></table>';
	print '</td><td colspan="3">';
	if ($object->type != 2)
	{
		if ($_GET['action'] == 'editpaymentterm')
		{
			$html->form_date($_SERVER['PHP_SELF'].'?facid='.$object->id,$object->date_lim_reglement,'paymentterm');
		}
		else
		{
			print dol_print_date($object->date_lim_reglement,'daytext');
			if ($object->date_lim_reglement < ($now - $conf->facture->client->warning_delay) && ! $object->paye && $object->statut == 1 && ! $object->am) print img_warning($langs->trans('Late'));
		}
	}
	else
	{
		print '&nbsp;';
	}
	print '</td></tr>';

	// Conditions de reglement
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td>';
	print $langs->trans('PaymentConditionsShort');
	print '</td>';
	if ($object->type != 2 && $_GET['action'] != 'editconditions' && $object->brouillon && $user->rights->facture->creer) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editconditions&amp;facid='.$object->id.'">'.img_edit($langs->trans('SetConditions'),1).'</a></td>';
	print '</tr></table>';
	print '</td><td colspan="3">';
	if ($object->type != 2)
	{
		if ($_GET['action'] == 'editconditions')
		{
			$html->form_conditions_reglement($_SERVER['PHP_SELF'].'?facid='.$object->id,$object->cond_reglement_id,'cond_reglement_id');
		}
		else
		{
			$html->form_conditions_reglement($_SERVER['PHP_SELF'].'?facid='.$object->id,$object->cond_reglement_id,'none');
		}
	}
	else
	{
		print '&nbsp;';
	}
	print '</td></tr>';

	// Mode de reglement
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td>';
	print $langs->trans('PaymentMode');
	print '</td>';
	if ($_GET['action'] != 'editmode' && $object->brouillon && $user->rights->facture->creer) print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=editmode&amp;facid='.$object->id.'">'.img_edit($langs->trans('SetMode'),1).'</a></td>';
	print '</tr></table>';
	print '</td><td colspan="3">';
	if ($_GET['action'] == 'editmode')
	{
		$html->form_modes_reglement($_SERVER['PHP_SELF'].'?facid='.$object->id,$object->mode_reglement_id,'mode_reglement_id');
	}
	else
	{
		$html->form_modes_reglement($_SERVER['PHP_SELF'].'?facid='.$object->id,$object->mode_reglement_id,'none');
	}
	print '</td></tr>';

	// Lit lignes de facture pour determiner montant
	// On s'en sert pas mais ca sert pour debuggage
	/*
	$sql  = 'SELECT l.price as price, l.qty, l.rowid, l.tva_tx,';
	$sql .= ' l.remise_percent, l.subprice';
	$sql .= ' FROM '.MAIN_DB_PREFIX.'facturedet as l ';
	$sql .= ' WHERE l.fk_facture = '.$object->id;
	$resql = $db->query($sql);
	if ($resql)
	{
	$num_lignes = $db->num_rows($resql);
	$i=0;
	$total_lignes_ht=0;
	$total_lignes_vat=0;
	$total_lignes_ttc=0;
	while ($i < $num_lignes)
	{
	$obj=$db->fetch_object($resql);
	$ligne_ht=($obj->price*$obj->qty);
	$ligne_vat=($ligne_ht*$obj->tva_tx/100);
	$ligne_ttc=($ligne_ht+$ligne_vat);
	$total_lignes_ht+=$ligne_ht;
	$total_lignes_vat+=$ligne_vat;
	$total_lignes_ttc+=$ligne_ttc;
	$i++;
	}
	}
	*/

	// Montants
/*	print '<tr><td>'.$langs->trans('AmountHT').'</td>';
	print '<td align="right" colspan="2" nowrap>'.price($object->total_ht).'</td>';
	print '<td>'.$langs->trans('Currency'.$conf->monnaie).'</td></tr>';
	print '<tr><td>'.$langs->trans('AmountVAT').'</td><td align="right" colspan="2" nowrap>'.price($object->total_tva).'</td>';
	print '<td>'.$langs->trans('Currency'.$conf->monnaie).'</td></tr>';

	// Amount Local Taxes
	if ($mysoc->pays_code=='ES')
	{
		if ($mysoc->localtax1_assuj=="1") //Localtax1 RE
		{
			print '<tr><td>'.$langs->transcountry("AmountLT1",$mysoc->pays_code).'</td>';
			print '<td align="right" colspan="2" nowrap>'.price($object->total_localtax1).'</td>';
			print '<td>'.$langs->trans("Currency".$conf->monnaie).'</td></tr>';
		}
		if ($mysoc->localtax2_assuj=="1") //Localtax2 IRPF
		{
			print '<tr><td>'.$langs->transcountry("AmountLT2",$mysoc->pays_code).'</td>';
			print '<td align="right" colspan="2" nowrap>'.price($object->total_localtax2).'</td>';
			print '<td>'.$langs->trans("Currency".$conf->monnaie).'</td></tr>';
		}
	}

	print '<tr><td>'.$langs->trans('AmountTTC').'</td><td align="right" colspan="2" nowrap>'.price($object->total_ttc).'</td>';
	print '<td>'.$langs->trans('Currency'.$conf->monnaie).'</td></tr>';

	// Statut
	print '<tr><td>'.$langs->trans('Status').'</td>';
	print '<td align="left" colspan="3">'.($object->getLibStatut(4,$totalpaye)).'</td></tr>';

	// Project
	if ($conf->projet->enabled)
	{
		$langs->load('projects');
		print '<tr>';
		print '<td>';

		print '<table class="nobordernopadding" width="100%"><tr><td>';
		print $langs->trans('Project');
		print '</td>';
		if ($_GET['action'] != 'classin')
		{
			print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=classin&amp;facid='.$object->id.'">';
			print img_edit($langs->trans('SetProject'),1);
			print '</a></td>';
		}
		print '</tr></table>';

		print '</td><td colspan="3">';
		if ($_GET['action'] == 'classin')
		{
			$html->form_project($_SERVER['PHP_SELF'].'?facid='.$object->id,$object->socid,$object->fk_project,'projectid');
		}
		else
		{
			$html->form_project($_SERVER['PHP_SELF'].'?facid='.$object->id,$object->socid,$object->fk_project,'none');
		}
		print '</td>';
		print '</tr>';
	}

	print '</table><br>';


	/*
	 * Lines
	 */
/*            $result = $object->getLinesArray();

            if ($conf->use_javascript_ajax && $object->statut == 0)
            {
                include(DOL_DOCUMENT_ROOT.'/core/tpl/ajaxrow.tpl.php');
            }

	print '<table id="tablelines" class="noborder" width="100%">';

    if (!empty($object->lines))
            {
                $object->print_title_list();
                $object->printLinesList(1,$mysoc,$soc);
            }

	/*
	 * Form to add new line
	 */
/*	if ($object->statut == 0 && $user->rights->facture->creer && $_GET['action'] <> 'valid' && $_GET['action'] <> 'editline')
	{
		$var=true;

		$object->showAddFreeProductForm(1,$mysoc,$soc);

		// Add predefined products/services
		if ($conf->product->enabled || $conf->service->enabled)
		{
			$var=!$var;
			$object->showAddPredefinedProductForm(1,$mysoc,$soc);
		}

		// Hook of thirdparty module
		if (! empty($object->hooks))
		{
			foreach($object->hooks as $module)
			{
				$var=!$var;
				$module->formAddObject($object);
			}
		}
	}
*/
	print "</table>\n";

	print "</div>\n";





?>

<!-- End of main div -->
</div>


<?php


/***************************************************
* LINKED OBJECT BLOCK
*
* Put here code to view linked object
****************************************************/
/*$myobject->load_object_linked($myobject->id,$myobject->element);
				
foreach($myobject->linked_object as $object => $objectid)
{
	if($conf->$object->enabled)
	{
		$somethingshown=$myobject->showLinkedObjectBlock($object,$objectid,$somethingshown);
	}
}*/

// End of page
$db->close();
llxFooter('$Date: 2010/11/20 13:50:43 $ - $Revision: 1.1 $');
?>




