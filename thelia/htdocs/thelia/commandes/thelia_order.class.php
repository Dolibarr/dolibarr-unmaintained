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
 * $Id: thelia_order.class.php,v 1.4 2010/04/13 14:05:43 grandoc Exp $
 */

/**
        \file       htdocs/thelia/commandes/thelia_order.class.php
        \ingroup    thelia/orders
        \brief      Fichier de la classe des commandes issus de Thelia
        \version    $Revision: 1.4 $
*/


require_once(DOL_DOCUMENT_ROOT."/thelia/clients/thelia_customer.class.php");
require_once(DOL_DOCUMENT_ROOT."/thelia/produits/thelia_product.class.php");
require_once(DOL_DOCUMENT_ROOT."/commande/commande.class.php");
require_once(DOL_DOCUMENT_ROOT."/thelia/includes/configure.php");


/**
        \class      Thelia_order
        \brief      Classe permettant la gestion des commandes issues d'une base THELIA
*/

class Thelia_order
{
	var $db;

	var $orderid;
   var $client_id;
   var $adrfact;
   var $adrlivr;
   var $date;
   var $datefact;
   var $ref;
   var $transaction;
   var $livraison;
   var $transport;
   var $port;
   var $datelivraison;
   var $remise;
   var $devise;
   var $taux;
   var $colis;
   var $paiement;
   var $statut;
   var $lang;
   var $prod_prixu;
	var $total; //total de la commande TTC
      
   var $client_ref; //identifant du client thelia
   var $nom;
   var $prenom;
   
	var $thelia_lines = array();

	var $error;

    /**
     *    \brief      Constructeur de la classe
     *    \param      id          Id client (0 par defaut)
     */
	function Thelia_order($DB, $id=0) {

        global $langs;

        $this->orderid = $id ;
         $this->db = $DB;
        
	}


/**
*      \brief      Charge la commande THELIA en mémoire
*      \param      id      Id de la commande dans OsC
*      \return     int     <0 si ko, >0 si ok
*/
   function fetch($id='')
    {
        global $langs;
		global $conf;

		$this->error = '';
		dol_syslog("Thelia_order::fetch $id=$id ");
      // Verification parametres
      if (! $id )
        {
            $this->error=$langs->trans('ErrorWrongParameters');
            return -1;
        }

		set_magic_quotes_runtime(0);

		//WebService Client.
		require_once(NUSOAP_PATH."/nusoap.php");
		require_once("../includes/configure.php");

		// Set the parameters to send to the WebService
		$parameters = array("orderid"=>$id);

		// Set the WebService URL
		$client = new soapclient_nusoap(THELIA_WS_URL."/ws_orders.php");
	    if ($client)
		{
			//$client->soap_defencoding='ISO-8859-1';
		}

		// Call the WebService and store its result in $obj
		$obj = $client->call("get_Order",$parameters );
      //var_dump($obj);
		if ($client->fault) {
			$this->error="Fault detected ".$client->getError();
			return -1;
		}
		elseif (!($err=$client->getError()) ) {
        
  			$this->orderid = $obj[0][id];
		   $this->ref = $obj[0][ref];
			$this->nom = $obj[0][nom];
         $this->prenom = $obj[0][prenom];
			$this->client_id = $obj[0][client_id];
         $this->client_ref = $obj[0][client_ref];
			$this->orderdate = $obj[0][date];
			$this->total = $obj[0][total];
         $this->totalport = $obj[0][total] + $obj[0][port];
			$this->paiement = $obj[0][paiement];
			$this->statut = $obj[0][statut];
         $this->port = $obj[0][port];
         $this->remise =  $obj[0][remise];


			for ($i=1;$i<count($obj);$i++) {
			// les lignes
				$this->thelia_lines[$i-1][prod_id] = $obj[$i][prod_id];
				$this->thelia_lines[$i-1][prod_ref] = $obj[$i][prod_ref];
            $this->thelia_lines[$i-1][prod_titre] = utf8_encode($obj[$i][prod_titre]);
				$this->thelia_lines[$i-1][subprice] = $obj[$i][prod_prixu];
				$this->thelia_lines[$i-1][final_price] = $obj[$i][final_price];
				$this->thelia_lines[$i-1][tva] = "19.6";
				$this->thelia_lines[$i-1][qty] = $obj[$i][prod_quantite];
				}
  			}
  		else {
		    $this->error = 'Erreur '.$err ;
			return -1;
		}
//		print_r($this);
		return 0;
	}

   // renvoie un objet commande dolibarr
	function thelia2dolibarr($thelia_orderid)
	{
	  $result = $this->fetch($thelia_orderid);
	  if ( !$result )
	  {
			$commande = new Commande($this->db);
	    	if ($_error == 1)
	    	{
	       		print '<br>erreur 1</br>';
				exit;
	    	}
	    	/* initialisation */
			$theliaclient = new Thelia_Customer($this->db);
			$clientid = $theliaclient->get_clientid($this->client_id);

			$theliaproduct = new Thelia_product($this->db);
         
			$commande->socid = $clientid;
			$commande->ref = $this->orderid;
         $commande->ref_client = $this->ref;
			$commande->date = $this->orderdate;
         $commande->date_commande = $this->orderdate;

			/* on force */
			$commande->statut = 0; // TODO: si la commande est payée, on passe à statut=1
			$commande->source = 0; // TODO: vérifier les valeurs correspondantes

			//les lignes

			for ($i = 0; $i < sizeof($this->thelia_lines);$i++) {
  
            $commande->lines[$i]->libelle = $this->thelia_lines[$i][prod_ref]." ".$this->thelia_lines[$i][prod_titre];
       //     $commande->lines[$i]->desc = $this->thelia_lines[$i][prod_titre];
            $price_ttc = price2num($this->thelia_lines[$i][subprice],'MU');
            $price_ht = price2num($this->thelia_lines[$i][subprice] / (1 + (19.6 / 100)),'MU');
            $commande->lines[$i]->subprice = convert_price($price_ht);
            $commande->lines[$i]->qty = $this->thelia_lines[$i][qty];
            $commande->lines[$i]->tva_tx = "19.6";
            $commande->lines[$i]->fk_product = $theliaproduct->get_productid($this->thelia_lines[$i][prod_id]);
            $commande->lines[$i]->remise_percent = 0; // à calculer avec le finalprice
            $commande->lines[$i]->remise_absolue = $this->thelia_lines[$i][remise];
			}
         

			// les frais de port
			$fp = sizeof($this->thelia_lines);
         
			$commande->lines[$fp]->libelle = "Frais de port";
			$commande->lines[$fp]->desc = "Frais de port";
			$commande->lines[$fp]->price = convert_price($this->port);
			$commande->lines[$fp]->subprice = convert_price($this->port);
			$commande->lines[$fp]->qty = 1;
			$commande->lines[$fp]->tva_tx = 0;
         $commande->lines[$fp]->fk_product = FK_PORT;
			$commande->lines[$fp]->remise_percent = 0;

		return $commande;
		}

	}


/**
*      \brief      Mise � jour de la table de transition
*      \param      theliaid      Id du produit dans OsC
*	   \param	   prodid	  champ r�f�rence
*      \return     int     <0 si ko, >0 si ok
*/
	function transcode($thelia_orderid, $doli_orderid)
	{

      /* suppression et insertion d'une association de commande thelia <-> dolibarr */
      $sql = "DELETE FROM ".MAIN_DB_PREFIX."thelia_order WHERE rowid = ".$thelia_orderid.";";
      $result=$this->db->query($sql);
      if ($result)
      {
         dol_syslog("thelia_order::transcode success suppression - theliacat ".$thelia_orderid);
      }
      else
      {
         dol_syslog("thelia_order::transcode echec suppression : ".$sql);
         return -1;
      }
      
      // Nouvelle insertion
      $sql = "INSERT INTO ".MAIN_DB_PREFIX."thelia_order VALUES (".$thelia_orderid.", ".$this->db->idate(mktime()).", ".$doli_orderid.") ;";

      $result=$this->db->query($sql);
      $doli_orderid = $this->db->last_insert_id(MAIN_DB_PREFIX."thelia_order");
      if ($result)
      {
         dol_syslog("thelia_product::transcode success insert - theliacat ".$thelia_orderid);
      }
      else
      {
         dol_syslog("thelia_product::transcode echec insert : ".$sql);
      //            $this->db->rollback();
      return -1;
      }
      return $doli_orderid;
     }
     
     
   // retourne l'id de la commande dolibarr en fonction de l'id THELIA
	function get_orderid($thelia_orderid)
	{
		$sql = "SELECT fk_commande";
		$sql.= " FROM ".MAIN_DB_PREFIX."thelia_order";
		$sql.= " WHERE rowid = ".$thelia_orderid;
		$resql=$this->db->query($sql);
		$obj = $this->db->fetch_object($resql);
      // test d'erreurs
		if ($obj) return $obj->fk_commande;
		else return 0;
	}

   // retourne l'id de la commande thelia en fonction de l'id doliabarr
   function get_thelia_orderid($doli_orderid)
   {
      $sql = "SELECT rowid";
      $sql.= " FROM ".MAIN_DB_PREFIX."thelia_order";
      $sql.= " WHERE fk_commande = ".$doli_orderid;
      dol_syslog("get_thelia_orderid : " .$sql);
      $resql=$this->db->query($sql);
      $obj = $this->db->fetch_object($resql);
      // test d'erreurs
      if ($obj) return $obj->rowid;
      else return 0;
   }
   
   
  function convert_statut($statut)
  {
      $statut_thelia = array(
         "1" => img_picto("Non payée",'statut0')." NonPayée",
         "2" => img_picto("Payée",'statut1')." Payée",
         "3" => img_picto("Traitement",'statut4')." Traitement",
         "4" => img_picto("Envoyée",'statut6')." Envoyée",
         "5" => img_picto("Annulée",'statut5')." Annulée"
      );

      return $statut_thelia[$statut];
  }
  
  function convert_paiement($paiement)
  {
      $statut_thelia = array(
      "11" => "Paypal",
      "9" => "Virement",
      "6" => "Chèque");

      return $statut_thelia[$paiement];
  }
}

?>
