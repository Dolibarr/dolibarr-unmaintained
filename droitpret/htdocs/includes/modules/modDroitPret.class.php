<?php
/* Copyright (C) 2007      Patrick Raguin    <patrick.raguin@gmail.com>
 * Copyright (C) 2005-2010 Regis Houssin     <regis@dolibarr.fr>
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
 *	\defgroup   DroitPret     Module droitpret
 *	\version	$Id: modDroitPret.class.php,v 1.6 2010/09/23 17:12:54 cdelambert Exp $
 *	\brief      Module pour gerer le suivi des droits de prets
 */

/**
 *	\file       htdocs/includes/modules/modDroitPret.class.php
 *	\ingroup    don
 *	\brief      Fichier de description et activation du module DroitPret
 */

include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**
 *	\class      modDroitPret
 *	\brief      Classe de description et activation du module DroitPret
 */

class modDroitPret  extends DolibarrModules
{

	/**
	 *   \brief      Constructeur. Definit les noms, constantes et boites
	 *   \param      DB      handler d'acces base
	 */
	function modDroitPret($DB)
	{
		$this->db = $DB ;
		$this->numero = 2200 ;

		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Gestion du droit de prêts";
		$this->version = '0.9';    // 'development' or 'experimental' or 'dolibarr' or version
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
		$this->langfiles 		= array("@droitpret,admin_droitpret@droitpret");

		// Config pages
		$this->config_page_url = array("../droitpret/admin/index.php");


		// Constants
		//Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',0),
		//                            1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0) );
		$this->const=array(1=>array('MAIN_MODULE_DROITPRET_NEEDSMARTY',"chaine",1,'Need smarty',0));

		// Boxes
		$this->boxes = array();

		// Menus
		//------
		$r=0;

		$this->menu[$r]=array(
	      'fk_menu'=>0,
	      'type'=>'top',
	      'titre'=>'Droit de pret',
	      'mainmenu'=>'droitpret',
	      'leftmenu'=>'1',
	      'url'=>'/droitpret/index.php',
	      'langs'=>'@droitpret',
	      'position'=>100,
	      'perms'=>'1',
	      'target'=>'',
	      'user'=>0);
		$r++;

		// Permissions
		$this->rights = array();
		$this->rights_class = 'droitpret';
		$r=0;

		$this->rights[$r][0] = 2200;
		$this->rights[$r][1] = 'Lire les droits de prets';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'lire';

		$r++;
		$this->rights[$r][0] = 2201;
		$this->rights[$r][1] = 'Creer/modifier les droits de prets';
		$this->rights[$r][2] = 'w';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'creer';
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
		return $this->_load_tables('/droitpret/sql/');
	}
}
?>
