<?php
/* Copyright (C) 2009 Marc STUDER  <dev@garstud.com>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
		\file       htdocs/speedfinder/pre.inc.php
		\ingroup    company
		\brief      Fichier gestionnaire du menu gauche des notifications
		\version    $Id: pre.inc.php,v 1.2 2009/11/30 22:37:40 hregis Exp $
*/

require ("../main.inc.php");

function llxHeader($head = "", $title="", $help_url='')
{
	global $conf, $user, $langs;
	$langs->load("speedfinder");
	$user->getrights('speedfinder'); //TODO ???

	top_menu($head, $title);

	$menu = new Menu();
	$menu->add(DOL_URL_ROOT."/speedfinder/index.php?mainmenu=speedfinder", $langs->trans("SFinderSubTitle"));
	left_menu($menu->liste, $help_url);
}
?>
