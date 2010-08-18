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
 * $Id: ws_orders.php,v 1.4 2010/08/18 13:46:31 eldy Exp $
 */
set_magic_quotes_runtime(0);
//if (function_exists('xdebug_disable')) xdebug_disable();

// Soap Server.
require_once('./lib/nusoap.php');

require_once('./includes/configure.php');

/*
// fichier THELIA_id
include_once("../classes/Commande.class.php");
include_once("classes/Client.class.php");
include_once("classes/Venteprod.class.php");
include_once("classes/Ventedeclidisp.class.php");
include_once("classes/Stock.class.php");
include_once("classes/Statut.class.php");
include_once("classes/Statutdesc.class.php");
include_once("fonctions/divers.php");
include_once("classes/Devise.class.php");*/

// Create the soap Object
$s = new soap_server;
$ns='thelia';
$s->configureWSDL('WebServicesTheliaForDolibarrOrders',$ns);
$s->wsdl->schemaTargetNamespace=$ns;


$s->wsdl->addComplexType(
   'CAmensuel',
   'complexType',
   'struct',
   'all',
   '',
   array(
      'value'=> array('name'=>'THELIA_id','type'=>'xsd:int','use'=>'optionnal'),
      'mois'=>array('name'=>'ref','type'=>'xsd:string', 'use'=>'optionnal'),
      'annee'=>array('name'=>'status','type'=>'xsd:string', 'use'=>'optionnal'),
   )
);

$s->wsdl->addComplexType(
   'TableauCA',
   'complexType',
   'array',
   '',
   'SOAP-ENC:Array',
   array(),
   array(
   array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:CAmensuel[]')
   ),
   'tns:CAmensuel'
);


$s->wsdl->addComplexType(
   'Commandes',
   'complexType',
   'struct',
   'all',
   '',
   array(
      'id'=> array('name'=>'id','type'=>'xsd:int','use'=>'optionnal'),
      'ref'=>array('name'=>'ref','type'=>'xsd:string', 'use'=>'optionnal'),
      'date'=>array('name'=>'date','type'=>'xsd:string', 'use'=>'optionnal'),
      'livraison'=>array('name'=>'livraison','type'=>'xsd:string', 'use'=>'optionnal'),
      'port'=>array('name'=>'port','type'=>'xsd:string', 'use'=>'optionnal'),
      'remise'=>array('name'=>'remise','type'=>'xsd:string', 'use'=>'optionnal'),
      'colis'=>array('name'=>'colis','type'=>'xsd:string', 'use'=>'optionnal'),
      'total'=>array('name'=>'total','type'=>'xsd:string', 'use'=>'optionnal'),
      'paiement'=>array('name'=>'paiement','type'=>'xsd:int', 'use'=>'optionnal'),
      'statut'=>array('name'=>'statut','type'=>'xsd:int', 'use'=>'optionnal'),
      'client_id'=>array('name'=>'client_id','type'=>'xsd:int', 'use'=>'optionnal'),
      'client_ref'=>array('name'=>'client_ref','type'=>'xsd:string', 'use'=>'optionnal'),
      'nom'=>array('name'=>'nom','type'=>'xsd:string', 'use'=>'optionnal'),
      'prenom'=>array('name'=>'prenom','type'=>'xsd:string', 'use'=>'optionnal'),
      'prod_id'=>array('name'=>'prod_id','type'=>'xsd:string', 'use'=>'optionnal'),
      'prod_ref'=>array('name'=>'prod_ref','type'=>'xsd:string', 'use'=>'optionnal'),
      'prod_titre'=>array('name'=>'prod_titre','type'=>'xsd:string', 'use'=>'optionnal'),
      'prod_prixu'=>array('name'=>'prod_prixu','type'=>'xsd:string', 'use'=>'optionnal'),
      'prod_tva'=>array('name'=>'prod_tva','type'=>'xsd:string', 'use'=>'optionnal'),
      'prod_quantite'=>array('name'=>'prod_prod_quantite','type'=>'xsd:int', 'use'=>'optionnal')
     )
);

$s->wsdl->addComplexType(
   'TableauCommandes',
   'complexType',
   'array',
   '',
   'SOAP-ENC:Array',
   array(),
   array(
      array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Commandes[]')
   ),
   'tns:Commandes'
);

// Register the methods available for clients
$s->register('get_CAmensuel', array(), array('produits'=>'tns:TableauCA'), $ns);
$s->register('get_orders', array(), array('commandes'=>'tns:TableauCommandes'), $ns);
$s->register('get_lastOrderClients');
$s->register('get_Order', array(), array('commandes'=>'tns:TableauCommandes'),$ns);

/*----------------------------------------------
* renvoie un tableau avec le CA mensuel realise
-----------------------------------------------*/
function get_CAmensuel() {

//on se connecte
	if (!($connexion = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD)))   return new soap_fault("Server", "MySQL 1", "connexion impossible");
	if (!($db = mysql_select_db(DB_DATABASE, $connexion)))  return new soap_fault("Server", "MySQL 2", mysql_error());

//la requ�te
	$sql = "SELECT sum(v.prixu) as value, MONTH(c.datefact) as mois, YEAR(c.datefact) as annee";
	$sql .= " FROM venteprod as v";
	$sql .= " JOIN commande as c ON c.id = v.commande";
	$sql .= " WHERE c.statut = '4' ";
//AND YEAR(o.date_purchased) = YEAR(now()) ";
	$sql .= " GROUP BY annee, mois ORDER BY annee desc ,mois desc limit 1,12";

	if (!($resquer = mysql_query($sql,$connexion)))  return new soap_fault("Server", "MySQL 3 ".$sql, mysql_error());

		switch ($numrows = mysql_numrows($resquer)) {
		case 0 :
			return new soap_fault("Server", "MySQL 4", $sql);
			break;
		default :
			$i = 0;
			while ( $i < $numrows)  {
				$result[$i] =  mysql_fetch_array($resquer, MYSQL_ASSOC);
				$i++;
			}

			break;
		}
	mysql_close($connexion);
 /* Sends the results to the client */
return $result;
}

/*
* renvoie un tableau avec les dernières commandes
*/
function get_orders($limit='', $status='') {

//on se connecte
	if (!($connexion = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD)))   return new soap_fault("Server", "MySQL 1", "connexion impossible");
	if (!($db = mysql_select_db(DB_DATABASE, $connexion)))  return new soap_fault("Server", "MySQL 2", mysql_error());

//on recherche//
$sql = "SELECT c.id, c.ref, c.date, c.paiement, cl.id as id_client, cl.ref as ref_client, cl.nom, cl.prenom";
$sql .= " FROM commande as c ";
$sql .= "JOIN client as cl ON cl.id=c.client ";
if ($status > 0) $sql .=  " WHERE c.statut = ".$status;
$sql .= " ORDER BY c.date desc";

if ($limit > 0) $sql .= " LIMIT ".$limit;

	if (!($resquer = mysql_query($sql,$connexion)))  return new soap_fault("Server", "MySQL 3 ".$sql, mysql_error());
	$result ='';

		switch ($numrows = mysql_numrows($resquer)) {
		case 0 :
			//return new soap_fault("Server", "MySQL 4", "produit inexistant");
			break;
		default :
			$i = 0;
			while ( $i < $numrows)  {
            $result[$i] =  mysql_fetch_array($resquer, MYSQL_ASSOC);
            // Calcul du total de la commande
            $sql_tot = "SELECT sum(quantite*prixu) as total FROM venteprod WHERE commande='".$result[$i]["id"]."'";
            if ($status > 0) $sqltot .=  " WHERE c.statut = ".$status;
            $sql_tot .= " GROUP BY commande";
            $restot = mysql_query($sql_tot,$connexion);
            $xrestot = mysql_fetch_array($restot,MYSQL_ASSOC);
            // total dans le tableau de réponse
            $result[$i]["total"] = $xrestot["total"];
				$i++;
			}
			break;

		}
	mysql_close($connexion);

 /* Sends the results to the client */
return $result;
}


function get_lastOrderClients($id='',$name='',$limit='') {

//on se connecte
	if (!($connexion = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD)))   return new soap_fault("Server", "MySQL 1", "connexion impossible");
	if (!($db = mysql_select_db(DB_DATABASE, $connexion)))  return new soap_fault("Server", "MySQL 2", mysql_error());

//on recherche
	$sql = "SELECT o.orders_id, o.customers_name, o.delivery_country, o.date_purchased, t.value, s.orders_status_name as statut, o.payment_method ";
	$sql .= " FROM orders_total as t JOIN orders as o on o.orders_id = t.orders_id ";
	$sql .= " JOIN orders_status as s on o.orders_status = s.orders_status_id and s.language_id = 1";
	$sql .= " WHERE t.class = 'ot_subtotal' and o.orders_status < 5 order by o.date_purchased desc";

	if ($limit > 0) $sql .= " LIMIT ".$limit;

	if (!($resquer = mysql_query($sql,$connexion)))  return new soap_fault("Server", "MySQL 3 ".$sql, mysql_error());
	$result ='';

		switch ($numrows = mysql_numrows($resquer)) {
		case 0 :
			return new soap_fault("Server", "MySQL 4", "produit inexistant");
			break;
		default :
			$i = 0;
			while ( $i < $numrows)  {
				$result[$i] =  mysql_fetch_array($resquer, MYSQL_ASSOC);
				$i++;
			}
			break;
		}
	mysql_close($connexion);
 /* Sends the results to the client */
return $result;
}

//renvoie la commande $id ou toute la liste des commandes si $id = 0

function get_Order($orderid="0",$limit='')
{

//on se connecte
	if (!($connexion = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD)))   return new soap_fault("Server", "MySQL 1", "connexion impossible");
	if (!($db = mysql_select_db(DB_DATABASE, $connexion)))  return new soap_fault("Server", "MySQL 2", mysql_error());

//on recherche la commande
/*$sql = "SELECT o.orders_id, o.customers_name, o.customers_id, o.date_purchased, t.value, o.payment_method ";
$sql .= " FROM orders_total as t JOIN orders as o on o.orders_id = t.orders_id ";
$sql .= " WHERE t.class = 'ot_subtotal'";
*/
// o.orders_id, o.customers_name, o.customers_id, o.date_purchased, o.payment_method, t.value as total, sum(p.value) as port, s.orders_status_name as statut
$sql = "SELECT c.id, c.ref, c.date, c.transaction, c.port, c.livraison as bl, c.remise, c.colis, c.paiement, c.statut, cl.id as client_id, cl.ref as client_ref, cl.nom, cl.prenom";
$sql .= " FROM commande as c ";
$sql .= " JOIN client as cl ON cl.id=c.client ";
$sql .= " WHERE c.statut < 5 "; // élimine les commandes annulées
if ($orderid > 0) $sql .=  " AND c.id = '".$orderid."'";
$sql .= " ORDER BY c.date desc";
//echo $sql;
	if (!($resquer = mysql_query($sql,$connexion)))  return new soap_fault("Server", "MySQL 3 ".$sql, mysql_error());
	$result ='';

		switch ($numrows = mysql_numrows($resquer)) {
		case 0 :
			return new soap_fault("Server", "MySQL 4", "commande inexistante ".$sql);
			break;
		default :
			$i = 0;
			while ( $i < $numrows)  {
				$result[$i] =  mysql_fetch_array($resquer, MYSQL_ASSOC);
            // Calcul du total de la commande
            $sql_tot = "SELECT sum(quantite*prixu) as total FROM venteprod WHERE commande='".$result[$i]["id"]."'";
            if ($status > 0) $sqltot .=  " WHERE c.statut = ".$status;
            $sql_tot .= " GROUP BY commande";
            $restot = mysql_query($sql_tot,$connexion);
            $xrestot = mysql_fetch_array($restot,MYSQL_ASSOC);
            // total dans le tableau de réponse
            $result[$i]["total"] = $xrestot["total"];
				$i++;
			}
			break;
		}
		$j = $i--;

      if ($orderid > 0)
      {
         //on recherche les lignes de la commande
         $sql = "SELECT l.id as lid, l.ref as prod_ref, l.titre as prod_titre, l.prixu as prod_prixu, l.tva as prod_tva, l.quantite as prod_quantite, p.id as prod_id ";
         $sql .= " FROM venteprod l ";
         $sql .= " JOIN produit as p ON l.ref=p.ref";
         $sql .= " WHERE l.commande = ".$orderid;


         if (!($resquer = mysql_query($sql,$connexion)))  return new soap_fault("Server", "MySQL 4 ".$sql, mysql_error());

            switch ($numrows = mysql_numrows($resquer)) {
            case 0 :
               return new soap_fault("Server", "MySQL 5", "commande sans articles");
               break;
            default :

               while ( $i < $numrows)  {
                  $result[$j + $i] =  mysql_fetch_array($resquer, MYSQL_ASSOC);
                  $i++;
               }
               break;
            }
      }
      mysql_close($connexion);
      /* Sends the results to the client */
      return $result;
}


// Return the results.
$s->service($HTTP_RAW_POST_DATA);

?>
