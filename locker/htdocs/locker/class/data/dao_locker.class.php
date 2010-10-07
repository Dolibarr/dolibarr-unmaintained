<?php
//Start of user code fichier htdocs/product/stock/dao_locker.class.php

/* Copyright (C) 2007-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
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

/**
        \file       htdocs/product/stock/dao_locker.class.php
        \ingroup    Locker
        \brief      This file is an example for a class file
		\version    2.4
		\author		 
*/

// Put here all includes required by your class file

/**
        \class      dao_locker
        \brief      Put here description of your class
		\remarks	Initialy built by build_class_from_table on 2008-06-06 09:29
*/
class dao_locker // extends CommonObject
{
	private $db;							//!< To store db handler
	private $error;							//!< To return error code (or message)
	private $errors=array();
	private $lastquery;				//!< To return several error codes (or messages)
	//private $element='locker';			//!< Id that identify managed objects
	//private $table_element='locker';	//!< Name of table without prefix where object is stored
    
    private $id;
    
    private $code;
    private $libelle;
    private $fk_entrepot;


	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    public function dao_locker($DB) 
    {
        $this->db = $DB;
        return 1;
    }

	public function getId(){return $this->id;}
	public function setId($value=''){$this->id = $value;}
	
    public function getCode() {return $this->code;}
    public function setCode($value='') {$this->code = $value;}
    public function getLibelle() {return $this->libelle;}
    public function setLibelle($value='') {$this->libelle = $value;}
    public function getFk_entrepot() {return $this->fk_entrepot;}
    public function setFk_entrepot($value='') {$this->fk_entrepot = $value;}
    
    public function getErrors(){ return $this->errors; }
    public function getError(){ return $this->error; }
    public function getLastquery(){ return $this->db->lastquery(); }
	
    /**
     *      \brief      Create in database
     *      \return     int         <0 si ko, 0 si ok
     */
    public function create()
    {
    	global $conf, $langs;
    	
		// Clean parameters 
		
	    $this->code=trim($this->code);
	    $this->libelle=trim($this->libelle);
		$this->fk_entrepot=trim($this->fk_entrepot);
        

		// Check parameters
		// Put here code to add control on parameters values
		
        // Insert request
        
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."locker(";
		
		$sql.= "code,";
		$sql.= "libelle, ";
		$sql.= "fk_entrepot";
		
        $sql.= ") VALUES (";

	    $sql.= " ".(! isset($this->code)?'NULL':"'".$this->code."'").",";
	    $sql.= " ".(! isset($this->libelle)?'NULL':"'".$this->libelle."'").",";
		$sql.= " ".((($this->fk_entrepot)=="")?'NULL':"'".$this->fk_entrepot."'");
		       
		$sql.= ")";

	   	dolibarr_syslog("dao_locker::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        
        if ($resql)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."locker");
    
            // Appel des triggers
            include_once(DOL_DOCUMENT_ROOT . '/core/class/interfaces.class.php');
            $interface=new Interfaces($this->db);
            $result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
            if ($result < 0) { $error++; $this->errors=$interface->errors; }
            // Fin appel triggers

            return $this->id;
        }
        else
        {
            $this->error="Error ".$this->db->lasterror();
            $this->lastquery = $this->db->lastquery();
            dolibarr_syslog("dao_locker::create ".$this->error, LOG_ERR);
            return -1;
        }
    }

    /*
     *      \brief      Update database
     *      \param      notrigger	    0=no, 1=yes (no update trigger)
     *      \return     int         	<0 if KO, 0 if OK
     */
    public function update($notrigger=0)
    {
    	global $conf, $langs;
    	
		// Clean parameters
	    $this->code=trim($this->code);
	    $this->libelle=trim($this->libelle);
		$this->fk_entrepot=trim($this->fk_entrepot);
       

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."locker SET";
        
	   	$sql.= " code='".addslashes($this->code)."',";
	   	$sql.= " libelle='".addslashes($this->libelle)."',";
 		$sql.= " fk_entrepot=".((($this->fk_entrepot)=="")?'NULL':"'".$this->fk_entrepot."'")."";
   
        
        $sql.= " WHERE rowid=".$this->id;


        dolibarr_syslog("dao_locker::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (! $resql)
        {
            $this->error="Error ".$this->db->lasterror();
            $this->lastquery = $this->db->lastquery();
            dolibarr_syslog("dao_locker::update ".$this->error, LOG_ERR);
            return -1;
        }

		if (! $notrigger)
		{
            // Appel des triggers
            include_once(DOL_DOCUMENT_ROOT . "/interfaces.class.php");
            $interface=new Interfaces($this->db);
            $result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
            if ($result < 0) { $error++; $this->errors=$interface->errors; }
            // Fin appel triggers
    	}

        return 0;
    }
  
  
    /*
     *    \brief      Load object in memory from database
     *    \param      rowid       rowid object
     *    \return     int         <0 if KO, 0 if OK
     */
    public function fetch($id)
    {
    	global $langs;
    	
        $sql = "SELECT";
        
	   	$sql.= " t.rowid,";
	   	$sql.= " t.code,";
	   	$sql.= " t.libelle	    , ";
		$sql.= " t.fk_entrepot";
      
		
        $sql.= " FROM ".MAIN_DB_PREFIX."locker as t";
        $sql.= " WHERE t.rowid = ".$id;
    
    	dolibarr_syslog("locker::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
    
				$this->id = $obj->rowid;
				$this->code = $obj->code;
				$this->libelle = $obj->libelle;
				$this->fk_entrepot= $obj->fk_entrepot;

            }
            else
            {
            	$this->error="Error ".$this->db->lasterror();
	            dolibarr_syslog("dao_locker::fetch ".$this->error, LOG_ERR);
	            return -2;
            }
            $this->db->free($resql);
            
            return 0;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dolibarr_syslog("dao_locker::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }
    
    
 	/*
	*   \brief      Delete object in database
	*	\return		int			<0 if KO, 0 if OK
	*/
	public function delete($id='')
	{
		global $conf, $langs;
		
		if($id!='')$this->id = $id;
	
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."locker";
		$sql.= " WHERE rowid=".$this->id;
	
	   	dolibarr_syslog("dao_locker::delete sql=".$sql);
		$resql = $this->db->query($sql);
		if (! $resql)
		{
			$this->error="Error ".$this->db->lasterror();
            dolibarr_syslog("dao_locker::delete ".$this->error, LOG_ERR);
			return -1;
		}
	
        // Appel des triggers
        include_once(DOL_DOCUMENT_ROOT . "/interfaces.class.php");
        $interface=new Interfaces($this->db);
        $result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
        if ($result < 0) { $error++; $this->errors=$interface->errors; }
        // Fin appel triggers

		return 0;
	}

  
	/**
	 *		\brief		Initialise object with example values
	 *		\remarks	id must be 0 if object instance is a specimen.
	 */
	public function initAsSpecimen()
	{
		$this->id=0;
		
	    $this->code='';
	    $this->libelle='';
		$this->fk_entrepot='';
		
	}
	

	/**
	 *		\brief		Execute a select query
	 *		\param		$db		object to manage the database
	 *		\param		$fields	array containing all fields that must be selected
	 *		\param		$where	condition of the WHERE clause (ie. "t.label = 'bar'")
	 *		\param		$order	condition of the ORDER BY clause (ie. "t.rowid DESC")
	 *		\return		the result as a query result.
	 */
	public static function select($DB,$fields='',$where='',$order='')
	{
   	global $langs;
    	
        $sql = "SELECT ";
        
        if( $fields == ''){
	   	$sql.= " t.rowid,";
	   	$sql.= " t.code,";
	   	$sql.= " t.libelle	    , ";
		$sql.= " t.fk_entrepot";
		} else {
			$sql.= "t.".implode(", t.", $fields) ;
		}
		
        $sql.= " FROM ".MAIN_DB_PREFIX."locker as t";
        if($where != '')$sql.= " WHERE ".$where;
        if($order != '')$sql.= " ORDER BY ".$order;
    
    	dolibarr_syslog("dao_locker::fetch sql=".$sql, LOG_DEBUG);
        
       
        return $DB->query($sql);
       
	
	
	}
	
	/**
	 *		\brief				Execute select a query
	 *		\param		$query  the query
	 *		\return				the result as a query result.
	 */
	public static function selectQuery($DB, $query,$where='',$order='')
	{
   	global $langs;
    	
        $sql = $query;
   
   		if($where != '')$sql.= " WHERE ".$where;
        if($order != '')$sql.= " ORDER BY ".$order;
        
    	dolibarr_syslog("dao_locker::fetch sql=".$sql, LOG_DEBUG);
        
       
        return $DB->query($sql);

	
	}
	

}
//End of user code
?>
