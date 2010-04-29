<?php
/*  Copyright (C) 2006      Jean Heimburger     <jean@tiaris.info>
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
 * $Id: fiche.php,v 1.6 2010/04/29 14:54:32 grandoc Exp $
 */
 
require_once("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/commande/class/commande.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once("../includes/configure.php");
require_once("../clients/thelia_customer.class.php");



llxHeader();

if ($action == '' && !$cancel) {

 if ($_GET["orderid"])
 {
  $thelia_order = new Thelia_order($db, $_GET["orderid"]);
  $result = $thelia_order->fetch($_GET["orderid"]);

  if ( !$result)
    { 
      $thelia_prod = new Thelia_Product($db);
      print_fiche_titre("Fiche commande THELIA : ".$thelia_order->ref);

      print '<table class="noborder" width="100%" style="margin-bottom: 20px">';
      print '<tr class="pair"><td  width="20%">Date commande</td><td>'.dol_print_date($thelia_order->orderdate,"dayhour").'</td></tr>';
      
      // Infos client
      print '<tr class="impair"><td>client Thelia</td><td ><a href="../clients/fiche.php?custid='.$thelia_order->client_id.'">'.$thelia_order->client_ref.'</a></td></tr>';
      print '<tr  class="pair"><td>Nom client</td>';
      $thelia_cust = new Thelia_customer($db, $thelia_order->client_id);
      
      $client_doli = $thelia_cust->get_clientid($thelia_order->client_id);
      if($client_doli > 0)
      {
         $client = new Societe($db);
         $fk_soc = $client->fetch($client_doli);
         print '<td>'.$client->getNomUrl(true).'</td>';
      }
      else
      {
         print '<td>'.strtoupper($thelia_order->nom).' '.ucfirst($thelia_order->prenom).'</td>';
      }
      print '</tr>';
      
      // Montant total
      print '<tr class="impair"><td>Montant total TTC (hors frais de port)</td><td>'.price($thelia_order->total).'</td></tr>';
      // Frais de port
      print '<tr class="impair"><td>Frais de port </td><td>'.price($thelia_order->port).'</td></tr>';
      // Montant avec frais de port
      print '<tr class="impair"><td><strong>Montant total TTC </strong></td><td><strong>'.price($thelia_order->totalport).'</strong></td></tr>';
      // remise
      print '<tr class="pair"><td>Remise </td><td>'.price($thelia_order->remise).'</td></tr>';
      
 
      
      // Statut et paiement
      print '<tr class="pair"><td>Statut commande</td><td>'.$thelia_order->convert_statut($thelia_order->statut).'</td></tr>';
      print '<tr class="impair"><td>Méthode de paiement</td><td>'.$thelia_order->convert_paiement($thelia_order->paiement).'</td></tr>';
      
      // Infos de la commande Dolibarr
      print '<tr><td>Commande Dolibarr </td>';
      
      $id_doli = $thelia_order->get_orderid($thelia_order->orderid);
      if ($id_doli > 0)
      {
         $doli_cde = new commande($db);
         $id_doli = $doli_cde->fetch($id_doli);
         print '<td>'.$doli_cde->getNomUrl(true).'</td>';
         
         // Statut
         print '<tr><td>'.$langs->trans('Status').' dolibarr</td>';
         print '<td colspan="2">'.$doli_cde->getLibStatut(4).'</td>';
         
      }
      else 
      {
         print '<td>Non importée</td>';
      }
      print '</tr>';
    
      
      print "</table>";
      
      
      print_titre("Produits Commandés");
      
      print '<table class="noborder" width="100%" cellspacing="0" >';
      print '<tr class="liste_titre">
      <th class="liste_titre">Thelia</th>
      <th class="liste_titre">Dolibarr</th>
      <th class="liste_titre">Réf thelia</th>
      <th class="liste_titre">Titre</th>
      <th class="liste_titre">Prix TTC</th>
      <th class="liste_titre">Quantite</th>
        </tr>';
      // les articles 
     
      $var=true;
      for ($l=0;$l < sizeof($thelia_order->thelia_lines); $l++)
      {
         $var=!$var;
         print '<tr '. $bc[$var].'>
         <td><a href="'.THELIA_ADMIN_URL.'produit_modifier.php?ref='.$thelia_order->thelia_lines[$l]["prod_ref"].'">modifier</a></td>';
         if($thelia_prod->get_productid($thelia_order->thelia_lines[$l]["prod_id"])>0) print '<td><a href="../../product/fiche.php?id='.$thelia_prod->get_productid($thelia_order->thelia_lines[$l]["prod_id"]).'">voir fiche</a></td>';
         else print '<td>à importer</td>';
         print '<td><a href="../produits/fiche.php?id='.$thelia_order->thelia_lines[$l]["prod_id"].'">'.$thelia_order->thelia_lines[$l]["prod_ref"].'</a></td>
         <td>'.$thelia_order->thelia_lines[$l]["prod_titre"].'</td>
         <td>'.price($thelia_order->thelia_lines[$l]["subprice"]).'</td>
         <td>'.$thelia_order->thelia_lines[$l]["qty"].'</td></tr>';
      }	
      print "</table>";

	/* ************************************************************************** */
	/*                                                                            */ 
	/* Barre d'action                                                             */ 
	/*                                                                            */ 
	/* ************************************************************************** */
	print "\n<div class=\"tabsAction\">\n";

	  if ( $user->rights->commande->creer) {
        print '<a class="butAction" href="fiche.php?action=import&amp;orderid='.$thelia_order->orderid.'">'.$langs->trans("Import").'</a>';
    	}
      print '<a class="butAction" href="'.THELIA_ADMIN_URL.'commande_details.php?ref='.$thelia_order->ref.'">'.$langs->trans("VoirdansBOThelia").'</a>';
  	  print '<a class="butAction" href="index.php">'.$langs->trans("Retour").'</a>';
	print "\n</div><br>\n";
// seule action importer
     
	}
      else
	{
	  print "\n<div class=\"tabsAction\">\n";
		  print "<p>ERROR 1c</p>\n";
		  dol_print_error('',"erreur webservice ".$thelia_order->error);
		  print '<a class="butAction" href="index.php">'.$langs->trans("Retour").'</a>';
	  print "\n</div><br>\n";
	}
 }
 else
 {
	  print "\n<div class=\"tabsAction\">\n";
		  print "<p>ERROR 1b</p>\n";
		  print '<a class="butAction" href="index.php">'.$langs->trans("Retour").'</a>';
	  print "\n</div><br>\n";
 }
}
/* action Import création de l'objet commande de dolibarr 
*
*/
 if (($_GET["action"] == 'import' ) && ( $_GET["orderid"] != '' ) && $user->rights->commande->creer)
    {
		  $thelia_order = new Thelia_order($db);
  		  $result = $thelia_order->fetch($_GET["orderid"]);
	  if ( !$result )
	  {
			$commande = $thelia_order->thelia2dolibarr($_GET["orderid"]);
	  }

      /* utilisation de la table de transco*/
		if ($thelia_order->get_orderid($thelia_order->orderid)>0)
		{
         print '<p class="warning">Cette commande existe déjà : <a href="../../commande/fiche.php?id='.$thelia_order->get_orderid($thelia_order->orderid).'">Voir la commande</a></p>';
		}
		else {
      // vérifier que la société est renseignée, sinon importer le client d'abord
			if ( ! $commande->socid) 
			{
				$thelia_cust = new Thelia_customer($db, $thelia_order->client_id);
  		  		$result = $thelia_cust->fetch($thelia_order->client_id);
			  if ( !$result )
	  		  {
				$societe = new Societe($db);
	    		if ($_error == 1)
	    		{
				  	print "\n<div class=\"tabsAction\">\n";
		    		print '<br>erreur 1</br>';
		    		print '<a class="butAction" href="index.php">'.$langs->trans("Retour").'</a>';
					print "\n</div><br>\n";
		    	}
            
            /* initialisation des données client à partir des infos THELIA*/
            $societe->nom = $thelia_cust->entreprise .' '.strtoupper($thelia_cust->nom). ' '.ucwords($thelia_cust->prenom);
            $societe->adresse = $thelia_cust->adresse1;
            $societe->cp = $thelia_cust->cpostal;
            $societe->ville = $thelia_cust->ville;
            $societe->departement_id = 0;
            $societe->pays_code = $thelia_cust->codepays;
            $societe->tel = $thelia_cust->telfixe;
            $societe->fax = $thelia_cust->thelia_custfax;
            $societe->email = $thelia_cust->email;
            /* on force */
            $societe->url = '';
            $societe->siren = '';
            $societe->siret = $thelia_cust->siret;
            $societe->ape = '';
            $societe->client = 1; // mettre 0 si prospect
      

				$cl = $societe->create($user);
			   if ($cl == 0)
			    {
					$commande->socid = $societe->id;
		    	  	print '<p class="ok">création réussie du nouveau nouveau client/prospect : '.$societe->nom;
			    	$res = $thelia_cust->transcode($thelia_cust->thelia_custid,$societe->id);
               
               /* TODO : barre d'action */
					print ' : Id Dolibarr '.$societe->id.' , Id thelia : '.$thelia_cust->thelia_custid.'</p>';
               
			    }
			    else
			    {
			    	print '<p>création impossible client : '. $thelia_cust->thelia_custid .'</p>';
			    	exit;
			    }
				}
			}
         // vérifier l'existence des produits commandés
			$thelia_product = new Thelia_Product($db);
			$err = 0;

         for ($lig = 0; $lig < sizeof($commande->thelia_lines); $lig++)
			{
				print "<p>traitement de ".$commande->lines[$lig]->fk_product."</p>";
				if (! $commande->thelia_lines[$lig]->prod_id) 
				{
               print '<p class="error">Article non trouvé '.$commande->thelia_lines[$lig]->titre.' : '.$commande->thelia_lines[$lig]->desc.'</p>';
					$err ++;
				}
			}			
			if ($err > 0) {
				print '<p class="warning"> Des produits de la commande sont inexistants dans Dolibarr, la commande n\'a donc pas été importée.</p>';
				$id =-9;
			}
			else 
         {
            // Création de la commande 
            // TODO: date de commande, remise, gestion des adresse de livraison
            $id = $commande->create($user);
         }

		    if ($id > 0)
		    {
				 
		       	  print '<p class="ok">Création réussie de la commande </p>';
   			     $res = $thelia_order->transcode($thelia_order->orderid,$id);
                 print "\n<div class=\"tabsAction\">\n";
                 print  '<a class="butAction" href="../../commande/fiche.php?id='.$id.'">Voir la commande générée.</a>';
					  print '<p><a class="butAction" href="index.php">'.$langs->trans("RetourListeCommande").'</a></p>';
				  print "\n</div><br>\n";

				if ($id > 0)  exit;
		    }
		    else
		    {
		        if ($id == -3)
		        {
						print ("<p>$id = -3 ".$commande->error."</p>");
		            $_error = 1;
		            $_GET["action"] = "create";
		            $_GET["type"] = $_POST["type"];
		        }
				if ($id == -2)
				{
				/* la r�f�rence existe on fait un update */
				 $societe_control = new Societe($db);
				 if ($_error == 1)
		    	 {
		       		print '<br>erreur 1</br>';
					exit;
		    	 }
			     $id = $societe_control->fetch($ref = $thelia_order->thelia_ref);
					
					if ($id > 0) 
					{ 
						$id = $societe->update($id, $user);
						if ($id < 0) print '<br>Erreur update '.$id.'</br>';
					}
					else print '<br>update impossible $id : '.$id.' </br>';
				}
			  print '<p><a class="butAction" href="index.php">'.$langs->trans("Retour").'</a></p>';
		    }
		 }
 
    }

llxFooter('$Date: 2010/04/29 14:54:32 $ - $Revision: 1.6 $');
?>
