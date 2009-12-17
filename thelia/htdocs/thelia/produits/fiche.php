<?php
/*  Copyright (C) 2006      Jean Heimburger     <jean@tiaris.info>
 *  Copyright (C) 2009      Jean-Francois FERRY     <jfefe@aternatik.fr>
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
 * $Id: fiche.php,v 1.1 2009/12/17 14:57:00 hregis Exp $
 */

require("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/product.class.php");
require_once("../includes/configure.php");

llxHeader();

print_fiche_titre($langs->trans("FicheArticleTHELIA"));



/* action Import cr�ation de l'objet product de dolibarr
*
*/

if (($_GET["action"] == 'import' ) && ( $_GET["id"] != '' ) && ($user->rights->produit->creer || $user->rights->service->creer))
{
   $thelia_prod = new Thelia_product($db, $_GET['id']);
   $result = $thelia_prod->fetch($_GET['id']);
   
   if ( !$result )
   {
      $product = new Product($db);
      if ($_error == 1)
      {
         print '<br>erreur 1</br>';
         // exit;
      }
      $product = $thelia_prod->thelia2dolibarr($_GET['id']);
      
   }
   else
   {
      print "<p>erreur $thelia_prod->fetch</p>";
   }
   
   /* utilisation de la table de transco*/
   if ($thelia_prod->get_productid($thelia_prod->thelia_id)>0)
   {
      print '<p class="error">Ce produit a déjà été importé dans Dolibarr. </p>';
   }
   else
   {
      
      $id = $product->create($user);
      
      if ($id > 0)
      {
        
         $prod = new Product($db);
         $res = $prod->fetch($id);
         $res = $thelia_prod->transcode($thelia_prod->thelia_id,$prod->id);
         //    $prod->add_photo_web($conf->produit->dir_output,$thelia_prod->thelia_image);
         
         
         print '<div class="ok">Création réussie de la référence : <a href="../../product/fiche.php?id='.$id.'">'.$product->ref.'</a></div>';
         
        
         print "\n<div class=\"tabsAction\">\n";
         
         
         print '<a class="butAction" href="index.php">'.$langs->trans("Retour").'</a>';
         
         print "\n</div><br>\n";
         //       $id_entrepot = THELIA_ENTREPOT;
         //       $id = $product->create_stock($user, $id_entrepot,$thelia_prod->thelia_stock);
         //          if ($id > 0)  exit;
      }
      else
      {
         print "<p class='error'>On a une erreur".$id.$prod->error."</p>";
         
	if ($id == -3)
         {
            $_error = 1;
            $_GET["action"] = "create";
            $_GET["type"] = $_POST["type"];
         }
         if ($id == -2)
         {
            /* la r�f�rence existe on fait un update */
            $product_control = new Product($db);
            if ($_error == 1)
            {
               print '<br>erreur 1</br>';
               // exit;
            }
            $id = $product_control->fetch($ref = $thelia_prod->thelia_ref);
            
            if ($id > 0)
            {
               $id = $product->update($id, $user);
               if ($id > 0)
               {
                  // $id_entrepot = 1;
                  // $id = $product->correct_stock($user, $id_entrepot,$thelia_prod->thelia_stock, 0);
               }
               else print '<br>Erreur update '.$product->error().'</br>';
            }
            else print '<br>update impossible $id : '.$product_control->error().' </br>';
         }
         if ($id == -1)
         {
            print '<p>erreur'.$product->error().'</p>';
         }
         print '<p><a class="butAction" href="index.php">'.$langs->trans("Retour").'</a></p>';
      }
   }
}




if ($action == '' && !$cancel) {

	if ($_GET['id'])
	{
		$thelia_prod = new Thelia_product($db, $_GET['id']);
		$result = $thelia_prod->fetch($_GET['id']);
      
		if ( !$result)
		{
         
			$id_prod = $thelia_prod->get_productid($_GET['id']);
			print '<table class="noborder impair" width="100%" cellspacing="0" cellpadding="4">';
			print '<tr class="liste_titre"><td colspan="2">'.$thelia_prod->thelia_titre.'</td></tr>';
			print '<tr class="impair"><td width="20%">Description</td><td width="80%">'.$thelia_prod->thelia_desc.'</td></tr>';
			print '<tr class="pair"><td width="20%">Référence </td><td width="80%">'.$thelia_prod->thelia_ref.'</td></tr>';
			print '<tr class="impair"><td width="20%">ID THELIA</td><td width="80%">'.$thelia_prod->thelia_id.'</td></tr>';
		        print '<tr class="impair"><td width="20%">ID Dolibarr</td>';
		        if(!$id_prod) print '<td width="80%">à importer</td>';
		        else print '<td><a href="../../product/fiche.php?id='.$id_prod.'">voir le produit</a></td>';
		        print '</tr>';
		        print '<tr class="pair"><td width="20%">Prix</td><td width="80%">'.$thelia_prod->thelia_prix.'</td></tr>';
			print "</table>";

			/* ************************************************************************** */
			/*                                                                            */
			/* Barre d'action                                                             */
			/*                                                                            */
			/* ************************************************************************** */
			print "\n<div class=\"tabsAction\">\n";

			if ($user->rights->produit->creer || $user->rights->service->creer)
			{
				print '<a class="butAction" href="fiche.php?action=import&amp;id='.$thelia_prod->thelia_id.'">'.$langs->trans("Import").'</a>';
			}

			print '<a class="butAction" href="index.php#'.$_GET['id'].'">'.$langs->trans("Retour").'</a>';
			print "\n</div><br>\n";
			// seule action importer

		}
		else
		{
			print "<p>ERROR 1 : produit non trouvé</p>\n";
			dol_print_error('',"erreur webservice ".$thelia_prod->error);
		}
	}
	else
	{
		print "<p>ERROR 1</p>\n";
		print "Error";
	}
}

llxFooter('$Date: 2009/12/17 14:57:00 $ - $Revision: 1.1 $');
?>
