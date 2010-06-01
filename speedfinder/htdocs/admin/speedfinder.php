<?php
/* Copyright (C) 2009 Marc STUDER  <dev@garstud.com>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
	    \file       htdocs/admin/speedfinder.php
        \ingroup    google
        \brief      Setup page for speedfinder module
		\version    $Id: speedfinder.php,v 1.4 2010/06/01 14:38:50 grandoc Exp $
*/

$res=@include("./pre.inc.php");
if (! $res) include("../../../dolibarr/htdocs/admin/pre.inc.php");	// Used on dev env only

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');

if (!$user->admin)
    accessforbidden();

$langs->load("speedfinder");
$langs->load("admin");
$langs->load("other");

$def = array();
$actiontest=$_POST["test"];
$actionsave=$_POST["save"];

if (empty($conf->global->SFINDER_WIDTH)) $conf->global->SFINDER_WIDTH="500";
if (empty($conf->global->SFINDER_HEIGHT)) $conf->global->SFINDER_HEIGHT="600";
if (empty($conf->global->SFINDER_FSCROLL)) $conf->global->SFINDER_FSCROLL="AUTO";
if (empty($conf->global->SFINDER_FSEPAR)) $conf->global->SFINDER_FSEPAR=" / ";
//if (empty($conf->global->SFINDER_POPUP)) $conf->global->SFINDER_POPUP=1;
if (empty($conf->global->SFINDER_POPUPW)) $conf->global->SFINDER_POPUPW="400";
if (empty($conf->global->SFINDER_POPUPH)) $conf->global->SFINDER_POPUPH="600";
//if (empty($conf->global->SFINDER_AUTOFOCUS)) $conf->global->SFINDER_AUTOFOCUS=1;
//if (empty($conf->global->SFINDER_TXTSELECT)) $conf->global->SFINDER_TXTSELECT=1;
if (empty($conf->global->SFINDER_TXTSIZE)) $conf->global->SFINDER_TXTSIZE="20";
if (empty($conf->global->SFINDER_NBCAR)) $conf->global->SFINDER_NBCAR=2;

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

	// Save Largeur
	$SF_Width=trim($_POST["SFINDER_WIDTH"]);
	if (empty($SF_Width)) $SF_Width='500';
	$res=dolibarr_set_const($db,'SFINDER_WIDTH',$SF_Width,'chaine',0);
	if (! $res > 0) $error++;

	// Save type de scroll
	$res=dolibarr_set_const($db,'SFINDER_FSCROLL',trim($_POST["SFINDER_FSCROLL"]),'chaine',0);
	if (! $res > 0) $error++;
	if (empty($conf->global->SFINDER_FSCROLL)) $conf->global->SFINDER_FSCROLL="AUTO";

	// Save separator character
	$res=dolibarr_set_const($db,'SFINDER_FSEPAR',$_POST["SFINDER_FSEPAR"],'chaine',0);
	if (! $res > 0) $error++;
	if (empty($conf->global->SFINDER_FSEPAR)) $conf->global->SFINDER_FSEPAR=" / ";

	// Save popup link
	$SF_Popup=$_POST["SFINDER_POPUP"];
	$res=dolibarr_set_const($db,'SFINDER_POPUP',$SF_Popup,'chaine',0);
	if (! $res > 0) $error++;

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

	// Save auto focus
	$SF_AutoFocus=$_POST["SFINDER_AUTOFOCUS"];
	$res=dolibarr_set_const($db,'SFINDER_AUTOFOCUS',$SF_AutoFocus,'chaine',0);
	if (! $res > 0) $error++;

	// Save selected text
	$SF_TxtSelected=$_POST["SFINDER_TXTSELECT"];
	$res=dolibarr_set_const($db,'SFINDER_TXTSELECT',$SF_TxtSelected,'chaine',0);
	if (! $res > 0) $error++;

	// Save text box size
	$SF_TxtSize=trim($_POST["SFINDER_TXTSIZE"]);
	if (empty($SF_TxtSize)) $SF_TxtSize=12;
	$res=dolibarr_set_const($db,'SFINDER_TXTSIZE',$SF_TxtSize,'chaine',0);
	if (! $res > 0) $error++;

	// Save nb car. mini to type before activate search
	$SF_NbCar=trim($_POST["SFINDER_NBCAR"]);
	if (empty($SF_NbCar)) $SF_NbCar=0;
	$res=dolibarr_set_const($db,'SFINDER_NBCAR',$SF_NbCar,'chaine',0);
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

$html=new Form($db);
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

// iframe width
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamWidth")."</td>";
print "<td>";
print '<input class="flat" type="text" size="5" maxlength="4" name="SFINDER_WIDTH" value="'.$conf->global->SFINDER_WIDTH.'">';
print "<span style='padding:5px; font-size:10px; color:#444;'>".$langs->trans("SFinderParamWidthHelp")."</span>";
print "</td>";
print "</tr>";

// iframe height
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamHeight")."</td>";
print "<td>";
print '<input class="flat" type="text" size="5" maxlength="4" name="SFINDER_HEIGHT" value="'.$conf->global->SFINDER_HEIGHT.'">';
print "<span style='padding:5px; font-size:10px; color:#444;'>".$langs->trans("SFinderParamHeightHelp")."</span>";
print "</td>";
print "</tr>";

// iframe scroll
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamScroll")."</td>";
print "<td>";
print '<select class="flat" name="SFINDER_FSCROLL">';
print '<option value="AUTO"'.($conf->global->SFINDER_FSCROLL=='AUTO'?' selected="true"':'').'>automatic</option>';
print '<option value="YES"'.($conf->global->SFINDER_FSCROLL=='YES'?' selected="true"':'').'>always</option>';
print '<option value="NO"'.($conf->global->SFINDER_FSCROLL=='NO'?' selected="true"':'').'>never</option>';
print '</select>';
print "<span style='padding:5px; font-size:10px; color:#444;'>".$langs->trans("SFinderParamScrollHelp")."</span>";
print "</td>";
print "</tr>";

// iframe phone separator
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamPhoneSepar")."</td>";
print "<td>";
print '<input class="flat" type="text" size="5"  maxlength="5" name="SFINDER_FSEPAR" value="'.$conf->global->SFINDER_FSEPAR.'">';
print "<span style='padding:5px; font-size:10px; color:#444;'>".$langs->trans("SFinderParamPhoneSeparHelp")."</span>";
print "</td>";
print "</tr>";

// popup link
$var = !$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamPopup")."</td>";
print "<td>";
print $html->selectyesno("SFINDER_POPUP",$conf->global->SFINDER_POPUP,1);
print "<span style='padding:5px; font-size:10px; color:#444;'>".$langs->trans("SFinderParamPopupHelp")."</span>";
print "</td>";
print "</tr>";

// popup width
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamPopupW")."</td>";
print "<td>";
print '<input class="flat" type="text" size="5" maxlength="4" name="SFINDER_POPUPW" value="'.$conf->global->SFINDER_POPUPW.'">';
print "<span style='padding:5px; font-size:10px; color:#444;'>".$langs->trans("SFinderParamPopupWHelp")."</span>";
print "</td>";
print "</tr>";

// popup height
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamPopupH")."</td>";
print "<td>";
print '<input class="flat" type="text" size="5" maxlength="4" name="SFINDER_POPUPH" value="'.$conf->global->SFINDER_POPUPH.'">';
print "<span style='padding:5px; font-size:10px; color:#444;'>".$langs->trans("SFinderParamPopupHHelp")."</span>";
print "</td>";
print "</tr>";

// Auto focus on textbox when loading
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamAutoFocus")."</td>";
print "<td>";
print $html->selectyesno("SFINDER_AUTOFOCUS",$conf->global->SFINDER_AUTOFOCUS,1);
print "<span style='padding:5px; font-size:10px; color:#444;'>".$langs->trans("SFinderParamAutoFocusHelp")."</span>";
print "</td>";
print "</tr>";

// Auto focus on textbox when loading
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamSelectText")."</td>";
print "<td>";
print $html->selectyesno("SFINDER_TXTSELECT",$conf->global->SFINDER_TXTSELECT,1);
print "<span style='padding:5px; font-size:10px; color:#444;'>".$langs->trans("SFinderParamSelectTextHelp")."</span>";
print "</td>";
print "</tr>";

// taille de la zone texte
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamSizeText")."</td>";
print "<td>";
print '<input class="flat" type="text" size="3" maxlength="3" name="SFINDER_TXTSIZE" value="'.$conf->global->SFINDER_TXTSIZE.'">';
print "<span style='padding:5px; font-size:10px; color:#444;'>".$langs->trans("SFinderParamSizeTextHelp")."</span>";
print "</td>";
print "</tr>";

// nb car. mini to type before activate search
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("SFinderParamNbCar")."</td>";
print "<td>";
print '<input class="flat" type="text" size="3" maxlength="2" name="SFINDER_NBCAR" value="'.$conf->global->SFINDER_NBCAR.'">';
print "<span style='padding:5px; font-size:10px; color:#444;'>".$langs->trans("SFinderParamNbCarHelp")."</span>";
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

llxFooter('$Date: 2010/06/01 14:38:50 $ - $Revision: 1.4 $');
?>
