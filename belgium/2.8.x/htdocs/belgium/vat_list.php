<?php
/* Copyright (C) 2009 Laurent Léonard <laurent@open-minds.org>
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

include('../main.inc.php');

if (!$user->rights->belgium->vat_list->read)
	accessforbidden();

include(DOL_DOCUMENT_ROOT . '/belgium/class/vat_list.class.php');

$action = $_POST['action'];
$date_day = isset($_POST['dateday']) ? $_POST['dateday'] : date('d');
$date_month = isset($_POST['datemonth']) ? $_POST['datemonth'] : date('m');
$date_year = isset($_POST['dateyear']) ? $_POST['dateyear'] : date('Y');
$name_id = isset($_POST['name_id']) ? $_POST['name_id'] : 0;
$sequence_number = isset($_POST['sequence_number']) ? $_POST['sequence_number'] : 1;
$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y');

$langs->load('@belgium');

$vat_list = new VATList($db);

$error_messages = array();

try
{
	$vat_list->setPeriod($year);
}
catch (Exception $exception)
{
	$error_messages[] = $exception->getMessage();
}

try
{
	$vat_list->setDate($date_day, $date_month, $date_year);
}
catch (Exception $exception)
{
	$error_messages[] = $exception->getMessage();
}

try
{
	$vat_list->setSequenceNumber($sequence_number);
}
catch (Exception $exception)
{
	$error_messages[] = $exception->getMessage();
}

$vat_list->setVATNumber($conf->global->MAIN_INFO_TVAINTRA);

$vat_list->addProposedName($user->fullname);
$vat_list->addProposedName($conf->global->MAIN_INFO_SOCIETE_NOM);
$vat_list->selectName($name_id);
$vat_list->setStreet($conf->global->MAIN_INFO_SOCIETE_ADRESSE);
$vat_list->setZipCode($conf->global->MAIN_INFO_SOCIETE_CP);
$vat_list->setCity($conf->global->MAIN_INFO_SOCIETE_VILLE);
if ($db->query('SELECT code FROM ' . MAIN_DB_PREFIX . 'c_pays WHERE rowid = ' . $conf->global->MAIN_INFO_SOCIETE_PAYS))
{
	$country = $db->fetch_array();
	$vat_list->setCountry($country['code']);
}
else
	dolibarr_print_error($db);

$vat_list->fillClientsList();

if ($action == 'export' && count($error_messages) == 0)
{
	if (!$user->rights->belgium->vat_list->export)
		accessforbidden();
	
	header('content-type: application/xml');
	echo $vat_list->outputXML();
	exit();
}

llxHeader('', $langs->trans('Belgium') . ' - ' . $langs->trans('Intervat') . ' - ' . $langs->trans('VATClientsList'));

foreach ($error_messages as $error_message)
	echo '<div class="error">' . $error_message . '</div>';
dol_fiche_head(array(array(NULL, $langs->trans('VATClientsList'))), NULL, $langs->trans('Intervat'));
echo $vat_list->printHTML();
echo '</div>';

$db->close();

llxFooter();

?>
