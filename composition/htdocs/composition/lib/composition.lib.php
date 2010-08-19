<?php
//Start of user code



/* Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
 * Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
 * Copyright (C) 2008 Samuel Bouchet, Patrick Raguin <samuel.bouchet@auguria.net, patrick.raguin@auguria.net> 
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
        \file       htdocs/nommodule/lib.php
        \ingroup    nommodule
        \brief      *complete here*
		\version    1.1
		\author		Samuel Bouchet, Patrick Raguin
*/

function getfieldToKeyFieldArray($query){
				
	preg_match("/SELECT (.*) FROM/i",$query,$matches) ; // fields between select and FROM
	$queryfields = explode(",",$matches[1]) ; // array of fields

	// get the id field per table
	$tableidfields = array() ;
	foreach($queryfields as $field){
		// en cas d'appel de fonction en sql on n garde que le contenu
		if(strpos($field,"(")){
			$tmp = explode("(",$field) ;
			$tmp = str_replace(")","",$tmp[1]) ;
		} else {
			$tmp = $field ;
		}
		// on recupere la partie gauche du point pour avoir la table
		$tmp = explode(".",$tmp) ;
		
		$table = trim($tmp[0]) ;
		
		$tmp = preg_split("/ as /i",$tmp[1]) ;
		$fieldname = (isset($tmp[1]))? trim($tmp[1]) : trim($tmp[0]) ;
		if($tmp[0]=="rowid"){
			$tableidfields[$table] = $fieldname ;
		}
		$keys[$fieldname] = $tableidfields[$table] ;
	}
	
	return $keys ;
}

/*
 *      \brief      	display the value according to the type
 *		\param $type	type of the value
 *		\param $value	value to display
 *      \return     	the value ready to be displayed
 */
function formatValue($type,$value){
	global $langs ;

	switch($type){
		case "datetime" :
			return dolibarr_print_date($value,"dayhour") ;
			break ;
		case "date" :
			return dolibarr_print_date($value,"day") ;
			break ;
		case "time" :
			return dolibarr_print_date($value,"hour") ;
			break ;
		case "boolean" :
			return yn($value) ;
			break ;
		case is_array($type):
			return $langs->trans($type[$value]) ;// a label of a list need to be translated
			break ;
		default :
			return $value ;
			break ;
	}
}

/*
 *      \brief      			get html code according the type
 *		\param $type			type of the value
 *		\param $attribute_name	name of the attribute
 * 		\param $value			value of the attribute
 *		\param $null			0=no null, 1=null
 * 		\param $form_name		name of the form
 *      \return     			html code
 */
function getHtmlForm($type,$attribute_name,$value='',$null=0,$form_name='',$textarea_rows=3){
	
	$html = new Form($db);
	
	switch($type)
	{
		case "datetime":
			
			ob_start(); 
			$html->select_date($value,$attribute_name,'1','1',$null,$form_name,1);
			$input = ob_get_contents(); 
			ob_end_clean();				
			break;

		case "date":
			ob_start(); 
			$html->select_date($value,$attribute_name,'0','0',$null,$form_name,1);
			$input = ob_get_contents(); 
			ob_end_clean();				
			break;

		case "time":
			ob_start(); 
			$html->select_date($value,$attribute_name,'1','1',$null,$form_name,0);
			$input = ob_get_contents(); 
			ob_end_clean();				
			break;				
			
		case "boolean":
			$input = $html->selectyesno($attribute_name,$value,1);
			break;
			
		case "text":
			$input = '<textarea name='.$attribute_name.' wrap="soft" cols="70" rows="'.$textarea_rows.'">'.$value.'</textarea>';
			break ;
			
		case is_array($type):
			ob_start(); 
			$html->selectarray($attribute_name,$type,$value,$null);
			$input = ob_get_contents(); 
			ob_end_clean();		
			break ;
			
		default:
			$input = '<input type="text" size="40" maxlength="255" name='.$attribute_name.' value="'.$value.'" />';
			break;
	}	

	return $input;
	
}


?>
