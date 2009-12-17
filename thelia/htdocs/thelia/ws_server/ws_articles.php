<?php
/*  Copyright (C) 2006      Jean Heimburger     <jean@tiaris.info>
 *
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
 * $Id: ws_articles.php,v 1.1 2009/12/17 14:57:00 hregis Exp $
 */

set_magic_quotes_runtime(0);

require_once("./includes/configure.php");


// Soap Server.
require_once('./lib/nusoap.php');

// Create the soap Object
$s = new soap_server();
$ns='thelia';
$s->configureWSDL('WebServicesTheliaForDolibarrProducts',$ns);
$s->wsdl->schemaTargetNamespace=$ns;

$s->wsdl->addComplexType(
   'produit',
   'complexType',
   'struct',
   'all',
   '',
   array(
      'THELIA_id'=> array('name'=>'THELIA_id','type'=>'xsd:int','use'=>'optionnal'),
      'ref'=>array('name'=>'ref','type'=>'xsd:string', 'use'=>'optionnal'),
      'status'=>array('name'=>'status','type'=>'xsd:string', 'use'=>'optionnal'),
      'titre'=>array('name'=>'type','type'=>'xsd:string', 'use'=>'optionnal'),
      'description'=>array('name'=>'description','type'=>'xsd:string', 'use'=>'optionnal'),
      'stock'=>array('name'=>'stock','type'=>'xsd:string', 'use'=>'optionnal'),
      'prix'=>array('name'=>'prix','type'=>'xsd:string', 'use'=>'optionnal')
   )
);
   
$s->wsdl->addComplexType(
   'produits',
   'complexType',
   'array',
   '',
   'SOAP-ENC:Array',
   array(),
   array(
      array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:produit[]')
   ),
   'tns:produit'
   ); 
 
$s->wsdl->addComplexType(
   'rubrique',
   'complexType',
   'struct',
   'all',
   '',
   array(
      'id'=> array('name'=>'id','type'=>'xsd:int','use'=>'optionnal'),
      'parent'=>array('name'=>'parent','type'=>'xsd:int', 'use'=>'optionnal'),
      'titre'=>array('name'=>'titre','type'=>'xsd:string', 'use'=>'optionnal'),
      'chapo'=>array('name'=>'chapo','type'=>'xsd:string', 'use'=>'optionnal'),
      'description'=>array('name'=>'description','type'=>'xsd:string', 'use'=>'optionnal'),
      'ligne'=>array('name'=>'ligne','type'=>'xsd:int', 'use'=>'optionnal')
   )
);   
$s->wsdl->addComplexType(
   'rubriques',
   'complexType',
   'array',
   '',
   'SOAP-ENC:Array',
   array(),
   array(
      array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:rubrique[]')
   ),
   'tns:rubrique'
); 

// Register a method available for clients
$s->register('get_article', array(), array('id'=>'xsd:int', 'ref'=>'xsd:string', 'prix'=>'xsd:string', 'stock'=>'xsd:string', 'rubrique'=>'xsd:string','statut'=>'xsd:int', 'titre'=>'xsd:string','description'=>'xsd:string' ), $ns);
$s->register("get_listearticles", array(), array('produits'=>'tns:produits'), $ns);
$s->register('create_article');
$s->register('get_categorylist', array('catid'=>''), array('categories'=>'tns:rubriques'),$ns);



function create_article($prod)
{
	// make a connection to the database... now
	tep_db_connect() or die('Unable to connect to database server!');

	// v�rifier les param�tres
	$sql_data_array = array('products_quantity' => $prod['quant'],
         'products_model' => $prod['ref'],
         'products_image' => $prod['image'],
         'products_price' => $prod['prix'],
         'products_weight' => $prod['poids'],
         'products_date_added' => 'now()',
         'products_last_modified' => '',
         'products_date_available' => $prod['dispo'],
         'products_status' => $prod['status'],
         'products_tax_class_id' => $prod['ttax'],
         'manufacturers_id' => $prod['fourn']);

	tep_db_perform(TABLE_PRODUCTS, $sql_data_array);
	$products_id = tep_db_insert_id();

	$category_id = 2;
	tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . (int)$products_id . "', '" . (int)$category_id . "')");

	$languages = tep_get_languages();
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$language_id = $languages[$i]['id'];
		$sql_data_array = array('products_name' => $prod['nom'],
                                    'products_description' => $prod['desc'],
                                    'products_url' => $prod['url'],
		//'products_head_title_tag' => $prod['nom'],
		//'products_head_desc_tag' => $prod['desc'],
		//'products_head_keywords_tag' => '',
                                 	'products_id' => $products_id,
                                    'language_id' => $language_id
		);
		tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
	}

	return $products_id;
}


function get_article($id='',$ref='')
{
	//on se connecte
	if (!($connexion = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD)))   return new soap_fault("Server", "MySQL 1", "connexion impossible");
	if (!($db = mysql_select_db(DB_DATABASE, $connexion)))  return new soap_fault("Server", "MySQL 2", mysql_error());

	//on recherche
	$sql = "SELECT p.id, p.ref, p.stock, p.ligne as statut, p.prix, p.rubrique,d.titre ,d.chapo,d.description";
	$sql .= " FROM produit as p ";
	$sql .= " JOIN produitdesc as d ON p.id = d.produit ";
	$sql .= " WHERE d.lang =" . OSC_LANGUAGE_ID;
	if ($id) $sql.= " AND p.id = ".$id;
	if ($ref) $sql.= " AND p.ref = '".addslashes($ref)."'";
	if (!($resquer = mysql_query($sql,$connexion)))  return new soap_fault("Server", "MySQL 3 ".$sql, mysql_error());
  
	switch (mysql_numrows($resquer)) {
		case 0 :
			return new soap_fault("Server", "MySQL 4", "produit inexistant");
			break;
		case 1 :
			$res_article =   @mysql_fetch_array($resquer, MYSQL_ASSOC);
			$res_article["time"] = time();
			break;
		default :
			return new soap_fault("Server", "MySQL 5", "erreur requete");
	} 
	mysql_close($connexion);
   
	/* Sends the results to the client */
	return $res_article;
}

function get_listearticles() {

	//on se connecte
	if (!($connexion = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD)))   return new soap_fault("Server", "MySQL 1", "connexion impossible");
	if (!($db = mysql_select_db(DB_DATABASE, $connexion)))  return new soap_fault("Server", "MySQL 2", mysql_error());
   $liste_articles = array();
	//on recherche
	$sql = "SELECT p.id as THELIA_id, p.ref as ref, p.stock as stock, p.ligne as status, d.titre as titre, d.description";
	$sql .= " FROM produit as p";
	$sql .= " JOIN produitdesc as d ON p.id = d.produit "; 		 	
	$sql .= " WHERE d.lang =" . OSC_LANGUAGE_ID;
	if (!($resquer = mysql_query($sql,$connexion)))  return new soap_fault("Server", "MySQL 3 ".$sql, mysql_error());
	switch ($numrows = mysql_numrows($resquer)) {
		case 0 :
			return new soap_fault("Server", "MySQL 4", "produit inexistant");
			break;
		default :
			$i = 0;
			while ( $i < $numrows)  {
				$liste_articles[$i] = mysql_fetch_array($resquer, MYSQL_ASSOC);
				$i++;
			}
         
	}

	mysql_close($connexion);
	/* Sends the results to the client */
  // print_r($liste_articles);
	return $liste_articles;
}

function saveImage($name,$content)
{
	$fich = fopen(OSCIMAGES.$name, 'wb');
	fwrite($fich,base64_decode($content));
	fclose($fich);
	return $name.' enregistr�';
}



// OSC categories list from $catid
function get_categorylist($catid)
{
	//on se connecte
	if (!($connexion = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD)))   return new soap_fault("Server", "MySQL 1", "connexion impossible");
	if (!($db = mysql_select_db(DB_DATABASE, $connexion)))  return new soap_fault("Server", "MySQL 2", mysql_error());

	$sql = "SELECT r.id, r.parent, rd.titre, rd.description, rd.chapo";
	$sql .= " FROM rubrique as r, rubriquedesc as rd ";
	$sql .= " WHERE r.parent = '".$catid."' and r.id = rd.rubrique and rd.lang='" . OSC_LANGUAGE_ID ."' order by classement, rd.titre";

	if (!($resquer = mysql_query($sql,$connexion)))  return new soap_fault("Server", "MySQL gey_categorylist ".$sql, mysql_error());

	switch ($numrows = mysql_numrows($resquer)) {
		case 0 :
			return new soap_fault("Server", "MySQL gey_categorylist", "pas de categories");
			break;
		default :
			$i = 0;
			while ( $i < $numrows)
			{
				$liste_cat[$i] =  mysql_fetch_array($resquer, MYSQL_ASSOC);
				$i++;
            
			}
	}
	mysql_close($connexion);
	/* Sends the results to the client */
	return $liste_cat;
}


// Return the results.
$s->service($HTTP_RAW_POST_DATA);

?>
