<?php
/* Copyright (C) 2009 Marc STUDER  <dev@garstud.com>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
    	\file       htdocs/speedfinder/livesearch.php
		\ingroup    speedfinder
		\brief      Speedfinder AJAX SQL result constructor page
		\version    $Id: livesearch.php,v 1.3 2010/04/25 14:34:46 eldy Exp $
		\author		Marc STUDER
*/


require("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");

$langs->load("speedfinder");

//get the q parameter from URL
$q=$_GET["q"];

//DEBUG
//echo $q;

$NBCAR_MINI=empty($conf->global->SFINDER_NBCAR)?2:$conf->global->SFINDER_NBCAR;
$SEPAR_PHONE=empty($conf->global->SFINDER_FSEPAR)?' / ':$conf->global->SFINDER_FSEPAR;

//lookup all links from the db if length of q>nn
if (strlen($q) >= $NBCAR_MINI) {
	$hint="";
	$q = strtolower($q);
	$sql_search = 'SELECT SOC.rowid as SOCid, SOC.nom, SOC.tel, CTC.rowid as CTCid, CTC.name, CTC.firstname, CTC.phone, CTC.phone_perso, CTC.phone_mobile '.
	    'FROM '.MAIN_DB_PREFIX.'societe SOC,'.MAIN_DB_PREFIX.'socpeople CTC '.
	    'WHERE CTC.fk_soc=SOC.rowid AND (SOC.nom like "%'.$q.'%" OR CTC.name like "%'.$q.'%" OR SOC.tel like "%'.$q.'%" OR CTC.phone like "%'.$q.'%" OR CTC.phone_perso like "%'.$q.'%" OR CTC.phone_mobile like "%'.$q.'%")';


//DEBUG
//$hint=$hint . "<br />".DOL_DOCUMENT_ROOT."<br />".$sql_search;
//echo $hint;

	$resql=$db->query($sql_search);
	if ($resql) {
		$i = 0;
		$retraitContactassoc = "";
		$num_rows=$db->num_rows($resql);
		while ($i < $num_rows)
		{
			$obj=$db->fetch_object($resql);

			// obligation de faire un (if !("===false")) =si pas(pas trouvé)), car le strrpos peut renvoyer 0 si trouvé en position de debut
			if ( !(strrpos($obj->tel, $q) === false) || !(strrpos(strtolower($obj->nom), $q) === false) ) {
				$hint=$hint . "<a href='../soc.php?socid=".$obj->SOCid."' target='_top'>".$obj->nom." ".$obj->tel."</a>  (".$obj->name." ".$obj->firstname.")<br />";
				$retraitContactassoc = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|---";
			}

			//TODO : les separateurs de contact ne sont pas aussi bien gérés que les séparateurs de société
			// soluce : saisir les num de contacts sans separateur !!!
			$currPhone = "";
			if ( !(strrpos($obj->phone, $q) === false) )
				$currPhone .= $SEPAR_PHONE.$obj->phone;
			if ( !(strrpos($obj->phone_perso, $q) === false) )
				$currPhone .= $SEPAR_PHONE.$obj->phone_perso;
			if ( !(strrpos($obj->phone_mobile, $q) === false) )
				$currPhone .= $SEPAR_PHONE.$obj->phone_mobile;
			if ( !(strrpos(strtolower($obj->name), $q) === false) ) {
				if ( !empty($obj->phone) ) $currPhone .= $SEPAR_PHONE.$obj->phone;
				if ( !empty($obj->phone_perso) ) $currPhone .= $SEPAR_PHONE.$obj->phone_perso;
				if ( !empty($obj->phone_mobile) ) $currPhone .= $SEPAR_PHONE.$obj->phone_mobile;
				//si aucun tel pour ce contact, on force l'affichage du contact quand meme
				if ( empty($currPhone) ) $currPhone .= ".";
			}
			if (!empty($currPhone)) {
				$contactSoc = "";
				if (empty($retraitContactassoc)) $contactSoc = $obj->nom." : ";
				$hint=$hint.$retraitContactassoc."<a href='../contact/fiche.php?id=".$obj->CTCid."' target='_top'>".$contactSoc.$obj->name." ".$obj->firstname.$currPhone."</a><br />";

			}
			$retraitContactassoc = "";
			$i++;
		}
	}
	else
	{
		dol_print_error($db);
	}

	// Set output to "no suggestion" if no hint were found or to the correct values
	if ($hint == "") {
		$response=$langs->trans("SFinderEmpty");
	} else {
		$response=$hint;
	}

	//output the response
	echo $response;
}
?>
