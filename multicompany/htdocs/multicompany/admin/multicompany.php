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
 *	\file       htdocs/admin/multicompany.php
 *	\ingroup    multicompany
 *	\brief      Page d'administration/configuration du module Multi-societe
 *	\version    $Id: multicompany.php,v 1.2 2010/09/24 16:33:32 hregis Exp $
 */

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/multicompany/class/actions_multicompany.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/lib/company.lib.php");

$langs->load("admin");

if (!$user->admin || $user->entity)
accessforbidden();

$mc = new ActionsMulticompany($db);

/*
 * Actions
 */

$mc->doActions();


/*
 * View
 */

llxHeader('',$langs->trans("MultiCompanySetup"));

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("MultiCompanySetup"),$linkback,'setup');

print '<br>';

$mc->template_dir = DOL_DOCUMENT_ROOT.'/multicompany/tpl/';

/*
 * Create
 */

if ($_GET["action"] == 'create')
{
	print_titre($langs->trans("AddEntity"));

	$mc->template = 'entity_create.tpl.php';
}

/*
 * Edit
 */

else if ($_GET["action"] == 'edit')
{
	print_titre($langs->trans("EditEntity"));

	$mc->template = 'entity_edit.tpl.php';
}

/*
 * View
 */

else
{
	print_titre($langs->trans("ListOfEntities"));

	$mc->template = 'entity_admin.tpl.php';

}

$mc->assign_values($_GET["action"]);
$mc->display();


llxFooter('$Date: 2010/09/24 16:33:32 $ - $Revision: 1.2 $');
?>