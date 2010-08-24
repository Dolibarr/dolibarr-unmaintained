<?PHP
/* Copyright (C) 2004-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * $Id: liste.php,v 1.2 2010/08/24 20:27:25 grandoc Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/contrat/liste.php,v $
 *
 */
require("./pre.inc.php");

$page = $_GET["page"];
$sortorder = $_GET["sortorder"];
$sortfield = $_GET["sortfield"];

llxHeader('','Telephonie - Contrats - Liste');

/*
 *
 *
 */

if ($page == -1) { $page = 0 ; }
if ($sortorder == "") $sortorder="ASC";
if ($sortfield == "") $sortfield="c.statut";

$offset = $conf->liste_limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;

/*
 * Mode Liste
 *
 *
 *
 */
$sql = "SELECT c.rowid, c.ref, s.rowid as socid, c.statut, s.nom ";
$sql .= ", sf.rowid as sfidp, sf.nom as sfnom";
$sql .= ", sa.rowid as saidp, sa.nom as sanom";
$sql .= " FROM ".MAIN_DB_PREFIX."societe as s";
$sql .= " , ".MAIN_DB_PREFIX."societe as sf";
$sql .= " , ".MAIN_DB_PREFIX."societe as sa";
$sql .= " , ".MAIN_DB_PREFIX."societe_perms as sp";
$sql .= " , ".MAIN_DB_PREFIX."telephonie_contrat as c";

$sql .= " WHERE c.fk_client_comm = s.rowid";
$sql .= " AND c.fk_soc = sa.rowid";
$sql .= " AND c.fk_soc_facture = sf.rowid";

$sql .= " AND c.fk_client_comm = sp.fk_soc";
$sql .= " AND sp.fk_user = ".$user->id." AND sp.pread = 1";

if ($_GET["search_contrat"])
{
  $sel = urldecode($_GET["search_contrat"]);
  $sql .= " AND c.ref LIKE '%".$sel."%'";
}

if ($_GET["search_client"])
{
  $sel = urldecode($_GET["search_client"]);
  $sql .= " AND s.nom LIKE '%".$sel."%'";
}

if ($_GET["search_client_facture"])
{
  $sel =urldecode($_GET["search_client_facture"]);
  $sql .= " AND sf.nom LIKE '%".$sel."%'";
}

if (dol_strlen($_GET["statut"]))
{
  $sql .= " AND c.statut = ".$_GET["statut"];
}

$sql .= " ORDER BY $sortfield $sortorder " . $db->plimit($conf->liste_limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
  $num = $db->num_rows();
  $i = 0;
  
  $urladd= "&amp;statut=".$_GET["statut"];

  print_barre_liste("Contrats", $page, "liste.php", $urladd, $sortfield, $sortorder, '', $num);
  print"\n<!-- debut table -->\n";
  print '<form action="liste.php" method="GET">'."\n";
  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre">';

  print_liste_field_titre("Ref","liste.php","c.ref");
  print_liste_field_titre("Client","liste.php","s.nom");
  print_liste_field_titre("Client (Agence/Filiale)","liste.php","sa.nom");

  print '<td>Client factur�</td>';
  print '<td align="center">-</td>';

  print "</tr>\n";

  print '<tr class="liste_titre">';
  print '<td><input type="text" name="search_contrat" value="'. $_GET["search_contrat"].'" size="10"></td>'; 
  print '<td><input type="text" name="search_client" value="'. $_GET["search_client"].'" size="10"></td>';
  print '<td><input type="text" name="search_client_agence" value="'. $_GET["search_client_agence"].'" size="10"></td>';
  print '<td><input type="text" name="search_client_facture" value="'. $_GET["search_client_facture"].'" size="10"></td>';


  print '<td><input type="submit" class="button" value="'.$langs->trans("Search").'""></td>';
  print '</tr>';

  $var=True;

  while ($i < min($num,$conf->liste_limit))
    {
      $obj = $db->fetch_object();
      $var=!$var;

      print "<tr $bc[$var]><td>";
      print '<img src="statut'.$obj->statut.'.png">&nbsp;';
      print '<a href="'.DOL_URL_ROOT.'/telephonie/contrat/fiche.php?id='.$obj->rowid.'">';
      print img_file();      
      print '</a>&nbsp;';

      print '<a href="fiche.php?id='.$obj->rowid.'">'.$obj->ref."</a></td>\n";

      print '<td><a href="'.DOL_URL_ROOT.'/telephonie/client/fiche.php?id='.$obj->socid.'">'.stripslashes($obj->nom).'</a></td>';

      print '<td><a href="'.DOL_URL_ROOT.'/telephonie/client/fiche.php?id='.$obj->socid.'">'.stripslashes($obj->sanom).'</a></td>';
      print '<td><a href="'.DOL_URL_ROOT.'/soc.php?socid='.$obj->sfidp.'">'.stripslashes($obj->sfnom).'</a></td>';

      print '<td align="center">-</td>';
      print "</tr>\n";
      $i++;
    }
  print "</table>";
  print '</form>';
  $db->free();
}
else 
{
  print $db->error() . ' ' . $sql;
}

$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2010/08/24 20:27:25 $ r&eacute;vision $Revision: 1.2 $</em>");
?>
