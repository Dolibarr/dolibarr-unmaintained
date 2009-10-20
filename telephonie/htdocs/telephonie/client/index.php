<?PHP
/* Copyright (C) 2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * $Id: index.php,v 1.1 2009/10/20 16:19:27 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/client/index.php,v $
 *
 */
require("./pre.inc.php");

if (!$user->rights->telephonie->lire)
  accessforbidden();

llxHeader('','Telephonie - Clients');

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
 *
 *
 */

print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';

print '<tr><td width="30%" valign="top">';

print '<form method="GET" action="'.DOL_URL_ROOT.'/telephonie/client/liste.php">';
print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
print '<tr class="liste_titre"><td>Recherche client</td>';
print "</tr>\n";
print "<tr $bc[1]>";
print '<td>Nom <input name="search_client" size="12"></td></tr>';
print '</table>';

print '<br />';

$sql = "SELECT distinct s.rowid as socid ";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_societe_ligne as l";
$sql .= " , ".MAIN_DB_PREFIX."societe as s";
$sql .= " WHERE s.rowid = l.fk_client_comm ";
$sql .= " AND l.fk_commercial_suiv = ".$user->id;
$resql = $db->query($sql);
if ($resql)
{
  $num = $db->num_rows($resql);
  $i = 0;

  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre"><td>Clients</td><td valign="center">Nb</td>';
  print "</tr>\n";
  $var=True;

  $row = $db->fetch_row($resql);

  print "<tr $bc[$var]>";
  print '<td><a href="my.php">Mes clients suivis</a></td>';
  print "<td>".$num."</td>\n";
  print "</tr>\n";
  print "</table>";
  $db->free();
}
else 
{
  print $db->error() . ' ' . $sql;
}

print '<br />';

/*
 * Liste
 *
 */
$sql = "SELECT s.rowid as socid, s.nom, max(sc.datec) as dam";
$sql .= " FROM ".MAIN_DB_PREFIX."societe as s";
$sql .= ",".MAIN_DB_PREFIX."societe_consult as sc";
$sql .= " WHERE s.rowid = sc.fk_soc";
$sql .= " AND sc.fk_user = ".$user->id;
$sql .= " GROUP BY s.rowid";
$sql .= " ORDER BY dam DESC LIMIT 10";

$resql = $db->query($sql);
if ($resql)
{
  $num = $db->num_rows($resql);
  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre"><td>'.min(10,$num).' derni�res fiches clients consult�es</td></tr>';
  $var=True;

  while ($obj = $db->fetch_object($resql))
    {
      print "<tr $bc[$var]><td>";
      print '<a href="'.DOL_URL_ROOT.'/telephonie/client/fiche.php?id='.$obj->socid.'">';
      print img_file();      
      print '</a>&nbsp;';
      $nom = $obj->nom;
      if (strlen($obj->nom) > 33)
	$nom = substr($obj->nom,0,30)."...";

      print '<a href="'.DOL_URL_ROOT.'/telephonie/client/fiche.php?id='.$obj->socid.'">'.stripslashes($nom).'</a></td>';

      print "</tr>\n";
      $var=!$var;
    }
  print "</table>";
  $db->free($resql);
}
else 
{
  print $db->error() . ' ' . $sql;
}


print '</td><td valign="top" width="70%" rowspan="3">';

/*
 * Liste
 *
 */
$sql = "SELECT s.rowid as socid, s.nom, count(l.ligne) as ligne";
$sql .= " FROM ".MAIN_DB_PREFIX."societe as s,".MAIN_DB_PREFIX."telephonie_societe_ligne as l";
$sql .= ",".MAIN_DB_PREFIX."societe_perms as sp";

$sql .= " WHERE l.fk_client_comm = s.rowid ";
$sql .= " AND s.rowid = sp.fk_soc";
$sql .= " AND sp.fk_user = ".$user->id." AND sp.pread = 1";
$sql .= " GROUP BY s.rowid";
$sql .= " ORDER BY s.datec DESC LIMIT 10";

$resql = $db->query($sql);
if ($resql)
{
  $num = $db->num_rows($resql);
  $i = 0;
  
  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre"><td>'.min(10,$num).' derniers nouveaux clients</td>';
  print '<td width="25%" align="center">Nb Lignes';
  print "</td></tr>\n";

  $var=True;

  $ligne = new LigneTel($db);

  while ($i < $num)
    {
      $obj = $db->fetch_object($resql);	
      $var=!$var;

      print "<tr $bc[$var]><td>";

      print '<a href="'.DOL_URL_ROOT.'/telephonie/client/fiche.php?id='.$obj->socid.'">';
      print img_file();      
      print '</a>&nbsp;';

      print '<a href="'.DOL_URL_ROOT.'/telephonie/client/fiche.php?id='.$obj->socid.'">'.stripslashes($obj->nom).'</a></td>';
      print '<td align="center">'.$obj->ligne."</td>\n";

      print "</tr>\n";
      $i++;
    }
  print "</table>";
  $db->free($resql);
}
else 
{
  print $db->error() . ' ' . $sql;
}

print "<br />";
/* Commentaires */

$sql = "SELECT s.rowid as socid, s.nom, c.commentaire";
$sql .= " FROM ".MAIN_DB_PREFIX."societe as s,".MAIN_DB_PREFIX."telephonie_societe_commentaire as c";
$sql .= ",".MAIN_DB_PREFIX."societe_perms as sp";

$sql .= " WHERE c.fk_soc = s.rowid ";
$sql .= " AND s.rowid = sp.fk_soc";
$sql .= " AND sp.fk_user = ".$user->id." AND sp.pread = 1";
$sql .= " ORDER BY c.datec DESC LIMIT 10";

$resql = $db->query($sql);
if ($resql)
{
  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre"><td colspan="2">'.min(10,$num).' derniers commentaires</td></tr>';

  while ($obj = $db->fetch_object($resql))
    {
      $var=!$var;
      print "<tr $bc[$var]><td>";
      print '<a href="'.DOL_URL_ROOT.'/telephonie/client/fiche.php?id='.$obj->socid.'">';
      print img_file();      
      print '</a>&nbsp;';
      $nom = $obj->nom;
      if (strlen($obj->nom) > 33)
	$nom = substr($obj->nom,0,30)."...";
      print '<a href="'.DOL_URL_ROOT.'/telephonie/client/fiche.php?id='.$obj->socid.'">'.stripslashes($nom).'</a></td>';
      print '<td>'.stripslashes($obj->commentaire)."</td>\n";
      print "</tr>\n";
    }
  print "</table>";
  $db->free($resql);
}
else 
{
  print $db->error() . ' ' . $sql;
}

print '</td></tr>';
print '</table>';

$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:27 $ r&eacute;vision $Revision: 1.1 $</em>");
?>
