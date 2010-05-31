<?php
//Start of user code fichier htdocs/nommodule/ajax/compteur_fk_groupe_query.php
	
/* Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 * Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
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

/******************************* Includes (old content of pre.inc.php) ***********************/
require("../../main.inc.php");

//Loading of the necessary localization files
// chargement des fichiers de langue nécessaires
$langs->load("product");

function llxHeader($langs, $head = "", $title="", $help_url='')
{

 	top_menu($head, $title);
	
	$menu = new Menu();
	
	left_menu($menu->liste, $help_url);
}
/******************************* /Includes ***********************/

$search = $_GET["search"] ;

// Generate the where clause
$where = '';
if($search!=''){
	$where = " WHERE nom like '%".$search."%'" ;
}

$sql = "SELECT rowid, nom";
$sql.= " FROM ".MAIN_DB_PREFIX."societe";
$sql.= $where;
$sql.= " ORDER BY nom";


$all = $db->query($sql);
if($all){
	
	
	// for each groupe 
	$selected = " SELECTED ";
	while($item = $db->fetch_object($all))
	{
		
		$sep = "" ;
		
		print '<option value="'.$item->rowid.'"'.$selected.'>' ;
		// for each attribute of groupe 

			print $item->nom ;
			$sep = " " ;
		
		print '</option>'."\n" ;
		
		$selected = "" ;
	}
}


?>