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
        \file       htdocs/product_composition/product_composition.class.php
        \ingroup    product_composition
        \brief      *complete here*
		\version    $Id: service_product_composition.class.php,v 1.1 2010/06/07 14:49:47 pit Exp $
		\author		Patrick Raguin
*/

/**
        \class      product_compositionService
        \brief      Service managing product_composition
		\remarks
*/
class service_product_composition
{
	private $dao ;
	protected $db ;
	private $error;

	public function service_product_composition($db)
	{
		$this->db = $db ;
		$this->dao = new dao_product_composition($db) ;
	}

	public function getError(){ return $this->error;}

	/*
     *      \brief      return an array with entries of the entity in database
	 *		\param		$where	condition of the WHERE clause (ie. "t.label = 'bar'")
	 *		\param		$order	condition of the ORDER BY clause (ie. "t.rowid DESC")
     *      \return     array(array(properties...,URL))
     */
	public function getAllListDisplayed($where='',$order=''){

		global $langs ;

		$liste = array() ;
		$all = dao_product_composition::select($this->db,$where,$order) ;
		if($all){

			while($item = $this->db->fetch_object($all)){

				$liste[] = array(
					"ID" => $item->rowid,
					array(
						"attribute" => "product_rowid",
						"label" => $langs->trans("compteur_rowid"),
						"value" => $item->rowid
					),
					array(
						"attribute" => "compteur_libelle",
						"label" => $langs->trans("compteur_libelle"),
						"value" => $item->libelle
					),
					array(
						"attribute" => "compteur_note",
						"label" => $langs->trans("compteur_note"),
						"value" => yn($item->note)
					),
				) ;

			}
		}

		return $liste ;
	}

	public function getListDisplayedFields(){

		global $langs ;

		$html = new Form($this->db);

		$fields = array()  ;

		$fields[] = array(
			"label" => $langs->trans("Label"),
			"type" => "VARCHAR(200)",
			"attribute" => "product_label"
		) ;
		$fields[] = array(
			"label" => $html->textwithhelp($langs->trans("UnitPriceHT"),$langs->trans('UnitPriceIsBasedFromCUMPOrSellingPrice'),1,0),
			"type" => "EDOUBLE",
			"attribute" => "product_price"
		) ;
		$fields[] = array(
			"label" => $langs->trans("qte"),
			"type" => "INT",
			"attribute" => "product_qte"
		) ;

		$fields[] = array(
			"label" => $langs->trans("StockManaged"),
			"type" => "TINYINT",
			"attribute" => "product_etat"
		) ;

		$fields[] = array(
			"label" => $langs->trans("FactoryPrice"),
			"type" => "TINYINT",
			"attribute" => "product_buying_cost"
		) ;


		$fields[] = array(
			"label" => $langs->trans("total_price","HT"),
			"type" => "INT",
			"attribute" => "total_price"
		) ;

		$fields[] = array(
			"label" => $langs->trans("total_price","TTC"),
			"type" => "INT",
			"attribute" => "total_price_ttc"
		) ;

		return $fields ;
	}

	/*
     *      \brief      Get attributs and data array from an entity
     *      \return     array(array("libelle","value")) if ok, empty array if ko
     */
	public function getAttributesAdd(){

		global $langs ;

		$data = array() ;



		$html = new Form($this->db);

		$dao_product->id;

		//Récupère la liste des produits (le dernier 0 correspond aux matières premières, le 4ème le nombre maximum de produits récupérés dans la base)
		ob_start();
		$html->select_produits('','product','',100,0,-1,0);
		$input = ob_get_contents();
		ob_end_clean();

		$data[] = array(
				"attribute" => "product",
				"type" => "string",
				"label" => $langs->trans("Product"),
				"input" => $input
			);

		//Qt�
		$data[] = array(
				"attribute" => "qte",
				"type" => "INT",
				"label" => $langs->trans("qte"),
				"input" => getHtmlForm('INT','qte')
			);

		//Gestion des stocks
		$data[] = array(
				"attribute" => "etat_stock",
				"type" => "TINYINT",
				"label" => $langs->trans("etatStock"),
				"input" => getHtmlForm('boolean','etat_stock')
			);

		//Cout de revient
		$data[] = array(
				"attribute" => "buying_cost",
				"type" => "TINYINT",
				"label" => $langs->trans("FactoryPrice"),
				"input" => getHtmlForm('boolean','buying_cost',1)
			);


		return $data ;
	}

	/*
     *      \brief      Get attributs and data array from an entity
     *      \return     array(array("libelle","value")) if ok, empty array if ko
     */
	public function getAttributesEdit($id){

		global $langs ;

		$data = array() ;

		$this->dao->fetch($id);
		//Attribut de la classe courante
		$dao_product = new Product($this->db,$this->dao->getFk_product_composition());

		$dao_product->id;
		$data[] = array(
				"attribute" => "label",
				"type" => "string",
				"value" => $dao_product->libelle,
				"label" => $langs->trans("label"),
				"input" => $dao_product->libelle
			);


		$data[] = array(
				"attribute" => "qte",
				"type" => "INT",
				"value" => $this->dao->getQte(),
				"label" => $langs->trans("qte"),
				"input" => getHtmlForm('INT','qte',$this->dao->getQte(),0)
			);


		$data[] = array(
				"attribute" => "etat_stock",
				"type" => "TINYINT",
				"value" => $this->dao->getEtat_stock(),
				"label" => $langs->trans("etatStock"),
				"input" => getHtmlForm('boolean','etat_stock',$this->dao->getEtat_stock(),0)
			);

		$data[] = array(
				"attribute" => "buying_cost",
				"type" => "TINYINT",
				"value" => $this->dao->getBuying_cost(),
				"label" => $langs->trans("FactoryPrice"),
				"input" => getHtmlForm('boolean','buying_cost',$this->dao->getBuying_cost(),0)
			);


		return $data ;
	}


	/*
     *      \brief      Get products
     *      \return     array of products
     */
	public function getCompoProductsDisplay($product)
	{
		//Liste des élements qui composent l'article


		global $langs ;

		$liste = array();

		//Récupère le rowid des sous produits
		$res = dao_product_composition::select($this->db,'fk_product = '.$product);

		if(($res)&&($this->db->num_rows() > 0))
		{
			while($item = $this->db->fetch_object($res))
			{

				//récupère les informations des sous-produits ainsi que le cump
				$rowid = $item->rowid;
				$where = 'rowid = '.$item->fk_product_composition;

				$sql = "SELECT p.rowid, p.label, p.price, p.price_ttc, p.tva_tx, p.pmp";
				$sql.= " FROM ".MAIN_DB_PREFIX."product p";
				$sql.= " WHERE p.rowid = ".$item->fk_product_composition;
				$sql.= " ORDER BY p.tms DESC";
				$sql.= " LIMIT 1";

				$all = dao_product_composition::selectQuery($this->db,$sql);


				if($all)
				{
					$qte = $item->qte;
					$etat_stock = $item->etat_stock;
					$buying_cost = $item->buying_cost;

					while($item = $this->db->fetch_object($all))
					{

						//Calcul le cout unitaire et ttc
						//à partir du cump
						if($item->price_pmp != 0)
						{
							$price = $item->price_pmp;
							$price_ttc = $item->price_pmp * (1 +($item->tva_tx / 100));

						}
						//� partir du prix de vente
						else
						{
							$price = $item->price;
							$price_ttc = $item->price_ttc;
						}


						$liste[] = array(

							"id_compo" => $rowid,
							"id_product" => $item->rowid,


							array(
								"attribute" => "libelle",
								"label" => $langs->trans("Label"),
								"value" => $item->label
							),


							array(
								"attribute" => "prix",
								"label" => $langs->trans("SellingPrice"),
								"value" => price($price)." HT"
							),

							array(
								"attribute" => "qte",
								"label" => $langs->trans("qte"),
								"value" => $qte

							),

							array(
								"attribute" => "etat_stock",
								"label" => $langs->trans("etatStock"),
								"value" => yn($etat_stock)

							),

							array(
								"attribute" => "selling_price",
								"label" => $langs->trans("selling_price"),
								"value" => yn($buying_cost)

							),

							array(
								"attribute" => "prix_total",
								"label" => $langs->trans("prix_total"),
								"value" => price($price * $qte)." HT"
							),

							array(
								"attribute" => "prix_total_ttc",
								"label" => $langs->trans("prix_total_ttc"),
								"value" => price($price_ttc * $qte)." TTC"
							)


						) ;

					}
				}
			}

		}

		return $liste	;

	}


	/*
     *      \brief      try to add the element
     *      \return     $error		'' if ok, string of the error if error.
     */
	public function add($post){

		global $langs ;

		$dao_product_composition = $this->dao;

		//Fk_product
		if($post["id_product"] == '')
		{
			$error = $langs->trans("ErrorFieldRequired","Fk_product");
		}
		$dao_product_composition->setFk_product($post['id_product']);

		//Fk_product_composition
		if($post["product"] == '')
		{
			$error = $langs->trans("ErrorFieldRequired","product");
		}
		$dao_product_composition->setFk_product_composition($post['product']);

		//qte
		if($post["qte"] == 0)
		{
			$error = $langs->trans("ErrorFieldRequired","qte");
		}
		$dao_product_composition->setQte($post['qte']);

		//etat_stock
		$dao_product_composition->setEtat_stock($post['etat_stock']);

		//buying_cost
		$dao_product_composition->setBuying_cost($post['buying_cost']);

		if(!isset($error))
		{
			// Si tous les champs sont bien remplis on envoi la requète de cr�ation
		  if ( ($dao_product_composition->create())==-1)
		    {
		      	// Si erreur lors de la création
		      	$this->error = $langs->trans("product_composition_error");
		      	return -1;

		    }
		    else
		    {

		    	// La création s'est bien déroulée
		    	return $dao_product_composition->getId();

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
     *      \return     $error	if ok, string of the error if error.
     */
	public function update($post){

		global $langs ;
		$this->dao->fetch($post['id']);


		if(!$post["cancel"])
		{

			//qte
			if($post["qte"] == '')
			{
				$error = $langs->trans("ErrorFieldRequired","qte");
			}
			$this->dao->setQte($post["qte"]);


			//etat_stock
			$this->dao->setEtat_stock($post["etat_stock"]);

			//buying_cost
			$this->dao->setBuying_cost($post["buying_cost"]);

			if(!isset($error))
			{
			  if ( $this->dao->update()!=0)
			    {
			      	$error = $langs->trans("product_composition_error");
			    }


			}
		}

		return $error ;

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
		    $form->form_confirm(DOL_URL_ROOT.'/composition/show_composition.php?id='.$_GET["id"].'&compo_product='.$_GET["compo_product"],$langs->trans("delete",$langs->trans("product_composition")),$langs->trans("confirm_delete",$langs->trans("product_composition")),"confirm_delete");
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
     *      \brief      	return label of the etat_stock
     *		\param $etat	num of etat
     *      \return     	label
     */
	public function labelEtatStock($etat=0)
	{
		global $langs;

		switch ($etat)
		{
			case 0:
				return $langs->trans("NoStock");
				break;
			case 1:
				return $langs->trans("StockManaged");
				break;

		}
	}

	/*
     *      \brief      Get list of etatStock
     *      \return     array
     */
	public function getListEtatStock()
	{
		$list = array();

		$list[] = array(
						"list_label" => $this->labelEtatStock(0),
						 "list_value" => 0
						);

		$list[] = array(
						"list_label" => $this->labelEtatStock(1),
						 "list_value" => 1
						);


		return $list;
	}


	public function select_etatstock($selected='',$htmlname='etat_stock')
	{

		$etat_stock = $this->getListEtatStock();	// Get array

		$select_etat_stock = '<select class="flat" name="'.$htmlname.'">';

		foreach ($etat_stock as $key => $val)
		{
			if ($selected == $key)
			{
				$select_etat_stock .= '<option value="'.$key.'" selected="true">';
			}
			else
			{
				$select_etat_stock .= '<option value="'.$key.'">';
			}
			$select_etat_stock .= $val['list_label'];
		}

		$select_month .= '</select>';

		return $select_etat_stock;

	}



	/*
     *      \brief      Cette méthode calcul le cout de revient pour toutes les compositions
     *      \return     array of prices (HT and TTC)
     */
	public function getFactoryPriceAll($product_id)
	{
		$sql = "SELECT p.tva_tx, p.price*c.qte as total_price, p.price_ttc*c.qte as total_price_ttc, p.pmp*c.qte as cump";
		$sql.= " FROM llx_product_composition c JOIN llx_product p ON c.fk_product_composition = p.rowid";
		$sql.= " WHERE c.fk_product = ".$product_id;

		$query = dao_product_composition::selectQuery($this->db,$sql);

		$factory_price = 0;
		$factoy_price_ttc = 0;

		while($obj = $this->db->fetch_object($query))
		{
			if($obj->cump != 0)
			{
				$factory_price += $obj->cump;
				$factory_price_ttc += $obj->cump * (1 + $obj->tva_tx / 100);
			}
			else
			{
				$factory_price += $obj->total_price;
				$factory_price_ttc += $obj->total_price_ttc;
			}

		}

		$price['HT'] = $factory_price;
		$price['TTC'] = $factory_price_ttc;
		return $price;


	}





	/*
     *      \brief   	Cette méthode calcul le cout de revient avec les compositions dont la l'option "cout de revient" est à 1.
     *      \return     array of prices (HT and TTC)
     */
	public function getFactoryPrice($product_id)
	{
		$sql = "SELECT p.tva_tx, p.price*c.qte as total_price, p.price_ttc*c.qte as total_price_ttc, p.pmp*c.qte as cump";
		$sql.= " FROM llx_product_composition c JOIN llx_product p ON c.fk_product_composition = p.rowid";
		$sql.= " WHERE c.fk_product = ".$product_id;
		$sql.= " AND c.buying_cost = 1";

		$query = dao_product_composition::selectQuery($this->db,$sql);

		$factory_price = 0;
		$factoy_price_ttc = 0;

		if($query)
		{
			while($obj = $this->db->fetch_object($query))
			{
				if($obj->cump != 0)
				{
					$factory_price += $obj->cump;
					$factory_price_ttc += $obj->cump * (1 + $obj->tva_tx / 100);
				}
				else
				{
					$factory_price += $obj->total_price;
					$factory_price_ttc += $obj->total_price_ttc;
				}
	
			}
		}

		$price['HT'] = $factory_price;
		$price['TTC'] = $factory_price_ttc;
		return $price;

	}



}
//End of user code
?>
