<?php
/* Copyright (C) 2007-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Denis Martin <denimartin@hotmail.fr>
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
 */

/**
 *      \file       htdocs/pointofsale/cashdesk.class.php
 *      \ingroup    pointofsale
 *      \brief      Class used to manages basket object from database and template display for PointOfSale module
 *		\version    $Id: cashdesk.class.php,v 1.1 2010/10/29 16:40:51 hregis Exp $
 *		\author		Denis Martin
 */

require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php") ;
require_once(DOL_DOCUMENT_ROOT."/compta/paiement/class/paiement.class.php") ;
require_once(DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php") ;
require_once(DOL_DOCUMENT_ROOT."/pointofsale/class/basket_det.class.php") ;
require_once(DOL_DOCUMENT_ROOT."/pointofsale/class/basket_paiement.class.php") ;

if (! defined("BASKET_DRAFT")) define("BASKET_DRAFT", 0) ;
if (! defined("BASKET_VALIDATED")) define("BASKET_VALIDATED", 1) ;
if (! defined("BASKET_PAID")) define("BASKET_PAID", 2) ;



/**
 *      \class      Cashdesk
 *      \brief      Class used to manage basket object and templates for pointofsale module
 */
class Cashdesk
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	
	//var $id ;
	var $customer ; // Societe object corresponding to $conf->global->POS_GENERIC_THIRD
	var $contact ; // Contact linked to the invoice
	//var $invoice ; // 
	var $root_categorie ; // Categorie object containing sub categories and products
	
	var $basket_id ; // rowid of the line in DB
	
	var $total_ht ;
	var $total_tva ;
	var $total_ttc ;
	var $statut ; // BASKET_DRAFT, BASKET_VALIDATED or BASKET_PAID
	
	var $lines ; // Array containing Basket_det objects
	var $paiements ; // Array containg Basket_paiement objects
	var $amount_paid ; // Amount of the TTC paid
	var $ref ; // Reference to add to the invoice when validating the basket.
	
    /**
     *      Constructor of cashdesk Class
     *      @param      DB      Database handler
     *      @param		user	$user object
     *      @return		1 if ok, -n for n errors
     */
    function Cashdesk($DB, $user = '') {
    	
	    global $langs, $conf ;
	    
	    $error = 0 ;
	    $this->db = $DB;
    	$this->loadCategoriesAndProducts() ;
    	$this->lines = array() ;
    	$this->paiements = array() ;
    	
    	$this->customer = new Societe($this->db) ;
    	
    	$this->contact = new Contact($this->db) ;
    	$id_contact = 0 ;
    	
    	if($this->customer->fetch($conf->global->POS_GENERIC_THIRD) < 0) {
    		$error++ ;
    		$this->errors[] = $langs->trans("ErrorLoadingThird") ;
    	}
    	
    	
    	if($user != '') {
    	   	//Load basket line
	        $sql = "SELECT t.rowid," ;
	        $sql.= "t.fk_contact, " ;
	        $sql.= "t.tms, " ;
			$sql.= "t.total_ttc, " ;
			$sql.= "t.total_ht, " ;
			$sql.= "t.total_tva, " ;
			$sql.= "t.statut " ;
	        $sql.= " FROM ".MAIN_DB_PREFIX."basket as t";
	        $sql.= " WHERE t.fk_user = '".$user->login."'" ;
	    
	    	dol_syslog("Cashdesk::Cashdesk sql=".$sql, LOG_DEBUG);
	        $resql=$this->db->query($sql);
	        if ($resql)
	        {
	            if ($this->db->num_rows($resql))
	            {
	                $obj = $this->db->fetch_object($resql);
					
	                $id_contact = $obj->fk_contact ;
	                $this->basket_id = $obj->rowid ;
	                $this->tms = $obj->tms ;
	                $this->total_ht = $obj->total_ht ;
	                $this->total_tva = $obj->total_tva ;
	                $this->total_ttc = $obj->total_ttc ;
	                $this->statut = $obj->statut ;
	                
	            }
	            $this->db->free($resql);
	        }
	        else
	        {
	        	$error++ ;
	      	    $this->error[]=$langs->trans("ErrorLoadingBasket") ;
	            dol_syslog("Cashdesk::Cashdesk ".$this->db->lasterror(), LOG_ERR);
	        }
	        
	        if($id_contact) $this->contact->fetch($id_contact) ;
	        
	        //Load basket_det lines
	        $sql = "SELECT d.rowid," ;
			$sql.= " d.fk_product,";
			$sql.= " d.qty,";
			$sql.= " d.remise,";
			$sql.= " d.remise_percent,";
			$sql.= " d.tva_taux,";
			$sql.= " d.subprice,";
			$sql.= " d.price,";
			$sql.= " d.total_ht,";
			$sql.= " d.total_tva,";
			$sql.= " d.total_ttc,";
			$sql.= " d.product_type,";
			$sql.= " d.date_start,";
			$sql.= " d.date_end";
	
			
	        $sql.= " FROM ".MAIN_DB_PREFIX."basket_det as d";
	        $sql.= " WHERE d.fk_basket = '".$this->basket_id . "'";
	        	    
	    	dol_syslog("Cashdesk::Cashdesk sql=".$sql, LOG_DEBUG);
	        $resql=$this->db->query($sql);
	        if ($resql)
	        {
	        	$num = $this->db->num_rows($resql);
	        	$i = 0 ;
	            if ($num)
	            {
	            	while($i < $num) {
		                $obj = $this->db->fetch_object($resql);
		                $this->lines[] = new Basket_det(
		                	$DB,
		                	$obj->rowid,
		                	$this->basket_id,
		                	$obj->fk_product,
		                	$obj->qty,
		                	$obj->remise,
		                	$obj->remise_percent,
		                	$obj->tva_taux,
		                	$obj->subprice,
		                	$obj->price,
		                	$obj->total_ht,
		                	$obj->total_tva,
		                	$obj->total_ttc,
		                	$obj->product_type,
		                	$obj->date_start,
		                	$obj->date_end
		                ) ;
		                $i++ ;
	            	}
	            }
	            $this->db->free($resql);
	        }
	        else
	        {
				$error++ ;
	        	$this->errors[]=$langs->trans("ErrorLoadingBasket") ;
	            dol_syslog("Cashdesk::Cashdesk ".$this->db->lasterror(), LOG_ERR);
	        }
	        
	        // Load existing paiements
	        $sql = "SELECT p.rowid," ;
			$sql.= " p.datec,";
			$sql.= " p.datep,";
			$sql.= " p.amount,";
			$sql.= " p.fk_paiement,";
			$sql.= " c.libelle,";
			$sql.= " p.fk_bank";
	        $sql.= " FROM ".MAIN_DB_PREFIX."basket_paiement as p, ".MAIN_DB_PREFIX."c_paiement as c";
	        $sql.= " WHERE p.fk_basket = '".$this->basket_id . "' AND c.id = p.fk_paiement";
	        	    
	    	dol_syslog("Cashdesk::Cashdesk sql=".$sql, LOG_DEBUG);
	        $resql=$this->db->query($sql);
	        if ($resql)
	        {
	        	$num = $this->db->num_rows($resql);
	        	$i = 0 ;
	            if ($num)
	            {
	            	while($i < $num) {
		                $obj = $this->db->fetch_object($resql);
		                $this->paiements[] = new Basket_paiement(
		                	$DB,
		                	$obj->rowid,
		                	$obj->datec,
		                	$obj->datep,
		                	$obj->amount,
		                	$obj->fk_paiement,
		                	$obj->libelle,
		                	$obj->fk_bank
		                ) ;
		                $this->amount_paid += $obj->amount ;
		                $i++ ;
	            	}
	            }
	            $this->db->free($resql);
	        }
	        else
	        {
	        	$error++ ;
	        	$this->errors[]=$langs->trans("ErrorLoadingBasket") ;
	            dol_syslog("Cashdesk::Cashdesk ".$this->db->lasterror(), LOG_ERR);
	        }
    	}
    	
        $this->ref = $this->getNextNumRef($this->customer) ;
		
    	if($error) return -1*$error ;
        else return 1;
    }
    
    /**
     * Function that loads categories and product to display it in the product zone
     * @return int 1 if OK, -n for n errors
     */
    function loadCategoriesAndProducts() {
   		
    	$error = 0 ;
    	dol_syslog('Cashdesk::loadCategoriesAndProducts()', LOG_DEBUG) ;
    	$id_root = 9 ;
    	$this->root_categorie = new Categorie($this->db) ;
    	if($this->root_categorie->fetch($id_root) != -1) {
    		//Root categorie loading OK
    		dol_syslog('Root categorie loaded : ' . $cat_root->description, LOG_DEBUG) ;
    		$this->root_categorie->filles = $this->root_categorie->get_filles() ;
    		if($this->root_categorie->filles != -1) {
    			//First level of categories loading OK
		    	foreach($this->root_categorie->filles as $cat) {
		    		$cat->filles = $cat->get_filles() ;
		    		if($cat->filles != -1) {
		    			//Second level of categories loading OK
			    		foreach($cat->filles as $cat_2) {
			    			$cat_2->products = $cat_2->get_type('product', 'Product') ;
			    		}
		    		}
		    		else {
		    			dol_syslog("Cashdesk::loadCategoriesAndProduct error loading second level of categories", LOG_ERROR) ;
		    			$error++ ;
		    			$this->errors[] = $langs->trans("ErrorLoadingSecondLevelCategories") ;
		    		}
		    	}
    		}
    		else {
    			dol_syslog("Cashdesk::loadCategoriesAndProduct error loading first level of categories", LOG_ERROR) ;
    			$error++ ;
    			$this->errors[] = $langs->trans("ErrorLoadingFirstLevelCategories") ;
    		}
    	}
    	else {
    		dol_syslog("Cashdesk::loadCategoriesAndProduct error loading root category", LOG_ERROR) ;
    		$error++ ;
    		$this->errors[] = $langs->trans("ErrorLoadingRootCategory") ;
    	}
		if($error) return -1*$error ;
		else return 1;
    }
    
    
    function displayTitle() {
    	global $langs;
    	include(DOL_DOCUMENT_ROOT.'/pointofsale/tpl/title_classical.tpl.php') ;
    }
    
    
    function displayCustomer() {
    	global $langs, $form, $conf ;
    	include(DOL_DOCUMENT_ROOT.'/pointofsale/tpl/customer_classical.tpl.php') ;
    }
    
    
    function displayProducts() {
    	global $langs ;
    	include(DOL_DOCUMENT_ROOT.'/pointofsale/tpl/products_categorieslists.tpl.php') ;
    }
    
    
    function displayControls() {
    	global $langs;
    	include(DOL_DOCUMENT_ROOT.'/pointofsale/tpl/controls_numericpad.tpl.php') ;
    }
    
    
    function displayBasket() {
    	global $langs;
    	include(DOL_DOCUMENT_ROOT.'/pointofsale/tpl/basket_classical.tpl.php') ;
    }
    
    /**
     * Function used to dump basket table from old basket and create a new basket
     * @param $user dolibarr user of the salesman
     * @return int -n if KO, 1 if OK
     */
    function newBasket($user) {
    	global $conf, $langs;
    	$error = 0 ;
		
    	//Delete previous basket of this user (if there is one)
    	if($this->dumpBasket($user) != 1) {
    		//$error++ ;
    	}
    	else {
    	    			
			// Clean parameters
			$this->contact = new Contact($this->db) ;
			$this->total_ttc = 0 ;
			$this->total_tva = 0 ;
			$this->total_ht = 0 ;
			$this->statut = BASKET_DRAFT ;
			
	        // Insert request
			$sql = "INSERT INTO ".MAIN_DB_PREFIX."basket(";
			$sql.= "fk_user, ";
			$sql.= "fk_contact, " ;
			$sql.= "total_ttc, ";
			$sql.= "total_tva, ";
			$sql.= "total_ht, ";
			$sql.= "statut" ;
			
	        $sql.= ") VALUES (";
			$sql.= " '".$user->login."', ";
			$sql.= " NULL, " ;
			$sql.= " '0', ";
			$sql.= " '0', ";
			$sql.= " '0', ";
			$sql.= " '0'" ;
			$sql.= ")";
	
			$this->db->begin();
			
		   	dol_syslog("Cashdesk::newBasket sql=".$sql, LOG_DEBUG);
	        $resql=$this->db->query($sql);
	    	if (! $resql) {
	    		$error++;
	    		$this->errors[]= $langs->trans("ErrorCreatingBasket") ;
	    		dol_syslog("Cashdesk::newBasket ".$this->db->lasterror(), LOG_ERROR) ;
	    	}
			if (! $this->error)
	        {
	            $this->basket_id = $this->db->last_insert_id(MAIN_DB_PREFIX."basket");
	        }
	        
	        $this->ref = $this->getNextNumRef($this->customer) ;
    	}
        // Commit or rollback
        if ($error != 0) {
			$this->db->rollback();
			return -1*$error;
		}
		else {
			$this->db->commit();
            return 1 ;
		}
    }
    
    
    /**
     * Function that removes every 'basket' linked to user $user.
     * @param $user User deletes basket linked to his username
     * @return 1 if ok, -n for n errors
     */
    function dumpBasket($user) {
    	
		global $conf, $langs;
    	$error = 0 ;
		
		foreach($this->lines as $basketDet) {
			$basketDet->delete($user) ;
		}
		
		foreach($this->paiements as $paiement) {
			$paiement->delete($user) ;
		}
		
		$this->lines = array() ;
		$this->paiements = array() ;
		
		//Delete user's basket
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."basket";
		$sql.= " WHERE fk_user='".$user->login."'";
	
		$this->db->begin();
		
		dol_syslog("Cashdesk::dumpBasket sql=".$sql);
		$resql = $this->db->query($sql);
    	if (! $resql) {
    		$error++;
    		$this->errors[]=$langs->trans("ErrorDeletingBasket");
    		dol_syslog("Cashdesk::dumpBasket ".$this->db->lasterror(), LOG_ERROR) ;
    	}
		
        // Commit or rollback
		if ($error) {
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    	
    }
        
    
    /**
     * Function to update database fields for basket object.
     * @param $user User that updates the basket
     * @return 1 if OK, -n for n errors
     */
    function updateBasket($user) {
    	global $langs ;
    	
        $sql = "UPDATE ".MAIN_DB_PREFIX."basket SET";
        $sql.= " fk_contact=".(isset($this->contact->id)?trim($this->contact->id):"null").",";
		$sql.= " tms=".(dol_strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " total_ttc=".(isset($this->total_ttc)?price2num($this->total_ttc):"null").",";
		$sql.= " total_tva=".(isset($this->total_tva)?price2num($this->total_tva):"null").",";
		$sql.= " total_ht=".(isset($this->total_ht)?price2num($this->total_ht):"null").",";
		$sql.= " statut=".(isset($this->statut)?$this->statut:"0")."";

        $sql.= " WHERE rowid=".$this->basket_id;

		$this->db->begin();
        
		dol_syslog("Cashdesk::updateBasket sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) {
    		$error++;
    		$this->errors[]=$langs->trans("ErrorUpdatingBasket") ;
    		dol_syslog("Cashdesk::updateBasket ".$this->db->lasterror(), LOG_ERROR) ;
    	}
        		
        // Commit or rollback
		if ($error)
		{
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}		
    }
    
    
    /**
     * Function to add a new basket_det line in database
     * @param $productId rowid of the product linked to the line
     * @param $qty Quantity
     * @param $remise_percent Discount in percent
     * @param $remise Discount
     * @param $product_type Product type (product or service)
     * @param $date_start Date start (if service)
     * @param $date_end Date end (if service)
     * @return >0 if OK, < 0 if KO 
     */
    function addLine($user, $productId, $qty, $remise_percent='', $remise='', $product_type=0, $date_start='', $date_end='') {
    	global $langs ;
    	
    	$product = new Product($this->db) ;
    	$error = 0 ;
    	
    	if($product->fetch($productId)<0) {
    		$error++ ;
    		$this->errors[] = $langs->trans("ErrorLoadingProductToAdd") ;
    		return -1 ;
    	}
    	
    	if($remise_percent && !$remise) {
    		$remise = $product->price_ttc * $remise_percent / 100 ;
    	}
    	
    	if($remise && !$remise_percent) {
    		$remise_percent = $remise / $product->price_ttc * 100 ;  
    	}
    	
    	$basket_det = new Basket_det($this->db) ;
    	
    	$basket_det->fk_basket = $this->basket_id ;
    	
    	$basket_det->fk_product = $product->id ;
		$basket_det->qty = $qty ;
		$basket_det->remise = $remise ;
		$basket_det->remise_percent = $remise_percent ;
		$basket_det->tva_taux = $product->tva_tx ;
		$basket_det->subprice = $product->price_ttc;
		$basket_det->price = round($basket_det->subprice-$basket_det->remise, 2) ;
		$basket_det->total_ttc = $qty * $basket_det->price	;
		$basket_det->total_ht = $basket_det->total_ttc / (1+($basket_det->tva_taux / 100));
		$basket_det->total_tva = $basket_det->total_ttc - $basket_det->total_ht;
		$basket_det->product_type = $product_type ;
		$basket_det->date_start = $date_start ;
		$basket_det->date_end = $date_end ;
		
		if($basket_det->create($user) > 0) {
			dol_syslog("Cashdesk::addLine basket_det with id '".$basket_det->id."' created.", LOG_DEBUG) ;
		} else {
			$error++ ;
			$this->errors[] = $langs->trans("ErrorAddingProduct") ;
			dol_syslog("Cashdesk::addLine Error creating new basket_det line", LOG_ERROR) ;
		}
		
		$basket_det->product = $product ;
		
		$this->lines[] = $basket_det ;
		$this->total_ttc += $basket_det->total_ttc ;
		$this->total_tva += $basket_det->total_tva ;
		$this->total_ht += $basket_det->total_ht ;
		
		if($this->updateBasket($user) < 0) {
			$error++ ;
		}
		
		if($error) return -1*$error ;
		else return 1 ;
    }
    
    /**
     * Function used to delete a basket line in DB
     * @param $user User that deletes the basket line
     * @param $selectedLine rowid of the line to delete
     * @return < 0 if KO, > 0 if OK
     */
    function deleteLine($user, $selectedLine) {
    	
    	if(!$selectedLine) {
    		$this->errors[] = $langs->trans("NoLineSelected") ; 
    		return -1 ;
    	}
    	
    	for($i = 0; $i<count($this->lines); $i++) {
    		if($this->lines[$i]->id == $selectedLine) {
    			
				$this->total_ttc -= $this->lines[$i]->total_ttc ;
				$this->total_tva -= $this->lines[$i]->total_tva ;
				$this->total_ht -= $this->lines[$i]->total_ht ;
				$this->updateBasket($user) ;
    			$this->lines[$i]->delete($user) ;
    			unset($this->lines[$i]) ;
    			break ;
    		}
    	}
    	return 1 ;
    }
    
    /**
     * 
     * @param $user user that is using the point of sale
     * @param $action type of paiement (CB, CHQ or LIQ in french)
     * @param $amount amount of paiement
     * @return 1 if OK, < 0 if KO
     */
    function addPaiement($user, $action, $amount) {
    	global $langs, $conf ;
    	
    	if($amount > $this->total_ttc-$this->amount_paye) {
    		//Amount is more than what is left to pay
    		$error++ ;
    		$this->errors[] = $langs->trans("PaymentTooHigh") ;
    	}
    	else {
	    	$paiement = new Basket_paiement($this->db) ;
	    	
	    	$paiement->amount = $amount ;
	    	$paiement->fk_basket = $this->basket_id ;
	    	
	    	
	    	switch($action) {
	    		case $langs->trans("PaymentCard") :
	    			$paiement->fk_paiement = 6 ; // correspond aux codes de la table llx_c_paiement
	    			$paiement->fk_bank = $conf->global->POS_CARD_ACCOUNT ;
	    			break ;
	    		case $langs->trans("PaymentCheck") :
	    			$paiement->fk_paiement = 7 ;
	    			$paiement->fk_bank = $conf->global->POS_CHECK_ACCOUNT ;
	    			break ;
	    		case $langs->trans("PaymentCash") :
	    			$paiement->fk_paiement = 4 ;
	    			$paiement->fk_bank = $conf->global->POS_CASH_ACCOUNT ;
	    			break ;
	    	}
	    	
	    	if($paiement->fk_bank == 0) {
	    		$error++ ;
	    		$this->errors[] = $langs->trans('CashdeskAccountNotConfigured') ;
	    		dol_syslog('Cashdesk::addPaiement Account not defined in module configuration', LOG_ERROR) ;
	    	}
	    	
	    	if($paiement->create($user) < 0) {
	    		$error++ ;
	    		$this->errors[] = $langs->trans("ErrorAddingPayment") ;
	    	}
	    	
	    	$paiement->fetch($paiement->id) ;
	    	
	    	$this->paiements[] = $paiement ;
	    	$this->amount_paid += $paiement->amount ;
	    	
	    	if($this->amount_paid == $this->total_ttc) {
	    		$this->statut = BASKET_PAID ; // 2 ;
	    		if($this->updateBasket($user) < 0) {
	    			$error++ ;
	    		}
	    	}
    	}
        if($error) return -1*$error ;
    	else return 1 ;
    	
    }
    
    
    /**
     * Function used to delete a payment line in DB
     * @param $user User that deletes the line
     * @param $idPaiement rowid of payment to delete
     * @return 1 if OK, < 0 if KO
     */
    function deletePaiement($user, $idPaiement) {

    	for($i = 0; $i<count($this->paiements); $i++) {
    		if($this->paiements[$i]->id == $idPaiement) {
    			$this->amount_paid -= $this->paiements[$i]->amount ;
    			if($this->paiements[$i]->delete($user) < 0) {
    				$error++ ;
    				$this->errors[] = $langs->trans("ErrorDeletingPayment") ;
    			}
    			unset($this->paiements[$i]) ;
    		}
    	}
    	
    	if($this->statut == BASKET_PAID) {
    		$this->statut = BASKET_VALIDATED ;
    		if($this->updateBasket($user) < 0) $error++ ;
    	}
    	if($error) return -1*$error ;
    	else return 1 ;
    }
    
   	/**
	 * Return next reference of invoice not already used
	 * according to numbering module defined into constant POS_FACTURE_ADDON
	 * @param	   soc  		            objet company
	 * @return     string                  free ref
	 */
	function getNextNumRef($soc)
	{
		global $conf, $db, $langs;
		$langs->load("bills");

		// Clean parameters (if not defined or using deprecated value)
		if (empty($conf->global->POS_FACTURE_ADDON)) $conf->global->POS_FACTURE_ADDON='mod_facture_terre';

		$mybool=false;

		$file = $conf->global->POS_FACTURE_ADDON.".php";
		$classname = $conf->global->POS_FACTURE_ADDON;
		// Include file with class
		$dir = DOL_DOCUMENT_ROOT."/pointofsale/inc/model/num/" ;
		// Load file with numbering class (if found)
		$mybool|=@include_once($dir.$file);

		// For compatibility
		if (! $mybool)
		{
			$file = $conf->global->POS_FACTURE_ADDON."/".$conf->global->POS_FACTURE_ADDON.".modules.php";
			$classname = "mod_facture_".$conf->global->POS_FACTURE_ADDON;
			// Include file with class
			// Load file with numbering class (if found)
			$mybool|=@include_once($dir.$file);
		}
		//print "xx".$mybool.$dir.$file."-".$classname;

		if (! $mybool)
		{
			dol_print_error('',"Failed to include file ".$file);
			return '';
		}

		$obj = new $classname();
		

		$numref = "";
		$numref = $obj->getNumRef($soc,$this);

		if ( $numref != "")
		{
			return $numref;
		}
		else
		{
			dol_print_error($db,"Facture::getNextNumRef ".$obj->error);
			return '';
		}
	}
	
	/**
	 * Function that creates a new contact in the generic third and links it the current basket
	 * @param $user user that creates the contact
	 * @param $familyname
	 * @param $firstname
	 * @param $address
	 * @param $zip
	 * @param $city
	 * @param $phone
	 * @param $email
	 * @return 1 if OK, <0 if KO
	 */
	function addContact($user, $name, $idcivilite, $address, $zip, $city, $phone, $email) {
		global $conf ;
		
		if(!$name) {
			return -1 ;
		}
		$this->contact = new Contact($this->db) ;
		
		$this->contact->socid = $conf->global->POS_GENERIC_THIRD ;
		$this->contact->name  = $name ;
		$this->contact->civilite_id  = $idcivilite ;
		$this->contact->address  = $address ;
		$this->contact->cp = $zip ;
		$this->contact->ville = $city ;
		$this->contact->phone_pro = $phone ;
		$this->contact->email = $email ;
		
		if($this->contact->create($user) > 0)
			return 1 ;
		else {
			$this->errors[] = $langs->trans("ErrorAddingContact") ;
			return -1 ;
		}
	}
	
	/**
	 * Function that links a existing contact of the generic third to the current bakset
	 * @param $user user that links the contact
	 * @param $id_contact rowid of the selected contact
	 * @return  
	 */
	function setContact($user, $id_contact) {
		
		$this->contact = new Contact($this->db) ;
		
		$ret = $this->contact->fetch($id_contact) ; 
		
		if($ret == 0) {
			$this->errors[] = $langs->trans("ContactNotFound") ;
			return -1 ;
		}
		if($ret < 0) {
			//Error already displayed with setContact returning -1.
			//$this->errors[] = $langs->trans("ErrorSettingContact") ;
			return -1 ;
		}
		if($ret == 1) {
			if($this->updateBasket($user) == 1) return 1 ;
			else return -1 ;
		}
		
	}
	
	
	function unSetContact($user) {
		
	}
	
	
	/**
	 * Function that will create an invoice object from the basket object
	 * @param $user
	 * @return 
	 */
	function createInvoice($user) {
		
		global $langs ;
		
		$error = 0 ;
		// Create Facture object for to create it in DB
		$facture = new Facture($this->db) ;
		$facture->socid = $this->customer->id ;
		$facture->ref = $cashdesk->ref ;
		$facture->type = 0 ; //Standard invoice
		$facture->date = dol_now() ;
		$facture->note_public = "" ;
		$facture->note = "" ;
		$facture->ref_client = "" ;
		$facture->modelpdf = "" ;
		$facture->fk_project = "" ;
		$facture->cond_reglement_id= 1 ;
		$facture->mode_reglement = 1 ;
		
		if($facture->create($user) < 0) {
			$error++ ;
			dol_syslog('Cashdesk::createInvoice Error creating invoice', LOG_ERROR) ;
		}
		
		//If contact defined for basket, link it to invoice
		if($this->contact->id) {
			if($facture->add_contact($this->contact->id, 'BILLING') < 0) {
				$error++ ;
				dol_syslog('Cashdesk::createInvoice Error adding contact', LOG_ERROR) ;
			}
		}
		//Add client to invoice for PDF generation 
		$facture->client = $this->customer ;

		//Add Basket_det lines to invoice in DB
		foreach($this->lines as $line) {
			$ret = $facture->addline(
				$facture->id,
				'',
				0,
				$line->qty,
				$line->tva_taux,
				0,
				0,
				$line->fk_product,
				$line->remise_percent,
				$line->date_start,
				$line->date_end,
				0,
				0,
				'',
				'TTC',
				$line->subprice,
				$line->product_type) ;
			if($ret < 0) {
				$error++ ;
				dol_syslog('Cashdesk::createInvoice Error adding line to the created invoice', LOG_ERROR) ;
			}
		}
		
		//Validate invoice with $this->ref as ref
		if($facture->validate($user, $this->ref) < 0) {
			$error++ ;
			dol_syslog("Cashdesk::createInvoice Error validating invoice", LOG_ERROR) ;
		}
		
		//Create Paiement for each Basket_paiement
		$paiement = '' ;
		$emetteur_nom = $this->contact->name?$this->contact->name:$this->customer->nom ;
		$label = $langs->trans('Payment').' '.$emetteur_nom ;
		$emetteur_banque = '' ;
		$basket_paiement = new Basket_paiement($this->db) ;
		foreach($this->paiements as $basket_paiement) {
			
			$paiement = new Paiement($this->db) ;
			$paiement->amounts[$facture->id] = $basket_paiement->amount ;
			$paiement->paiementid = $basket_paiement->fk_paiement ;
			$paiement->num_paiement = '' ;
			$paiement->datepaye = dol_now() ;
			if($paiement->create($user) < 0) {
				$error++ ;
				dol_syslog("Cashdesk::createInvoice Error creating Paiement", LOG_ERROR) ;
				break ;
			}
			if($paiement->addPaymentToBank($user,'payment', $label ,$basket_paiement->fk_bank,$emetteur_nom,$emetteur_banque) < 0) {
				$error++ ;
				dol_syslog("Cashdesk::createInvoice Error creating Bank movement for Paiement", LOG_ERROR) ;
				break ;
			}
			
			$facture->paye += $basket_paiement->amount ;
		}
		
		//If amount paid is enough, set invoice as paid.
		if($this->amount_paid == $this->total_ttc) {
			if($facture->set_paid($user) < 0) {
				$error++ ;
				dol_syslog("Cashdesk::createInvoice Error setting invoice paid", LOG_ERROR) ;
			}
		}
		
		if($error) {
			$this->errors[] = $langs->trans('ErrorCreatingInvoice') ;
			return -1*$error ;
		}
		dol_syslog('Cashdesk::createInvoice Invoice '.$facture->id.' created.', LOG_DEBUG) ;		
		
		//PDF generation
		$facture->setDocModel($user, 'crabe');
		
		// Define output language
		$outputlangs = $langs;
		$newlang='';
		if ($conf->global->MAIN_MULTILANGS && empty($newlang) && ! empty($_REQUEST['lang_id'])) $newlang=$_REQUEST['lang_id'];
		if ($conf->global->MAIN_MULTILANGS && empty($newlang)) $newlang=$facture->client->default_lang;
		if (! empty($newlang)) {
			$outputlangs = new Translate("",$conf);
			$outputlangs->setDefaultLang($newlang);
		}
		$result=facture_pdf_create($this->db, $facture, '', $facture->modelpdf, $outputlangs);
		if ($result <= 0) {
			dol_print_error($this->db,$result);
			exit;
		}
		$this->errors[] = $langs->trans('InvoicePrinted') ;
		return 1 ;
		
	}
    
    
}
?>
