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
 * $Id: analyse.php,v 1.1 2009/10/20 16:19:26 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/stats/communications/analyse.php,v $
 *
 */
require("./pre.inc.php");

$page = $_GET["page"];
$sortorder = $_GET["sortorder"];

if (!$user->rights->telephonie->lire)
  accessforbidden();

llxHeader('','Telephonie - Statistiques');

/*
 * S�curit� acc�s client
 */
if ($user->societe_id > 0) 
{
  $action = '';
  $socid = $user->societe_id;
}


$h = 0;

$head[$h][0] = DOL_URL_ROOT.'/telephonie/stats/communications/index.php';
$head[$h][1] = "Global";
$h++;

$head[$h][0] = DOL_URL_ROOT.'/telephonie/stats/communications/lastmonth.php';
$head[$h][1] = "Dur�e";
$h++;
$head[$h][0] = DOL_URL_ROOT.'/telephonie/stats/communications/destmonth.php';
$head[$h][1] = "Destinations";
$h++;

$head[$h][0] = DOL_URL_ROOT.'/telephonie/stats/communications/rentabilite.php';
$head[$h][1] = "Rentabilite";
$h++;

$head[$h][0] = DOL_URL_ROOT.'/telephonie/stats/communications/analyse.php';
$head[$h][1] = "Analyse";
$hselected = $h;
$h++;


dol_fiche_head($head, $hselected, "Communications");

print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">'."\n";

print '<tr><td width="50%" valign="top">';

print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=telephoniegraph&file=communications/heure_appel_nb.png" alt="Heure appel"><br /><br />';

print '</td><td valign="top" width="50%">';

print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=telephoniegraph&file=communications/joursemaine_nb.png" alt="Jour de la semaine"><br /><br />';

print '</td></tr>';

print '</table>';

$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:26 $ r&eacute;vision $Revision: 1.1 $</em>");
?>
