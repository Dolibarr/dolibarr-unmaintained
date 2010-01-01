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
 * $Id: index.php,v 1.2 2010/01/01 19:18:34 jfefe Exp $
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
	$OscProd = new Thelia_Product($db);

  	if ($num > 0) {
		print "<table width=\"100%\" class=\"noborder\">";
		print '<TR class="liste_titre">';
		print "<td>id</td>";
		print "<td>Ref</td>";
		print "<td>ProductId</td>";
		print "<td>Titre</td>";
		print "<td>Groupe</td>";
		print '<td align="center">Stock</td>';
		print '<TD align="center">Status</TD>';
		print '<TD align="center">Importer</TD>';
  		print "</TR>\n";

		while ($i < $num) {
      		$var=!$var;
            
      		$prodid = $OscProd->get_productid($result[$i][THELIA_id]);
        
		    print "<TR $bc[$var] >";
          print '<TD id="'.$result[$i][THELIA_id].'"><a href="fiche.php?id='.$result[$i][THELIA_id].'">'.$result[$i][THELIA_id]."</TD>\n";
    		print '<TD >'.$result[$i][ref]."</TD>\n";
    		print '<td><a href="../../product/fiche.php?id='.$prodid.'">'.$prodid.'</a></td>';
         print "<TD>".$langs->convToOutputCharset($result[$i]["titre"])."</TD>\n";
    		print "<TD>".$result[$i][manufacturer]."</TD>\n";
    		print '<TD align="center">'.$result[$i][stock]."</TD>\n";
    		print '<TD align="center">'.$result[$i][status]."</TD>\n";
    		if ($prodid) $lib = "modifier";
    		else $lib = "<u>importer</u>";
    		print '<TD align="center"><a href="fiche.php?action=import&id='.$result[$i][THELIA_id].'"'.">".$lib."</a></TD>\n";
    		print '</TR>'."\n";
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


llxFooter('$Date: 2010/01/01 19:18:34 $ - $Revision: 1.2 $');
?>
