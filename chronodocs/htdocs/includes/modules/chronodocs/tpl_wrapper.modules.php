<?php
/* Copyright (C) 2004-2008 Laurent Destailleur    <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2008 Regis Houssin          <regis.houssin@dolibarr.fr>
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
       	\file       htdocs/includes/modules/chronodocs/tpl_wrapper.modules.php
		\ingroup    facture
		\brief      File of class to generate chronodocs files from wrapper model
		\author	    Raphael Bertrand
		\version    $Id: tpl_wrapper.modules.php,v 1.3 2009/06/10 19:50:52 eldy Exp $
*/

require_once(DOL_DOCUMENT_ROOT."/includes/modules/chronodocs/modules_chronodocs.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_entries.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_types.class.php");


/**
	\class      tpl_wrapper
	\brief      Classe permettant de generer une copie du fichier definit pour le chronodocs_type du chronodoc
*/

class tpl_wrapper extends TplChronodocs
{
	var $emetteur;	// Objet societe qui emet


    /**
     *		\brief  Constructor
     *		\param	db		Database handler
     */
    function tpl_wrapper($DB=0)
    {
        global $conf,$langs,$mysoc;
		global $db;

        $langs->load("main");
        $langs->load("bills");

        if($DB)
			$this->db = $DB;
		else
			$this->db = $db;
        $this->name = "wrapper";
        $this->description = $langs->trans('TPLWrapperDescription');
		$this->dir_output=DOL_DATA_ROOT."/chronodocs";

        // Dimension page pour format A4
        $this->type = 'var';
        $this->page_largeur = 210;
        $this->page_hauteur = 297;
        $this->format = array($this->page_largeur,$this->page_hauteur);

        // Recupere emmetteur
        $this->emetteur=$mysoc;
        if (! $this->emetteur->pays_code) $this->emetteur->pays_code=substr($langs->defaultlang,-2);    // Par defaut, si n'était pas défini

    }


 /**
     *		\brief      Fonction générant le chronodoc sur le disque
     *		\param	chronodoc 	Objet chronodoc à générer (ou id si ancienne methode)
     *		\param	outputlangs	Lang object for output language
     *		\return	int     		1=ok, 0=ko
     */
    function write_file($chronodoc,$outputlangs='')
	{
		global $user,$langs,$conf;

		if (! is_object($outputlangs)) $outputlangs=$langs;
		$outputlangs->load("main");
		$outputlangs->load("dict");
		$outputlangs->load("companies");

		// Get chronodocs_type filename
		if(empty($chronodoc->fk_chronodocs_type) || (!($chronodoc->fetch_chronodocs_type()>0)))
		{
			$this->error=$langs->trans("ErrorInvalidChronodocsType");
			return 0;
		}

		if(empty($chronodoc->chronodocs_type->filename) )
		{
			$this->error=$langs->trans("ErrorChronodocsTypeFilenameNotSet");
			return 0;
		}

		$input_filename=$this->dir_output."/types/".$chronodoc->chronodocs_type->filename;
		$ext=strrchr($input_filename,'.');
		if(empty($ext) || (! file_exists($input_filename)) || (! is_readable($input_filename)))
		{
			$this->error=$langs->trans("ErrorInvalidChronodocsTypeFilename");
			return 0;
		}

		if ($this->dir_output)
		{
			// Définition de l'objet $chronodoc (pour compatibilite ascendante)
			if (! is_object($chronodoc))
			{
				$id = $chronodoc;
				$chronodoc = new Chronodocs_entries($this->db);
				$ret=$chronodoc->fetch($id);
			}


			// Définition de $dir et $file
			if ($chronodoc->specimen)
			{
				$dir = $this->dir_output;
				$file = $dir . "/SPECIMEN".$ext;
			}
			else
			{
				$chronodocref = dol_string_nospecial($chronodoc->ref);
				$dir = $this->dir_output. "/" . $chronodocref;
				$file = $dir . "/" . $chronodocref . $ext;
			}

			if (! file_exists($dir))
			{
				if (create_exdir($dir) < 0)
				{
					$this->error=$langs->trans("ErrorCanNotCreateDir",$dir);
					return 0;
				}
			}

			if (file_exists($dir))
			{
				//Generate wanted file
				copy($input_filename,$file);

				return 1;   // Pas d'erreur
			}
			else
			{
				$this->error=$langs->trans("ErrorCanNotCreateDir",$dir);
				return 0;
			}
		}
		else
		{
			$this->error=$langs->trans("ErrorConstantNotDefined","FAC_OUTPUTDIR");
			return 0;
		}
		$this->error=$langs->trans("ErrorUnknown");
		return 0;   // Erreur par defaut
	}

}

?>
