<?php
/* Copyright (C) 2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 */

/**     \defgroup   google     Module Google
        \brief      Module to Google tools integration.
*/

/**
        \file       htdocs/includes/modules/modGoogle.class.php
        \ingroup    google
        \brief      Description and activation file for module Google
		\version	$Id: modGoogle.class.php,v 1.1 2008/03/09 23:27:59 eldy Exp $
*/

include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**     \class      modMyModule
        \brief      Description and activation class for module MyModule
*/

class modGoogle extends DolibarrModules
{

    /**
    *   \brief      Constructor. Define names, constants, directories, boxes, permissions
    *   \param      DB      Database handler
    */
	function modGoogle($DB)
	{
		$this->db = $DB;
		
		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used module id).
		$this->numero = 11000;
		// Key text used to identify module (for permission, menus, etc...)
		$this->rights_class = 'google';
		
		// Family can be 'crm','financial','hr','projects','product','technic','other'
		// It is used to group modules in module setup page 
		$this->family = "projects";		
		// Module title used if translation string 'ModuleXXXName' not found (XXX is value MyModule)
		$this->name = "Google";	
		// Module description used if translation string 'ModuleXXXDesc' not found (XXX is value MyModule)
		$this->description = "Module to integrate Google tools in dolibarr";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = '1.0.1';    
		// Key used in llx_const table to save module status enabled/disabled (XXX is value MyModule)
		$this->const_name = 'MAIN_MODULE_GOOGLE';
		// Where to store the module in setup page (0=common,1=interface,2=other)
		$this->special = 1;
		// Name of png file (without png) used for this module.
		// Png file must be in theme/yourtheme/img directory under name object_pictovalue.png. 
		$this->picto='generic';
		
		// Data directories to create when module is enabled
		$this->dirs = array();
		//$this->dirs[0] = DOL_DATA_ROOT.'/mymodule;
        //$this->dirs[1] = DOL_DATA_ROOT.'/mymodule/temp;
 		
		// Config pages. Put here list of php page names stored in admmin directory used to setup module
		$this->config_page_url = array('google.php');
		
		// Dependencies
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(4,1);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(2,4);	// Minimum version of Dolibarr required by module
		
		// Constants
		$this->const = array();			// List of parameters
		
		// Boxes
		$this->boxes = array();			// List of boxes 
		$r=0;
		
		// Add here list of php file(s) stored in includes/boxes that contains class to show a box.
		// Example:
        //$this->boxes[$r][1] = "myboxa.php";
    	//$r++;
        //$this->boxes[$r][1] = "myboxb.php";
    	//$r++;

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;
		
		// Add here list of permission defined by an id, a label, a boolean and two constant strings.
		// Example:
		// $this->rights[$r][0] = 2000; 				// Permission id (must not be already used)
		// $this->rights[$r][1] = 'Permision label';	// Permission label
		// $this->rights[$r][3] = 1; 					// Permission by default for new user (0/1)
		// $this->rights[$r][4] = 'level1';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $this->rights[$r][5] = 'level2';				// In php code, permission will be checked by test if ($user->rights->permkey->level1->level2)
		// $r++;

		// Main menu entries
		$this->menus = array();			// List of menus to add
		$r=0;

		$r++;
		$this->menu[$r]=array('fk_menu'=>0,'type'=>'top','titre'=>'Agenda Google','mainmenu'=>'google','leftmenu'=>'google','url'=>'/google/index.php','langs'=>'google','position'=>100,'perms'=>'','target'=>'','user'=>0);

	}

	/**
     *		\brief      Function called when module is enabled.
     *					The init function add previous constants, boxes and permissions into Dolibarr database.
     *					It also creates data directories.
     */
	function init()
  	{
    	$sql = array();
    
    	return $this->_init($sql);
  	}

	/**
	 *		\brief		Function called when module is disabled.
 	 *              	Remove from database constants, boxes and permissions from Dolibarr database.
 	 *					Data directories are not deleted.
 	 */
	function remove()
	{
    	$sql = array();

    	return $this->_remove($sql);
  	}

}

?>
