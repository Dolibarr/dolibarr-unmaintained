<?php
/* Copyright (C) 2008 Laurent Destailleur         <eldy@users.sourceforge.net>
 * Copyright (C) 2008 Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
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
 */

/**
 \file			htdocs/lib/saphir2.lib.php
 \brief			Ensemble de fonctions de base de dolibarr sous forme d'include.
				Used for counters.
 \version		$Id: saphir2.lib.php,v 1.2 2008/11/02 00:22:23 raphael_bertrand Exp $
 */


/**
 * Return next value for a saphir2 mask (based on get_next_value of function2.lib.php used by saphir)
 *
 * @param unknown_type $db
 * @param string	$mask				The mask to use for ref computation
 * @param string $table					The table of referenced objects
 * @param string $field					The name of the ref field
 * @param string $where				To add a filter on selection (for exemple to filter for invoice types) -optional
 * @param string $valueforccc			Value for "client code" used in ref computation -optional
 * @param integer $date				Date to use for ref computation (timestamp) -optional
 * @param integer $useglobalrefclient		Flag for Global Ref Client Computation mode switching -optional
 * @param array $extraparams			Array of extra params (for extensions) -optional
 * @return 	string					New value
 */
function get_next_value_saphir2($db,$mask,$table,$field,$where='',$valueforccc='',$date='',$useglobalrefclient=0,$extraparams=array())
{
	// Clean parameters
	if ($date == '') $date=time();
	
	// Extract value for mask counter, mask raz and mask offset
	if (! eregi('\{(0+)([@\+][0-9]+)?([@\+][0-9]+)?\}',$mask,$reg))
	{
		// BEGIN Saphir2 extension
		if(!empty($extraparams['allowOnlyRefClient']) && eregi('\{(c+)(0+)\}',$mask,$regClientRef))
		{
			$reg[1]=$regClientRef[2];
			$reg[2]="";
			$reg[3]="";
		}
		else
		// END Saphir2 extension
			return 'ErrorBadMask';
			
		// BEGIN Saphir2 extension
		if(!empty($extraparams['allowOnlyRefClient']) && eregi('\{([@\+][0-9]+)([@\+][0-9]+)?\}',$mask,$regRaz))
		{
			$reg[2]=$regRaz[1];
			$reg[3]=$regRaz[2];
			$mask = str_replace($regRaz[0],"",$mask); //cleaning mask for next steps
		}	
		// END Saphir2 extension
	}
	$masktri=$reg[1].$reg[2].$reg[3];
	$maskcounter=$reg[1];
	$maskraz=-1;
	$maskoffset=0;
	if (strlen($maskcounter) < 3) return 'CounterMustHaveMoreThan3Digits';

	// Extract value for third party mask counter
	if (eregi('\{(c+)(0*)\}',$mask,$regClientRef))
	{
		$maskrefclient=$regClientRef[1].$regClientRef[2];
		$maskrefclient_maskclientcode=$regClientRef[1];
		$maskrefclient_maskcounter=$regClientRef[2];
		$maskrefclient_maskoffset=0; //default value of maskrefclient_counter offset
		$maskrefclient_clientcode=substr($valueforccc,0,strlen($maskrefclient_maskclientcode));//get n first characters of client code to form maskrefclient_clientcode
		$maskrefclient_clientcode=str_pad($maskrefclient_clientcode,strlen($maskrefclient_maskclientcode),"#",STR_PAD_RIGHT);//padding maskrefclient_clientcode for having exactly n characters in maskrefclient_clientcode
		$maskrefclient_clientcode=dol_string_nospecial($maskrefclient_clientcode);//sanitize maskrefclient_clientcode for sql insert and sql select like
		//if (strlen($maskrefclient_maskcounter) > 0 && strlen($maskrefclient_maskcounter) < 3) return 'CounterMustHaveMoreThan3Digits';
	}
	else $maskrefclient='';

	$maskwithonlyymcode=$mask;
	$maskwithonlyymcode=eregi_replace('\{(0+)([@\+][0-9]+)?([@\+][0-9]+)?\}',$maskcounter,$maskwithonlyymcode);
	$maskwithonlyymcode=eregi_replace('\{dd\}','dd',$maskwithonlyymcode);
	$maskwithonlyymcode=eregi_replace('\{(c+)(0*)\}',$maskrefclient,$maskwithonlyymcode);
	$maskwithnocode=$maskwithonlyymcode;
	$maskwithnocode=eregi_replace('\{yyyy\}','yyyy',$maskwithnocode);
	$maskwithnocode=eregi_replace('\{yy\}','yy',$maskwithnocode);
	$maskwithnocode=eregi_replace('\{y\}','y',$maskwithnocode);
	$maskwithnocode=eregi_replace('\{mm\}','mm',$maskwithnocode);
	// Now maskwithnocode = 0000ddmmyyyyccc for example
	// and maskcounter    = 0000 for example
	//print "maskwithonlyymcode=".$maskwithonlyymcode." maskwithnocode=".$maskwithnocode."\n<br>";

	// If an offset is asked
	if (! empty($reg[2]) && eregi('^\+',$reg[2])) $maskoffset=eregi_replace('^\+','',$reg[2]);
	if (! empty($reg[3]) && eregi('^\+',$reg[3])) $maskoffset=eregi_replace('^\+','',$reg[3]);

	// Define $sqlwhere
	// If a restore to zero after a month is asked we check if there is already a value for this year.
	if (! empty($reg[2]) && eregi('^@',$reg[2]))  $maskraz=eregi_replace('^@','',$reg[2]);
	if (! empty($reg[3]) && eregi('^@',$reg[3]))  $maskraz=eregi_replace('^@','',$reg[3]);
	if ($maskraz >= 0)
	{
		if ($maskraz > 12) return 'ErrorBadMaskBadRazMonth';

		// Define reg
		if ($maskraz > 1 && ! eregi('^(.*)\{(y+)\}\{(m+)\}',$maskwithonlyymcode,$reg)) return 'ErrorCantUseRazInStartedYearIfNoYearMonthInMask';
		if ($maskraz <= 1 && ! eregi('^(.*)\{(y+)\}',$maskwithonlyymcode,$reg)) return 'ErrorCantUseRazIfNoYearInMask';
		//print "x".$maskwithonlyymcode." ".$maskraz;

		// Define $yearcomp and $monthcomp (that will be use in the select where to search max number)
		$monthcomp=$maskraz;
		$yearoffset=0;
		$yearcomp=0;
		if (date("m",$date) < $maskraz) { $yearoffset=-1; }	// If current month lower that month of return to zero, year is previous year
		if (strlen($reg[2]) == 4) $yearcomp=sprintf("%04d",date("Y",$date)+$yearoffset);
		if (strlen($reg[2]) == 2) $yearcomp=sprintf("%02d",date("y",$date)+$yearoffset);
		if (strlen($reg[2]) == 1) $yearcomp=substr(date("y",$date),2,1)+$yearoffset;
			
		$sqlwhere='';
		$sqlwhere.='SUBSTRING('.$field.', '.(strlen($reg[1])+1).', '.strlen($reg[2]).') >= '.$yearcomp;
		if ($monthcomp > 1)	// Test useless if monthcomp = 1 (or 0 is same as 1)
		{
			$sqlwhere.=' AND SUBSTRING('.$field.', '.(strlen($reg[1])+strlen($reg[2])+1).', '.strlen($reg[3]).') >= '.$monthcomp;
		}
	}
	//print "masktri=".$masktri." maskcounter=".$maskcounter." maskraz=".$maskraz." maskoffset=".$maskoffset."<br>\n";

	// Define $sqlstring
	$posnumstart=strpos($maskwithnocode,$maskcounter);	// Pos of counter in final string (from 0 to ...)
	if ($posnumstart < 0) return 'ErrorBadMaskFailedToLocatePosOfSequence';
	$sqlstring='SUBSTRING('.$field.', '.($posnumstart+1).', '.strlen($maskcounter).')';
	//print "x".$sqlstring;

	// Define $maskLike
	$maskLike = dol_string_nospecial($mask);
	$maskLike = str_replace("%","_",$maskLike);
	// Replace protected special codes with matching number of _ as wild card caracter
	$maskLike = str_replace(dol_string_nospecial('{yyyy}'),'____',$maskLike);
	$maskLike = str_replace(dol_string_nospecial('{yy}'),'__',$maskLike);
	$maskLike = str_replace(dol_string_nospecial('{y}'),'_',$maskLike);
	$maskLike = str_replace(dol_string_nospecial('{mm}'),'__',$maskLike);
	$maskLike = str_replace(dol_string_nospecial('{dd}'),'__',$maskLike);
	$maskLike = str_replace(dol_string_nospecial('{'.$masktri.'}'),str_pad("",strlen($maskcounter),"_"),$maskLike);
	if ($maskrefclient) $maskLike = str_replace(dol_string_nospecial('{'.$maskrefclient.'}'),str_pad("",strlen($maskrefclient),"_"),$maskLike);

	// Get counter in database
	$counter=0;
	$sql = "SELECT MAX(".$sqlstring.") as val";
	$sql.= " FROM ".MAIN_DB_PREFIX.$table;
	//		$sql.= " WHERE ".$field." not like '(%'";
	$sql.= " WHERE ".$field." like '".$maskLike."'";
	if ($where) $sql.=$where;
	if ($sqlwhere) $sql.=' AND '.$sqlwhere;

	dolibarr_syslog("saphir2::get_next_value sql=".$sql, LOG_DEBUG);
	$resql=$db->query($sql);
	if ($resql)
	{
		$obj = $db->fetch_object($resql);
		$counter = $obj->val;
	}
	else dolibarr_print_error($db);
	if (empty($counter) || eregi('[^0-9]',$counter)) $counter=$maskoffset;
	$counter++;

	if ($maskrefclient_maskcounter)
	{
		#SAPHIR2 SPECIFIC CODE BEGIN
 		if($useglobalrefclient)
 		{
			//global refclient computation
			dolibarr_syslog("saphir2::get_next_value global refclient computation",LOG_DEBUG);
 			$maskrefclient_counter=get_next_refclientcounter_value($db,'','','','',$valueforccc,$date);
 		}
 		else
 		{
			// saphir-like refclient computation
			dolibarr_syslog("saphir2::get_next_value local refclient computation",LOG_DEBUG);
			$maskrefclient_counter=get_next_refclientcounter_value($db,$mask,$table,$field,$where,$valueforccc,$date,$extraparams);
		}
		if( strlen($maskrefclient_counter)>strlen($maskrefclient_maskcounter) )
			{//Too long, probably overflow
			 $maskrefclient_counter=(int)$maskrefclient_counter;// get int value
			 if (((int)(str_pad("",strlen($maskrefclient_maskcounter),"9",STR_PAD_LEFT)))<$maskrefclient_counter)
				{//Overflow
				dolibarr_syslog("saphir2::get_next_value overflow (".$maskrefclient_counter.") as more than ".strlen($maskrefclient_maskcounter)."chars",LOG_ERR); //Overflow Alert
				$maskrefclient_counter=str_pad("",strlen($maskrefclient_maskcounter),"#",STR_PAD_LEFT);
				}
			}
			$maskrefclient_counter=str_pad($maskrefclient_counter,strlen($maskrefclient_maskcounter),"0",STR_PAD_LEFT); //adjust returned value
		#SAPHIR2 SPECIFIC CODE END
	}

	// Build numFinal
	$numFinal = $mask;

	// We replace special codes except refclient
	$numFinal = str_replace('{yyyy}',date("Y",$date),$numFinal);
	$numFinal = str_replace('{yy}',date("y",$date),$numFinal);
	$numFinal = str_replace('{y}' ,substr(date("y",$date),2,1),$numFinal);
	$numFinal = str_replace('{mm}',date("m",$date),$numFinal);
	$numFinal = str_replace('{dd}',date("d",$date),$numFinal);
	if ($maskclientcode) $numFinal = str_replace(('{'.$maskclientcode.'}'),$clientcode,$numFinal);

	// Now we replace the counter
	$maskbefore='{'.$masktri.'}';
	$maskafter=str_pad($counter,strlen($maskcounter),"0",STR_PAD_LEFT);
	//print 'x'.$maskbefore.'-'.$maskafter.'y';
	$numFinal = str_replace($maskbefore,$maskafter,$numFinal);

	// Now we replace the refclient
	if ($maskrefclient)
	{
		//print "maskrefclient=".$maskrefclient." maskwithonlyymcode=".$maskwithonlyymcode." maskwithnocode=".$maskwithnocode."\n<br>";
		$maskrefclient_maskbefore='{'.$maskrefclient.'}';
		$maskrefclient_maskafter=$maskrefclient_clientcode.str_pad($maskrefclient_counter,strlen($maskrefclient_maskcounter),"0",STR_PAD_LEFT);
		$numFinal = str_replace($maskrefclient_maskbefore,$maskrefclient_maskafter,$numFinal);
	}

	dolibarr_syslog("saphir2::get_next_value return ".$numFinal,LOG_DEBUG);
	return $numFinal;
}

/**
 * Return next refclient counter part value
 *
  * @param unknown_type $db
 * @param unknown_type $masks			The array or sting of mask(s) to use for ref computation -optional
 * @param unknown_type $tables			The array or sting of  table(s) of referenced objects -optional
 * @param unknown_type $fields			The array or sting of  name(s) of the ref field -optional
 * @param unknown_type $wheres			The array or sting of additional(s) filter(s) on selection (for exemple to filter for invoice types) -optional
 * @param string $valueforccc			Value for "client code" used in ref computation -optional
 * @param integer $date				Date to use for ref computation (timestamp) -optional
 * @param array $extraparams			Array (of array if arrays provided behove) of  params (for extensions) -optional
 * @return 	string					New value
 */
function get_next_refclientcounter_value($db,$masks='',$tables='',$fields='',$wheres='',$valueforccc='', $date='',$extraparams_tab='')
{
global $conf;

	// Clean parameters
	if ($date == '') $date=time();

	if(!is_array($extraparams_tab))	$extraparams_tab=array(); //get default extraparams if any or not array value
		
	if((!empty($masks) || !empty($tables) || !empty($fields) || !empty($wheres) )
	&&(is_string($masks) || is_string($tables) || is_string($fields) || is_string($wheres) ))
	{ //convert strings to single row arrays
		$masks=array($masks);
		$tables=array($tables);
		$fields=array($fields);
		$wheres=array($wheres);
		$extraparams_tab=array($extraparams_tab);
	}
	
	if(!is_array($masks) || !is_array($tables) || !is_array($fields) || !is_array($wheres))
	{   //get defaults (use global refs) - overide  given values except valueforccc because of null or invalid data
		dolibarr_syslog("saphir2::get_next_refclientcounter_value use defauts values",LOG_DEBUG);
		$defaultInputs=get_globalrefclientcounter_settings();
		$masks=$defaultInputs['masks'];
		$tables=$defaultInputs['tables'];
		$fields=$defaultInputs['fields'];
		$wheres=$defaultInputs['wheres'];
		$extraparams_tab=$defaultInputs['extraparams_tab'];
	}
	
	for($numStep=0;$numStep<sizeOf($masks);$numStep++)
	{
		//get step values
		$mask=$masks[$numStep];
		$table=$tables[$numStep];
		$field=$fields[$numStep];
		$where=$wheres[$numStep];
		
		if(isset($extraparams_tab[$numStep])) 
			$extraparams=$extraparams_tab[$numStep];
		else
			$extraparams=array();
		
		# BEGIN Saphir refclient routine part
		// Extract value for mask counter, mask raz and mask offset
		if (! eregi('\{(0+)([@\+][0-9]+)?([@\+][0-9]+)?\}',$mask,$reg))
		{
			// BEGIN Saphir2 extension
			if(!empty($extraparams['allowOnlyRefClient']) && eregi('\{(c+)(0+)\}',$mask,$regClientRef))
			{
				$reg[1]=$regClientRef[2];
				$reg[2]="";
				$reg[3]="";
			}
			else
			// END Saphir2 extension
				return 'ErrorBadMask';
				
			// BEGIN Saphir2 extension
			if(!empty($extraparams['allowOnlyRefClient']) && eregi('\{([@\+][0-9]+)([@\+][0-9]+)?\}',$mask,$regRaz))
			{
				$reg[2]=$regRaz[1];
				$reg[3]=$regRaz[2];
				$mask = str_replace($regRaz[0],"",$mask); //cleaning mask for next steps
			}	
			// END Saphir2 extension
		}
		$masktri=$reg[1].$reg[2].$reg[3];
		$maskcounter=$reg[1];
		$maskraz=-1;
		if (strlen($maskcounter) < 3) return 'CounterMustHaveMoreThan3Digits';

		// Extract value for third party mask counter
		if (eregi('\{(c+)(0*)\}',$mask,$regClientRef))
		{
			$maskrefclient=$regClientRef[1].$regClientRef[2];
			$maskrefclient_maskclientcode=$regClientRef[1];
			$maskrefclient_maskcounter=$regClientRef[2];
			$maskrefclient_maskoffset=0; //default value of maskrefclient_counter offset
			$maskrefclient_clientcode=substr($valueforccc,0,strlen($maskrefclient_maskclientcode));//get n first characters of client code to form maskrefclient_clientcode
			$maskrefclient_clientcode=str_pad($maskrefclient_clientcode,strlen($maskrefclient_maskclientcode),"#",STR_PAD_RIGHT);//padding maskrefclient_clientcode for having exactly n characters in maskrefclient_clientcode
			$maskrefclient_clientcode=dol_string_nospecial($maskrefclient_clientcode);//sanitize maskrefclient_clientcode for sql insert and sql select like
			//if (strlen($maskrefclient_maskcounter) > 0 && strlen($maskrefclient_maskcounter) < 3) return 'CounterMustHaveMoreThan3Digits';
		}
		else
		{
			$maskrefclient='';
			$maskrefclient_maskcounter='';
		}

		$maskwithonlyymcode=$mask;
		$maskwithonlyymcode=eregi_replace('\{(0+)([@\+][0-9]+)?([@\+][0-9]+)?\}',$maskcounter,$maskwithonlyymcode);
		$maskwithonlyymcode=eregi_replace('\{dd\}','dd',$maskwithonlyymcode);
		$maskwithonlyymcode=eregi_replace('\{(c+)(0*)\}',$maskrefclient,$maskwithonlyymcode);
		$maskwithnocode=$maskwithonlyymcode;
		$maskwithnocode=eregi_replace('\{yyyy\}','yyyy',$maskwithnocode);
		$maskwithnocode=eregi_replace('\{yy\}','yy',$maskwithnocode);
		$maskwithnocode=eregi_replace('\{y\}','y',$maskwithnocode);
		$maskwithnocode=eregi_replace('\{mm\}','mm',$maskwithnocode);
		// Now maskwithnocode = 0000ddmmyyyyccc for example
		// and maskcounter    = 0000 for example
		//print "maskwithonlyymcode=".$maskwithonlyymcode." maskwithnocode=".$maskwithnocode."\n<br>";

		// If an offset is asked
		if (! empty($reg[2]) && eregi('^\+',$reg[2])) $maskoffset=eregi_replace('^\+','',$reg[2]);
		if (! empty($reg[3]) && eregi('^\+',$reg[3])) $maskoffset=eregi_replace('^\+','',$reg[3]);

		// Define $sqlwhere
		// If a restore to zero after a month is asked we check if there is already a value for this year.
		if (! empty($reg[2]) && eregi('^@',$reg[2]))  $maskraz=eregi_replace('^@','',$reg[2]);
		if (! empty($reg[3]) && eregi('^@',$reg[3]))  $maskraz=eregi_replace('^@','',$reg[3]);
		if ($maskraz >= 0)
		{
			if ($maskraz > 12) return 'ErrorBadMaskBadRazMonth';
			
			// Define reg
			if ($maskraz > 1 && ! eregi('^(.*)\{(y+)\}\{(m+)\}',$maskwithonlyymcode,$reg)) return 'ErrorCantUseRazInStartedYearIfNoYearMonthInMask';
			if ($maskraz <= 1 && ! eregi('^(.*)\{(y+)\}',$maskwithonlyymcode,$reg)) return 'ErrorCantUseRazIfNoYearInMask';
			//print "x".$maskwithonlyymcode." ".$maskraz;
			
			// Define $yearcomp and $monthcomp (that will be use in the select where to search max number)
			$monthcomp=$maskraz;
			$yearoffset=0;
			$yearcomp=0;
			if (date("m") < $maskraz) { $yearoffset=-1; }	// If current month lower that month of return to zero, year is previous year
			if (strlen($reg[2]) == 4) $yearcomp=sprintf("%04d",date("Y")+$yearoffset);
			if (strlen($reg[2]) == 2) $yearcomp=sprintf("%02d",date("y")+$yearoffset);
			if (strlen($reg[2]) == 1) $yearcomp=substr(date("y"),2,1)+$yearoffset;
				
			$sqlwhere='';
			$sqlwhere.='SUBSTRING('.$field.', '.(strlen($reg[1])+1).', '.strlen($reg[2]).') >= '.$yearcomp;
			if ($monthcomp > 1)	// Test useless if monthcomp = 1 (or 0 is same as 1)
			{
				$sqlwhere.=' AND SUBSTRING('.$field.', '.(strlen($reg[1])+strlen($reg[2])+1).', '.strlen($reg[3]).') >= '.$monthcomp;
			}
		}
		else
			$sqlwhere='';
		//print "masktri=".$masktri." maskcounter=".$maskcounter." maskraz=".$maskraz." maskoffset=".$maskoffset."<br>\n";
		
		if ($maskrefclient_maskcounter)
		{
			//print "maskrefclient_maskcounter=".$maskrefclient_maskcounter." maskwithnocode=".$maskwithnocode." maskrefclient=".$maskrefclient."\n<br>";
				
			// Define $sqlstring
			$maskrefclient_posnumstart=strpos($maskwithnocode,$maskrefclient_maskcounter,strpos($maskwithnocode,$maskrefclient));	// Pos of counter in final string (from 0 to ...)
			if ($maskrefclient_posnumstart <= 0) return 'ErrorBadMask';
			$maskrefclient_sqlstring='SUBSTRING('.$field.', '.($maskrefclient_posnumstart+1).', '.strlen($maskrefclient_maskcounter).')';
			//print "x".$sqlstring;

			// Define $maskrefclient_maskLike
			$maskrefclient_maskLike = dol_string_nospecial($mask);
			$maskrefclient_maskLike = str_replace("%","_",$maskrefclient_maskLike);
			// Replace protected special codes with matching number of _ as wild card caracter
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{yyyy}'),'____',$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{yy}'),'__',$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{y}'),'_',$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{mm}'),'__',$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{dd}'),'__',$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{'.$masktri.'}'),str_pad("",strlen($maskcounter),"_"),$maskrefclient_maskLike);
			$maskrefclient_maskLike = str_replace(dol_string_nospecial('{'.$maskrefclient.'}'),$maskrefclient_clientcode.str_pad("",strlen($maskrefclient_maskcounter),"_"),$maskrefclient_maskLike);

			// Get counter in database
			$maskrefclient_counter=0;
			$maskrefclient_sql = "SELECT MAX(".$maskrefclient_sqlstring.") as val";
			$maskrefclient_sql.= " FROM ".MAIN_DB_PREFIX.$table;
			//$sql.= " WHERE ".$field." not like '(%'";
			$maskrefclient_sql.= " WHERE ".$field." like '".$maskrefclient_maskLike."'";
			if ($where) $maskrefclient_sql.=$where;//use the same optional where as general mask
			if ($sqlwhere) $maskrefclient_sql.=' AND '.$sqlwhere; //use the same sqlwhere as general mask
			$maskrefclient_sql.=' AND (SUBSTRING('.$field.', '.(strpos($maskwithnocode,$maskrefclient)+1).', '.strlen($maskrefclient_maskclientcode).")='".$maskrefclient_clientcode."')";
				
			dolibarr_syslog("saphir2::get_next_refclientcounter_value maskrefclient_sql=".$maskrefclient_sql, LOG_DEBUG);
			$maskrefclient_resql=$db->query($maskrefclient_sql);
			if ($maskrefclient_resql)
			{
				$maskrefclient_obj = $db->fetch_object($maskrefclient_resql);
				$maskrefclient_counter = $maskrefclient_obj->val;
			}
			else dolibarr_print_error($db);
			if (empty($maskrefclient_counter) || eregi('[^0-9]',$maskrefclient_counter)) $maskrefclient_counter=$maskrefclient_maskoffset;
			$maskrefclient_counter++;
		}
		
		# END Saphir refclient routine part
		$numSteps[]=str_pad($maskrefclient_counter,strlen($maskrefclient_maskcounter),"0",STR_PAD_LEFT);
		dolibarr_syslog("saphir2::get_next_refclientcounter_value numSteps value=".$numFinal,LOG_DEBUG);
	}
	$numSteps[]=str_pad("1",strlen($maskrefclient_maskcounter),"0",STR_PAD_LEFT);
	$numFinal=max($numSteps);
	dolibarr_syslog("saphir2::get_next_refclientcounter_value return ".$numFinal,LOG_DEBUG);
	return $numFinal;
}

/**
 * Return refclientcounter settings values for counters activated with refclientcounter value global sharing
 *
 * @return 	array	Associative Array  containing values of $masks,$tables,$fields,$wheres,$extraparams_tab 
 */
function get_globalrefclientcounter_settings()
{
global $conf;

	//Init params vars
	$masks=array();
	$tables=array();
	$fields=array();
	$wheres=array();
	$extraparams_tab=array();
		
	// Module commande client
	if ($conf->commande->enabled && $conf->global->COMMANDE_ADDON == "mod_commande_saphir2" && $conf->global->COMMANDE_SAPHIR2_MASK && $conf->global->COMMANDE_SAPHIR2_USEGLOBALCLIENTREF)
	{
		$extraparams=array();//no extra params basicaly
		if(!empty($conf->global->ALLOWONLYREFCLIENT))
			$extraparams['allowOnlyRefClient']=true;
			
		$masks[]=$conf->global->COMMANDE_SAPHIR_MASK;
		$tables[]='commande';
		$fields[]='ref';
		$wheres[]='';
		$extraparams_tab[]=$extraparams;
	}
	
	// Module facture
	if ($conf->facture->enabled && $conf->global->FACTURE_ADDON == "saphir2" && $conf->global->FACTURE_SAPHIR2_MASK_CREDIT && $conf->global->FACTURE_SAPHIR2_USEGLOBALCLIENTREF_CREDIT)
	{
		$masks[]=$conf->global->FACTURE_SAPHIR2_MASK_CREDIT;
		$tables[]='facture';
		$fields[]='facnumber';
		$wheres[]=" AND type = 2";
		$extraparams_tab[]=$extraparams;//no extra params (facture)
	}
	if ($conf->facture->enabled && $conf->global->FACTURE_ADDON == "saphir2" && $conf->global->FACTURE_SAPHIR2_MASK_INVOICE && $conf->global->FACTURE_SAPHIR2_USEGLOBALCLIENTREF_INVOICE)
	{
			
		$masks[]=$conf->global->FACTURE_SAPHIR2_MASK_INVOICE;
		$tables[]='facture';
		$fields[]='facnumber';
		$wheres[]=" AND type != 2";
		$extraparams_tab[]=array();//no extra params (facture)
	}
	
	// Module ficheinter
	if ($conf->fichinter->enabled && $conf->global->FICHEINTER_ADDON == "saphir2" && $conf->global->FICHINTER_SAPHIR2_MASK && $conf->global->FICHINTER_SAPHIR2_USEGLOBALCLIENTREF)
	{
		$extraparams=array();//no extra params basicaly
		if(!empty($conf->global->ALLOWONLYREFCLIENT))
			$extraparams['allowOnlyRefClient']=true;
			
		$masks[]=$conf->global->FICHINTER_SAPHIR2_MASK;
		$tables[]='fichinter';
		$fields[]='ref';
		$wheres[]='';
		$extraparams_tab[]=$extraparams;
	}
	
	// Module propal
	if ($conf->propal->enabled && $conf->global->PROPALE_ADDON == "mod_propale_saphir2" && $conf->global->PROPALE_SAPHIR2_MASK && $conf->global->PROPALE_SAPHIR2_USEGLOBALCLIENTREF)
	{
		$extraparams=array();//no extra params basicaly
		if(!empty($conf->global->ALLOWONLYREFCLIENT))
			$extraparams['allowOnlyRefClient']=true;
	
		$masks[]=$conf->global->PROPALE_SAPHIR2_MASK;
		$tables[]='propal';
		$fields[]='ref';
		$wheres[]='';
		$extraparams_tab[]=$extraparams;
	}	
		
	// Module chronodocs
	if ( (!empty($conf->global->MAIN_MODULE_CHRONODOCS)) && $conf->global->CHRONODOCS_ADDON == "saphir2" && $conf->global->CHRONODOCS_SAPHIR2_MASK && $conf->global->CHRONODOCS_SAPHIR2_USEGLOBALCLIENTREF)
	{
		$chronodocs_saphir2_mask=$conf->global->CHRONODOCS_SAPHIR2_MASK;
		if (eregi('\{(t+)\}',$chronodocs_saphir2_mask,$regTypeRef))
		{	//escape chronodocs's  TypeRef filter before getting next refclientcounter value for chronodocs
			$typeRef_maskbefore='{'.$regTypeRef[1].'}';
			$typeRef_maskafter=str_pad("",strlen($regTypeRef[1]),"_",STR_PAD_LEFT);
			$chronodocs_saphir2_mask = str_replace($typeRef_maskbefore,$typeRef_maskafter,$chronodocs_saphir2_mask);
		}		
		
		$extraparams=array();//no extra params basicaly
		if(!empty($conf->global->ALLOWONLYREFCLIENT))
			$extraparams['allowOnlyRefClient']=true;

		
		$masks[]=$chronodocs_saphir2_mask;
		$tables[]='chronodocs_entries';
		$fields[]='ref';
		$wheres[]='';
		$extraparams_tab[]=$extraparams;
	}
	
	//RETURN
	return array('masks' => $masks,
				 'tables' =>$tables,
				 'fields' => $fields,
				 'wheres' => $wheres,
				 'extraparams_tab' => $extraparams_tab
				 );
}

