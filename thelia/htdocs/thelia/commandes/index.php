<?php
/* Copyright (C) 2006 Jean Heimburger     <jean@tiaris.info>
 * Copyright (C) 2007 Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2009      Jean-Francois FERRY    <jfefe@aternatik.fr>
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
 * $Id: index.php,v 1.3 2010/02/08 00:50:30 jfefe Exp $
 */

require("./pre.inc.php");

$langs->load("companies");
$langs->load("orders");


llxHeader();


if ($page == -1) { $page = 0 ; }
$limit = $conf->liste_limit;
$offset = $limit * $page ;

print_barre_liste("Liste des commandes de la boutique web", $page, "index.php");

set_magic_quotes_runtime(0);

//WebService Client.
require_once(NUSOAP_PATH."nusoap.php");
require_once("../includes/configure.php");

// Set the parameters to send to the WebService
$parameters = array("orderid"=>"0");

// Set the WebService URL
$client = new nusoap_client(THELIA_WS_URL."ws_orders.php");
if ($client)
{
	$client->soap_defencoding='UTF-8';
}

$result = $client->call("get_Order",$parameters );

//		echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';

if ($client->fault) {
  		dol_print_error('',"erreur de connexion ");
      print_r($client->faultstring);
}
elseif (!($err = $client->getError()) )
{
	$num=0;
  	if ($result) $num = sizeof($result);
	$var=True;
  	$i=0;

	$TheliaOrder = new Thelia_Order($db);

  	if ($num > 0) {
		print "<TABLE width=\"100%\" class=\"noborder\">";
		print '<TR class="liste_titre">';
		print '<TD align="center">'.$langs->trans("Order").'</TD>';
		print '<td>'.$langs->trans("Customer").'</td>';
		print '<td>'.$langs->trans("Date").'</td>';
		print '<td>'.$langs->trans("Amount").'</td>';
		print '<td align="center">Paiement</td>';
		print '<td align="center">Statut</td>';
		print '<TD align="center">Actions</TD>';
  		print "</TR>\n";

		while ($i < $num) {
      		$var=!$var;

         $ordid = $TheliaOrder->get_orderid($result[$i][id]);
         print "<TR $bc[$var]>";
         print '<TD id="'.$result[$i][id].'"><a href="fiche.php?orderid='.$result[$i][id].'">'.$result[$i][ref]."</a> </TD>\n";
         print "<TD>".$result[$i][entreprise] .' '.strtoupper($result[$i][nom]). ' '.ucwords($result[$i][prenom])."</TD>\n";
         print "<TD>".dol_print_date($result[$i][date],"dayhour")."</TD>\n";
			$tot = convert_price($result[$i][total]) + convert_price($result[$i][port]);
    		print "<TD>".$tot."</TD>\n";
         print '<TD align="center">'.' '.$TheliaOrder->convert_paiement($result[$i][paiement])."</TD>\n";
         print '<TD align="center">'.$TheliaOrder->convert_statut($result[$i][statut])."</TD>\n";
         
         if ($ordid) 
         {  
            print '<td><a href="fiche.php?orderid='.$result[$i][id].'">'.img_picto("Modifier",'view').' Voir fiche</a></td>';
         }
         else
         {
            print '<td><a href="fiche.php?action=import&orderid='.$result[$i][id].'">'.img_picto("Importer",'filenew').' Importer</a></td>';
         }

    		print "</TR>\n";
    		$i++;
  		}
		print "</table></p>";
	}
	else {
  		dol_print_error('',"Aucune commande trouvée");
	}
}
else {
	print $client->getHTTPBody($client->response);
}

print "</TABLE>";


llxFooter('$Date: 2010/02/08 00:50:30 $ - $Revision: 1.3 $');
?>
