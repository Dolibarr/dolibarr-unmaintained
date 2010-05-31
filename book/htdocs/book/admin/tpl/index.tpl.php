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
 *  \file       htdocs/book/admin/tpl/index.tpl.php
 *  \ingroup    book
 *  \brief      Page d'administration/configuration du module Book
 *  \version    $Id: index.tpl.php,v 1.1 2010/05/31 15:28:24 pit Exp $
 */



llxHeader('',$langs->trans("BookSetup"));

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("BookSetup"),$linkback,'setup');

// Description of parameters
print '<br />'.$langs->trans("ParametersDescription").'<br /><br />';

$var=true;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print "  <td>".$langs->trans("Parameters")."</td>\n";
print "  <td align=\"left\" width=\"60\">".$langs->trans("Value")."</td>\n";
print "  <td width=\"80\">&nbsp;</td></tr>\n";

/*
 * Various parameter form
 */

// BOOK_CONTRACT_BILL_SLOGAN parameter
$var=!$var;
print "<form method=\"post\" action=\"index.php\">";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print "<input type=\"hidden\" name=\"action\" value=\"BOOK_CONTRACT_BILL_SLOGAN\">";
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("BookContractBillSloganDescription").'</td>';
print '<td width="60" align="left">';
print "<input size=\"50\" type=\"text\" class=\"flat\" name=\"value\" value=\"".$conf->global->BOOK_CONTRACT_BILL_SLOGAN."\">";
print '</td><td align="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print "</td>";
print '</tr>';
print '</form>';

// BOOK_CONTRACT_BILL_SOCIETE_INFOS parameter
$var=!$var;
print "<form method=\"post\" action=\"index.php\">";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print "<input type=\"hidden\" name=\"action\" value=\"BOOK_CONTRACT_BILL_SOCIETE_INFOS\">";
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("BookContractBillSocieteInfosDescription").'</td>';
print '<td width="60" align="left">';
print "<input size=\"50\" type=\"text\" class=\"flat\" name=\"value\" value=\"".$conf->global->BOOK_CONTRACT_BILL_SOCIETE_INFOS."\">";
print '</td><td align="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print "</td>";
print '</tr>';
print '</form>';

// BOOK_CONTRACT_BILL_OTHER_INFOS parameter
$var=!$var;
print "<form method=\"post\" action=\"index.php\">";
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print "<input type=\"hidden\" name=\"action\" value=\"BOOK_CONTRACT_BILL_OTHER_INFOS\">";
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("BookContractBillOtherInfosDescription").'</td>';
print '<td width="60" align="left">';
print "<input size=\"50\" type=\"text\" class=\"flat\" name=\"value\" value=\"".$conf->global->BOOK_CONTRACT_BILL_OTHER_INFOS."\">";
print '</td><td align="center">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print "</td>";
print '</tr>';
print '</form>';


$db->close();

llxFooter('$Date: 2010/05/31 15:28:24 $ - $Revision: 1.1 $');
?>
