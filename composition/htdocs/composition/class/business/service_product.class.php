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
        \file       htdocs/product_composition/product.class.php
        \ingroup    product
        \brief      *complete here*
		\version    $Id: service_product.class.php,v 1.1 2010/06/07 14:49:46 pit Exp $
		\author		Patrick Raguin
*/

/**
        \class      productService
        \brief      Service managing product
		\remarks	
*/
class service_product
{
	private $dao ;
	protected $db ;	
	private $error;
	
	public function service_product($db)
	{	
		$this->db = $db ;
		$this->dao = new Product($db) ;
	}
	
	public function getError(){return $this->error;}
	
	/*
     *      \brief      return an array with entries of the entity in database. This must be a select query and the rowid field must be selected as well.
     *      \return      array(ID => array(property => array(info => value))
     */
	public function getFromQuery($query,$where='',$order=''){
		
		global $langs ;
		
		// Check the query
		if (stristr($query,'SELECT')) dolibarr_syslog("service_product::getFromQuery. Query must be a SELECT statement : sql=".$query, LOG_DEBUG);
		if (stristr($query,'rowid')) dolibarr_syslog("service_product::getFromQuery. Query must select the rowid :  sql=".$query, LOG_DEBUG);
		
		// execute the query
		$all = Product::selectQuery($this->db,'',$query,$where,$order) ;
		
		// return the result
		return $this->selectAsArray($all) ;

	}
	
	/*
     *      \brief      	return an array with entries from the result of query in parameter
	 * 		\param $result	result of a query
	 *		\param $toDisplay array("fieldname" => data). fieldname, the fields to be displayed
     *      \return     	array(ID => array(property => array(info => value))
     */
	public function selectAsArray($result,$toDisplay =''){
	
		$liste = array() ;
		
		// If the query return smthg
		if($result){
			
			// we browse the result set using the $item var
			while($item = $this->db->fetch_object($result)){
				
				//id of the main entity
				$ID = $item->rowid ;
				
				//make the array
				foreach($item as $fieldName => $value){
				
					// display only recorded fields
					if( isset($toDisplay[$fieldName]) ||($toDisplay=='') ){
					
						$liste[$ID][$fieldName] = array(
							"attribute_name" => $fieldName,
							"attribute_type" => $this->getType($fieldName),
							"attribute_value" => $this->formatValue($this->getType($fieldName),$value),
							"showpage_name" => $this->getShowPage($fieldName,$ID,$value)
						) ;
						
					}
						
				} // end foreach
				
			} // end while
			
		}//end if
		
		return $liste ;
		
	}
	
	
	/*
     *      \brief      return an array with entries of the entity in database
     *      \return     array(array(properties...,URL))
     */
	public function getAllLight($where='',$order=''){
		
		global $langs ;
		
		$liste = array() ;
	
		$all = Product::select($this->db,$where,$order) ;
		if($all){
		
			while($item = $this->db->fetch_object($all)){
				$liste[] = array(
					array(
						"attribute" => "product_rowid",
						"label" => $langs->trans("product_rowid"),
						"value" => $item->rowid
					),

					array(
						"attribute" => "product_label",
						"label" => $langs->trans("Label"),
						"value" => $item->label
					),

				) ;
				
			}
		}
		
		return $liste ;
	}
	
	public function getListDisplayedFields(){
		
		global $langs ;
		
		$fields = array()  ;
		
			$fields[] = array(
				"label" => $langs->trans("groupe_libelle"),
				"type" => "VARCHAR(200)",
				"attribute" => "libelle"
			) ;
			$fields[] = array(
				"attribute_label" => $langs->trans("groupe_datec"),
				"type" => "DATETIME",
				"attribute" => "datec"
			) ;
			$fields[] = array(
				"label" => $langs->trans("groupe_note"),
				"type" => "VARCHAR(200)",
				"attribute" => "qte"
			) ;

		return $fields ;
	}
	

	/*
     *      \brief      try to add the element
     *      \return     $error		'' if ok, string of the error if error.
     */
	public function add($post){
	
		global $langs, $user ;

		//ref

		if($post["product_ref"] == '')
		{
			$this->error = $error = $langs->trans("ErrorFieldRequired",$langs->trans("Ref"));
			return -1;
		}
			$this->dao->ref = $post["product_ref"];
			
		//label
		if($post["product_label"] == '')
		{
			$this->error = $error = $langs->trans("ErrorFieldRequired",$langs->trans("Label"));
			return -1;
		}
			$this->dao->libelle = $post["product_label"];
			
		//description

			$this->dao->description = $post["product_description"];
			
		//note
			$this->dao->note =$post["product_note"];
			
		//price
			$this->dao->price = $post["product_price"];
			
		//price_ttc
			$this->dao->price_ttc = $post["product_price_ttc"];
			
		//price_base_type
			$this->dao->price_base_type = $post["product_price_base_type"];
			
		//tva_tx
			$this->dao->tva_tx = $post["product_tva_tx"];
			
		//envente
			$this->dao->status = $post["product_status"];
			
		//seuil_stock_alerte
			$this->dao->seuil_stock_alerte = $post["product_seuil_stock_alerte"];
			
		//weight
			$this->dao->weight = $post["product_weight"];
			
		//weight_units
			$this->dao->weight_units = $post["product_weight_units"];
			
		//volume
			$this->dao->volume = $post["product_volume"];
			
		//volume_units
			$this->dao->volume_units = $post["product_volume_units"];
			
		//canvas
			$this->dao->canvas = $post["product_canvas"];
	
		//type
			$this->dao->type = $post["product_type"];	

		//finished
			$this->dao->finished = $post["product_finished"];	
		
		if(!isset($error))
		{
			// Si tous les champs sont bien remplis on envoi la requete de creation
			
			
			$product_id = $this->dao->create($user);

			if ($product_id < 0)
			{
				// Si erreur lors de la creation
				$this->error = $this->dao->error();
				return -1;	
			}
			else
			{
		    	// La cr�ation s'est bien d�roul�e
		    	return $product_id;
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
	
		global $langs, $user ;
		
		$this->dao->fetch($post['id']);
		$error = 0;
		
		if(!$post["cancel"])
		{

			//product_datec
			if($post["product_datec"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","product_datec");
			}
			$this->dao->datec = $post["product_datec"];
			
			//product_tms
			$this->dao->tms = $post["product_tms"];
			
			//product_ref
			if($post["product_ref"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","product_ref");
			}
			$this->dao->ref = $post["product_ref"];
			
			//product_label
			if($post["product_label"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","product_label");
			}
			$this->dao->libelle = $post["product_label"];
			
			//product_description
			$this->dao->description = $post["product_description"];
			
			//product_note
			$this->dao->note = $post["product_note"];
			
			//product_price
			$this->dao->price = $post["product_price"];

			
			//product_price_base_type
			$this->dao->price_base_type = $post["product_price_base_type"];
			
			//product_tva_tx
			$this->dao->tva_tx = $post["product_tva_tx"];
			
			//product_envente
			$this->dao->status = $post["product_status"];
			
			//product_nbvente
			$this->dao->nbvente = $post["product_nbvente"];
			
			//product_stock_propale
			$this->dao->stock_propale = $post["product_stock_propale"];
			
			//product_stock_commande
			$this->dao->stock_commande = $post["product_stock_commande"];
			
			//product_seuil_stock_alerte
			$this->dao->seuil_stock_alerte = $post["product_seuil_stock_alerte"];
			
			//product_stock_loc
			$this->dao->stock_loc = $post["product_stock_loc"];
			
			//product_barcode
			$this->dao->barcode = $post["product_barcode"];
			
			//product_partnumber
			$this->dao->partnumber = $post["product_partnumber"];
			
			//product_weight
			$this->dao->weight = $post["product_weight"];
			
			//product_weight_units
			$this->dao->weight_units = $post["product_weight_units"];
			
			//product_volume
			$this->dao->volume = $post["product_volume"];
			
			//product_volume_units
			$this->dao->volume_units = $post["product_volume_units"];
			
			//product_canvas
			$this->dao->canvas = $post["product_canvas"];
			
			//product_fk_product_type
			$this->dao->fk_product_type = $post["product_fk_product_type"];
			
			//product_fk_barcode_type
			$this->dao->fk_barcode_type = $post["product_fk_barcode_type"];
			
	
			if($error == 0)
			{
				
				$update = $this->dao->update($post['id'],$user);
				if ($update < 0)
				{	      	
					$this->error = $this->dao->error;
      				return -1;
				}
				else
				{
					$updatePrice = $this->dao->update_price($post['id'],$post['product_price'],$post['product_price_base_type'],$user) ;
					
					if($updatePrice < 0)
					{
						$this->error = $this->dao->error;
						return -1;
					}
				}
				
			}
			else
			{
				$this->error = $error;
				return -1;
			}
			
		}

		return 0 ;	
		
	}

	/*
     *      \brief      Get data array from an occurence of the element
     *      \return     array(array("libelle","value")) if ok, empty array if ko
     */
	public function getAttributesShow($id){
		
		global $langs ;
		
		$data = array() ;

		
		if( $this->dao->fetch($id) != -1){		

			$data["product_ref"] = array(
				"label" => $langs->trans("Ref"),
				"value" => $this->dao->ref
			);
			
			$data["product_label"] = array(
				"label" => $langs->trans("Label"),
				"value" => $this->dao->libelle
			);
			
			$data["product_description"] = array(
				"label" => $langs->trans("Description"),
				"value" => $this->dao->description
			);
			
			$data["product_note"] = array(
				"label" => $langs->trans("Note"),
				"value" => $this->dao->note
			);
			
			$data["product_price"] = array(
				"label" => $langs->trans("SellingPrice")." HT",
				"value" => price($this->dao->price)
			);
			
			$data["product_price_ttc"] = array(
				"label" => $langs->trans("SellingPrice")." TTC",
				"value" => price($this->dao->price_ttc)
			);
			
			$data["product_price_base_type"] = array(
				"label" => $langs->trans("product_price_base_type"),
				"value" => $this->dao->price_base_type
			);
			
			$data["product_tva_tx"] = array(
				"label" => $langs->trans("VATRate"),
				"value" => vatrate($this->dao->tva_tx)
			);
			
			$data["product_envente"] = array(
				"label" => $langs->trans("Status"),
				"value" => $this->dao->getLibStatut(2)
			);
			
			$data["product_stock_reel"] = array(
				"label" => $langs->trans("product_stock_reel"),
				"value" => $this->dao->stock_reel
			);
			
			
			$data["product_seuil_stock_alerte"] = array(
				"label" => $langs->trans("product_seuil_stock_alerte"),
				"value" => $this->dao->seuil_stock_alerte
			);
			
			$data["product_stock_loc"] = array(
				"label" => $langs->trans("product_stock_loc"),
				"value" => $this->dao->getStock_loc
			);

			
			
			$data["product_weight"] = array(
				"label" => $langs->trans("Weight"),
				"value" => $this->dao->weight
			);
			
			
			$data["product_weight_units"] = array(
				"label" => $langs->trans("Weight"),
				"value" => measuring_units_string($this->dao->weight_units,"weight")
			);
			
			$data["product_volume"] = array(
				"label" => $langs->trans("product_volume"),
				"value" => $this->dao->getVolume
			);
			
			$data["product_volume_units"] = array(
				"label" => $langs->trans("product_volume_units"),
				"value" => $this->dao->getVolume_units
			);
			
			$data["product_statut"] = array(
				"label" => $langs->trans("product_statut"),
				"value" => $this->dao->statut
			);
			
			return $data ;
			
		}
		return -1;
		
	}
	
	
	/*
     *      \brief      Get data array from an occurence of the element
     *      \return     array(array("libelle","value")) if ok, empty array if ko
     */
	public function getAttributesShowLight($id){
		
		global $langs ;
		
		$data = array() ;

		
		if( $this->dao->fetch($id) > 0){		
		
			
			$data["product_ref"] = array(
				"label" => $langs->trans("Ref"),
				"value" => $this->dao->ref
			);
			
			$data["product_label"] = array(
				"label" => $langs->trans("Label"),
				"value" => $this->dao->libelle
			);
			
			
			if ($this->dao->price_base_type == 'TTC')
			{
				$price = price($this->dao->price_ttc).' '.$langs->trans($this->dao->price_base_type);
			}
			else
			{
				$price = price($this->dao->price).' '.$langs->trans($this->dao->price_base_type);
			}
			
			$data["product_price"] = array(
				"label" => $langs->trans("SellingPrice"),
				"value" => $price
			);

			$data["product_vatrate"] = array(
				"label" => $langs->trans("VATRate"),
				"value" => vatrate($this->dao->tva_tx,true)
			);

			$data["product_status"] = array(
				"label" => $langs->trans("Status"),
				"value" => $this->LibStatut($this->dao->status,2)
			);
			
			$data["product_description"] = array(
				"label" => $langs->trans("Description"),
				"value" => $this->dao->description
			);

			$data["product_weight"] = array(
				"label" => $langs->trans("Weight"),
				"value" => $this->dao->weight." ".measuring_units_string($this->dao->weight_units,"weight")
			);

			$data["product_volume"] = array(
				"label" => $langs->trans("Volume"),
				"value" => $this->dao->volume." ".measuring_units_string($this->dao->volume_units,"volume")
			);			
			
			$data["product_note"] = array(
				"label" => $langs->trans("Note"),
				"value" => $this->dao->note
			);
			

			
		}
		
		return $data ;
	}	

	
	
	/*
     *      \brief      Get attributs array from an entity
     *      \return     array(array("entity","attribute","type")) if ok, empty array if ko
     */
	public function getAttributesAdd($post){
		
		global $langs ;
		
		$data = array() ;
		
		//Attribut de la classe courante

			$data["product_ref"] = array(
					"entity" => "product",
					"attribute" => "ref",
					"type" => "string",
					"label" => $langs->trans("Ref"),
					"input" => getHtmlForm($this->db,self::getType("ref"),"product_ref",$post['product_ref'],false)
				);
			$data["product_label"] = array(
					"entity" => "product",
					"attribute" => "label",
					"type" => "string",
					"label" => $langs->trans("Label"),
					"input" => getHtmlForm($this->db,self::getType("label"),"product_label",$post['product_label'],false)
				);
			$data["product_description"] = array(
					"entity" => "product",
					"attribute" => "description",
					"type" => "string",
					"label" => $langs->trans("Description"),
					"input" => getHtmlForm($this->db,self::getType("description"),"product_description",$post['product_description'])
				);
			$data["product_note"] = array(
					"entity" => "product",
					"attribute" => "note",
					"type" => "string",
					"label" => $langs->trans("Note"),
					"input" => getHtmlForm($this->db,self::getType("note"),"product_note",$post['product_note'])
				);
			$data["product_price"] = array(
					"entity" => "product",
					"attribute" => "price",
					"type" => "edouble",
					"label" => $langs->trans("SellingPrice"),
					"input" => getHtmlForm($this->db,self::getType("price"),"product_price",$post['product_price'],0,"",10)
				);
			$data["product_price_base_type"] = array(
					"entity" => "product",
					"attribute" => "price_base_type",
					"type" => "edouble",
					"label" => $langs->trans("price_base_type"),
					"input" => getHtmlForm($this->db,self::getType("price_base_type"),"product_price_base_type",$post['product_price_base_type'])
				);

			$data["product_tva_tx"] = array(
					"entity" => "product",
					"attribute" => "tva_tx",
					"type" => "edouble",
					"label" => $langs->trans("VATRate"),
					"input" => getHtmlForm($this->db,self::getType("tva_tx"),"product_tva_tx",$post['product_tva_tx'])
				);
			$data["product_status"] = array(
					"entity" => "product",
					"attribute" => "envente",
					"type" => "boolean",
					"label" => $langs->trans("Status"),
					"input" => getHtmlForm($this->db,"status","product_status",$post['product_status'])
				);


			$data["product_seuil_stock_alerte"] = array(
					"entity" => "product",
					"attribute" => "seuil_stock_alerte",
					"type" => "integer",
					"label" => $langs->trans("StockLimit"),
					"input" => getHtmlForm($this->db,self::getType("seuil_stock_alerte"),"product_seuil_stock_alerte",$post['product_seuil_stock_alerte'],0,"",10)
				);


			$data["product_weight"] = array(
					"entity" => "product",
					"attribute" => "weight",
					"type" => "efloat",
					"label" => $langs->trans("Weight"),
					"input" => getHtmlForm($this->db,self::getType("weight"),"product_weight",$post['product_weight'],0,"",10)
				);
				
			$data["product_weight_units"] = array(
					"entity" => "product",
					"attribute" => "weight_units",
					"type" => "integer",
					"label" => $langs->trans(product."_".weight_units),
					"input" => getHtmlForm($this->db,self::getType("weight_units"),"product_weight_units",$post['product_weight_units'],0,"",10)
				);




		return $data ;
	}
	

	/*
     *      \brief      Get attributs and data array from an entity
     *      \return     array(array("label","value")) if ok, empty array if ko
     */
	public function getAttributesEdit($id,$post){
		
		global $langs ;
		
		$data = array() ;
		
		$this->dao->fetch($id);
		
		
		
		//Attribut de la classe courante
								
		
		$value = (($post['product_ref']=='')?$this->dao->ref:$post['product_ref']);
		$data["product_ref"] = array(
				"entity" => "product",
				"attribute" => "ref",
				"type" => self::getType("ref"),	
				"value" => $this->dao->ref,
				"label" => $langs->trans("Ref"),
				"input" => getHtmlForm($this->db,self::getType("ref"),"product_ref",$value)
			);
			
							
		$value = (($post['product_label']=='')?$this->dao->libelle:$post['product_label']);
		$data["product_label"] = array(
				"entity" => "product",
				"attribute" => "label",
				"type" => self::getType("label"),	
				"value" => $this->dao->libelle,
				"label" => $langs->trans("Label"),
				"input" => getHtmlForm($this->db,self::getType("label"),"product_label",$value)
			);
							
		$value = (($post['product_description']=='')?$this->dao->description:$post['product_description']);
		$data["product_description"] = array(
				"entity" => "product",
				"attribute" => "description",
				"type" => self::getType("description"),	
				"value" => $this->dao->description,
				"label" => $langs->trans("Description"),
				"input" => getHtmlForm($this->db,self::getType("description"),"product_description",$value)
			);
							
		$value = (($post['product_note']=='')?$this->dao->note:$post['product_note']);
		$data["product_note"] = array(
				"entity" => "product",
				"attribute" => "note",
				"type" => self::getType("note"),	
				"value" => $this->dao->note,
				"label" => $langs->trans("Note"),
				"input" => getHtmlForm($this->db,self::getType("note"),"product_note",$value)
			);

		$value = (($post['product_price_base_type']=='')?$this->dao->price_base_type:$post['product_price_base_type']);
		$data["product_price_base_type"] = array(
				"entity" => "product",
				"attribute" => "price_base_type",
				"type" => self::getType("price_base_type"),	
				"value" => $this->dao->price_base_type,
				"label" => $langs->trans("price_base_type"),
				"input" => getHtmlForm($this->db,self::getType("price_base_type"),"product_price_base_type",$value)
			);
			
		if($value == 'HT')
		{
			$value = (($post['product_price']=='')?$this->dao->price:$post['product_price']);
			
		}
		else
		{
			$value = (($post['product_price']=='')?$this->dao->price_ttc:$post['product_price']);
		}
		$data["product_price"] = array(
				"entity" => "product",
				"attribute" => "price",
				"type" => self::getType("price"),	
				"value" => $this->dao->price,
				"label" => $langs->trans("SellingPrice"),
				"input" => getHtmlForm($this->db,self::getType("price"),"product_price",price($value),0,"",15)
			);
							
			

							
		$value = (($post['product_tva_tx']=='')?$this->dao->tva_tx:$post['product_tva_tx']);
		$data["product_tva_tx"] = array(
				"entity" => "product",
				"attribute" => "tva_tx",
				"type" => self::getType("tva_tx"),
				"value" => $this->dao->tva_tx,
				"label" => $langs->trans("VATRate"),
				"input" => getHtmlForm($this->db,self::getType("tva_tx"),"product_tva_tx",$value)
			);
							
		$value = (($post['product_status']=='')?$this->dao->status:$post['product_status']);
		$data["product_status"] = array(
				"entity" => "product",
				"attribute" => "status",
				"type" => self::getType("status"),
				"value" => $this->dao->status,
				"label" => $langs->trans("Status"),
				"input" => getHtmlForm($this->db,self::getType("status"),"product_status",$value)
			);
							

							
		$value = (($post['product_seuil_stock_alerte']=='')?$this->dao->seuil_stock_alerte:$post['product_seuil_stock_alerte']);
		$data["product_seuil_stock_alerte"] = array(
				"entity" => "product",
				"attribute" => "seuil_stock_alerte",
				"type" => self::getType("seuil_stock_alerte"),
				"value" => $this->dao->seuil_stock_alerte,
				"label" => $langs->trans("seuil_stock_alerte"),
				"input" => getHtmlForm($this->db,self::getType("seuil_stock_alerte"),"product_seuil_stock_alerte",$value)
			);
							
							
							
		$value = (($post['product_weight']=='')?$this->dao->weight:$post['product_weight']);
		$data["product_weight"] = array(
				"entity" => "product",
				"attribute" => "weight",
				"type" => self::getType("weight"),
				"value" => $this->dao->weight,
				"label" => $langs->trans("Weight"),
				"input" => getHtmlForm($this->db,self::getType("weight"),"product_weight",$value,0,"",7)
			);
							
		$value = (($post['product_weight_units']=='')?$this->dao->weight_units:$post['product_weight_units']);
		$data["product_weight_units"] = array(
				"entity" => "product",
				"attribute" => "weight_units",
				"type" => self::getType("weight_units"),
				"value" => $this->dao->weight_units,
				"label" => $langs->trans("weight_units"),
				"input" => getHtmlForm($this->db,self::getType("weight_units"),"product_weight_units",$value)
			);

		

		return $data ;
	}

	/*
     *      \brief      Get products
     *      \return     array of products
     */
	public function getCompoProductsDisplay()
	{
		//Liste des �lements qui composent l'article
		
		
		global $langs ;
		
		$liste = array();
		$where = '';
		
		//R�cup�re le rowid des sous produits
		$res = Product_composition::select($this->db,'','fk_product = '.$this->dao->getId());
		if(($res)&&($this->db->num_rows() > 0))
		{	
			while($item = $this->db->fetch_object($res))
			{
				$productID[] = $item->fk_product_composition;
				$product_qte[] = $item->qte;	
			}
			
			$where = 'rowid IN ('.implode(",",$productID).')';

			//r�cup�re les sous-produits � partir des rowid
			$liste = array() ;
			$all = Product::select($this->db,'',$where,$order) ;
			if($all){
			
				while($item = $this->db->fetch_object($all)){
					$qte = $product_qte[array_search($item->rowid,$productID)];

					$liste[] = array(
						"ID" => $item->rowid,
						array(
							"attribute" => "libelle",
							"label" => $langs->trans("Label"),
							"value" => $item->label
						),
	
						array(
							"attribute" => "prix",
							"label" => $langs->trans("SellingPrice"),
							"value" => $item->price
						),
						
						"qte" => $qte
						
					) ;
					
				}
			}
			
		}

		return $liste	;
		
		

		
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
		    $form->form_confirm(DOL_URL_ROOT.'/product_composition/.php?id='.$_GET["id"],$langs->trans("delete",$langs->trans("product")),$langs->trans("confirm_delete",$langs->trans("product")),"confirm_delete");
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


  /**
   *    	\brief      Renvoi le libell� d'un statut donne
   *    	\param      status      Statut
   *		\param      mode        0=libell� long, 1=libell� court, 2=Picto + Libell� court, 3=Picto, 4=Picto + Libell� long, 5=Libell� court + Picto
   *    	\return     string      Libell� du statut
   */
  public function LibStatut($status,$mode=0)
  {
    global $langs;
    $langs->load('products');
    if ($mode == 0)
      {
	if ($status == 0) return $langs->trans('ProductStatusNotOnSellShort');
	if ($status == 1) return $langs->trans('ProductStatusOnSellShort');
      }
    if ($mode == 1)
      {
	if ($status == 0) return $langs->trans('ProductStatusNotOnSell');
	if ($status == 1) return $langs->trans('ProductStatusOnSell');
      }
    if ($mode == 2)
      {
	if ($status == 0) return img_picto($langs->trans('ProductStatusNotOnSell'),'statut5').' '.$langs->trans('ProductStatusNotOnSell');
	if ($status == 1) return img_picto($langs->trans('ProductStatusOnSell'),'statut4').' '.$langs->trans('ProductStatusOnSell');
      }
    if ($mode == 3)
      {
	if ($status == 0) return img_picto($langs->trans('ProductStatusNotOnSell'),'statut5');
	if ($status == 1) return img_picto($langs->trans('ProductStatusOnSell'),'statut4');
      }
    if ($mode == 4)
      {
	if ($status == 0) return img_picto($langs->trans('ProductStatusNotOnSell'),'statut5').' '.$langs->trans('ProductStatusNotOnSell');
	if ($status == 1) return img_picto($langs->trans('ProductStatusOnSell'),'statut4').' '.$langs->trans('ProductStatusOnSell');
      }
    if ($mode == 5)
      {
	if ($status == 0) return $langs->trans('ProductStatusNotOnSell').' '.img_picto($langs->trans('ProductStatusNotOnSell'),'statut5');
	if ($status == 1) return $langs->trans('ProductStatusOnSell').' '.img_picto($langs->trans('ProductStatusOnSell'),'statut4');
      }
    return $langs->trans('Unknown');
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
			case "datec" :
				return "datetime" ;
				break ;
			case "tms" :
				return "datetime" ;
				break ;
			case "ref" :
				return "string" ;
				break ;
			case "label" :
				return "string" ;
				break ;
			case "description" :
				return "text" ;
				break ;
			case "note" :
				return "text" ;
				break ;
			case "price" :
				return "string" ;
				break ;
			case "price_base_type" :
				return "price_base_type" ;
				break ;
			case "tva_tx" :
				return "vatrate" ;
				break ;
			case "status" :
				return "status" ;
				break ;
			case "nbvente" :
				return "integer" ;
				break ;
			case "fk_product_type" :
				return "integer" ;
				break ;
			case "duration" :
				return "string" ;
				break ;
			case "stock_propale" :
				return "integer" ;
				break ;
			case "stock_commande" :
				return "integer" ;
				break ;
			case "seuil_stock_alerte" :
				return "integer" ;
				break ;
			case "stock_loc" :
				return "string" ;
				break ;
			case "barcode" :
				return "string" ;
				break ;
			case "fk_barcode_type" :
				return "integer" ;
				break ;
			case "partnumber" :
				return "string" ;
				break ;
			case "weight" :
				return "string" ;
				break ;
			case "weight_units" :
				return "weight_units" ;
				break ;
			case "volume" :
				return "string" ;
				break ;
			case "volume_units" :
				return "integer" ;
				break ;
			case "canvas" :
				return "string" ;
				break ;
			case "finished" :
				return "boolean" ;
				break ;
			case "price_ttc" :
				return "string" ;
				break ;
		}
	}



}
//End of user code
?>
