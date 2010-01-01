<?php
/* Copyright (C) 2003-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 \file       htdocs/oscommerce_ws/index.php
 \ingroup    oscommerce2
 \brief      Page accueil zone boutique
 \version    $Id: index.php,v 1.2 2010/01/01 19:18:35 jfefe Exp $
 */

require("./pre.inc.php");

$langs->load("thelia");
$langs->load("orders");


llxHeader("",$langs->trans("Thelia"));

print_fiche_titre($langs->trans("Thelia"));

if (! @ini_get('allow_url_fopen'))
{
	$langs->load("errors");
	print '<div class="warning">'.$langs->trans("WarningAllowUrlFopenMustBeOn").'</div><br>';
}


print '<table width="100%" class="notopnoleftnoright">';

print '<tr><td valign="top" width="40%" class="notopnoleft">';

// initialisation des webservices
set_magic_quotes_runtime(0);

//WebService Client.
require_once(NUSOAP_PATH."nusoap.php");
require_once("./includes/configure.php");

// Set the parameters to send to the WebService
$parameters = array();

// Set the WebService URL
//print THELIA_WS_URL."ws_orders.php"; exit;
$client = new nusoap_client(THELIA_WS_URL."ws_orders.php");
if ($client)
{
	$client->soap_defencoding='UTF-8';
}


/*
 /* Chiffre d'affaire
 */

print_titre($langs->trans('SalesTurnover'));

print '<table class="noborder" cellspacing="0" cellpadding="3" width="100%">';
print '<tr class="liste_titre"><td>'.$langs->trans("Year").'</td>';
print '<td>'.$langs->trans("Month").'</td>';
print '<td align="right">'.$langs->trans("Total").'</td></tr>';


//$client->setDebugLevel(9);

// Call the WebService and store its result in $result.
$result = $client->call("get_CAmensuel",$parameters );

if ($client->fault) {
	dol_print_error('',"Erreur de connexion ");
	print_r($client->faultstring);
}
elseif (!($err = $client->getError()) )
{
	$num=0;
	if ($result) $num = sizeof($result);
	$var=True;
	$i=0;

	if ($num > 0) {
		while ($i < $num)
		{
			$var=!$var;
			print "<tr $bc[$var]>";
			print '<td align="left">'.$result[$i][annee].'</td>';
			print '<td align="left">'.$result[$i][mois].'</td>';
			print '<td align="right">'.convert_price($result[$i][value]).'</td>';

			print "</tr>\n";
			$i++;
		}
	}
}
else
{
	print $client->getHTTPBody($client->response);
}


print "</table>";
print '</td><td valign="top" width="60%" class="notopnoleftnoright">';

// partie commandes
print_titre($langs->trans("Orders"));

/*
 * 5 derni�res commandes re�ues
 */

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan="4">'.$langs->trans("LastOrders").'</td></tr>';

// Call the WebService and store its result in $result.
$parameters = array("limit"=>THELIA_MAXNBCOM);
$result = $client->call("get_orders",$parameters );
if ($client->fault) {
	dol_print_error('',"Erreur de connexion ");
   print_r($client->faultstring);
}
elseif (!($err = $client->getError()) ) {
	$num=0;
	if ($result) $num = sizeof($result);
	$var=True;
	$i=0;

	if ($num > 0) {

		$num = min($num,THELIA_MAXNBCOM);
		while ($i < $num) {
			$var=!$var;
			print "<tr $bc[$var]>";
         print '<td><a href="commandes/fiche.php?orderid='.$result[$i][id].'">'.$result[$i][id].'</a></td>
         <td>'.$result[$i][ref].'</td>
         <td>'.$result[$i][nom].' '.$result[$i][prenom].'</td>
            <td>'.convert_price($result[$i][total]).'</td>
            <td>'.$result[$i][paiement].'</td></tr>';
			$i++;
		}
	}
}
else {
	print $client->getHTTPBody($client->response);
}

print "</table><br>";

/*
 * 5 derni�res commandes en attente
 */

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan="4">'.$langs->trans("OnStandBy").'</td></tr>';

$parameters = array("limit"=>THELIA_MAXNBCOM, "status"=>THELIA_ORDWAIT);
$result = $client->call("get_orders",$parameters );

if ($client->fault) {
	dol_print_error('',"Erreur webservice ".$client->faultstring);
}
elseif (!($err = $client->getError()) ) {
	$var=True;
	$i=0;
	$num=0;
	if ($result) $num = sizeof($result);
	$langs->load("orders");

	if ($num > 0) {
		$num = min($num,THELIA_MAXNBCOM);

		while ($i < $num) {
			$var=!$var;
         print "<tr $bc[$var]>";
         print '<td>'.$result[$i][id].'</td>
         <td>'.$result[$i][nom].' '.$result[$i][prenom].'</td>
         <td>'.convert_price($result[$i][total]).'</td>
         <td>'.$result[$i][paiement].'</td></tr>';
			$i++;
		}
	}
}
else {
	print $client->getHTTPBody($client->response);
}

print "</table><br>";
/*
 * Commandes � traiter
 */

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan="5">'.$langs->trans("TreatmentInProgress").'</td></tr>';
print '<tr class="liste_titre"><td>ID</td><td>ref</td><td>Client</td><td>Total</td><td>Paiement</td></tr>';

$parameters = array("limit"=>THELIA_MAXNBCOM, "status"=>THELIA_ORDPROCESS);
$result = $client->call("get_orders",$parameters );

if ($client->fault) {
	dol_print_error('',"Erreur webservice ".$client->faultstring);
}
elseif (!($err = $client->getError()) ) {
	$var=True;
	$i=0;
	$num=0;
	if ($result) $num = sizeof($result);
	$langs->load("orders");

	if ($num > 0)	{
		$num = min($num,THELIA_MAXNBCOM);

		while ($i < $num)	{
  print "<tr $bc[$var]>";
  print '<td>'.$result[$i][id].'</td><td> '.$result[$i][ref].'</td>
  <td>'.$result[$i][nom].' '.$result[$i][prenom].'</td>
  <td>'.convert_price($result[$i][total]).'</td>
  <td>'.$result[$i][paiement].'</td></tr>';
			$i++;
			$var=!$var;
		}
	}
}
else {
	print $client->getHTTPBody($client->response);
}

print "</table><br>";
print '</td></tr><tr>';

/*
 * Derniers clients qui ont command�
 */

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan="7">'.$langs->trans("LastCustomers").'</td></tr>';

$parameters = array("limit"=>THELIA_MAXNBCOM);
$result = $client->call("get_lastOrderClients",$parameters );

if ($client->fault) {
	dol_print_error('',"Erreur webservice ".$client->faultstring);
}
elseif (!($err = $client->getError()) ) {
	$var=True;
	$i=0;
	$num=0;
	if ($result) $num = sizeof($result);
	$langs->load("orders");

	if ($num > 0)	{
		$num = min($num,THELIA_MAXNBCOM);

		while ($i < $num)	{
			print "<tr $bc[$var]>";
			print "<td>".$result[$i][date_purchased]."</td><td>".$result[$i][customers_name]."</td><td>".$result[$i][delivery_country]."</td><td>".convert_price($result[$i][value])."</td><td>".$result[$i][payment_method]."</td><td>".$result[$i][orders_id]."</td><td>".$result[$i][statut]."</td></tr>";
			$i++;
			$var=!$var;
		}
		print "</table><br>";
	}
}
else {
	print $client->getHTTPBody($client->response);
}


print '</tr></table>';


llxFooter('$Date: 2010/01/01 19:18:35 $ - $Revision: 1.2 $');
?>
