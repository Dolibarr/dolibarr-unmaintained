<?PHP
/* Copyright (C) 2004-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * $Id: index.php,v 1.1 2009/10/20 16:19:31 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/fournisseur/index.php,v $
 *
 */
require("./pre.inc.php");

$user->getrights('telephonie');

if (!$user->rights->telephonie->fournisseur->lire)
  accessforbidden();

$page = $_GET["page"];
$sortorder = $_GET["sortorder"];

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
  $sortorder="ASC";
}
if ($sortfield == "") {
  $sortfield="f.nom";
}

if ($page == -1) { $page = 0 ; }

$offset = $conf->liste_limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;

/*
 * Mode Liste
 *
 */

$sql = "SELECT f.rowid, f.nom, f.email_commande, f.commande_active";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_fournisseur as f";

$sql .= " ORDER BY $sortfield $sortorder " . $db->plimit($conf->liste_limit+1, $offset);

$result = $db->query($sql);
if ($result)
{
  $num = $db->num_rows();
  $i = 0;
  
  print_barre_liste("Fournisseurs", $page, "fournisseurs.php", "", $sortfield, $sortorder, '', $num);

  print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
  print '<tr class="liste_titre">';
  print '<td>Id.</td>';
  print_liste_field_titre("Soci�t�","fournisseurs.php","s.nom");
  print '<td>Email de commande</td>';
  print '<td align="center">Commande possible</td>';

  if($user->rights->telephonie->fournisseur->config)
    {
      print '<td>&nbsp;</td>';
    }

  print "</tr>\n";
  $var=True;

  while ($i < min($num,$conf->liste_limit))
    {
      $obj = $db->fetch_object($i);	
      $var=!$var;

      print "<tr $bc[$var]>";
      print "<td>".$obj->rowid."</td>\n";
      print '<td><a href="fiche.php?id='.$obj->rowid.'">'.stripslashes($obj->nom)."</a></td>\n";
      print "<td>".$obj->email_commande."</td>\n";
      print '<td align="center">'.$langs->trans($yesno[$obj->commande_active])."</td>\n";

      if($user->rights->telephonie->fournisseur->config)
	{
	  print '<td align="center">';
	  if ($obj->commande_active)
	    {
	      print '<a href="fiche.php?action=desactive&amp;id='.$obj->rowid.'">D�sactive</a>';
	    }
	  else
	    {
	      print '<a href="fiche.php?action=active&amp;id='.$obj->rowid.'">Active</a>';
	    }
	  print '</td>';
	}

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

llxFooter("<em>Derni&egrave;re modification $Date: 2009/10/20 16:19:31 $ r&eacute;vision $Revision: 1.1 $</em>");
?>
