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

$service = new service_entrepot($db) ;
$fields = $service->getfkDisplayedFields() ;
$search = $_GET["search"] ;

// Generate the where clause
$where = '';
if($search!=''){
	$or = '' ;
	foreach ($fields as $att) {
		if(($att["type"]!='boolean')&&($att["type"]!='datetime')&&($att["type"]!='date')){
			$where.=' '.$or.$att["attribute"].' LIKE \'%'.$search.'%\'' ;
			$or = 'OR ' ;	
		}
		$fieldnames[] = $att["attribute"];
	} 
}
				
$all = dao_entrepot::select($db,$fieldnames,$where,'') ;

if($all){
	
	// for each entrepot 
	$selected = " SELECTED ";
	while($item = $db->fetch_object($all))
	{
		
		$sep = "" ;
		
		print '<option value="'.$item->rowid.'"'.$selected.'>' ;
		// for each attribute of entrepot 
		foreach ($fields as $att) {
			print $sep.formatValue($att["type"],$item->$att["attribute"]) ;
			$sep = " " ;
		}
		print '</option>'."\n" ;
		
		$selected = "" ;
	}
}


?>
