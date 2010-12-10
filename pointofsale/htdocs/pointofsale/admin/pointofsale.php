<?php
/* Copyright (C) 2010 	   Denis Martin  <denimartin@hotmail.fr>
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
 *      \file       htdocs/pointofsale/admin/pointofsale.php
 *		\ingroup    pointofsale
 *		\brief      Page to setup pointofsale module
 *		\version    $Id: pointofsale.php,v 1.2 2010/12/10 10:23:07 denismartin Exp $
 */


require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/compta/bank/class/account.class.php") ;
require_once(DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php") ;
require_once(DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php") ;
//require_once(DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php');

$langs->load("admin");
$langs->load("bills");
$langs->load("@pointofsale") ;
$langs->load("other");

if (!$user->admin)
accessforbidden();

$typeconst=array('yesno','texte','chaine');

/**
 * Function that return a select input to select the template from files existing
 * in pointofsale/tpl folder.
 * @param $zone zone of the screen. Can be 'basket', 'controls', 'customer', 'title' or 'products'
 * @return string HTML string to display select input
 */
function select_template($zone) {
	$str = '<select id="" class="flat" name="selecttpl'.$zone.'">'."\n" ;
	
	
	$str.= "</select>\n" ;
}

/*
 * Action
 */
if($_POST['action'] == 'changecashaccount') {
	if($_POST['cashaccount'] && $_POST['cashaccount'] != -1) dolibarr_set_const($db, "POS_CASH_ACCOUNT", $_POST['cashaccount']) ;
}
elseif($_POST['action'] == 'changecheckaccount') {
	if($_POST['checkaccount'] && $_POST['checkaccount'] != -1) dolibarr_set_const($db, "POS_CHECK_ACCOUNT", $_POST['checkaccount']) ;
}
elseif($_POST['action'] == 'changecardaccount') {
	if($_POST['cardaccount'] && $_POST['cardaccount'] != -1) dolibarr_set_const($db, "POS_CARD_ACCOUNT", $_POST['cardaccount']) ;
}
elseif($_POST['action'] == 'changegenericthird') {
	if($_POST['genericthird'] && $_POST['genericthird'] != -1) dolibarr_set_const($db, "POS_GENERIC_THIRD", $_POST['genericthird']) ;
}
elseif($_POST['action'] == 'changerootcategory') {
	if($_POST['rootcategory'] && $_POST['rootcategory'] != -1) dolibarr_set_const($db, "POS_ROOT_CATEGORY", $_POST['rootcategory']) ;
}

if ($_POST["action"] == 'updateMask')
{
	$maskconstinvoice=$_POST['maskconstinvoice'];
	//$maskconstcredit=$_POST['maskconstcredit'];
	$maskinvoice=$_POST['maskinvoice'];
	//$maskcredit=$_POST['maskcredit'];
	if ($maskconstinvoice) dolibarr_set_const($db,$maskconstinvoice,$maskinvoice,'chaine',0,'',$conf->entity);
	//if ($maskconstcredit)  dolibarr_set_const($db,$maskconstcredit,$maskcredit,'chaine',0,'',$conf->entity);
}

if ($_GET["action"] == 'setmod')
{
	// TODO Verifier si module numerotation choisi peut etre active
	// par appel methode canBeActivated

	dolibarr_set_const($db, "POS_FACTURE_ADDON",$_GET["value"],'chaine',0,'',$conf->entity);
}

if ($_GET["action"] == 'specimen')
{
	$modele=$_GET["module"];

	$facture = new Facture($db);
	$facture->initAsSpecimen();

	// Charge le modele
	$dir = DOL_DOCUMENT_ROOT . "/includes/modules/facture/";
	$file = "pdf_".$modele.".modules.php";
	if (file_exists($dir.$file))
	{
		$classname = "pdf_".$modele;
		require_once($dir.$file);

		$obj = new $classname($db);

		if ($obj->write_file($facture,$langs) > 0)
		{
			header("Location: ".DOL_URL_ROOT."/document.php?modulepart=facture&file=SPECIMEN.pdf");
			return;
		}
		else
		{
			$mesg='<div class="error">'.$obj->error.'</div>';
			dol_syslog($obj->error, LOG_ERR);
		}
	}
	else
	{
		$mesg='<div class="error">'.$langs->trans("ErrorModuleNotFound").'</div>';
		dol_syslog($langs->trans("ErrorModuleNotFound"), LOG_ERR);
	}
}

if ($_GET["action"] == 'set')
{
	$type='invoice';
	$sql = "INSERT INTO ".MAIN_DB_PREFIX."document_model (nom, type, entity) VALUES ('".$_GET["value"]."','".$type."',".$conf->entity.")";
	if ($db->query($sql))
	{

	}
}

if ($_GET["action"] == 'del')
{
	$type='invoice';
	$sql = "DELETE FROM ".MAIN_DB_PREFIX."document_model";
	$sql.= " WHERE nom = '".$_GET["value"]."'";
	$sql.= " AND type = '".$type."'";
	$sql.= " AND entity = ".$conf->entity;

	if ($db->query($sql))
	{

	}
}

if ($_GET["action"] == 'setdoc')
{
	$db->begin();

	if (dolibarr_set_const($db, "POS_FACTURE_ADDON_PDF",$_GET["value"],'chaine',0,'',$conf->entity))
	{
		$conf->global->POS_FACTURE_ADDON_PDF = $_GET["value"];
	}

	// On active le modele
	$type='invoice';

	$sql_del = "DELETE FROM ".MAIN_DB_PREFIX."document_model";
	$sql_del.= " WHERE nom = '".addslashes($_GET["value"])."'";
	$sql_del.= " AND type = '".$type."'";
	$sql_del.= " AND entity = ".$conf->entity;
    dol_syslog("facture.php ".$sql_del);
	$result1=$db->query($sql_del);

	$sql = "INSERT INTO ".MAIN_DB_PREFIX."document_model (nom,type,entity) VALUES ('".addslashes($_GET["value"])."','".$type."',".$conf->entity.")";
    dol_syslog("facture.php ".$sql);
	$result2=$db->query($sql);
	if ($result1 && $result2)
	{
		$db->commit();
	}
	else
	{
		dol_syslog("facture.php ".$db->lasterror(), LOG_ERR);
		$db->rollback();
	}
}





/*
 * View
 */

llxHeader("",$langs->trans("PointOfSaleSetup"));

$html=new Form($db);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("PointOfSaleSetup"),$linkback,'setup');
print '<br>';

$h = 0;

$head[$h][0] = DOL_URL_ROOT."/pointofsale/admin/pointofsale.php";
$head[$h][1] = $langs->trans("PointOfSale");
$hselected=$h;
$h++;

dol_fiche_head($head, $hselected, $langs->trans("ModuleSetup"));

print '<br />' ;

//print_titre($langs->trans("BillsNumberingModule"));


/**
 * Comptes bancaires
 */
$var = true ;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '  <td colspan="2">'.$langs->trans("AccountsToUse")."</td>\n";
print '</tr>'."\n";



print "<tr ".$bc[$var].">";
print '<td width="60%">'.$langs->trans("AccountForCash").'</td>';

print '<td width="40%" align="right">';
print '<form method="post" action="pointofsale.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="changecashaccount">';
print $html->select_comptes($conf->global->POS_CASH_ACCOUNT,'cashaccount',0,'',1) ;
print '</select>' ;
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print "</td>\n";
print "</tr>\n";
$var=!$var ;



print "<tr ".$bc[$var].">";
print '<td width="60%">'.$langs->trans("AccountForCheck").'</td>';

print '<td width="40%" align="right">';
print '<form method="post" action="pointofsale.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="changecheckaccount">';
print $html->select_comptes($conf->global->POS_CHECK_ACCOUNT,'checkaccount',0,'',1) ;
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print "</td>\n";
print "</tr>\n";
$var=!$var ;



print "<tr ".$bc[$var].">";
print '<td width="60%">'.$langs->trans("AccountForCard").'</td>';

print '<td width="40%" align="right">';
print '<form method="post" action="pointofsale.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="changecardaccount">';
print $html->select_comptes($conf->global->POS_CARD_ACCOUNT,'cardaccount',0,'',1) ;
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print "</td>\n";
print "</tr>\n";
$var=!$var ;

print '</table>' ;

print '<br/>' ;


/**
 * Third party for cashdesk customer
 */
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '  <td colspan="2">'.$langs->trans("GenericThird")."</td>\n";
print '</tr>'."\n";

print "<tr ".$bc[true].">";
print '<td width="60%">'.$langs->trans("ThirdPartyToUse").'</td>';
print '<td width="40%" align="right">' ;
print '<form method="post" action="pointofsale.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="changegenericthird">';
print $html->select_societes($conf->global->POS_GENERIC_THIRD,'genericthird','',1,1);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print "</td>\n";
print "</tr>\n";
print '</table>' ;

print '<br/>' ;


/*
 * Root category for product display
 */
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '  <td colspan="2">'.$langs->trans("RootCategory")."</td>\n";
print '</tr>'."\n";

print "<tr ".$bc[true].">";
print '<td width="60%">'.$langs->trans("RootCategoryForProdDisplay").'</td>';
print '<td width="40%" align="right">' ;
print '<form method="post" action="pointofsale.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="changerootcategory">';
print $html->select_all_categories(0, $conf->global->POS_ROOT_CATEGORY,'rootcategory', 64);
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print "</td>\n";
print "</tr>\n";
print '</table>' ;

print '<br/>' ;

/*
 * Templates used for Point Of Sale module
 */
$var = true ;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '  <td colspan="2">'.$langs->trans("TemplatesConfiguration")."</td>\n";
print '</tr>'."\n";

print "<tr ".$bc[$var].">";
print '<td width="60%">'.$langs->trans("TitleTemplate").'</td>';
print '<td width="40%" align="right">' ;
print '<form method="post" action="pointofsale.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="changeproducttemplate">';

print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print "</td>\n";
print "</tr>\n";
$var = !$var ;

print "<tr ".$bc[$var].">";
print '<td width="60%">'.$langs->trans("ProductTemplate").'</td>';
print '<td width="40%" align="right">' ;
print '<form method="post" action="pointofsale.php">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="changeproducttemplate">';
print '<input type="submit" class="button" value="'.$langs->trans("Modify").'">';
print '</form>';
print "</td>\n";
print "</tr>\n";
$var = !$var ;

print '</table>' . "\n" ;
print '<br/>' ;


/*
 * Numbering modules
 */
print_titre($langs->trans("BillsNumberingModule"));

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td nowrap>'.$langs->trans("Example").'</td>';
print '<td align="center" width="60">'.$langs->trans("Status").'</td>';
print '<td align="center" width="16">'.$langs->trans("Infos").'</td>';
print '</tr>'."\n";

clearstatcache();

$var=true;
//foreach ($conf->file->dol_document_root as $dirroot)
//{

	$dir = DOL_DOCUMENT_ROOT."/pointofsale/inc/model/num/" ;
	if (is_dir($dir))
	{
		$handle = opendir($dir);
		if ($handle)
		{
			while (($file = readdir($handle))!==false)
			{
				if (! is_dir($dir.$file) || (substr($file, 0, 1) <> '.' && substr($file, 0, 3) <> 'CVS'))
				{
					$filebis = $file;
					$classname = preg_replace('/\.php$/','',$file);
					// For compatibility
					if (! is_file($dir.$filebis))
					{
						$filebis = $file."/".$file.".modules.php";
						$classname = "mod_facture_".$file;
					}
					//print "x".$dir."-".$filebis."-".$classname;
					if (! class_exists($classname) && is_readable($dir.$filebis) && (preg_match('/mod_/',$filebis) || preg_match('/mod_/',$classname)) && substr($filebis, dol_strlen($filebis)-3, 3) == 'php')
					{
						// Chargement de la classe de numerotation
						require_once($dir.$filebis);
	
						$module = new $classname($db);
	
						// Show modules according to features level
						if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) continue;
						if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) continue;
	
						if ($module->isEnabled())
						{
							$var = !$var;
							print '<tr '.$bc[$var].'><td width="100">';
							echo preg_replace('/mod_facture_/','',preg_replace('/\.php$/','',$file));
							print "</td><td>\n";
	
							print $module->info();
	
							print '</td>';
	
							// Affiche example
							print '<td nowrap="nowrap">'.$module->getExample().'</td>';
	
							print '<td align="center">';
							//print "> ".$conf->global->POS_FACTURE_ADDON." - ".$file;
							if ($conf->global->POS_FACTURE_ADDON == $file || $conf->global->POS_FACTURE_ADDON.'.php' == $file)
							{
								print img_picto($langs->trans("Activated"),'on');
							}
							else
							{
								print '<a href="'.$_SERVER["PHP_SELF"].'?action=setmod&amp;value='.preg_replace('/\.php$/','',$file).'" alt="'.$langs->trans("Default").'">'.img_picto($langs->trans("Disabled"),'off').'</a>';
							}
							print '</td>';
	
							$facture=new Facture($db);
							$facture->initAsSpecimen();
	
							// Example for standard invoice
							$htmltooltip='';
							$htmltooltip.=''.$langs->trans("Version").': <b>'.$module->getVersion().'</b><br>';
							$facture->type=0;
							$nextval=$module->getNextValue($mysoc,$facture);
							if ("$nextval" != $langs->trans("NotAvailable"))	// Keep " on nextval
							{
								$htmltooltip.=$langs->trans("NextValueForInvoices").': ';
								if ($nextval)
								{
									$htmltooltip.=$nextval.'<br>';
								}
								else
								{
									$htmltooltip.=$langs->trans($module->error).'<br>';
								}
							}
							// Example for credit invoice
							$facture->type=2;
							$nextval=$module->getNextValue($mysoc,$facture);
							if ("$nextval" != $langs->trans("NotAvailable"))	// Keep " on nextval
							{
								$htmltooltip.=$langs->trans("NextValueForCreditNotes").': ';
								if ($nextval)
								{
									$htmltooltip.=$nextval;
								}
								else
								{
									$htmltooltip.=$langs->trans($module->error);
								}
							}
	
							print '<td align="center">';
							print $html->textwithpicto('',$htmltooltip,1,0);
							print '</td>';
	
							print "</tr>\n";
						}
					}
				}
			}
			closedir($handle);
		}
	}
//}
print '</table>';


/*
 * Document templates generators
 */
print '<br>';
print_titre($langs->trans("BillsPDFModules"));

// Load array def with activated templates
$def = array();
$sql = "SELECT nom";
$sql.= " FROM ".MAIN_DB_PREFIX."document_model";
$sql.= " WHERE type = 'invoice'";
$sql.= " AND entity = ".$conf->entity;
$resql=$db->query($sql);
if ($resql)
{
	$i = 0;
	$num_rows=$db->num_rows($resql);
	while ($i < $num_rows)
	{
		$array = $db->fetch_array($resql);
		array_push($def, $array[0]);
		$i++;
	}
}
else
{
	dol_print_error($db);
}

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td align="center" width="60">'.$langs->trans("Status").'</td>';
print '<td align="center" width="60">'.$langs->trans("Default").'</td>';
print '<td align="center" width="32" colspan="2">'.$langs->trans("Infos").'</td>';
print "</tr>\n";

clearstatcache();


$var=true;
foreach ($conf->file->dol_document_root as $dirroot)
{
	foreach (array('','/doc') as $valdir)
	{
		$dir = $dirroot . "/includes/modules/facture".$valdir;

		if (is_dir($dir))
		{
			$handle=opendir($dir);
			if ($handle)
			{
				while (($file = readdir($handle))!==false)
				{
					if (preg_match('/\.modules\.php$/i',$file) && preg_match('/^(pdf_|doc_)/',$file))
					{
						$name = substr($file, 4, dol_strlen($file) -16);
						$classname = substr($file, 0, dol_strlen($file) -12);

						require_once($dir.'/'.$file);
						$module = new $classname($db);

						$modulequalified=1;
						if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) $modulequalified=0;
						if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) $modulequalified=0;

						if ($modulequalified)
						{
							$var = !$var;
							print '<tr '.$bc[$var].'><td width="100">';
							print (empty($module->name)?$name:$module->name);
							print "</td><td>\n";
							if (method_exists($module,'info')) print $module->info($langs);
							else print $module->description;
							print '</td>';

							// Active
							if (in_array($name, $def))
							{
								print "<td align=\"center\">\n";
								if ($conf->global->POS_FACTURE_ADDON_PDF != "$name")
								{
									print '<a href="'.$_SERVER["PHP_SELF"].'?action=del&amp;value='.$name.'">';
									print img_picto($langs->trans("Enabled"),'on');
									print '</a>';
								}
								else
								{
									print img_picto($langs->trans("Enabled"),'on');
								}
								print "</td>";
							}
							else
							{
								print "<td align=\"center\">\n";
								print '<a href="'.$_SERVER["PHP_SELF"].'?action=set&amp;value='.$name.'">'.img_picto($langs->trans("Disabled"),'off').'</a>';
								print "</td>";
							}

							// Defaut
							print "<td align=\"center\">";
							if ($conf->global->POS_FACTURE_ADDON_PDF == "$name")
							{
								print img_picto($langs->trans("Default"),'on');
							}
							else
							{
								print '<a href="'.$_SERVER["PHP_SELF"].'?action=setdoc&amp;value='.$name.'" alt="'.$langs->trans("Default").'">'.img_picto($langs->trans("Disabled"),'off').'</a>';
							}
							print '</td>';

							// Info
							$htmltooltip =    ''.$langs->trans("Name").': '.$module->name;
							$htmltooltip.='<br>'.$langs->trans("Type").': '.($module->type?$module->type:$langs->trans("Unknown"));
							if ($module->type == 'pdf')
							{
								$htmltooltip.='<br>'.$langs->trans("Height").'/'.$langs->trans("Width").': '.$module->page_hauteur.'/'.$module->page_largeur;
							}
							$htmltooltip.='<br><br><u>'.$langs->trans("FeaturesSupported").':</u>';
							$htmltooltip.='<br>'.$langs->trans("Logo").': '.yn($module->option_logo,1,1);
							$htmltooltip.='<br>'.$langs->trans("PaymentMode").': '.yn($module->option_modereg,1,1);
							$htmltooltip.='<br>'.$langs->trans("PaymentConditions").': '.yn($module->option_condreg,1,1);
							$htmltooltip.='<br>'.$langs->trans("Escompte").': '.yn($module->option_escompte,1,1);
							$htmltooltip.='<br>'.$langs->trans("CreditNote").': '.yn($module->option_credit_note,1,1);
							$htmltooltip.='<br>'.$langs->trans("MultiLanguage").': '.yn($module->option_multilang,1,1);
							$htmltooltip.='<br>'.$langs->trans("WatermarkOnDraftInvoices").': '.yn($module->option_draft_watermark,1,1);


							print '<td align="center">';
							print $html->textwithpicto('',$htmltooltip,1,0);
							print '</td>';

							// Preview
							print '<td align="center">';
							if ($module->type == 'pdf')
							{
								print '<a href="'.$_SERVER["PHP_SELF"].'?action=specimen&module='.$name.'">'.img_object($langs->trans("Preview"),'bill').'</a>';
							}
							else
							{
								print img_object($langs->trans("PreviewNotAvailable"),'generic');
							}
							print '</td>';

							print "</tr>\n";
						}
					}
				}
				closedir($handle);
			}
		}
	}
}
print '</table>';




dol_fiche_end();

$db->close();

llxFooter('$Date: 2010/12/10 10:23:07 $ - $Revision: 1.2 $');
?>
