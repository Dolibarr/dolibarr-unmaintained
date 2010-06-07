<?php

/* Copyright (C) 2010      Auguria SARL         <info@auguria.org>
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
        \file       htdocs/book/class/business/service_book_contract_societe.class.php
        \ingroup    book
        \brief      Service initialize a list of societies to be the editor of the book for the contract
		\version    $Id: service_book_contract_societe.class.php,v 1.2 2010/06/07 14:16:31 pit Exp $
		\author		Pierre Morin
*/

/**
        \class      service_book_contract_societe
        \brief      Service managing book editor choice
		\remarks	
*/
class service_book_contract_societe
{
	private $dao;
	private $db;	
	private $error;
	
	
	public function service_book_contract_societe($db)
	{	
		$this->db = $db;
		$this->dao = new dao_book_contract_societe($db);
	}
	
	
	public function getError() { return $this->error; }
	
	public function getDao() { return $this->dao; }
	
	
	
	/*
     *      \brief      	return an array with entries from the result of query in parameter
	 * 		\param $result	result of a query
	 *		\param $toDisplay array("fieldName" => data). fieldname, the fields to be displayed
     *      \return     	array(ID => array(property => array(info => value))
     */
	public function selectToArray($result,$toDisplay ='',$keys=array()){

		$liste = array() ;

		// If the query has returned something
		if($result){

			// we browse the result set using the $item var
			while($item = $this->db->fetch_object($result)){

				//make the array
				foreach($item as $entityfieldName => $value){

					// get the name of the field without the entity
					$fieldName = strtolower(str_replace("book_contract_societe_","",$entityfieldName)) ;

					// display only recorded fields
					if( @in_array($entityfieldName,$toDisplay) || ($toDisplay=='') ){

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
				return "integer";
				break ;
			case "statut" :
				return "integer";
				break ;
			case "parent" :
				return "integer";
				break ;
			case "tms" :
				return "datetime";
				break ;
			case "datetimec" :
				return "datetime";
				break ;
			case "datetimea" :
				return "datetime";
				break ;
			case "nom" :
				return "string";
				break ;
			case "entity" :
				return "integer";
				break ;
			case "code_client" :
				return "string";
				break ;
			case "code_fournisseur" :
				return "string";
				break ;
			case "code_compta" :
				return "string";
				break ;
			case "code_compta_fournisseur" :
				return "string";
				break ;
			case "address" :
				return "string";
				break ;
			case "cp" :
				return "string";
				break ;
			case "ville" :
				return "string";
				break ;
			case "fk_departement" :
				return "integer";
				break ;
			case "fk_pays" :
				return "integer";
				break ;
			case "tel" :
				return "string";
				break ;
			case "fax" :
				return "string";
				break ;
			case "url" :
				return "string";
				break ;
			case "email" :
				return "string";
				break ;
			case "fk_secteur" :
				return "integer";
				break ;
			case "fk_effectif" :
				return "integer";
				break ;
			case "fk_typent" :
				return "integer";
				break ;
			case "fk_forme_juridique" :
				return "integer";
				break ;
			case "siren" :
				return "string";
				break ;
			case "siret" :
				return "string";
				break ;
			case "ape" :
				return "string";
				break ;
			case "idprof4" :
				return "string";
				break ;
			case "tva_intra" :
				return "string";
				break ;
			case "capital" :
				return "double";
				break ;
			case "description" :
				return "text";
				break ;
			case "fk_stcomm" :
				return "integer";
				break ;
			case "note" :
				return "text";
				break ;
			case "services" :
				return "integer";
				break ;
			case "prefix_comm" :
				return "string";
				break ;
			case "client" :
				return "integer";
				break ;
			case "fournisseur" :
				return "integer";
				break ;
			case "supplier_account" :
				return "string";
				break ;
			case "fk_prospectlevel" :
				return "string";
				break ;
			case "customer_bad" :
				return "integer";
				break ;
			case "customer_rate" :
				return "double";
				break ;
			case "supplier_rate" :
				return "double";
				break ;
			case "fk_user_creat" :
				return "integer";
				break ;
			case "fk_user_modif" :
				return "integer";
				break ;
			case "remise_client" :
				return "double";
				break ;
			case "mode_reglement" :
				return "integer";
				break ;
			case "cond_reglement" :
				return "integer";
				break ;
			case "tva_assuj" :
				return "integer";
				break ;
			case "localtax1_assuj" :
				return "integer";
				break ;
			case "localtax2_assuj" :
				return "integer";
				break ;
			case "gencod" :
				return "string";
				break ;
			case "price_level" :
				return "integer";
				break ;
			case "default_lang" :
				return "string";
				break ;
			case "import_key" :
				return "string";
				break ;
			default :
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
				default :
					return "" ;
		}
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

}
?>
