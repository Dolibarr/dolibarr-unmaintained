<?php
/*  Copyright (C) 2006      Jean Heimburger     <jean@tiaris.info>
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
 * $Id: OSCvente.php,v 1.6 2011/01/16 14:38:41 eldy Exp $
 */

require("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/oscommerce_ws/includes/configure.php");

llxHeader();
$html = new Form($db);

if ($_GET["action"] == 'liste' )
{
	// affichage des produits en vente a partir de la tavle de transco
	$sql = "SELECT o.doli_prodidp as idp, o.osc_prodid as oscid, o.osc_lastmodif as date ";
	$sql .= "FROM ".MAIN_DB_PREFIX."osc_product as o";

	$resql=$db->query($sql);
	if ($resql)
	{
	    $langs->load("products");
	    $num = $db->num_rows($resql);
	    if ($num)
	    {
	        $i = 0;
	        print '<table class="noborder" width="100%">';
	        print '<tr class="liste_titre">';
	        print '<td colspan="2">'.$langs->trans("OscProds").'</td></tr>';
	        $var = True;
	        while ($i < $num)
	        {
	            $var=!$var;
	            $obj = $db->fetch_object($resql);

	            print '<tr $bc[$var]><td nowrap><a href="'.dol_buildpath('/product/fiche.php',1).'?id='.$obj->idp.'">'.img_object($langs->trans("ShowProduct"),"Product").' '.$obj->idp.'</a></td>';
	            print '<td><a href="'.dol_buildpath('/comm/fiche.php',1).'?socid='.$obj->idp.'">'.img_object($langs->trans("OscProd"),"Product").' '.$obj->oscid.'</a></td></tr>';
	            $i++;
	        }
	        print "</table><br>";
	    }
	}
}
if ($_GET["action"] == 'vendre' )
{
	$product = new Product($db, $_POST["idprod"]);
	$oscprod = new Osc_product($db);

	$oscid = $oscprod->get_osc_productid($_POST["idprod"]);
	if ( $oscid <= 0)
	{
		$prod = array();
		$prod['ref'] = $product->ref;
		$prod['nom'] = $product->libelle;
		$prod['desc'] = $product->description;
		$prod['quant'] = $_POST["qty"];
		$prod['prix'] = convert_backprice($product->price);
		// a gerer $product->tx_tva
		$prod['poids'] = $product->weight;
		// gerer $product->weight_units
		$prod['dispo'] = '';
		$prod['status'] = '1';
		$prod['fourn'] = '';
		$prod['url'] = '';

		//recherche de l'image
		$pdir = get_exdir($product->id,2) . $product->id ."/photos/";
		$dir = $conf->product->dir_output . '/'. $pdir;
		$img = $product->liste_photos($dir);

		if (sizeof($img) ==0) $prod['image'] = '';
		else
		{
			if ($img[0]['photo_vignette']) $filename=$img[0]['photo_vignette'];
		   else $filename=$img[0]['photo'];
		   $prod['image'] = dol_trunc($filename,16);
		}

//		print_r($prod);
//		print '<br/>';

		set_magic_quotes_runtime(0);

		//WebService Client.
		require_once(NUSOAP_PATH."/nusoap.php");

		// Creation
		// Set the parameters to send to the WebService
		$parameters = array("prod"=>$prod);

		// Set the WebService URL
		$client = new soapclient_nusoap(OSCWS_DIR."ws_articles.php");
		if ($client)
		{
			$client->soap_defencoding='UTF-8';
		}

		// Call the WebService and store its result in $result.
		$result = $client->call("create_article",$parameters );
		if ($client->fault)
		{
			$this->error="Fault detected";
			return -1;
		}
		elseif (!($err=$client->getError()) )
		{
			if ($result > 0)
			{
			// creation de l'enregistrement dans osc_products
				$oscprod->transcode($result,$_POST["idprod"]);
				print "<p>le produit ".$product->ref." ".$product->libelle.' est en vente en ligne <a href="'.OSC_URL.'product_info.php?products_id='.$result.'">consulter</a></p>';
				print "\n";
//			print_r($result);

			}
		}
	}
	else
	{
		print "<p>Le produit ".$_POST["idprod"].' est deja en vente en ligne : <a href="'.OSC_URL.'product_info.php?products_id='.$oscid.'">consulter</a></p>';
		print "\n";
	}
}

/* choix du produit */

	  print '<table class="noborder">';
	  print '<tr><td>'.$langs->trans('ProductsAndServices').'</td>';
	  print '<td>'.$langs->trans('Qty').'</td>';
	  print '</tr>';
	  print '<form action="OSCvente.php?action=vendre" method="POST">';
	  print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	  print '<input type="hidden" name="prod" value="test"/>';
	  print '<tr><td>';
	  print $html->select_produits('','idprod'.$i,'',$conf->product->limit_size,$soc->price_level);
	  print '</td>';
	  print '<td><input type="text" size="3" name="qty'.$i.'" value="1"></td></tr>';
	  print '<tr><td colspan="3" align="center"><input type="submit" class="button" value="'.$langs->trans('Oscsell').'"></td></tr>';
	  print '</form>';
	  print '</table>';


	/* ************************************************************************** */
	/*                                                                            */
	/* Barre d'action                                                             */
	/*                                                                            */
	/* ************************************************************************** */
	print "\n<div class=\"tabsAction\">\n";

 		print '<a class="tabAction" href="../index.php">'.$langs->trans("Retour").'</a>';
 		print '<a class="tabAction" href="OSCvente.php?action=liste">'.$langs->trans("Liste").'</a>';
	print "\n</div>\n";

llxFooter('$Date: 2011/01/16 14:38:41 $ - $Revision: 1.6 $');
?>
