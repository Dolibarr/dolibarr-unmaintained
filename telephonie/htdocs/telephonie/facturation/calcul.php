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
 * $Id: calcul.php,v 1.1 2009/10/20 16:19:21 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/facturation/calcul.php,v $
 *
 */
require("./pre.inc.php");
require_once DOL_DOCUMENT_ROOT.'/telephonie/facturation/FacturationCalcul.class.php';

if (!$user->rights->telephonie->facture->ecrire) accessforbidden();

llxHeader();

/*
 * S�curit� acc�s client
 */
if ($user->societe_id > 0) 
{
  $action = '';
  $socid = $user->societe_id;
}
/*
 *
 */
print_barre_liste("Calcul des CDR", $page, "calcul.php", "", $sortfield, $sortorder, '', $num);

print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
print '<tr class="liste_titre">';
print '<td colspan="2">Messages</td></tr>';

$var=True;

$obj = new FacturationCalcul($db);
$obj->Calcul();

foreach ($obj->messages as $message)
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
