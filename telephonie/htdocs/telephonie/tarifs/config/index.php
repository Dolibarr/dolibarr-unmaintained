<?PHP
/* Copyright (C) 2005-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * $Id: index.php,v 1.1 2009/10/20 16:19:25 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/tarifs/config/index.php,v $
 *
 */
require("./pre.inc.php");

llxHeader();

/*
 * Securite acces client
 */
if ($user->societe_id > 0) 
{
  $action = '';
  $socid = $user->societe_id;
}



/*
 * Recherche
 *
 *
 */
if ($mode == 'search') {
  if ($mode-search == 'soc') {
    $sql = "SELECT s.rowid as socid FROM ".MAIN_DB_PREFIX."societe as s ";
    $sql .= " WHERE lower(s.nom) like '%".strtolower($socname)."%'";
  }
      
  if ( $db->query($sql) ) {
    if ( $db->num_rows() == 1) {
      $obj = $db->fetch_object(0);
      $socid = $obj->socid;
    }
    $db->free();
  }
}

$page = $_GET["page"];
$sortorder = $_GET["sortorder"];
$sortfield = $_GET["sortfield"];

if ($sortorder == "") $sortorder="ASC";
if ($sortfield == "") $sortfield="t.libelle ASC, d.rowid ";

$offset = $conf->liste_limit * $page ;

/*
 * Mode Liste
 *
 *
 *
 */

$sql = "SELECT d.rowid as grille, d.libelle as tarif_desc, d.type_tarif";
$sql .= " , t.libelle as tarif, t.rowid as tarif_id";
$sql .= " , m.temporel, m.fixe";
$sql .= " , u.login";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_tarif_grille as d";
$sql .= ","    . MAIN_DB_PREFIX."telephonie_tarif_grille_rights as r";
$sql .= ","    . MAIN_DB_PREFIX."telephonie_tarif_montant as m";
$sql .= ","    . MAIN_DB_PREFIX."telephonie_tarif as t";
$sql .= ","    . MAIN_DB_PREFIX."user as u";

$sqlc .= " WHERE d.rowid = m.fk_tarif_desc";
$sqlc .= " AND m.fk_tarif = t.rowid";
$sqlc .= " AND m.fk_user = u.rowid";

$sqlc .= " AND d.rowid = r.fk_grille";
$sqlc .= " AND r.fk_user =".$user->id;
$sqlc .= " AND r.pread = 1";

if ($_GET["search_libelle"])
{
  $sqlc .=" AND t.libelle LIKE '%".$_GET["search_libelle"]."%'";
}

if ($_GET["search_grille"])
{
  $sqlc .=" AND d.libelle LIKE '%".$_GET["search_grille"]."%'";
}

if ($_GET["type"])
{
  $sqlc .= " AND d.type_tarif = '".$_GET["type"]."'";
}

$sql = $sql . $sqlc . " ORDER BY $sortfield $sortorder " . $db->plimit($conf->liste_limit+1, $offset);


$resql = $db->query($sql);
if ($resql)
{
  $num = $db->num_rows($resql);
  $i = 0;
  
  print_barre_liste("Tarifs", $page, "index.php", "&type=".$_GET["type"], $sortfield, $sortorder, '', $num);

  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre">';

  print_liste_field_titre("Grille","index.php","d.libelle");

  print_liste_field_titre("Tarif","index.php","t.libelle", "&type=".$_GET["type"]);

  print_liste_field_titre("Cout / min","index.php","temporel", "&type=".$_GET["type"]);
  print "</td>";
  print "<td>Cout fixe</td>";
  print '<td align="center">Type</td><td align="center">User</td>';
  print "</tr>\n";

  print '<tr class="liste_titre">';
  print '<form action="index.php" method="GET">';
  print '<input type="hidden" name="type" value="'.$_GET["type"].'">';
  print '<td><input type="text" name="search_grille" size="10" value="'.$_GET["search_grille"].'"></td>';
  print '<td><input type="text" name="search_libelle" size="20" value="'.$_GET["search_libelle"].'"></td>';
  print '<td>&nbsp;</td>';
  print '<td>&nbsp;</td>';
  print '<td>&nbsp;</td>';
  print '<td><input type="submit" value="'.$langs->trans("Search").'"></td>';
  print '</form>';
  print '</tr>';

  $var=True;

  while ($i < min($num,$conf->liste_limit))
    {
      $obj = $db->fetch_object($resql);
      $var=!$var;

      print "<tr $bc[$var]>";

      print '<td><a href="grille.php?id='.$obj->grille.'">';
      print $obj->tarif_desc."</td>\n";
      print '<td><a href="tarif.php?id='.$obj->tarif_id.'">';
      print $obj->tarif."</a></td>\n";
      print "<td>".sprintf("%01.4f",$obj->temporel)."</td>\n";
      print "<td>".sprintf("%01.4f",$obj->fixe)."</td>\n";
      print '<td align="center">'.$obj->type_tarif."</td>\n";
      print '<td align="center">'.$obj->login."</td>\n";
      print "</tr>\n";
      $i++;
    }
  print "</table>";
  $db->free();
}
else 
{
  print $db->error() . ' ' . $sql;
}

$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:25 $ r&eacute;vision $Revision: 1.1 $</em>");
?>
