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
        \file       chronodocs/chronodocs_types.class.php
        \ingroup    chronodocs
        \brief      CRUD class file for chronodocs types (Create/Read/Update/Delete)
		\version    $Id: chronodocs_types.class.php,v 1.6 2010/04/21 22:14:17 hregis Exp $
		\author	Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
		\remarks	Initialy built by build_class_from_table on 2008-08-18 17:28
*/

// Put here all includes required by your class file
require_once(DOL_DOCUMENT_ROOT."/core/commonobject.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_propfields.class.php");


/**
        \class      Chronodocs_types
        \brief       CRUD class for chronodocs types (Create/Read/Update/Delete)
		\remarks	Initialy built by build_class_from_table on 2008-08-18 17:28
*/
class Chronodocs_types  extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='chronodocs_types';			//!< Id that identify managed objects
	var $table_element='chronodocs_types';	//!< Name of table without prefix where object is stored
    
    var $id;
    
	var $propfields=null;			// Tableau contenant les objets chronodocs_propfields correspondants  (a charger par fetch_chronodocs_propfields)
	
	var $ref;
	var $title;
	var $brief;
	var $filename;
	var $date_c='';
	var $date_u='';
	var $fk_status;
	var $fk_user_c;
	var $fk_user_u;

    

	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    function Chronodocs_types($DB) 
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
		if (isset($this->filename)) $this->filename=trim($this->filename);
		if (isset($this->fk_status)) $this->fk_status=trim($this->fk_status);
		if (isset($this->fk_user_c)) $this->fk_user_c=trim($this->fk_user_c);
		if (isset($this->fk_user_u)) $this->fk_user_u=trim($this->fk_user_u);

        

		// Check parameters
		// Put here code to add control on parameters values
		
		//Set date_u to now if not set
        if (!isset($this->date_u)) $this->date_u=time();
		if (!isset($this->fk_user_u) && !empty($user)) $this->fk_user_u=trim($user->id);
		
        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."chronodocs_types(";
		
		$sql.= "ref,";
		$sql.= "title,";
		$sql.= "brief,";
		$sql.= "filename,";
		$sql.= "date_c,";
		$sql.= "date_u,";
		$sql.= "fk_status,";
		$sql.= "fk_user_c,";
		$sql.= "fk_user_u";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->ref)?'NULL':"'".addslashes($this->ref)."'").",";
		$sql.= " ".(! isset($this->title)?'NULL':"'".addslashes($this->title)."'").",";
		$sql.= " ".(! isset($this->brief)?'NULL':"'".addslashes($this->brief)."'").",";
		$sql.= " ".(! isset($this->filename)?'NULL':"'".addslashes($this->filename)."'").",";
		$sql.= " ".(! isset($this->date_c) || strval($this->date_c)==''?'NULL':$this->db->idate($this->date_c)).",";
		$sql.= " ".(! isset($this->date_u) || strval($this->date_u)==''?'NULL':$this->db->idate($this->date_u)).",";
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
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."chronodocs_types");
    
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
		$sql.= " t.brief,";
		$sql.= " t.filename,";
		$sql.= " ".$this->db->pdate('t.date_c')." as date_c,";
		$sql.= " ".$this->db->pdate('t.date_u')." as date_u,";
		$sql.= " t.fk_status,";
		$sql.= " t.fk_user_c,";
		$sql.= " t.fk_user_u";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."chronodocs_types as t";
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
				$this->filename = $obj->filename;
				$this->date_c = $obj->date_c;
				$this->date_u = $obj->date_u;
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
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean parameters
        
		if (isset($this->ref)) $this->ref=trim($this->ref);
		if (isset($this->title)) $this->title=trim($this->title);
		if (isset($this->brief)) $this->brief=trim($this->brief);
		if (isset($this->filename)) $this->filename=trim($this->filename);
		if (isset($this->fk_status)) $this->fk_status=trim($this->fk_status);
		if (isset($this->fk_user_c)) $this->fk_user_c=trim($this->fk_user_c);
		if (isset($this->fk_user_u)) $this->fk_user_u=trim($this->fk_user_u);

        //Overwrite date_u to now
        $this->date_u=time();
		if(!empty($user) && ($user->id)) $this->fk_user_u=trim($user->id);

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_types SET";
        
		$sql.= " ref=".(isset($this->ref)?"'".addslashes($this->ref)."'":"null").",";
		$sql.= " title=".(isset($this->title)?"'".addslashes($this->title)."'":"null").",";
		$sql.= " brief=".(isset($this->brief)?"'".addslashes($this->brief)."'":"null").",";
		$sql.= " filename=".(isset($this->filename)?"'".addslashes($this->filename)."'":"null").",";
		$sql.= " date_c=".(strval($this->date_c)!='' ? "'".$this->db->idate($this->date_c)."'" : 'null').",";
		$sql.= " date_u=".(strval($this->date_u)!='' ? "'".$this->db->idate($this->date_u)."'" : 'null').",";
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
		
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."chronodocs_types";
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
			$upload_dir=DOL_DATA_ROOT."/chronodocs/types";
			if($this->filename && is_dir($upload_dir))
			{   // Delete old file
				$result=dol_delete_file($upload_dir . "/" .$this->filename);
				if (! $result) 
				{
					$error++;
					$this->errors[]=$langs->trans("ErrorCanNotDeleteFile",$upload_dir . "/" .$this->filename);
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
		$this->brief='';
		$this->filename='';
		$this->date_c='';
		$this->date_u='';
		$this->fk_status='';
		$this->fk_user_c='';
		$this->fk_user_u='';
		
	}
	
 /** 
     *      \brief       Renvoi la liste des types actifs
     *      \param      db	database
     *      \param      status	 (optional) status of types searched
     *	   \return     array		Liste des types
     */
    function liste_types($db,$status='0')
    {
        $liste=array();
        $sql ="SELECT rowid as id, title, ref";
        $sql.=" FROM ".MAIN_DB_PREFIX."chronodocs_types";
        $sql.=" WHERE fk_status = '".$status."'";
        
        $resql = $db->query($sql);
        if ($resql)
        {
            $num = $db->num_rows($resql);
            $i = 0;
            while ($i < $num)
            {
                $row = $db->fetch_row($resql);
                $liste[$row[0]]=$row[1];
				if(!empty($row[2])) 
					$liste[$row[0]].=' ('.$row[2].')';
                $i++;
            }
        }
        else
        {
            return -1;
        }
        return $liste;
    }
	
	
 /** 
     *      \brief       Renvoi le nom sous forme d'url
     *      \param      withpicto (optional) 	boolean
     *      \param      option	 (optional) status of types searched
     *	   \param     maxlen (optional) 
     *       \return	string result
     */
	function getNomUrl($withpicto=0,$option='',$maxlen=0)
	{
		global $langs;

		$result='';

		
		$lien = '<a href="'.DOL_URL_ROOT.'/chronodocs/types.php?id='.$this->id.'">';
		$lienfin='</a>';
		

		if ($withpicto) $result.=($lien.img_object($langs->trans("ShowChronodocType").': '.$this->title,'generic').$lienfin.' ');
		$result.=$lien.($maxlen?dolibarr_trunc($this->title,$maxlen):$this->title).$lienfin;
		return $result;
	}

/**
     *      \brief      Update brief
     *      \param      user        	User that modify
     *      \param      newbrief       New bref value
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, >0 if OK
     */
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
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_types SET";
        
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
     *      \brief      Update title
     *      \param      user        	User that modify
     *      \param      newtitle         New title value
     *      \param      notrigger      0=launch triggers after, 1=disable triggers
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
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_types SET";
        
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
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_types SET";
        
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
     *      \brief      Update filename
     *      \param      user        	User that modify
     *      \param      newref       New filename value
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, >0 if OK
     */
	function set_filename($user,$newfilename,$notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean and fetch parameters
		$this->filename=trim($newfilename);

        //Overwrite date_u to now
        $this->date_u=time();
		$this->fk_user_u=trim($user->id);

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_types SET";
        
		$sql.= " filename=".(isset($this->filename)?"'".addslashes($this->filename)."'":"null").",";
		$sql.= " date_u=".(strval($this->date_u)!='' ? "'".$this->db->idate($this->date_u)."'" : 'null').",";
		$sql.= " fk_user_u=".(isset($this->fk_user_u)?$this->fk_user_u:"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();
        
		dolibarr_syslog(get_class($this)."::set_filename sql=".$sql, LOG_DEBUG);
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
	            dolibarr_syslog(get_class($this)."::set_filename ".$errmsg, LOG_ERR);
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
        $sql = "UPDATE ".MAIN_DB_PREFIX."chronodocs_types SET";
        
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
	 *      \brief      Information sur l'objet type de chronodoc
	 *      \param      id      id du type de chronodoc
	 */	
	function info($id='')
	{
		if(empty($id))
			$id=$this->id;
		
		$sql = "SELECT c.rowid, c.ref, ".$this->db->pdate("date_c")." as date_c, ".$this->db->pdate("date_u")." as date_u,";
		$sql.= " fk_user_c, fk_user_u";
		$sql.= " FROM ".MAIN_DB_PREFIX."chronodocs_types as c";
		$sql.= " WHERE c.rowid = ".$id;

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
	 *      \brief      Nb de chronodoc du type donn�
	 *      \param      id      id du type de chronodoc (defaut: id courant)
	 */	
	function get_nb_chronodocs($id='')
	{
		if(empty($id))
			$id=$this->id;
			
		$sql = "SELECT COUNT(*) as nbchronodocs";
		$sql.= " FROM ".MAIN_DB_PREFIX."chronodocs_entries";
		$sql.= " WHERE fk_chronodocs_type = ".$id;
			
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
	 *      \brief      Get a array of object giving data (dp,date_u,id,title) on chronodoc type found whith given search parametters 
	 *      \param      limit      max result size
	 *      \param      offset (default: 0)
	 *      \param      sortfield (default: f.date_c)
	 *      \param      sortorder (default: DESC)
	 *      \param      search_ref    if not empty, restrict to chronodoc containing given ref (default: empty)
	 *      \param      search_title    if not empty, restrict to chronodoc containing given title (default: empty)
	 */
	function get_list($limit=0,$offset="0",$sortfield="f.date_c",$sortorder="DESC",$search_ref="",$search_title="")
	{
		global $user, $conf, $langs;
		$db = $this->db;
		
		$sql = "SELECT f.ref,".$db->pdate("f.date_c")." as dp,".$db->pdate("f.date_u")." as date_u, f.rowid as id, f.title";
		$sql.= " FROM ".MAIN_DB_PREFIX."chronodocs_types as f ";
		
		if (!empty($search_ref))
		{
			$sql .= " AND f.ref LIKE '%".addslashes($search_ref)."%'";
		}
		
		if (!empty($search_title))
		{
			$sql .= " AND f.title LIKE '%".addslashes($search_title)."%'";
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
    *		\brief      Fetch propfields data object list in $this->propfields
    *		\param      fk_status          If set, restrict to given Status
    *		\param       limit  		nb max propfields
    *		\return	int			<0 if KO, >0 if OK
    */
    function fetch_chronodocs_propfields($fk_status='',$limit="100")
	{
		$chronodocs_propfields = new Chronodocs_propfields($this->db);
		$this->propfields=$chronodocs_propfields->get_list($limit,"0","p.ref","ASC",$this->id,$fk_status);
		if(is_array($this->propfields)) //OK
			return sizeOf($this->propfields);
		else
			return -1;
	}
	
 /** 
     *      \brief       Renvoi la liste des types actifs
     *      \param      otherprops array (optional)	
     *	   \return     array		Liste des types
     */
	function liste_auto_propfields_ref($otherprops=array())
	{
		$auto_propfields_ref=$otherprops;
		$auto_propfields_ref[]="#R_SOCIETE#";
		$auto_propfields_ref[]="#R_TITRE_NOTE#";
		$auto_propfields_ref[]="#R_REDACTEUR#";
		$auto_propfields_ref[]="#R_TRIGRAMME#";
		$auto_propfields_ref[]="#R_DT_REDAC#";
		$auto_propfields_ref[]="#R_NUM_CHRONO#";
		$auto_propfields_ref[]="#R_CUSTOMER#";
	}
}
?>