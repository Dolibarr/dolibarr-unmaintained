<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 */

/**
 *	    \file       htdocs/compta/bank/bplc.php
 *      \ingroup    banque
 *		\brief      Page of BPLC transactions
 *		\version    $Id: bplc.php,v 1.2 2010/08/24 20:27:25 grandoc Exp $
 */

require("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/bank.lib.php");

// Security check
restrictedArea($user,'banque');


/*
 * View
 */

llxHeader();

if ($page == -1) { $page = 0 ; }

$offset = $conf->liste_limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;

print_barre_liste("Transactions BPLC", $page, "bplc.php");

print "<table class=\"noborder\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">";
print "<tr class=\"liste_titre\">";
print "<td>R�f. commande</td>";
print "<td>ip client</td><td>Num. transaction</td><td>Date</td><td>Heure</td>";
print "<td>Num autorisation</td>";
print "<td>cl� acceptation</td>";
print "<td>code retour</td>";
print "</tr>\n";

$sql = "SELECT ipclient,
               num_transaction,
               date_transaction,
               heure_transaction,
               num_autorisation,
               cle_acceptation,
               code_retour,
               ref_commande";

$sql .= " FROM ".MAIN_DB_PREFIX."transaction_bplc";

$resql = $db->query($sql);
if ($resql)
{
  $var=True;
  $num = $db->num_rows($resql);
  $i = 0; $total = 0;

  $sep = 0;

  while ($i < $num)
    {
      $objp = $db->fetch_object($resql);

      print "<tr $bc[1]>";

      $type = substr($objp->ref_commande, dol_strlen($objp->ref_commande) - 2 );
      $id = substr($objp->ref_commande, 0 , dol_strlen($objp->ref_commande) - 2 );

      if ($type == 10)
	{
	  print '<td><a href="../dons/fiche.php?rowid='.$id.'&action=edit">'.$objp->ref_commande.'</a></td>';
	}

      print "<td>$objp->ipclient</td>";
      print "<td>$objp->num_transaction</td>";
      print "<td>$objp->date_transaction</td>";
      print "<td>$objp->heure_transaction</td>";
      print "<td>$objp->num_autorisation</td>";
      print "<td>$objp->cle_acceptation</td>";
      print "<td>$objp->code_retour</td>";
      $i++;
    }
  $db->free($resql);
}
print "</table>";

$db->close();

llxFooter('$Date: 2010/08/24 20:27:25 $ - $Revision: 1.2 $');
?>
