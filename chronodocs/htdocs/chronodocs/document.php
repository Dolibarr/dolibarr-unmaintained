<?php
/* Copyright (C) 2003-2004 Rodolphe Quiedeville  <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur   <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Marc Barilley / Ocebo <marc@ocebo.com>
 * Copyright (C) 2005      Regis Houssin         <regis@dolibarr.fr>
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
        \file       htdocs/chronodocs/document.php
        \ingroup    chronodocse
        \brief      Page de gestion des documents attachées à un chronodoc
        \version    $Id: document.php,v 1.3 2008/11/02 00:22:22 raphael_bertrand Exp $
*/

require('./pre.inc.php');



require_once(DOL_DOCUMENT_ROOT."/includes/modules/chronodocs/files.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_entries.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_types.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/chronodocs.lib.php");

$langs->load("companies");
$langs->load("chronodocs");
$langs->load('other');

$action=empty($_GET['action']) ? (empty($_POST['action']) ? '' : $_POST['action']) : $_GET['action'];

$chronodocsid = isset($_GET["chronodocsid"])?$_GET["chronodocsid"]:'';
if($action=='delete')
	$chronodocsid = isset($_GET["id"])?$_GET["id"]:$chronodocsid;

// Security check
if ($user->societe_id) 
{
	unset($_GET["action"]);
	$action=''; 
	$socid = $user->societe_id;
}
$result = restrictedArea($user, 'chronodocs', $chronodocsid, 'chronodocs_entries','entries','socid');

// Get parameters
$page=$_GET["page"];
$sortorder=$_GET["sortorder"];
$sortfield=$_GET["sortfield"];

if (! $sortorder) $sortorder="ASC";
if (! $sortfield) $sortfield="name";
if ($page == -1) { $page = 0 ; }
$offset = $conf->liste_limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;

//Other Inits
$formfile=new ChronodocsFiles($db);
/*
 * Actions
 */
 
// Envoi fichier
if ($_POST["sendit"] && $conf->upload && $user->rights->chronodocs->entries->write)
{
	$chronodocs = new Chronodocs_entries($db);
	if ($chronodocs->fetch($chronodocsid))
	{
		$mesg=$formfile->process_action_sendit($user,$chronodocs);
	}	
}

// Delete
if ($action=='delete' && $user->rights->chronodocs->entries->delete)
{
	$chronodocs = new Chronodocs_entries($db);
	if ($chronodocs->fetch($chronodocsid))
    {
		$mesg=$formfile->process_action_delete($user,$chronodocs);
    }
}

/*
 * Autres Actions sur fichiers (similaire à traitement ancien htdocs/document.php)
 */
 if($action=='get_file' || $action=='remove_file')
{
	if(empty($_GET["modulepart"]) || urldecode($_GET["modulepart"]) == 'chronodocs_entries')
	{
		$chronodocs = new Chronodocs_entries($db);
		if ($chronodocs->fetch($chronodocsid))
	    {
			$mesg=$formfile->process_actions_file($user,$chronodocs);
	    }
		exit(); // fin output
	}
	elseif(urldecode($_GET["modulepart"]) == 'chronodocs_types')
	{
		$chronodocstype = new Chronodocs_types($db);
		$chronodocstypeid=(!empty($_GET["chronodocstypeid"]))?($_GET["chronodocstypeid"]):$chronodocsid;
		if ($chronodocstype->fetch($chronodocstypeid))
	    {
			$mesg=$formfile->process_actions_file($user,$chronodocstype);
	    }
		exit(); // fin output
	}
}

/*
 * Affichage
 */
 
llxHeader();

if ($chronodocsid > 0)
{
	$chronodocs = new Chronodocs_entries($db);
	if ($chronodocs->fetch($chronodocsid))
    {
		$upload_dir = $conf->chronodocs->dir_output.'/'.dol_string_nospecial($chronodocs->ref);

        $societe = new Societe($db);
        $societe->fetch($chronodocs->socid);

		$head = chronodocs_prepare_head($chronodocs);
		dolibarr_fiche_head($head, 'document', $langs->trans('Chronodoc'));

		
		// Construit liste des fichiers
		//$filearray=dol_dir_list($upload_dir,"files",0,'','\.meta$',$sortfield,(strtolower($sortorder)=='desc'?SORT_ASC:SORT_DESC),1);
		$filearray=$formfile->get_filelist($user,$chronodocs);
		$totalsize=0;
		foreach($filearray as $key => $file)
		{
			$totalsize+=$file['size'];
		}
		

        print '<table class="border"width="100%">';

		// Ref
        print '<tr><td width="30%">'.$langs->trans('Ref').'</td><td colspan="3">'.$chronodocs->ref.'</td></tr>';

        // Société
        print '<tr><td>'.$langs->trans('Company').'</td><td colspan="5">'.$societe->getNomUrl(1).'</td></tr>';

        print '<tr><td>'.$langs->trans("NbOfAttachedFiles").'</td><td colspan="3">'.sizeof($filearray).'</td></tr>';
        print '<tr><td>'.$langs->trans("TotalSizeOfAttachedFiles").'</td><td colspan="3">'.$totalsize.' '.$langs->trans("bytes").'</td></tr>';

        print '</table>';

        print '</div>';

        if ($mesg) { print "$mesg<br>"; }

        // Affiche formulaire upload
		$formfile->form_attach_new_file(DOL_URL_ROOT.'/chronodocs/document.php?chronodocsid='.$chronodocs->id);


		// List of documents
		$param='&chronodocsid='.$chronodocs->id;
		$formfile->list_of_documents($filearray,$chronodocs,'chronodocs_entries',$param);

	}
	else
	{
		dolibarr_print_error($db);
	}
}
else
{
	print $langs->trans("UnkownError");
}

$db->close();

llxFooter('$Date: 2008/11/02 00:22:22 $ - $Revision: 1.3 $');
?>
