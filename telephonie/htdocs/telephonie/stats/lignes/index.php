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
 * $Id: index.php,v 1.1 2009/10/20 16:19:27 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/stats/lignes/index.php,v $
 *
 */
require("./pre.inc.php");

if (!$user->rights->telephonie->lire) accessforbidden();
if (!$user->rights->telephonie->stats->lire) accessforbidden();

llxHeader('','Telephonie - Statistiques - Lignes');

/*
 *
 *
 */

include "./onglets.php";
dol_fiche_head($head, $hselected, "Lignes");

print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';

print '<tr><td valign="top">';

print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=telephoniegraph&file=lignes/lignes.actives.png" alt="Lignes Actives" title="Lignes Actives"><br /><br />'."\n";

print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=telephoniegraph&file=lignes/lignes.statut.png" alt="Lignes Actives" title="Lignes Actives"><br /><br />'."\n";

print '</td><td valign="top">';

print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=telephoniegraph&file=lignes/resiliations.hebdomadaire.png" alt="R�siliation de lignes" title="R�siliation de Lignes"><br /><br />'."\n";

print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=telephoniegraph&file=lignes/rejets.hebdomadaire.png" alt="R�siliation de lignes" title="R�siliation de Lignes"><br /><br />'."\n";

print '</td></tr>';
print '</table>';

$db->close();

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:27 $ r&eacute;vision $Revision: 1.1 $</em>");
?>