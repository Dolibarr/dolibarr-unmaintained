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
 * $Id: my.php,v 1.1 2009/10/20 16:19:27 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/client/my.php,v $
 *
 */
require("./pre.inc.php");

$page = $_GET["page"];
$sortorder = $_GET["sortorder"];
$sortfield = $_GET["sortfield"];

llxHeader('','Telephonie - Mes clients');
/*
 * S�curit� acc�s client
 */
if ($user->societe_id > 0) 
{
  $action = '';
  $socid = $user->societe_id;
}

if ($sortorder == "") {
  $sortorder="ASC";
}
if ($sortfield == "") {
  $sortfield="s.nom";
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
 *
 */
$sql = "SELECT s.rowid as socid, s.nom, count(l.ligne) as ligne, cs.ca";
$sql .= " FROM ".MAIN_DB_PREFIX."societe as s";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."telephonie_societe_ligne AS l ON l.fk_client_comm = s.rowid";
$sql .= " LEFT JOIN ".MAIN_DB_PREFIX."telephonie_client_stats as cs ON cs.fk_client_comm = s.rowid";
$sql .= " WHERE l.fk_commercial_suiv = ".$user->id; 

if ($_GET["search_client"])
{
  $sel = urldecode($_GET["search_client"]);
  $sql .= " AND s.nom LIKE '%".$sel."%'";
}

$sql .= " GROUP BY s.rowid";
$sql .= " ORDER BY $sortfield $sortorder " . $db->plimit($conf->liste_limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
  $num = $db->num_rows();
  $i = 0;
  
  $urladd= "&amp;statut=".$_GET["statut"];

  $titre = "Les clients de ".$user->fullname;

  print_barre_liste($titre, $page, "my.php", $urladd, $sortfield, $sortorder, '', $num);

  //print '<a href="myxls.php">Exporter dans un tableur</a>';

  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre">';
  print_liste_field_titre("Client","my.php","s.nom","","",' width="50%"');
  print '<td width="25%" align="center">Nb Lignes';
  print '</td><td width="25%" align="right">Chiffre d\'affaire</td>';
  print "</tr>\n";

  print '<tr class="liste_titre">';
  print '<form action="my.php" method="GET">';
  print '<td><input type="text" name="search_client" value="'. $_GET["search_client"].'" size="12"></td>';  
  print '<td><input type="submit" value="Chercher"></td><td>&nbsp;</td>';

  print '</form>';
  print '</tr>';

  $var=True;

  $ligne = new LigneTel($db);

  while ($i < min($num,$conf->liste_limit))
    {
      $obj = $db->fetch_object($i);	
      $var=!$var;

      print "<tr $bc[$var]><td>";

      print '<a href="'.DOL_URL_ROOT.'/telephonie/client/fiche.php?id='.$obj->socid.'">';
      print img_file();      
      print '</a>&nbsp;';

      print '<a href="'.DOL_URL_ROOT.'/telephonie/client/fiche.php?id='.$obj->socid.'">'.$obj->nom.'</a></td>';
      print '<td align="center">'.$obj->ligne."</td>\n";

      print '<td align="right">'.price($obj->ca)." euros HT</td>\n";


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

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:27 $ r&eacute;vision $Revision: 1.1 $</em>");
?>
