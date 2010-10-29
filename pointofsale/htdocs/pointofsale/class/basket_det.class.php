<?php
/* Copyright (C) 2007-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Denis Martin <denimartin@hotmail.fr>
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
 *      \file       htdocs/pointofsale/basket_det.class.php
 *      \ingroup    pointofsale
 *      \brief      Class used to manages basket_det lines and CRUD function for database connection
 *		\version    $Id: basket_det.class.php,v 1.1 2010/10/29 16:40:51 hregis Exp $
 *		\author		Denis Martin
 *		\remarks	Initialy built by build_class_from_table on 2010-09-27 15:45
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *      \class      Basket_det
 *      \brief      Class representing a line of a bakset.
 *		\remarks	Initialy built by build_class_from_table on 2010-09-27 15:45
 */
class Basket_det // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='basket_det';			//!< Id that identify managed objects
	//var $table_element='basket_det';	//!< Name of table without prefix where object is stored
    
    var $id;
    
	var $fk_basket; // rowid of basket object in DB
	var $fk_product; // rowid of product object in DB
	var $qty; // Quantity
	var $remise; // Absolute discount
	var $remise_percent; // Discount in percent
	var $tva_taux; // VAT rate
	var $subprice; // Unitary price before discount
	var $price; // Unitary price after discount
	var $total_ht; // Total without tax
	var $total_tva; // VAT amount
	var $total_ttc; // Total including tax
	var $product_type; // 0 if product, 1 if service
	var $date_start=''; // Date start (if service)
	var $date_end=''; // Date end (if service)
	
	var $product ;

    

	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    function Basket_det($DB, $id='',$fk_basket='',$fk_product='',$qty='',$remise='',$remise_percent='',$tva_taux='',$subprice='',$price='',$total_ht='',$total_tva='',$total_ttc='',$product_type='',$date_start='',$date_end='') 
    {
        $this->db = $DB;
        $this->id = $id ;
        $this->fk_basket=$fk_basket;
        $this->fk_product=$fk_product;
        $this->qty=$qty;
        $this->remise=$remise;
        $this->remise_percent=$remise_percent;
        $this->tva_taux=$tva_taux;
        $this->subprice=$subprice;
        $this->price=$price;
        $this->total_ht=$total_ht;
        $this->total_tva=$total_tva;
        $this->total_ttc=$total_ttc;
        $this->product_type=$product_type;
        $this->date_start=$date_start;
        $this->date_end=$date_end;
        
        $this->product = new Product($DB, $this->fk_product) ;
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
        
		if (isset($this->fk_basket)) $this->fk_basket=trim($this->fk_basket);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->qty)) $this->qty=trim($this->qty);
		if (isset($this->remise)) $this->remise=trim($this->remise);
		if (isset($this->remise_percent)) $this->remise_percent=trim($this->remise_percent);
		if (isset($this->tva_taux)) $this->tva_taux=trim($this->tva_taux);
		if (isset($this->subprice)) $this->subprice=trim($this->subprice);
		if (isset($this->price)) $this->price=trim($this->price);
		if (isset($this->total_ht)) $this->total_ht=trim($this->total_ht);
		if (isset($this->total_tva)) $this->total_tva=trim($this->total_tva);
		if (isset($this->total_ttc)) $this->total_ttc=trim($this->total_ttc);
		if (isset($this->product_type)) $this->product_type=trim($this->product_type);
        

		// Check parameters
		// Put here code to add control on parameters values
		
        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."basket_det(";
		
		$sql.= "fk_basket,";
		$sql.= "fk_product,";
		$sql.= "qty,";
		$sql.= "remise,";
		$sql.= "remise_percent,";
		$sql.= "tva_taux,";
		$sql.= "subprice,";
		$sql.= "price,";
		$sql.= "total_ht,";
		$sql.= "total_tva,";
		$sql.= "total_ttc,";
		$sql.= "product_type,";
		$sql.= "date_start,";
		$sql.= "date_end";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_basket)?'NULL':"'".$this->fk_basket."'").",";
		$sql.= " ".(! isset($this->fk_product)?'NULL':"'".$this->fk_product."'").",";
		$sql.= " ".(! isset($this->qty)?'NULL':"'".$this->qty."'").",";
		$sql.= " ".(! isset($this->remise)?'NULL':"'".$this->remise."'").",";
		$sql.= " ".(! isset($this->remise_percent)?'NULL':"'".$this->remise_percent."'").",";
		$sql.= " ".(! isset($this->tva_taux)?'NULL':"'".$this->tva_taux."'").",";
		$sql.= " ".(! isset($this->subprice)?'NULL':"'".price2num($this->subprice)."'").",";
		$sql.= " ".(! isset($this->price)?'NULL':"'".price2num($this->price)."'").",";
		$sql.= " ".(! isset($this->total_ht)?'NULL':"'".price2num($this->total_ht)."'").",";
		$sql.= " ".(! isset($this->total_tva)?'NULL':"'".price2num($this->total_tva)."'").",";
		$sql.= " ".(! isset($this->total_ttc)?'NULL':"'".price2num($this->total_ttc)."'").",";
		$sql.= " ".(! isset($this->product_type)?'NULL':"'".$this->product_type."'").",";
		$sql.= " ".(! isset($this->date_start) || dol_strlen($this->date_start)==0?'NULL':$this->db->idate($this->date_start)).",";
		$sql.= " ".(! isset($this->date_end) || dol_strlen($this->date_end)==0?'NULL':$this->db->idate($this->date_end))."";

        
		$sql.= ")";

		$this->db->begin();
		
	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."basket_det");
        }

        // Commit or rollback
        if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
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
		
		$sql.= " t.fk_basket,";
		$sql.= " t.fk_product,";
		$sql.= " t.qty,";
		$sql.= " t.remise,";
		$sql.= " t.remise_percent,";
		$sql.= " t.tva_taux,";
		$sql.= " t.subprice,";
		$sql.= " t.price,";
		$sql.= " t.total_ht,";
		$sql.= " t.total_tva,";
		$sql.= " t.total_ttc,";
		$sql.= " t.product_type,";
		$sql.= " t.date_start,";
		$sql.= " t.date_end";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."basket_det as t";
        $sql.= " WHERE t.rowid = ".$id;
    
    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
    
                $this->id    = $obj->rowid;
                
				$this->fk_basket = $obj->fk_basket;
				$this->fk_product = $obj->fk_product;
				$this->qty = $obj->qty;
				$this->remise = $obj->remise;
				$this->remise_percent = $obj->remise_percent;
				$this->tva_taux = $obj->tva_taux;
				$this->subprice = $obj->subprice;
				$this->price = $obj->price;
				$this->total_ht = $obj->total_ht;
				$this->total_tva = $obj->total_tva;
				$this->total_ttc = $obj->total_ttc;
				$this->product_type = $obj->product_type;
				$this->date_start = $this->db->jdate($obj->date_start);
				$this->date_end = $this->db->jdate($obj->date_end);

                
            }
            $this->db->free($resql);
            
            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
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
        
		if (isset($this->fk_basket)) $this->fk_basket=trim($this->fk_basket);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->qty)) $this->qty=trim($this->qty);
		if (isset($this->remise)) $this->remise=trim($this->remise);
		if (isset($this->remise_percent)) $this->remise_percent=trim($this->remise_percent);
		if (isset($this->tva_taux)) $this->tva_taux=trim($this->tva_taux);
		if (isset($this->subprice)) $this->subprice=trim($this->subprice);
		if (isset($this->price)) $this->price=trim($this->price);
		if (isset($this->total_ht)) $this->total_ht=trim($this->total_ht);
		if (isset($this->total_tva)) $this->total_tva=trim($this->total_tva);
		if (isset($this->total_ttc)) $this->total_ttc=trim($this->total_ttc);
		if (isset($this->product_type)) $this->product_type=trim($this->product_type);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."basket_det SET";
        
		$sql.= " fk_basket=".(isset($this->fk_basket)?$this->fk_basket:"null").",";
		$sql.= " fk_product=".(isset($this->fk_product)?$this->fk_product:"null").",";
		$sql.= " qty=".(isset($this->qty)?$this->qty:"null").",";
		$sql.= " remise=".(isset($this->remise)?$this->remise:"null").",";
		$sql.= " remise_percent=".(isset($this->remise_percent)?$this->remise_percent:"null").",";
		$sql.= " tva_taux=".(isset($this->tva_taux)?$this->tva_taux:"null").",";
		$sql.= " subprice=".(isset($this->subprice)?$this->subprice:"null").",";
		$sql.= " price=".(isset($this->price)?$this->price:"null").",";
		$sql.= " total_ht=".(isset($this->total_ht)?$this->total_ht:"null").",";
		$sql.= " total_tva=".(isset($this->total_tva)?$this->total_tva:"null").",";
		$sql.= " total_ttc=".(isset($this->total_ttc)?$this->total_ttc:"null").",";
		$sql.= " product_type=".(isset($this->product_type)?$this->product_type:"null").",";
		$sql.= " date_start=".(dol_strlen($this->date_start)!=0 ? "'".$this->db->idate($this->date_start)."'" : 'null').",";
		$sql.= " date_end=".(dol_strlen($this->date_end)!=0 ? "'".$this->db->idate($this->date_end)."'" : 'null')."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();
        
		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
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
		
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."basket_det";
		$sql.= " WHERE rowid=".$this->id;
	
		$this->db->begin();
		
		dol_syslog(get_class($this)."::delete sql=".$sql);
		$resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
		
        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
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
	 *		\brief      Load an object from its id and create a new one in database
	 *		\param      fromid     		Id of object to clone
	 * 	 	\return		int				New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;
		
		$error=0;
		
		$object=new Basket_det($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->statut=0;

		// Clear fields
		// ...
				
		// Create clone
		$result=$object->create($user);

		// Other options
		if ($result < 0) 
		{
			$this->error=$object->error;
			$error++;
		}
		
		if (! $error)
		{
			
			
			
		}
		
		// End
		if (! $error)
		{
			$this->db->commit();
			return $object->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}

	
	/**
	 *		\brief		Initialise object with example values
	 *		\remarks	id must be 0 if object instance is a specimen.
	 */
	function initAsSpecimen()
	{
		$this->id=0;
		
		$this->fk_basket='';
		$this->fk_product='';
		$this->qty='';
		$this->remise='';
		$this->remise_percent='';
		$this->tva_taux='';
		$this->subprice='';
		$this->price='';
		$this->total_ht='';
		$this->total_tva='';
		$this->total_ttc='';
		$this->product_type='';
		$this->date_start='';
		$this->date_end='';

		
	}

}
?>
