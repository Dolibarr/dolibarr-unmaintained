<?php
/* Copyright (C) 2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2009 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\defgroup   	editeur     Module editor
 *	\brief      	Module pour gerer les livres et editeurs
 *	\version		$Id: modEditeur.class.php,v 1.1 2010/03/22 12:13:16 hregis Exp $
 */

/**
 *	\file       htdocs/includes/modules/modEditeur.class.php
 *	\ingroup    editeur
 *	\brief      Fichier de description et activation du module Editeur
 */

include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**
 *	\class      modEditeur
 *	\brief      Classe de description et activation du module Editeur
 */
class modEditeur extends DolibarrModules
{

	/**
	 *   \brief      Constructeur. Definit les noms, constantes et boites
	 *   \param      DB      Database handler
	 */
	function modEditeur($DB)
	{
		$this->db = $DB ;
		$this->numero = 49 ;

		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Gestion des livres et editeurs";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = 'development';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 3;
		$this->picto='book';

		// Data directories to create when module is enabled
		$this->dirs = array('/smarty/cache/temp','/smarty/templates/temp');

		// Config pages
		$this->config_page_url = array("editeur.php");

		// Dependances
		$this->depends = array();
		$this->requiredby = array();
		$this->conflictwith = array();
		$this->needleftmenu = array();
		$this->needtotopmenu = array();
		$this->langfiles = array("orders","bills","companies");

		// Constants
		//Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',0),
		//                            1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0) );
		$this->const=array(1=>array('MAIN_MODULE_DROITPRET_NEEDSMARTY','chaine',1,'Need smarty',0));

		// Boxes
		$this->boxes = array();

		// Permissions
		$this->rights = array();
	}


	/**
	 *   \brief      Fonction appelee lors de l'activation du module. Insere en base les constantes, boites, permissions du module.
	 *               Definit egalement les repertoires de donnees a creer pour ce module.
	 */
	function init()
	{
		global $conf;

		return $this->_init($sql);
	}


	/**
	 *    \brief      Fonction appelee lors de la desactivation d'un module.
	 *                Supprime de la base les constantes, boites et permissions du module.
	 */
	function remove()
	{
		global $conf;

		return $this->_remove($sql);
	}
}
?>
