<?php
/* Copyright (C) 2010 Regis Houssin  <regis@dolibarr.fr>
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
 *   \file		htdocs/workflow/class/workflow.class.php
 *   \ingroup		workflow
 *   \brief			Fichier de la classe de gestion des workflow
 *   \version		$Id: workflow.class.php,v 1.1 2010/06/29 14:57:06 hregis Exp $
 */


/**
 *    \class      Interfaces
 *    \brief      Classe de la gestion des workflow
 */

class Workflow
{
	var $errors	= array();	// Array for errors

   /**
	*   \brief      Constructeur.
	*   \param      DB      handler d'acces base
	*/
	function Workflow($DB)
	{
		$this->db = $DB ;
	}

}
?>
