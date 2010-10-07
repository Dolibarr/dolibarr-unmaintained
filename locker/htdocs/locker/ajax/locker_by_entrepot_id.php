<?php
//Start of user code fichier htdocs/product/stock/ajax/locker_fk_entrepot_query.php
	
	
	
/* Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 * Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
 * Copyright (C) 2008   < > 
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
require("./pre.inc.php") ;

$langs->Load("main") ;

$service = new service_locker($db) ;
$fields = $service->getfkDisplayedFields() ;
$id_entrepot = $_GET["id_entrepot"] ;
$id_product = $_GET["id_product"] ;

	// print '<option>'.$id_product.$i_entrepot.'</option>'."\n" ;

// requ?te SQL
$sql = "SELECT count(*) as nb FROM ".MAIN_DB_PREFIX."product_stock";
$sql .= " WHERE fk_product = ".$id_product." AND fk_entrepot = ".$id_entrepot." AND fk_locker IS NOT NULL " ;

$resql=$db->query($sql);
if ($resql)
{
	
	$row = $db->fetch_object($resql);
	// S'il existe d?ja un entrepot o? est stock? l'objet, on ne peut choisir qu'une seul casier dans cet entrepot
	if ($row->nb > 0)
	{
		$sql = "SELECT l.rowid, l.code FROM ".MAIN_DB_PREFIX."locker l" ;
		$sql .= " JOIN ".MAIN_DB_PREFIX."product_stock ps ON l.rowid = ps.fk_locker" ;
		$sql .= " WHERE ps.fk_entrepot = ".$db->escape($id_entrepot) ;
		$sql .= " AND ps.fk_product =".$db->escape($id_product) ;
		
		$optionNone = '' ;
		
	// Sinon on peut cr?er le stock dans n'importe lequel des casiers
	} else {
		$sql = "SELECT rowid, code FROM ".MAIN_DB_PREFIX."locker " ;
		$sql .= " WHERE fk_entrepot = ".$db->escape($id_entrepot) ;
		
		$optionNone = '<option value="NULL" selected>'.$langs->trans('None').'</option>' ;
	}

}

print $row->nb ;

$all = $db->query($sql) ;
if($all){
	
	// for each entrepot 
	$selected = " SELECTED ";
	while($item = $db->fetch_object($all))
	{
		
		$sep = "" ;
		
		print '<option value="'.$item->rowid.'"'.$selected.'>' ;
			print $sep.formatValue("string",$item->code) ;
		print '</option>'."\n" ;
		
		$selected = "" ;
	}
	print $optionNone ;
	
}

?>
