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
 * $Id: thelia_product.class.php,v 1.4 2010/04/13 14:05:42 grandoc Exp $
 */

/**
        \file       htdocs/thelia_ws/produits/thelia_product.class.php
        \ingroup    thelia_ws/produits/
        \brief      Fichier de la classe des produits issus de OSC
        \version    $Revision: 1.4 $
*/

require_once(DOL_DOCUMENT_ROOT."/thelia/produits/thelia_categories.class.php");


/**
 *       \class      Osc_product
 *       \brief      Classe permettant la gestion des produits issus d'une base OSC
 */
class Thelia_product
{
	var $db;

	var $thelia_id;
	var $thelia_ref;
	var $thelia_titre;
	var $thelia_desc;
	var $thelia_prix;
	var $thelia_tva;
	var $thelia_stockmini;
	var $thelia_stock;
   var $thelia_statut;
	var $thelia_image;
	var $thelia_catid;

	var $error;


    /**
     *    \brief      Constructeur de la classe
     *    \param      id          Id produit (0 par defaut)
     */
	function Thelia_product($DB, $id=0) {

        global $langs;

        $this->thelia_id = $id ;

        /* les initialisations n�cessaires */
        $this->db = $DB;
	}

/**
     *      \brief      Charge le produit Thelia en mémoire
     *      \param      id      Id du produit dans Thelia
     *      \param      ref     Ref du produit dans Thelia (doit être unique)
     *      \return     int     <0 si ko, thelia_id si ok
     */
   function fetch($id='',$ref='')
    {
        global $langs;
         global $conf;

		$this->error = '';
		dol_syslog("Thelia_product::fetch id=$id ref=$ref");
      // Verification parametres
      if (! $id && ! $ref)
      {
         $this->error=$langs->trans('ErrorWrongParameters');
         return -1;
      }

		set_magic_quotes_runtime(0);

		//WebService Client.
		require_once(NUSOAP_PATH."/nusoap.php");
		require_once("../includes/configure.php");

		// Set the parameters to send to the WebService
		$parameters = array("id"=>$id,"ref"=>$ref);

		// Set the WebService URL
		$client = new soapclient_nusoap(THELIA_WS_URL."/ws_articles.php");
	    if ($client)
		{
			$client->soap_defencoding='UTF-8';
		}

		// Call the WebSeclient->fault)rvice and store its result in $obj
		$obj = $client->call("get_article",$parameters );
      //print_r( $obj);
		if ($client->fault) {
			$this->error="Fault detected";
         dol_syslog("Thelia_product::fetch Erreur webservice !");
			return -1;
		}
		elseif (!($err=$client->getError()) ) {
  			$this->thelia_id = $obj['id'];
  			$this->thelia_ref = $obj['ref'];
  			$this->thelia_titre = utf8_encode($obj['titre']);
  			$this->thelia_desc = $obj['description'];
  			$this->thelia_stock = $obj['stock'];
         $this->thelia_statut = $obj['statut'];
			$this->thelia_prix = $obj['prix'];
         $this->thelia_prix2 = $obj['prix2'];
         $this->thelia_promo = $obj['promo'];
         $this->thelia_nouveaute = $obj['nouveaute'];
         $this->thelia_poids = $obj['poids'];
         $this->thelia_tva = $obj['tva'];
			$this->thelia_image = $obj['image'];
			$this->thelia_catid = $obj['rubrique'];
         
         return $this->thelia_id;
         
      }
  		else 
      {
		    $this->error = 'Erreur '.$client->getError();
			return -1;
		}
		return 0;
	}

// renvoie un objet produit dolibarr
	function thelia2dolibarr($thelia_productid)
	{

	  $result = $this->fetch($thelia_productid);
	  if ( $result )
	  {
	  		$product = new Product($this->db);
	    	if ($this->error == 1)
	    	{
	      	print '<br>erreur 1</br>';
				return '';
	    	}
	    	/* initialisation */
	    		$product->ref = $this->thelia_ref;
	    		$product->libelle = $this->thelia_titre;
	    		$product->description = $this->thelia_desc;
	    		$product->price_ttc = convert_price($this->thelia_prix);
            $product->price_base_type = "TTC";
	    		$product->tva_tx = "19.6";
            //! Type 0 for regular product, 1 for service, 2 for assembly kit, 3 for stock kit
	    		$product->type = 0; // produit manufacturé (0 pour matière première)
	    		$product->catid = $this->get_catid($this->thelia_catid) ;
	    		$product->seuil_stock_alerte = 0; /* on force */
            
            // Statut indique si le produit est en vente '1' ou non '0'
            $product->status = $this->thelia_statut;
            // Statut indique si le produit est un produit finis '1' ou une matiere premiere '0'
            $product->finished ="1";
            
	

		 return $product;
	  }

	}
/**
*      \brief      Mise � jour de la table de transition
*      \param      theliaid      Id du produit dans OsC
*	   \param	   prodid	  champ r�f�rence
*      \return     int     <0 si ko, >0 si ok
*/
	function transcode($theliaid, $prodid)
	{

		/* suppression et insertion */
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."thelia_product WHERE rowid = ".$theliaid.";";
      
		$result=$this->db->query($sql);
        if ($result)
        {
         }
        else
        {
            dol_syslog("thelia_product::transcode echec suppression");
 
//            $this->db->rollback();
//            return -1;
		}
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."thelia_product VALUES (".$theliaid.", ".$prodid.") ;";
 
		$result=$this->db->query($sql);
        if ($result)
        {
            return 1;
         }
        else
        {
            dol_syslog("thelia_product::transcode echec insert");
//            $this->db->rollback();
//            return -1;
		}
	return 0;
     }

// converti le produit thelia en produit dolibarr

	function get_productid($thelia_product)
	{
		$sql = "SELECT fk_product";
		$sql.= " FROM ".MAIN_DB_PREFIX."thelia_product";
		$sql.= " WHERE rowid = '".$thelia_product."'";
    
		$resql=$this->db->query($sql);
		$obj = $this->db->fetch_object($resql);
// test d'erreurs
		if ($obj) return $obj->fk_product;
		else return '';
	}

	function get_catid($theliacatid)
	{
		$mycat=new Thelia_categorie($this->db);

		if ($mycat->fetch_theliacat($theliacatid) > 0)
		{
			return $mycat->dolicatid;
		}
		else return 0;
	}

	function get_thelia_productid($productidp)
	{
		$sql = "SELECT rowid";
		$sql.= " FROM ".MAIN_DB_PREFIX."thelia_product";
		$sql.= " WHERE fk_product = ".$productidp;
		$result=$this->db->query($sql);
		$row = $this->db->fetch_row($result);
// test d'erreurs
		if ($row) return $row[0];
		else return -1;
	}
   
   
   /**
   *    \brief      retourne sous forme de texte le statut d'un produit (en vente en ligne ou non)
   *    \param      $statut : correspond au statut dans THELIA
   *    \return      chaine de caractères avec image      
   */
   function getStatut($statut)
   {
      global $langs;
      if ($statut==1) return img_picto($langs->trans('ProductOnLine'),'on').' '.$langs->trans('ProductOnLine');
      if ($statut==0) return img_picto($langs->trans('ProductOffLine'),'off').' '.$langs->trans('ProductOffLine');
   
   }
   
   
	  /**
     *    \brief      cr�ation d'un article dans base THELIA
     *    \param      $user utilisateur
     */
	function create($user)
    {
    /* non impl�ment�e */
    }

	  /**
     *    \brief      modification d'un article dans base THELIA
     *    \param      $user utilisateur
     */
	function update($id, $user)
    {
    /* non impl�ment�e */
    }

    /**
     *    \brief      Suppression du produit en base THELIA
     *    \param      id          id du produit
     */
   function delete($id)
    {
    /* non impl�ment�e */
    }
}
?>
