<?php
/* Copyright (C) 2005-2007 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2007      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 */

/**
        \file       htdocs/chronodocs/contact.php
        \ingroup    chronodocs
        \brief      Onglet de gestion des contacts de chronodoc
        \version    $Id: contact.php,v 1.1 2008/09/10 09:34:55 raphael_bertrand Exp $
*/

require ("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_entries.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_types.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/chronodocs.lib.php");
require_once(DOL_DOCUMENT_ROOT."/contact.class.php");

$langs->load("chronodocs");
$langs->load("sendings");
$langs->load("companies");

$chronodocsid = isset($_GET["id"])?$_GET["id"]:'';

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'chronodocs', $chronodocsid, 'chronodocs_entries','entries');


if (( eregi("(add.*)|(upd.*)|(swap.*)|(del.*)|(set.*)|(edit.*)",$_POST["action"])
	 )&& !$user->rights->chronodocs->entries->write
	) accessforbidden();
	
/*
 * Ajout d'un nouveau contact
 */

if ($_POST["action"] == 'addcontact' && $user->rights->chronodocs->entries->write)
{
	
	$result = 0;
	$chronodocs = new Chronodocs_entries($db);
	$result = $chronodocs->fetch($_GET["id"]);

    if ($result > 0 && $_GET["id"] > 0)
    {
  		$result = $chronodocs->add_contact($_POST["contactid"], $_POST["type"], $_POST["source"]);
    }
    
	if ($result >= 0)
	{
		$chronodocs->set_date_u($user); // Update date_u and user_u
		Header("Location: contact.php?id=".$chronodocs->id);
		exit;
	}
	else
	{
		if ($chronodocs->error == 'DB_ERROR_RECORD_ALREADY_EXISTS')
		{
			$langs->load("errors");
			$mesg = '<div class="error">'.$langs->trans("ErrorThisContactIsAlreadyDefinedAsThisType").'</div>';
		}
		else
		{
			$mesg = '<div class="error">'.$chronodocs->error.'</div>';
		}
	}
}
// modification d'un contact. On enregistre le type
if ($_POST["action"] == 'updateligne' && $user->rights->chronodocs->entries->write)
{
	$chronodocs = new Chronodocs_entries($db);
	if ($chronodocs->fetch($_GET["id"]))
	{
		$contact = $chronodocs->detail_contact($_POST["elrowid"]);
		$type = $_POST["type"];
		$statut = $contact->statut;

		$result = $chronodocs->update_contact($_POST["elrowid"], $statut, $type);
		if ($result >= 0)
		{
			$db->commit();
			$chronodocs->set_date_u($user); // Update date_u and user_u
		} else
		{
			dolibarr_print_error($db, "result=$result");
			$db->rollback();
		}
	} else
	{
		dolibarr_print_error($db);
	}
}

// bascule du statut d'un contact
if ($_GET["action"] == 'swapstatut' && $user->rights->chronodocs->entries->write)
{
	$chronodocs = new Chronodocs_entries($db);
	if ($chronodocs->fetch($_GET["id"]))
	{
		$contact = $chronodocs->detail_contact($_GET["ligne"]);
		$id_type_contact = $contact->fk_c_type_contact;
		$statut = ($contact->statut == 4) ? 5 : 4;

		$result = $chronodocs->update_contact($_GET["ligne"], $statut, $id_type_contact);
		if ($result >= 0)
		{
			$db->commit();
			$chronodocs->set_date_u($user); // Update date_u and user_u
		} else
		{
			dolibarr_print_error($db, "result=$result");
			$db->rollback();
		}
	} else
	{
		dolibarr_print_error($db);
	}
}

// Efface un contact
if ($_GET["action"] == 'deleteline' && $user->rights->chronodocs->entries->write)
{
	$chronodocs = new Chronodocs_entries($db);
	$chronodocs->fetch($_GET["id"]);
	$result = $chronodocs->delete_contact($_GET["lineid"]);

	if ($result >= 0)
	{
		$chronodocs->set_date_u($user); // Update date_u and user_u
		Header("Location: contact.php?id=".$chronodocs->id);
		exit;
	}
	else {
		dolibarr_print_error($db);
	}
}


llxHeader();

$html = new Form($db);
$contactstatic=new Contact($db);


/* *************************************************************************** */
/*                                                                             */
/* Mode vue et edition                                                         */
/*                                                                             */
/* *************************************************************************** */
if (isset($mesg)) print $mesg;

$id = $_GET["id"];
if ($id > 0)
{
	$chronodocs = new Chronodocs_entries($db);
	if ($chronodocs->fetch($_GET['id']) > 0)
	{
		$soc = new Societe($db, $chronodocs->socid);
		$soc->fetch($chronodocs->socid);


		$head = chronodocs_prepare_head($chronodocs);
		dolibarr_fiche_head($head, 'contact', $langs->trans("Chronodoc"));


		/*
		*   Fiche chronodoc synthese pour rappel
		*/
		print chronodocs_prepare_synthese($chronodocs);

		print '</div>';

		/*
		* Lignes de contacts
		*/
		echo '<br><table class="noborder" width="100%">';

		/*
		* Ajouter une ligne de contact
		* Non affiché en mode modification de ligne
		*/
		if ($_GET["action"] != 'editline' && $user->rights->chronodocs->entries->write)
		{
			
			print '<tr class="liste_titre">';
			print '<td>'.$langs->trans("Source").'</td>';
			print '<td>'.$langs->trans("Company").'</td>';
			print '<td>'.$langs->trans("Contacts").'</td>';
			print '<td>'.$langs->trans("ContactType").'</td>';
			print '<td colspan="3">&nbsp;</td>';
			print "</tr>\n";

			$var = false;

			print '<form action="contact.php?id='.$id.'" method="post">';
			print '<input type="hidden" name="action" value="addcontact">';
			print '<input type="hidden" name="source" value="internal">';
			print '<input type="hidden" name="id" value="'.$id.'">';

			// Ligne ajout pour contact interne
			print "<tr $bc[$var]>";

			print '<td>';
			print $langs->trans("Internal");
			print '</td>';

			print '<td colspan="1">';
			print $conf->global->MAIN_INFO_SOCIETE_NOM;
			print '</td>';

			print '<td colspan="1">';
			// On récupère les id des users déjà sélectionnés
			//$userAlreadySelected = $chronodocs->getListContactId('internal'); 	// On ne doit pas desactiver un contact deja selectionner car on doit pouvoir le seclectionner une deuxieme fois pour un autre type
			$html->select_users($user->id,'contactid',0,$userAlreadySelected);
			print '</td>';
			print '<td>';
			$chronodocs->selectTypeContact($chronodocs, '', 'type','internal');
			print '</td>';
			print '<td align="right" colspan="3" ><input type="submit" class="button" value="'.$langs->trans("Add").'"></td>';
			print '</tr>';

			print '</form>';

			print '<form action="contact.php?id='.$id.'" method="post">';
			print '<input type="hidden" name="action" value="addcontact">';
			print '<input type="hidden" name="source" value="external">';
			print '<input type="hidden" name="id" value="'.$id.'">';

			// Ligne ajout pour contact externe
			$var=!$var;
			print "<tr $bc[$var]>";

			print '<td>';
			print $langs->trans("External");
			print '</td>';

			print '<td colspan="1">';
			$selectedCompany = isset($_GET["newcompany"])?$_GET["newcompany"]:$chronodocs->client->id;
			$selectedCompany = $chronodocs->selectCompaniesForNewContact($chronodocs, 'id', $selectedCompany, $htmlname = 'newcompany');
			print '</td>';

			print '<td colspan="1">';
			// On récupère les id des contacts déjà sélectionnés
			//$contactAlreadySelected = $chronodocs->getListContactId('external');	// On ne doit pas desactiver un contact deja selectionner car on doit pouvoir le seclectionner une deuxieme fois pour un autre type	
			$nbofcontacts=$html->select_contacts($selectedCompany, $selected = '', $htmlname = 'contactid',0,$contactAlreadySelected);
			if ($nbofcontacts == 0) print $langs->trans("NoContactDefined");
			print '</td>';
			print '<td>';
			$chronodocs->selectTypeContact($chronodocs, '', 'type','external');
			print '</td>';
			print '<td align="right" colspan="3" ><input type="submit" class="button" value="'.$langs->trans("Add").'"';
			if (! $nbofcontacts) print ' disabled="true"';
			print '></td>';
			print '</tr>';

			print "</form>";

			print '<tr><td colspan="6">&nbsp;</td></tr>';
		}

		// Liste des contacts liés
		print '<tr class="liste_titre">';
		print '<td>'.$langs->trans("Source").'</td>';
		print '<td>'.$langs->trans("Company").'</td>';
		print '<td>'.$langs->trans("Contacts").'</td>';
		print '<td>'.$langs->trans("ContactType").'</td>';
		print '<td align="center">'.$langs->trans("Status").'</td>';
		print '<td colspan="2">&nbsp;</td>';
		print "</tr>\n";

		$societe = new Societe($db);
		$var = true;

		foreach(array('internal','external') as $source)
		{
			$tab = $chronodocs->liste_contact(-1,$source);
			$num=sizeof($tab);

			$i = 0;
			while ($i < $num)
			{
				$var = !$var;

				print '<tr '.$bc[$var].' valign="top">';

				// Source
				print '<td align="left">';
				if ($tab[$i]['source']=='internal') print $langs->trans("Internal");
				if ($tab[$i]['source']=='external') print $langs->trans("External");
				print '</td>';

				// Societe
				print '<td align="left">';
				if ($tab[$i]['socid'] > 0)
				{
					print '<a href="'.DOL_URL_ROOT.'/soc.php?socid='.$tab[$i]['socid'].'">';
					print img_object($langs->trans("ShowCompany"),"company").' '.$societe->get_nom($tab[$i]['socid']);
					print '</a>';
				}
				if ($tab[$i]['socid'] < 0)
				{
					print $conf->global->MAIN_INFO_SOCIETE_NOM;
				}
				if (! $tab[$i]['socid'])
				{
					print '&nbsp;';
				}
				print '</td>';

				// Contact
				print '<td>';
				if ($tab[$i]['source']=='internal')
				{
					print '<a href="'.DOL_URL_ROOT.'/user/fiche.php?id='.$tab[$i]['id'].'">';
					print img_object($langs->trans("ShowUser"),"user").' '.$tab[$i]['nom'].'</a>';
				}
				if ($tab[$i]['source']=='external')
				{
					print '<a href="'.DOL_URL_ROOT.'/contact/fiche.php?id='.$tab[$i]['id'].'">';
					print img_object($langs->trans("ShowContact"),"contact").' '.$tab[$i]['nom'].'</a>';
				}
				print '</td>';

				// Type de contact
				print '<td>'.$tab[$i]['libelle'].'</td>';

				// Statut
				print '<td align="center">';
				// Activation desativation du contact
				if ($chronodocs->statut >= 0) print '<a href="contact.php?id='.$chronodocs->id.'&amp;action=swapstatut&amp;ligne='.$tab[$i]['rowid'].'">';
				print $contactstatic->LibStatut($tab[$i]['status'],3);
				if ($chronodocs->statut >= 0) print '</a>';
				print '</td>';

				// Icon update et delete
				print '<td align="center" nowrap>';
				if ($chronodocs->statut < 5 && $user->rights->chronodocs->entries->write)
				{
					print '&nbsp;';
					print '<a href="contact.php?id='.$chronodocs->id.'&amp;action=deleteline&amp;lineid='.$tab[$i]['rowid'].'">';
					print img_delete();
					print '</a>';
				}
				print '</td>';

				print "</tr>\n";

				$i ++;
			}
		}
		print "</table>";
	}
	else
	{
		// Fiche chronodoc non trouvée
		print "Fiche chronodoc inexistante ou accès refusé";
	}
}

$db->close();

llxFooter('$Date: 2008/09/10 09:34:55 $');
?>