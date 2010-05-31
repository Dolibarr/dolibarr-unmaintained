<?php
//Start of user code fichier htdocs/product/dao_book.class.php


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
        \file       htdocs/product/dao_book.class.php
        \ingroup    book
        \brief      This file is an example for a class file
		\version    $Id: dao_book.class.php,v 1.1 2010/05/31 15:28:23 pit Exp $
		\author		Patrick Raguin
*/

// Put here all includes required by your class file

/**
        \class      dao_book
        \brief      Put here description of your class
		\remarks	Initialy built by build_class_from_table on 2008-06-06 09:29
*/
class dao_book // extends CommonObject
{
	private $db;							//!< To store db handler
	private $error;							//!< To return error code (or message)
	private $errors=array();
	private $lastquery;				//!< To return several error codes (or messages)
	//private $element='book';			//!< Id that identify managed objects
	//private $table_element='book';	//!< Name of table without prefix where object is stored
    
    private $id;
    
    private $nbpages;
    private $isbn;
    private $isbn13;
    private $ean;
    private $ean13;
    private $format;
    private $contract;
	private $stock_reel;

	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    public function dao_book($DB) 
    {
        $this->db = $DB;
        return 1;
    }

	public function getId(){return $this->id;}
	public function setId($value=''){$this->id = $value;}
	
    public function getNbpages() {return $this->nbpages;}
    public function setNbpages($value='') {$this->nbpages = $value;}
    public function getISBN() {return $this->isbn;}
    public function setISBN($value='') {$this->isbn = $value;}
    public function getISBN13() {return $this->isbn13;}
    public function setISBN13($value='') {$this->isbn13 = $value;}
    public function getEAN() {return $this->ean;}
    public function setEAN($value='') {$this->ean = $value;}
	public function getEAN13() {return $this->ean13;}
    public function setEAN13($value='') {$this->ean13 = $value;}
    public function getFormat() {return $this->format;}
    public function setFormat($value='') {$this->format = $value;}
    public function getContract() {return $this->contract;}
    public function setContract($value='') {$this->contract = $value;}
    public function getStockReel() {return $this->stock_reel;}
    public function setStockReel($value='') {$this->stock_reel = $value;}   
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
		
	    $this->nbpages=trim($this->nbpages);
	    $this->isbn=trim($this->isbn);
	    $this->ean=trim($this->ean);
	    $this->isbn13=trim($this->isbn13);
	    $this->ean13=trim($this->ean13);
	    $this->format=trim($this->format);
        

		// Check parameters
		// Put here code to add control on parameters values
		
        // Insert request
        
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_book(";
		
		$sql.= "rowid,";
		$sql.= "nbpages,";
		$sql.= "isbn,";
		$sql.= "isbn13,";
		$sql.= "ean,";
		$sql.= "ean13,";
		$sql.= "format,";
		$sql.= "fk_contract";
		
        $sql.= ") VALUES (";

	    $sql.= " ".$this->id .",";
	    $sql.= " ".(! isset($this->nbpages)?'NULL':"'".$this->nbpages."'").",";
	    $sql.= " ".(! isset($this->isbn)?'NULL':"'".$this->isbn."'").",";
	    $sql.= " ".(! isset($this->isbn13)?'NULL':"'".$this->isbn13."'").",";
	    $sql.= " ".(! isset($this->ean)?'NULL':"'".$this->ean."'").",";
	    $sql.= " ".(! isset($this->ean13)?'NULL':"'".$this->ean13."'").",";
	    $sql.= " ".(! isset($this->format)?'NULL':"'".$this->format."',");
	    $sql.= " ".(! isset($this->contract)?'NULL':"'".$this->contract."'");
		       
		$sql.= ")";

		
	   	dolibarr_syslog("dao_book::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        
        if ($resql)
        {
    
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
            dolibarr_syslog("dao_book::create ".$this->error, LOG_ERR);
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
	    $this->nbpages=trim($this->nbpages);
	    $this->isbn=trim($this->isbn);
	    $this->ean=trim($this->ean);
	    $this->format=trim($this->format);
       

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."product_book SET";
        
	   	$sql.= " nbpages='".addslashes($this->nbpages)."',";
	   	$sql.= " isbn='".addslashes($this->isbn)."',";
	   	$sql.= " isbn13='".addslashes($this->isbn13)."',";
	   	$sql.= " ean='".addslashes($this->ean)."',";
	   	$sql.= " ean13='".addslashes($this->ean13)."',";
	   	$sql.= " format='".addslashes($this->format)."',";
   		$sql.= " fk_contract=".( ((! isset($this->contract)) || empty($this->contract))?'NULL':"'".addslashes($this->contract)."'" ); //if we don't use NULL when $this->contract is empty, we've got a problem with constraints in database
        
        $sql.= " WHERE rowid=".$this->id;

        dolibarr_syslog("dao_book::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (! $resql)
        {
            $this->error="Error ".$this->db->lasterror();
            $this->lastquery = $this->db->lastquery();
            dolibarr_syslog("dao_book::update ".$this->error, LOG_ERR);
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
        
	   	$sql.= " b.rowid,";
	   	$sql.= " b.nbpages,";
	   	$sql.= " b.isbn,";
	   	$sql.= " b.isbn13,";
	   	$sql.= " b.ean,";
	   	$sql.= " b.ean13,";
	   	$sql.= " b.format,";
		$sql.= " b.fk_contract,";
		$sql.= " s.reel";
		
      
		
        $sql.= " FROM ".MAIN_DB_PREFIX."product_book as b";
        $sql.= " LEFT OUTER JOIN ".MAIN_DB_PREFIX."product_stock s ON s.fk_product = b.rowid";
        $sql.= " WHERE b.rowid = ".$id;
    
    	dolibarr_syslog("book::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
    
				$this->id = $obj->rowid;
				$this->nbpages = $obj->nbpages;
				$this->isbn = $obj->isbn;
				$this->isbn13 = $obj->isbn13;
				$this->ean = $obj->ean;
				$this->ean13 = $obj->ean13;
				$this->format = $obj->format;
				$this->contract = $obj->fk_contract;
				$this->stock_reel = $obj->reel;
            }
            else
            {
            	$this->error="Error ".$this->db->lasterror();
	            dolibarr_syslog("dao_book::fetch ".$this->error, LOG_ERR);
	            return -2;
            }
            $this->db->free($resql);
            
            return 0;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dolibarr_syslog("dao_book::fetch ".$this->error, LOG_ERR);
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
	
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."product_book";
		$sql.= " WHERE rowid=".$this->id;
	
	   	dolibarr_syslog("dao_book::delete sql=".$sql);
		$resql = $this->db->query($sql);
		if (! $resql)
		{
			$this->error="Error ".$this->db->lasterror();
            dolibarr_syslog("dao_book::delete ".$this->error, LOG_ERR);
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
		
	    $this->nbpages='';
	    $this->isbn='';
	    $this->ean='';
	    $this->isbn13='';
	    $this->ean13='';
	    $this->format='';
	    $this->contract='';
		
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
	   	$sql.= " t.nbpages,";
	   	$sql.= " t.isbn,";
	   	$sql.= " t.isbn13,";
	   	$sql.= " t.ean,";
	   	$sql.= " t.ean13,";
	   	$sql.= " t.format,";
	   	$sql.= " t.fk_contract";

		} else {
			$sql.= "t.".implode(", t.", $fields) ;
		}
		
        $sql.= " FROM ".MAIN_DB_PREFIX."product_book as t";
        if($where != '')$sql.= " WHERE ".$where;
        if($order != '')$sql.= " ORDER BY ".$order;

    	dolibarr_syslog("dao_book::fetch sql=".$sql, LOG_DEBUG);
        

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
   
	if($where != ''){
   			if (stristr($query,'GROUP BY')){
   				$sql.= " HAVING ".$where;
   			} else {
   				$sql.= " WHERE ".$where;
   			}
   		}
        if($order != '')$sql.= " ORDER BY ".$order;

    	dolibarr_syslog("dao_book::fetch sql=".$sql, LOG_DEBUG);
        
        return $DB->query($sql);

	
	}
	
	/*
     *      \brief      	exist
     *		\param $id		id product
     *      \return     	true/false
     */	
	public function exist($id)
	{
		$sql = 'SELECT t.rowid';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'product_book as t';
		$sql.= ' WHERE rowid='.$id;
		
		$result = $this->db->query($sql);

		return($this->db->num_rows($result) > 0)?true: false;
	}
	

}
//End of user code
?>