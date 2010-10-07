<?php
/* Copyright (C) 2007      Patrick Raguin    <patrick.raguin@gmail.com>
 * Copyright (C) 2010 Cyrille de Lambert     <cyrille.delambert@auguria.net>
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
 *	\file       htdocs/includes/modules/modLocker.class.php
 *	\ingroup    don
 *	\brief      Description file of the module locker
 */

include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**
 *	\class      modDroitPret
 *	\brief      Classe de description et activation du module Locker
 */

class modLocker  extends DolibarrModules
{

	/**
	 *   \brief      Constructeur. Definit les noms, constantes et boites
	 *   \param      DB      handler d'acces base
	 */
	function modLocker($DB)
	{
		$this->db = $DB ;
		$this->numero = 2220 ;

		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Gestion des casiers";
		$this->version = 'development';    // 'development' or 'experimental' or 'dolibarr' or version
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 3;

		// Dir
		$this->dirs = array('/smarty/cache/temp','/smarty/templates/temp');

		// Dependances
		$this->depends 			= array();
		$this->requiredby 		= array();
		$this->conflictwith 	= array();
		$this->needleftmenu 	= array();
		$this->needtotopmenu 	= array();
		$this->langfiles 		= array("@locker,admin_locker@locker","@stocks");

		// Config pages
		//$this->config_page_url = array("../locker/admin/index.php");
		$this->const=array(1=>array('MAIN_MODULE_LOCKER_NEEDSMARTY',"chaine",1,'Need smarty',0));
		$this->tabs = array('stock:Casier:@locker:/locker/liste_locker.php?id=__ID__');
		

		// Boxes
		$this->boxes = array();

	}


	/**
	 *   \brief      Fonction appelee lors de l'activation du module. Insere en base les constantes, boites, permissions du module.
	 *               Definit egalement les repertoires de donnees a creer pour ce module.
	 */
	function init()
	{
		$sql = array();
		
		$result=$this->load_tables();

		return $this->_init($sql);
	}

	/**
	 *    \brief      Fonction appelee lors de la desactivation d'un module.
	 *                Supprime de la base les constantes, boites et permissions du module.
	 */
	function remove()
	{
		$sql = array();

		return $this->_remove($sql);
	}
	
	/**
	 *		\brief		Create tables and keys required by module
	 *					This function is called by this->init.
	 * 		\return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/locker/sql/');
	}
}
?>
