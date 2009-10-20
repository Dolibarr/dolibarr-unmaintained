<?PHP
/* Copyright (C) 2004-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * $Id: index.php,v 1.1 2009/10/20 16:19:28 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/adsl/index.php,v $
 *
 */
require("./pre.inc.php");

if (!$user->rights->telephonie->adsl->lire) accessforbidden();

llxHeader('','Telephonie');

/*
 * S�curit� acc�s client
 */
if ($user->societe_id > 0) 
{
  $action = '';
  $socid = $user->societe_id;
}

/*
 * Mode Liste
 *
 */

print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';

print '<tr><td width="30%" valign="top">';

print '<form method="GET" action="'.DOL_URL_ROOT.'/telephonie/adsl/liste.php">';
print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
print '<tr class="liste_titre"><td>Recherche ligne</td>';
print "</tr>\n";
print "<tr $bc[1]>";
print '<td>Num�ro <input name="search_ligne" size="12"></td></tr>';
print '</table>';

print '<br />';


$sql = "SELECT distinct statut, count(*) as cc";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_adsl_ligne as l";
$sql .= " GROUP BY statut";

if ($db->query($sql))
{
  $num = $db->num_rows();
  $i = 0;
  $ligne = new LigneAdsl($db);

  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre"><td>Liaisons Statuts</td><td valign="center">Nb</td>';
  print "<td>&nbsp;</td></tr>\n";
  $var=True;

  while ($i < min($num,$conf->liste_limit))
    {
      $obj = $db->fetch_object();	
      $values[$obj->statut] = $obj->cc;
      $i++;
    }

  foreach ($ligne->statuts_order as $keyo => $statut)
    {
      $var=!$var;
      $key = $statut;
      print "<tr $bc[$var]>";
      print "<td>".$ligne->statuts[$statut]."</td>\n";
      print "<td>".$values[$key]."</td>\n";
      print '<td><a href="'.DOL_URL_ROOT.'/telephonie/adsl/liste.php?statut='.$key.'"><img border="0" src="./statut'.$key.'.png"></a></td>';
      print "</tr>\n";
    }

  print "</table>";
  $db->free();
}
else 
{
  print $db->error() . ' ' . $sql;
}

print '<br />';

/*
 *
 *
 */

print '</td><td width="70%" valign="top">';

print '</td></tr>';

print '</table>';



$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:28 $ r&eacute;vision $Revision: 1.1 $</em>");
?>
