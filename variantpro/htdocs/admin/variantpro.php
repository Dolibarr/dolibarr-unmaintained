<?php
/* Copyright (C) 2010 Regis Houssin  <regis@dolibarr.fr>
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
 *	\file       htdocs/admin/variantpro.php
 *	\ingroup    product
 *	\brief      Page d'administration/configuration du module VariantPro
 *	\version    $Id: variantpro.php,v 1.1 2010/02/23 15:52:10 hregis Exp $
 */

require("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
//require_once(DOL_DOCUMENT_ROOT.'/variantpro/variantpro.class.php');

$langs->load("@variantpro");

if (!$user->admin)
accessforbidden();

//$vp = new VariantPro($db);

/*
 * Actions
 */



llxHeader('',$langs->trans("VariantProSetup"));

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("VariantProSetup"),$linkback,'setup');

print '<br>';

$smarty->template_dir = DOL_DOCUMENT_ROOT.'/variantpro/templates/';
$template = 'admin.tpl';

//$vp->assign_smarty_values($smarty,$_GET["action"]);
$smarty->display($template);


llxFooter('$Date: 2010/02/23 15:52:10 $ - $Revision: 1.1 $');
?>