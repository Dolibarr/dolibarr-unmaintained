<?PHP
/* Copyright (C) 2005-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * $Id: commercialca.php,v 1.1 2009/10/20 16:19:28 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/stats/commerciaux/commercialca.php,v $
 *
 */
require("./pre.inc.php");

if (!$user->rights->telephonie->lire) accessforbidden();

llxHeader('','Telephonie - Statistiques - Commerciaux');

/*
 *
 */
$h = 0;

$year = strftime("%Y",time());
if (strftime("%m",time()) == 1)
{
  $year = $year -1;
}
if ($_GET["year"] > 0)
{
  $year = $_GET["year"];
}
$head[$h][0] = DOL_URL_ROOT.'/telephonie/stats/commerciaux/index.php';
$head[$h][1] = "Global";
$h++;

if ($_GET["commid"])
{
  $comm = new User($db, $_GET["commid"]);
  $comm->fetch();

  $head[$h][0] = DOL_URL_ROOT.'/telephonie/stats/commerciaux/commercial.php?commid='.$comm->id;
  $head[$h][1] = $comm->fullname;
  $h++;

  $head[$h][0] = DOL_URL_ROOT.'/telephonie/stats/commerciaux/commercialca.php?commid='.$comm->id;
  $head[$h][1] = "CA";
  $hselected = $h;
  $h++;

  $head[$h][0] = DOL_URL_ROOT.'/telephonie/stats/commerciaux/lignes.php?commid='.$comm->id;
  $head[$h][1] = "Lignes";
  $h++;

  dol_fiche_head($head, $hselected, "Commerciaux");
  stat_year_bar($year);

  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';

  print '<tr><td valign="top" width="70%">';

  print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=telephoniegraph&file=commercials/'.$comm->id.'/gain.mensuel.'.$year.'.png" alt="Gain mensuel" title="Gain mensuel">'."\n";

  print '</td><td width="30%" valign="top">';

  print '<table class="border" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre">';
  print '<td>Mois</td><td align="right">Marge</td></tr>';
  
  $sql = "SELECT legend, valeur";
  $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_stats";
  $sql .= " WHERE graph  = 'commercial.gain.mensuel.".$_GET["commid"]."'";
  $sql .= " AND legend like '".$year."%'";
  $sql .= " ORDER BY legend ASC;";
  $resql = $db->query($sql);
  
  if ($resql)
    {
      while ($row = $db->fetch_row($resql))
	{
	  $var=!$var;	  
	  print "<tr $bc[$var]><td>".$row[0].'</td>';  
	  print '<td align="right">'.price($row[1]).'</td></tr>';
	}
      $db->free($resql);
    }
  else 
    {
      print $db->error() . ' ' . $sql;
    }
  print '</table>';

  print '</td></tr><tr><td valign="top">';

  print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=telephoniegraph&file=commercials/'.$comm->id.'/ca.mensuel.'.$year.'.png" alt="Chiffre d\'affaire mensuel" title="Chiffre d\'affaire mensuel"><br /><br />'."\n";
  
  print '</td><td width="30%" valign="top">';

  print '<table class="border" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre">';
  print '<td>Mois</td><td align="right">CA</td></tr>';
  
  $sql = "SELECT legend, valeur";
  $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_stats";
  $sql .= " WHERE graph  = 'commercial.ca.mensuel.".$_GET["commid"]."'";
  $sql .= " AND legend like '".$year."%'";
  $sql .= " ORDER BY legend ASC;";
  $resql = $db->query($sql);
  
  if ($resql)
    {
      while ($row = $db->fetch_row($resql))
	{
	  $var=!$var;	  
	  print "<tr $bc[$var]><td>".$row[0].'</td>';  
	  print '<td align="right">'.price($row[1]).'</td></tr>';
	}
      $db->free($resql);
    }
  else 
    {
      print $db->error() . ' ' . $sql;
    }
  print '</table>';
  print '</td></tr>';
  print '</table>';
  
  $db->close(); 
}

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:28 $ r&eacute;vision $Revision: 1.1 $</em>");
?>
