<?php
/* Copyright (C) 2002-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2007 Regis Houssin        <regis@dolibarr.fr>
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
 \file       htdocs/chronodocs/fiche.php
 \brief      Fichier fiche chronodoc
 \ingroup    chronodocs
 \version    $Id: fiche.php,v 1.6 2010/08/19 15:25:25 hregis Exp $
 */

require("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_entries.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_types.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_propfields.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_propvalues.class.php");
require_once(DOL_DOCUMENT_ROOT."/includes/modules/chronodocs/modules_chronodocs.php");
require_once(DOL_DOCUMENT_ROOT."/lib/chronodocs.lib.php");
require_once(DOL_DOCUMENT_ROOT."/lib/date.lib.php");


if (defined("CHRONODOCS_ADDON") && is_readable(DOL_DOCUMENT_ROOT ."/includes/modules/chronodocs/mod_chronodocs_".CHRONODOCS_ADDON.".php"))
{
	require_once(DOL_DOCUMENT_ROOT ."/includes/modules/chronodocs/mod_chronodocs_".CHRONODOCS_ADDON.".php");
}

$langs->load("companies");
$langs->load("chronodocs");

// Get parameters
$chronodocsid = isset($_GET["id"])?$_GET["id"]:'';

// If socid provided by ajax company selector
if (! empty($_POST['socid_id']))
{
	$_POST['socid'] = $_POST['socid_id'];
	$_REQUEST['socid'] = $_REQUEST['socid_id'];
}

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'chronodocs', $chronodocsid, 'chronodocs_entries','entries','socid');

if ((in_array($_POST["action"],array('create','add','update','builddoc'))
	 || eregi("(set.*)|(edit.*)",$_POST["action"])
	 )&& !$user->rights->chronodocs->entries->write
	) accessforbidden();

if (in_array($_POST["action"],array('delete'))
	&& !$user->rights->chronodocs->entries->delete
	) accessforbidden();


/*
 * Traitements des actions
 */
if ($_REQUEST["action"] != 'create' && $_REQUEST["action"] != 'add' && ! $_REQUEST["id"] > 0)
{
	Header("Location: index.php");
	return;
}

if ($_POST["action"] == 'add')
{
	$chronodocs = new Chronodocs_entries($db);

	$chronodocs->ref = $_POST["ref"];
	$chronodocs->title = $_POST["title"];
	$chronodocs->brief = $_POST["brief"];
	$chronodocs->date_c = dolibarr_mktime($_POST["phour"], $_POST["pmin"] , $_POST["psec"], $_POST["pmonth"], $_POST["pday"], $_POST["pyear"]);
	$chronodocs->date_u = time();
	$chronodocs->fk_chronodocs_type = $_POST["chronodocs_type"];
	$chronodocs->socid = $_POST["socid"];
	$chronodocs->fk_status = '0';
	$chronodocs->fk_user_c = $user->id;
	$chronodocs->fk_user_u = $user->id;

	if ($chronodocs->socid > 0)
	{
		if(empty($chronodocs->ref))
		{//Get new reference before create
			$chronodocs->fetch_thirdparty();
			$chronodocs->fetch_chronodocs_type();

			if (! $conf->global->CHRONODOCS_ADDON)
			{
				dolibarr_print_error($db,$langs->trans("Error")." ".$langs->trans("Error_CHRONODOCS_ADDON_NotDefined"));
				exit;
			}

			$obj = $conf->global->CHRONODOCS_ADDON;
			$obj = "mod_chronodocs_".$obj;
			$modChronodocs = new $obj;
			$chronodocs->ref = $modChronodocs->getNextValue($chronodocs->client,$chronodocs,$chronodocs->chronodocs_type->ref);
		}

		$result = $chronodocs->create($user);
		if ($result > 0)
		{
			$_GET["id"]=$result;      // Force raffraichissement sur fiche venant d'etre creee
			$chronodocsid=$result;

		    $chronodocs->add_contact($user->id,"AUTHOR", "internal"); //Auto set author contact
		}
		else
		{
			$mesg='<div class="error">'.$chronodocs->error.'</div>';
			$_GET["action"] = 'create';
		}
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("ErrorFieldRequired",$langs->trans("ThirdParty")).'</div>';
		$_GET["action"] = 'create';
	}
}

if ($_POST["action"] == 'update')
{
	$chronodocs = new Chronodocs_entries($db);
	$chronodocs->fetch($_POST["id"]); //get old chronodoc

	$chronodocs_oldref=$chronodocs->ref;
	$chronodocs_oldtype=$chronodocs->fk_chronodocs_type;

	$chronodocs->ref = $_POST["ref"];
	$chronodocs->title = $_POST["title"];
	$chronodocs->brief = $_POST["brief"];
	$chronodocs->date_c = dolibarr_mktime($_POST["phour"], $_POST["pmin"] , $_POST["psec"], $_POST["pmonth"], $_POST["pday"], $_POST["pyear"]);
	$chronodocs->date_u = time();
	$chronodocs->fk_chronodocs_type = $_POST["chronodocs_type"];
	$chronodocs->socid = $_POST["socid"];
	//$chronodocs->fk_status = 0;
	//$chronodocs->fk_user_c = $user->id;
	$chronodocs->fk_user_u = $user->id;

	if(empty($chronodocs->ref) ||($chronodocs_oldref==$chronodocs->ref && $chronodocs_oldtype!=$chronodocs->fk_chronodocs_type))
	{//Get new reference before update
		$chronodocs->fetch_thirdparty();
		$chronodocs->fetch_chronodocs_type();

		$chronodocs->set_ref($user,'REGENERATED'); // Erase old ref (to avoid increasing counter if updating last created ref)

		if (! $conf->global->CHRONODOCS_ADDON)
		{
			dolibarr_print_error($db,$langs->trans("Error")." ".$langs->trans("Error_CHRONODOCS_ADDON_NotDefined"));
			exit;
		}

		$obj = $conf->global->CHRONODOCS_ADDON;
		$obj = "mod_chronodocs_".$obj;
		$modChronodocs = new $obj;
		$chronodocs->ref = $modChronodocs->getNextValue($chronodocs->client,$chronodocs,$chronodocs->chronodocs_type->ref);
	}

	$chronodocs->update($user);

	if(!empty($_POST['new_propvalues']) && is_array($_POST['new_propvalues'])
	&& !empty($_POST['id_propvalues']) && is_array($_POST['id_propvalues']) )
	{
		$chronodocs_propvalues=new Chronodocs_propvalues($db);
		$chronodocs_propfields=new Chronodocs_propfields($db);

		foreach ($_POST['new_propvalues'] as $propid => $new_content)
		{
			if($propid > 0)
			{
				if(!empty($_POST['id_propvalues'][$propid]))
					$valid=$_POST['id_propvalues'][$propid];
				else
					$valid=0;

				if($valid >0)
				{
					$result=$chronodocs_propvalues->fetch($valid);
					if($result>0 && ($chronodocs_propvalues->fk_objectid == $chronodocs->id ) && ($chronodocs_propvalues->fk_propfield == $propid ))
					{
						$chronodocs_propvalues->content = $new_content;
						$result=$chronodocs_propvalues->update($user);
					}
				}
				else
				{
					$chronodocs_propfields->fetch($propid);
					if($result>0 && ($chronodocs_propfields->fk_type == $chronodocs->fk_chronodocs_type ) )
					{
						$chronodocs_propvalues->initAsSpecimen();
						$chronodocs_propvalues->fk_objectid = $chronodocs->id;
						$chronodocs_propvalues->fk_propfield = $propid;
						$chronodocs_propvalues->content = $new_content;
						$result=$chronodocs_propvalues->create($user);
					}
					else if($result<0)
					{
						dolibarr_print_error($db,$chronodocs_propfields->error);
						$result=0;
					}
				}

				if($result<0)
				{
					dolibarr_print_error($db,$chronodocs_propvalues->error);
				}
			}
		}
	}

	if($chronodocs_oldref!=$chronodocs->ref)
	{
		$olddir=DOL_DATA_ROOT."/chronodocs/" . dol_string_nospecial($chronodocs_oldref);
		$newdir=DOL_DATA_ROOT."/chronodocs/" . dol_string_nospecial($chronodocs->ref);
		rename($olddir,$newdir);
	}
	$_GET["id"]=$_POST["id"];      // Force raffraichissement sur fiche venant d'etre mise a jour
}
/*
Mise a jour de la description
*/
if ($_POST['action'] == 'setbrief')
{
	$chronodocs = new Chronodocs_entries($db);
	$chronodocs->fetch($_GET['id']);
	$result=$chronodocs->set_brief($user,$_POST['brief']);
	if ($result < 0) dolibarr_print_error($db,$chronodocs->error);
}

/*
Mise a jour de la date de creation
*/
if ($_POST['action'] == 'set_date_c')
{
	$chronodocs = new Chronodocs_entries($db);
	$chronodocs->fetch($_GET['id']);
	$result=$chronodocs->set_date_c($user,dolibarr_mktime(12, 0, 0, $_POST['date_c_month'], $_POST['date_c_day'], $_POST['date_c_year']));
	if ($result < 0) dolibarr_print_error($db,$chronodocs->error);
}

/*
Mise a jour du titre
*/
if ($_POST['action'] == 'set_title')
{
	$chronodocs = new Chronodocs_entries($db);
	$chronodocs->fetch($_GET['id']);
	$result=$chronodocs->set_title($user,$_POST['title']);
	if ($result < 0) dolibarr_print_error($db,$chronodocs->error);
}

/*
 * Generer ou regenerer le document
 */
if ($_REQUEST['action'] == 'builddoc')	// En get ou en post
{
	$chronodocs = new Chronodocs_entries($db);
	$chronodocs->fetch($_GET['id']);
    //$chronodocs->fetch_lines();

	if ($_REQUEST['lang_id'])
	{
		$outputlangs = new Translate("",$conf);
		$outputlangs->setDefaultLang($_REQUEST['lang_id']);
	}

	$result=chronodocs_create($db, $chronodocs, $_REQUEST['model'], $outputlangs);
	if ($result <= 0)
	{
		dolibarr_print_error($db,$result);
		exit;
	}
}


if(($_POST["action"] == 'confirm_delete' AND $_POST["confirm"] == 'yes') && ($user->rights->chronodocs->types->delete))
{
	$chronodocs = new Chronodocs_entries($db);
	$chronodocs->fetch($_GET['id']);
	if (!empty($chronodocs->id))
	{
		$result = $chronodocs->delete($user);
		if ($result < 0)
			dolibarr_print_error($db,$chronodocs->error);
		else
		{
			Header('Location: index.php');
			exit;
		}
	}
}



/*
 * View
 */

$html = new Form($db);

llxHeader();
if ($_GET["id"] > 0 && $_GET["action"] != 'create' )
{
	/*
	 * Affichage en mode visu
	 */
	$chronodocs = new Chronodocs_entries($db);
	$result=$chronodocs->fetch($_GET["id"]);
	if (! $result > 0)
	{
		dolibarr_print_error($db);
		exit;
	}
	$chronodocs->fetch_thirdparty();
	$chronodocs->fetch_chronodocs_type();

	if ($mesg) print $mesg."<br>";

	$head = chronodocs_prepare_head($chronodocs);

	dolibarr_fiche_head($head, 'card', $langs->trans("Chronodoc"));

	/*
	 * Confirmation de la suppression de la fiche chronodoc
	 */
	if ($_GET['action'] == 'delete')
	{
		$html->form_confirm($_SERVER["PHP_SELF"].'?id='.$chronodocs->id, $langs->trans('DeleteChronodoc'), $langs->trans('ConfirmDeleteChronodoc'), 'confirm_delete');
		print '<br>';
	}


	print '<table class="border" width="100%">';

	// Ref
	print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td>'.$chronodocs->ref.'</td></tr>';

	// Societe
	print "<tr><td>".$langs->trans("Company")."</td><td>".$chronodocs->client->getNomUrl(1)."</td></tr>";

	// Type
	if ($user->rights->chronodocs->types->read)
		print "<tr><td>".$langs->trans("ChronodocType")."</td><td>".$chronodocs->chronodocs_type->getNomUrl(1)."</td></tr>";
	else
		print "<tr><td>".$langs->trans("ChronodocType")."</td><td>".$chronodocs->chronodocs_type->title."</td></tr>";

	// Date
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td>';
	print $langs->trans('Date');
	print '</td>';
	if ($_GET['action'] != 'edit_date_c') print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=edit_date_c&amp;id='.$chronodocs->id.'">'.img_edit($langs->trans('SetDateCreate'),1).'</a></td>';
	print '</tr></table>';
	print '</td><td colspan="3">';
	if ($_GET['action'] == 'edit_date_c')
	{
		print '<form name="edit_date_c" action="'.$_SERVER["PHP_SELF"].'?id='.$chronodocs->id.'" method="post">';
		print '<input type="hidden" name="action" value="set_date_c">';
		$html->select_date($chronodocs->date_c,'date_c_','','','',"edit_date_c");
		print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
		print '</form>';
	}
	else
	{
		print dolibarr_print_date($chronodocs->date_c,'%a %d %B %Y');
	}
	print '</td>';
	print '</tr>';

	// Titre
	print '<tr>';
	if ($_GET['action'] == 'edit_title')
	{
		print "<td>".$langs->trans("Title")."</td>";
		print '<td><form name="edit_title" action="'.$_SERVER["PHP_SELF"].'?id='.$chronodocs->id.'" method="post">';
		print '<input type="hidden" name="action" value="set_title">';
		print "<input name=\"title\" value=\"".$chronodocs->title."\" size=\"74\">";
		print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
		print "</form></td>";
	}
	else
	{
		print '<td><table class="nobordernopadding" width="100%"><tr><td>';
		print "<td>".$langs->trans("Title")."</td>";
		print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=edit_title&amp;id='.$chronodocs->id.'">'.img_edit($langs->trans('SetTitle'),1).'</a></td>';
		print '</tr></table></td>';
		print "<td>".$chronodocs->title."</td>";
	}
	print "</tr>\n";

	// Description
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td>';
	print $langs->trans('Description');
	print '</td>';
	if ($_GET['action'] != 'edit_brief') print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=edit_brief&amp;id='.$chronodocs->id.'">'.img_edit($langs->trans('SetDescription'),1).'</a></td>';
	print '</tr></table>';
	print '</td><td colspan="3">';
	if ($_GET['action'] == 'edit_brief')
	{
		print '<form name="edit_brief" action="'.$_SERVER["PHP_SELF"].'?id='.$chronodocs->id.'" method="post">';
		print '<input type="hidden" name="action" value="setbrief">';
		if ($conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_SOCIETE)
		{
			// Editeur wysiwyg
			require_once(DOL_DOCUMENT_ROOT."/lib/doleditor.class.php");
			$doleditor=new DolEditor('brief',$chronodocs->brief,280,'dolibarr_notes','In',true);
			$doleditor->Create();
		}
		else
		{
			print '<textarea name="brief" wrap="soft" cols="70" rows="12">'.dol_htmlentitiesbr_decode($chronodocs->brief).'</textarea>';
		}
		print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
		print '</form>';
	}
	else
	{
		print nl2br($chronodocs->brief);
	}
	print '</td>';
	print '</tr>';

	// Statut
	//print '<tr><td>'.$langs->trans("Status").'</td><td>'.$chronodocs->getLibStatut(4).'</td></tr>';

	print "</table><br>";

	//PROPRIETES supplementaires
	print $langs->trans("OtherPropfields")." :<br>";
	print '<table class="border" width="100%">';
	$nb_propvals=$chronodocs->fetch_chronodocs_propvalues();
	if($nb_propvals > 0) // OK et au moins 1 prop
	{
		$auto_propfields_index=array();
		for($i=0;$i<$nb_propvals;$i++)
		{
			if(!ereg("#R|V_([^#]+)#",$chronodocs->propvalues[$i]->ref))
			{
				print "<tr><td width=\"25%\">".$chronodocs->propvalues[$i]->title."</td>";
				print "<td>".nl2br($chronodocs->propvalues[$i]->content)."</td></tr>\n";
			}
			else
				$auto_propfields_index[]=$i;
		}
		if(!empty($auto_propfields_index) && !empty($chronodocs->id) ) // au moins 1 prop auto et chronodoc ok
		{
			$tab_replace=chronodocs_get_tab_replace($chronodocs);
			foreach ($auto_propfields_index as $key => $value)
			{
				$i=$value;
				if(ereg("#R|V_([^#]+)#",$chronodocs->propvalues[$i]->ref))
				{
					print "<tr><td>AUTO: ".$chronodocs->propvalues[$i]->title."</td>";
					print "<td>".$chronodocs->propvalues[$i]->ref." => ".$tab_replace[$chronodocs->propvalues[$i]->ref]."</td></tr>\n";
				}
			}
		}
	}
	//fin  PROPRIETES supplementaires

	print "</table><br>";

		/*
		 * Documents generes
		 */
		require_once(DOL_DOCUMENT_ROOT."/includes/modules/chronodocs/files.class.php");
		$formfile=new ChronodocsFiles($db);

		$filename=dol_string_nospecial($chronodocs->ref);
		$filedir=$formfile->dir_output . "/" . dol_string_nospecial($chronodocs->ref);
		$urlsource=$_SERVER["PHP_SELF"]."?id=".$chronodocs->id;
		$genallowed=$user->rights->chronodocs->entries->write;
		$delallowed=$user->rights->chronodocs->entries->delete;

		$var=true;
		$somethingshown=$formfile->show_documents('chronodocs_entries',$filename,$filedir,$urlsource,$genallowed,$delallowed);

	print '</div>';
	print "\n";


	/**
	 * Barre d'actions
	 *
	 */
	print '<div class="tabsAction">';

	if ($user->societe_id == 0 && $_GET["action"] != 'editAll')
	{
		// Edit
		if ($chronodocs->statut == 0 && $user->rights->chronodocs->entries->write)
		{
			print '<a class="butActionDelete" ';
			print 'href="'.$_SERVER["PHP_SELF"].'?id='.$chronodocs->id.'&amp;action=editAll"';
			print '>'.$langs->trans('Modify').'</a>';
		}

		// Delete
		if ($chronodocs->statut == 0 && $user->rights->chronodocs->entries->delete)
		{
			print '<a class="butActionDelete" href="'.$_SERVER["PHP_SELF"].'?id='.$chronodocs->id.'&amp;action=delete"';
			print '>'.$langs->trans('Delete').'</a>';
		}
	}

	print '</div>';


}

if ($_GET["action"] == 'create' || $_GET["action"] == 'editAll')
{
	/*
	 * Mode creation ou edition fiche chronodoc
	 */
	if ($_GET["action"] == 'create')
	{
		print_titre($langs->trans("AddChronodoc"));
	}
	else
	{
		print_titre($langs->trans("EditChronodoc"));
	}
	if ($mesg) print $mesg.'<br>';

	$chronodocs = new Chronodocs_entries($db);
	$chronodocs->date_c = time();
	if ($chronodocsid) 	$result=$chronodocs->fetch($chronodocsid);

	if ($_GET["action"] == 'editAll' && !empty($chronodocs->socid))
	{
		$_GET["socid"]=$chronodocs->socid; //get socid from chronodoc instead of url
	}

	if ($_GET["socid"] > 0)
	{
			$societe=new Societe($db);
			$societe->fetch($_GET["socid"]);
	}

	if ($_GET["chronodocs_type"] > 0 && $_GET["action"] == 'create')
	{
		$chronodocs->fk_chronodocs_type=$_GET["chronodocs_type"]; //get given chronotypeid
	}

	/* This is done during validation
	if (! $conf->global->CHRONODOCS_ADDON)
	{
		dolibarr_print_error($db,$langs->trans("Error")." ".$langs->trans("Error_CHRONODOCS_ADDON_NotDefined"));
		exit;
	}
	//get new ref
	$obj = $conf->global->CHRONODOCS_ADDON;
	$obj = "mod_chronodocs_".$obj;
	$modChronodocs = new $obj;
	$numpr = $modChronodocs->getNextValue($societe,$chronodocs);
	$chronodocs->ref=$numpr;
	*/

	if ($_GET["socid"] && !empty($chronodocs->fk_chronodocs_type))
	{
		$chronodocs->fetch_chronodocs_type();

		print "<form name='chronodocs' action=\"fiche.php\" method=\"post\">";
		if ($_GET["action"] == 'create')
			{
				print "<input type=\"hidden\" name=\"action\" value=\"add\">";
			}
		else
			{
				print "<input type=\"hidden\" name=\"action\" value=\"update\">";
				print "<input type=\"hidden\" name=\"id\" value=\"$chronodocsid\">";
			}
		print '<table class="border" width="100%">';

		// Ref
		print "<tr><td>".$langs->trans("Ref")."</td>";
		print "<td><input name=\"ref\" value=\"".$chronodocs->ref."\" size=\"32\"></td></tr>\n";

		// Societe
		print '<input type="hidden" name="socid" value='.$_GET["socid"].'>';
		print "<tr><td>".$langs->trans("Company")."</td><td>".$societe->getNomUrl(1)."</td></tr>";

		// Type
		print '<tr>';
		print '<td>'.$langs->trans("ChronodocType").'</td>';
		print '<td>';
		//$chronodocType=new Chronodocs_types($db); //Not editable since chronodocs propfields
		//$liste=$chronodocType->liste_types($db);
		//$html->selectarray('chronodocs_type',$liste,$chronodocs->fk_chronodocs_type);
		print $chronodocs->chronodocs_type->getNomUrl(1);
		print '<input type="hidden" name="chronodocs_type" value='.$chronodocs->fk_chronodocs_type.'>';
		print "</td></tr>";

		// Date
		print "<tr><td>".$langs->trans("Date")."</td><td>";
		$html->select_date($chronodocs->date_c,"p",'','','','chronodocs');
		print "</td></tr>";

		// Titre
		print "<tr><td>".$langs->trans("Title")."</td>";
		print "<td><input name=\"title\" size=\"74\" value=\"".$chronodocs->title."\"></td></tr>\n";

		// Description
		print '<tr><td valign="top">'.$langs->trans("Description").'</td>';
		print "<td>";
		if ($conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_SOCIETE)
		{
			// Editeur wysiwyg
			require_once(DOL_DOCUMENT_ROOT."/lib/doleditor.class.php");
			$doleditor=new DolEditor('brief',$chronodocs->brief,280,'dolibarr_notes','In',true);
			$doleditor->Create();
		}
		else
		{
			print '<textarea name="brief" wrap="soft" cols="70" rows="12">'.$chronodocs->brief.'</textarea>';
		}

		print '</td></tr>';

		//PROPRIETES supplementaires
		$nb_propvals=$chronodocs->fetch_chronodocs_propvalues();
		if($nb_propvals > 0) // OK et au moins 1 prop
		{
			$auto_propfields_index=array();
			for($i=0;$i<$nb_propvals;$i++)
			{
				if(!ereg("#R|V_([^#]+)#",$chronodocs->propvalues[$i]->ref))
				{
					print "<tr><td>".$chronodocs->propvalues[$i]->title."</td>";
					print "<td><input name=\"new_propvalues[".$chronodocs->propvalues[$i]->propid."]\" size=\"74\" value=\"".$chronodocs->propvalues[$i]->content."\"><input name=\"id_propvalues[".$chronodocs->propvalues[$i]->propid."]\" type=\"hidden\" value=\"".$chronodocs->propvalues[$i]->valid."\">"."</td></tr>\n";
				}
				else
					$auto_propfields_index[]=$i;
			}
		}
		//fin  PROPRIETES supplementaires

		print '<tr><td colspan="2" align="center">';
		if ($_GET["action"] == 'create')
			print '<input type="submit" class="button" value="'.$langs->trans("Create").'">';
		else
			print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';

		print '</td></tr>';

		print '</table>';
		print '</form>';

	}
	else
	{
		print "<form name='chronodocs' action=\"fiche.php\" method=\"get\">";
		print '<table class="border" width="100%">';
		print "<tr><td>".$langs->trans("Company")."</td><td>";
		$html->select_societes($_GET['socid'],'socid','',0);
		print "</td></tr>";
		print "<tr><td>".$langs->trans("ChronodocType")."</td><td>";
		$chronodocType=new Chronodocs_types($db); //Not editable since chronodocs propfields
		$liste=$chronodocType->liste_types($db);
		$html->selectarray('chronodocs_type',$liste,$_GET['chronodocs_type']);
		print "</td></tr>";
		print '<tr><td colspan="2" align="center">';
		print "<input type=\"hidden\" name=\"action\" value=\"create\">";
		print '<input type="submit" class="button" value="'.$langs->trans("Create").'">';
		print '</td></tr>';
		print '</table>';
		print '</form>';
	}

}


$db->close();

llxFooter('$Date: 2010/08/19 15:25:25 $ - $Revision: 1.6 $');
?>
