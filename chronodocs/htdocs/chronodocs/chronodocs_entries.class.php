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
        \file       chronodocs/chronodocs_entries.class.php
        \ingroup    chronodocs
        \brief      CRUD class file for chronodocs entries (Create/Read/Update/Delete)
		\version    $Id: chronodocs_entries.class.php,v 1.7 2010/04/09 06:47:23 grandoc Exp $
		\author	Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
		\remarks	Initialy built by build_class_from_table on 2008-08-18 17:28
*/

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/commonobject.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_types.class.php");
//require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_propfields.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_propvalues.class.php");


/**
        \class      Chronodocs_entries
        \brief       CRUD class for chronodocs entries (Create/Read/Update/Delete)
		\remarks	Initialy built by build_class_from_table on 2008-08-18 17:28
*/
class Chronodocs_entries  extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='chronodocs_entries';			//!< Id that identify managed objects
	var $table_element='chronodocs_entries';	//!< Name of table without prefix where object is stored
    
    var $id;
	
	var $client=null;				// Objet societe client (a charger par fetch_client)
	var $chronodocs_type=null;		// Objet chronodocs_types (a charger par fetch_chronodocs_type)
	var $propvalues=null;			// Objet contenant les objets chronodocs_propvalues correspondants (a charger par fetch_chronodocs_propvalues)
    
	var $ref;
	var $title;
	var $filename;				//not already used because docs are stocked only in directory
	var $filename_orig;			//not already used because docs are stocked only in directory
	var $filesize;				//not already used because docs are stocked only in directory
	var $brief;
	var $date_c='';
	var $date_u='';
	var $fk_chronodocs_type;
	var $socid;
	var $fk_status;				//not already used
	var $fk_user_c;
	var $fk_user_u;

    

	
 /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    function Chronodocs_entries($DB) 
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
    function create($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean parameters
        
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->title)) $this->title=trim($this->title);
		if (isset($this->filename)) $this->filename=trim($this->filename);
		if (isset($this->filename_orig)) $this->filename_orig=trim($this->filename_orig);
		if (isset($this->filesize)) $this->filesize=trim($this->filesize);
		if (isset($this->brief)) $this->brief=trim($this->brief);
		if (isset($this->fk_chronodocs_type)) $this->fk_chronodocs_type=trim($this->fk_chronodocs_type);
		if (isset($this->socid)) $this->socid=trim($this->socid);
		if (isset($this->fk_status)) $this->fk_status=trim($this->fk_status);
		if (isset($this->fk_user_c)) $this->fk_user_c=trim($this->fk_user_c);
		if (isset($this->fk_user_u)) $this->fk_user_u=trim($this->fk_user_u);

        //Set date_u to now if not set
        if (!isset($this->date_u)) $this->date_u=time();
		if (!isset($this->fk_user_u) && !empty($user)) $this->fk_user_u=trim($user->id);

		// Check parameters
		// Put here code to add control on parameters values
		
        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."chronodocs_entries(";
		
		$sql.= "ref,";
		$sql.= "title,";
		$sql.= "filename,";
		$sql.= "filename_orig,";
		$sql.= "filesize,";
		$sql.= "brief,";
		$sql.= "date_c,";
		$sql.= "date_u,";
		$sql.= "fk_chronodocs_type,";
		$sql.= "socid,";
		$sql.= "fk_status,";
		$sql.= "fk_user_c,";
		$sql.= "fk_user_u";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->ref)?'NULL':"'".addslashes($this->ref)."'").",";
		$sql.= " ".(! isset($this->title)?'NULL':"'".addslashes($this->title)."'").",";
		$sql.= " ".(! isset($this->filename)?'NULL':"'".addslashes($this->filename)."'").",";
		$sql.= " ".(! isset($this->filename_orig)?'NULL':"'".addslashes($this->filename_orig)."'").",";
		$sql.= " ".(! isset($this->filesize)?'NULL':"'".$this->filesize."'").",";
		$sql.= " ".(! isset($this->brief)?'NULL':"'".addslashes($this->brief)."'").",";
		$sql.= " ".(! isset($this->date_c) || strval($this->date_c)==''?'NULL':$this->db->idate($this->date_c)).",";
		$sql.= " ".(! isset($this->date_u) || strval($this->date_u)==''?'NULL':$this->db->idate($this->date_u)).",";
		$sql.= " ".(! isset($this->fk_chronodocs_type)?'NULL':"'".$this->fk_chronodocs_type."'").",";
		$sql.= " ".(! isset($this->socid)?'NULL':"'".$this->socid."'").",";
		$sql.= " ".(! isset($this->fk_status)?'NULL':"'".$this->fk_status."'").",";
		$sql.= " ".(! isset($this->fk_user_c)?'NULL':"'".$this->fk_user_c."'").",";
		$sql.= " ".(! isset($this->fk_user_u)?'NULL':"'".$this->fk_user_u."'")."";

        
		$sql.= ")";

		$this->db->begin();
		
	   	dolibarr_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."chronodocs_entries");
    
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.
	            
	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/interfaces.class.php");
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
		$sql.= " t.filename,";
		$sql.= " t.filename_orig,";
		$sql.= " t.filesize,";
		$sql.= " t.brief,";
		$sql.= " ".$this->db->pdate('t.date_c')." as date_c,";
		$sql.= " ".$this->db->pdate('t.date_u')." as date_u,";
		$sql.= " t.fk_chronodocs_type,";
		$sql.= " t.socid,";
		$sql.= " t.fk_status,";
		$sql.= " t.fk_user_c,";
		$sql.= " t.fk_user_u";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."chronodocs_entries as t";
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
				$this->filename = $obj->filename;
				$this->filename_orig = $obj->filename_orig;
				$this->filesize = $obj->filesize;
				$this->brief = $obj->brief;
				$this->date_c = $obj->date_c;
				$this->date_u = $obj->date_u;
				$this->fk_chronodocs_type = $obj->fk_chronodocs_type;
				$this->socid = $obj->socid;
				$this->fk_status = $obj->fk_status;
				$this->fk_user_c = $obj->fk_user_c;
				$this->fk_user_u = $obj->fk_user_u;

                
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
     *      \remarks  Be Aware to rename old chronodocs_entrie filedir with new ref when changing ref
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean parameters
        
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->title)) $this->title=trim($this->title);
		if (isset($this->filename)) $this->filename=trim($this->filename);
		if (isset($this->filename_orig)) $this->filename_orig=trim($this->filename_orig);
		if (isset($this->filesize)) $this->filesize=trim($this->filesize);
		if (isset($this->brief)) $this->brief=trim($this->brief);
		if (isset($this->fk_chronodocs_type)) $this->fk_chronodocs_type=trim($this->fk_chronodocs_type);
		if (isset($this->socid)) $this->socid=trim($this->socid);
		if (isset($this->fk_status)) $this->fk_status=trim($this->fk_status);
		if (isset($this->fk_user_c)) $this->fk_user_c=trim($this->fk_user_c);
		if (isset($this->fk_user_u)) $this->fk_user_u=trim($this->fk_user_u);

        //Overwrite date_u to now
        $this->date_u=time();
		if(!empty($user) && ($user->id)) $this->fk_user_u=trim($user->id);

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_entries SET";
        
		$sql.= " ref=".(isset($this->ref)?"'".addslashes($this->ref)."'":"null").",";
		$sql.= " title=".(isset($this->title)?"'".addslashes($this->title)."'":"null").",";
		$sql.= " filename=".(isset($this->filename)?"'".addslashes($this->filename)."'":"null").",";
		$sql.= " filename_orig=".(isset($this->filename_orig)?"'".addslashes($this->filename_orig)."'":"null").",";
		$sql.= " filesize=".(isset($this->filesize)?$this->filesize:"null").",";
		$sql.= " brief=".(isset($this->brief)?"'".addslashes($this->brief)."'":"null").",";
		$sql.= " date_c=".(strval($this->date_c)!='' ? "'".$this->db->idate($this->date_c)."'" : 'null').",";
		$sql.= " date_u=".(strval($this->date_u)!='' ? "'".$this->db->idate($this->date_u)."'" : 'null').",";
		$sql.= " fk_chronodocs_type=".(isset($this->fk_chronodocs_type)?$this->fk_chronodocs_type:"null").",";
		$sql.= " socid=".(isset($this->socid)?$this->socid:"null").",";
		$sql.= " fk_status=".(isset($this->fk_status)?$this->fk_status:"null").",";
		$sql.= " fk_user_c=".(isset($this->fk_user_c)?$this->fk_user_c:"null").",";
		$sql.= " fk_user_u=".(isset($this->fk_user_u)?$this->fk_user_u:"null")."";

        
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
	            //include_once(DOL_DOCUMENT_ROOT . "/core/interfaces.class.php");
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
		
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."chronodocs_entries";
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
		        //include_once(DOL_DOCUMENT_ROOT . "/core/interfaces.class.php");
		        //$interface=new Interfaces($this->db);
		        //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
		        //if ($result < 0) { $error++; $this->errors=$interface->errors; }
		        //// End call triggers
			}	
		}
		
		// Delete files
		if(! $error)
		{
			$dir = DOL_DATA_ROOT."/chronodocs/" . dol_string_nospecial($chronodocs->ref);
			if (file_exists($dir))
				{
					dolibarr_syslog(get_class($this)."::delete delete_dir:".$dir);
					if (!dol_delete_dir_recursive($dir))
					{
						$error++;
						$this->errors[]=$langs->trans("ErrorCanNotDeleteDir",$dir);
					}
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
		$this->filename='';
		$this->filename_orig='';
		$this->filesize='';
		$this->brief='';
		$this->date_c='';
		$this->date_u=time();
		$this->fk_chronodocs_type='';
		$this->socid='';
		$this->fk_status='';
		$this->fk_user_c='';
		$this->fk_user_u='';
		
	}
	
	function fetch_chronodocs_type()
	{
		if(empty($this->fk_chronodocs_type))
			return "NOTSET";
			
		$chronodocs_type=new Chronodocs_types($this->db);
		$result=$chronodocs_type->fetch($this->fk_chronodocs_type);
		$this->chronodocs_type=$chronodocs_type;
		return $result;
	}

	function set_brief($user,$newbrief,$notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean and fetch parameters
		$this->brief=trim($newbrief);

        //Overwrite date_u to now
        $this->date_u=time();
		$this->fk_user_u=trim($user->id);

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_entries SET";
        
		$sql.= " brief=".(isset($this->brief)?"'".addslashes($this->brief)."'":"null").",";
		$sql.= " date_u=".(strval($this->date_u)!='' ? "'".$this->db->idate($this->date_u)."'" : 'null').",";
		$sql.= " fk_user_u=".(isset($this->fk_user_u)?$this->fk_user_u:"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();
        
		dolibarr_syslog(get_class($this)."::set_brief sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.
				
	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/interfaces.class.php");
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
	            dolibarr_syslog(get_class($this)."::set_brief ".$errmsg, LOG_ERR);
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
     *      \brief      Update date_c
     *      \param      user        	User that modify
     *      \param      new_date_c       New date_c value
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, >0 if OK
     */
	function set_date_c($user,$new_date_c,$notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean and fetch parameters
		$this->date_c=trim($new_date_c);

        //Overwrite date_u to now
        $this->date_u=time();
		$this->fk_user_u=trim($user->id);

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_entries SET";
        
		$sql.= " date_c=".(strval($this->date_c)!='' ? "'".$this->db->idate($this->date_c)."'" : 'null').",";
		$sql.= " date_u=".(strval($this->date_u)!='' ? "'".$this->db->idate($this->date_u)."'" : 'null').",";
		$sql.= " fk_user_u=".(isset($this->fk_user_u)?$this->fk_user_u:"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();
        
		dolibarr_syslog(get_class($this)."::set_date_c sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.
				
	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/interfaces.class.php");
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
	            dolibarr_syslog(get_class($this)."::set_date_c ".$errmsg, LOG_ERR);
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
     *      \brief      Update title
     *      \param      user        	User that modify
     *      \param      newtitle       New title value
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, >0 if OK
     */
	 function set_title($user,$newtitle,$notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean and fetch parameters
		$this->title=trim($newtitle);

        //Overwrite date_u to now
        $this->date_u=time();
		$this->fk_user_u=trim($user->id);

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_entries SET";
        
		$sql.= " title=".(isset($this->title)?"'".addslashes($this->title)."'":"null").",";
		$sql.= " date_u=".(strval($this->date_u)!='' ? "'".$this->db->idate($this->date_u)."'" : 'null').",";
		$sql.= " fk_user_u=".(isset($this->fk_user_u)?$this->fk_user_u:"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();
        
		dolibarr_syslog(get_class($this)."::set_title sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.
				
	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/interfaces.class.php");
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
	            dolibarr_syslog(get_class($this)."::set_title ".$errmsg, LOG_ERR);
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
     *      \brief      Update ref
     *      \param      user        	User that modify
     *      \param      newref       New ref value
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, >0 if OK
     *      \remarks  Be Aware to rename old chronodocs_entrie filedir with new ref
     */
	function set_ref($user,$newref,$notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean and fetch parameters
		$this->ref=trim($newref);

        //Overwrite date_u to now
        $this->date_u=time();
		$this->fk_user_u=trim($user->id);

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_entries SET";
        
		$sql.= " ref=".(isset($this->ref)?"'".addslashes($this->ref)."'":"null").",";
		$sql.= " date_u=".(strval($this->date_u)!='' ? "'".$this->db->idate($this->date_u)."'" : 'null').",";
		$sql.= " fk_user_u=".(isset($this->fk_user_u)?$this->fk_user_u:"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();
        
		dolibarr_syslog(get_class($this)."::set_ref sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.
				
	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/interfaces.class.php");
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
	            dolibarr_syslog(get_class($this)."::set_ref ".$errmsg, LOG_ERR);
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
     *      \brief      Update set_date_u with now value
     *      \param      user        	User that modify
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, >0 if OK
     */
	function set_date_u($user,$notrigger=0)
	{
    	global $conf, $langs;
		$error=0;
    	
		// Clean and fetch parameters
		// Put here code to clean parameters values
		
        //Overwrite date_u to now
        $this->date_u=time();
		$this->fk_user_u=trim($user->id);

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_entries SET";
        
		$sql.= " date_u=".(strval($this->date_u)!='' ? "'".$this->db->idate($this->date_u)."'" : 'null').",";
		$sql.= " fk_user_u=".(isset($this->fk_user_u)?$this->fk_user_u:"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();
        
		dolibarr_syslog(get_class($this)."::set_date_u sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.
				
	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/interfaces.class.php");
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
	            dolibarr_syslog(get_class($this)."::set_date_u ".$errmsg, LOG_ERR);
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
	 *      \brief      Information sur l'objet entr�e chronodoc
	 *      \param      id      id du chronodoc
	 */
	function info($id='')
	{
		if(empty($id))
			$id=$this->id;
			
		$sql = "SELECT c.rowid, c.ref, ".$this->db->pdate("date_c")." as date_c, ".$this->db->pdate("date_u")." as date_u,";
		$sql.= " fk_user_c, fk_user_u";
		$sql.= " FROM ".MAIN_DB_PREFIX."chronodocs_entries as c";
		$sql.= " WHERE c.rowid = ".$id;
		
		dolibarr_syslog(get_class($this)."::info sql=".$sql, LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);

				$this->id = $obj->rowid;

				if ($obj->fk_user_c) {
					$cuser = new User($this->db, $obj->fk_user_c);
					$cuser->fetch();
					$this->user_creation     = $cuser;
				}

				if ($obj->fk_user_u) {
					$muser = new User($this->db, $obj->fk_user_u);
					$muser->fetch();
					$this->user_modification = $muser;
				}
				$this->ref			     = $obj->ref;
				$this->date_creation     = $obj->date_c;
				$this->date_modification = $obj->date_u;
			}

			$this->db->free($result);

		}
		else
		{
			dolibarr_print_error($this->db);
		}
	}
	
	/**
	 *      \brief      Get a array of object giving data (nom,socid,ref,dp,date_u,fichid,title,fk_chronodocs_type + socid,fk_user)  on chronodoc found whith given search parametters 
	 *      \param      limit      max result size
	 *      \param      offset (default: 0)
	 *      \param      sortfield (default: f.date_c)
	 *      \param      sortorder (default: DESC)
	 *      \param      socid    if not null, restrict to soc with given socid (default: 0)
	 *      \param      search_ref    if not empty, restrict to chronodoc containing given ref (default: empty)
	 *      \param      search_societe    if not empty, restrict to soc containing given name (default: empty)
	 *      \param      search_title    if not empty, restrict to chronodoc containing given title (default: empty)
	 *      \param      year    if not null, restrict to chronodoc created during given year (default: 0)
	 *      \param      month    if in 1-12, restrict to chronodoc created during given month of given year (default: 0)
	 *      \param      fk_chronodocs_type    if not null, restrict to chronodoc with given chronodocs_type  id(default: 0)
	 */
	function get_list($limit,$offset="0",$sortfield="f.date_c",$sortorder="DESC",$socid=0,$search_ref="",$search_societe="",$search_title="",$year=0,$month=0,$fk_chronodocs_type=0)
	{
		global $user, $conf, $langs, $db;
		
		$tab_objp=array();
		
		$sql = "SELECT s.nom,s.rowid as socid, f.ref,".$db->pdate("f.date_c")." as dp,".$db->pdate("f.date_u")." as date_u, f.rowid as fichid, f.title, f.fk_chronodocs_type";
		//if (!$user->rights->societe->client->voir && !$socid) $sql .= ", sc.socid, sc.fk_user";
		$sql.= " FROM ".MAIN_DB_PREFIX."societe as s, ".MAIN_DB_PREFIX."chronodocs_entries as f ";
		if (!$user->rights->societe->client->voir && !$socid) $sql .= ", ".MAIN_DB_PREFIX."societe_commerciaux as sc";
		$sql.= " WHERE f.socid = s.rowid ";
		if (!$user->rights->societe->client->voir && !$socid) $sql .= " AND s.rowid = sc.fk_soc AND sc.fk_user = " .$user->id;
		if ($socid > 0)
		{
			$sql .= " AND s.rowid = " . $socid;
		}

		//filtres
		if ($month > 0 && $month <= 12)
		{
		if ($year > 0)
			$sql .= " AND date_format(f.date_c, '%Y-%m') = '$year-$month'";
		  else
			$sql .= " AND date_format(f.date_c, '%m') = '$month'";
		}
		if ($year > 0)
			$sql .= " AND date_format(f.date_c, '%Y') = $year";
			
		if (!empty($search_ref))
		{
			$sql .= " AND f.ref LIKE '%".addslashes($search_ref)."%'";
		}
		if (!empty($search_societe))
		{
			$sql .= " AND s.nom LIKE '%".addslashes($search_societe)."%'";
		}
		
		if (!empty($search_title))
		{
			$sql .= " AND f.title LIKE '%".addslashes($search_title)."%'";
		}
		
		if ($fk_chronodocs_type > 0)
		{
		$sql .= " AND f.fk_chronodocs_type = " . $fk_chronodocs_type;
		}
		
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
	 *      \brief      Nb de chronodoc
	 *      \param      socid  si non nul, restreint � la soc donn�e(defaut: 0)
	 *      \param      fk_chronodocs_type  si non nul, id du type de chronodoc (defaut: 0)
	 */	
	function get_nb_chronodocs($socid=0,$fk_chronodocs_type=0)
	{
		$where="";
		
		$sql = "SELECT COUNT(*) as nbchronodocs";
		$sql.= " FROM ".MAIN_DB_PREFIX."chronodocs_entries";
		if(!empty($fk_chronodocs_type))
			$where.= " fk_chronodocs_type = ".$fk_chronodocs_type;
			
		if(!empty($socid))
		{
			if(!empty($where))
				$where.= " AND ";
				
			$where.= " socid = ".$socid;
		}
		if(!empty($where))
			$sql.=" WHERE ".$where;
			
		dolibarr_syslog(get_class($this)."::get_nb_chronodocs sql=".$sql, LOG_DEBUG);
		$result=$this->db->query($sql);
		if ($result)
		{
			if ($this->db->num_rows($result))
			{
				$obj = $this->db->fetch_object($result);
				
				$nbchronodocs = $obj->nbchronodocs;
			}

			$this->db->free($result);
			
			return $nbchronodocs;
		}
		else
		{
			dolibarr_print_error($this->db);
		}
	}
	
	/**
    *		\brief      Fetch propfields data object list in $this->propfields
    *		\param      fk_status          If set, restrict to given Status
    *		\param       limit  		nb max propfields
    *		\return	int			<0 if KO, >0 if OK
    */
    function fetch_chronodocs_propvalues($fk_status='',$limit="100")
	{
		$chronodocs_propvalues = new Chronodocs_propvalues($this->db);
		$this->propvalues=$chronodocs_propvalues->get_list($limit,"0","p.ref","ASC",0,$this->id,$this->fk_chronodocs_type,$fk_status);
		if(is_array($this->propvalues)) //OK
			return sizeOf($this->propvalues);
		else
			return -1;
	}
	
}
?>