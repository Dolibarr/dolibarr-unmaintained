<?php
/*  Copyright (C) 2006      Jean Heimburger     <jean@tiaris.info>
 *  Copyright (C) 2009      Jean-Francois FERRY    <jfefe@aternatik.fr>
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
 * $Id: fiche.php,v 1.3 2010/02/08 00:50:30 jfefe Exp $
 */

require_once("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/societe.class.php");
require_once("../includes/configure.php");



llxHeader();

print_barre_liste("Fiche client THELIA", $page, "fiche.php");

$thelia_cust = new Thelia_customer($db, $_GET['custid']);

/* action Import creation de l'objet product de dolibarr
*/
if (($_GET["action"] == 'import' ) && ( $_GET["custid"] != '' ) && ($user->rights->produit->creer || $user->rights->service->creer))
{
   // $thelia_cust = new Thelia_customer($db, $_GET['custid']);
   $result = $thelia_cust->fetch($_GET['custid']);
   
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
      
      /* initialisation */
      $societe->nom = $thelia_cust->entreprise.' '.strtoupper($thelia_cust->nom).' '.ucwords($thelia_cust->prenom);
      $societe->adresse = $thelia_cust->adresse1."\n".$thelia_cust->adresse2."\n".$thelia_cust->adresse3;
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
   }
   
   /* utilisation de la table de transco*/
   if ($thelia_cust->get_clientid($thelia_cust->id)>0)
   {
      print "\n<div class=\"tabsAction\">\n";
      print '<p class="warning">Ce client existe déjà.  mise à jour à prévoir</p>';
      print '<a class="butAction" href="index.php">'.$langs->trans("Retour").'</a>';
      print "\n</div><br>\n";
   }
   else 
   {
      $id = $societe->create($user);
      
      if ($id == 0)
      {
         print "\n<div class=\"ok\">\n";
         print '<p >Création réussie du nouveau client : '.$societe->nom;
         $res = $thelia_cust->transcode($thelia_cust->id,$societe->id);
         print ' : Id Dolibarr '.$societe->id.' , Id thelia : '.$thelia_cust->id.'</p>';
        
         print "\n</div><br>\n";
      }
      else
      {
         if ($id == -3)
         {
            $_error = 1;
            $_GET["action"] = "create";
            $_GET["type"] = $_POST["type"];
         }
         if ($id == -2)
         {
            /* la référence existe on fait un update */
            $societe_control = new Societe($db);
        
            $idp = $societe_control->fetch($socid = $thelia_cust->thelia_ref);
            
            if ($idp > 0)
            {
               $res = $societe->update($idp, $user);
               if ($res < 0) print '<br>Erreur update '.$idp.'</br>';
               $res = $thelia_cust->transcode($thelia_cust->custid,$idp );
               if ($res < 0) print '<br>Erreur update '.$idp.'</br>';
            }
            else print '<br>update impossible $id : '.$idp.' </br>';
         }
      }
   }
}

if ($action == '' && !$cancel) {
	if ($_GET['custid'])
	{
		
		$result = $thelia_cust->fetch($_GET['custid']);

		if ( !$result)
		{
//			print_titre("Fiche client THELIA : ".$thelia_cust->nom.'  '.$thelia_cust->prenom);

         print '<table class="noborder" width="100%" cellspacing="0" cellpadding="4">';
         print '<tr class="pair"><td width="20%">Nom</td><td width="80%">'.strtoupper($thelia_cust->nom).'</td></tr>';
         print '<tr class="impair"><td width="20%">Prénom</td><td width="80%">'.ucwords($thelia_cust->prenom).'</td></tr>';
         print '<tr class="pair"><td width="20%">Adresse</td><td width="80%">'.$thelia_cust->adresse1.' '.$thelia_cust->adresse2.' '.$thelia_cust->adresse3.'</td></tr>';
         print '<tr class="impair"><td width="20%">Code postal</td><td width="80%">'.$thelia_cust->cpostal.'</td></tr>';
			print '<tr class="pair"><td width="20%">Ville</td><td width="80%">'.$thelia_cust->ville.'</td></tr>';
         print '<tr class="impair"><td width="20%">Pays</td><td width="80%">'.$thelia_cust->pays.'</td></tr>';
         print '<tr class="pair"><td width="20%">Ref Thelia</td><td width="80%">'.$thelia_cust->ref.'</td></tr>';
         print '<tr class="impair"><td width="20%">Téléphone</td><td width="80%">'.$thelia_cust->telfixe.'</td></tr>';
         print '<tr class="pair"><td width="20%">E-mail</td><td width="80%">'.$thelia_cust->email.'</td></tr>';
			print "</table>";

			/* ************************************************************************** */
			/*                                                                            */
			/* Barre d'action                                                             */
			/*                                                                            */
			/* ************************************************************************** */
			print "\n<div class=\"tabsAction\">\n";

         if ( $user->rights->societe->lire) {
            print '<a class="butAction" href="../../comm/fiche.php?socid='.$thelia_cust->get_clientid($thelia_cust->id).'">'.$langs->trans("Voir fiche client Dolibarr").'</a>';
         }  
	  if ( $user->rights->societe->creer) {
	  	print '<a class="butAction" href="fiche.php?action=import&amp;custid='.$thelia_cust->id.'">'.$langs->trans("Import").'</a>';
	  }
     
	  print '<a class="butAction" href="index.php">'.$langs->trans("Retour").'</a>';
	  print "\n</div><br>\n";
	  // seule action importer

		}
		else
		{
			print "\n<div class=\"tabsAction\">\n";
			print "<p>ERROR 1c</p>\n";
			dol_print_error('',"erreur webservice ".$thelia_cust->error);
			print '<a class="butAction" href="index.php">'.$langs->trans("Retour").'</a>';
			print "\n</div><br>\n";
		}
	}
	else
	{
		print "\n<div class=\"tabsAction\">\n";
		print "<p>ERROR 1b</p>\n";
		print "Error";
		print '<a class="butAction" href="index.php">'.$langs->trans("Retour").'</a>';
		print "\n</div><br>\n";
	}
}



llxFooter('$Date: 2010/02/08 00:50:30 $ - $Revision: 1.3 $');
?>
