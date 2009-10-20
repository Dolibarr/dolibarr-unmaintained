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
 * $Id: liste.php,v 1.1 2009/10/20 16:19:28 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/adsl/liste.php,v $
 *
 */
require("./pre.inc.php");

if (!$user->rights->telephonie->adsl->lire) accessforbidden();

$page = $_GET["page"];
$sortorder = $_GET["sortorder"];
$sortfield = $_GET["sortfield"];

llxHeader('','Telephonie - Ligne - Liste');
/*
 * S�curit� acc�s client
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

$offset = $conf->liste_limit * $page ;
if ($sortorder == "") $sortorder="ASC";
if ($sortfield == "") $sortfield="la.statut ASC, s.nom";

/*
 * Mode Liste
 *
 *
 */

$sql = "SELECT la.rowid, fk_client, s.nom as nom, la.numero_ligne, la.statut, t.intitule";
$sql .= " , s.rowid as socid";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_adsl_ligne as la";
$sql .= " ,  ".MAIN_DB_PREFIX."societe as s";
$sql .= " ,  ".MAIN_DB_PREFIX."telephonie_adsl_type as t";
$sql .= " WHERE la.fk_client = s.rowid";
$sql .= " AND t.rowid = la.fk_type";

if ($_GET["search_ligne"])
{
  $sel =urldecode($_GET["search_ligne"]);
  $sel = ereg_replace("\.","",$sel);
  $sel = ereg_replace(" ","",$sel);
  $sql .= " AND la.numero_ligne LIKE '%".$sel."%'";
}

if ($_GET["search_client"])
{
  $sel =urldecode($_GET["search_client"]);
  $sql .= " AND s.nom LIKE '%".$sel."%'";
}

if (strlen($_GET["statut"]))
{
  $sql .= " AND la.statut = ".$_GET["statut"];
}

$sql .= " ORDER BY $sortfield $sortorder " . $db->plimit($conf->liste_limit+1, $offset);

$resql = $db->query($sql);
if ($resql)
{
  $num = $db->num_rows($resql);
  $i = 0;
  
  $urladd= "&amp;statut=".$_GET["statut"];

  print_barre_liste("Liaisons ADSL", $page, "liste.php", $urladd, $sortfield, $sortorder, '', $num);
  print"\n<!-- debut table -->\n";
  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre">';

  print_liste_field_titre("Ligne","liste.php","l.ligne");
  print_liste_field_titre("Client","liste.php","s.nom");

  print '<td>Type</td>';
  print '<td align="center">Statut</td>';

  print "</tr>\n";
  
  print '<tr class="liste_titre">';
  print '<form action="liste.php" method="GET">';
  print '<td><input type="text" name="search_ligne" value="'. $_GET["search_ligne"].'" size="10"></td>'; 
  print '<td><input type="text" name="search_client" value="'. $_GET["search_client"].'" size="10"></td>';
  print '<td>&nbsp;</td>';

  print '<td><input type="submit" value="Chercher"></td>';
  print '</form>';
  print '</tr>';
  

  $var=True;

  $ligne = new LigneAdsl($db);

  while ($i < min($num,$conf->liste_limit))
    {
      $obj = $db->fetch_object($resql);
      $var=!$var;

      print "<tr $bc[$var]><td>";

      print '<img src="./statut'.$obj->statut.'.png">&nbsp;';
      
      print '<a href="'.DOL_URL_ROOT.'/telephonie/adsl/fiche.php?id='.$obj->rowid.'">';
      print img_file();      
      print '</a>&nbsp;';

      print '<a href="fiche.php?id='.$obj->rowid.'">'.dol_print_phone($obj->numero_ligne,0,0,true)."</a></td>\n";

      print '<td><a href="'.DOL_URL_ROOT.'/compta/fiche.php?socid='.$obj->socid.'">'.img_object($langs->trans("Fiche Compta"),"bill")."</a> ";

      print '&nbsp;<a href="'.DOL_URL_ROOT.'/telephonie/comm/fiche.php?socid='.$obj->socid.'">'.$obj->nom.'</a></td>';
      print '<td>'.$obj->intitule.'</td>';

      print '<td align="center">'.$ligne->statuts[$obj->statut]."</td>\n";

      print "</tr>\n";
      $i++;
    }
  print "</table>";
  $db->free($resql);
}
else 
{
  print $db->error($resql) . ' ' . $sql;
}

$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:28 $ r&eacute;vision $Revision: 1.1 $</em>");
?>
