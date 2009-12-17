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
 * $Id: ws_customers.php,v 1.1 2009/12/17 14:57:00 hregis Exp $
 */

set_magic_quotes_runtime(0);

// Soap Server.
require_once('./lib/nusoap.php');

require_once('./includes/configure.php');

// Create the soap Object
$s = new soap_server;
$ns='oscommerce';
$s->configureWSDL('WebServicesOSCommerceForDolibarrCustomers',$ns);
$s->wsdl->schemaTargetNamespace=$ns;


$s->wsdl->addComplexType(
'client',
'complexType',
'struct',
'all',
'',
array(
   'id'=> array('name'=>'id','type'=>'xsd:int','use'=>'optionnal'),
   'ref'=>array('name'=>'ref','type'=>'xsd:string', 'use'=>'optionnal'),
   'raison'=>array('name'=>'raison','type'=>'xsd:string', 'use'=>'optionnal'),
   'entreprise'=>array('name'=>'entreprise','type'=>'xsd:string', 'use'=>'optionnal'),
   'siret'=>array('name'=>'siret','type'=>'xsd:string', 'use'=>'optionnal'),
   'intracom'=>array('name'=>'intracom','type'=>'xsd:string', 'use'=>'optionnal'),
   'nom'=>array('name'=>'nom','type'=>'xsd:string', 'use'=>'optionnal'),
   'prenom'=>array('name'=>'prenom','type'=>'xsd:string', 'use'=>'optionnal'),
   'adresse1'=>array('name'=>'adresse1','type'=>'xsd:string', 'use'=>'optionnal'),
   'adresse2'=>array('name'=>'adresse2','type'=>'xsd:string', 'use'=>'optionnal'),
   'adresse3'=>array('name'=>'adresse3','type'=>'xsd:string', 'use'=>'optionnal'),
   'cpostal'=>array('name'=>'cpostal','type'=>'xsd:string', 'use'=>'optionnal'),
   'ville'=>array('name'=>'ville','type'=>'xsd:string', 'use'=>'optionnal'),
   'pays'=>array('name'=>'pays','type'=>'xsd:string', 'use'=>'optionnal'),
   'telfixe'=>array('name'=>'telfixe','type'=>'xsd:string', 'use'=>'optionnal'),
   'telport'=>array('name'=>'telport','type'=>'xsd:string', 'use'=>'optionnal'),
   'email'=>array('name'=>'email','type'=>'xsd:string', 'use'=>'optionnal'),
   'type'=>array('name'=>'type','type'=>'xsd:string', 'use'=>'optionnal'),
   'pourcentage'=>array('name'=>'pourcentage','type'=>'xsd:string', 'use'=>'optionnal'),
   'lang'=>array('name'=>'lang','type'=>'xsd:string', 'use'=>'optionnal')
)
);

$s->wsdl->addComplexType(
   'clients',
   'complexType',
   'array',
   '',
   'SOAP-ENC:Array',
   array(),
   array(
      array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:client[]')
   ),
   'tns:client'
); 


// Register the methods available for clients
$s->register('get_Client', array('custid'=>'xsd:int'), array('clients'=>'tns:clients'),$ns);

// m�thodes
function get_Client($custid='') {

	//on se connecte
	if (!($connexion = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD)))   return new soap_fault("Server", "MySQL 1", "connexion impossible");
	if (!($db = mysql_select_db(DB_DATABASE, $connexion)))  return new soap_fault("Server", "MySQL 2", mysql_error());

	//la requ�te
	$sql = "SELECT c.id, c.ref, c.raison, c.siret, c.intracom, c.nom, c.prenom, c.adresse1, c.adresse2, c.adresse3, c.cpostal, c.ville, c.pays, c.telfixe, c.telport, 
   c.email, c.type, c.pourcentage, c.lang, p.titre as pays";
	$sql .= " from client c";
   $sql .= " JOIN pays ON pays.id=c.pays ";
   $sql .= " JOIN paysdesc as p ON p.pays = pays.id ";
   if ($custid > 0)  $sql .= " WHERE c.id = '".$custid."'";
   $sql .= " GROUP BY c.ref";   
   if(!($custid > 0))  $sql .= " ORDER BY c.id DESC";
   

	if (!($resquer = mysql_query($sql,$connexion)))  return new soap_fault("Server", "MySQL 3 ".$sql, mysql_error());

	//	$result[$i] = $numrows." lignes trouv�es ".$sql;
	switch ($numrows = mysql_numrows($resquer)) {
		case 0 :
			return new soap_fault("Server", "MySQL 4", "client inexistant ".$sql);
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

// Return the results.
$s->service($HTTP_RAW_POST_DATA);

?>
