<?php


//Start of user code copyright htdocs/product_composition/pre.inc.php

/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 * Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
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

/**     \defgroup   product_composition     Module product_composition
        \brief      Gestion des produits composés
*/

/**
        \file       htdocs/includes/modules/modproduct_composition.class.php
        \ingroup    product_composition
        \brief      Description and activation file for Module product_composition
		\version	$Id: modproduct_composition.class.php,v 1.0
		\author		Patrick Raguin
*/
//End of user code
require("../main.inc.php");

//Inclusion des DAO et des Services n�cessaires
require_once(DOL_DOCUMENT_ROOT."/composition/service_product.class.php");
require_once(DOL_DOCUMENT_ROOT."/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/composition/dao_product_composition.class.php");
require_once(DOL_DOCUMENT_ROOT."/composition/service_product_composition.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/composition/lib.php");

// chargement des fichiers de langue n�cessaires
$langs->load("bills");
$langs->load("products");
$langs->load("other");

// inclusion de Smarty
require_once('../../external-libs/smarty/libs/Smarty.class.php');

//Start of user code UserCode htdocs/product_composition/pre.inc.php
function llxHeader($langs, $head = "", $title="", $help_url='')
{

	global $conf, $user, $langs;
	
 	top_menu($head, $title);
	
	$menu = new Menu();




	left_menu($menu->liste, $help_url);
}
//End of user code
?>
