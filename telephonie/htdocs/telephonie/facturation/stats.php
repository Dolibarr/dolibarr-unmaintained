<?PHP
/* Copyright (C) 2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 *
 * $Id: stats.php,v 1.1 2009/10/20 16:19:21 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/facturation/stats.php,v $
 *
 */
require("./pre.inc.php");

if (!$user->rights->telephonie->facture->ecrire) accessforbidden();


llxHeader();

$messages = array();

require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/ProcessGraphLignes.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/ProcessGraphContrats.class.php");
$obj = new ProcessGraphLignes($db);
$obj->GenerateAll();
$messages = array_merge($messages, $obj->messages);
$obj = new ProcessGraphContrats($db);
$obj->GenerateAll();
$messages = array_merge($messages, $obj->messages);

/*
 * S�curit� acc�s client
 */

print_barre_liste("Generation des statistiques", $page, "stats.php", "", $sortfield, $sortorder, '', $num);

print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
print '<tr class="liste_titre">';
print '<td colspan="2">Messages</td>';

print "</tr>\n";

$var=True;

foreach ($messages as $message)
{
  $var=!$var;
  print "<tr $bc[$var]>";

  if (is_array($message))
    {
      $func = 'img_'.$message[0];
      print '<td>'.$func().'</td>';
      print '<td width="99%">'.$message[1].'</td></tr>';
    }
  else
    {
      print '<td>'.img_info().'</td>';
      print '<td width="99%">'.$message.'</td></tr>';
    }
}
print "</table>";

$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:21 $ r&eacute;vision $Revision: 1.1 $</em>");
?>
