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

$service = new service_locker($db) ;
$fields = $service->getfkDisplayedFields() ;
$id_entrepot = $_GET["id_entrepot"] ;
$id_product = $_GET["id_product"] ;

	// print '<option>'.$id_product.$i_entrepot.'</option>'."\n" ;

// requ?te SQL
$sql = "SELECT rowid, code FROM ".MAIN_DB_PREFIX."locker " ;
$sql .= " WHERE fk_entrepot = ".$db->escape($id_entrepot) ;

$all = $db->query($sql) ;
if($all){
	
	// for each entrepot 
	print '<option value="NULL">'.$langs->trans('None').'</option>' ;
	while($item = $db->fetch_object($all))
	{
		
		$sep = "" ;
		
		print '<option value="'.$item->rowid.'">' ;
			print $sep.formatValue("string",$item->code) ;
		print '</option>'."\n" ;
		
		$selected = "" ;
	}
	
}

?>
