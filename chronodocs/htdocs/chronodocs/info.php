<?php
/* Copyright (C) 2005-2007  Regis Houssin  <regis@dolibarr.fr>
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
 */

/**
	\file       htdocs/chronodocs/info.php
	\ingroup    chronodocs
	\brief      Page d'affichage des infos d'une chronodoc
	\version    $Id: info.php,v 1.1 2008/09/10 09:34:56 raphael_bertrand Exp $
*/

require('./pre.inc.php');
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_entries.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/chronodocs.lib.php");


$langs->load('companies');
$langs->load('chronodocs');

$chronodocsid = isset($_GET["id"])?$_GET["id"]:'';

// Security check
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'chronodocs', $chronodocsid, 'chronodocs_entries','entries');


/*
*	View
*/

llxHeader();

$chronodocs = new Chronodocs_entries($db);
$chronodocs->fetch($chronodocsid);

$societe = new Societe($db);
$societe->fetch($chronodocs->socid);

$head = chronodocs_prepare_head($chronodocs);
dolibarr_fiche_head($head, 'info', $langs->trans('Chronodoc'));

$chronodocs->info($chronodocs->id);

print '<table width="100%"><tr><td>';
dolibarr_print_object_info($chronodocs);
print '</td></tr></table>';

print '</div>';
 
// Juste pour éviter bug IE qui réorganise mal div précédents si celui-ci absent
print '<div class="tabsAction">';
print '</div>';

$db->close();

llxFooter('$Date: 2008/09/10 09:34:56 $ - $Revision: 1.1 $');
?>
