<?php
/* Copyright (C) 2009 Marc STUDER  <dev@garstud.com>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
    	\file       htdocs/speedfinder/index.php
		\ingroup    speedfinder
		\brief      Main speedfinder area page
		\version    $Id: index.php,v 1.3 2010/05/26 11:46:15 eldy Exp $
		\author		Marc STUDER
*/

include("./pre.inc.php");

// Load traductions files
$langs->load("speedfinder");

// Load permissions
//$user->getrights('speedfinder'); //TODO ???

// Get parameters
//$socid = isset($_GET["socid"])?$_GET["socid"]:'';

// Protection quand utilisateur externe
/*if ($user->societe_id > 0) {
    $action = '';
    $socid = $user->societe_id;
}*/

$CURRHEIGHT=empty($conf->global->SFINDER_HEIGHT)?'600':$conf->global->SFINDER_HEIGHT;
$CURRWIDTH=empty($conf->global->SFINDER_WIDTH)?'500':$conf->global->SFINDER_WIDTH;
$CURRFSCROLL=empty($conf->global->SFINDER_FSCROLL)?'AUTO':$conf->global->SFINDER_FSCROLL;
$POPUPLINKACTIV=empty($conf->global->SFINDER_POPUP)?0:$conf->global->SFINDER_POPUP;
$POPUPWIDTH=empty($conf->global->SFINDER_POPUPW)?'360':$conf->global->SFINDER_POPUPW;
$POPUPHEIGHT=empty($conf->global->SFINDER_POPUPH)?'520':$conf->global->SFINDER_POPUPH;

/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/


/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/

llxHeader("",$langs->trans("SFinderSubTitle"), $langs->trans("SFinderSubTitle"));

print_fiche_titre($langs->trans("SpeedFinder"));

$frame = $langs->trans("SFinderSubTitleHelp");
$frame .= "<script language = 'JavaScript'>var sfPopup; function openPopup() {sfPopup = window.open('formsearch_popup.php','nameofwindow','toolbar=no,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=yes,width=".$POPUPWIDTH.",height=".$POPUPHEIGHT."'); sfPopup.document.close();  sfPopup.focus();}</script>";

if($POPUPLINKACTIV)
	$frame .= ' (<a href="javascript:openPopup()">popup <img src="../theme/eldy/img/object_commercial.png" title="'.$langs->trans("SpeedFinder").' popup" border="0"></a>)';

$frame .= '<br /><br /><form>';
$frame .= '<table class="noborder" width="100%">';
$frame .= "<tr class=\"liste_titre\">";
$frame .= '<td colspan="3">'.$langs->trans("SFinderSearch").'</td></tr>';
$frame .= "<tr $bc[0] width='150' valign='top' style='line-height:40px'><td>";
$frame .= $langs->trans("SFinderSearchLabel").':</td><td>';
$frame .='<iframe src="formsearch.php" style=" border-width:0 " width="'.$CURRWIDTH.'" height="'.$CURRHEIGHT.'" frameborder="1" scrolling="'.$CURRFSCROLL.'">';
$frame.='</iframe>';
$frame .= "</td></tr></table></form><br>";

print $frame;

// End of page
$db->close();

llxFooter('$Date: 2010/05/26 11:46:15 $ - $Revision: 1.3 $');
?>
