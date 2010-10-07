<?php
//Start of user code

	
	
	
	
/* Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 * Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
 * Copyright (C) 2008   < > 
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
        \file       htdocs/product/stock/service_locker.class.php
        \ingroup    Locker
        \brief      *complete here*
		\version    2.4
		\author		 
*/

/**
        \class      lockerService
        \brief      Service managing locker
		\remarks	
*/

require_once(DOL_DOCUMENT_ROOT."/locker/class/business/service_locker.class.php");

class service_locker
{
	private $dao ;
	protected $db ;	
	
	public function getDAO(){
		return $this->dao ;
	}
	public function service_locker($db)
	{	
		$this->db = $db ;
		$this->dao = new dao_locker($db) ;
	}
	
	/*
     *      \brief      return an array with entries of the entity in database. This must be a select query and the rowid field must be selected as well.
     *      \return      array(ID => array(property => array(info => value))
     */
	public function getFromQuery($query,$toDisplay='',$where='',$order=''){
		
		global $langs ;
		
		// Check the query
		if (stristr($query,'SELECT')) dolibarr_syslog("service_locker::getFromQuery. Query must be a SELECT statement : sql=".$query, LOG_DEBUG);
		if (stristr($query,'rowid')) dolibarr_syslog("service_locker::getFromQuery. Query must select the rowid :  sql=".$query, LOG_DEBUG);
		
		// execute the query
		$all = dao_locker::selectQuery($this->db,$query,$where,$order) ;
		
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
					$fieldName = strtolower(str_replace("locker_","",$entityfieldName,$nbrReplace)) ;

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
	
	/*
     *      \brief      return an array with entries of the entity in database
	 *		\param		$where	condition of the WHERE clause (ie. "t.label = 'bar'")
	 *		\param		$order	condition of the ORDER BY clause (ie. "t.rowid DESC")
     *      \return     array(array(properties...,URL))
     */
	public function getAllFkDisplayed($id='',$where='',$order=''){
		
		global $langs, $db ;
	
		if($id != '') $this->dao->fetch($id);
		
		$listFk = array() ;
		
	//entrepot
		$fkService = new Entrepot($this->db) ;
		
		//fields to be selected
		 $sql = "SELECT e.rowid, e.label as entrepot_label";
	    $sql.= " FROM ".MAIN_DB_PREFIX."entrepot e";
		if($id != '') $sql .= " WHERE e.rowid = ".$id;
	    
		//select fk values according to conditions
		$all = $db->query($sql) ;
		
		//R?cup?ration valeur de la cl? ?trang?re dans le post
		if(isset($_POST['locker_fk_entrepot'])) 
		{
			$value = $_POST['locker_fk_entrepot'];
		}
		else
		{
			$value = $this->dao->getFk_entrepot();
		}
		
		// add data about the foreign keys in the array
		$listFk[] = array(
					"entity" => "locker",
					"attribute" => "fk_entrepot",
					"value" => $value,
					"label" => $langs->trans("locker_fk_entrepot"),
					"null" => 1,
					"values" => self::selectToArray($all,array("entrepot_label"))
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
		
		// r?cup?ration de la liste des champs ? afficher
		
		$attributes =array(); // attributes to use in the SELECT query
		$labels = array();	  // name of the field to be displayed from the query.
		$join = array();	  // tables to join
		
		
		
		//Classe courante
		$attributes[] = "locker.code as locker_code";
		$labels[] = "locker_code";
		$attributes[] = "locker.libelle as locker_libelle";
		$labels[] = "locker_libelle";
		
		$query = "SELECT locker.rowid, ".implode(", ",$attributes)." ";
		$query.= "FROM ".MAIN_DB_PREFIX."locker locker ".implode("",$join);
		
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
		$fields["locker_code"] = array(
			"entity" => "locker",
			"attribute" => "code",
			"type" => $this->getType("code"),
			"label" => $langs->trans("locker_code")
		) ;
		$fields["locker_libelle"] = array(
			"entity" => "locker",
			"attribute" => "libelle",
			"type" => $this->getType("libelle"),
			"label" => $langs->trans("locker_libelle")
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
		
		$fields["locker_code"] = array(
			"entity" => "locker",
			"attribute" => "code",
			"type" => $this->getType("code"),
			"label" => $langs->trans("locker_code")
		) ;

		return $fields ;
		
		
	}

	/*
     *      \brief      try to add the element
     *      \return     $error		'' if ok, string of the error if error.
     */
	public function add($post){
	
		global $langs ;
		
		$_locker = $this->dao;
		
		
		//rowid
		
		
		//code
			$_locker->setCode($post["locker_code"]);
			
		//libelle
			$_locker->setLibelle($post["locker_libelle"]);
			
		
			//fk_entrepot
			if($post["locker_fk_entrepot"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_entrepot");
			}
			$_locker->setFk_entrepot($post["locker_fk_entrepot"]);
		
		if(!isset($error))
		{
			// Si tous les champs sont bien remplis on envoi la requ?te de cr?ation
		  if ( ($_locker->create())==-1)
		    {
		      	// Si erreur lors de la cr?ation
		      	$_GET['error'] = $langs->trans("Locker_error");
		      	return -1;
		      	
		    }
		    else
		    {
		    
		    	// La cr?ation s'est bien d?roul?e
		    	return $_locker->getId();

		    }
		}
		else
		{
			$_GET['error'] = $error;
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


				//locker_code
					
				$this->dao->setCode($post["locker_code"]);
			
				//locker_libelle
					
				$this->dao->setLibelle($post["locker_libelle"]);
			
			
			//fk_entrepot
			if($post["locker_fk_entrepot"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","fk_entrepot");
			}
			$this->dao->setFk_entrepot($post["locker_fk_entrepot"]);
		
			
			if(!isset($error))
			{
			  if ( $this->dao->update()!=0)
			    {
			      	
			      	$error = $langs->trans("Locker_error");
			      	return $error ;
      
			    }

			}
		}
		else
		{
	      	Header("Location: fiche_locker.php?id=".$this->dao->getId());
		}
		$_GET['id'] = $post['id'];
		return $error ;	
		
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
				"label" => $langs->trans("locker_code"),
				"value" => formatValue($this->getType("code"),$this->dao->getCode())
			);
			
			$data[] = array(
				"label" => $langs->trans("locker_libelle"),
				"value" => formatValue($this->getType("libelle"),$this->dao->getLibelle())
			);
			
			

		}
		
		return $data ;
	}
	

	/*
     *      \brief      Get attributes array from an entity
     *      \return     array(array("class_name","attribut_name","type")) if ok, empty array if ko
     */
	public function getAttributesAdd(){
		
		global $langs ;
		
		$data = array() ;


		
		//Attribut de la classe courante
			$data["locker_code"] = array(
					"entity" => "locker",
					"attribute" => "code",
					"type" => self::getType("code"),
					"label" => $langs->trans(locker."_".code),
					"html_input" => getHtmlForm(self::getType("code"),"locker_code",$this->dao->getCode(),true)
				);
			$data["locker_libelle"] = array(
					"entity" => "locker",
					"attribute" => "libelle",
					"type" => self::getType("libelle"),
					"label" => $langs->trans(locker."_".libelle),
					"html_input" => getHtmlForm(self::getType("libelle"),"locker_libelle",$this->dao->getLibelle(),true)
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
								
			$data["locker_code"] = array(
					"entity" => "locker",
					"attribute" => "code",
					"type" => self::getType("code"),	
					"value" => $this->dao->getCode(),
					"label" => $langs->trans(locker."_".code),
					"html_input" => getHtmlForm(self::getType("code"),"locker_code",$this->dao->getCode(),true)
				);
								
			$data["locker_libelle"] = array(
					"entity" => "locker",
					"attribute" => "libelle",
					"type" => self::getType("libelle"),	
					"value" => $this->dao->getLibelle(),
					"label" => $langs->trans(locker."_".libelle),
					"html_input" => getHtmlForm(self::getType("libelle"),"locker_libelle",$this->dao->getLibelle(),true)
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
		
		// On utlise la fonction de dolibar pour g?n?rer un formulaire de confirmation
		// et on place ce texte g?n?rer dans une variable.
		ob_start() ;
		    $form = new Form($db);
		    $form->form_confirm(DOL_URL_ROOT.'/product/stock/fiche_locker.php?id='.$_GET["id"],$langs->trans("delete",$langs->trans("locker")),$langs->trans("confirm_delete",$langs->trans("locker")),"confirm_delete");
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
		return $this->dao->delete($id) ;
		
		
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
			case "code" :
				return "string" ;
				break ;
			case "libelle" :
				return "text" ;
				break ;
			case "entrepot_label" :	
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
				case "entrepot_label" :
					return "fiche.php?id=".$ID ;
					break ;
				default :
					return "fiche_locker.php?id=".$ID ;
		}
	}
	

}
//End of user code
?>
