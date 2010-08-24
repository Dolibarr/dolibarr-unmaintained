<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2008 Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
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
 * or see http://www.gnu.org/
 */

/**	
        \file       htdocs/includes/modules/chronodocs/modules_chronodocs.php
		\ingroup    chronodocs
		\brief      Fichier contenant les classes mères des modules de generation des chronodocs 
		            et la classe mère de numérotation des chronodocs 
		\version    $Id: modules_chronodocs.php,v 1.2 2010/08/24 20:27:25 grandoc Exp $
*/

require_once(DOL_DOCUMENT_ROOT.'/lib/functions.lib.php');
require_once(DOL_DOCUMENT_ROOT.'/includes/fpdf/fpdfi/fpdi_protection.php'); // For PDF


/**
    	\class      TplChronodocs
		\brief      Classe mère des modèles de  génération de chronodocs
*/
class TplChronodocs
{
	var $error='';
	var $dir_output;
	
 /**
        \brief      Constructeur
     */
    function TplChronodocs()
    {
		$this->dir_output = $this->dir_output=DOL_DATA_ROOT."/chronodocs";
    }
	
    /** 
        \brief      Renvoi le dernier message d'erreur de création de chronodocs
     */
    function tplError()
    {
        return $this->error;
    }
	
   /** 
     *      \brief      Renvoie la liste des modèles actifs
     */
    function liste_modeles($db)
    {
        $type='chronodocs';
        $liste=array();
        $sql ="SELECT nom as id, nom as lib";
        $sql.=" FROM ".MAIN_DB_PREFIX."document_model";
        $sql.=" WHERE type = '".$type."'";
        
        $resql = $db->query($sql);
        if ($resql)
        {
            $num = $db->num_rows($resql);
            $i = 0;
            while ($i < $num)
            {
                $row = $db->fetch_row($resql);
                $liste[$row[0]]=$row[1];
                $i++;
            }
        }
        else
        {
            return -1;
        }
        return $liste;
    }

}

/**
    	\class      ModelePDFChronodocs
	\brief      Classe mère des modèles PDF de chronodocs
*/
class ModelePDFChronodocs extends FPDF
{
    var $error='';
	var $dir_output;

    /**
        \brief      Constructeur
     */
    function ModelePDFChronodocs()
    {
		$this->dir_output=DOL_DATA_ROOT."/chronodocs";
    }

    /** 
        \brief      Renvoi le dernier message d'erreur de création de fiche intervention
     */
    function pdferror()
    {
        return $this->error;
    }

  /** 
        \brief      Renvoi le dernier message d'erreur de création de chronodocs
     */
    function tplError()
    {
        return $this->error;
    }
	
    /** 
     *      \brief      Renvoi la liste des modèles actifs
     */
    function liste_modeles($db)
    {
        $type='chronodocs';
        $liste=array();
        $sql ="SELECT nom as id, nom as lib";
        $sql.=" FROM ".MAIN_DB_PREFIX."document_model";
        $sql.=" WHERE type = '".$type."'";
        
        $resql = $db->query($sql);
        if ($resql)
        {
            $num = $db->num_rows($resql);
            $i = 0;
            while ($i < $num)
            {
                $row = $db->fetch_row($resql);
                $liste[$row[0]]=$row[1];
                $i++;
            }
        }
        else
        {
            return -1;
        }
        return $liste;
    }

}


/**
        \class      ModeleNumRefChronodocs
		\brief      Classe mère des modèles de numérotation des références de fiches d'intervention
*/

class ModeleNumRefChronodocs
{
    var $error='';

    /**     \brief      Renvoi la description par defaut du modele de numérotation
     *      \return     string      Texte descripif
     */
    function info()
    {
        global $langs;
        $langs->load("chronodocs");
        return $langs->trans("NoDescription");
    }

    /**     \brief      Renvoi un exemple de numérotation
     *      \return     string      Example
     */
    function getExample()
    {
        global $langs;
        $langs->load("chronodocs");
        return $langs->trans("NoExample");
    }

    /**     \brief      Test si les numéros déjà en vigueur dans la base ne provoquent pas de
     *                  de conflits qui empechera cette numérotation de fonctionner.
     *      \return     boolean     false si conflit, true si ok
     */
    function canBeActivated()
    {
        return true;
    }

    /**     \brief      Renvoi prochaine valeur attribuée
     *      \return     string      Valeur
     */
    function getNextValue()
    {
        global $langs;
        return $langs->trans("NotAvailable");
    }

	/**     \brief      Renvoi version du module numerotation
	*      	\return     string      Valeur
	*/
	function getVersion()
	{
		global $langs;
		$langs->load("admin");

		if ($this->version == 'development') return $langs->trans("VersionDevelopment");
		if ($this->version == 'experimental') return $langs->trans("VersionExperimental");
		if ($this->version == 'dolibarr') return DOL_VERSION;
		return $langs->trans("NotAvailable");
	}	
}


/**
		\brief      Crée un document sur disque en fonction du modèle de CHRONODOCS_ADDON_TEMPLATE
		\param	    db  			objet base de donnée
		\param	    object			Object chronodocs
		\param	    modele			force le modele à utiliser ('' par defaut)
		\param		outputlangs		objet lang a utiliser pour traduction
        \return     int         	0 si KO, 1 si OK
*/
function chronodocs_create($db, $object, $modele='', $outputlangs='')
{
	global $conf,$langs;
	$langs->load("chronodocs");
	
	$dir = DOL_DOCUMENT_ROOT."/includes/modules/chronodocs/";
	
	dolibarr_syslog("chronodocs_create modele=$modele", LOG_DEBUG);
	// Positionne modele sur le nom du modele à utiliser
	if (! dol_strlen($modele))
	{
		if ($conf->global->CHRONODOCS_ADDON_TEMPLATE)
		{
			$modele = $conf->global->CHRONODOCS_ADDON_TEMPLATE;
		}
		else
		{
			dolibarr_syslog("Error ".$langs->trans("Error_CHRONODOCS_ADDON_TEMPLATE_NotDefined"), LOG_ERR);
			print "Error ".$langs->trans("Error_CHRONODOCS_ADDON_TEMPLATE_NotDefined");
			return 0;
		}
	}
	
	// Charge le modele
	$file = "tpl_".$modele.".modules.php";
	if (file_exists($dir.$file))
	{
		$classname = "tpl_".$modele;
		require_once($dir.$file);
	
		$obj = new $classname($db);
	
		dolibarr_syslog("chronodocs_create build DOC", LOG_DEBUG);
		if ($obj->write_file($object,$outputlangs) > 0)
		{
			return 1;
		}
		else
		{
			dolibarr_print_error($db,$obj->tplerror());
			return 0;
		}
	}
	else
	{
		print $langs->trans("Error")." ".$langs->trans("ErrorFileDoesNotExists",$dir.$file);
		return 0;
	}
}

?>
