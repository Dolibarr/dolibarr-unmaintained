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
 \file       htdocs/chronodocs/types.php
 \brief      Fichier fiche type de chronodoc
 \ingroup    chronodocs
 \version    $Id: types.php,v 1.4 2010/05/01 14:30:11 eldy Exp $
 */

require("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_types.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_propfields.class.php");
//require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_propvalues.class.php");
require_once(DOL_DOCUMENT_ROOT."/includes/modules/chronodocs/modules_chronodocs.php");
require_once(DOL_DOCUMENT_ROOT."/lib/chronodocs.lib.php");
//require_once(DOL_DOCUMENT_ROOT."/lib/date.lib.php");

/*
if (defined("CHRONODOCS_ADDON") && is_readable(DOL_DOCUMENT_ROOT ."/includes/modules/chronodocs/mod_chronodocs_".CHRONODOCS_ADDON.".php"))
{
	require_once(DOL_DOCUMENT_ROOT ."/includes/modules/chronodocs/mod_chronodocs_".CHRONODOCS_ADDON.".php");
}
*/

$langs->load("companies");
$langs->load("chronodocs");

$form = new Form($db);

// Get parameters
$typeid = isset($_GET["id"])?$_GET["id"]:'';

// If socid provided by ajax company selector
if (! empty($_POST['socid_id']))
{
	$_POST['socid'] = $_POST['socid_id'];
	$_REQUEST['socid'] = $_REQUEST['socid_id'];
}

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'chronodocs', 0, 'chronodocs_types','types');

if ((in_array($_POST["action"],array('create','add','update'))
	 || eregi("(set.*)|(edit.*)|(del_.*)",$_POST["action"])
	 )&& !$user->rights->chronodocs->types->write
	) accessforbidden();

if (in_array($_POST["action"],array('delete'))
	&& !$user->rights->chronodocs->types->delete
	) accessforbidden();


/*
 * Traitements des actions
 */
if (isset($_REQUEST["action"]) && $_REQUEST["action"] != 'create' && $_REQUEST["action"] != 'add' && ! $_REQUEST["id"] > 0)
{
	Header("Location: types.php");
	return;
}

if ($_POST["action"] == 'add')
{
	$chronodocstype = new Chronodocs_types($db);

	$chronodocstype->ref = $_POST["ref"];
	$chronodocstype->title = $_POST["title"];
	$chronodocstype->brief = $_POST["brief"];
	$chronodocstype->filename = $_POST["filename"];
	$chronodocstype->date_c = time();
	$chronodocstype->date_u = time();
	$chronodocstype->fk_status = '0';
	$chronodocstype->fk_user_c = $user->id;
	$chronodocstype->fk_user_u = $user->id;


	$result = $chronodocstype->create($user);
	if ($result > 0)
	{
		$_GET["id"]=$result;      // Force raffraichissement sur fiche venant d'etre creee
		$typeid=$result;
	}
	else
	{
		$mesg='<div class="error">'.$chronodocstype->error.'</div>';
		$_GET["action"] = 'create';
	}
}

if ($_POST["action"] == 'update')
{
	$chronodocstype = new Chronodocs_types($db);
	$chronodocstype->fetch($_POST["id"]); //get old chronodoc_type values
	$chronodocstype->ref = $_POST["ref"];
	$chronodocstype->title = $_POST["title"];
	$chronodocstype->brief = $_POST["brief"];
	$chronodocstype->date_u = time();
	$chronodocstype->fk_user_u = $user->id;

	if(!empty($chronodocstype->ref))
		$chronodocstype->update($user);

	$_GET["id"]=$_POST["id"];      // Force raffraichissement sur fiche venant d'etre mise a jour
}
/*
Mise a jour de la description
*/
if ($_POST['action'] == 'setbrief')
{
	$chronodocstype = new Chronodocs_types($db);
	$chronodocstype->fetch($_GET['id']);
	$result=$chronodocstype->set_brief($user,$_POST['brief']);
	if ($result < 0) dolibarr_print_error($db,$chronodocstype->error);
}


/*
Mise a jour du titre
*/
if ($_POST['action'] == 'set_title')
{
	$chronodocstype = new Chronodocs_types($db);
	$chronodocstype->fetch($_GET['id']);
	$result=$chronodocstype->set_title($user,$_POST['title']);
	if ($result < 0) dolibarr_print_error($db,$chronodocstype->error);
}

/*
Mise a jour du fichier
*/
if ($_POST["action"] == 'setfile')
{
	require_once DOL_DOCUMENT_ROOT.'/lib/files.lib.php';

	$chronodocstype = new Chronodocs_types($db);
	$chronodocstype->fetch($_GET['id']);
	$upload_dir=DOL_DATA_ROOT."/chronodocs/types";

	if (! is_dir($upload_dir)) create_exdir($upload_dir);
	if (is_dir($upload_dir))
	{
		if($chronodocstype->filename)
		{ //Security check
			if(!($user->rights->chronodocs->types->delete))
				accessforbidden();
		  // Delete old file
			$result=dol_delete_file($upload_dir . "/" .$chronodocstype->filename);
			if ($result)
				$result=$chronodocstype->set_filename($user,'null');
			else
			{
				$result=-1;
				$chronodocstype->error="dol_delete_file: Unable to Unlink".$upload_dir . "/" .$chronodocstype->filename;
			}
			if ($result < 0)
				dolibarr_print_error($db,$chronodocstype->error);
		}

		//$filename=$_FILES['userfile']['name'];
		$filename=dol_string_nospecial($chronodocstype->ref.".".$_FILES['userfile']['name']);
		if (dol_move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_dir . "/" . $filename,0) > 0)
		{
			$result=$chronodocstype->set_filename($user,$filename);
			if ($result < 0) dolibarr_print_error($db,$chronodocstype->error);
			$mesg = '<div class="ok">'.$langs->trans("FileTransferComplete").'</div>';
			//print_r($_FILES);
		}
		else
		{
			// Echec transfert (fichier dépassant la limite ?)
			$mesg = '<div class="error">'.$langs->trans("ErrorFileNotUploaded").'</div>';
			// print_r($_FILES);
		}
	}
	else
	{
		// Repertoire upload_dir absent malgre tentative creation
		$mesg = '<div class="error">'.$langs->trans("ErrorFileNotUploaded").'</div>';
		// print_r($_FILES);
	}
}

/*
Mise a jour d'une propriete
*/
if ($_POST['action'] == 'set_propfield')
{

	$chronodocstype = new Chronodocs_types($db);
	$chronodocstype->fetch($_GET['id']);

	$propfield = new Chronodocs_propfields($db);
	$result=$propfield->fetch($_POST['propid']);
	if ($result < 0) dolibarr_print_error($db,$propfield->error);
	else if(!empty($propfield->fk_type) && !empty($chronodocstype->id) && $propfield->fk_type == $chronodocstype->id)
	{
		$propfield->ref=$_POST['propfield_ref'];
		$propfield->title=$_POST['propfield_title'];
		$propfield->brief=$_POST['propfield_brief'];

		$result=$propfield->update($user);
		if ($result < 0)
			dolibarr_print_error($db,$propfield->error);
		else
			$chronodocstype->set_date_u($user);
	}
}

/*
Ajout d'une propriete
*/
if ($_POST['action'] == 'add_propfield')
{

	$chronodocstype = new Chronodocs_types($db);
	$result=$chronodocstype->fetch($_GET['id']);
	if ($result < 0)
		dolibarr_print_error($db,$chronodocstype->error);
	else
	{
		$propfield = new Chronodocs_propfields($db);
		$result=$propfield->initAsSpecimen();
		$propfield->fk_type = $_GET['id'];
		$propfield->ref=$_POST['propfield_ref'];
		$propfield->title=$_POST['propfield_title'];
		$propfield->brief=$_POST['propfield_brief'];
		$propfield->fk_status='0';

		if(ereg("#R_([^#]+)#",$propfield->ref))
		{
			$auto_propfields_ref=$propfield->liste_auto_propfields();
			$propfield->title = $auto_propfields_ref[$propfield->ref];
			if(empty($propfield->title)) $propfield->title="AUTO:".$_POST['propfield_title'];
		}

		$result=$propfield->create($user);
		if ($result < 0)
			dolibarr_print_error($db,$propfield->error);
		else
			$chronodocstype->set_date_u($user);
	}
}

/*
Suppression d'une propriete
*/
if ($_GET['action'] == 'del_propfield')
{

	$chronodocstype = new Chronodocs_types($db);
	$chronodocstype->fetch($_GET['id']);

	$propfield = new Chronodocs_propfields($db);
	$result=$propfield->fetch($_GET['propid']);
	if ($result < 0) dolibarr_print_error($db,$propfield->error);
	else if(!empty($propfield->fk_type) && !empty($chronodocstype->id) && $propfield->fk_type == $chronodocstype->id)
	{
		$result=$propfield->clear_values($user);
		if ($result > 0)
			$result=$propfield->delete($user);
		else if ($result < 0)
			dolibarr_print_error($db,$propfield->error);
		else
			$chronodocstype->set_date_u($user);
	}
}

if(($_POST["action"] == 'confirm_delete' AND $_POST["confirm"] == 'yes') && ($user->rights->chronodocs->types->delete))
{
	$chronodocstype = new Chronodocs_types($db);
	$chronodocstype->fetch($_GET['id']);
	if (!empty($chronodocstype->id))
	{
		$result = $chronodocstype->delete($user);
		if ($result < 0)
			dolibarr_print_error($db,$chronodocstype->error);
		else
		{
			Header('Location: '.$_SERVER["PHP_SELF"]);
			exit;
		}
	}
}

/*
 * View
 */

$html = new Form($db);

llxHeader();
if ($_GET["id"] > 0 && $_GET["action"] == 'info' )
{
	/*
	 * Affichage infos
	 */
	$chronodocstype = new Chronodocs_types($db);
	$result=$chronodocstype->fetch($_GET["id"]);
	if (! $result > 0)
	{
		dolibarr_print_error($db);
		exit;
	}

	$head = chronodocs_types_prepare_head($chronodocstype);

	dolibarr_fiche_head($head, 'infos', $langs->trans("ChronodocType"));

	$chronodocstype->info($chronodocs->id);

	print '<table width="100%"><tr><td>';
	dolibarr_print_object_info($chronodocstype);
	print '</td></tr></table>';


}

if ($_GET["id"] > 0 && $_GET["action"] != 'create' && $_GET["action"] != 'info' )
{
	/*
	 * Affichage en mode visu
	 */
	$chronodocstype = new Chronodocs_types($db);
	$result=$chronodocstype->fetch($_GET["id"]);
	if (! $result > 0)
	{
		dolibarr_print_error($db);
		exit;
	}

	if ($mesg) print $mesg."<br>";

	$head = chronodocs_types_prepare_head($chronodocstype);

	dolibarr_fiche_head($head, 'card', $langs->trans("ChronodocType"));

	/*
	 * Confirmation de la suppression de la fiche chronodoc
	 */
	if ($_GET['action'] == 'delete')
	{
		$html->form_confirm($_SERVER["PHP_SELF"].'?id='.$chronodocstype->id, $langs->trans('DeleteChronodocType'), $langs->trans('ConfirmDeleteChronodoc'), 'confirm_delete');
		print '<br>';
	}


	print '<table class="border" width="100%">';

	// Ref
	print '<tr><td width="25%">'.$langs->trans("Ref").'</td><td>'.$chronodocstype->ref.'</td></tr>';

	// Titre
	print '<tr>';
	if ($_GET['action'] == 'edit_title')
	{
		print "<td>".$langs->trans("Title")."</td>";
		print '<td><form name="edit_title" action="'.$_SERVER["PHP_SELF"].'?id='.$chronodocstype->id.'" method="post">';
		print '<input type="hidden" name="action" value="set_title">';
		print "<input name=\"title\" value=\"".$chronodocstype->title."\" size=\"74\">";
		print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
		print "</form></td>";
	}
	else
	{
		print '<td><table class="nobordernopadding" width="100%"><tr><td>';
		print "<td>".$langs->trans("Title")."</td>";
		print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=edit_title&amp;id='.$chronodocstype->id.'">'.img_edit($langs->trans('SetTitle'),1).'</a></td>';
		print '</tr></table></td>';
		print "<td>".$chronodocstype->title."</td>";
	}
	print "</tr>\n";

	// Description
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td>';
	print $langs->trans('Description');
	print '</td>';
	if ($_GET['action'] != 'edit_brief') print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=edit_brief&amp;id='.$chronodocstype->id.'">'.img_edit($langs->trans('SetDescription'),1).'</a></td>';
	print '</tr></table>';
	print '</td><td colspan="3">';
	if ($_GET['action'] == 'edit_brief')
	{
		print '<form name="edit_brief" action="'.$_SERVER["PHP_SELF"].'?id='.$chronodocstype->id.'" method="post">';
		print '<input type="hidden" name="action" value="setbrief">';
		if ($conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_SOCIETE)
		{
			// Editeur wysiwyg
			require_once(DOL_DOCUMENT_ROOT."/lib/doleditor.class.php");
			$doleditor=new DolEditor('brief',$chronodocstype->brief,280,'dolibarr_notes','In',true);
			$doleditor->Create();
		}
		else
		{
			print '<textarea name="brief" wrap="soft" cols="70" rows="12">'.dol_htmlentitiesbr_decode($chronodocstype->brief).'</textarea>';
		}
		print '<input type="submit" class="button" value="'.$langs->trans('Modify').'">';
		print '</form>';
	}
	else
	{
		print nl2br($chronodocstype->brief);
	}
	print '</td>';
	print '</tr>';

	// Statut
	//print '<tr><td>'.$langs->trans("Status").'</td><td>'.$chronodocstype->getLibStatut(4).'</td></tr>';

	// Fichier
	print '<tr><td>';
	print '<table class="nobordernopadding" width="100%"><tr><td>';
	print $langs->trans("File");
	print '</td>';
	if ($_GET['action'] != 'edit_file')
	{
		$urlsource=$_SERVER["PHP_SELF"].'?action=edit_file&amp;id='.$chronodocstype->id;
		$relativepath=$chronodocstype->filename;
		if($chronodocstype->filename)
			print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=edit_file&id='.$chronodocstype->id.'">'.img_delete($langs->trans('SetFile'),1).'</a></td>';
		else
			print '<td align="right"><a href="'.$_SERVER["PHP_SELF"].'?action=edit_file&id='.$chronodocstype->id.'">'.img_edit($langs->trans('SetFile'),1).'</a></td>';
	}
	print '</tr></table>';
	print '</td><td>';
	if ($_GET['action'] == 'edit_file')
	{
		$url=$_SERVER["PHP_SELF"].'?id='.$chronodocstype->id;
		print '<form name="userfile" action="'.$url.'" enctype="multipart/form-data" method="POST">';
		print '<table width="100%" class="noborder">';
		print '<tr><td width="50%" valign="top">';
		print '<input type="hidden" name="action" value="setfile">';
		$max=$conf->upload;							// En Kb
		$maxphp=@ini_get('upload_max_filesize');	// En inconnu
		if (eregi('m$',$maxphp)) $maxphp=$maxphp*1024;
		if (eregi('k$',$maxphp)) $maxphp=$maxphp;
		// Now $max and $maxphp are in Kb
		if ($maxphp > 0) $max=min($max,$maxphp);

		if ($conf->upload > 0)
		{
			print '<input type="hidden" name="max_file_size" value="'.($max*1024).'">';
		}
		print '<input class="flat" type="file" name="userfile" size="70">';
		print ' &nbsp; ';
		print '<input type="submit" class="button" name="sendit" value="'.$langs->trans("Upload").'">';

		if ($addcancel)
		{
			print ' &nbsp; ';
			print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
		}

		print ' ('.$langs->trans("MaxSize").': '.$max.' '.$langs->trans("Kb");
		print ' '.info_admin($langs->trans("ThisLimitIsDefinedInSetup",$max,$maxphp),1);
		print ')';
		print "</td></tr>";
		print "</table>";

		print '</form>';
		print '<br>';
	}
	else
	{
		if($chronodocstype->filename)
			print '<a href="document.php?action=get_file&chronodocstypeid='.$chronodocstype->id.'&modulepart=chronodocs_types&file='.urlencode($chronodocstype->filename).'">'.$chronodocstype->filename."</a>";
		else
			print $langs->trans("NoFile");
	}

	print '</td></tr>';

	// NbChronodocs
	print '<tr><td width="25%">'.$langs->trans("NbChronodocs").'</td><td>'.$chronodocstype->get_nb_chronodocs().'</td></tr>';

	print "</table><br>";

		//PROPRIETES supplementaires
		print $langs->trans("Others")." :<br>";
		$nb_propfields=$chronodocstype->fetch_chronodocs_propfields();
		if($nb_propfields > 0 || ($chronodocstype->statut == 0 && $user->rights->chronodocs->types->write) )
		{
			//ligne titre
			$propfield = new Chronodocs_propfields($db);
			$auto_propfields_ref=$propfield->liste_auto_propfields();
			$desc_autopropfields="";
			foreach($auto_propfields_ref as $propfields_ref => $propfields_title)
				$desc_autopropfields.="<br>".$propfields_ref." - ".$propfields_title;
			print '<table class="border" width="100%">';
			print '<tr>';
			print '<td width="25%">'.$form->textwithhelp($langs->trans("Ref"),$langs->trans("Desc_Chronodocs_propfields").$desc_autopropfields).'</td>';
			print '<td width="25%">'.$langs->trans("Title").'</td>';
			print '<td>'.$langs->trans("Description").'</td>';
			if ($user->rights->chronodocs->types->write)
				{
					print '<td colspan="2" align="center">';
					print '</td>';
				}
			print "</tr>";

			// lignes props
			if($nb_propfields > 0) // OK et au moins 1 prop
			{
				$auto_propfields_index=array();

				for($i=0;$i<$nb_propfields;$i++)
				{
					if(!ereg("#R_([^#]+)#",$chronodocstype->propfields[$i]->ref))
					{
						print '<tr>';
						if ($_GET['action'] == 'edit_propfield' && $_GET['propid'] == $chronodocstype->propfields[$i]->propid && $user->rights->chronodocs->types->write)
						{
							print '<form name="edit_propfield" action="'.$_SERVER["PHP_SELF"].'?id='.$chronodocstype->id.'" method="post">';
							print '<input type="hidden" name="action" value="set_propfield">';
							print '<input type="hidden" name="propid" value="'.$chronodocstype->propfields[$i]->propid.'">';

							print '<td width="25%" align="center">'."<input name=\"propfield_ref\" value=\"".$chronodocstype->propfields[$i]->ref."\" size=\"40\">".'</td>';
							print '<td width="25%" align="center">'."<input name=\"propfield_title\" value=\"".$chronodocstype->propfields[$i]->title."\" size=\"40\">".'</td>';
							print '<td align="center">';
							print '<textarea name="propfield_brief" wrap="soft" cols="80" rows="2">'.dol_htmlentitiesbr_decode($chronodocstype->propfields[$i]->brief).'</textarea>';
							print '</td>';
							print '<td colspan="2" align="center"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></td>';
							print "</form>";
						}
						else
						{
							print '<td width="25%">'.$chronodocstype->propfields[$i]->ref.'</td>';
							print '<td width="25%">'.$chronodocstype->propfields[$i]->title.'</td>';
							print '<td>'.nl2br($chronodocstype->propfields[$i]->brief).'</td>';
							if ($user->rights->chronodocs->types->write)
							{
								print '<td align="center">';
								print '<a href="'.$_SERVER["PHP_SELF"].'?action=edit_propfield&amp;id='.$chronodocstype->id.'&amp;propid='.$chronodocstype->propfields[$i]->propid.'">'.img_edit($langs->trans('Edit'),1).'</a>';
								print '</td><td align="center">';
								print '<a href="'.$_SERVER["PHP_SELF"].'?action=del_propfield&amp;id='.$chronodocstype->id.'&amp;propid='.$chronodocstype->propfields[$i]->propid.'">'.img_delete($langs->trans('Delete'),1).'</a>';
								print '</td>';
							}
						}
						print "</tr>";
					}
					else
					{
						$auto_propfields_index[]=$i;
					}
				}

			}
			// Nouvelle propriete
			if ($chronodocstype->statut == 0 && $user->rights->chronodocs->types->write)
			{
				print '<tr>';
				$propfield = new Chronodocs_propfields($db);
				$propfield->initAsSpecimen();
				print '<form name="add_propfield" action="'.$_SERVER["PHP_SELF"].'?id='.$chronodocstype->id.'" method="post">';
				print '<input type="hidden" name="action" value="add_propfield">';
				print '<td width="25%" align="center">'."<input name=\"propfield_ref\" value=\"".$propfield->ref."\" size=\"40\">".'</td>';
				print '<td width="25%" align="center">'."<input name=\"propfield_title\" value=\"".$propfield->title."\" size=\"40\">".'</td>';
				print '<td align="center">';
				print '<textarea name="propfield_brief" wrap="soft" cols="80" rows="2">'.dol_htmlentitiesbr_decode($propfield->brief).'</textarea>';
				print '</td>';
				print '<td colspan="2" align="center"><input type="submit" class="button" value="'.$langs->trans("Create").'"></td>';
				print "</form>";
				print "</tr>";
			}

			//proprietes auto
			if($nb_propfields > 0 && !empty($auto_propfields_index) ) // au moins 1 prop auto
			{
				foreach ($auto_propfields_index as $key => $value)
				{
					$i=$value;
					if(ereg("#R_([^#]+)#",$chronodocstype->propfields[$i]->ref))
					{
						print '<tr>';
						if ($_GET['action'] == 'edit_propfield' && $_GET['propid'] == $chronodocstype->propfields[$i]->propid && $user->rights->chronodocs->types->write)
						{
							print '<form name="edit_propfield" action="'.$_SERVER["PHP_SELF"].'?id='.$chronodocstype->id.'" method="post">';
							print '<input type="hidden" name="action" value="set_propfield">';
							print '<input type="hidden" name="propid" value="'.$chronodocstype->propfields[$i]->propid.'">';

							print '<td width="25%" align="center">'."AUTO: <input name=\"propfield_ref\" value=\"".$chronodocstype->propfields[$i]->ref."\" readonly size=\"30\">".'</td>';
							print '<td width="25%" align="center">'."<input name=\"propfield_title\" value=\"".$chronodocstype->propfields[$i]->title."\" size=\"40\">".'</td>';
							print '<td align="center">';
							print '<textarea name="propfield_brief" wrap="soft" cols="80" rows="2">'.dol_htmlentitiesbr_decode($chronodocstype->propfields[$i]->brief).'</textarea>';
							print '</td>';
							print '<td colspan="2" align="center"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></td>';
							print "</form>";
						}
						else
						{
							print '<td width="25%">AUTO: '.$chronodocstype->propfields[$i]->ref.'</td>';
							print '<td width="25%">'.$chronodocstype->propfields[$i]->title.'</td>';
							print '<td>'.nl2br($chronodocstype->propfields[$i]->brief).'</td>';
							if ($user->rights->chronodocs->types->write)
							{
								print '<td align="center">';
								print '<a href="'.$_SERVER["PHP_SELF"].'?action=edit_propfield&amp;id='.$chronodocstype->id.'&amp;propid='.$chronodocstype->propfields[$i]->propid.'">'.img_edit($langs->trans('Edit'),1).'</a>';
								print '</td><td align="center">';
								print '<a href="'.$_SERVER["PHP_SELF"].'?action=del_propfield&amp;id='.$chronodocstype->id.'&amp;propid='.$chronodocstype->propfields[$i]->propid.'">'.img_delete($langs->trans('Delete'),1).'</a>';
								print '</td>';
							}
						}
						print "</tr>";
					}
				}
			}

			// Nouvelle propriete auto
			if ($chronodocstype->statut == 0 && $user->rights->chronodocs->types->write)
			{
				$propfield = new Chronodocs_propfields($db);
				$auto_propfields_ref=$propfield->liste_auto_propfields();

				print '<tr>';
				$propfield->initAsSpecimen();
				print '<form name="add_propfield" action="'.$_SERVER["PHP_SELF"].'?id='.$chronodocstype->id.'" method="post">';
				print '<input type="hidden" name="action" value="add_propfield">';
				print '<td colspan="2" align="left">AUTO: ';
				$html->select_array('propfield_ref',$auto_propfields_ref,$_GET['propfield_ref'],0,1);
				print '</td>';
				//print '<td width="25%" align="center">'."<input name=\"propfield_title\" value=\"".$propfield->title."\" size=\"40\">".'</td>';
				print '<td align="center">';
				print '<textarea name="propfield_brief" wrap="soft" cols="80" rows="2">'.dol_htmlentitiesbr_decode($propfield->brief).'</textarea>';
				print '</td>';
				print '<td colspan="2" align="center"><input type="submit" class="button" value="'.$langs->trans("Create").'"></td>';
				print "</form>";
				print "</tr>";
			}

			print "</table><br>";
		}
		// fin PROPRIETES supplementaires


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
		if ($chronodocstype->statut == 0 && $user->rights->chronodocs->types->write)
		{
			print '<a class="butActionDelete" ';
			print 'href="'.$_SERVER["PHP_SELF"].'?id='.$chronodocstype->id.'&amp;action=editAll"';
			print '>'.$langs->trans('Modify').'</a>';
		}

		// Delete
		if ($chronodocstype->statut == 0 && $user->rights->chronodocs->types->delete && ($chronodocstype->get_nb_chronodocs()==0) )
		{
			print '<a class="butActionDelete" ';
			if ($conf->use_javascript_ajax && $conf->global->MAIN_CONFIRM_AJAX)
			{
				$url = $_SERVER["PHP_SELF"].'?id='.$chronodocstype->id.'&action=confirm_delete&confirm=yes';
				print 'href="#" onClick="dialogConfirm(\''.$url.'\',\''.$langs->trans("ConfirmDeleteChronodocType").'\',\''.$langs->trans("Yes").'\',\''.$langs->trans("No").'\',\'delete\')"';
			}
			else
			{
				print 'href="'.$_SERVER["PHP_SELF"].'?id='.$chronodocstype->id.'&amp;action=delete"';
			}
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
		print_titre($langs->trans("AddChronodocType"));
	}
	else
	{
		print_titre($langs->trans("EditChronodocType"));
	}
	if ($mesg) print $mesg.'<br>';

	$chronodocstype = new Chronodocs_types($db);
	$chronodocstype->date_c = time();
	if ($typeid) 	$result=$chronodocstype->fetch($typeid);

	if ($_GET["action"] == 'editAll' && !empty($chronodocstype->socid))
	{
		$_GET["socid"]=$chronodocstype->socid; //get socid from chronodoc instead of url
	}


	print "<form name='chronodocstype' action=\"types.php\" method=\"post\">";
	if ($_GET["action"] == 'create')
		{
			print "<input type=\"hidden\" name=\"action\" value=\"add\">";
		}
	else
		{
			print "<input type=\"hidden\" name=\"action\" value=\"update\">";
			print "<input type=\"hidden\" name=\"id\" value=\"$typeid\">";
		}
	print '<table class="border" width="100%">';

	// Ref
	if($_GET["action"] != 'create' && ($nb_chronodocs=$chronodocstype->get_nb_chronodocs($typeid)) )
	{
	print '<tr><td><table class="nobordernopadding" width="100%"><tr><td>'.$langs->trans("Ref")."</td>";
	print '<td align="right"><span id="ImgConfirmEditChronodocsTypeRef" onclick="if(confirm(\''.$langs->trans("ConfirmEditChronodocsTypeRef",$nb_chronodocs).'\')) {(document.getElementById(\'ImgConfirmEditChronodocsTypeRef\')).style.visibility = \'hidden\'; (document.getElementById(\'ref\')).readOnly=false;}">'.img_edit($langs->trans('SetDescription'),1).'</span></td>';
	print '</tr></table></td>';
	print "<td><input id=\"ref\" name=\"ref\" value=\"".$chronodocstype->ref."\" size=\"32\" readonly>";
	print "</td></tr>\n";
	}
	else
	{
		print '<tr><td>'.$langs->trans("Ref")."</td>";
		print "<td><input name=\"ref\" value=\"".$chronodocstype->ref."\" size=\"32\"></td></tr>\n";
	}
	// Titre
	print "<tr><td>".$langs->trans("Title")."</td>";
	print "<td><input name=\"title\" size=\"74\" value=\"".$chronodocstype->title."\"></td></tr>\n";

	// Description
	print '<tr><td valign="top">'.$langs->trans("Description").'</td>';
	print "<td>";
	if ($conf->fckeditor->enabled && $conf->global->FCKEDITOR_ENABLE_SOCIETE)
	{
		// Editeur wysiwyg
		require_once(DOL_DOCUMENT_ROOT."/lib/doleditor.class.php");
		$doleditor=new DolEditor('brief',$chronodocstype->brief,280,'dolibarr_notes','In',true);
		$doleditor->Create();
	}
	else
	{
		print '<textarea name="brief" wrap="soft" cols="70" rows="12">'.$chronodocstype->brief.'</textarea>';
	}

	print '</td></tr>';

	print '<tr><td colspan="2" align="center">';
	if ($_GET["action"] == 'create')
		print '<input type="submit" class="button" value="'.$langs->trans("Create").'">';
	else
		print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';

	print '</td></tr>';

	print '</table>';
	print '</form>';
}


/*
  * Affichage liste types chronodocs
  */
if (empty($_GET["id"]) &&  (empty($_GET["action"]) || $_GET["action"] != 'create' ))
{
	// Params
	$sortorder=$_GET["sortorder"]?$_GET["sortorder"]:$_POST["sortorder"];
	$sortfield=$_GET["sortfield"]?$_GET["sortfield"]:$_POST["sortfield"];
	$socid=$_GET["socid"]?$_GET["socid"]:$_POST["socid"];
	$page=$_GET["page"]?$_GET["page"]:$_POST["page"];

	if (! $sortorder) $sortorder="DESC";
	if (! $sortfield) $sortfield="f.date_c";
	if ($page == -1) { $page = 0 ; }

	if (!empty($_GET['search_title']))
		$search_title = $_GET['search_title'];
	else
		$search_title = "";

	if (!empty($_GET['search_ref']))
		$search_ref = $_GET['search_ref'];
	else
		$search_ref = "";

	$limit = $conf->liste_limit;
	$offset = $limit * $page ;
	$pageprev = $page - 1;
	$pagenext = $page + 1;

	// List Request
	$chronodocs_static=new Chronodocs_types($db);
	$result = $chronodocs_static->get_list($limit,$offset,$sortfield,$sortorder,$search_ref,$search_title);
	if ($result)
	{
	    $num = sizeOf($result);

		$urlparam="&amp;socid=$socid";
	    print_barre_liste($langs->trans("ListOfChronodocsTypes"), $page, "types.php",$urlparam,$sortfield,$sortorder,'',$num);

	    $i = 0;
	    print '<table class="noborder" width="100%">';
	    print "<tr class=\"liste_titre\">";
	    print_liste_field_titre($langs->trans("Ref"),"types.php","f.ref","",$urlparam,'width="15%"',$sortfield,$sortorder);
	    print '<td>'.$langs->trans("Title").'</td>';
	    print_liste_field_titre($langs->trans("Date"),"types.php","f.date_c","",$urlparam,'align="center"',$sortfield);
		print_liste_field_titre($langs->trans("DateU"),"types.php","f.date_u","",$urlparam,'align="center"',$sortfield);
		print '<td align="center">'.$langs->trans("NbChronodocs").'</td>';
	    print "</tr>\n";
	    $var=True;
	    $total = 0;
	    while ($i < min($num, $limit))
	    {
	        $objp = array_shift($result);
	        $var=!$var;
	        print "<tr $bc[$var]>";
	        print "<td><a href=\"types.php?id=".$objp->id."\">".img_object($langs->trans("Show"),"task").' '.$objp->ref."</a></td>\n";
	        print '<td>'.nl2br($objp->title).'</td>';
	        print '<td align="center">'.dolibarr_print_date($objp->dp)."</td>\n";
			print '<td align="center">'.dolibarr_print_date($objp->date_u)."</td>\n";
			print '<td align="center">'.$chronodocs_static->get_nb_chronodocs($objp->id)."</td>\n";

	        print "</tr>\n";
	        $i++;
	    }

	    print '</table>';
	}

	/**
	 * Barre d'actions
	 *
	 */
	print '<div class="tabsAction">';

	if ($user->societe_id == 0 && $_GET["action"] != 'editAll')
	{
		// Create
		if ($chronodocstype->statut == 0 && $user->rights->chronodocs->types->write)
		{
			print '<a class="butActionDelete" ';
			print 'href="'.$_SERVER["PHP_SELF"].'?action=create"';
			print '>'.$langs->trans('Create').'</a>';
		}
	}

	print '</div>';

}
$db->close();

llxFooter('$Date: 2010/05/01 14:30:11 $ - $Revision: 1.4 $');
?>