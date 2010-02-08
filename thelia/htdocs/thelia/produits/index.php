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

llxHeader();


if ($page == -1) { $page = 0 ; }
$limit = $conf->liste_limit;
$offset = $limit * $page ;

print_barre_liste("Liste des articles de la boutique web", $page, "index.php");

set_magic_quotes_runtime(0);

//WebService Client.
require_once(NUSOAP_PATH."nusoap.php");
require_once("../includes/configure.php");

// Set the parameters to send to the WebService
$parameters = array();

// Set the WebService URL
$client = new nusoap_client(THELIA_WS_URL."ws_articles.php");
if ($client)
{
	$client->soap_defencoding='ISO-8859-1';
}

$result = $client->call("get_listearticles");


if ($client->fault) {
  		dol_print_error('',"erreur de connexion ");
}
elseif (!($err = $client->getError()) )
{
   
	$num=0;
  	if ($result) $num = sizeof($result);
   echo $num." résults";
	$var=True;
  	$i=0;

// un produit osc
	$TheliaProd = new Thelia_Product($db);

  	if ($num > 0) {
		print "<table width=\"100%\" class=\"noborder\">";
		print '<TR class="liste_titre">';
		print "<td>Ref</td>";
		print "<td>ProductId</td>";
		print "<td>Titre</td>";
		print "<td>Groupe</td>";
		print '<td align="center">Stock</td>';
		print '<TD align="center">Statut</TD>';
		print '<TD align="center">Action</TD>';
  		print "</TR>\n";

		while ($i < $num) {
      		$var=!$var;
            
      		$prodid = $TheliaProd->get_productid($result[$i][THELIA_id]);
        
		    print "<tr $bc[$var] >";
          print '<td id="'.$result[$i][THELIA_id].'"><a href="fiche.php?id='.$result[$i][THELIA_id].'">'.$result[$i][ref]."</td>\n";
    		print '<td><a href="../../product/fiche.php?id='.$prodid.'">'.$prodid.'</a></td>';
         print "<td>".$langs->convToOutputCharset($result[$i]["titre"])."</td>\n";
    		print "<td>".$result[$i][manufacturer]."</td>\n";
    		print '<td align="center">'.$result[$i][stock]."</td>\n";
         print '<td align="center">'.$TheliaProd->getStatut($result[$i][status])."</td>\n";
         
    		if ($prodid) 
         {  
            print '<td><a href="fiche.php?id='.$result[$i][THELIA_id].'">'.img_picto("Modifier",'view').' Voir fiche</a></td>';
         }
    		else
         {
            print '<td><a href="fiche.php?action=import&id='.$result[$i][THELIA_id].'">'.img_picto("Importer",'filenew').' Importer</a></td>';
         }
    		
    		print '</tr>'."\n";
    		$i++;
  		}
		print "</table></p>";
	}
	else {
  		dol_print_error('',"Aucun article trouvé");
	}
}
else {
	print $client->getHTTPBody($client->response);
}

print "</TABLE>";


llxFooter('$Date: 2010/02/08 00:50:30 $ - $Revision: 1.3 $');
?>
