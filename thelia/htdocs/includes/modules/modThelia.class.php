<?php
/* Copyright (C) 2003-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Sebastien Di Cintio  <sdicintio@ressource-toi.org>
 * Copyright (C) 2004      Benoit Mortier       <benoit.mortier@opensides.be>
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

/**     \defgroup   thelia     Module thelia
 *		\brief      Module pour gerer une boutique et interface avec THELIA via Web Services
 */

/**
 *	\file       htdocs/includes/modules/modthelia.class.php
 *	\ingroup    thelia
 *	\brief      Fichier de description et activation du module THELIA
 * 	\version	$Id: modThelia.class.php,v 1.4 2010/08/09 14:58:18 eldy Exp $
 */
include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**
 *	\class 		modThelia
 *	\brief      Classe de description et activation du module THELIA
 */
class modThelia extends DolibarrModules
{

	/**
	 *   \brief      Constructeur. Definit les noms, constantes et boites
	 *   \param      DB      handler d'acces base
	 */
	function modThelia($DB)
	{
		$this->db = $DB ;
		$this->numero = 1000;

		$this->family = "products";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Interface de visualisation d'une boutique THELIA via des Web services.\nCe module requiert d'installer les composants dans /thelia/ws_server sur THELIA. Voir fichier README dans /oscommerce_ws/ws_server";
		$this->version = 'development';	// 'development' or 'experimental' or 'dolibarr' or version
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 1;

		// Data directories to create when module is enabled.
		$this->dirs = array();

		// Config pages
        $this->config_page_url =  array("thelia_setup.php@thelia");

		// Dependances
		$this->depends = array();
		$this->requiredby = array();
		$this->conflictwith = array("modBoutique");
		$this->langfiles = array("shop");

		// Constantes
		$this->const = array();

		// Boites
		$this->boxes = array();

		// Permissions
		$this->rights = array();
      //	$this->rights_class = 'boutique';



      // Menus
      //------
      $r=0;

      $this->menu[$r]=array(
      'fk_menu'=>0,
      'type'=>'top',
      'titre'=>'Thelia WS',
      'mainmenu'=>'theliaws',
      'leftmenu'=>'1',
      'url'=>'/thelia/index.php',
      'langs'=>'other',
      'position'=>100,
      'perms'=>'1',
      'target'=>'',
      'user'=>0);
      $r++;

      // Example to declare a Left Menu entry:
      $this->menu[$r]=array(
      'fk_menu'=>'r=0',    // Use r=value where r is index key used for the parent menu entry (higher parent must be a top menu entry)
      'type'=>'left',         // This is a Left menu entry
      'titre'=>'Thelia',
      'mainmenu'=>'thelia',
      'url'=>'/thelia/index.php',
      'langs'=>'other',  // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
      'position'=>100,
      'enabled'=>'1',         // Define condition to show or hide menu entry. Use '$conf->mymodule->enabled' if entry must be visible if module is enabled.
      'perms'=>'1',        // Use 'perms'=>'$user->rights->mymodule->level1->level2' if you want your menu with a permission rules
      'target'=>'',
      'user'=>0);          // 0=Menu for internal users,1=external users, 2=both
      $r++;

      // Example to declare a Left Menu entry:
      $this->menu[$r]=array('fk_menu'=>'r=1', 'type'=>'left', 'titre'=>'Commandes', 'mainmenu'=>'thelia', 'url'=>'/thelia/commandes/index.php', 'langs'=>'other', 'position'=>101, 'enabled'=>'1', 'perms'=>'1', 'target'=>'', 'user'=>0);
      $r++;
      $this->menu[$r]=array('fk_menu'=>'r=1', 'type'=>'left', 'titre'=>'Clients', 'mainmenu'=>'thelia', 'url'=>'/thelia/clients/index.php', 'langs'=>'other', 'position'=>102, 'enabled'=>'1', 'perms'=>'1', 'target'=>'', 'user'=>0);
      $r++;
      $this->menu[$r]=array('fk_menu'=>'r=1', 'type'=>'left', 'titre'=>'Produits', 'mainmenu'=>'thelia', 'url'=>'/thelia/produits/index.php', 'langs'=>'other', 'position'=>103, 'enabled'=>'1', 'perms'=>'1', 'target'=>'', 'user'=>0);
      $r++;
      $this->menu[$r]=array('fk_menu'=>'r=1', 'type'=>'left', 'titre'=>'Catégories', 'mainmenu'=>'thelia', 'url'=>'/thelia/produits/categories.php', 'langs'=>'other', 'position'=>104, 'enabled'=>'1', 'perms'=>'1', 'target'=>'', 'user'=>0);
      $r++;

      // Array to add new pages in new tabs
      $this->tabs = array('product:Thelia:@thelia:'.DOL_URL_ROOT.'/thelia/produits/fiche.php?dol_id=__ID__');
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
   function load_tables()
   {
      return $this->_load_tables('/thelia/sql/');
   }


}
?>
