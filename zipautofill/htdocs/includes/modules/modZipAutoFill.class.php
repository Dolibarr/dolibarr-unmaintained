<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2010 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\defgroup   zipautofill     Module ZiopAutoFill
 * 	\brief      Module to add zip codes
 *	\version	$Id: modZipAutoFill.class.php,v 1.1 2010/08/09 15:03:15 eldy Exp $
 */

/**
 *	\file       htdocs/includes/modules/modZipAutoFill.class.php
 *	\ingroup    zipautofill
 *	\brief      Fichier de description et activation du module ZipAutoFill
 */

include_once(DOL_DOCUMENT_ROOT ."/includes/modules/DolibarrModules.class.php");


/**
 *	\class 		modZipAutoFill
 *	\brief      Classe de description et activation du module Energie
 */

class modZipAutoFill extends DolibarrModules
{

	/**
	 *   \brief      Constructeur. Definit les noms, constantes et boites
	 *   \param      DB      handler d'acces base
	 */
	function modZipAutoFill($DB)
	{
		$this->db = $DB ;
		$this->numero = 12200;

		$this->family = "other";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Add zip codes and towns into database";

		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
		$this->version = 'dolibarr';

		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 3;
		$this->picto='zipautofill';

		// Data directories to create when module is enabled
		$this->dirs = array();

		// Dependances
		$this->depends = array();

		// Config pages
		$this->config_page_url = array();

		// Constantes
		$this->const = array();

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
		// Permissions et valeurs par defaut
		$this->remove();

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
		return $this->_load_tables('/zipautofill/sql/');
	}
}
?>
