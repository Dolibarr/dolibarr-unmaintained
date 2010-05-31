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
        \file       htdocs/product/book.class.php
        \ingroup    book
        \brief      *complete here*
		\version    $Id: service_book.class.php,v 1.1 2010/05/31 15:28:23 pit Exp $
		\author		Patrick Raguin
*/

/**
        \class      bookService
        \brief      Service managing book
		\remarks	
*/
class service_book extends service_product
{
	private $dao ;
	private $error;
	
	
	public function service_book($db)
	{	
		$this->service_product($db) ;
		$this->dao = new dao_book($db) ;
	}
	
	
	public function getError() {return $this->error;}
	
	/*
     *      \brief      return an array with entries of the entity in database. This must be a select query and the rowid field must be selected as well.
     *      \return      array(ID => array(property => array(info => value))
     */
	public function getFromQuery($query,$toDisplay='',$where='',$order=''){
		
		global $langs ;
		
		// Check the query
		if (stristr($query,'SELECT')) dolibarr_syslog("service_book::getFromQuery. Query must be a SELECT statement : sql=".$query, LOG_DEBUG);
		if (stristr($query,'rowid')) dolibarr_syslog("service_book::getFromQuery. Query must select the rowid :  sql=".$query, LOG_DEBUG);
		
		// execute the query
		$all = dao_book::selectQuery($this->db,$query,$where,$order) ;

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
					$fieldName = strtolower(str_replace("book_","",$entityfieldName)) ;

					// display only recorded fields
					if( @in_array($entityfieldName,$toDisplay) ||($toDisplay=='') ){

						// generate or not the link
						$link = $this->getLink($entityfieldName,$item->rowid) ;
						
						
						
						$liste[$item->rowid][$fieldName] = array(
							"attribute" => $fieldName,
							"type" => $this->getType($fieldName),
							"value" => $value,
							"link" => $link
						) ;
						
						if(($value == '') && ($liste[$item->rowid][$fieldName]['type'] == 'integer'))$liste[$item->rowid][$fieldName]['value'] = 0; 


					}
	
				} // end foreach
				
				//Factory Price
				$fieldName = 'factoryPrice_ttc';
				$factoryPrice = $this->getFactoryPrice($item->rowid);
				$liste[$item->rowid][$fieldName] = array(
					"attribute" => $fieldName,
					"type" => '',
					"value" => $factoryPrice['TTC'],
					"link" => ''
				) ;

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
		
		$listFk = parent::getAllFkDisplayed($id);
	
			
		return $listFk ;
	}
	
	/*
     *      \brief      return an array with entries of the entity in database
	 *		\param		$where	condition of the WHERE clause (ie. "t.label = 'bar'")
	 *		\param		$order	condition of the ORDER BY clause (ie. "t.rowid DESC")
     *      \return     array(array(properties...,URL))
     */
	public function getAllListDisplayed($where='',$order=''){
		
		global $conf;
		
		
		// Recovery of fields to display
		
		$attributes =array(); // attributes to use in the SELECT query
		$labels = array();	  // name of the field to be displayed from the query.
		$join = array();	  // tables to join
		
		$attributes[] = "pdt.ref as product_ref";
		$attributes[] = "pdt.label as product_label";
		//$attributes[] = "locker.code as locker_code";
		$attributes[] = "sum(fdet.qty) as product_vente";
		$attributes[] = "stock.reel as product_stock";
		$attributes[] = "book.nbpages as book_pages";

		$labels[] = "product_ref";
		$labels[] = "product_label";
		//$labels[] = "locker_code";
		$labels[] = "product_vente";
		$labels[] = "product_stock";
		$labels[] = "book_pages";

		$join[] = " JOIN ".MAIN_DB_PREFIX."product_book book ON pdt.rowid = book.rowid";
		$join[] = " LEFT OUTER JOIN ".MAIN_DB_PREFIX."product_stock stock ON stock.fk_product = pdt.rowid";
		//$join[] = " LEFT OUTER JOIN ".MAIN_DB_PREFIX."locker locker ON locker.rowid = stock.fk_locker";
		$join[] = " LEFT OUTER JOIN ".MAIN_DB_PREFIX."facturedet fdet ON fdet.fk_product = pdt.rowid";	
		
		//Current class
		
		$query = "SELECT book.rowid, ".implode(", ",$attributes);
		$query.= " FROM ".MAIN_DB_PREFIX."product pdt ".implode("",$join);
		$query.= " GROUP BY pdt.ref, stock.reel";
		//$query.= " GROUP BY pdt.ref, locker.code, stock.reel";

		return $this->getFromQuery($query,$labels,$where,$order) ;
	}
	
	/*
     *      \brief     return an array with entries of the fields to be displayed in the list
     *      \return    array(property => translation)
     */
	public function getListDisplayedFields(){
		
		global $langs ;
		
		//get 1st part of the list from the parent object
		
		//product

		$fields["ref"] = array(
			"entity" => "pdt",
			"attribute" => "ref",
			"type" => parent::getType("ref"),
			"label" => $langs->trans("Ref")
		) ;

		$fields["label"] = array(
			"entity" => "pdt",
			"attribute" => "label",
			"type" => parent::getType("label"),
			"label" => $langs->trans("Label")
		) ;

		/*$fields["locker"] = array(
			"entity" => "locker",
			"attribute" => "label",
			"type" => "string",
			"label" => $langs->trans("Locker")
		) ;*/
		
		$fields["vente"] = array(
			"entity" => "cdet",
			"attribute" => "qty",
			"type" => "string",
			"label" => $langs->trans("vente")
		) ;		
		$fields["stock"] = array(
			"entity" => "stock",
			"attribute" => "reel",
			"type" => "string",
			"label" => $langs->trans("stock")
		) ;		
		$fields["pages"] = array(
			"entity" => "book",
			"attribute" => "nbpages",
			"type" => "string",
			"label" => $langs->trans("pages")
		) ;	
		$fields["factoryPrice_ttc"] = array(
			"entity" => "book",
			"attribute" => "factoryPrice_ttc",
			"type" => "",
			"label" => $langs->trans("FactoryPrice","TTC")
		) ;	
		
		
		//The second using the current object properties

		return $fields ;
	}
	
	/*
     *      \brief     return an array with entries of the fields to be displayed in the combobox when invoked as foreign Key
     *      \return    array(property => translation)
     */
	public function getfkDisplayedFields(){
		
		global $langs ;
		
		$fields = parent::getfkDisplayedFields($where,$order)  ;
		

		return $fields ;
		
		
	}

	/*
     *      \brief      try to add the element
     *      \return     $error		'' if ok, string of the error if error.
     */
	public function add($post){
	
		global $langs ;
		
		
		$id = parent::add($post);
		if($id < 0)
		{
			$this->error = parent::getError();
			return -1;
		}
		
		//rowid
		$this->dao->setId($id);		
		
		//nbpages
		$this->dao->setNbpages($post["book_nbpages"]);
			
		//ISBN
		if($post['book_isbn'] != '')
		{
			$book_isbn_array = explode("-",$post['book_isbn']);
			if($book_isbn_array[3] == '')
			{
				$isbn_key = calculate_isbn_key($book_isbn_array[0].$book_isbn_array[1].$book_isbn_array[2]);	
			}
			else
			{
				$isbn_key = $book_isbn_array[3];
			}
	
			$isbn = $book_isbn_array[0].'-'.$book_isbn_array[1].'-'.$book_isbn_array[2].'-'.$isbn_key;
			$this->dao->setISBN($isbn);
	
			//EAN13
			$ean13 = $post['book_ean13'];
			if($ean13 == '')
			{
				$ean13 = convert_code_barre('isbn','ean13',$isbn);
	
			}
			$this->dao->setEAN13($ean13);
			
			// ISBN13
			$isbn13 = $post['book_isbn13'];
			if($isbn13 == '')
			{
				$isbn13 = convert_code_barre('isbn','isbn13',$isbn);
			}
			$this->dao->setISBN13($isbn13);				
			
			
			
		}
	

		//format
		$this->dao->setFormat($post["book_format"]);

		if(!isset($error))
		{
			// If all the fields are filled, sending the creation query
			if ( ($this->dao->create())==-1)
		    {
		      	// If an error occured during creation
		      	$this->error = $langs->trans("Impossible d'ajouter le livre");
		      	return -1; 	
		    }
		    else
		    {	    
		    	// If everything is OK
		    	return $this->dao->getId();
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
		

		$update = parent::update($post);
		if($update != 0)
		{
			$this->error = parent::getError();	
			return -1;
		}

		//book_nbpages		
		$this->dao->setNbpages($post["book_nbpages"]);

		//book_ISBN
		if($post['book_isbn'] != '')
		{
			$book_isbn_array = explode("-",$post['book_isbn']);
			if($book_isbn_array[3] == '')
			{
				$isbn_key = calculate_isbn_key($book_isbn_array[0].$book_isbn_array[1].$book_isbn_array[2]);	
			}
			else
			{
				$isbn_key = $book_isbn_array[3];
			}
	
			$isbn = $book_isbn_array[0].'-'.$book_isbn_array[1].'-'.$book_isbn_array[2].'-'.$isbn_key;
			$this->dao->setISBN($isbn);
	
			//EAN13
			$ean13 = $post['book_ean13'];
			if($ean13 == '')
			{
				$ean13 = convert_code_barre('isbn','ean13',$isbn);
	
			}
			$this->dao->setEAN13($ean13);
			
			// ISBN13
			$isbn13 = $post['book_isbn13'];
			if($isbn13 == '')
			{
				$isbn13 = convert_code_barre('isbn','isbn13',$isbn);
			}
			$this->dao->setISBN13($isbn13);			
		
		}
	
		//book_format
		$this->dao->setFormat($post["book_format"]);
	
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
			echo "2";
			return -1;
		}
		return 0;	
	}

	/*
     *      \brief      Get data array from an occurence of the element
     *      \return     array(array("libelle","value")) if ok, empty array if ko
     */
	public function getAttributesShow($id){
		
		global $langs ;
		
		$data = array() ;

		$data = parent::getAttributesShow($id);
		if($data < 0) return -1;
		
		if( $this->dao->fetch($id) == 0){	

			$data["book_nbpages"] = array(
				"label" => $langs->trans("Nbpages"),
				"value" => $this->dao->getNbpages()
			);
			
			$data["book_isbn"] = array(
				"label" => "ISBN",
				"value" => $this->dao->getISBN()
			);
			
			$data["book_isbn13"] = array(
				"label" => "ISBN13",
				"value" => $this->dao->getISBN13()
			);
			
			$data["book_ean"] = array(
				"label" => "EAN",
				"value" => $this->dao->getEAN()
			);

			$data["book_ean13"] = array(
				"label" => "EAN13",
				"value" => $this->dao->getEAN13()
			);
			
			$data["book_format"] = array(
				"label" => $langs->trans("Format"),
				"value" => getLabelFormatFromKey($this->db,$this->dao->getFormat(),array('label'))
			);
			
			$data["book_pxrevient"] = array(
				"label" => $langs->trans("book_pxrevient"),
				"value" => $this->getFactoryPrice($id)
			);
			
			$data["book_contract"] = array(
				"label" => $langs->trans("contract"),
				"value" => $this->dao->getContract()
			);
			

			$data["product_sold"] = array(
				"label" => $langs->trans("Product_sold"),
				"value" => $this->getQtySold($id)
			);

			return $data ;
		}
		
		return -1;
	}
	

	/*
     *      \brief      Get attributs array from an entity
     *      \return     array(array("class_name","attribut_name","type")) if ok, empty array if ko
     */
	public function getAttributesAdd($post){
		
		global $langs ;
		
		$data = array() ;

		$data = parent::getAttributesAdd($post);

		
		//Attribut of the current class
		$data["book_nbpages"] = array(
				"entity" => "book",
				"attribute" => "nbpages",
				"type" => self::getType("nbpages"),
				"label" => $langs->trans("Nbpages"),
				"input" =>getHtmlForm($this->db,self::getType("nbpages"),"book_nbpages",$post['book_nbpages'])
			);
			
		
			
		$data["book_isbn"] = array(
				"entity" => "book",
				"attribute" => "isbn",
				"type" => self::getType("ISBN"),
				"label" => "ISBN",
				"input" =>  getHtmlForm($this->db,self::getType("isbn"),"book_isbn",$post['book_isbn'])   

	
			);
		$data["book_ean"] = array(
				"entity" => "book",
				"attribute" => "ean",
				"type" => self::getType("EAN"),
				"label" => "EAN",
				"input" => "<i>".$langs->trans("Calculated")."</i>"
			);
			
		$data["book_isbn13"] = array(
				"entity" => "book",
				"attribute" => "isbn13",
				"type" => self::getType("ISBN13"),
				"label" => "ISBN13",
				"input" => "<i>".$langs->trans("Calculated")."</i>"
			);
		$data["book_ean13"] = array(
				"entity" => "book",
				"attribute" => "ean13",
				"type" => self::getType("EAN13"),
				"label" => "EAN13",
				"input" => "<i>".$langs->trans("Calculated")."</i>"
			);
			
		$data["book_format"] = array(
				"entity" => "book",
				"attribute" => "format",
				"type" => self::getType("format"),
				"label" => $langs->trans("Format"),
				"input" =>  getHtmlForm($this->db,self::getType("format"),"book_format",$post['book_format'])   
			);
				

		return $data ;
	}
	

	/*
     *      \brief      Get attributs and data array from an entity
     *      \return     array(array("libelle","value")) if ok, empty array if ko
     */
	public function getAttributesEdit($id,$post){
		
		global $langs ;
		
		$data = array() ;
		
		$this->dao->fetch($id);
		
		$data = parent::getAttributesEdit($id,$post);
		
		
		//Attribut de la classe courante
								
		$value = (($post['book_nbpages']=='')?$this->dao->getNbpages():$post['book_nbpages']);	
		$data["book_nbpages"] = array(
				"entity" => "book",
				"attribute" => "nbpages",
				"type" => self::getType("nbpages"),	
				"value" => $this->dao->getNbpages(),
				"label" => $langs->trans("Nbpages"),
				"input" => getHtmlForm($this->db,self::getType("nbpages"),"book_nbpages",$value,0,"",7)
			);

		$value = (($post['book_isbn']=='')?$this->dao->getISBN():$post['book_isbn']);
		$data["book_isbn"] = array(
				"entity" => "book",
				"attribute" => "isbn",
				"type" => self::getType("ISBN"),	
				"value" => $this->dao->getISBN(),
				"label" => "ISBN",
				"input" => getHtmlForm($this->db,self::getType("isbn"),"book_isbn",$value)
			);
			
		$value = (($post['book_isbn13']=='')?$this->dao->getISBN13():$post['book_isbn13']);
		$data["book_isbn13"] = array(
				"entity" => "book",
				"attribute" => "isbn13",
				"type" => self::getType("ISBN"),	
				"value" => $this->dao->getISBN(),
				"label" => "ISBN13",
				"input" => getHtmlForm($this->db,self::getType("isbn13"),"book_isbn13",$value)
			);								

			
			
		$value = (($post['book_ean13']=='')?$this->dao->getEAN13():$post['book_ean13']);	
		$data["book_ean13"] = array(
				"entity" => "book",
				"attribute" => "ean13",
				"type" => self::getType("EAN13"),	
				"value" => $this->dao->getEAN13(),
				"label" => "EAN13",
				"input" => getHtmlForm($this->db,self::getType("ean13"),"book_ean13",$value)
			);
							
		$value = (($post['book_format']=='')?$this->dao->getFormat():$post['book_format']);
		$data["book_format"] = array(
				"entity" => "book",
				"attribute" => "format",
				"type" => self::getType("format"),	
				"value" => $this->dao->getFormat(),
				"label" => $langs->trans("book_Format"),
				"input" => getHtmlForm($this->db,self::getType("format"),"book_format",$value)
			);
	
		$value = (($post['book_pxrevient']=='')?$this->dao->ref:$post['book_pxrevient']);
		$data["book_pxrevient"] = array(
				"entity" => "book",
				"attribute" => "book_pxrevient",
				"type" => self::getType("pxrevient"),	
				"value" => $this->getFactoryPrice($id),
				"label" => $langs->trans("FactoryPrice"),
				"input" => getHtmlForm($this->db,self::getType("pxrevient"),"book_pxrevient",$value)
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
		
		// On utlise la fonction de dolibar pour générer un formulaire de confirmation
		// et on place ce texte générer dans une variable.
		ob_start() ;
		    $form = new Form($db);
		    $form->form_confirm(DOL_URL_ROOT.'/product/show_book.php?id='.$_GET["id"],$langs->trans("delete",$langs->trans("book")),$langs->trans("confirm_delete",$langs->trans("book")),"confirm_delete");
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
		if(parent::delete($id) < 0)
		{
			$this->error = parent::error;
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
		switch($field){
			case "rowid" :
				return "integer" ;
				break ;
			case "product_ref" :
				return "string" ;
				break ;
			case "product_label" :
				return "string" ;
				break ;
			case "locker_code" :
				return "string" ;
				break ;
			case "product_vente" :
				return "integer" ;
				break ;
			case "product_stock" :
				return "integer" ;
				break ;
			case "nbpages" :
				return "integer" ;
				break ;
			case "isbn" :
				return "string" ;
				break ;
			case "isbn13" :
				return "string" ;
				break ;
			case "format" :
				return "format" ;
				break ;
			case "ean" :
				return "string" ;
				break ;
			case "ean13" :
				return "string" ;
				break ;
			case "format" :
				return "string" ;
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
				case "product_ref":
					return "show_book.php?id=".$ID ;
					break;
				default :
					return "" ;
		}
	}
	
	
	public function getORDERBY($entityname,$fields){
		
		$sort = '' ;
		if( isset($_GET['sortfield']) && ($_GET['entity']==$entityname) )
		{
			//Ascending or Descending sort
			($_GET['sortorder']=='desc')? $order = 'DESC' : $order = 'ASC' ;
			$att = $fields[$entityname][$_GET['sortfield']] ;
			$sort = $att["entity"].'.'.$att["attribute"].' '.$order ;
		}
		return $sort ;
	}
	
	public function getWHERE(&$params,$entityname,$fields){
		global $db ;
		
		$where = '' ;
		if( $_GET['search']==$entityname )
		{
			// regenerate the get parameters for the search to preserve filter while sorting
			$params[$entityname].='&amp;search='.$entityname ;
			
			$and = '' ;
			
			foreach ($fields[$entityname] as $attname=> $att) {
				if( $_GET[$entityname.$attname] != ''){
					// write the get part of URL
					$params[$entityname].='&amp;'.$entityname.$attname.'='.$_GET[$entityname.$attname] ;
					
					//compare booleans
					if($att["type"]=='boolean'){
						$bool = (in_array(strtolower($_GET[$entityname.$attname]),array(strtolower($langs->trans("yes")),'1')))? '1':'0';
						$where.=' '.$and.$att["entity"].'.'.$attname.' LIKE \'%'.$bool.'%\'' ;
						
					// compare dates
					}else if(($att["type"]=='datetime')||($att["type"]=='date')){
						/*TODO : comparaison de dates*/
					// general text case
					}else{
						$where.=' '.$and.$att["entity"].'.'.$att["attribute"].' LIKE \'%'.$db->escape($_GET[$entityname.$attname]).'%\'' ;
					}
					$and = 'AND ' ;	
				} 
			}
		} 
		
		return $where ;
	}
	
	/*
     *      \brief      	isLivre
     *		\param $id		id product
     *      \return     	true/false
     */	
	public function isLivre($id=0)
	{
		return $this->dao->exist($id);

	}
	
	/*
     *      \brief      	get Factory Price
     *		\param $id		id product
     *      \return     	return factory price
     */		
	public function getFactoryPrice($id)
	{
		$service = new service_product_composition($this->db);
		return $service->getFactoryPrice($id);
	}	
	
	/*
     *      \brief      	get buying cost
     *		\param $id		id product
     *      \return     	return  buying cost
     */		
	public function getBuyingCost($id)
	{
		$service = new service_product_composition($this->db);
		return $service->getBuyingCost($id);
	}	
	
	/**
	 * Calcule le nombre de livre vendue sur la p�riode
	 *
	 * @param $date_deb
	 * @param $date_fin
	 * @param $product_id
	 * @return nombre de livre command�
	 */	
	public function getQtySold($product_id,$date_deb='',$date_fin='')
	{
		
		$sql = "SELECT SUM(fd.qty) as qteC";
		$sql.= " FROM ".MAIN_DB_PREFIX."facturedet fd";
		$sql.= " JOIN ".MAIN_DB_PREFIX."facture f ON f.rowid = fd.fk_facture";
		if($date_deb != '' && $date_fin != '')
		{
			$sql.= " WHERE f.datef BETWEEN '".$date_deb."' AND '".$date_fin."'";	
		}
		else
		{
			//Si pas de date, on prend les dates du contrat
			$sql.= " JOIN ".MAIN_DB_PREFIX."product_book b ON fd.fk_product = b.rowid";
			$sql.= " JOIN ".MAIN_DB_PREFIX."product_book_contract bc ON b.fk_contract = bc.rowid ";
			$sql.= " WHERE f.datef BETWEEN bc.date_deb AND bc.date_fin";
		}
		
		$sql.= " GROUP BY fd.fk_product";
		$sql.= " HAVING fd.fk_product = ".$product_id;

		$result = $this->db->query($sql);
		$obj = $this->db->fetch_object($result);
		return $obj->qteC;
		
		
	
	}
	
	


	
	

}
//End of user code
?>
