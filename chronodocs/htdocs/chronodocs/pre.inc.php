<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
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
   \file       htdocs/dev/skeletons/pre.inc.php
   \brief      File to manage left menu by default
   \version    $Id: pre.inc.php,v 1.1 2008/09/10 09:34:56 raphael_bertrand Exp $
*/

// Include environment and check authentification
require ("../main.inc.php");		// This include must use a relative link to the main.inc.php file


/**
		\brief		Function called by page to show menus (top and left)
*/
function llxHeader($head = "")
{
	global $user, $conf, $langs;

	top_menu($head);

	$menu = new Menu();
	
	// Create default menu.
	
	// No code here is required if you already added menu entries in
	// the module descriptor (recommanded).
	// If not you must manually add menu entries here (not recommanded).
	/*
	$langs->load("mylangfile");
	$menu->add(DOL_URL_ROOT."/mylink.php", $langs->trans("MyMenuLabel"));
	}
	*/
	
	left_menu($menu->liste);  
}
?>
