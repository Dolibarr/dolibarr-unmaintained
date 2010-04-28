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
 * $Id: fiche.php,v 1.5 2010/04/28 21:38:23 grandoc Exp $
 */

require("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/product.lib.php");

require_once("../includes/configure.php");

// Load traductions files
$langs->load("companies");
$langs->load("thelia");

llxHeader();



/* action Import : création de l'objet product de dolibarr
*
*/

if (($_GET["action"] == 'import' ) && ( $_GET["id"] != '' ) && ($user->rights->produit->creer || $user->rights->service->creer))
{
   $thelia_prod = new Thelia_product($db, $_GET['id']);
   $result = $thelia_prod->fetch($_GET['id']);
  
   if ( !$result )
   {
      
      dol_syslog("Thelia_product::fetch failed :".$_GET['id'].' '.$thelia_prod->error);
      if ($_error == 1)
      {
         print '<br>erreur 1</br>';
         // exit;
      }

   }
   else
   {
      $doli_prod = new Product($db);
      $doli_prod = $thelia_prod->thelia2dolibarr($_GET['id']);
      //var_dump($product);
   }
   
   /* utilisation de la table de transco*/
   if ($thelia_prod->get_productid($thelia_prod->thelia_id)>0)
   {
      print '<p class="error">Ce produit a déjà été importé dans Dolibarr. </p>';
   }
   else
   {
      
      $id = $doli_prod->create($user);
      
      if ($id > 0)
      {
        
         $prod = new Product($db);
         $res = $prod->fetch($id);
         $res = $thelia_prod->transcode($thelia_prod->thelia_id,$prod->id);
         //    $prod->add_photo_web($conf->produit->dir_output,$thelia_prod->thelia_image);
         
         
         print '<div class="ok">Création réussie de la référence : <a href="../../product/fiche.php?id='.$id.'">'.$prod->ref.'</a></div>';
         
        
         print "\n<div class=\"tabsAction\">\n";
         print '<a class="butAction" href="index.php">'.$langs->trans("Retour").'</a>'; 
         print "\n</div><br>\n";
         //       $id_entrepot = THELIA_ENTREPOT;
         //       $id = $product->create_stock($user, $id_entrepot,$thelia_prod->thelia_stock);
         //          if ($id > 0)  exit;
      }
      else
      {
         print '<p class="error">Erreur lors de la création du produit :  '.$product->error.'</p>';
         
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
  
         print '<p><a class="butAction" href="index.php">'.$langs->trans("Retour").'</a></p>';
      }
   }
}




if ($action == '' && !$cancel) {

  $thelia_prod = new Thelia_product($db, $_GET['id']);

  // Consultation de la fiche depuis l'onglet "THELIA"
  if ($_GET['dol_id'])
  {
   // retrouve le produit THELIA associé au produit DOLIBARR
   $thelia_prodid = $thelia_prod->get_thelia_productid($_GET['dol_id']);
   
   if($thelia_prodid > 0 )
   {
      $_GET['id'] = $thelia_prodid;
   }
      
      $product = new Product($db);
      $product->fetch($_GET['dol_id']);
      $head=product_prepare_head($product, $user);
      $titre=$langs->trans("CardProduct".$product->type);
      $picto='product';
      dol_fiche_head($head, 'thelia_ws', $titre, 0, $picto);
  }
  else
  {
     print_titre("Fiche Article THELIA");
  
  }
     
      
		// récupère les infos du produit thelia par le webservice
		$result = $thelia_prod->fetch($_GET['id']);
      
		if ( $result > 0)
		{
         
			$id_prod = $thelia_prod->get_productid($_GET['id']);
         print '<table class="border" width="100%"><tr>';
         // Reference
         print '<td width="15%">'.$langs->trans("Ref").'</td><td width="85%">';
         print $thelia_prod->thelia_ref;
         print '</td>';
         
         // Libelle
         print '<tr><td>'.$langs->trans("Label").'</td><td>'.$thelia_prod->thelia_titre.'</td></tr>';
         
         // Prix 
         print '<tr><td>'.$langs->trans("SellingPrice").'</td><td>'.price($thelia_prod->thelia_prix).'</td></tr>';
         print '<tr><td>'.$langs->trans("SellingPrice").' 2</td><td>'.price($thelia_prod->thelia_prix2).'</td>';
         print '</tr>';
         
         // TVA 
         print '<tr><td>'.$langs->trans("VAT").'</td><td>'.vatrate($thelia_prod->thelia_tva,true).'</td></tr>';
         
         // Statut
         print '<tr><td>'.$langs->trans("Status").'</td><td>';
         print $thelia_prod->getStatut($thelia_prod->thelia_statut);
         print '</td></tr>';
         
         // Description
         print '<tr><td valign="top">'.$langs->trans("Description").'</td><td>'.nl2br($thelia_prod->thelia_desc).'</td></tr>';
         
         
			
        // Promo / nouveautés
         print '<tr><td>'.$langs->trans("TheliaPromo").'</td><td>'.$thelia_prod->thelia_promo.'</td></tr>';
         print '<tr><td>'.$langs->trans("TheliaNew").'</td><td>'.$thelia_prod->thelia_nouveaute.'</td></tr>';
         
         // Stock et poids
         print '<tr><td>'.$langs->trans("Stock").'</td><td>'.$thelia_prod->thelia_stock.'</td></tr>';
         print '<tr><td>'.$langs->trans("Weight").'</td><td>'.$thelia_prod->thelia_poids.'</td></tr>';
        
			print "</table></div>";

			/* ************************************************************************** */
			/*                                                                            */
			/* Barre d'action                                                             */
			/*                                                                            */
			/* ************************************************************************** */
			print "\n<div class=\"tabsAction\">\n";

			if ($user->rights->produit->creer || $user->rights->service->creer)
			{
            if(!$id_prod) print '<a class="butAction" href="fiche.php?action=import&amp;id='.$thelia_prod->thelia_id.'">'.$langs->trans("Import").'</a>';
			}
         if($id_prod && !isset($_GET['dol_id'])) print '<a class="butAction" href="../../product/fiche.php?id='.$id_prod.'">Fiche produit Dolibarr</a>';
         print '<a class="butAction" href="'.THELIA_ADMIN_URL.'produit_modifier.php?ref='.$thelia_prod->thelia_ref.'">Voir dans le back office Thelia</a>';
         print '<a class="butAction" href="'.THELIA_URL.'produit.php?ref='.$thelia_prod->thelia_ref.'">Voir sur le site Thelia</a>';
         print '<a class="butAction" href="index.php#'.$_GET['id'].'">'.$langs->trans("TheliaListProducts").'</a>';
			print "\n</div><br>\n";
			// seule action importer

		}
		else
		{
			print "<p>ERROR 1 : produit non trouvé</p>\n";
			dol_print_error('',"erreur webservice ".$thelia_prod->error);
		}

}

llxFooter('$Date: 2010/04/28 21:38:23 $ - $Revision: 1.5 $');
?>
