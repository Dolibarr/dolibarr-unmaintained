<?php
/* Copyright (C) 2009 Marc STUDER  <dev@garstud.com>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
	    \file       htdocs/admin/speedfinder.php
        \ingroup    google
        \brief      Setup page for speedfinder module
		\version    $Id: speedfinder.php,v 1.1 2009/09/22 14:09:40 hregis Exp $
*/

$res=@include("./pre.inc.php");
if (! $res) include("../../../dolibarr/htdocs/admin/pre.inc.php");	// Used on dev env only

require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/html.formadmin.class.php');


if (!$user->admin)
    accessforbidden();

$langs->load("speedfinder");
$langs->load("admin");
$langs->load("other");

$def = array();
$actiontest=$_POST["test"];
$actionsave=$_POST["save"];

if (empty($conf->global->SFINDER_HEIGHT)) $conf->global->SFINDER_HEIGHT="600";
if (empty($conf->global->SFINDER_FSCROLL)) $conf->global->SFINDER_FSCROLL="AUTO";
if (empty($conf->global->SFINDER_POPUP)) $conf->global->SFINDER_POPUP="1";
if (empty($conf->global->SFINDER_POPUPW)) $conf->global->SFINDER_POPUPW="360";
if (empty($conf->global->SFINDER_POPUPH)) $conf->global->SFINDER_POPUPH="520";

/*
 * Actions
 */

if ($actionsave) {
	$db->begin();
	$i=1;
	$error=0;

	// Save Hauteur
	$SF_Height=trim($_POST["SFINDER_HEIGHT"]);
	if (empty($SF_Height)) $SF_Height='600';
	$res=dolibarr_set_const($db,'SFINDER_HEIGHT',$SF_Height,'chaine',0);
	if (! $res > 0) $error++;

	// Save type de scroll
	$res=dolibarr_set_const($db,'SFINDER_FSCROLL',trim($_POST["SFINDER_FSCROLL"]),'chaine',0);
	if (! $res > 0) $error++;
	if (empty($conf->global->SFINDER_FSCROLL)) $conf->global->SFINDER_FSCROLL="AUTO";

	// Save popup link
	$res=dolibarr_set_const($db,'SFINDER_POPUP',$_POST["SFINDER_POPUP"]);
	if (! $res > 0) $error++;
	if (empty($conf->global->SFINDER_POPUP)) $conf->global->SFINDER_POPUP="YES";

	// Save popup largeur
	$SF_PopupW=trim($_POST["SFINDER_POPUPW"]);
	if (empty($SF_PopupW)) $SF_PopupW='360';
	$res=dolibarr_set_const($db,'SFINDER_POPUPW',$SF_PopupW,'chaine',0);
	if (! $res > 0) $error++;

	// Save popup hauteur
	$SF_PopupH=trim($_POST["SFINDER_POPUPH"]);
	if (empty($SF_PopupH)) $SF_PopupH='520';
	$res=dolibarr_set_const($db,'SFINDER_POPUPH',$SF_PopupH,'chaine',0);
	if (! $res > 0) $error++;

	// validation de la sauvegarde ou annulation si erreur
	if (! $error) {
		$db->commit();
		$mesg = "<font class=\"ok\">".$langs->trans("SetupSaved")."</font>";
	} else {
		$db->rollback();
		$mesg = "<font class=\"error\">".$langs->trans("Error")."</font>";
	}
}


/*
 * View
 */


$formadmin=new FormAdmin($db);

//chargé depuis le pre.inc.php
llxHeader();

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("SpeedfinderSetup"),$linkback,'setup');
print '<br>';


print '<form name="speedfinderconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';

$var=false;
print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td width="300">'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";

// iframe height
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamHeight")."</td>";
print "<td>";
print '<input class="flat" type="text" size="5" name="SFINDER_HEIGHT" value="'.$conf->global->SFINDER_HEIGHT.'">';
print "</td>";
print "</tr>";

// iframe scroll
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamScroll")."</td>";
print "<td>";
print '<select class="flat" name="SFINDER_FSCROLL">';
print '<option value="AUTO"'.($conf->global->SFINDER_FSCROLL=='AUTO'?' selected="true"':'').'>automatic</option>';
print '<option value="YES"'.($conf->global->SFINDER_FSCROLL=='YES'?' selected="true"':'').'>always</option>';
print '<option value="NO"'.($conf->global->SFINDER_FSCROLL=='NO'?' selected="true"':'').'>never</option>';
print '</select>';
print "</td>";
print "</tr>";

// popup link
$var = !$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamPopup")."</td>";
print "<td>";
print '<select class="flat" name="SFINDER_POPUP">';
print '<option value="NO"'.($conf->global->SFINDER_POPUP=='NO'?' selected="true"':'').'>No</option>';
print '<option value="YES"'.($conf->global->SFINDER_POPUP=='YES'?' selected="true"':'').'>Yes</option>';
print '</select>';
print "</td>";
print "</tr>";

// popup width
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamPopupW")."</td>";
print "<td>";
print '<input class="flat" type="text" size="5" name="SFINDER_POPUPW" value="'.$conf->global->SFINDER_POPUPW.'">';
print "</td>";
print "</tr>";

// popup height
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamPopupH")."</td>";
print "<td>";
print '<input class="flat" type="text" size="5" name="SFINDER_POPUPH" value="'.$conf->global->SFINDER_POPUPH.'">';
print "</td>";
print "</tr>";



print "</table>";
print "<br>";


print '<center>';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</center>";

print "</form>\n";

if ($mesg) print "<br>$mesg<br>";
print "<br>";

// Show message
$urlgarstudhelp='<a href="http://speedfinder.garstud.com" target="_blank">http://speedfinder.garstud.com</a>';
$message=$langs->trans("SFinderSetupHelp",$urlgarstudhelp);
print info_admin($message);

$db->close();

llxFooter('$Date: 2009/09/22 14:09:40 $ - $Revision: 1.1 $');
?>
