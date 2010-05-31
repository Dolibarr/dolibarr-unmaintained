<?php


//Start of user code copyright htdocs/product/book/pre.inc.php

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

/**     \defgroup   product     Module Book
        \brief      Book
*/

/**
        \file       htdocs/product/book/pre.inc.php
        \ingroup    book
        \brief      Description and activation file for Module product
		\version	$Id: pre.inc.php,v 1.0
		\author		Patrick Raguin
*/
//End of user code
require("../main.inc.php");
require(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require(DOL_DOCUMENT_ROOT."/product/class/html.formproduct.class.php");
require(DOL_DOCUMENT_ROOT."/lib/files.lib.php");
require(DOL_DOCUMENT_ROOT."/lib/product.lib.php");
require(DOL_DOCUMENT_ROOT."/book/lib/barcode.lib.php");
require(DOL_DOCUMENT_ROOT."/book/lib/book.lib.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/courrier_droit_editeur.class.php");

//Inclusion of necessary services and DAO
//Inclusion des DAO et des Services nécessaires
require(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require(DOL_DOCUMENT_ROOT."/fourn/class/fournisseur.class.php");
require(DOL_DOCUMENT_ROOT."/product/stock/class/mouvementstock.class.php");
require(DOL_DOCUMENT_ROOT."/composition/class/business/service_product.class.php");
require(DOL_DOCUMENT_ROOT."/composition/class/data/dao_product_composition.class.php");
require(DOL_DOCUMENT_ROOT."/composition/class/business/service_product_composition.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/data/dao_book.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/service_book.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/data/dao_book_contract.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/service_book_contract.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/data/dao_book_contract_bill.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/service_book_contract_bill.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/data/dao_book_contract_societe.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/service_book_contract_societe.class.php");
require(DOL_DOCUMENT_ROOT."/book/class/business/html.formbook.class.php");

//Loading of the necessary localization files
// chargement des fichiers de langue nécessaires
$langs->load("products");
$langs->load("@book");
$langs->load("main");
$langs->load("other");

//Smarty inclusion
// inclusion de Smarty
require_once(DOL_DOCUMENT_ROOT.'/includes/smarty/libs/Smarty.class.php');

//End of user code
?>
