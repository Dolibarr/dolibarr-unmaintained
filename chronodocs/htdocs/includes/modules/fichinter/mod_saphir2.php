<?php
/* Copyright (C) 2003-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2007 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2008 Raphael Bertrand (Resultic)       <raphael.bertrand@resultic.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PSAPHIR2ULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * or see http://www.gnu.org/
 */

/**
	\file       htdocs/includes/modules/fichinter/mod_saphir2.php
	\ingroup    fiche intervention
	\brief      Fichier contenant la classe du modèle de numérotation de référence de fiche intervention Saphir2
	\version    $Id: mod_saphir2.php,v 1.1 2008/09/10 09:34:58 raphael_bertrand Exp $
*/

require_once(DOL_DOCUMENT_ROOT ."/includes/modules/fichinter/modules_fichinter.php");
require_once(DOL_DOCUMENT_ROOT.'/lib/saphir2.lib.php');

/**
	\class      mod_saphir2
	\brief      Classe du modèle de numérotation de référence de fiche intervention Saphir2
*/
class mod_saphir2 extends ModeleNumRefFicheinter
{
	var $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	var $error = '';
	var $nom = 'Saphir2';
	
	/**   \brief      Constructeur
	*/
	function mod_saphir2()
	{
		$this->nom = "saphir2";
	}

 /**     \brief      Renvoi la description du modele de numérotation
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
		$texte.= '<input type="hidden" name="maskconst" value="FICHINTER_SAPHIR2_MASK">';
		$texte.= '<table class="nobordernopadding" width="100%">';
		
		// Parametrage du prefix des factures
		$texte.= '<tr><td>'.$langs->trans("Mask").':</td>';
		$texte.= '<td align="right">'.$form->textwithhelp('<input type="text" class="flat" size="24" name="maskvalue" value="'.$conf->global->FICHINTER_SAPHIR2_MASK.'">',$langs->trans("GenericMaskCodes",$langs->transnoentities("InterventionCard"),$langs->transnoentities("InterventionCard"),$langs->transnoentities("InterventionCard")),1,1).'</td>';

		$texte.= '<td align="left" rowspan="2">&nbsp; <input type="submit" class="button" value="'.$langs->trans("Modify").'" name="Button"></td>';

		$texte.= '</tr>';
		
		$texte.= '</table>';
		$texte.= '</form>';
		
		// Set use of Global Client Ref
		$texte.= '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		$texte.= '<input type="hidden" name="action" value="updateUseGlobalClientRef">';
		$texte.= '<input type="hidden" name="constUseGlobalClientRef" value="FICHINTER_SAPHIR2_USEGLOBALCLIENTREF">';
		$texte.= '<table class="nobordernopadding" width="100%">';
		$texte.= '<tr><td>'.$langs->trans("UseGlobalClientRef").':</td>';
		$texte.= '<td align="right">'.$form->textwithhelp('<input type="checkbox" class="flat" name="valUseGlobalClientRef" value="1" '.(($conf->global->FICHINTER_SAPHIR2_USEGLOBALCLIENTREF==true)?'checked':'').' />',$langs->trans("UseGlobalClientRefHelp",$langs->transnoentities("InterventionCard")),1,1).'</td>';
		$texte.= '<td align="left" rowspan="2">&nbsp; <input type="submit" class="button" value="'.$langs->trans("Modify").'" name="Button"></td>';
		$texte.= '</tr>';
		$texte.= '</table>';
		$texte.= '</form>';

		return $texte;
    }

    /**     \brief      Renvoi un exemple de numérotation
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

	/**		\brief      Renvoi prochaine valeur attribuée
	*      	\param      objsoc      Objet société
	*      	\param      ficheinter	Object ficheinter
	*      	\return     string      Valeur
	*/
    function getNextValue($objsoc=0,$ficheinter='')
    {
		global $db,$conf;

		require_once(DOL_DOCUMENT_ROOT ."/lib/functions2.lib.php");
		
		// On défini critere recherche compteur
		$mask=$conf->global->FICHINTER_SAPHIR2_MASK;
		
		if (! $mask) 
		{
			$this->error='NotConfigured';
			return 0;
		}
		
		if(empty($conf->global->FICHINTER_SAPHIR2_USEGLOBALCLIENTREF))
 			$useglobalrefclient=0;
 		else
 			$useglobalrefclient=$conf->global->FICHINTER_SAPHIR2_USEGLOBALCLIENTREF;

			
		$extraparams=array();//no extra params basicaly
		if(!empty($conf->global->ALLOWONLYREFCLIENT))
			$extraparams['allowOnlyRefClient']=true;
			
		$numFinal=get_next_value_saphir2($db,$mask,'fichinter','ref','',$objsoc->code_client,$ficheinter->date,$useglobalrefclient,$extraparams);
		
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
			$constUseGlobalClientRef=$_POST['constUseGlobalClientRef'];
			if(empty($_POST['valUseGlobalClientRef']))
				$valUseGlobalClientRef=0;
			else
				$valUseGlobalClientRef=$_POST['valUseGlobalClientRef'];
			if ($constUseGlobalClientRef)  dolibarr_set_const($db,$constUseGlobalClientRef,$valUseGlobalClientRef);
		}
		return true;
	}
	
	
}    

?>