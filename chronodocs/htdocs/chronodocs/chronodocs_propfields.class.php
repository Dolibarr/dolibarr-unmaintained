<?php
/* Copyright (C) 2007-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
/**
        \file       chronodocs/chronodocs_propfields.class.php
        \ingroup    chronodocs
        \brief       CRUD class file for chronodocs propfields (Create/Read/Update/Delete)
	\version    $Id: chronodocs_propfields.class.php,v 1.1 2008/09/10 09:34:56 raphael_bertrand Exp $
	\author	Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
	\remarks	Initialy built by build_class_from_table on 2008-09-03 17:35
*/

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_types.class.php");
//require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_entries.class.php");
//require_once(DOL_DOCUMENT_ROOT."/lib/chronodocs.lib.php");


/**
        \class      Chronodocs_propfields
        \brief       CRUD class  for chronodocs propfields (Create/Read/Update/Delete)
	\remarks	Initialy built by build_class_from_table on 2008-09-03 17:35
*/
class Chronodocs_propfields // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='chronodocs_propfields';			//!< Id that identify managed objects
	//var $table_element='chronodocs_propfields';	//!< Name of table without prefix where object is stored
    
    var $id;
    
	var $ref;
	var $title;
	var $brief;
	var $fk_type;
	var $fk_status;

    

	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    function Chronodocs_propfields($DB) 
    {
        $this->db = $DB;
        return 1;
    }

	
    /**
     *      \brief      Create in database
     *      \param      user        	User that create
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean parameters
        
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->title)) $this->title=trim($this->title);
		if (isset($this->brief)) $this->brief=trim($this->brief);
		if (isset($this->fk_type)) $this->fk_type=trim($this->fk_type);
		if (isset($this->fk_status)) $this->fk_status=trim($this->fk_status);

        

		// Check parameters
		// Put here code to add control on parameters values
		
        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."chronodocs_propfields(";
		
		$sql.= "ref,";
		$sql.= "title,";
		$sql.= "brief,";
		$sql.= "fk_type,";
		$sql.= "fk_status";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->ref)?'NULL':"'".$this->ref."'").",";
		$sql.= " ".(! isset($this->title)?'NULL':"'".$this->title."'").",";
		$sql.= " ".(! isset($this->brief)?'NULL':"'".$this->brief."'").",";
		$sql.= " ".(! isset($this->fk_type)?'NULL':"'".$this->fk_type."'").",";
		$sql.= " ".(! isset($this->fk_status)?'NULL':"'".$this->fk_status."'")."";

        
		$sql.= ")";

		$this->db->begin();
		
	   	dolibarr_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."chronodocs_propfields");
    
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.
	            
	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/interfaces.class.php");
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
			}
        }

        // Commit or rollback
        if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dolibarr_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}	
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
            return $this->id;
		}
    }

    
    /**
     *    \brief      Load object in memory from database
     *    \param      id          id object
     *    \return     int         <0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.ref,";
		$sql.= " t.title,";
		$sql.= " t.brief,";
		$sql.= " t.fk_type,";
		$sql.= " t.fk_status";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."chronodocs_propfields as t";
        $sql.= " WHERE t.rowid = ".$id;
    
    	dolibarr_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
    
                $this->id    = $obj->rowid;
                
				$this->ref = $obj->ref;
				$this->title = $obj->title;
				$this->brief = $obj->brief;
				$this->fk_type = $obj->fk_type;
				$this->fk_status = $obj->fk_status;

                
            }
            $this->db->free($resql);
            
            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dolibarr_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }
    

    /**
     *      \brief      Update database
     *      \param      user        	User that modify
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean parameters
        
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->title)) $this->title=trim($this->title);
		if (isset($this->brief)) $this->brief=trim($this->brief);
		if (isset($this->fk_type)) $this->fk_type=trim($this->fk_type);
		if (isset($this->fk_status)) $this->fk_status=trim($this->fk_status);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_propfields SET";
        
		$sql.= " ref=".(isset($this->ref)?"'".addslashes($this->ref)."'":"null").",";
		$sql.= " title=".(isset($this->title)?"'".addslashes($this->title)."'":"null").",";
		$sql.= " brief=".(isset($this->brief)?"'".addslashes($this->brief)."'":"null").",";
		$sql.= " fk_type=".(isset($this->fk_type)?$this->fk_type:"null").",";
		$sql.= " fk_status=".(isset($this->fk_status)?$this->fk_status:"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();
        
		dolibarr_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.
				
	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/interfaces.class.php");
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}
		
        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dolibarr_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}	
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}		
    }
  
  
 	/**
	*   \brief      Delete object in database
	*   \param      user        	User that delete
	*   \param      notrigger	    0=launch triggers after, 1=disable triggers
	*	\return		int				<0 if KO, >0 if OK
	*/
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;
		
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."chronodocs_propfields";
		$sql.= " WHERE rowid=".$this->id;
	
		$this->db->begin();
		
		dolibarr_syslog(get_class($this)."::delete sql=".$sql);
		$resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		
		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
		        // want this action call a trigger.
				
		        //// Call triggers
		        //include_once(DOL_DOCUMENT_ROOT . "/interfaces.class.php");
		        //$interface=new Interfaces($this->db);
		        //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
		        //if ($result < 0) { $error++; $this->errors=$interface->errors; }
		        //// End call triggers
			}	
		}
		
        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dolibarr_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}	
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}

  
	/**
	 *		\brief		Initialise object with example values
	 *		\remarks	id must be 0 if object instance is a specimen.
	 */
	function initAsSpecimen()
	{
		$this->id=0;
		
		$this->ref='';
		$this->title='';
		$this->brief='';
		$this->fk_type='';
		$this->fk_status='0';

		
	}

	/**
	 *      \brief      Get a array of object giving data (ref,title,brief,fk_type,fk_status,propid)  on chronodoc found whith given search parametters 
	 *      \param      limit      max result size
	 *      \param      offset (default: 0)
	 *      \param      sortfield (default: f.date_c)
	 *      \param      sortorder (default: DESC)
	 *      \param      fk_type    if not null, restrict to propfield for given fk_type of chronodoc (default: 0)
	 *      \param      fk_status  if not null,  restrict to propfield  with given status
	 */
	function get_list($limit,$offset="0",$sortfield="p.ref",$sortorder="ASC",$fk_type=0,$fk_status='')
	{
		global $user, $conf, $langs;
		$db = $this->db;
		
		$tab_objp=array();
		$where="";
		
		$sql = "SELECT p.ref,p.title,p.brief,p.fk_type,p.fk_status,p.rowid as propid";
		$sql.= " FROM ".MAIN_DB_PREFIX."chronodocs_propfields as p ";
		if($fk_type > 0)
			$where.= " p.fk_type = ".$fk_type;
			
		if(!empty($fk_status))
		{
			if(!empty($where))
				$where.= " AND ";
				
			$where.= " p.fk_status = ".$fk_status;
		}
		if(!empty($where))
			$sql.=" WHERE ".$where;

				$sql.= " ORDER BY $sortfield $sortorder ";
		$sql.= $db->plimit( $limit + 1 ,$offset);

		dolibarr_syslog(get_class($this)."::get_list sql=".$sql, LOG_DEBUG);
		$result=$db->query($sql);
		if ($result)
		{
		    $num = $db->num_rows($result);
			while ($i < min($num, $limit))
			{
				$objp = $db->fetch_object($result);
				if($objp) 
					$tab_objp[]= $objp;
				$i++;
			}
			$db->free($result);
			
			return $tab_objp;
		}
		else
		{
			return 0;
		}

	}
	
	/**
	*   \brief      Delete propvalues object in database for current propfield
	*   \param      user        	User that delete
	*   \param      notrigger	    0=launch triggers after, 1=disable triggers
	*	\return		int				<0 if KO, >0 if OK
	*/
	function clear_values($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;
		
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."chronodocs_propvalues";
		$sql.= " WHERE fk_propfield=".$this->id;
	
		$this->db->begin();
		
		dolibarr_syslog(get_class($this)."::clear_values sql=".$sql);
		$resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		
		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
		        // want this action call a trigger.
				
		        //// Call triggers
		        //include_once(DOL_DOCUMENT_ROOT . "/interfaces.class.php");
		        //$interface=new Interfaces($this->db);
		        //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
		        //if ($result < 0) { $error++; $this->errors=$interface->errors; }
		        //// End call triggers
			}	
		}
		
        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dolibarr_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}	
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}
	
	/**
	*   \brief      Retourne la liste des auto_propfields
	*   \param      other_propfields_ref	array (optional)
	*   \return	 array (desckey=>titlevalue)
	*/
	function liste_auto_propfields($other_propfields_ref=array())
	{
	 global $langs;
	 
		$auto_propfields_ref=$other_propfields_ref;
		
		$auto_propfields_ref["#R_TITRE_NOTE#"]=$langs->trans("Propfield_R_TITRE_NOTE");
		$auto_propfields_ref["#R_NUM_CHRONO#"]=$langs->trans("Propfield_R_NUM_CHRONO");
		$auto_propfields_ref["#R_DT_REDAC#"]=$langs->trans("Propfield_R_DT_REDAC");
		
		$auto_propfields_ref["#R_SOCIETE#"]=$langs->trans("Propfield_R_SOCIETE");
		$auto_propfields_ref["#R_SOC_ADR#"]=$langs->trans("Propfield_R_SOC_ADR");
		$auto_propfields_ref["#R_SOC_CP#"]=$langs->trans("Propfield_R_SOC_CP");
		$auto_propfields_ref["#R_SOC_VILLE#"]=$langs->trans("Propfield_R_SOC_VILLE");
		
		$auto_propfields_ref["#R_REDACTEUR#"]=$langs->trans("Propfield_R_REDACTEUR");
		$auto_propfields_ref["#R_REDAC_NOM#"]=$langs->trans("Propfield_R_REDAC_NOM");
		$auto_propfields_ref["#R_REDAC_PNOM#"]=$langs->trans("Propfield_R_REDAC_PNOM");
		$auto_propfields_ref["#R_REDAC_CIV#"]=$langs->trans("Propfield_R_REDAC_CIV");
		$auto_propfields_ref["#R_REDAC_EMAIL#"]=$langs->trans("Propfield_R_REDAC_EMAIL");
		
		$auto_propfields_ref["#R_CUSTOMER#"]=$langs->trans("Propfield_R_CUSTOMER");
		$auto_propfields_ref["#R_CUST_NOM#"]=$langs->trans("Propfield_R_CUST_NOM");
		$auto_propfields_ref["#R_CUST_PNOM#"]=$langs->trans("Propfield_R_CUST_PNOM");
		$auto_propfields_ref["#R_CUST_CIV#"]=$langs->trans("Propfield_R_CUST_CIV");
		$auto_propfields_ref["#R_CUST_EMAIL#"]=$langs->trans("Propfield_R_CUST_EMAIL");
		
		$auto_propfields_ref["#R_TRIGRAMME#"]=$langs->trans("Propfield_R_TRIGRAMME");
				
		return $auto_propfields_ref;
	}
}
?>
