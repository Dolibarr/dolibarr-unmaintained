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
 
function ajaxFilter(file,input,output){
	new Ajax.Request(file, {
		method: 'get',
		parameters: { search: $F(input) },
		onSuccess: function(transport) { $(output).update(transport.responseText);  }
	});
	
}

function ajaxSelect(file,params,output){
	new Ajax.Request(file, {
		method: 'get',
		parameters: params,
		onSuccess: function(transport) {$(output).update(transport.responseText);}
	});
	
}

/**
* Return true if source == target, false in other cases
*/
function compareEntrepots(){
	if( $('id_entrepot_destination').value == $('id_entrepot_source').value ){
		$('div_nbpieces').className = 'hidden' ;
		$('div_lockers').className = 'visible' ;
		return true ;
	}else{
		$('div_nbpieces').className = 'visible' ;
		$('div_lockers').className = 'hidden' ;
		return false ;
	}
}

function entrepot_source_change(id){

	compareEntrepots() ;

	// La source est toujours limit?e aux casiers de base
	ajaxSelect(
		'ajax/locker_by_entrepot_id.php',
		'id_entrepot='+$('id_entrepot_source').value+'&id_product='+id,
		'id_locker_source'
	);
	
}

function entrepot_destination_change(id){

	var equal = compareEntrepots() ;

	var page ;
	if(equal){
		page = 'ajax/locker_by_entrepot_id_all.php' ;
	} else {
		page = 'ajax/locker_by_entrepot_id.php' ;
	}
	// on peut transf?rer vers n'importe o?
	ajaxSelect(
		page,
		'id_entrepot='+$('id_entrepot_destination').value+'&id_product='+id,
		'id_locker_destination'
	);
	
}
