<?php
//Start of user code

/* Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 * Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
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
        \file       htdocs/product/service_book_contract_bill.class.php
        \ingroup    book_contract_bill
        \brief      *complete here*
		\version    1.0
		\author		Patrick Raguin
*/

/**
        \class      book_contract_billService
        \brief      Service managing book_contract_bill
		\remarks	
*/
class service_book_contract_bill
{
	private $dao ;
	protected $db ;	
	protected $error;
	
	public function service_book_contract_bill($db)
	{	
		$this->db = $db ;
		$this->dao = new dao_book_contract_bill($db) ;
	}
	
	
	public function getError(){return $this->error;}
	
	/*
     *      \brief      return an array with entries of the entity in database. This must be a select query and the rowid field must be selected as well.
     *      \return      array(ID => array(property => array(info => value))
     */
	public function getFromQuery($query,$toDisplay='',$where='',$order=''){
		
		global $langs ;
		
		// Check the query
		if (stristr($query,'SELECT')) dolibarr_syslog("service_book_contract_bill::getFromQuery. Query must be a SELECT statement : sql=".$query, LOG_DEBUG);
		if (stristr($query,'rowid')) dolibarr_syslog("service_book_contract_bill::getFromQuery. Query must select the rowid :  sql=".$query, LOG_DEBUG);
		
		// execute the query
		$all = dao_book_contract_bill::selectQuery($this->db,$query,$where,$order) ;
		
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
					$fieldName = strtolower(str_replace("book_contract_bill_","",$entityfieldName,$nbrReplace)) ;

					// display only recorded fields
					if( @in_array($entityfieldName,$toDisplay) ||($toDisplay=='') ){

						// generate the link
						$link = $this->getLink($fieldName,$item->$keys['rowid']) ;

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
	
			
		
		//book_contract
		$fkService = new service_book_contract($this->db) ;
		
		//fields to be selected
		$fields = array("rowid") ;
		$DisplayedFields = $fkService->getfkDisplayedFields() ;
		foreach($DisplayedFields as $field)
			$fields[] = $field["attribute"].' as '.$field["entity"].'_'.$field["attribute"] ;
			
		//select fk values according to conditions
		
		$all = dao_book_contract::select($this->db,$fields,$where,$order) ;
		
		//R�cup�ration valeur de la cl� �trang�re dans le post
		if(isset($_POST['book_contract_bill_fk_contract'])) 
		{
			$value = $_POST['book_contract_bill_fk_contract'];
		}
		else
		{
			$value = $this->dao->getFk_contract();
		}
		
		// add data about the foreign keys in the array
		$listFk[] = array(
					"entity" => "book_contract_bill",
					"attribute" => "fk_contract",
					"value" => $value,
					"label" => $langs->trans("book_contract_bill_fk_contract"),
					"null" => 1,
					"values" => $fkService->selectToArray($all,array_keys($fkService->getfkDisplayedFields()))
					);

				
		return $listFk ;
	}
	
	/*
     *      \brief      return an array with entries of the entity in database
	 *		\param		$where	condition of the WHERE clause (ie. "t.label = 'bar'")
	 *		\param		$order	condition of the ORDER BY clause (ie. "t.rowid DESC")
     *      \return     array(array(properties...,URL))
     */
	public function getAllListDisplayed($where='',$order=''){
		
		// r�cup�ration de la liste des champs � afficher
		
		$attributes =array(); // attributes to use in the SELECT query
		$labels = array();	  // name of the field to be displayed from the query.
		$join = array();	  // tables to join
		
		
		
		//Classe courante
		$attributes[] = "book_contract_bill.ref as book_contract_bill_ref";
		$labels[] = "book_contract_bill_ref";
		$attributes[] = "'book_contract_bill_pdf'";
		$labels[] = "book_contract_bill_pdf";
		$attributes[] = "book_contract_bill.date_deb as book_contract_bill_date_deb";
		$labels[] = "book_contract_bill_date_deb";
		$attributes[] = "book_contract_bill.date_fin as book_contract_bill_date_fin";
		$labels[] = "book_contract_bill_date_fin";
		$attributes[] = "book_contract_bill.qtyOrder as book_contract_bill_qtyOrder";
		$labels[] = "book_contract_bill_qtyOrder";
		$attributes[] = "book_contract_bill.qtySold as book_contract_bill_qtySold";
		$labels[] = "book_contract_bill_qtySold";
		
		$query = "SELECT book_contract_bill.rowid, ".implode(", ",$attributes)." ";
		$query.= "FROM ".MAIN_DB_PREFIX."product_book_contract_bill book_contract_bill ".implode("",$join);
		$query.= " JOIN ".MAIN_DB_PREFIX."product_book_contract book_contract ON book_contract.rowid = book_contract_bill.fk_contract";
		$where = "book_contract.fk_product_book = ".$_GET['id'];
		
		return $this->getFromQuery($query,$labels,$where,$order) ;
	}
	
	/*
     *      \brief     return an array with entries of the fields to be displayed in the list
     *      \return    array(property => translation)
     */
	public function getListDisplayedFields(){
		
		global $langs ;
		
		//get 1st part of the list from the parent object
		
		$fields["book_contract_bill_ref"] = array(
			"entity" => "book_contract_bill",
			"attribute" => "ref",
			"type" => $this->getType("ref"),
			"label" => $langs->trans("book_contract_bill_ref")
		) ;
		
		$fields["pdf"] = array(
			"entity" => "book_contract_bill",
			"attribute" => "pdf",
			"type" => $this->getType("pdf"),
			"label" => ""
		) ;
			
		$fields["book_contract_bill_date_deb"] = array(
			"entity" => "book_contract_bill",
			"attribute" => "date_deb",
			"type" => $this->getType("date_deb"),
			"label" => $langs->trans("book_contract_bill_date_deb")
		) ;
		$fields["book_contract_bill_date_fin"] = array(
			"entity" => "book_contract_bill",
			"attribute" => "date_fin",
			"type" => $this->getType("date_fin"),
			"label" => $langs->trans("book_contract_bill_date_fin")
		) ;
		
		$fields["book_contract_bill_qtyOrder"] = array(
			"entity" => "book_contract_bill",
			"attribute" => "qtyOrder",
			"type" => $this->getType("qtyOrder"),
			"label" => $langs->trans("book_contract_bill_qtyOrder")
		) ;		
		
		$fields["book_contract_bill_qtySold"] = array(
			"entity" => "book_contract_bill",
			"attribute" => "qtySold",
			"type" => $this->getType("qtySold"),
			"label" => $langs->trans("book_contract_bill_qtySold")
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
		
		$_book_contract_bill = $this->dao;

		
		//Ref
		if($post["ref"] == '')
		{
			$error = $langs->trans("ErrorFieldRequired","Ref");
		}
		$_book_contract_bill->setRef($post["ref"]);		
		
		/*
		//date_deb
		$date = dolibarr_mktime(
				0,
				0,
				0,
				 $post["book_contract_bill_date_debmonth"], 
				 $post["book_contract_bill_date_debday"], 
				 $post["book_contract_bill_date_debyear"]);						

		
		if (! ($date > 0)) 
		{
				$error = $langs->trans("ErrorFieldRequired","book_contract_bill_date_deb");
		}		
		$this->dao->setDate_deb(dolibarr_date('Y-m-d H:i:s',$date));
			
		//date_fin
		$date = dolibarr_mktime(
				0,
				0,
				0,
				 $post["book_contract_bill_date_finmonth"], 
				 $post["book_contract_bill_date_finday"], 
				 $post["book_contract_bill_date_finyear"]
				 );	
		*/
		if(!empty($post["book_contract_bill_year"]))
		{
			$mail_year = $post["book_contract_bill_year"];
		}
		else
		{
			$error = $langs->trans("ErrorFieldRequired","book_contract_bill_year");
		}	
		$mk_date_deb = dolibarr_mktime(0,0,0,1,1,$mail_year);
		$mk_date_fin = dolibarr_mktime(23,59,59,12,31,$mail_year);
		$date_deb = dolibarr_date('Y-m-d H:i:s',$mk_date_deb);
		$date_fin = dolibarr_date('Y-m-d H:i:s',$mk_date_fin);
		
		$this->dao->setDate_deb($date_deb);
		$this->dao->setDate_fin($date_fin);
			
		//qteOrder	
		$_book_contract_bill->setQtyOrder($post["qtyOrder"]);
			

		//qteSold	
		$_book_contract_bill->setQtySold($post["qtySold"]);
		
		//fk_contract
		if($post["contract"] == '')
		{
			$error = $langs->trans("ErrorFieldRequired","fk_contract");
		}
		$_book_contract_bill->setFk_contract($post["contract"]);
		
		
		//fk_commande
		$_book_contract_bill->setFk_commande($post["id_commande"]);

		if(!isset($error))
		{
			// Si tous les champs sont bien remplis on envoi la requ�te de cr�ation
			if ( ($_book_contract_bill->create())==-1)
		    {
		      	// Si erreur lors de la cr�ation
		      	$this->error = $langs->trans("book_contract_bill_error");
		      	return -1; 	
		    }
		    else
		    {	    
		    	// La cr�ation s'est bien d�roul�e
		    	return $_book_contract_bill->getId();
		    }
		}
		else
		{
			$this->error = $error;
			return -1;
		}	
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

			
			//Ref
			if($post["ref"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","Ref");
			}
			$_book_contract_bill->setRef($post["ref"]);					
			

			//book_contract_bill_date_deb
			$date = dolibarr_mktime(
					0,
					0,
					0,
					 $post["book_contract_bill_date_debmonth"], 
					 $post["book_contract_bill_date_debday"], 
					 $post["book_contract_bill_date_debyear"]);						

			
			if (! ($date > 0)) 
			{
					$error = $langs->trans("ErrorFieldRequired","book_contract_bill_date_deb");
			}		
			$this->dao->setDate_deb($date);
		
			//book_contract_bill_date_fin
			$date = dolibarr_mktime(
					0,
					0,
					0,
					 $post["book_contract_bill_date_finmonth"], 
					 $post["book_contract_bill_date_finday"], 
					 $post["book_contract_bill_date_finyear"]);						

			
			if (! ($date > 0)) 
			{
					$error = $langs->trans("ErrorFieldRequired","book_contract_bill_date_fin");
			}		
			$this->dao->setDate_fin($date);
		
			
			//book_contract_bill_qtyOrder
			if($post["book_contract_bill_qtyOrder"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","book_contract_bill_qtyOrder");
			}
				
			$this->dao->setQtyOrder($post["book_contract_bill_qtyOrder"]);
			
			//book_contract_bill_qtySold
			if($post["book_contract_bill_qtySold"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","book_contract_bill_qtySold");
			}
				
			$this->dao->setQtySold($post["book_contract_bill_qtySold"]);
			
			
			//fk_contract
			if($post["book_contract_bill_fk_contract"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_contract");
			}
			$this->dao->setFk_contract($post["book_contract_bill_fk_contract"]);
			
			
			//fk_commande
			if($post["book_contract_bill_fk_commande"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_commande");
			}
			$this->dao->setFk_commande($post["book_contract_bill_fk_commande"]);
		
			
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

		
		if( $this->dao->fetch($id) == 0){		
			$data[] = array(
				"label" => $langs->trans("book_contract_bill_date_deb"),
				"value" => formatValue($this->getType("date_deb"),$this->dao->getDate_deb())
			);
			
			$data[] = array(
				"label" => $langs->trans("book_contract_bill_date_fin"),
				"value" => formatValue($this->getType("date_fin"),$this->dao->getDate_fin())
			);
			
			$data[] = array(
				"label" => $langs->trans("book_contract_bill_qtyOrder"),
				"value" => formatValue($this->getType("_qtyOrder"),$this->dao->getQtyOrder())
			);
			
			$data[] = array(
				"label" => $langs->trans("book_contract_bill_qtySold"),
				"value" => formatValue($this->getType("_qtySold"),$this->dao->getQtySold())
			);		

			

		}
		
		return $data ;
	}
	

	/*
     *      \brief      Get attributes array from an entity
     *      \return     array(array("class_name","attribut_name","type")) if ok, empty array if ko
     */
	public function getAttributesAdd($post=''){
		
		global $langs ;
		
		$data = array() ;


		/*
		//Attribut de la classe courante
		if(isset($post['book_contract_bill_date_deb']))
		{
			$value = $post['book_contract_bill_date_deb'];
		}
		else
		{
			$year = date("Y") - 1;
			$value = $year."-01-01";
		}
		$data["book_contract_bill_date_deb"] = array(
				"entity" => "book_contract_bill",
				"attribute" => "date_deb",
				"type" => self::getType("date_deb"),
				"label" => $langs->trans(book_contract_bill."_".date_deb),
				"html_input" => getHtmlForm($this->db,self::getType("date_deb"),"book_contract_bill_date_deb",$value,false)
			);

			
		if(isset($post['book_contract_bill_date_fin']))
		{
			$value = $post['book_contract_bill_date_fin'];
		}
		else
		{
			$year = date("Y") - 1;
			$value = $year."-12-31";
		}				
		$data["book_contract_bill_date_fin"] = array(
			"entity" => "book_contract_bill",
			"attribute" => "date_fin",
			"type" => self::getType("date_fin"),
			"label" => $langs->trans(book_contract_bill."_".date_fin),
			"html_input" => getHtmlForm($this->db,self::getType("date_fin"),"book_contract_bill_date_fin",$value,false)
		);
		*/
		
		if(isset($post['book_contract_bill_year']))
		{
			$value = $post['book_contract_bill_year'];
		}
		else
		{
			$value = date("Y") - 1;
		}				
		$data["book_contract_bill_year"] = array(
			"entity" => "book_contract_bill",
			"attribute" => "date_fin",
			"type" => "integer",
			"label" => $langs->trans("book_contract_bill_year"),
			"html_input" => getHtmlForm($this->db,"integer","book_contract_bill_year",$value,false)
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
								
			$data["book_contract_bill_date_deb"] = array(
					"entity" => "book_contract_bill",
					"attribute" => "date_deb",
					"type" => self::getType("date_deb"),	
					"value" => $this->dao->getDate_deb(),
					"label" => $langs->trans(book_contract_bill."_".date_deb),
					"html_input" => getHtmlForm(self::getType("date_deb"),"book_contract_bill_date_deb",$this->dao->getDate_deb(),false)
				);
								
			$data["book_contract_bill_date_fin"] = array(
					"entity" => "book_contract_bill",
					"attribute" => "date_fin",
					"type" => self::getType("date_fin"),	
					"value" => $this->dao->getDate_fin(),
					"label" => $langs->trans(book_contract_bill."_".date_fin),
					"html_input" => getHtmlForm(self::getType("date_fin"),"book_contract_bill_date_fin",$this->dao->getDate_fin(),false)
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
		    $form->form_confirm(DOL_URL_ROOT.'/product/show_contact.php?id='.$_GET["id"],$langs->trans("delete",$langs->trans("book_contract_bill")),$langs->trans("confirm_delete",$langs->trans("book_contract_bill")),"confirm_delete");
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
			case "rowid" :
				return "integer" ;
				break ;
			case "ref" :
				return "string" ;
				break ;
			case "pdf" :
				return "file" ;
				break ;
			case "date_deb" :
				return "date" ;
				break ;
			case "date_fin" :
				return "date" ;
				break ;
			case "qtyorder" :
				return "integer" ;
				break ;
			case "qtysold" :
				return "integer" ;
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
		
		global $conf;
			
		
		switch($field){
		
				// foreign key
					
				case "pdf" :
					//Calcul le lien vers le pfd
					$sql = "SELECT p.ref as book_ref, b.ref as bill_ref";
					$sql.= " FROM ".MAIN_DB_PREFIX."product_book_contract_bill b";
					$sql.= " JOIN ".MAIN_DB_PREFIX."product_book_contract c ON c.rowid = b.fk_contract";
					$sql.= " JOIN ".MAIN_DB_PREFIX."product p ON p.rowid = c.fk_product_book";
					$sql.= " WHERE b.rowid = ".$ID;
					
					$result = $this->db->query($sql);
					$obj = $this->db->fetch_object($result);
					$file = $obj->book_ref."/".$obj->bill_ref.".pdf";
					
					return DOL_URL_ROOT . '/document.php?modulepart=book&amp;file=droitauteur/'.urlencode($file) ;
					
			
				case "fk_contract" :
					return "show_contact.php?id=".$ID ;
					break ;
		
				// foreign key
				case "fk_commande" :
					return ".php?id=".$ID ;
					break ;
				default :
					return "" ;
		}
	}
	
	
	
	/**
	 * Calcul la quantit� de droit � re-commander
	 *
	 * @param unknown_type $id_contract
	 */
	public function getQteStock($id_contract)
	{
		
		//Nombre de droits en stock
		$sql = "SELECT s.reel";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_book_contract c";
		$sql.= " LEFT OUTER JOIN ".MAIN_DB_PREFIX."product_stock s ON c.fk_product_droit = s.fk_product ";
		$sql.= " WHERE c.rowid = ".$id_contract;

		$result = $this->db->query($sql);
		$obj = $this->db->fetch_object($result);
		return $obj->reel;

	}
	
	
	/**
	 * Creation d'une facture
	 *
	 * @param unknown_type $contract
	 * @return unknown
	 */
	public function newCommande($contract,$qtyToOrder)
	{
		
		global $user;
		
		$dao_contract = new dao_book_contract($this->db);
		$dao_contract->fetch($contract);
		
		$dao_product = new Product($this->db);
		$dao_product->fetch($dao_contract->getFk_product_droit());
	

		
		//ORDER
		$commande = new CommandeFournisseur($this->db);
		$commande->socid = $dao_contract->getFk_soc();
		
		$sql = "SELECT pfp.rowid";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_fournisseur_price pfp";
		$sql.= " JOIN llx_product_fournisseur pf ON pf.rowid = pfp.fk_product_fournisseur";
		$sql.= " WHERE pf.fk_product = ".$dao_contract->getFk_product_droit();
		$sql.= " AND pf.fk_soc = ".$dao_contract->getFk_soc();
		$sql.= " AND pfp.price = '".$dao_product->price."'"; 

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
		

		if($commande->addline("",$dao_product->price,$qtyToOrder,$dao_product->tva_tx,$dao_product->id,$fk_fourn_product_price,'',0,'TTC',$dao_product->price_ttc) < 0)
		{	
			$this->error = $commande->error;
			return -1;
		}
		
		//Update order information / Validation
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
		$commande->fetch($commande->id);
		

		//Increasing (Incrementation) of the stock
		if($commande->DispatchProduct($user,$dao_product->id,$qtyToOrder,1) < 0)
		{	
			$this->error = $commande->error;
			return -1	;
		}	
		
		return $commande->id;
		
		
	}
	
	
	public function generate($id,$post)
	{
		
		global $langs;
		
		/*
		$mk_date_deb = dolibarr_mktime(0,0,0, $post["book_contract_bill_date_debmonth"],$post["book_contract_bill_date_debday"], $post["book_contract_bill_date_debyear"]);		 
		$date_deb = dolibarr_date('Y-m-d H:i:s',$mk_date_deb);
		
		$mk_date_fin = dolibarr_mktime(0,0,0,$_POST["book_contract_bill_date_finmonth"],$post["book_contract_bill_date_finday"], $post["book_contract_bill_date_finyear"]);	 
		$date_fin = dolibarr_date('Y-m-d H:i:s',$mk_date_fin);
		*/
		if(!empty($post["book_contract_bill_year"]))
		{
			$mail_year = $post["book_contract_bill_year"];
		}
		else
		{
			$mail_year = date("Y")-1;
		}
		$mk_date_deb = dolibarr_mktime(0,0,0,1,1,$mail_year);
		$mk_date_fin = dolibarr_mktime(23,59,59,12,31,$mail_year);
		$date_deb = dolibarr_date('Y-m-d H:i:s',$mk_date_deb);
		$date_fin = dolibarr_date('Y-m-d H:i:s',$mk_date_fin);

		//Contract ID retrieving
		$query = dao_book::select($this->db,""," t.rowid = ".$id);
		$obj = $this->db->fetch_object($query);
		$post['contract'] = $obj->fk_contract;		
		
		
		//Check if mail has not been done before
		
		$sql = "SELECT rowid";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_book_contract_bill";
		$sql.= " WHERE fk_contract = ".$post['contract'];
		$sql.= " AND date_deb = '".$date_deb."'";
		$sql.= " AND date_fin = '".$date_fin."'";
		
		$query = dao_book_contract_bill::selectQuery($this->db,$sql);
		if($this->db->num_rows($query) > 0)
		{
			$this->error = $langs->trans("MailAlreadyExist");
			return -1;
		}
		

		//Calculation of the quantity of rights in stocks
		$qtyStock = 0;
		$qtyStock = $this->getQteStock($post['contract']);
		$qtyToOrder = 0;
	
		//Possibly order if < 0
		if($qtyStock < 0)
		{
			//Calculation of the quantity to order
			$qtyToOrder = 0 -$qtyStock;
	
			//Order of new rights
			$id_commande = $this->newCommande($post['contract'],$qtyToOrder);
	
			if($id_commande == -1)
			{
				return -1;
			}
		}
	


		
		
		$post['id_commande'] = $id_commande;
		$post['qtyOrder'] = $qtyToOrder;
		
		//Calculation of the quantity of books sold
		$service_book = new service_book($this->db);
		$post['qtySold'] = $service_book->getQtySold($id,$date_deb,$date_fin);
		
		//Creation of the ref of the contract_bill
		$dao_contract = new dao_book_contract($this->db);
		$dao_contract->fetch($post['contract']);
		$dao_product = new Product($this->db);
		$dao_product->fetch($dao_contract->getFk_product_droit());
		//$post['ref'] = $dao_product->ref."_".$post["book_contract_bill_date_debyear"];
		$post['ref'] = $dao_product->ref."_".$mail_year;

		$contract_bill_id = $this->add($post);
		
		//Generate the pdf
		if($contract_bill_id != -1)
		{
			$pdf = new pdf_courrier_droit_editeur($this->db);
			if($pdf->Generate($id,$post['contract'],$contract_bill_id) < 0)
			{
				$this->error = $pdf->error;
				return -1;
			}
		}
		else
		{
			return -1;
		}	
		
	
	}
	
	public function generateAll($post)
	{
		
		global $conf;
		
		//Generating the missing PDFs
		/*$mk_date_deb = dolibarr_mktime(0,0,0, $post["book_contract_bill_date_debmonth"], $post["book_contract_bill_date_debday"], $post["book_contract_bill_date_debyear"]);						
		$date_deb = dolibarr_date('Y-m-d H:i:s',$mk_date_deb);
		
		$mk_date_fin = dolibarr_mktime(0,0,0, $post["book_contract_bill_date_finmonth"], $post["book_contract_bill_date_finday"], $post["book_contract_bill_date_finyear"]);						
		$date_fin = dolibarr_date('Y-m-d H:i:s',$mk_date_fin);*/	
		if(!empty($post["book_contract_bill_year"]))
		{
			$mail_year = $post["book_contract_bill_year"];
		}
		else
		{
			$mail_year = date("Y")-1;
		}
		$mk_date_deb = dolibarr_mktime(0,0,0,1,1,$mail_year);
		$mk_date_fin = dolibarr_mktime(23,59,59,12,31,$mail_year);
		$date_deb = dolibarr_date('Y-m-d H:i:s',$mk_date_deb);
		$date_fin = dolibarr_date('Y-m-d H:i:s',$mk_date_fin);
		
		//Selecting only the articles of which the mail on the selected dates is not already generated
		$sql = "SELECT p.rowid";
		$sql.= " FROM ".MAIN_DB_PREFIX."product_book p";
		$sql.= " LEFT OUTER JOIN ".MAIN_DB_PREFIX."product_book_contract c ON c.rowid = p.fk_contract";
		$sql.= " LEFT OUTER JOIN ".MAIN_DB_PREFIX."product_book_contract_bill b ON c.rowid = b.fk_contract";
		$sql.= " JOIN ".MAIN_DB_PREFIX."societe s ON s.rowid = c.fk_soc";
		$sql.= " WHERE p.fk_contract != 0";
		$sql.= " AND c.date_deb < '".$date_fin."'";
		$sql.= " AND c.date_fin > '".$date_deb."'";
		$sql.= " AND p.rowid NOT IN (";
		$sql.= " SELECT p.rowid";
		$sql.= " FROM llx_product_book p";
		$sql.= " LEFT OUTER JOIN ".MAIN_DB_PREFIX."product_book_contract c ON c.rowid = p.fk_contract";
		$sql.= " LEFT OUTER JOIN ".MAIN_DB_PREFIX."product_book_contract_bill b ON c.rowid = b.fk_contract";
		$sql.= " WHERE p.fk_contract != 0";
		$sql.= " AND b.date_deb = '".$date_deb."'";
		$sql.= " AND b.date_fin = '".$date_fin."')";
		$sql.= " ORDER BY s.nom";

		$query = dao_book::selectQuery($this->db,$sql);
		
		while($obj = $this->db->fetch_object($query))
		{
			if($this->generate($obj->rowid,$post) < 0)
			{
				
				$this->error .= " | book = ".$obj->rowid;
				return -1;	
			}
 		}
 		
 		
 		
 		//Retrieve the list of mails for the given period
		$sql = "SELECT pb.rowid as book_id, b.rowid as contract_bill_id, p.ref as book_ref, b.ref as contract_bill_ref";
		$sql.= " FROM ".MAIN_DB_PREFIX."product p";
		$sql.= " JOIN ".MAIN_DB_PREFIX."product_book pb ON p.rowid = pb.rowid";
		$sql.= " LEFT OUTER JOIN ".MAIN_DB_PREFIX."product_book_contract c ON c.rowid = pb.fk_contract";
		$sql.= " LEFT OUTER JOIN ".MAIN_DB_PREFIX."product_book_contract_bill b ON c.rowid = b.fk_contract";
		$sql.= " WHERE pb.fk_contract != 0";
		$sql.= " AND b.date_deb = '".$date_deb."'";
		$sql.= " AND b.date_fin = '".$date_fin."'";		
 		
		$query = dao_book::selectQuery($this->db,$sql);
		
		$files = array();
		while($obj = $this->db->fetch_object($query))
		{
			$files[] = $conf->book->dir_output.'/droitauteur/'.$obj->book_ref.'/'.$obj->contract_bill_ref.'.pdf';
 		}	

 		$this->db->free();

 		
 		//Concatenation of the pdf
			
		
		//Generate the file from the other files
		$pdf=new FPDI();
		foreach($files as $file){
			//Charging a PDF document from a file
			$pagecount = $pdf->setSourceFile($file); 
            for ($i = 1; $i <= $pagecount; $i++) { 
                 $tplidx = $pdf->ImportPage($i); 
                 $s = $pdf->getTemplatesize($tplidx); 
                 $pdf->AddPage($s['h'] > $s['w'] ? 'P' : 'L'); 
                 $pdf->useTemplate($tplidx); 
            } 
		}
		
		// Check if filepath is good
		if(!is_dir($conf->book->dir_output)) mkdir($conf->book->dir_output);
		if(!is_dir($conf->book->dir_output.'/droitauteur')) mkdir($conf->book->dir_output.'/droitauteur');
		
		// Saving the PDF concatenated 
		$pdf->Output( $conf->book->dir_output.'/droitauteur/droitauteur_'.dolibarr_date('d-m-Y',$mk_date_deb).'_'.dolibarr_date('d-m-Y',$mk_date_fin).'.pdf');

		
		return 0;
		
	}
	
	

}
//End of user code
?>