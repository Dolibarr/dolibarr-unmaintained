<?php
/* Copyright (C) 2006-2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2007      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2008 	   Raphael Bertrand (Resultic)  <raphael.bertrand@resultic.fr>
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
 *
 * $Id: chronodocs.lib.php,v 1.3 2010/03/29 20:53:25 grandoc Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/chronodocs/htdocs/lib/Attic/chronodocs.lib.php,v $
 */

/**
   \file       htdocs/lib/chronodocs.lib.php
   \brief      Ensemble de fonctions de base pour le module chronodocs
   \ingroup    chronodocs
   \version    $Revision: 1.3 $
   
   Ensemble de fonctions de base de dolibarr sous forme d'include
*/



function chronodocs_prepare_head($chronodocs)
{
  global $langs, $conf, $user;
  $langs->load("chronodocs");
  
  $h = 0;
  $head = array();
  
  $head[$h][0] = DOL_URL_ROOT.'/chronodocs/fiche.php?id='.$chronodocs->id;
  $head[$h][1] = $langs->trans("Card");
  $head[$h][2] = 'card';
  $h++;
  
  $head[$h][0] = DOL_URL_ROOT.'/chronodocs/contact.php?id='.$chronodocs->id;
	$head[$h][1] = $langs->trans('Contacts');
	$head[$h][2] = 'contact';
	$h++;
	
	$head[$h][0] = DOL_URL_ROOT.'/chronodocs/document.php?chronodocsid='.$chronodocs->id;
	$head[$h][1] = $langs->trans('Documents');
	$head[$h][2] = 'document';
	$h++;
	
	$head[$h][0] = DOL_URL_ROOT.'/chronodocs/info.php?id='.$chronodocs->id;
	$head[$h][1] = $langs->trans('Info');
	$head[$h][2] = 'info';
	$h++;
  
  return $head;
}

function chronodocs_types_prepare_head($chronodocs_types)
{
  global $langs, $conf, $user;
  $langs->load("chronodocs");
  
  $h = 0;
  $head = array();
  
  $head[$h][0] = DOL_URL_ROOT.'/chronodocs/types.php?id='.$chronodocs_types->id;
  $head[$h][1] = $langs->trans("Card");
  $head[$h][2] = 'card';
  $h++;
  
  $head[$h][0] = DOL_URL_ROOT.'/chronodocs/types.php?id='.$chronodocs_types->id.'&action=info';
  $head[$h][1] = $langs->trans("Infos");
  $head[$h][2] = 'infos';
  $h++;
  
  $head[$h][0] = DOL_URL_ROOT.'/chronodocs/types.php';
  $head[$h][1] = $langs->trans("Liste");
  $head[$h][2] = 'Liste';
  $h++;

  $head[$h][0] = DOL_URL_ROOT.'/chronodocs/types.php?action=create';
  $head[$h][1] = $langs->trans("Create");
  $head[$h][2] = 'Create';
  $h++;  
  
  return $head;
}

/**
 *	\brief      Preparation d'une fiche de synthese pour sortie html
 *	\param     chronodocs fetched object used
 *	\return     string      Fiche formatee en html
 */
function chronodocs_prepare_synthese($chronodocs)
{
global $langs, $conf, $user;

	$chronodocs->fetch_client();
	$chronodocs->fetch_chronodocs_type();

	$strReturn=""; // Init
	
	$strReturn.= '<table class="border" width="100%">';

	// Ref
	$strReturn.= '<tr><td width="25%">'.$langs->trans("Ref").'</td><td>'.$chronodocs->ref.'</td></tr>';
	
	// Societe
	$strReturn.= "<tr><td>".$langs->trans("Company")."</td><td>".$chronodocs->client->getNomUrl(1)."</td></tr>";
	
	// Type
	$strReturn.= "<tr><td>".$langs->trans("ChronodocType")."</td><td>".$chronodocs->chronodocs_type->getNomUrl(1)."</td></tr>";

	// Date
	$strReturn.= '<tr><td>';
	$strReturn.= $langs->trans('Date');
	$strReturn.= '</td><td colspan="3">';
	$strReturn.= dolibarr_print_date($chronodocs->date_c,'%a %d %B %Y');
	$strReturn.= '</td>';
	$strReturn.= '</tr>';
	
	// Titre 
	$strReturn.= '<tr>';
	$strReturn.= "<td>".$langs->trans("Title")."</td>";
	$strReturn.= "<td>".$chronodocs->title."</td>";
	$strReturn.= "</tr>\n";
	
	// Description
	$strReturn.= '<tr><td>';
	$strReturn.= $langs->trans('Description');
	$strReturn.= '</td>';
	$strReturn.= '</td><td colspan="3">';
	$strReturn.= nl2br($chronodocs->brief);
	$strReturn.= '</td>';
	$strReturn.= '</tr>';

	// Statut
	//$strReturn.= '<tr><td>'.$langs->trans("Status").'</td><td>'.$chronodocs->getLibStatut(4).'</td></tr>';

	$strReturn.= "</table><br>";


	$strReturn.= '</div>';
	$strReturn.= "\n";
	
	return $strReturn;
}


	function chronodocs_get_tab_replace($chronodoc,$outputlangs='')
	{
		global $conf,$langs,$mysoc;
		dolibarr_syslog("chronodocs.lib.php"."::_get_tab_replace ");
		
		// Definition variables de remplacement
		$tab_replace = array();
		
		//recherche contacts
		$contacts_internes = $chronodoc->liste_contact(-1,'internal');
		$contacts_externes = $chronodoc->liste_contact(-1,'external');
		
		//recuperations infos sur chronodoc 
		$chronodoc->info();
		
		//recherche id contact redacteur
		$id_contact_redacteur = 0;
		for($i=0;($i<sizeOf($contacts_internes) && $id_contact_redacteur == 0);$i++)
		{
			if($contacts_internes[$i]['code']=='AUTHOR')
				$id_contact_redacteur=$contacts_internes[$i]['id'];
			
		}
		
		//recuperation contact redacteur
		if($id_contact_redacteur>0 && $chronodoc->fetch_user($id_contact_redacteur))
			$user_redacteur=$chronodoc->user;
		
		//recherche id contact destinataire
		$id_contact_dest = 0;
		for($i=0;($i<sizeOf($contacts_externes) && $id_contact_dest == 0);$i++)
		{
			if($contacts_externes[$i]['code']=='CUSTOMER')
				$id_contact_dest=$contacts_externes[$i]['id'];
		}
		
		//recuperation contact destinataire
		if($id_contact_dest>0 && $chronodoc->fetch_contact($id_contact_dest))
			$contact_dest=$chronodoc->contact;
		
			
		//recuperation donnees societe client
		require_once(DOL_DOCUMENT_ROOT."/societe/societe.class.php");
		$chronodoc->fetch_client();
		
		// Recherche valeurs de remplacement à utiliser via proprietes du chronodoc
		$nb_propvals=$chronodoc->fetch_chronodocs_propvalues();
		if($nb_propvals > 0) // OK et au moins 1 prop
		{
			for($i=0;$i<$nb_propvals;$i++)
			{
				if(!empty($chronodoc->propvalues[$i]->ref))
				{
					// Recuperation variables de remplacement saisies
					if(ereg("#P_([^#]+)#",$chronodoc->propvalues[$i]->ref,$reg_proptag))
					{
						$desc_key=$reg_proptag[0];
						$tab_replace[$desc_key]=$chronodoc->propvalues[$i]->content;
						//dolibarr_syslog("chronodocs.lib.php"."::get_tab_replace $desc_key ".$chronodoc->propvalues[$i]->content);
					}
					
					// Recuperation variables de remplacement à valeur auto
					if(ereg("#R_([^#]+)#",$chronodoc->propvalues[$i]->ref,$reg_proptag))
					{
						$desc_key=$reg_proptag[0];
						$tab_replace[$desc_key]="UNDEFINED";
						//dolibarr_syslog("chronodocs.lib.php"."::get_tab_replace $desc_key ".$chronodoc->propvalues[$i]->content);
					}
					
					/* DESACTIVE CAR NON UTILISE
					// Recuperation variables de remplacement à valeur récupérée sur client
					if(ereg("#V_(CLIENT_([a-zA-Z]+))#",$chronodoc->propvalues[$i]->ref,$reg_proptag))
					{
						$desc_key=$reg_proptag[0];
						$tab_replace[$desc_key]="UNDEFINED";
						$value_name=$reg_proptag[2];
						$tab_replace[$desc_key]=$chronodoc->client->$value_name;
						dolibarr_syslog("chronodocs.lib.php"."::_get_tab_replace $desc_key ".$chronodoc->propvalues[$i]->content);
					}
					*/
					
				}
			}	
		}
		
		
		// Recuperation valeurs variables de remplacement à valeur auto
		if(array_key_exists('#R_SOCIETE#',$tab_replace) && $tab_replace['#R_SOCIETE#'] == "UNDEFINED")
			$tab_replace['#R_SOCIETE#'] = $chronodoc->client->nom;
			
		if(array_key_exists('#R_SOC_ADR#',$tab_replace) && $tab_replace['#R_SOC_ADR#'] == "UNDEFINED")
			$tab_replace['#R_SOC_ADR#'] = nl2br($chronodoc->client->adresse);
		
		if(array_key_exists('#R_SOC_CP#',$tab_replace) && $tab_replace['#R_SOC_CP#'] == "UNDEFINED")
			$tab_replace['#R_SOC_CP#'] = $chronodoc->client->cp;
		
		if(array_key_exists('#R_SOC_VILLE#',$tab_replace) && $tab_replace['#R_SOC_VILLE#'] == "UNDEFINED")
			$tab_replace['#R_SOC_VILLE#'] = $chronodoc->client->ville;

		if(array_key_exists('#R_SOC_PAYS#',$tab_replace) && $tab_replace['#R_SOC_PAYS#'] == "UNDEFINED")
			$tab_replace['#R_SOC_PAYS#'] = $chronodoc->client->pays;
		
		if(array_key_exists('#R_TITRE_NOTE#',$tab_replace) && $tab_replace['#R_TITRE_NOTE#'] == "UNDEFINED")
			$tab_replace['#R_TITRE_NOTE#'] = $chronodoc->title;
		//$tab_replace['#R_TITRE_DOSSIER#'] = $chronodoc->ref;
			
		if (!empty($user_redacteur))
		{
			if(array_key_exists('#R_REDACTEUR#',$tab_replace) && $tab_replace['#R_REDACTEUR#'] == "UNDEFINED")
				$tab_replace['#R_REDACTEUR#'] = $user_redacteur->fullname;
				
			if(array_key_exists('#R_TRIGRAMME#',$tab_replace) && $tab_replace['#R_TRIGRAMME#'] == "UNDEFINED")
				$tab_replace['#R_TRIGRAMME#'] = chronodocs_get_R_TRIGRAMME($user_redacteur->nom,$user_redacteur->prenom);
				
			if(array_key_exists('#R_REDAC_NOM#',$tab_replace) && $tab_replace['#R_REDAC_NOM#'] == "UNDEFINED")
				$tab_replace['#R_REDAC_NOM#'] = $user_redacteur->nom;
			
			if(array_key_exists('#R_REDAC_PNOM#',$tab_replace) && $tab_replace['#R_REDAC_PNOM#'] == "UNDEFINED")
				$tab_replace['#R_REDAC_PNOM#'] = $user_redacteur->prenom;	
			
			//if(array_key_exists('#R_REDAC_CIV#',$tab_replace) && $tab_replace['#R_REDAC_CIV#'] == "UNDEFINED")
			//	$tab_replace['#R_REDAC_CIV#'] = $contact_redacteur->getCivilityLabel();
				
			if(array_key_exists('#R_REDAC_EMAIL#',$tab_replace) && $tab_replace['#R_REDAC_EMAIL#'] == "UNDEFINED")
				$tab_replace['#R_REDAC_EMAIL#'] = $user_redacteur->email;	
		}
		else
		{
			if(array_key_exists('#R_REDACTEUR#',$tab_replace) && $tab_replace['#R_REDACTEUR#'] == "UNDEFINED")
				$tab_replace['#R_REDACTEUR#'] = $chronodoc->user_creation->fullname;
				
			if(array_key_exists('#R_TRIGRAMME#',$tab_replace) && $tab_replace['#R_TRIGRAMME#'] == "UNDEFINED")
				$tab_replace['#R_TRIGRAMME#'] = chronodocs_get_R_TRIGRAMME($chronodoc->user_creation->nom,$chronodoc->user_creation->prenom);
				
			if(array_key_exists('#R_REDAC_NOM#',$tab_replace) && $tab_replace['#R_REDAC_NOM#'] == "UNDEFINED")
				$tab_replace['#R_REDAC_NOM#'] = $chronodoc->user_creation->nom;

			if(array_key_exists('#R_REDAC_PNOM#',$tab_replace) && $tab_replace['#R_REDAC_PNOM#'] == "UNDEFINED")
				$tab_replace['#R_REDAC_PNOM#'] = $chronodoc->user_creation->prenom;
				
			if(array_key_exists('#R_REDAC_EMAIL#',$tab_replace) && $tab_replace['#R_REDAC_EMAIL#'] == "UNDEFINED")
				$tab_replace['#R_REDAC_EMAIL#'] = $chronodoc->user_creation->email;
		}
		
		if (!empty($contact_dest))
		{
			if(array_key_exists('#R_CUSTOMER#',$tab_replace) && $tab_replace['#R_CUSTOMER#'] == "UNDEFINED")
				$tab_replace['#R_CUSTOMER#'] = $contact_dest->getFullName($langs,1,1);
				
			if(array_key_exists('#R_CUST_NOM#',$tab_replace) && $tab_replace['#R_CUST_NOM#'] == "UNDEFINED")
				$tab_replace['#R_CUST_NOM#'] = $contact_dest->name;
			
			if(array_key_exists('#R_CUST_PNOM#',$tab_replace) && $tab_replace['#R_CUST_PNOM#'] == "UNDEFINED")
				$tab_replace['#R_CUST_PNOM#'] = $contact_dest->firstname;	
			
			if(array_key_exists('#R_CUST_CIV#',$tab_replace) && $tab_replace['#R_CUST_CIV#'] == "UNDEFINED")
				$tab_replace['#R_CUST_CIV#'] = $contact_dest->getCivilityLabel();
				
			if(array_key_exists('#R_CUST_EMAIL#',$tab_replace) && $tab_replace['#R_CUST_EMAIL#'] == "UNDEFINED")
				$tab_replace['#R_CUST_EMAIL#'] = $contact_dest->email;	
		}
		
		if(array_key_exists('#R_DT_REDAC#',$tab_replace) && $tab_replace['#R_DT_REDAC#'] == "UNDEFINED")
			$tab_replace['#R_DT_REDAC#'] = dolibarr_print_date($chronodoc->date_c,"day") ;
			
		if(array_key_exists('#R_NUM_CHRONO#',$tab_replace) && $tab_replace['#R_NUM_CHRONO#'] == "UNDEFINED")
			$tab_replace['#R_NUM_CHRONO#'] = $chronodoc->ref;
		
		// Recherche autres valeurs de remplacement en regardant dans la description du type pour definition des cles et du chronodoc pour les valeurs
		/* DESACTIVE CAR DEJA FAIT DIFFEREMMENT VIA PROPRIETES, mais conserve pour servir de modele si besoin

		$str_descriptors=$chronodoc->chronodocs_type->brief;
		$nbother_descriptors=0;
		while(ereg("#R_([^#]+)#",$str_descriptors,$reg_descriptors) && $nbother_descriptors<100)
		{
			$nbother_descriptors++;
			$desc_key=$reg_descriptors[0];
			 if(ereg("#R_".$reg_descriptors[1]."=([^#]+)#",$chronodoc->brief,$reg_desc_value))
			 {
				$tab_replace[$desc_key]=$reg_desc_value[1];
			 }
			$str_descriptors=str_replace($reg_descriptors[0],"",$str_descriptors);
			dolibarr_syslog("chronodocs.lib.php"."::_get_tab_replace $desc_key ".$reg_desc_value[1]);
		}
		*/
		
		return 	$tab_replace;
	}

	function chronodocs_get_R_TRIGRAMME($nom,$prenom)
	{		
		$trigramme=substr(trim($prenom),0,1);
		$trigramme.=substr(trim($nom),0,1);
		$trigramme.=substr(trim($nom),-1,1);
		return $trigramme;
	}

?>
