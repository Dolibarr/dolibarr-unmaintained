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
 *	\file       htdocs/multicompany/dao_multicompany.class.php
 *	\ingroup    multicompany
 *	\brief      File Class multicompany
 *	\version    $Id: dao_multicompany.class.php,v 1.3 2010/09/26 09:54:49 hregis Exp $
 */


/**
 *	\class      DaoMulticompany
 *	\brief      Class of the module multicompany
 */
class DaoMulticompany
{
	var $db;
	var $error;
	//! Numero de l'erreur
	var $errno = 0;
	
	var $tpl=array();

	var $entities = array();

	/**
	 *    \brief      Constructeur de la classe
	 *    \param      DB          Handler acces base de donnees
	 *    \param      id          Id produit (0 par defaut)
	 */
	function DaoMulticompany($DB)
	{
		$this->db = $DB;
	}

	/**
	 *    \brief      Creation
	 */
	function Create($user,$datas)
	{

	}

	/**
	 *    \brief      Supression
	 */
	function Delete($id)
	{

	}

    /**
	 *    \brief      Fetch entity
	 */
	function fetch($id)
	{
		global $conf;

		$sql = "SELECT ";
		$sql.= $this->db->decrypt('name')." as name";
		$sql.= ", ".$this->db->decrypt('value')." as value";
		$sql.= " FROM ".MAIN_DB_PREFIX."const";
		$sql.= " WHERE ".$this->db->decrypt('name')." LIKE 'MAIN_%'";
		$sql.= " AND entity = ".$id;

		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$entityDetails = array();
			$i = 0;

			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);

				if (preg_match('/^MAIN_INFO_SOCIETE_PAYS$/i',$obj->name))
				{
					$tmp=explode(':',$obj->value);
					$pays_id=$tmp[0];
					$entityDetails[$obj->name] = getCountry($pays_id);
				}
				else if (preg_match('/^MAIN_MONNAIE$/i',$obj->name))
				{
					$entityDetails[$obj->name] = currency_name($obj->value);
				}
				else
				{
					$entityDetails[$obj->name] = $obj->value;
				}

				$i++;
			}
			return $entityDetails;
		}

	}

    /**
	 *    \brief      Enable/disable entity
	 */
	function setEntity($id, $type='active', $value)
	{
		global $conf;

		$sql = "UPDATE ".MAIN_DB_PREFIX."entity";
		$sql.= " SET ".$type." = ".$value;
		$sql.= " WHERE rowid = ".$id;

		dol_syslog("Multicompany::setEntity sql=".$sql, LOG_DEBUG);

		$result = $this->db->query($sql);
	}

	/**
	 *    \brief      List of entities
	 */
	function getEntities($details=0,$visible=0)
	{
		global $conf;

		$sql = "SELECT rowid, label, description, visible, active";
		$sql.= " FROM ".MAIN_DB_PREFIX."entity";
		if ($visible) $sql.= " WHERE visible = 1";

		$result = $this->db->query($sql);
		if ($result)
		{
			$num = $this->db->num_rows($result);
			$i = 0;

			while ($i < $num)
			{
				$obj = $this->db->fetch_object($result);

				$this->entities[$i]['id']			= $obj->rowid;
				$this->entities[$i]['label']		= $obj->label;
				$this->entities[$i]['description'] 	= $obj->description;
				$this->entities[$i]['visible'] 		= $obj->visible;
				$this->entities[$i]['active']		= $obj->active;
				if ($details) $this->entities[$i]['details'] = $this->fetch($obj->rowid);

				$i++;
			}
		}
	}

}
?>