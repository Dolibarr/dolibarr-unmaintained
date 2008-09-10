<?php
/* Copyright (C) 2003-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2007 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2008      Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
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
 * or see http://www.gnu.org/
 */

/**
	\file       htdocs/includes/modules/facture/saphir2/saphir2.modules.php
	\ingroup    facture
	\brief      Class filte of Saphir2 numbering module for invoice
	\version    $Id: saphir2.modules.php,v 1.1 2008/09/10 09:34:59 raphael_bertrand Exp $
*/

require_once(DOL_DOCUMENT_ROOT ."/includes/modules/facture/modules_facture.php");
require_once(DOL_DOCUMENT_ROOT.'/lib/saphir2.lib.php');


/**
	\class      mod_facture_saphir2
	\brief      Classe du modele de numerotation de reference de facture Saphir2
*/
class mod_facture_saphir2 extends ModeleNumRefFactures
{
	var $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	var $error = '';
	var $nom = 'Saphir2';

	
    /**     \brief      Renvoi la description du modele de numerotation
     *      \return     string      Texte descripif
     */
	function info()
    {
		global $conf,$langs;

		$langs->load("bills");
		$langs->load("saphir2");

		//extra admin actions
		if ($_POST["action"] == 'updateUseGlobalClientRef')
			$this->extra_admin_actions();
			
		$form = new Form($db);
		
		$texte = $langs->trans('GenericNumRefModelDesc')."<br>\n";
		$texte.= '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		$texte.= '<input type="hidden" name="action" value="updateMask">';
		$texte.= '<input type="hidden" name="maskconstinvoice" value="FACTURE_SAPHIR2_MASK_INVOICE">';
		$texte.= '<input type="hidden" name="maskconstcredit" value="FACTURE_SAPHIR2_MASK_CREDIT">';
		$texte.= '<table class="nobordernopadding" width="100%">';
		
		// Parametrage du prefix des factures
		$texte.= '<tr><td>'.$langs->trans("Mask").' ('.$langs->trans("InvoiceStandard").'):</td>';
		$texte.= '<td align="right">'.$form->textwithhelp('<input type="text" class="flat" size="24" name="maskinvoice" value="'.$conf->global->FACTURE_SAPHIR2_MASK_INVOICE.'">',$langs->trans("GenericMaskCodes",$langs->transnoentities("Invoice"),$langs->transnoentities("Invoice"),$langs->transnoentities("Invoice")),1,1).'</td>';

		$texte.= '<td align="left" rowspan="2">&nbsp; <input type="submit" class="button" value="'.$langs->trans("Modify").'" name="Button"></td>';

		$texte.= '</tr>';
		
		// Parametrage du prefix des avoirs
		$texte.= '<tr><td>'.$langs->trans("Mask").' ('.$langs->trans("InvoiceAvoir").'):</td>';
		$texte.= '<td align="right">'.$form->textwithhelp('<input type="text" class="flat" size="24" name="maskcredit" value="'.$conf->global->FACTURE_SAPHIR2_MASK_CREDIT.'">',$langs->trans("GenericMaskCodes",$langs->transnoentities("Invoice"),$langs->transnoentities("Invoice"),$langs->transnoentities("Invoice")),1,1).'</td>';
		$texte.= '</tr>';
		
		$texte.= '</table>';
		$texte.= '</form>';
		
		
		$texte.= '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		$texte.= '<input type="hidden" name="action" value="updateUseGlobalClientRef">';
		$texte.= '<input type="hidden" name="constUseGlobalClientRefinvoice" value="FACTURE_SAPHIR2_USEGLOBALCLIENTREF_INVOICE">';
		$texte.= '<input type="hidden" name="constUseGlobalClientRefcredit" value="FACTURE_SAPHIR2_USEGLOBALCLIENTREF_CREDIT">';
		$texte.= '<table class="nobordernopadding" width="100%">';
		// Set use of Global Client Ref for invoices
		$texte.= '<tr><td>'.$langs->trans("UseGlobalClientRef").':</td>';
		$texte.= '<td align="right">'.$langs->trans("InvoiceStandard").':</td>';
		$texte.= '<td align="right">'.$form->textwithhelp('<input type="checkbox" class="flat" name="valUseGlobalClientRefinvoice" value="1" '.(($conf->global->FACTURE_SAPHIR2_USEGLOBALCLIENTREF_INVOICE==true)?'checked':'').' />',$langs->trans("UseGlobalClientRefHelp",$langs->transnoentities("InvoiceStandard")),1,1).'</td>';
		// Set use of Global Client Ref for credit
		$texte.= '<td align="right">'.$langs->trans("InvoiceAvoir").':</td>';
		$texte.= '<td align="right">'.$form->textwithhelp('<input type="checkbox" class="flat" name="valUseGlobalClientRefcredit" value="1" '.(($conf->global->FACTURE_SAPHIR2_USEGLOBALCLIENTREF_CREDIT==true)?'checked':'').' />',$langs->trans("UseGlobalClientRefHelp",$langs->transnoentities("InvoiceAvoir")),1,1).'</td>';
		$texte.= '<td align="left" rowspan="2">&nbsp; <input type="submit" class="button" value="'.$langs->trans("Modify").'" name="Button"></td>';
		$texte.= '</tr>';
		$texte.= '</table>';
		$texte.= '</form>';


		return $texte;
    }

    /**     \brief      Renvoi un exemple de numerotation
     *      \return     string      Example
     */
    function getExample()
    {
    	global $conf,$langs,$mysoc;
    	
    	$old_code_client=$mysoc->code_client;
    	$mysoc->code_client='CCCCCCCCCC';
    	$numExample = $this->getNextValue($mysoc,'');
		$mysoc->code_client=$old_code_client;
        
		if (! $numExample)
		{
			$numExample = $langs->trans('NotConfigured');
		}
		return $numExample;
    }

	/**		\brief      Return next value
	*      	\param      objsoc      Object third party
	*      	\param      facture		Object invoice
	*      	\return     string      Value if OK, 0 if KO
	*/
	function getNextValue($objsoc,$facture)
	{
		global $db,$conf;

		require_once(DOL_DOCUMENT_ROOT ."/lib/functions2.lib.php");
		
		// On defini critere recherche compteur
		if (is_object($facture) && $facture->type == 2) $mask=$conf->global->FACTURE_SAPHIR2_MASK_CREDIT;
		else $mask=$conf->global->FACTURE_SAPHIR2_MASK_INVOICE;
		
		if (! $mask) 
		{
			$this->error='NotConfigured';
			return 0;
		}

		$where='';
		if ($facture->type == 2) 
		{
			$where.= " AND type = 2";
			if(empty($conf->global->FACTURE_MERCURE_USEGLOBALCLIENTREF_CREDIT))
				$useglobalrefclient=0;
			else
				$useglobalrefclient=$conf->global->FACTURE_MERCURE_USEGLOBALCLIENTREF_CREDIT;
		}
		else
		{
			$where.=" AND type != 2";
			if(empty($conf->global->FACTURE_MERCURE_USEGLOBALCLIENTREF_INVOICE))
				$useglobalrefclient=0;
			else
				$useglobalrefclient=$conf->global->FACTURE_MERCURE_USEGLOBALCLIENTREF_INVOICE;
		}
		
		$numFinal=get_next_value_saphir2($db,$mask,'facture','facnumber',$where,$objsoc->code_client,$facture->date,$useglobalrefclient,array());
		
		return  $numFinal;
	}
    
  
	/**		\brief      Return next free value
    *      	\param      objsoc      Object third party
	* 		\param		objforref	Object for number to search
    *   	\return     string      Next free value
    */
    function getNumRef($objsoc,$objforref)
    {
        return $this->getNextValue($objsoc,$objforref);
    }
	
 /**	
    *	\brief      Process extra admin actions
    *   	\return    boolean true
    */
	function extra_admin_actions()
	{
		global $db,$conf;
		
		if ($_POST["action"] == 'updateUseGlobalClientRef')
		{
			$constUseGlobalClientRefinvoice=$_POST['constUseGlobalClientRefinvoice'];
			$constUseGlobalClientRefcredit=$_POST['constUseGlobalClientRefcredit'];
			if(empty($_POST['valUseGlobalClientRefinvoice']))
				$valUseGlobalClientRefinvoice=0;
			else
				$valUseGlobalClientRefinvoice=$_POST['valUseGlobalClientRefinvoice'];
				
			if(empty($_POST['valUseGlobalClientRefcredit']))
				$valUseGlobalClientRefcredit=0;
			else
				$valUseGlobalClientRefcredit=$_POST['valUseGlobalClientRefcredit'];
				
			if ($constUseGlobalClientRefinvoice)  dolibarr_set_const($db,$constUseGlobalClientRefinvoice,$valUseGlobalClientRefinvoice);
			if ($constUseGlobalClientRefcredit)  dolibarr_set_const($db,$constUseGlobalClientRefcredit,$valUseGlobalClientRefcredit);
		}
		return true;
	}
	
}    
?>