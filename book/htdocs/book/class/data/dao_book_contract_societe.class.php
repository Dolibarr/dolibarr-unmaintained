<?php
/* Copyright (C) 2010      Auguria SARL         <info@auguria.org>
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
        \file       htdocs/book/class/data/dao_book_contract_societe.class.php
        \ingroup    book
        \brief      DAO to access societies table
		\version    $Id: dao_book_contract_societe.class.php,v 1.1 2010/05/31 15:28:23 pit Exp $
		\author		Pierre Morin
*/

// Put here all includes required by your class file

/**
        \class      dao_book_contract_societe
        \brief      Class to access "societe" table from book module
		\remarks	Created to improve a book module functionnality
*/
class dao_book_contract_societe
{
	private $db;							//!< To store db handler

	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    public function dao_book_contract_societe($DB) 
    {
        $this->db = $DB;
        return 1;
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
		$sql.= " t.statut,";
		$sql.= " t.parent,";
		$sql.= " t.tms,";
		$sql.= " t.datec,";
		$sql.= " t.datea,";
		$sql.= " t.nom,";
		$sql.= " t.entity,";
		$sql.= " t.code_client,";
		$sql.= " t.code_fournisseur,";
		$sql.= " t.code_compta,";
		$sql.= " t.code_compta_fournisseur,";
		$sql.= " t.address,";
		$sql.= " t.cp,";
		$sql.= " t.ville,";
		$sql.= " t.fk_departement,";
		$sql.= " t.fk_pays,";
		$sql.= " t.tel,";
		$sql.= " t.fax,";
		$sql.= " t.url,";
		$sql.= " t.email,";
		$sql.= " t.fk_secteur,";
		$sql.= " t.fk_effectif,";
		$sql.= " t.fk_typent,";
		$sql.= " t.fk_forme_juridique,";
		$sql.= " t.siren,";
		$sql.= " t.siret,";
		$sql.= " t.ape,";
		$sql.= " t.idprof4,";
		$sql.= " t.tva_intra,";
		$sql.= " t.capital,";
		$sql.= " t.description,";
		$sql.= " t.fk_stcomm,";
		$sql.= " t.e,";
		$sql.= " t.services,";
		$sql.= " t.prefix_comm,";
		$sql.= " t.client,";
		$sql.= " t.fournisseur,";
		$sql.= " t.supplier_account,";
		$sql.= " t.fk_prospectlevel,";
		$sql.= " t.customer_bad,";
		$sql.= " t.customer_rate,";
		$sql.= " t.supplier_rate,";
		$sql.= " t.fk_user_creat,";
		$sql.= " t.fk_user_modif,";
		$sql.= " t.remise_client,";
		$sql.= " t.mode_reglement,";
		$sql.= " t.cond_reglement,";
		$sql.= " t.tva_assuj,";
		$sql.= " t.localtax1_assuj,";
		$sql.= " t.localtax2_assuj,";
		$sql.= " t.gencod,";
		$sql.= " t.price_level,";
		$sql.= " t._lang,";
		$sql.= " t.import_key";

		} else {
			$sql.= "t.".implode(", t.", $fields) ;
		}
		
        $sql.= " FROM ".MAIN_DB_PREFIX."societe as t";
        if($where != '')$sql.= " WHERE ".$where;
        if($order != '')$sql.= " ORDER BY ".$order;

    	dolibarr_syslog("dao_book_contract_societe::fetch sql=".$sql, LOG_DEBUG);
        

        return $DB->query($sql);
	
	}
}
?>