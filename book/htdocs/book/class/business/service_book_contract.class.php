<?php
//Start of user code
	
/* Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 * Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
 * Copyright (C) 2010      Auguria SARL         <info@auguria.org>
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
        \file       htdocs/product/service_book_contract.class.php
        \ingroup    book_contract
        \brief      *complete here*
		\version    1.0
		\author		Patrick Raguin
*/

/**
        \class      book_contractService
        \brief      Service managing book_contract
		\remarks	
*/
class service_book_contract
{
	private $dao ;
	protected $db ;	
	private $error;
	
	public function service_book_contract($db)
	{	
		$this->db = $db ;
		$this->dao = new dao_book_contract($db) ;
	}
	
	public function getError() { return $this->error;}
	
	/*
     *      \brief      return an array with entries of the entity in database. This must be a select query and the rowid field must be selected as well.
     *      \return      array(ID => array(property => array(info => value))
     */
	public function getFromQuery($query,$toDisplay='',$where='',$order=''){
		
		global $langs ;
		
		// Check the query
		if (stristr($query,'SELECT')) dolibarr_syslog("service_book_contract::getFromQuery. Query must be a SELECT statement : sql=".$query, LOG_DEBUG);
		if (stristr($query,'rowid')) dolibarr_syslog("service_book_contract::getFromQuery. Query must select the rowid :  sql=".$query, LOG_DEBUG);
		
		// execute the query
		$all = dao_book_contract::selectQuery($this->db,$query,$where,$order) ;
		
		// return the result
		return $this->selectToArray($all,$toDisplay,getfieldToKeyFieldArray($query)) ;

	}
	
	/*
     *      \brief      	return an array with entries from the result of query in parameter
	 * 		\param $result	result of a query
	 *		\param $toDisplay array("fieldName" => data). fieldname, the fields to be displayed
     *      \return     	array(ID => array(property => array(info => value))
     */
	public function selectToArray($result,$toDisplay ='',$keys=array()){

		$liste = array() ;

		// If the query return smthg
		if($result){

			// we browse the result set using the $item var
			while($item = $this->db->fetch_object($result)){

				//make the array
				foreach($item as $entityfieldName => $value){


					
					// get the name of the field without the entity
					$fieldName = strtolower(str_replace("book_contract_","",$entityfieldName,$nbrReplace)) ;


					// display only recorded fields
					if( @in_array($entityfieldName,$toDisplay) ||($toDisplay=='') ){

						// generate or not the link
						if(isset($item->$keys[$entityfieldName])){
							$link = $this->getLink($fieldName,$item->$keys[$entityfieldName]) ;
						} else {
							$link = '' ;
						}
						$liste[$item->rowid][$fieldName] = array(
						
							"name" => $fieldName,
							"type" => $this->getType($fieldName),
							"value" => formatValue($this->getType($fieldName),$value),
							"link" => $link
						) ;

					}
	
				} // end foreach

			} // end while

		}//end if

		return $liste ;

	}
	
	
	//Start of edit by Pierre Morin in order to make the module compatible with Dolibarr 2.8+
	
	/*
     *      \brief      return an array with entries of the entity in database
	 *		\param		$where	condition of the WHERE clause (ie. "t.label = 'bar'")
	 *		\param		$order	condition of the ORDER BY clause (ie. "t.rowid DESC")
     *      \return     array(array(properties...,URL))
     */
	public function getAllFkDisplayed($id='',$where='',$order=''){
		
		global $langs ;
	
		if($id != '') $this->dao->fetch($id);
		
		$listFk = array() ;
	

		//service to show a list of societies that can be selected to be the editor of the book for the contract :
		$fkService = new service_book_contract_societe($this->db) ;
		
		//fields to be selected
		$fields = array("rowid", "nom") ;
		
		//We do not need this line (too complicated for what we want) :
		//$DisplayedFields = $fkService->getfkDisplayedFields() ;
		
		$order = " nom";

		//We do not need this 2 lines (too complicated for what we want) :
//		foreach($DisplayedFields as $field)
//			$fields[] = $field["fieldname"].' as '.$field["entity"].'_'.$field["fieldname"] ;
			
		//select societies according to conditions
		$all = $fkService->getDao()->select($this->db,$fields,$where,$order) ;
		
		//Retriving the value of the selected society
		if(isset($_POST['book_contract_fk_soc'])) 
		{
			$value = $_POST['book_contract_fk_soc'];
		}
		else
		{
			$value = $this->dao->getFk_soc();
		}
		
		// add data about the societies in the array
		$listFk["book_contract_fk_soc"] = array(
					"entity" => "book_contract",
					"attribute" => "fk_soc",
					"value" => $value,
					"label" => $langs->trans("book_contract_fk_soc"),
					"null" => 1,
					"values" => $fkService->selectToArray($all,array("nom"))
					);

				
		return $listFk ;
	}
	
	//End of edits by Pierre Morin
	
	/*
     *      \brief      return an array with entries of the entity in database
	 *		\param		$where	condition of the WHERE clause (ie. "t.label = 'bar'")
	 *		\param		$order	condition of the ORDER BY clause (ie. "t.rowid DESC")
     *      \return     array(array(properties...,URL))
     */
	public function getAllListDisplayed($where='',$order=''){
		
		//Catching the list of fields to display
		
		$attributes =array(); // attributes to use in the SELECT query
		$labels = array();	  // name of the field to be displayed from the query.
		$join = array();	  // tables to join
		
		
		
		//Current class
		$attributes[] = "book_contract.taux as book_contract_taux";
		$attributes[] = "book_contract.date_deb as book_contract_date_deb";
		$attributes[] = "book_contract.date_fin as book_contract_date_fin";
		$attributes[] = "societe.nom as societe_nom";
		$labels[] = "book_contract_taux";
		$labels[] = "book_contract_date_deb";
		$labels[] = "book_contract_date_fin";
		$labels[] = "societe_nom";
		
		$join[] = "LEFT OUTER JOIN ".MAIN_DB_PREFIX."societe societe ON societe.rowid = book_contract.fk_soc";
		
		$query = "SELECT book_contract.rowid, societe.rowid as soc_id, ".implode(", ",$attributes)." ";
		$query.= "FROM ".MAIN_DB_PREFIX."product_book_contract book_contract ".implode(" ",$join);
		$order = "book_contract_date_deb DESC";

		return $this->getFromQuery($query,$labels,$where,$order) ;
	}
	
	/*
     *      \brief     return an array with entries of the fields to be displayed in the list
     *      \return    array(property => translation)
     */
	public function getListDisplayedFields(){
		
		global $langs ;
		
		//get 1st part of the list from the parent object
		
		//The second using the current object properties
		$fields["book_contract_taux"] = array(
			"entity" => "book_contract",
			"attribute" => "taux",
			"type" => $this->getType("taux"),
			"label" => $langs->trans("book_contract_taux")
		) ;
		
		$fields["book_contract_date_deb"] = array(
			"entity" => "book_contract",
			"attribute" => "date_deb",
			"type" => $this->getType("date_deb"),
			"label" => $langs->trans("book_contract_date_deb")
		) ;
		
		$fields["book_contract_date_fin"] = array(
			"entity" => "book_contract",
			"attribute" => "date_fin",
			"type" => $this->getType("date_fin"),
			"label" => $langs->trans("book_contract_date_fin")
		) ;
		
		$fields["book_contract_fk_soc"] = array(
			"entity" => "book_contract",
			"attribute" => "fk_soc",
			"type" => $this->getType("fk_soc"),
			"label" => $langs->trans("book_contract_fk_soc")
		) ;
			


		return $fields ;
	}
	
	/*
     *      \brief     return an array with entries of the fields to be displayed in the combobox when invoked as foreign Key
     *      \return    array(property => translation)
     */
	public function getfkDisplayedFields(){
		
		global $langs ;
		
		$fields = array()  ;
		
		
		return $fields ;
		
		
	}

	/*
     *      \brief      try to add the element
     *      \return     $error		'' if ok, string of the error if error.
     */
	public function add($post){
	
		global $langs ;
		
		$_book_contract = $this->dao;
		
		
		//rowid
		
		
		//taux
			$_book_contract->setTaux($post["book_contract_taux"]);
			
		//date_deb
				$date = dolibarr_mktime(
						0,
						0,
						0,
						 $post["book_contract_date_debmonth"], 
						 $post["book_contract_date_debday"], 
						 $post["book_contract_date_debyear"]);						
	
			
				if (! ($date > 0)) 
				{
						$date = '';
				}		
				$this->dao->setDate_deb(dolibarr_date('Y-m-d H:i:s',$date));
			
		//date_fin
				$date = dolibarr_mktime(
						0,
						0,
						0,
						 $post["book_contract_date_finmonth"], 
						 $post["book_contract_date_finday"], 
						 $post["book_contract_date_finyear"]);						
	
				
				if (! ($date > 0)) 
				{
						$date = '';
				}		
				$this->dao->setDate_fin(dolibarr_date('Y-m-d H:i:s',$date));
			
		//quantity
			if($post["book_contract_quantity"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","quantity");
			}
			$_book_contract->setQuantity($post["book_contract_quantity"]);
			
		
			//fk_user
			if($post["book_contract_fk_user"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_user");
			}
			$_book_contract->setFk_user($post["book_contract_fk_user"]);
			
			//fk_product_book
			if($post["book_contract_fk_product_book"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_product_book");
			}
			$_book_contract->setFk_product_book($post["book_contract_fk_product_book"]);
			
			//fk_soc
			if($post["book_contract_fk_soc"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_soc");
			}
			$_book_contract->setFk_soc($post["book_contract_fk_soc"]);
			
			//fk_product_droit
			if($post["book_contract_fk_product_droit"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_product_droit");
			}
			$_book_contract->setFk_product_droit($post["book_contract_fk_product_droit"]);

		if(!isset($error))
		{
			// If all the fields are filled, sending the creation query
			if ( ($_book_contract->create())==-1)
		    {
		      	
		    	// If an error occured during creation
		      	$this->error = $_book_contract->getError();
		      	return -1;
		      	
		    }
		    else
		    {
		    
		    	// If everything is OK
		    	return $_book_contract->getId();

		    }
		}
		else
		{
			$this->error = $error;
			return -1;
		}
		
		
	}

	
	/*
     *      \brief     
     *      \return    
     */
	public function newContract($post)
	{

		global $user, $langs;

		if($post['product_ref'] == '')
		{
			$this->error = $langs->trans("ErrorFieldRequired",$langs->trans("Ref"));
			return -1;
		}
		if($post['book_contract_date_deb'] == '')
		{
			$this->error = $langs->trans("ErrorFieldRequired",$langs->trans("date_deb"));
			return -1;
		}
		if($post['book_contract_date_fin'] == '')
		{
			$this->error = $langs->trans("ErrorFieldRequired",$langs->trans("date_fin"));
			return -1;
		}
		if($post['book_contract_taux'] == '')
		{
			$this->error = $langs->trans("ErrorFieldRequired",$langs->trans("taux"));
			return -1;
		}
		if($post['book_contract_quantity'] == '')
		{
			$this->error = $langs->trans("ErrorFieldRequired",$langs->trans("quantity"));
			return -1;
		}
		if($post['book_contract_fk_soc'] == '')
		{
			$this->error = $langs->trans("ErrorFieldRequired",$langs->trans("societe"));
			return -1;
		}
		
		
		$fourn = new Fournisseur($this->db);
		if($fourn->fetch($_POST["book_contract_fk_soc"]) < 0)
		{
			$this->error = $fourn->error;
			return -1;
		}
		
		//In order to have a rate as a float, even with "french notation" :
		$post['book_contract_taux'] = str_replace(",", ".", $post['book_contract_taux']);
		
		//Calcul du prix du droit par rapport au prix de l'article et du taux du droit (contrat)
		//Calculation of the rights price from the article price and the rights rate (contract)
		$book_id = $post['book_contract_fk_product_book'];
		$book = new Product($this->db);
		
		if($book->fetch($book_id) < 0)
		{
			$this->error = "Error : Fetch book (".$book_id.")";
			return -1;
		}
		$price = ($book->price_ttc/100) * $post['book_contract_taux'];
		
		//Creation of the droit_editeur product
		$product_droit = new Product($this->db);
		$product_droit->ref = "cda_".$post['product_ref'];
		$product_droit->libelle = "cda_".$post['product_ref'];

		$product_droit->price_ttc = $price;
		$product_droit->price_base_type = 'TTC';
		$product_droit->tva_tx = "5.5";
		
		$product_droit->type = 0;
		$product_droit->status = 0;
		$product_droit->finished = 0;
		
		$product_droit->canvas = "default@book"; // in order to avoid "non-instanciated" product
		
		$id_product_droit = $product_droit->create($user);
		if($id_product_droit < 0)
		{
			$this->error = $product_droit->error();
			return -1;
		}
		$post['book_contract_fk_product_droit'] = $id_product_droit;
		
		//Ajout product_droit dans la composition du book
		$service_compo = new service_product_composition($this->db);
		$post_compo['product'] = $product_droit->id;
		$post_compo['id_product'] = $book_id;
		$post_compo['qte'] = 1;
		$post_compo['etat_stock'] = 1;
		$id_compo = $service_compo->add($post_compo);
		if($id_compo < 0)
		{
			$this->error= $service_compo->getError();
			return -1;
		}
		
		//Ajout product_fournisseur
		if($product_droit->add_fournisseur($user,$fourn->id,$product_droit->ref) < 0)
		{
			$this->error = $product_droit->error;
			return -1;
		}
		
		//Ajoute prix fournisseur
		$id_product_fourn = $product_droit->product_fourn_id;
		$fourn_product = new ProductFournisseur($this->db);
		$fourn_product->id = $id_product_droit;
		
		if($fourn_product->fetch_fourn_data($fourn->id) < 0)
		{
			$this->error = "Error: fetch_fourn_data (".$id_product_fourn.")";
			return -1;
		}
		$fourn_product->product_fourn_id = $id_product_fourn;

		if($fourn_product->update_buyprice(1,$product_droit->price,$user,$product_droit->price_base_type,$fourn) < 0)
		{
			$this->error = $fourn_product->error;
			return -1;
		}

		//ajout du nouveau contrat
		$id_contract = $this->add($post) ;
		
		if ($id_contract < 0)
		{
			return -1;
		}

      	//Mise à jour book
      	$bookContract = new dao_book($this->db);
      	if($bookContract->fetch($book_id) < 0)
      	{
      		$this->error = $bookContract->getError();
      		return -1;
      	}
      	
      	$bookContract->setContract($id_contract);
      	
      	if($bookContract->update() < 0)
      	{
      		$this->error =  $bookContract->getError();
      		return -1;	
      	}
      	
		//COMMANDE
		$commande = new CommandeFournisseur($this->db);
		$commande->socid = $fourn->id;
		
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."product_fournisseur_price";
		$sql.= " WHERE fk_product_fournisseur = ".$fourn_product->product_fourn_id;
		$sql.= " AND price = '".$product_droit->price."'"; 

		$resql = $this->db->query($sql);
		if ($resql)
		{
			$obj = $this->db->fetch_object($resql);
			$fk_fourn_product_price = $obj->rowid;
			
		}
		else
		{
			$this->error = "Impossible de trouver le prix fournisseur";
			return -1;	
		}
		if($commande->create($user) < 0)
		{
			$this->error = $commande->error;
			return -1;
		}

		if($commande->addline("",$product_droit->price,$post['book_contract_quantity'],$product_droit->tva_tx,$product_droit->id,$fk_fourn_product_price,'',0,'TTC',$product_droit->price_ttc) < 0)
		{	
			$this->error = $commande->error;
			return -1;
		
		}
		
		//Mise à jour information commande / Validation 
		$sql = "UPDATE ".MAIN_DB_PREFIX."commande_fournisseur";
		$sql.= " SET fk_statut = 5";
		$sql.= " ,date_valid = now()";
		$sql.= " ,fk_user_valid = ".$user->id;
		$sql.= " ,fk_methode_commande = 1";
		$sql.= " ,date_commande = now()";
		$sql.= " WHERE rowid = ".$commande->id;
		
		if(!$this->db->query($sql))
		{
			$this->error = "error commande";
			return -1;
		}
		if($commande->fetch($commande->id) < 0)
		{
			$this->error = $commande->error;
			return -1;
		}
		

		//Incrementation du stock
		$dispatchProduct = $commande->DispatchProduct($user,$product_droit->id,$post['book_contract_quantity'],1);
		if($dispatchProduct < 0)
		{	
			$this->error = $commande->error;
			if($this->error == '')
			{
				$this->error = "Error: DispatchProducts";
			}
			return -1;	
		}	

		return 0;


	}
	
	
	
	/*
     *      \brief      try to update the element
     *      \return     $error		'' if ok, string of the error if error.
     */
	public function update($post){
	
		global $langs ;
		$this->dao->fetch($post['id']);
		
		if(!$post["cancel"])
		{


				//book_contract_taux
					
				$this->dao->setTaux($post["book_contract_taux"]);
			
				//book_contract_date_deb
				$date = dolibarr_mktime(
						0,
						0,
						0,
						 $post["book_contract_date_debmonth"], 
						 $post["book_contract_date_debday"], 
						 $post["book_contract_date_debyear"]);						
	
				
				if (! ($date > 0)) 
				{
						$date = '';
				}		
				$this->dao->setDate_deb($date);
			
				//book_contract_date_fin
				$date = dolibarr_mktime(
						0,
						0,
						0,
						 $post["book_contract_date_finmonth"], 
						 $post["book_contract_date_finday"], 
						 $post["book_contract_date_finyear"]);						
	
				
				if (! ($date > 0)) 
				{
						$date = '';
				}		
				$this->dao->setDate_fin($date);
			
				//book_contract_quantity
				if($post["book_contract_quantity"] == '')
				{
					$error = $langs->trans("ErrorFieldRequired","book_contract_quantity");
				}
					
				$this->dao->setQuantity($post["book_contract_quantity"]);
			
			
			//fk_user
			if($post["book_contract_fk_user"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_user");
			}
			$this->dao->setFk_user($post["book_contract_fk_user"]);
			//fk_product_book
			if($post["book_contract_fk_product_book"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_product_book");
			}
			$this->dao->setFk_product_book($post["book_contract_fk_product_book"]);
			//fk_soc
			if($post["book_contract_fk_soc"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_soc");
			}
			$this->dao->setFk_soc($post["book_contract_fk_soc"]);
			//fk_product_droit
			if($post["book_contract_fk_product_droit"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_product_droit");
			}
			$this->dao->setFk_product_droit($post["book_contract_fk_product_droit"]);
		
			
			if(!isset($error))
			{
				if ( $this->dao->update()!=0)
				{
					$this->error = $this->dao->getError();
					return -1;
				}
			}
			else
			{
				$this->error = $error;
				return -1;
			}

		}
		
		return 0;
		
	}

	/*
     *      \brief      Get data array from an occurence of the element
     *      \return     array if ok, empty array if ko
     */
	public function getAttributesShow($id){
		
		global $langs ;
		
		$data = array() ;

		$this->dao->fetch($id);
		
		$product = new Product($this->db);
		$product->fetch($this->dao->getFk_product_droit());

		
		$data["book_contract_ref"] = array(
			"label" => $langs->trans("Ref"),
			"value" => "<a href='".$this->getLink("fk_product_droit",$this->dao->getFk_product_droit())."'>".formatValue($this->getType("ref"),$product->ref)."</a>"
		);
		
		$data["book_contract_taux"] = array(
			"label" => $langs->trans("book_contract_taux"),
			"value" => vatrate(formatValue($this->getType("taux"),$this->dao->getTaux()),true)
		);
		
		$data["book_contract_date_deb"] = array(
			"label" => $langs->trans("book_contract_date_deb"),
			"value" => formatValue($this->getType("date_deb"),$this->dao->getDate_deb())
		);
		
		$data["book_contract_date_fin"] = array(
			"label" => $langs->trans("book_contract_date_fin"),
			"value" => formatValue($this->getType("date_fin"),$this->dao->getDate_fin())
		);
		
		
		$data["book_contract_quantity"] = array(
			"label" => $langs->trans("book_contract_quantity"),
			"value" => formatValue($this->getType("quantity"),$this->dao->getQuantity())
		);
		
		
		$dao_product = new Product($this->db);
		if($this->dao->getFk_product_droit())
		{
			$dao_product->fetch($this->dao->getFk_product_droit());			
		}


		$data["product_droit_price"] = array(
			"label" => $langs->trans("PriceHT"),
			"value" => price($dao_product->price)
		);
		
		$data["product_droit_price_tcc"] = array(
			"label" => $langs->trans("PriceTTC"),
			"value" => price($dao_product->price_ttc)
		);
			

		$dao_societe = new Societe($this->db);
		$dao_societe->fetch($this->dao->getFk_soc());
		
		$data["book_contract_fk_soc"] = array(
			"label" => $langs->trans("book_contract_fk_soc"),
			"value" => "<a href='".$this->getLink("fk_soc",$this->dao->getFk_soc())."'>".$dao_societe->nom."</a>"
		);

		
		
		
		return $data ;
	}
	

	/*
     *      \brief      Get attributes array from an entity
     *      \return     array(array("class_name","attribut_name","type")) if ok, empty array if ko
     */
	public function getAttributesAdd($post=''){
		
		global $langs ;
		
		$data = array() ;


		
		//Attribut de la classe courante
		$data["product_ref"] = array(
				"entity" => "product",
				"attribute" => "ref",
				"type" => self::getType("ref"),
				"label" => $langs->trans("product_ref"),
				"html_input" => "cda_". getHtmlForm($this->db,self::getType("ref"),"product_ref",$post['product_ref'],false)
			);

		$date_deb = $post['book_contract_date_debyear'].'-'.$post['book_contract_date_debmonth'].'-'.$post['book_contract_date_debday'];
		$data["book_contract_date_deb"] = array(
				"entity" => "book_contract",
				"attribute" => "date_deb",
				"type" => self::getType("date_deb"),
				"label" => $langs->trans(book_contract."_".date_deb),
				"html_input" => getHtmlForm($this->db,self::getType("date_deb"),"book_contract_date_deb",$date_deb,false)
			);
			
		$date_fin = $post['book_contract_date_finyear'].'-'.$post['book_contract_date_finmonth'].'-'.$post['book_contract_date_finday'];
		$data["book_contract_date_fin"] = array(
				"entity" => "book_contract",
				"attribute" => "date_fin",
				"type" => self::getType("date_fin"),
				"label" => $langs->trans(book_contract."_".date_fin),
				"html_input" => getHtmlForm($this->db,self::getType("date_fin"),"book_contract_date_fin",$date_fin,false)
			);
		$data["book_contract_taux"] = array(
				"entity" => "book_contract",
				"attribute" => "taux",
				"type" => self::getType("taux"),
				"label" => $langs->trans(book_contract."_".taux),
				"html_input" => getHtmlForm($this->db,self::getType("taux"),"book_contract_taux",$post['book_contract_taux'],false)
			);
		$data["book_contract_quantity"] = array(
				"entity" => "book_contract",
				"attribute" => "quantity",
				"type" => self::getType("quantity"),
				"label" => $langs->trans(book_contract."_".quantity),
				"html_input" => getHtmlForm($this->db,self::getType("quantity"),"book_contract_quantity",$post['book_contract_quantity'],false)
			);
						
		return $data ;
	}
	

	/*
     *      \brief      Get attributes and data array from an entity
     *      \return     array(array("libelle","value")) if ok, empty array if ko
     */
	public function getAttributesEdit($id){
		
		global $langs ;
		
		$data = array() ;
		
		$this->dao->fetch($id);
		
		
		//Attribut de la classe courante
								
			$data["book_contract_taux"] = array(
					"entity" => "book_contract",
					"attribute" => "taux",
					"type" => self::getType("taux"),	
					"value" => $this->dao->getTaux(),
					"label" => $langs->trans(book_contract."_".taux),
					"html_input" => getHtmlForm(self::getType("taux"),"book_contract_taux",$this->dao->getTaux(),true)
				);
								
			$data["book_contract_date_deb"] = array(
					"entity" => "book_contract",
					"attribute" => "date_deb",
					"type" => self::getType("date_deb"),	
					"value" => $this->dao->getDate_deb(),
					"label" => $langs->trans(book_contract."_".date_deb),
					"html_input" => getHtmlForm(self::getType("date_deb"),"book_contract_date_deb",$this->dao->getDate_deb(),true)
				);
								
			$data["book_contract_date_fin"] = array(
					"entity" => "book_contract",
					"attribute" => "date_fin",
					"type" => self::getType("date_fin"),	
					"value" => $this->dao->getDate_fin(),
					"label" => $langs->trans(book_contract."_".date_fin),
					"html_input" => getHtmlForm(self::getType("date_fin"),"book_contract_date_fin",$this->dao->getDate_fin(),true)
				);
								
			$data["book_contract_quantity"] = array(
					"entity" => "book_contract",
					"attribute" => "quantity",
					"type" => self::getType("quantity"),	
					"value" => $this->dao->getQuantity(),
					"label" => $langs->trans(book_contract."_".quantity),
					"html_input" => getHtmlForm(self::getType("quantity"),"book_contract_quantity",$this->dao->getQuantity(),false)
				);
		

		return $data ;
	}


	/*
     *      \brief      generate the html code for the confirmation form
     *      \return     html code for the form
     */
	public function confirmDeleteForm()
	{
		global $langs ;
		
		// On utlise la fonction de dolibar pour g�n�rer un formulaire de confirmation
		// et on place ce texte g�n�rer dans une variable.
		ob_start() ;
		    $form = new Form($db);
		    $form->form_confirm(DOL_URL_ROOT.'/product/show_contract.php?id='.$_GET["id"],$langs->trans("delete",$langs->trans("book_contract")),$langs->trans("confirm_delete",$langs->trans("book_contract")),"confirm_delete");
		    $html = ob_get_contents();
		ob_end_clean();
		
		return $html ;
	}
	
	/*
     *      \brief      delete the element
     *		\param $id	id to delete
     *      \return     html code for the form
     */
	public function delete($id)
	{
		if($this->dao->delete($id) < 0)
		{
			$this->error = $this->dao->getError();
			return -1;
		}
		return 0;
	
	}
	
	public function getErrors()
	{
		return $this->dao->getErrors();
	}
	

	/*
     *      \brief      	get the type of the field
     *		\param $field	field to ask for the type
     *      \return     	the type
     */
	public function getType($field){
		global $langs ;
		
		switch($field){
			case "societe_nom" :
				return "string" ;
				break ;
			case "rowid" :
				return "integer" ;
				break ;
			case "taux" :
				return "double" ;
				break ;
			case "date_deb" :
				return "date" ;
				break ;
			case "date_fin" :
				return "date" ;
				break ;
			case "quantity" :
				return "integer" ;
				break ;
			case "nom" :
				return "string" ;
				break ;
			case "price_ttc" :
				return "string" ;
				break ;
			case "ref" :
				return "string" ;
				break ;
			default:
				return "" ;
				break ;
		}
	}
	
	/*
     *      \brief      	get the showPage of the entity
     *		\param $field	field to ask for the type
     *      \return     	the type
     */
	public function getLink($field,$ID){
		switch($field){
		
				// foreign key
				case "fk_user" :
					return ".php?id=".$ID ;
					break ;
		
				// foreign key
				case "fk_product_book" :
					return "show_book.php?id=".$ID ;
					break ;
		
				// foreign key
				case "fk_soc" :
					return DOL_URL_ROOT."/fourn/fiche.php?socid=".$ID ;
					break ;
		
				// foreign key
				case "fk_product_droit" :
					return DOL_URL_ROOT."/product/fiche.php?id=".$ID ;
					break ;
				default :
					return "show_contract.php?id=".$ID ;
		}
	}
	

}
//End of user code
?>