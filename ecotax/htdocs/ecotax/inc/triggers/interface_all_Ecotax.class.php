<?php
/* Copyright (C) 2007 Franky Van Liedekerke <franky.van.liedekerker@telenet.be>
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
 *
 * $Id: interface_all_Ecotax.class.php,v 1.1 2010/07/13 23:37:08 eldy Exp $
 */

/**
 *  \file       htdocs/includes/triggers/interface_modCommande_Ecotax.class.php
 *  \ingroup    core
 *  \brief      Ajout Ecotax sur produit d'un certaine categorie
 */


/**
 *  \class      InterfaceEcotax
 *  \brief      Classe des fonctions triggers des actions personalisees du workflow
 */
class InterfaceEcotax
{
	var $db;
	var $error;

	/**
	 *   \brief      Constructeur.
	 *   \param      DB      Handler d'acces base
	 */
	function InterfaceEcotax($DB)
	{
		$this->db = $DB ;

		$this->name = preg_replace('/^Interface/i','',get_class($this));
		$this->family = "product";
		$this->description = "Les triggers de ce composant calculent le ecotax du produit si le produit est membre d'une catégorie ayant pour libellé 'Ecotax').";
		$this->revision = explode(' ','$Revision: 1.1 $');
		$this->version = $this->revision[1];
	}

	/**
	 *   \brief      Renvoi nom du lot de triggers
	 *   \return     string      Nom du lot de triggers
	 */
	function getName()
	{
		return $this->name;
	}

	/**
	 *   \brief      Renvoi descriptif du lot de triggers
	 *   \return     string      Descriptif du lot de triggers
	 */
	function getDesc()
	{
		return $this->description;
	}

	/**
	 *   \brief      Renvoi version du lot de triggers
	 *   \return     string      Version du lot de triggers
	 */
	function getVersion()
	{
		global $langs;
		$langs->load("admin");

		if ($this->version == 'experimental') return $langs->trans("Experimental");
		elseif ($this->version == 'dolibarr') return DOL_VERSION;
		elseif ($this->version) return $this->version;
		else return $langs->trans("Unknown");
	}

	/**
	 *      \brief      Fonction appelee lors du declenchement d'un evenement Dolibarr.
	 *                  D'autres fonctions run_trigger peuvent etre presentes dans includes/triggers
	 *      \param      action      Code de l'evenement
	 *      \param      object      Objet concern
	 *      \param      user        Objet user
	 *      \param      lang        Objet lang
	 *      \param      conf        Objet conf
	 *      \return     int         <0 si ko, 0 si aucune action faite, >0 si ok
	 */
	function run_trigger($action,$object,$user,$langs,$conf)
	{
		if (empty($conf->ecotax->enabled)) return 0;

		if ($conf->global->ECOTAX_USE_ON_CUSTOMER_ORDER)
		{
			if ($action == 'LINEORDER_INSERT')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEORDER_DELETE')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
		}
		if ($conf->global->ECOTAX_USE_ON_PROPOSAL)
		{
			if ($action == 'LINEPROPAL_INSERT')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEPROPAL_DELETE')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
		}
		if ($conf->global->ECOTAX_USE_ON_CUSTOMER_INVOICE)
		{
			if ($action == 'LINEBILL_INSERT')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
			if ($action == 'LINEBILL_DELETE')
			{
				return $this->_add_replace_ecotax($action,$object,$user,$langs,$conf);
			}
		}

		// Renvoi 0 car aucune action de faite
		return 0;
	}


	/**
	 * Calculate ecotax
	 */
	function _add_replace_ecotax($action,$object,$user,$langs,$conf)
	{
		// The next 3 parameters can be replaced at will:
		$desc = "Ecotax"; // the description on line
		$ecocat = "Ecotax"; // the category products must be in for ecotax to apply

		// Add a line EcoTax automatically
		dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->fk_commande);

		/*
		 * Calculate the ecotax.
		 */
		$fieldid=$object->fk_element;
		$parentid=$object->$fieldid;

		$sql = "SELECT fd.total_ht, fd.qty, fd.fk_product, fd.info_bits";
		$sql.= " FROM ".MAIN_DB_PREFIX.$object->table_element_line." as fd";
		$sql.= " WHERE fd.".$object->fk_element." = '".$parentid."'";
		$sql.= " AND special_code = 0";

		$resql = $this->db->query($sql) ;

		$eco = 0;
		if ($resql)
		{
			while ( $row = $this->db->fetch_row($resql) )
			{
				$price=$row[0];
				$qty=$row[1];
				$prod_id=$row[2];
				$info_bits=$row[3];
				if ($this->_is_in_cat($ecocat,$prod_id))
				{
					// TODO Calculate ecotax
					if ($price<=100) {
						$eco+=5*$qty;
					} elseif ($price>100 && $price<=300) {
						$eco+=10*$qty;
					} else {
						$eco+=15*$qty;
					}
				}
			}
		}
		else
		{
			dol_syslog("Trigger '".$this->name."' in action '$action' [2] SQL ERROR ");
		}


		/*
		 * Detect if line already exists
		 */
		$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX.$object->table_element_line;
		$sql.= " WHERE ".$object->fk_element." = '".$parentid."'";
		$sql.= " AND special_code = 2";

		$resql = $this->db->query($sql) ;

		if ($resql)
		{
			$exists = $this->db->num_rows($resql);
			$this->db->free($resql);
		}
		else
		{
			dol_syslog("Trigger '".$this->name."' in action '$action' [3] SQL ERROR $sql");
		}

		/*
		 * Nombre de ligne de commande
		 */
		/*
		$sql = "SELECT MAX(rang) FROM ".MAIN_DB_PREFIX.$object->table_element_line;
		$sql.= " WHERE ".$object->fk_element."=".$parentid;

		$resql = $this->db->query($sql) ;
		$num_lignes=0;
		if ($resql)
		{
			while ( $row = $this->db->fetch_row($resql) )
			{
				$num_lignes = $row[0];
			}
		}
		else
		{
			dol_syslog("Trigger '".$this->name."' in action '$action' [4] SQL ERROR ");
		}
		*/


		/*
		 * Calcul les ecotax
		 */
		$rang = $num_lignes + 1;

		if ($exists > 0)
		{
			// Update line
			// TODO Call the class line $object->class_element_line;


			if ($result)
			{
				return 2;
			}
			else
			{
				dol_syslog("Trigger '".$this->name."' in action '$action' [5] ERROR $resql");
			}
		}
		else
		{
			// Insert line
			// TODO Call the class line $object->class_element_line;


			if ($result)
			{
				return 1;
			}
			else
			{
				dol_syslog("Trigger '".$this->name."' in action '$action' [5] ERROR $result");
			}
		}
	}


	/**
	 * See if the product is in the ecotax category.
	 * Stop and return 0 if the product is not in it.
	 */
	function _is_in_cat($ecocat,$product_id)
	{
		require_once(DOL_DOCUMENT_ROOT."/categories/categorie.class.php");

		if (!isset($product_id) || empty($product_id))
		{
			return 0;
		}

		$c = new Categorie($this->db);
		$cats = array();
		$cats = $c->containing($product_id,0);
		$found=0;
		if (sizeof($cats)==0) return 0;
		foreach ($cats as $cat)
		{
			if ($cat->label===$ecocat)
			{
				$found=1;
				break;
			}
		}
		if ($found==0) return 0;

		return 1;
	}
}
?>
