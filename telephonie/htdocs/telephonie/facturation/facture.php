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
 * $Id: facture.php,v 1.1 2009/10/20 16:19:21 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/facturation/facture.php,v $
 *
 */
require("./pre.inc.php");

if (!$user->rights->telephonie->facture->lire) accessforbidden();

$page = $_GET["page"];
$sortorder = $_GET["sortorder"];
$sortfield = $_GET["sortfield"];

llxHeader();

/*
 * S�curit� acc�s client
 */
if ($user->societe_id > 0) 
{
  $action = '';
  $socid = $user->societe_id;
}

if ($sortorder == "") {
  $sortorder="DESC";
}
if ($sortfield == "") {
  $sortfield="rowid";
}

/*
 * Recherche
 *
 *
 */

if ($page == -1) { $page = 0 ; }

$offset = $conf->liste_limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;

/*
 * Mode Liste
 *
 */

print_barre_liste("Factures telephonie", $page, "facture.php", "", $sortfield, $sortorder, '', $num);

print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
print '<tr class="liste_titre">';
print '<td>Ligne</td><td>Date</td>';
print '<td align="right">Montant HT</td><td align="right">Montant Fournisseur</td>';
print "</tr>\n";

print '<tr class="liste_titre">';
print '<form action="facture.php" method="GET">';
print '<td><input type="text" name="search_ligne" value="'. $_GET["search_ligne"].'" size="10"></td>'; 
print '<td>&nbsp;</td><td>&nbsp;</td>';
print '<td><input type="submit" class="button" value="'.$langs->trans("Search").'"></td>';

$var=True;

$sql = "SELECT fk_contrat,ligne,date,cout_vente,fourn_montant";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_facture";
$sql .= " WHERE 1=1";
if ($_GET["search_ligne"])
{
  $sel =urldecode($_GET["search_ligne"]);
  $sel = ereg_replace("\.","",$sel);
  $sel = ereg_replace(" ","",$sel);
  $sql .= " AND ligne LIKE '%".$sel."%'";
}
$sql .= " ORDER BY $sortfield $sortorder " . $db->plimit($conf->liste_limit+1, $offset);
$resql = $db->query($sql);

while ($obj = $db->fetch_object($resql))
{
  $var=!$var;
  
  print "<tr $bc[$var]>";
  print '<td>'.$obj->ligne."</td>\n";
  print '<td>'.$obj->date."</td>\n";
  print '<td align="right">'.price($obj->cout_vente)."</td>\n";
  print '<td align="right">'.price($obj->fourn_montant)."</td>\n";


}
print "</table>";


$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:21 $ r&eacute;vision $Revision: 1.1 $</em>");
?>
