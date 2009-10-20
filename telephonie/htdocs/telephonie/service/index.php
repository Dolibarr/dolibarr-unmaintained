<?PHP
/* Copyright (C) 2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * $Id: index.php,v 1.1 2009/10/20 16:19:26 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/service/index.php,v $
 *
 */
require("./pre.inc.php");

if (!$user->rights->telephonie->service->lire) accessforbidden();

llxHeader('','Telephonie - Services');

/*
 *
 *
 *
 */

print_titre("Services");

print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';

print '<tr><td width="30%" valign="top">';

$sql = "SELECT s.rowid , libelle, count(cs.rowid) ";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_service as s";
$sql .= " , ".MAIN_DB_PREFIX."telephonie_contrat_service as cs";
$sql .= " WHERE cs.fk_service = s.rowid";
$sql .= " GROUP by s.rowid DESC";
//print $sql;
if ($db->query($sql))
{
  $num = $db->num_rows();
  $i = 0;

  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre"><td>Libell�</td><td>-</td>';
  print "</tr>\n";
  $var=True;

  while ($i < min($num,$conf->liste_limit))
    {
      $row = $db->fetch_row();
      print '<tr><td><a href="fiche.php?id='.$row[0].'">'.stripslashes($row[1])."</a></td>";
      print '<td>'.$row[2]."</td></tr>";
      $i++;
    }



  print "</table>";
  $db->free();
}
else 
{
  print $db->error() . ' ' . $sql;
}

print '<br />';
print '</td><td width="70%" valign="top">&nbsp;';

print '</td>';


print '</table>';



$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:26 $ r&eacute;vision $Revision: 1.1 $</em>");
?>
