<?php
//Start of user code fichier htdocs/product_composition/dao_product_composition.class.php


/* Copyright (C) 2007-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2008 Samuel  Bouchet <samuel.bouchet@auguria.net>
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

/**
        \file       htdocs/product_composition/dao_product_composition.class.php
        \ingroup    product_composition
        \brief      This file is an example for a class file
		\version    $Id: dao_product_composition.class.php,v 1.4 2010/04/21 16:29:48 hregis Exp $
		\author		Patrick Raguin
*/

// Put here all includes required by your class file

/**
        \class      dao_product_composition
        \brief      Put here description of your class
		\remarks	Initialy built by build_class_from_table on 2008-06-06 09:29
*/
class dao_product_composition // extends CommonObject
{
	private $db;							//!< To store db handler
	private $error;							//!< To return error code (or message)
	private $errors=array();
	private $lastquery;				//!< To return several error codes (or messages)
	//private $element='product_composition';			//!< Id that identify managed objects
	//private $table_element='product_composition';	//!< Name of table without prefix where object is stored
    
    private $id;
    
    private $fk_product_composition;
    private $fk_product;
    private $qte;
    private $etat_stock;
    private $buying_cost;


	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    public function dao_product_composition($DB) 
    {
        $this->db = $DB;
        return 1;
    }

	public function getId(){return $this->id;}
	public function setId($value=''){$this->id = $value;}
	
    public function getFk_product_composition() {return $this->fk_product_composition;}
    public function setFk_product_composition($value='') {$this->fk_product_composition = $value;}
    public function getFk_product() {return $this->fk_product;}
    public function setFk_product($value='') {$this->fk_product = $value;}
	public function getQte() {return $this->qte;}
    public function setQte($value='') {$this->qte = $value;}
	public function getEtat_stock() {return $this->etat_stock;}
    public function setEtat_stock($value='') {$this->etat_stock = $value;}   
	public function getBuying_cost() {return $this->buying_cost;}
    public function setBuying_cost($value='') {$this->buying_cost = $value;}   
        
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
		
		$this->fk_product_composition=trim($this->fk_product_composition);
		$this->fk_product=trim($this->fk_product);
        

		// Check parameters
		// Put here code to add control on parameters values
		
        // Insert request
        
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_composition(";
		
 		$sql.= "fk_product_composition,";
		$sql.= "fk_product,";
		$sql.= "qte,";
		$sql.= "etat_stock,";
		$sql.= "buying_cost";
		
        $sql.= ") VALUES (";

		$sql.= " ".((($this->fk_product_composition)=="")?'NULL':"'".$this->fk_product_composition."'").",";
		$sql.= " ".((($this->fk_product)=="")?'NULL':"'".$this->fk_product."'").",";
		$sql.= " ".(! isset($this->qte)?0:"'".$this->qte."',"); 
		$sql.= " ".(! isset($this->etat_stock)?0:"'".$this->etat_stock."',");   
		$sql.= " ".(! isset($this->buying_cost)?1:"'".$this->buying_cost."'");        
		$sql.= ")";

	   	dolibarr_syslog("dao_product_composition::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        
        if ($resql)
        {
    
            // Appel des triggers
            /*include_once(DOL_DOCUMENT_ROOT . "/workflow/class/interfaces.class.php");
            $interface=new Interfaces($this->db);
            $result=$interface->call_workflow('MYOBJECT_CREATE',$this,$user,$langs,$conf);
            if ($result < 0) { $error++; $this->errors=$interface->errors; }
            */
            // Fin appel triggers

            return $this->id;
        }
        else
        {
            $this->error="Error ".$this->db->lasterror();
            $this->lastquery = $this->db->lastquery();
            dolibarr_syslog("dao_product_composition::create ".$this->error, LOG_ERR);
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
		$this->fk_product_composition=trim($this->fk_product_composition);
		$this->fk_product=trim($this->fk_product);
       

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."product_composition SET";
        
		$sql.= " fk_product_composition=".((($this->fk_product_composition)=="")?'NULL':"'".$this->fk_product_composition."'").",";
		$sql.= " fk_product=".((($this->fk_product)=="")?'NULL':"'".$this->fk_product."'").",";
   		$sql.= " qte=".((($this->qte)=="")?'0':"'".$this->qte."'").",";
   		$sql.= " etat_stock=".(! isset($this->etat_stock)?1:"'".$this->etat_stock."'").",";
        $sql.= " buying_cost=".(! isset($this->buying_cost)?1:"'".$this->buying_cost."'"); 
        $sql.= " WHERE rowid=".$this->id;

        dolibarr_syslog("dao_product_composition::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (! $resql)
        {
            $this->error="Error ".$this->db->lasterror();
            $this->lastquery = $this->db->lastquery();
            dolibarr_syslog("dao_product_composition::update ".$this->error, LOG_ERR);
            return -1;
        }

		if (! $notrigger)
		{
            // Appel des triggers
            /*include_once(DOL_DOCUMENT_ROOT . "/workflow/class/interfaces.class.php");
            $interface=new Interfaces($this->db);
            $result=$interface->call_workflow('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
            if ($result < 0) { $error++; $this->errors=$interface->errors; }
            */
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
        
	   	$sql.= " t.rowid	    , ";
		$sql.= " t.fk_product_composition,";
		$sql.= " t.fk_product,";
		$sql.= " t.qte,";
      	$sql.= " t.etat_stock,";
      	$sql.= " t.buying_cost";
		
        $sql.= " FROM ".MAIN_DB_PREFIX."product_composition as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dolibarr_syslog("product_composition::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
    
				$this->id = $obj->rowid;
				$this->fk_product_composition = $obj->fk_product_composition;
				$this->fk_product = $obj->fk_product;
				$this->qte = $obj->qte;
    			$this->etat_stock = $obj->etat_stock;
    			$this->buying_cost = $obj->buying_cost;

                
            }
            $this->db->free($resql);
            
            return 0;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dolibarr_syslog("dao_product_composition::update ".$this->error, LOG_ERR);
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
	
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."product_composition";
		$sql.= " WHERE rowid=".$this->id;
	
	   	dolibarr_syslog("dao_product_composition::delete sql=".$sql);
		$resql = $this->db->query($sql);
		if (! $resql)
		{
			$this->error="Error ".$this->db->lasterror();
            dolibarr_syslog("dao_product_composition::delete ".$this->error, LOG_ERR);
			return -1;
		}
	
        // Appel des triggers
        /*include_once(DOL_DOCUMENT_ROOT . "/workflow/class/interfaces.class.php");
        $interface=new Interfaces($this->db);
        $result=$interface->call_workflow('MYOBJECT_DELETE',$this,$user,$langs,$conf);
        if ($result < 0) { $error++; $this->errors=$interface->errors; }
        */
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
		
		$this->fk_product_composition='';
		$this->fk_product='';
		
	}
	

	/**
	 *		\brief		Execute a select query
	 *		\param		$where	condition of the WHERE clause (ie. "t.label = 'bar'")
	 *		\param		$order	condition of the ORDER BY clause (ie. "t.rowid DESC")
	 *		\return		the result as a query result.
	 */
	public static function select($DB,$where='',$order='')
	{
   		global $langs;
    	
        $sql = "SELECT";
	   	$sql.= " t.rowid,";
		$sql.= " t.fk_product_composition,";
		$sql.= " t.fk_product,";
		$sql.= " t.qte,";
		$sql.= " t.etat_stock,";
		$sql.= " t.buying_cost";

        $sql.= " FROM ".MAIN_DB_PREFIX."product_composition as t";
        
        if($where != '')$sql.= " WHERE ".$where;
        if($order != '')$sql.= " ORDER BY ".$order;

    	dolibarr_syslog("dao_product_composition::fetch sql=".$sql, LOG_DEBUG);

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
        
    	dolibarr_syslog("dao_product_composition::fetch sql=".$sql, LOG_DEBUG);
        
       
        return $DB->query($sql);

	
	}

}
//End of user code
?>
