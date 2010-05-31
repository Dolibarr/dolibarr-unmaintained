<?php
/* Copyright (C) 2008 Patrick Raguin  <patrick.raguin@auguria.net>
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
 * or see http://www.gnu.org/
 *
 * $Id: book.lib.php,v 1.1 2010/05/31 15:28:24 pit Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/book/htdocs/book/lib/book.lib.php,v $
 */

/**
	    \file       htdocs/lib/book.lib.php
	    \ingroup    modBook
		\brief      Ensemble de fonctions de base pour le module book
		\version    $Revision: 1.1 $
		\author		Samuel Bouchet, Patrick Raguin, Pierre Morin
*/

/**
*      \brief      get Format label
*      \param      id        format ID
*      \param      format    array of fields
*	   \return	   return label
*/
function getLabelFormatFromKey($db,$id,$format='')
{
	$sql.= 'SELECT '.(($format=='')?'code':implode(',',$format));
	$sql.= ' FROM '.MAIN_DB_PREFIX.'c_paper_format';
	$sql.= ' WHERE rowid = '.$id;
	
	
	dolibarr_syslog('getLabelFormatFromKey sql='.$sql,LOG_DEBUG);
    $resql=$db->query($sql);
    if ($resql)
    {
		$row = $db->fetch_row($resql);

		return implode(' ',$row);
    }
	return '';
	
}


function getfieldToKeyFieldArray($query){
				
	preg_match("/SELECT (.*) FROM/i",$query,$matches) ; // fields between select and FROM
	$queryfields = explode(",",$matches[1]) ; // array of fields

	// get the id field per table
	$tableidfields = array() ;
	foreach($queryfields as $field){
		// en cas d'appel de fonction en sql on ne garde que le contenu
		if(strpos($field,"(") !== false){
			$tmp = explode("(",$field) ;
			$tmp = str_replace(")","",$tmp[1]) ;
		} else {
			$tmp = $field ;
		}
		// on récupère la partie gauche du point pour avoir la table
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
			return dolibarr_print_date(strtotime($value),"dayhour") ;
			break ;
		case "date" :
			return dolibarr_print_date(strtotime($value),"day") ;
			break ;
		case "time" :
			return dolibarr_print_date(strtotime($value),"hour") ;
			break ;
		case "boolean" :
			return yn($value) ;
			break ;
		case "file" :

			return img_pdf($value,2);
			break;
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
function getHtmlForm($db,$type,$attribute_name,$value='',$null=0,$form_name='',$size=40, $textarea_rows=3){
	
	global $langs;
	
	$html = new Formbook($db);
	$html_product = new FormProduct($db);

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

		case "isbn":
			$input = '<input name="'.$attribute_name.'a" size="3" maxlength="5" value="'.$value['book_isbna'].'">';
    		$input.= ' - <input name="'.$attribute_name.'b" size="8" maxlength="7" value="'.$value['book_isbnb'].'">';
    		$input.= ' - <input name="'.$attribute_name.'c" size="13" maxlength="12" value="'.$value['book_isbnc'].'">';
    		$input.= ' - <input name="'.$attribute_name.'d" size="1" maxlength="1" value="'.$value['book_isbnd'].'">';

			break;	
			
		case "price_base_type" :
			ob_start(); 
			$html->select_PriceBaseType($value,$attribute_name);
			$input = ob_get_contents(); 
			ob_end_clean();				
			break;				

		case "vatrate" :
			ob_start(); 
			if($value == '') $value = '5.5';
			$html->select_tva($attribute_name,$value);
			$input = ob_get_contents(); 
			ob_end_clean();				
			break;	
		
		case "format" :
			ob_start();
			$html->select_paper_format($value,$attribute_name,array('label')); 
			$input = ob_get_contents(); 
			ob_end_clean();				
			break;	
							
		case "status" :
			
			ob_start(); 
			$statutarray=array('1' => $langs->trans("OnSell"), '0' => $langs->trans("NotOnSell"));
			$html->select_array($attribute_name,$statutarray,$value);	
			$input = ob_get_contents(); 
			ob_end_clean();			
			break;		
			
		case "weight_units" :
			if($value == '')$value = -3;
			ob_start(); 
			$html_product->select_measuring_units($attribute_name,"weight",$value);
			$input = ob_get_contents(); 
			ob_end_clean();			
			break;		
						
		case "boolean":
			$input = $html->selectyesno($attribute_name,$value,1);
			break;
			
		case "text":
			$input = '<textarea name="'.$attribute_name.'" wrap="soft" cols="70" rows="'.$textarea_rows.'">'.$value.'</textarea>';
			break ;
			
		case is_array($type):
			ob_start(); 
			$html->select_array($attribute_name,$type,$value,$null);
			$input = ob_get_contents(); 
			ob_end_clean();		
			break ;

		case "integer":
			$input = '<input type="text" size="10" maxlength="255" name="'.$attribute_name.'" value="'.$value.'" />';
			break ;			
			
			
		default:
			$input = '<input type="text" size="'.$size.'" maxlength="255" name="'.$attribute_name.'" value="'.$value.'" />';
			break;
	}	

	return $input;
	
}

/*
 *      \brief      		get the content of the ORDER BY clause
 *		\param $entityname	name of the entity
 *		\param $fields		array the list the displayed fields (from selectToArray)
 *      \return     		string the content of the ORDER BY clause
 */
function getORDERBY($entityname, $fields){
	
	$sort = '' ;
	if( isset($_GET['sortfield']) && ($_GET['entity']==$entityname) )
	{
		//Ascending or Descending sort
		($_GET['sortorder']=='desc')? $order = 'DESC' : $order = 'ASC' ;
		$att = $fields[$entityname][$entityname.'_'.$_GET['sortfield']] ;
		$sort = $att["entity"].'.'.$att["attribute"].' '.$order ;
	}
	return $sort ;
}

/*
 *      \brief      		get the content of the WHERE clause
 *		\param $entityname	name of the entity
 *		\param $fields		array the list the displayed fields (from selectToArray)
 *		\param &$params		string that will contain the GET parameters corresponding to the WHERE clause
 *      \return     		string the content of the WHERE clause
 */
function getWHERE($entityname,$fields, &$params){
	global $db ;
	
	$where = '' ;
	if( $_GET['search']==$entityname )
	{
		// regenerate the get parameters for the search to preserve filter while sorting
		$params[$entityname].='&amp;search='.$entityname ;
		
		$and = '' ;
		
		foreach ($fields[$entityname] as $att) {

			if( $_GET[$entityname.$att["attribute"]] != ''){
				// write the get part of URL
				$params[$entityname].='&amp;'.$entityname.$att["attribute"].'='.$_GET[$entityname.$att["attribute"]] ;
				
				//compare booleans
				if($att["type"]=='boolean'){
					$bool = (in_array(strtolower($_GET[$entityname.$att["attribute"]]),array(strtolower($langs->trans("yes")),'1')))? '1':'0';
					$where.=' '.$and.$att["entity"].'.'.$att["attribute"].' LIKE \'%'.$bool.'%\'' ;
					
				// compare dates
				}else if(($att["type"]=='datetime')||($att["type"]=='date')){
					/*TODO : comparaison de dates*/
				// general text case
				}else{
					$where.=' '.$and.$att["entity"].'.'.$att["attribute"].' LIKE \'%'.$db->escape($_GET[$entityname.$att["attribute"]]).'%\'' ;
				}
				$and = 'AND ' ;	
			} 
		}
	} 
	
	return $where ;
}

?>