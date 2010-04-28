<?php
/* Copyright (C) 2007-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2008      Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
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
        \file       dev/skeletons/chronodocs_propvalues.class.php
        \ingroup    mymodule othermodule1 othermodule2
        \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
		\version    $Id: chronodocs_propvalues.class.php,v 1.7 2010/04/28 12:39:06 hregis Exp $
		\author		Put author name here
		\remarks	Initialy built by build_class_from_table on 2008-09-03 17:35
*/

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_types.class.php");
//require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_entries.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_propfields.class.php");



/**
        \class      Chronodocs_propvalues
        \brief      Put here description of your class
		\remarks	Initialy built by build_class_from_table on 2008-09-03 17:35
*/
class Chronodocs_propvalues // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='chronodocs_propvalues';			//!< Id that identify managed objects
	//var $table_element='chronodocs_propvalues';	//!< Name of table without prefix where object is stored
    
    var $id;
    
	var $content;
	var $fk_propfield;
	var $fk_objectid;

    

	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    function Chronodocs_propvalues($DB) 
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
        
		if (isset($this->content)) $this->content=trim($this->content);
		if (isset($this->fk_propfield)) $this->fk_propfield=trim($this->fk_propfield);
		if (isset($this->fk_objectid)) $this->fk_objectid=trim($this->fk_objectid);

        

		// Check parameters
		// Put here code to add control on parameters values
		
        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."chronodocs_propvalues(";
		
		$sql.= "content,";
		$sql.= "fk_propfield,";
		$sql.= "fk_objectid";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->content)?'NULL':"'".addslashes($this->content)."'").",";
		$sql.= " ".(! isset($this->fk_propfield)?'NULL':"'".$this->fk_propfield."'").",";
		$sql.= " ".(! isset($this->fk_objectid)?'NULL':"'".$this->fk_objectid."'")."";

        
		$sql.= ")";

		$this->db->begin();
		
	   	dolibarr_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."chronodocs_propvalues");
    
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.
	            
	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
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
		
		$sql.= " t.content,";
		$sql.= " t.fk_propfield,";
		$sql.= " t.fk_objectid";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."chronodocs_propvalues as t";
        $sql.= " WHERE t.rowid = ".$id;
    
    	dolibarr_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
    
                $this->id    = $obj->rowid;
                
				$this->content = $obj->content;
				$this->fk_propfield = $obj->fk_propfield;
				$this->fk_objectid = $obj->fk_objectid;

                
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
        
		if (isset($this->content)) $this->content=trim($this->content);
		if (isset($this->fk_propfield)) $this->fk_propfield=trim($this->fk_propfield);
		if (isset($this->fk_objectid)) $this->fk_objectid=trim($this->fk_objectid);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_propvalues SET";
        
		$sql.= " content=".(isset($this->content)?"'".addslashes($this->content)."'":"null").",";
		$sql.= " fk_propfield=".(isset($this->fk_propfield)?$this->fk_propfield:"null").",";
		$sql.= " fk_objectid=".(isset($this->fk_objectid)?$this->fk_objectid:"null")."";

        
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
	            //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
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
    *	\param      user        	User that delete
    *   \param      notrigger	    0=launch triggers after, 1=disable triggers
	*	\return		int				<0 if KO, >0 if OK
	*/
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;
		
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."chronodocs_propvalues";
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
		        //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
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
		
		$this->content='';
		$this->fk_propfield='';
		$this->fk_objectid='';

		
	}
	
	
	/**
	 *      \brief      Get a array of object giving data (valid,content +ref,title,brief,fk_type,fk_status,propid)  on chronodoc found whith given search parametters 
	 *      \param      limit      max result size
	 *      \param      offset (default: 0)
	 *      \param      sortfield (default: f.date_c)
	 *      \param      sortorder (default: DESC)
	 *      \param      fk_propfield    if not null, restrict to given propfield  id (default: 0)
	 *      \param      fk_objectid  if not null,  restrict to given chronodoc object id
	*      \param       fk_status  if not null,  restrict to given propfield status 
	*/
	function get_list($limit=0,$offset="0",$sortfield="p.ref",$sortorder="ASC",$fk_propfield=0,$fk_objectid=0,$fk_type=0,$fk_status='')
	{
		global $user, $conf, $langs;
		$db = $this->db;
		
		$tab_objp=array();
		$where="";
		
		$sql = "SELECT v.rowid as valid,v.content,p.ref,p.title,p.brief,p.fk_type,p.fk_status,p.rowid as propid";
		$sql.= " FROM ".MAIN_DB_PREFIX."chronodocs_propfields as p ";
		$sql.= " LEFT JOIN ".MAIN_DB_PREFIX."chronodocs_propvalues as v";
		if($fk_objectid > 0)
			$sql.= " ON (v.fk_propfield = p.rowid  AND v.fk_objectid = ".$fk_objectid." )";
		else
			$sql.= " ON (v.fk_propfield = p.rowid )";
		
		if($fk_propfield > 0)
		{
			$where.=(empty($where))?" WHERE ":" AND ";
			$where.= " p.rowid = ".$fk_propfield;
		}	
		if($fk_type > 0)
		{
			$where.=(empty($where))?" WHERE ":" AND ";
			$where.= " p.fk_type = ".$fk_type;
		}
		if(!empty($fk_status))
		{
			$where.=(empty($where))?" WHERE ":" AND ";
			$where.= " p.fk_status = ".$fk_status;
		}
		
		$sql.= $where;
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

}
?>
