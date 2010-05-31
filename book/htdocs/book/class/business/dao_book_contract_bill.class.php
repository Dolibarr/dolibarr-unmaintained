<?php
//Start of user code fichier htdocs/product/dao_book_contract_bill.class.php
	
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
        \file       htdocs/product/dao_book_contract_bill.class.php
        \ingroup    book_contract_bill
        \brief      This file is an example for a class file
		\version    1.0
		\author		Patrick Raguin
*/

// Put here all includes required by your class file

/**
        \class      dao_book_contract_bill
        \brief      Put here description of your class
		\remarks	Initialy built by build_class_from_table on 2008-06-06 09:29
*/
class dao_book_contract_bill // extends CommonObject
{
	private $db;							//!< To store db handler
	private $error;							//!< To return error code (or message)
	private $errors=array();
	private $lastquery;				//!< To return several error codes (or messages)
	//private $element='book_contract_bill';			//!< Id that identify managed objects
	//private $table_element='book_contract_bill';	//!< Name of table without prefix where object is stored
    
    private $id;
    private $ref;
    private $date_deb;
    private $date_fin;
    private $qtyOrder;
    private $qtySold;
    private $fk_contract;
    private $fk_commande;


	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    public function dao_book_contract_bill($DB) 
    {
        $this->db = $DB;
        return 1;
    }

	public function getId(){return $this->id;}
	public function setId($value=''){$this->id = $value;}
    public function getRef() {return $this->ref;}
    public function setRef($value='') {$this->ref = $value;}	
    public function getDate_deb() {return $this->date_deb;}
    public function setDate_deb($value='') {$this->date_deb = $value;}
    public function getDate_fin() {return $this->date_fin;}
    public function setDate_fin($value='') {$this->date_fin = $value;}
    public function getQtyOrder() {return $this->qtyOrder;}
    public function setQtyOrder($value='') {$this->qtyOrder = $value;}
    public function getQtySold() {return $this->qtySold;}
    public function setQtySold($value='') {$this->qtySold = $value;}
    public function getFk_contract() {return $this->fk_contract;}
    public function setFk_contract($value='') {$this->fk_contract = $value;}
    public function getFk_commande() {return $this->fk_commande;}
    public function setFk_commande($value='') {$this->fk_commande = $value;}
    
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
		$this->ref=trim($this->ref);
	    $this->date_deb=trim($this->date_deb);
	    $this->date_fin=trim($this->date_fin);
	    $this->qtyOrder=trim($this->qtyOrder);
	    $this->qtySold=trim($this->qtySold);
		$this->fk_contract=trim($this->fk_contract);
		$this->fk_commande=trim($this->fk_commande);
        

		// Check parameters
		// Put here code to add control on parameters values
		
        // Insert request
        
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_book_contract_bill(";
		$sql.= "ref,";
		$sql.= "date_deb,";
		$sql.= "date_fin,";
		$sql.= "qtyOrder, ";
		$sql.= "qtySold, ";
		$sql.= "fk_contract,";
		$sql.= "fk_commande";
		
        $sql.= ") VALUES (";
		
        $sql.= " ".(! isset($this->ref)?'NULL':"'".$this->ref."'").",";
	    $sql.= " ".(! isset($this->date_deb)?'NULL':"'".$this->date_deb."'").",";
	    $sql.= " ".(! isset($this->date_fin)?'NULL':"'".$this->date_fin."'").",";
	    $sql.= " ".(! isset($this->qtyOrder)?'0':"'".$this->qtyOrder."'").",";
	    $sql.= " ".(! isset($this->qtySold)?'0':"'".$this->qtySold."'").",";
		$sql.= " ".((($this->fk_contract)=="")?'NULL':"'".$this->fk_contract."'").",";
		$sql.= " ".((($this->fk_commande)=="")?'NULL':"'".$this->fk_commande."'");
		       
		$sql.= ")";

	   	dolibarr_syslog("dao_book_contract_bill::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        
        if ($resql)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."product_book_contract_bill");
    
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
            dolibarr_syslog("dao_book_contract_bill::create ".$this->error, LOG_ERR);
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
		$this->ref=trim($this->ref);
	    $this->date_deb=trim($this->date_deb);
	    $this->date_fin=trim($this->date_fin);
	    $this->qtyOrder=trim($this->qtyOrder);
	    $this->qtySold=trim($this->qtySold);
		$this->fk_contract=trim($this->fk_contract);
		$this->fk_commande=trim($this->fk_commande);

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."product_book_contract_bill SET";
        
        $sql.= " ref='".addslashes($this->ref)."',";
	   	$sql.= " date_deb='".addslashes($this->date_deb)."',";
	   	$sql.= " date_fin='".addslashes($this->date_fin)."',";
	   	$sql.= " qtyOrder='".addslashes($this->qtyOrder)."',";
	   	$sql.= " qtySold='".addslashes($this->qtySold)."',";
 		$sql.= " fk_contract=".((($this->fk_contract)=="")?'NULL':"'".$this->fk_contract."'").",";
		$sql.= " fk_commande=".((($this->fk_commande)=="")?'NULL':"'".$this->fk_commande."'")."";
   
        
        $sql.= " WHERE rowid=".$this->id;


        dolibarr_syslog("dao_book_contract_bill::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
        if (! $resql)
        {
            $this->error="Error ".$this->db->lasterror();
            $this->lastquery = $this->db->lastquery();
            dolibarr_syslog("dao_book_contract_bill::update ".$this->error, LOG_ERR);
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
	   	$sql.= " t.ref,";
	   	$sql.= " t.date_deb,";
	   	$sql.= " t.date_fin,";
	   	$sql.= " t.qtyOrder,";
	   	$sql.= " t.qtySold,";
		$sql.= " t.fk_contract,";
		$sql.= " t.fk_commande";
      
		
        $sql.= " FROM ".MAIN_DB_PREFIX."product_book_contract_bill as t";
        $sql.= " WHERE t.rowid = ".$id;
    
    	dolibarr_syslog("book_contract_bill::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
    
				$this->id = $obj->rowid;
				$this->ref = $obj->ref;
				$this->date_deb = $obj->date_deb;
				$this->date_fin = $obj->date_fin;
				$this->qtyOrder = $obj->qtyOrder;
				$this->qtySold = $obj->qtySold;
				$this->fk_contract= $obj->fk_contract;
				$this->fk_commande= $obj->fk_commande;

            }
            else
            {
            	$this->error="Error ".$this->db->lasterror();
	            dolibarr_syslog("dao_book_contract_bill::fetch ".$this->error, LOG_ERR);
	            return -2;
            }
            $this->db->free($resql);
            
            return 0;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dolibarr_syslog("dao_book_contract_bill::fetch ".$this->error, LOG_ERR);
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
	
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."product_book_contract_bill";
		$sql.= " WHERE rowid=".$this->id;
	
	   	dolibarr_syslog("dao_book_contract_bill::delete sql=".$sql);
		$resql = $this->db->query($sql);
		if (! $resql)
		{
			$this->error="Error ".$this->db->lasterror();
            dolibarr_syslog("dao_book_contract_bill::delete ".$this->error, LOG_ERR);
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
		$this->ref='';
	    $this->date_deb='';
	    $this->date_fin='';
	    $this->qtyOrder='';
	    $this->qtySold='';
		$this->fk_contract='';
		$this->fk_commande='';
		
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
	   	$sql.= " t.ref,";
	   	$sql.= " t.date_deb,";
	   	$sql.= " t.date_fin,";
	   	$sql.= " t.qtyOrder, ";
	   	$sql.= " t.qtySold, ";
		$sql.= " t.fk_contract,";
		$sql.= " t.fk_commande";
		} else {
			$sql.= "t.".implode(", t.", $fields) ;
		}
		
        $sql.= " FROM ".MAIN_DB_PREFIX."product_book_contract_bill as t";
        if($where != '')$sql.= " WHERE ".$where;
        if($order != '')$sql.= " ORDER BY ".$order;
    
    	dolibarr_syslog("dao_book_contract_bill::fetch sql=".$sql, LOG_DEBUG);
        
       
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

    	dolibarr_syslog("dao_book_contract_bill::fetch sql=".$sql, LOG_DEBUG);
 
        return $DB->query($sql);

	
	}
	

	

}
//End of user code
?>