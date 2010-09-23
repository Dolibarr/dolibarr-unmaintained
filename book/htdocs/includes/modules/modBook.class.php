<?php
//Start of user code copyright htdocs/includes/modules/modBook.class.php


/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2008 	   Samuel  Bouchet		<samuel.bouchet@auguria.net>
 * Copyright (C) 2008 	   Patrick Raguin  		<patrick.raguin@auguria.net>
 * Copyright (C) 2010 	   Pierre Morin			<pierre.morin@auguria.net>
 * Copyright (C) 2010 	   Cyrille de Lambert			<cyrille.delambert@auguria.net>
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

/**     \defgroup   Book     Module Book
 \brief      Book
 */

/**
 \file       htdocs/includes/modules/modBook.class.php
 \ingroup    book
 \brief      Description and activation file for Module book
 \version	$Id: modBook.class.php,v 1.0
 \author		Patrick Raguin, Pierre Morin
 */
//End of user code
include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**     \class      modlivre
 \brief      Description and activation class for Module livre
 */

class modBook extends DolibarrModules
{

	/**
	 *   \brief      Constructor. Define names, constants, directories, boxes, permissions
	 *   \param      DB      Database handler
	 */
	function modBook($DB)
	{
		$this->db = $DB;

		$this->numero = 1600 ;
		// Key text used to identify module (for permission, menus, etc...)
		$this->rights_class = 'book';
	  
		$this->family = "products";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Gestion des livres";

		$this->revision = explode(' ','$Revision: 1.7 $');
		$this->version = $this->revision[1];

		// Key used in llx_const table to save module status enabled/disabled
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 3;
		$this->picto='product';

		// Dependances
		$this->depends = array("modProduit","modCommande","modFacture","modStock","modComposition"/*,"modLocker"*/);
		$this->requiredby = array();

		// Config pages
		$this->config_page_url = array("../book/admin/index.php");
		$this->langfiles = array("products","@book","admin_book@book");

		// Data directories to create when Module is enabled.
		$this->dirs = array();

			
		// Relative path to Module style sheet if exists. Example: '/myModule/mycss.css'.
		$this->style_sheet = '/book/css/book.css';



		$this->phpmin = array(5,1);					// Minimum version of PHP required by Module
		$this->need_dolibarr_version = array(2,8);	// Minimum version of Dolibarr required by Module


		// Constants
		// List of parameters
		$this->const = array(0=>array('BOOK_CONTRACT_BILL_SLOGAN','chaine','My society slogan','This is the slogan of the current Society, to be printed in contract bill mails.',0),
							1=>array('BOOK_CONTRACT_BILL_SOCIETE_INFOS','chaine','TEL. 01 02 03 04 05, FAX 05 06 07 08 09 - SARL AU CAPITAL DE 7 500 EUROS - SIRET - 419 700 425 88865','','This is the informations about the current Society, to be printed in contract bill mails.',0),
							2=>array('BOOK_CONTRACT_BILL_OTHER_INFOS','chaine','www.mysociety.com','This is other informations about the current Society, to be printed in contract bill mails.',0),
							3=>array('MAIN_MODULE_BOOK_NEEDSMARTY','chaine',1,'Need smarty',0)
							/*,
							3=>array('BOOK_SUBPERMCATEGORY_FOR_DOCUMENTS','chaine','book','This is the name of the sub-permissions to use to get the documents for this module',0),
							4=>array('BOOK_SQLPROTECTAGAINSTEXTERNALS_FOR_DOCUMENTS','chaine','SELECT rowid as fk_soc FROM ".MAIN_DB_PREFIX."societe WHERE rowid=\'$refname\'','This is the SQL command to execute for the basic protection against external users',0)*/);
		//Start of user code - Constants are not generated, must be implemented. The code within user code markups while NOT be lost if the file is generated again

		//End of user code
		
		// New pages on tabs
		$this->tabs = array('product:Book:@book:/book/show_book.php?id=__ID__');

		// Boxes
		$this->boxes = array();			// List of boxes
		$r=0;

		// Permissions
		$this->rights = array();		// Permission array used by this Module
		$this->rights_class = 'book';
		$r=0;
		$this->rights[$r][0] = 1601 ; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'accès en lecture';	// Permission label
		$this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'read';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;
		$this->rights[$r][0] = 1602 ; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'écrire';			// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'write';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;
		$this->rights[$r][0] = 1603 ; 				// Permission id (must not be already used)
		$this->rights[$r][1] = 'supprimer un élément';	// Permission label
		$this->rights[$r][3] = 0; 					// Permission by default for new user (0/1)
		$this->rights[$r][4] = 'delete';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		$r++;


		// Exports
		$r=1;
		//Start of user code - Exports are not generated, must be implemented. The code within user code markups while NOT be lost if the file is generated again

		//End of user code

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		// Top menu
		$this->menu[$r]=array('fk_menu'=>0,
							  'type'=>'top',
							  'titre'=>'Books',
							  'mainmenu'=>'book',
							  'leftmenu'=>'1',		// To say if we can overwrite leftmenu
							  'url'=>'/book/index.php?leftmenu=product',
							  'langs'=>'@book',
							  'position'=>100,
							  'perms'=>'$user->rights->book->read',
							  'enabled'=>'$conf->book->enabled',
							  'target'=>'',
							  'user'=>2);			// 0=Menu for internal users, 1=external users, 2=both
		$r++;
		
		// Left menu
		$this->menu[$r]=array(	'fk_menu'=>'r=0',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
								'type'=>'left',			// This is a Left menu entry
								'titre'=>'Books',
								'mainmenu'=>'book',
								'url'=>'/book/index.php?leftmenu=product',
								'langs'=>'book',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>100,
								'enabled'=>'1',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->book->read',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				// 0=Menu for internal users,1=external users, 2=both
		$r++;
		
		$this->menu[$r]=array(	'fk_menu'=>'r=1',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
								'type'=>'left',			// This is a Left menu entry
								'titre'=>'NewBook',
								'mainmenu'=>'book',
								'url'=>'/book/add_book.php?leftmenu=product',
								'langs'=>'book',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>100,
								'enabled'=>'1',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->book->write',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				// 0=Menu for internal users,1=external users, 2=both
		$r++;
		
		$this->menu[$r]=array(	'fk_menu'=>'r=1',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
								'type'=>'left',			// This is a Left menu entry
								'titre'=>'BookList',
								'mainmenu'=>'book',
								'url'=>'/book/index.php?leftmenu=product',
								'langs'=>'book',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
								'position'=>100,
								'enabled'=>'1',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
								'perms'=>'$user->rights->book->read',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
								'target'=>'',
								'user'=>2);				// 0=Menu for internal users,1=external users, 2=both
		$r++;
		
		//if($conf->stock->enabled)
		if(1)
		{
			$this->menu[$r]=array(	'fk_menu'=>'r=1',		// Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
									'type'=>'left',			// This is a Left menu entry
									'titre'=>'Stocks',
									'mainmenu'=>'book',
									'url'=>'/product/reassort.php?type=0',
									'langs'=>'stock',	// Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
									'position'=>100,
									'enabled'=>'1',			// Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
									'perms'=>'$user->rights->stock->lire',			// Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
									'target'=>'',
									'user'=>2);				// 0=Menu for internal users,1=external users, 2=both
			$r++;
		}

	}

	/**
	 *		\brief      Function called when Module is enabled.
	 *					The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *					It also creates data directories.
	 *      \return     int             1 if OK, 0 if KO
	 */
	function init()
	{

		$sql = array();
		dolibarr_set_const($this->db, "PRODUIT_COMPOSITION", 1);
		$result=$this->load_tables();

		return $this->_init($sql);
	}

	/**
	 *		\brief		Function called when Module is disabled.
	 *              	Remove from database constants, boxes and permissions from Dolibarr database.
	 *					Data directories are not deleted.
	 *      \return     int             1 if OK, 0 if KO
	 */
	function remove()
	{
		$sql = array();

		return $this->_remove($sql);
	}


	/**
	 *		\brief		Create tables and keys required by Module
	 * 					Files myModule.sql and myModule.key.sql with create table and create keys
	 * 					commands must be stored in directory /mysql/tables/livre.
	 *					This function is called by this->init.
	 * 		\return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/book/sql/');
	}
}

?>