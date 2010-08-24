<?PHP
/* Copyright (C) 2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * $Id: pre.inc.php,v 1.2 2010/08/24 20:27:25 grandoc Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/distributeurs/pre.inc.php,v $
 *
 */
require("../../main.inc.php");

$user->getrights('telephonie');
$user->distributeur_id = 0;
require DOL_DOCUMENT_ROOT.'/telephonie/distributeurtel.class.php';
require DOL_DOCUMENT_ROOT.'/telephonie/telephonie.commercial.class.php';

$sql = "SELECT fk_distributeur";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_distributeur_commerciaux";
$sql .= " WHERE fk_user=".$user->id;

$resql = $db->query($sql);
if ($resql)
{
  $row = $db->fetch_row($resql);
  $user->distributeur_id = $row[0];
}

$sql = "SELECT fk_distributeur";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_distributeur_responsable";
$sql .= " WHERE fk_user=".$user->id;

$resql = $db->query($sql);
if ($resql)
{
  $row = $db->fetch_row($resql);
  $user->responsable_distributeur_id = $row[0];
}

function llxHeader($head = "", $title="") {
  global $user, $conf, $db;

  /*
   *
   *
   */
  top_menu($head, $title);

  $menu = new Menu();

  $menu->add(DOL_URL_ROOT."/telephonie/index.php", "Telephonie");

  $menu->add(DOL_URL_ROOT."/telephonie/client/index.php", "Clients");

  $menu->add(DOL_URL_ROOT."/telephonie/contrat/", "Contrats");

  $menu->add(DOL_URL_ROOT."/telephonie/ligne/index.php", "Lignes");

  if ($user->rights->telephonie->ligne_commander)
    $menu->add(DOL_URL_ROOT."/telephonie/ligne/commande/", "Commandes");

  if ($user->rights->telephonie->facture->lire)
    $menu->add(DOL_URL_ROOT."/telephonie/facture/", "Factures");

  if ($user->rights->telephonie->stats->lire)
    {
      $menu->add(DOL_URL_ROOT."/telephonie/stats/", "Statistiques");
      $menu->add_submenu(DOL_URL_ROOT."/telephonie/stats/distributeurs/", "Distributeurs");
    }

  $menu->add(DOL_URL_ROOT."/telephonie/tarifs/", "Tarifs");

  $menu->add(DOL_URL_ROOT."/telephonie/distributeurs/", "Distributeurs");


  $sql = "SELECT d.nom, d.rowid";
  $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_distributeur as d";

  if ($user->distributeur_id)
    {
      $sql .= " WHERE d.rowid = ".$user->distributeur_id;
    }

  $sql .= " ORDER BY d.nom ASC";
  
  $resql = $db->query($sql);
  
  if ($resql)
    {
      $num = $db->num_rows();
      $i = 0;
      $total = 0;
      
      while ($i < $num)
	{
	  $row = $db->fetch_row($resql);

	  $texte = $row[0];

	  if (dol_strlen($texte) > 15)
	    {
	      $texte = substr($texte,0,12)."...";
	    }
	  
	  $menu->add_submenu(DOL_URL_ROOT.'/telephonie/distributeurs/distributeur.php?id='.$row[1], $texte);

	  $i++;
	}
    }

  if ($user->rights->telephonie->fournisseur->lire)
    $menu->add(DOL_URL_ROOT."/telephonie/fournisseur/index.php", "Fournisseurs");

  if ($user->rights->telephonie->service->lire)
    $menu->add(DOL_URL_ROOT."/telephonie/service/", "Services");

  if ($user->rights->telephonie->ca->lire)
    $menu->add(DOL_URL_ROOT."/telephonie/ca/", "Chiffre d'affaire");

  left_menu($menu->liste);
}

?>
