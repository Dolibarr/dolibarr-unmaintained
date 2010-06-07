<?php
/* Copyright (C) 2010      Auguria SARL         <info@auguria.org>
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
 *  \file       htdocs/admin/produit.php
 *  \ingroup    produit
 *  \brief      Page d'administration/configuration du module Produit
 *  \version    $Id: index.php,v 1.2 2010/06/07 14:16:32 pit Exp $
 */

require_once("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");

$langs->load("admin");
$langs->load("admin_book@book");

// Security check
if (!$user->admin)
accessforbidden();


if (isset($_POST["action"]) && (($_POST["action"] == 'BOOK_CONTRACT_BILL_SLOGAN') || ($_POST["action"] == 'BOOK_CONTRACT_BILL_SOCIETE_INFOS') || ($_POST["action"] == 'BOOK_CONTRACT_BILL_OTHER_INFOS')) )
{
	dolibarr_set_const($db, $_POST["action"], $_POST["value"],'chaine',0,'',$conf->entity);
}


include_once("tpl/index.tpl.php");

?>
