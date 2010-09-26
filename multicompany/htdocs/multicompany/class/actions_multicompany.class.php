<?php
/* Copyright (C) 2009-2010 Regis Houssin <regis@dolibarr.fr>
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
 *	\file       htdocs/multicompany/actions_multicompany.class.php
 *	\ingroup    multicompany
 *	\brief      File Class multicompany
 *	\version    $Id: actions_multicompany.class.php,v 1.2 2010/09/26 09:54:49 hregis Exp $
 */

require_once(DOL_DOCUMENT_ROOT."/multicompany/class/dao_multicompany.class.php");

/**
 *	\class      ActionsMulticompany
 *	\brief      Class Actions of the module multicompany
 */
class ActionsMulticompany
{
	var $db;
	var $error;
	//! Numero de l'erreur
	var $errno = 0;
	
	var $template_dir;
	var $template;
	
	var $tpl=array();

	/**
	 *    \brief      Constructeur de la classe
	 *    \param      DB          Handler acces base de donnees
	 *    \param      id          Id produit (0 par defaut)
	 */
	function ActionsMulticompany($DB)
	{
		$this->db = $DB;

		$this->canvas = "default";
		$this->name = "admin";
		$this->description = "";
	}
	
	function doActions()
	{
		if ($_GET["action"] == 'setactive')
		{
			$mc = new DaoMulticompany($this->db);
			
			$mc->setEntity($_GET['id'],'active',$_GET["value"]);
			if ($_GET["value"] == 0) $mc->setEntity($_GET['id'],'visible',$_GET["value"]);
		}
		
		if ($_GET["action"] == 'setvisible')
		{
			$mc = new DaoMulticompany($this->db);
			
			$mc->setEntity($_GET['id'],'visible',$_GET["value"]);
		}
	}

	/**
	 *    Return combo list of entities.
	 *    @param		selected    Preselected entity
	 *    @param		option		Option
	 */
	function select_entities($selected='',$option='')
	{
		$mc = new DaoMulticompany($this->db);
		
		$mc->getEntities(0,1);
		
		$return = '<select class="flat" name="entity" '.$option.'>';

		if (is_array($mc->entities))
		{
			foreach ($mc->entities as $entity)
			{
				if ($entity['active'] == 1)
				{
					$return.= '<option value="'.$entity['id'].'" ';
					if ($selected == $entity['id'])	$return.= 'selected="true"';
					$return.= '>';
					$return.= $entity['label'];
					$return.= '</option>';
				}
			}
		}
		$return.= '</select>';

		return $return;
	}

	/**
	 *    Assigne les valeurs pour les templates
	 *    @param      action     Type of action
	 */
	function assign_values($action='')
	{
		global $conf,$langs;
		
		$mc = new DaoMulticompany($this->db);
		$mc->getEntities(1);
		
		$picto='title.png';
		if (empty($conf->browser->firefox)) $picto='title.gif';
		$this->tpl['title_picto'] = img_picto('',$picto);

		$this->tpl['entities'] = $mc->entities;
		$this->tpl['img_on'] = img_picto($langs->trans("Activated"),'on');
		$this->tpl['img_off'] = img_picto($langs->trans("Disabled"),'off');
		$this->tpl['img_modify'] = img_edit();
		$this->tpl['img_delete'] = img_delete();
	}
	
	/**
	 *    Display the template
	 */
	function display()
	{
		global $langs;
		
		include($this->template_dir.$this->template);
	}

}
?>