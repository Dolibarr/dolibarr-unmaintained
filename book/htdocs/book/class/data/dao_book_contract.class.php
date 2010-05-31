<?php
//Start of user code fichier htdocs/product/dao_book_contract.class.php


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
        \file       htdocs/product/dao_book_contract.class.php
        \ingroup    book_contract
        \brief      This file is an example for a class file
		\version    1.0
		\author		Patrick Raguin
*/

// Put here all includes required by your class file

/**
        \class      dao_book_contract
        \brief      Put here description of your class
		\remarks	Initialy built by build_class_from_table on 2008-06-06 09:29
*/
class dao_book_contract // extends CommonObject
{
	private $db;							//!< To store db handler
	private $error;							//!< To return error code (or message)
	private $errors=array();
	private $lastquery;				//!< To return several error codes (or messages)
	//private $element='book_contract';			//!< Id that identify managed objects
	//private $table_element='book_contract';	//!< Name of table without prefix where object is stored
    
    private $id;
    
    private $taux;
    private $date_deb;
    private $date_fin;
    private $fk_product_book;
    private $fk_product_droit;
    private $fk_soc;
    private $quantity;
    private $fk_user;
 
    
    
    
/**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    public function dao_book_contract($DB) 
    {
        $this->db = $DB;
        return 1;
    }

	public function getId(){return $this->id;}
	public function setId($value=''){$this->id = $value;}
	
    public function getTaux() {return $this->taux;}
    public function setTaux($value='') {$this->taux = $value;}
    public function getDate_deb() {return $this->date_deb;}
    public function setDate_deb($value='') {$this->date_deb = $value;}
    public function getDate_fin() {return $this->date_fin;}
    public function setDate_fin($value='') {$this->date_fin = $value;}
    public function getFk_user() {return $this->fk_user;}
    public function setFk_user($value='') {$this->fk_user = $value;}
    public function getFk_product_book() {return $this->fk_product_book;}
    public function setFk_product_book($value='') {$this->fk_product_book = $value;}
    public function getFk_soc() {return $this->fk_soc;}
    public function setFk_soc($value='') {$this->fk_soc = $value;}
    public function getFk_product_droit() {return $this->fk_product_droit;}
    public function setFk_product_droit($value='') {$this->fk_product_droit = $value;}
    public function getQuantity() {return $this->quantity;}
    public function setQuantity($value='') {$this->quantity = $value;}
    
    public function getErrors(){ return $this->errors; }
    public function getError(){ return $this->error; }
    public function getLastquery(){ return $this->db->lastquery(); }
	
    /**
     *      \brief      Create in database
     *      \return     int         <0 si ko, 0 si ok
     */
    public function create()
    {
    	global $conf, $langs, $user;
    	
		// Clean parameters 
		
	    $this->taux=trim($this->taux);
	    $this->date_deb=trim($this->date_deb);
	    $this->date_fin=trim($this->date_fin);
		$this->fk_user=trim($this->fk_user);
		$this->fk_product_book=trim($this->fk_product_book);
		$this->fk_soc=trim($this->fk_soc);
		$this->fk_product_droit=trim($this->fk_product_droit);
		$this->quantity=trim($this->quantity);
        

		// Check parameters
		// Put here code to add control on parameters values
		
        // Insert request
        
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_book_contract(";
		
		$sql.= "taux,";
		$sql.= "date_deb,";
		$sql.= "date_fin,";
		$sql.= "quantity, ";
		$sql.= "fk_user,";
		$sql.= "fk_product_book,";
		$sql.= "fk_soc,";
		$sql.= "fk_product_droit";
		
        $sql.= ") VALUES (";

	    $sql.= " ".(! isset($this->taux)?'NULL':"'".$this->taux."'").",";
	    $sql.= " ".(! isset($this->date_deb)?'NULL':"'".$this->date_deb)."'".",";
	    $sql.= " ".(! isset($this->date_fin)?'NULL':"'".$this->date_fin)."'".",";
	    $sql.= " ".(! isset($this->quantity)?'NULL':"'".$this->quantity."'").",";
		$sql.= " ".((($this->fk_user)=="")?'NULL':"'".$this->fk_user."'").",";
		$sql.= " ".((($this->fk_product_book)=="")?'NULL':"'".$this->fk_product_book."'").",";
		$sql.= " ".((($this->fk_soc)=="")?'NULL':"'".$this->fk_soc."'").",";
		$sql.= " ".((($this->fk_product_droit)=="")?'NULL':"'".$this->fk_product_droit."'");
		       
		$sql.= ")";

	   	dolibarr_syslog("dao_book_contract::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        
        if ($resql)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."product_book_contract");
    
            // Appel des triggers
            include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
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
            dolibarr_syslog("dao_book_contract::create ".$this->error, LOG_ERR);
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
	    $this->taux=trim($this->taux);
	    $this->date_deb=trim($this->date_deb);
	    $this->date_fin=trim($this->date_fin);
	    $this->quantity=trim($this->quantity);
		$this->fk_user=trim($this->fk_user);
		$this->fk_product_book=trim($this->fk_product_book);
		$this->fk_soc=trim($this->fk_soc);
		$this->fk_product_droit=trim($this->fk_product_droit);
       

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."product_book_contract SET";
        
	   	$sql.= " taux='".addslashes($this->taux)."',";
	   	$sql.= " date_deb='".addslashes($this->date_deb)."',";
	   	$sql.= " date_fin='".addslashes($this->date_fin)."',";
	   	$sql.= " quantity='".addslashes($this->quantity)."',";
 		$sql.= " fk_user=".((($this->fk_user)=="")?'NULL':"'".$this->fk_user."'").",";
		$sql.= " fk_product_book=".((($this->fk_product_book)=="")?'NULL':"'".$this->fk_product_book."'").",";
		$sql.= " fk_soc=".((($this->fk_soc)=="")?'NULL':"'".$this->fk_soc."'").",";
		$sql.= " fk_product_droit=".((($this->fk_product_droit)=="")?'NULL':"'".$this->fk_product_droit."'")."";
   
        
        $sql.= " WHERE rowid=".$this->id;


        dolibarr_syslog("dao_book_contract::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (! $resql)
        {
            $this->error="Error ".$this->db->lasterror();
            $this->lastquery = $this->db->lastquery();
            dolibarr_syslog("dao_book_contract::update ".$this->error, LOG_ERR);
            return -1;
        }

		if (! $notrigger)
		{
            // Appel des triggers
            include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
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
	   	$sql.= " t.taux,";
	   	$sql.= " t.date_deb,";
	   	$sql.= " t.date_fin,";
	   	$sql.= " t.quantity	    , ";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_product_book,";
		$sql.= " t.fk_soc,";
		$sql.= " t.fk_product_droit";
      
		
        $sql.= " FROM ".MAIN_DB_PREFIX."product_book_contract as t";
        $sql.= " WHERE t.rowid = ".$id;
    
    	dolibarr_syslog("book_contract::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
    
				$this->id = $obj->rowid;
				$this->taux = $obj->taux;
				$this->date_deb = $obj->date_deb;
				$this->date_fin = $obj->date_fin;
				$this->quantity = $obj->quantity;
				$this->fk_user= $obj->fk_user;
				$this->fk_product_book= $obj->fk_product_book;
				$this->fk_soc= $obj->fk_soc;
				$this->fk_product_droit= $obj->fk_product_droit;

            }
            else
            {
            	$this->error="Error ".$this->db->lasterror();
	            dolibarr_syslog("dao_book_contract::fetch ".$this->error, LOG_ERR);
	            return -2;
            }
            $this->db->free($resql);
            
            return 0;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dolibarr_syslog("dao_book_contract::fetch ".$this->error, LOG_ERR);
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
	
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."product_book_contract";
		$sql.= " WHERE rowid=".$this->id;
	
	   	dolibarr_syslog("dao_book_contract::delete sql=".$sql);
		$resql = $this->db->query($sql);
		if (! $resql)
		{
			$this->error="Error ".$this->db->lasterror();
            dolibarr_syslog("dao_book_contract::delete ".$this->error, LOG_ERR);
			return -1;
		}
	
        // Appel des triggers
        include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
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
		
	    $this->taux='';
	    $this->date_deb='';
	    $this->date_fin='';
	    $this->quantity='';
		$this->fk_user='';
		$this->fk_product_book='';
		$this->fk_soc='';
		$this->fk_product_droit='';
		
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
	   	$sql.= " t.taux,";
	   	$sql.= " t.date_deb,";
	   	$sql.= " t.date_fin,";
	   	$sql.= " t.quantity	    , ";
		$sql.= " t.fk_user,";
		$sql.= " t.fk_product_book,";
		$sql.= " t.fk_soc,";
		$sql.= " t.fk_product_droit";
		} else {
			$sql.= "t.".implode(", t.", $fields) ;
		}
		
        $sql.= " FROM ".MAIN_DB_PREFIX."product_book_contract as t";
        if($where != '')$sql.= " WHERE ".$where;
        if($order != '')$sql.= " ORDER BY ".$order;
    
    	dolibarr_syslog("dao_book_contract::fetch sql=".$sql, LOG_DEBUG);
        
       
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

    	dolibarr_syslog("dao_book_contract::fetch sql=".$sql, LOG_DEBUG);
        
       
        return $DB->query($sql);

	
	}
	

}
//End of user code
?>