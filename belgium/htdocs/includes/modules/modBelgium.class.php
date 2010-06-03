<?php
/* Copyright (C) 2009-2010 Laurent Léonard <laurent@open-minds.org>
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
 * 		\file 		htdocs/includes/modules/modBelgium.class.php
 *  	\ingroup 	belgium
 *  	\brief 		Description and activation file for module Belgium.
 *  	\version 	0.1.1
 */

include_once(DOL_DOCUMENT_ROOT . '/includes/modules/DolibarrModules.class.php');

/** 
 * 		\class 		modBelgium
 *  	\brief 		Description and activation class for module Belgium.
 */
class modBelgium extends DolibarrModules
{
	public $boxes;
	public $config_page_url;
	public $const;
	public $const_name;
	public $db;
	public $depends;
	public $description;
	public $dirs;
	public $family;
	public $langfiles;
	public $menu;
	public $name;
	public $need_dolibarr_version;
	public $numero;
	public $phpmin;
	public $picto;
	public $requiredby;
	public $rights;
	public $rights_class;
	public $special;
	public $style_sheet;
	public $version;
	
	/** 
	 * 	\brief 	Constructor 	Define names, constants, directories, boxes, permissions.
	 * 	\param 	db 				Database handler
	 */
	function __construct($db)
	{
		$this->db = $db;
		
		$this->numero = 100000;
		$this->rights_class = 'belgium';
		
		$this->family = 'financial';
		$this->name = preg_replace('/^mod/i', '', get_class($this));
		$this->description = 'Features specific to belgian legislation';
		$this->version = '0.1.1';
		$this->const_name = 'MAIN_MODULE_' . strtoupper($this->name);
		$this->special = 0;
		$this->picto = 'belgium@belgium';
		
		$this->dirs = array();
		
		$this->style_sheet = '';
		
		$this->config_page_url = array();
		
		$this->depends = array();
		$this->requiredby = array();
		$this->phpmin = array(5,2);
		$this->need_dolibarr_version = array(2,5);
		$this->langfiles = array('@belgium');
		
		$this->const = array();
		
		$this->boxes = array();
		
		$this->rights = array();
		$this->rights_class = 'belgium';
		$r=0;
		
		$this->rights[$r][0] = 10001;
		$this->rights[$r][1] = 'Read VAT clients list';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'vat_list';
		$this->rights[$r][5] = 'read';
		$r++;
		
		$this->rights[$r][0] = 10002;
		$this->rights[$r][1] = 'Export VAT clients list';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'vat_list';
		$this->rights[$r][5] = 'export';
		$r++;
		
		$this->menu = array();
		$r = 0;
		
		$this->menu[$r] = array(
			'fk_menu' => 0,
			'type' => 'top',
			'titre' => 'Belgium',
			'mainmenu' => 'belgium',
			'leftmenu' => '1',
			'url' => '/belgium/index.php',
			'langs' => '@belgium',
			'position' => 100,
			'perms' => '$user->rights->belgium->vat_list->read',
			'target' => '',
			'user' => 0
		);
		$r++;
		
		$this->menu[$r] = array(
			'fk_menu' => 'r=0',
			'type' => 'left',
			'titre' => 'Intervat',
			'mainmenu' => 'belgium',
			'url' => '/belgium/index.php',
			'langs' => '@belgium',
			'position' => 100,
			'perms' => '$user->rights->belgium->vat_list->read',
			'target' => '',
			'user' => 0
		);
		$r++;
		
		$this->menu[$r] = array(
			'fk_menu' => 'r=1',
			'type' => 'left',
			'titre' => 'VATClientsList',
			'mainmenu' => 'belgium',
			'url' => '/belgium/vat_list.php',
			'langs' => '@belgium',
			'position' => 100,
			'perms' => '$user->rights->belgium->vat_list->read',
			'target' => '',
			'user' => 0
		);
		$r++;
	}
	
	function init()
	{
		return $this->_init(array());
	}
	
	function remove()
	{
		return $this->_remove(array());
	}
}

?>
