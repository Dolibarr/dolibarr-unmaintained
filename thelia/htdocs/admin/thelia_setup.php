<?php
/* Copyright (C) 2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *   	\file       admin/thelia_setup.php
 *		\ingroup    thelia
 *		\brief      Configuration du module thelia
 *		\version    $Id: thelia_setup.php,v 1.1 2010/01/01 19:20:24 jfefe Exp $
 *		\author		Put author name here
 *		\remarks	Put here some comments
 */
require("./pre.inc.php");
//require_once(DOL_DOCUMENT_ROOT."/../dev/skeletons/skeleton_class.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("thelia");

if (!$user->admin)
accessforbidden();







if ($_POST["action"] == 'setvalue' && $user->admin)
{
   $result=dolibarr_set_const($db, "THELIA_WS_URL",$_POST["THELIA_WS_URL"],'chaine',0,'',$conf->entity);
   $result=dolibarr_set_const($db, "THELIA_URL",$_POST["THELIA_URL"],'chaine',0,'',$conf->entity);
   $result=dolibarr_set_const($db, "THELIA_ADMIN_URL",$_POST["THELIA_ADMIN_URL"],'chaine',0,'',$conf->entity);
   
   if ($result >= 0)
   {
      $mesg='<div class="ok">'.$langs->trans("SetupSaved").'</div>';
   }
   else
   {
      dol_print_error($db);
   }
}





/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/


llxHeader('THeliaSetup','','');

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';

print_fiche_titre($langs->trans("Thelia"),$linkback,'setup');

print '<form method="post" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="setvalue">';

$var=true;

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Parameter").'</td>';
print '<td>'.$langs->trans("Value").'</td>';
print "</tr>\n";


$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("TheliaSetupWSDirectory").'</td><td>';
print '<input size="64" type="text" name="THELIA_WS_URL" value="'.$conf->global->THELIA_WS_URL.'">';
print '<br />'.$langs->trans("Example").': https://www.votresite.com/ws_server/';
print '</td></tr>';


$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("TheliaSetupUrl").'</td><td>';
print '<input size="64" type="text" name="THELIA_URL" value="'.$conf->global->THELIA_URL.'">';
print '<br />'.$langs->trans("Example").': http://www.votresite.com/';

print '</td></tr>';

$var=!$var;
print '<tr '.$bc[$var].'><td>';
print $langs->trans("TheliaSetupBoUrl").'</td><td>';
print '<input size="64" type="text" name="THELIA_ADMIN_URL" value="'.$conf->global->THELIA_ADMIN_URL.'">';
print '<br />'.$langs->trans("Example").': https://www.votresite.com/admin/';
print '</td></tr>';

print '<tr><td colspan="2" align="center"><input type="submit" class="button" value="'.$langs->trans("Modify").'"></td></tr>';
print '</table></form>';



// End of page
$db->close();
llxFooter('$Date: 2010/01/01 19:20:24 $ - $Revision: 1.1 $');
?>
