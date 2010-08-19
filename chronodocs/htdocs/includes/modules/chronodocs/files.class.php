<?php
/*
 * Copyright (C) 2004-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2007 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2008 	   Raphael Bertrand (Resultic)  <raphael.bertrand@resultic.fr>
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
        \file       htdocs/includes/modules/chronodocs/files.class.php
        \ingroup    chronodocs
        \brief      Classe de gestion des documents de chronodoc
        \author	Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
        \remarks	Initialy built from FormFile.class.php and htdocs/document.php
        \version    $Id: files.class.php,v 1.5 2010/08/19 15:25:25 hregis Exp $
*/

require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_types.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/files.lib.php");

/**
 \class      FormFile
 \brief      Classe permettant la génération de composants html fichiers ainsi que la gestion d'actions
 */
class ChronodocsFiles extends FormFile
{
	//var $db;		//Already in FormFile
	//var $error; 	//Already in FormFile
	var $urlDocumentPage;


	/**
	 *		\brief     Constructeur
	 *		\param     DB      handler d'accès base de donnée
	 */
	function ChronodocsFiles($DB)
	{
		$this->db = $DB;
		$this->urlDocumentPage=DOL_URL_ROOT.'/chronodocs/document.php';
		$this->dir_output=DOL_DATA_ROOT."/chronodocs";


		return 1;
	}

	/**
	 *      \brief      Affiche la cartouche de la liste des documents d'une propale, facture...
	 *      \param      modulepart          propal=propal, facture=facture, ...
	 *      \param      filename            Sous rep à scanner (vide si filedir deja complet)
	 *      \param      filedir             Repertoire à scanner
	 *      \param      urlsource           Url page origine (pour retour)
	 *      \param      genallowed          Génération autorisée (1/0 ou array des formats)
	 *      \param      delallowed          Suppression autorisée (1/0)
	 *      \param      modelselected       Modele à pré-sélectionner par défaut
	 *      \param      modelliste			Tableau des modeles possibles
	 *      \param      forcenomultilang	N'affiche pas option langue meme si MAIN_MULTILANGS défini
	 *      \param      iconPDF             N'affiche que l'icone PDF avec le lien (1/0)
	 * 		\param		maxfilenamelength	Max length for filename shown
	 *      \remarks    Le fichier de facture détaillée est de la forme
	 *                  REFFACTURE-XXXXXX-detail.pdf ou XXXXX est une forme diverse
	 *		\return		int					<0 si ko, nbre de fichiers affichés si ok
	 */
	function show_documents($modulepart,$filename,$filedir,$urlsource,$genallowed,$delallowed=0,$modelselected='',$modelliste=array(),$forcenomultilang=0,$iconPDF=0,$maxfilenamelength=28)
	{
		// filedir = conf->...dir_ouput."/".get_exdir(id)
		include_once(DOL_DOCUMENT_ROOT.'/lib/files.lib.php');

		global $langs,$bc,$conf;

		dolibarr_syslog(get_class($this)."::show_documents filename=$filename filedir=$filedir");

		$var=true;

		if ($iconPDF == 1)
		{
			$genallowed = '';
			$delallowed = 0;
			$modelselected = '';
			$modelliste = '';
			$forcenomultilang=0;
		}

		$filename = dol_string_nospecial($filename);
		$headershown=0;
		$i=0;

		// Affiche en-tete tableau
		if ($genallowed)
		{
			$modellist=array();
			if ($modulepart == 'chronodocs_entries')
			{
				if (is_array($genallowed)) $modellist=$genallowed;
				else
				{
					include_once(DOL_DOCUMENT_ROOT.'/includes/modules/chronodocs/modules_chronodocs.php');
					$model=new TplChronodocs();
					$modellist=$model->liste_modeles($this->db);
				}
			}
			else
			{
				dolibarr_print_error($this->db,'Bad value for modulepart');
				return -1;
			}
			if(!empty($modellist))
			{
				$headershown=1;

				$html = new Form($db);

				print '<form action="'.$urlsource.'#builddoc" method="post">';
				print '<input type="hidden" name="action" value="builddoc">';

				print_titre($langs->trans("Documents"));
				print '<table class="border" width="100%">';

				print '<tr '.$bc[$var].'>';
				print '<td align="center">'.$langs->trans('Model').' ';
				$html->selectarray('model',$modellist,$modelselected,0,0,1);
				$texte=$langs->trans('Generate');
				print '</td>';
				print '<td align="center">';
				if($conf->global->MAIN_MULTILANGS && ! $forcenomultilang)
				{
					$html->select_lang($langs->getDefaultLang());
				}
				else
				{
					print '&nbsp;';
				}
				print '</td>';
				print '<td align="center" colspan="'.($delallowed?'2':'1').'">';
				print '<input class="button" type="submit" value="'.$texte.'">';
				print '</td></tr>';
			}
		}

		// Recupe liste des fichiers
		$png = '';
		$filter = '';
		if ($iconPDF==1)
		{
			$png = '|\.png$';
			$filter = $filename.'.pdf';
		}
		$file_list=dol_dir_list($filedir,'files',0,$filter,'\.meta$'.$png,'date',SORT_DESC);

		// Affiche en-tete tableau si non deja affiché
		if (sizeof($file_list) && ! $headershown && !$iconPDF)
		{
			$headershown=1;
			print_titre($langs->trans("Documents"));
			print '<table class="border" width="100%">';
		}

		// Boucle sur chaque ligne trouvée
		foreach($file_list as $i => $file)
		{
			$var=!$var;

			// Défini chemin relatif par rapport au module pour lien download
			$relativepath=$file["name"];								// Cas general
			if ($filename) $relativepath=$filename."/".$file["name"];	// Cas propal, facture...
			// Autre cas
			if ($modulepart == 'don')        { $relativepath = get_exdir($filename,2).$file["name"]; }
			if ($modulepart == 'export')     { $relativepath = $file["name"]; }

			if (!$iconPDF) print "<tr ".$bc[$var].">";

			// Affiche nom fichier avec lien download
			if (!$iconPDF) print '<td>';
			print '<a href="'.$this->urlDocumentPage.'?action=get_file&modulepart='.$modulepart.'&amp;file='.urlencode($relativepath).'">';
			if (!$iconPDF)
			{
				print img_mime($file["name"],$langs->trans("File").': '.$file["name"]).' '.dolibarr_trunc($file["name"],$maxfilenamelength);
			}
			else
			{
				print img_pdf($file["name"],2);
			}
			print '</a>';
			if (!$iconPDF) print '</td>';
			// Affiche taille fichier
			if (!$iconPDF) print '<td align="right">'.filesize($filedir."/".$file["name"]). ' bytes</td>';
			// Affiche date fichier
			if (!$iconPDF) print '<td align="right">'.dolibarr_print_date(filemtime($filedir."/".$file["name"]),'dayhour').'</td>';

			if ($delallowed)
			{
				print '<td align="right"><a href="'.$this->urlDocumentPage.'?action=remove_file&amp;modulepart='.$modulepart.'&amp;file='.urlencode($relativepath).'&amp;urlsource='.urlencode($urlsource).'">'.img_delete().'</a></td>';
			}

			if (!$iconPDF) print '</tr>';

			$i++;
		}


		if ($headershown)
		{
			// Affiche pied du tableau
			print "</table>\n";
			if ($genallowed)
			{
				print '</form>';
			}
		}

		return ($i?$i:$headershown);
	}

	/**
	 *      \brief      Show list of documents in a directory
	 *      \param      filearray			Array of files loaded by dol_dir_list function
	 * 		\param		object				Object on which document is linked to
	 * 		\param		modulepart			Value for modulepart used by download wrapper
	 * 		\param		param				Parameters on sort links
	 * 		\param		forcedownload		Mime type is forced to 'application/binary' to have a download
	 * 		\param		relativepath		Relative path of docs (autodefined if not provided)
	 * 		\param		permtodelete		Permission to delete
	 *		\return		int					<0 if KO, nb of files shown if OK
	 */
	function list_of_documents($filearray,$object,$modulepart='',$param,$forcedownload=0,$relativepath='',$permtodelete=1)
	{
		global $user, $conf, $langs;
		global $bc;
		global $sortfield, $sortorder;

		 dolibarr_syslog(get_class($this)."::list_of_documents relativepath=".($relativepath?$relativepath:'autodefined'));

		// Affiche liste des documents existant
		print_titre($langs->trans("AttachedFiles"));

		$url=$_SERVER["PHP_SELF"];
		print '<table width="100%" class="noborder">';
		print '<tr class="liste_titre">';
		print_liste_field_titre($langs->trans("Document"),$_SERVER["PHP_SELF"],"name","",$param,'align="left"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Size"),$_SERVER["PHP_SELF"],"size","",$param,'align="right"',$sortfield,$sortorder);
		print_liste_field_titre($langs->trans("Date"),$_SERVER["PHP_SELF"],"date","",$param,'align="center"',$sortfield,$sortorder);
		print '<td>&nbsp;</td>';
		print '</tr>';

		$var=true;
		foreach($filearray as $key => $file)
		{
			if (!is_dir($dir.$file['name'])
			&& $file['name'] != '.'
			&& $file['name'] != '..'
			&& $file['name'] != 'CVS'
			&& ! eregi('\.meta$',$file['name']))
			{
				// Define relative path used to store the file
				if (! $relativepath)
				{
					$relativepath=$object->ref.'/';
				}

				$var=!$var;
				print "<tr $bc[$var]><td>";
				print '<a href="'.$this->urlDocumentPage.'?action=get_file';
				if ($modulepart) print '&modulepart='.$modulepart;
				if ($forcedownload) print '&type=application/binary';
				print '&file='.urlencode($relativepath.$file['name']).'">';
				print img_mime($file['name']).' ';
				print $file['name'];
				print '</a>';
				print "</td>\n";
				print '<td align="right">'.dol_print_size($file['size']).'</td>';
				print '<td align="center">'.dolibarr_print_date($file['date'],"dayhour").'</td>';
				print '<td align="right">';
				//print '&nbsp;';
				if ($permtodelete)
				print '<a href="'.$url.'?id='.$object->id.'&amp;section='.$_REQUEST["section"].'&amp;action=delete&urlfile='.urlencode($file['name']).'">'.img_delete().'</a>';
				else
				print '&nbsp;';
				print "</td></tr>\n";
			}
		}
		if (sizeof($filearray) == 0) print '<tr '.$bc[$var].'><td colspan="4">'.$langs->trans("NoFileFound").'</td></tr>';
		print "</table>";
		// Fin de zone

	}

	/*
	\brief      Get Dir list for object
	\param     chronodocs object (can be null if other params until path -included- given)
	\param     delallowed 	boolean giving read access (optional)
	\param     modulepart 	string (optional)
	\param     filter        	Regex for filter(optional)
	\param     exludefilter  	Regex for exclude filter (example: '\.meta$') (optional)
	\param     path 	string (optional)
	\param     sortcriteria	Sort criteria ("name","date","size") (optional)
	\param     sortorder	Sort order (SORT_ASC, SORT_DESC) (optional)
	\param     mode		0=Return array with only keys needed, 1=Force all keys to be loaded (optional)
	\return     array		Array of array('name'=>xxx,'date'=>yyy,'size'=>zzz) (optional)
	*/
	function get_filelist($user,$chronodocs,$readallowed='',$modulepart='',$filter='',$excludefilter='',$path='',$sortcriteria="name", $sortorder=SORT_ASC, $mode=1)
	{
		dolibarr_syslog(get_class($this)."::get_filelist");
		if($readallowed || (empty($readallowed) && $user->rights->chronodocs->entries->read))
		{
			if(empty($path))
			{
				if(empty($modulepart) || $modulepart=='chronodocs_entries')
				{
					$path = $this->dir_output . "/" . dol_string_nospecial($chronodocs->ref);

					if(empty($modulepart))
						$excludefilter='\.meta$';

					$mode=1;
				}
				else if($modulepart=='chronodocs_types')
				{
					$path = $this->dir_output . "/types";
					$mode=1;
				}
				else return array();
			}
			$filearray=dol_dir_list($path,"files",0,$filter, $excludefilter, $sortcriteria, $sortorder, $mode);
			return $filearray;
		}
		else return array();
	}


	/*
	\brief      Wrapper to allow upload of data files
	\param     chronodocs object (can be null if other params given and no set_date_u needed)
	\param     delallowed (optional) boolean giving read access
	\param     upload_dir string (optional)
	\param     dest_file string (optional)
	*/
	function process_action_sendit($user,$chronodocs,$writeallowed='',$upload_dir='',$dest_file='')
	{
		global $conf,$langs;

		require_once DOL_DOCUMENT_ROOT.'/lib/files.lib.php';

		dolibarr_syslog(get_class($this)."::process_action_sendit");

		if($writeallowed || (empty($writeallowed) && $user->rights->chronodocs->entries->write))
		{
			if(empty($upload_dir))
			{
				$upload_dir = $this->dir_output . "/" . dol_string_nospecial($chronodocs->ref);
			}
			if (! is_dir($upload_dir)) create_exdir($upload_dir);

			if (is_dir($upload_dir))
			{
				if(empty($dest_file))
					$dest_file=$upload_dir . "/" . $_FILES['userfile']['name'];
				else
					$dest_file=$upload_dir . "/" . $dest_file;
				if (dol_move_uploaded_file($_FILES['userfile']['tmp_name'],$dest_file ,0) > 0)
				{
					if(!empty($chronodocs->id))
						$chronodocs->set_date_u($user); // Update date_u and user_u
					$mesg = '<div class="ok">'.$langs->trans("FileTransferComplete").'</div>';
					//print_r($_FILES);
				}
				else
				{
					// Echec transfert (fichier dépassant la limite ?)
					$mesg = '<div class="error">'.$langs->trans("ErrorFileNotUploaded").'</div>';
					// print_r($_FILES);
				}
			}
			else
			{
				// Repertoire upload_dir absent malgre tentative creation
				$mesg = '<div class="error">'.$langs->trans("ErrorFileNotUploaded").'</div>';
				// print_r($_FILES);
			}

		}
		else
		{
			// Droits insuffisants
			$mesg = '<div class="error">'.$langs->trans("ErrorFileNotUploaded").'</div>';
			// print_r($_FILES);
		}

		return $mesg;
	}

	/*
	\brief      Wrapper to allow deletion of data files
	\param     chronodocs object
	\param     delallowed (optional) boolean giving read access
	\param     upload_dir string (optional)
	\param     original_file string (optional)
	*/
	function process_action_delete($user,$chronodocs,$delallowed='',$upload_dir='',$original_file='')
	{
		global $conf,$langs;

		dolibarr_syslog(get_class($this)."::process_action_delete");

		if($delallowed || (empty($delallowed) && $user->rights->chronodocs->entries->delete))
		{
			if(empty($upload_dir))
			{
				$upload_dir = $this->dir_output . "/" . dol_string_nospecial($chronodocs->ref);
			}
			if(empty($original_file))
				$file = $upload_dir . '/' . urldecode($_GET['urlfile']);
			else
				$file = $upload_dir . '/' . $original_file;
	    	dol_delete_file($file);
			$chronodocs->set_date_u($user);
	        $mesg = '<div class="ok">'.$langs->trans("FileWasRemoved").'</div>';
		}
		else
		{
			// Repertoire upload_dir absent malgre tentative creation
			$mesg = '<div class="error">'.$langs->trans("Error").'</div>';
			// print_r($_FILES);
		}

		return $mesg;
	}

	/*
	\brief      Wrapper to allow download of data files
	\param     chronodocs object
	\param     readallowed (optional) boolean giving read access
	\param     dir_output string (optional)
	\param     original_file string (optional)
	*/
	function process_actions_file($user,$object,$readallowed='',$dir_output='',$original_file='')
	{
		global $conf,$langs;

		dolibarr_syslog(get_class($this)."::process_actions_file");

		$default_modulepart='chronodocs_entries';
		if($readallowed || (empty($readallowed) && $user->rights->chronodocs->entries->read))
		{
			if(empty($original_file)) $original_file = urldecode($_GET["file"]);
			if(empty($dir_output)) $dir_output=$this->dir_output;
			$action = $_GET["action"];
			$modulepart = urldecode($_GET["modulepart"]);
			$urlsource = urldecode($_GET["urlsource"]);

			// Define mime type
			$type = 'application/octet-stream';
			if (! empty($_GET["type"])) $type=urldecode($_GET["type"]);
			else $type=dol_mimetype($original_file);

			// Define attachment (attachment=1 to force popup 'save as')
			$attachment = true;
			if (eregi('\.sql$',$original_file))     { $attachment = true; }
			if (eregi('\.html$',$original_file)) 	{ $attachment = false; }
			if (eregi('\.csv$',$original_file))  	{ $attachment = true; }
			if (eregi('\.pdf$',$original_file))  	{ $attachment = true; }
			if (eregi('\.xls$',$original_file))  	{ $attachment = true; }
			if (eregi('\.jpg$',$original_file)) 	{ $attachment = true; }
			if (eregi('\.png$',$original_file)) 	{ $attachment = true; }
			if (eregi('\.tiff$',$original_file)) 	{ $attachment = true; }
			if (eregi('\.vcs$',$original_file))  	{ $attachment = true; }
			if (eregi('\.ics$',$original_file))  	{ $attachment = true; }

			// Suppression de la chaine de caractere ../ dans $original_file
			$original_file = str_replace("../","/", "$original_file");
			// find the subdirectory name as the reference
			$refname=basename(dirname($original_file)."/");

			$encoding='ISO-8859-1';				// By default
			$sqlprotectagainstexternals='';

			if ($modulepart && $modulepart!=$default_modulepart)
			{ // si on n'est pas dans le module par défaut
				$accessallowed=0;
			    // On fait une verification des droits et on definit le repertoire concerne

			    // Wrapping pour les types de documents
			    if ($modulepart == 'chronodocs_types')
			    {
			        $user->getrights('chronodocs');
			        if ($user->rights->chronodocs->types->read)
			        {
			            $accessallowed=1;
						if($action == 'remove_file')
							$accessallowed=($user->rights->chronodocs->types->delete)?1:0;
			        }
			        $original_file=$dir_output.'/types/'.$original_file;
					//$sqlprotectagainstexternals = "SELECT fk_soc as fk_soc FROM ".MAIN_DB_PREFIX."$modulepart WHERE ref='$refname'";
			    }
			}
			else
			{
				$modulepart = $default_modulepart;

				// Wrapping pour les chronodocs
				$user->getrights('chronodocs');
				if ($user->rights->chronodocs->entries->read)
				{
					$accessallowed=1;
					if($action == 'remove_file')
						$accessallowed=($user->rights->chronodocs->entries->delete)?1:0;
				}
			    $original_file=$dir_output.'/'.$original_file;
			}

			// Basic protection (against external users only)
			if ($user->societe_id > 0)
			{
				if ($sqlprotectagainstexternals)
				{
					$resql = $db->query($sqlprotectagainstexternals);
					if ($resql)
					{
					   $obj = $db->fetch_object($resql);
					   $num=$db->num_rows($resql);
					   if ($num>0 && $user->societe_id != $obj->fk_soc)
					      $accessallowed=0;
					}
				}
			}

			// Security:
			// Limite acces si droits non corrects
			if (! $accessallowed)
			{
			    accessforbidden();
			}

			// Security:
			// On interdit les remontees de repertoire ainsi que les pipe dans
			// les noms de fichiers.
			if (eregi('\.\.',$original_file) || eregi('[<>|]',$original_file))
			{
				dolibarr_syslog("get_class($this).::process_actions_file Refused to deliver file ".$original_file);
				// Do no show plain path in shown error message
				dolibarr_print_error(0,$langs->trans("ErrorFileNameInvalid",$_GET["file"]));
				exit;
			}


			if ($action == 'remove_file')
			{
				/*
				 * Suppression fichier
				 */
				clearstatcache();
				$filename = basename($original_file);

				dolibarr_syslog("get_class($this).::process_actions_file remove $original_file $filename $urlsource");

				if (! file_exists($original_file))
				{
				    dolibarr_print_error(0,$langs->trans("ErrorFileDoesNotExists",$_GET["file"]));
				    exit;
				}
				unlink($original_file);

				dolibarr_syslog("get_class($this).::process_actions_file back to ".urldecode($urlsource));

				header("Location: ".urldecode($urlsource));

				return;
			}
			else
			{
				/*
				 * Ouvre et renvoi fichier
				 */
				clearstatcache();
				$filename = basename($original_file);

				dolibarr_syslog("document.php download $original_file $filename content-type=$type");

				if (! file_exists($original_file))
				{
				    dolibarr_print_error(0,$langs->trans("ErrorFileDoesNotExists",$original_file));
				    exit;
				}


				// Les drois sont ok et fichier trouve, on l'envoie

				if ($encoding)   header('Content-Encoding: '.$encoding);
				if ($type)       header('Content-Type: '.$type);
				if ($attachment) header('Content-Disposition: attachment; filename="'.$filename.'"');

				// Ajout directives pour resoudre bug IE
				header('Cache-Control: Public, must-revalidate');
				header('Pragma: public');

				readfile($original_file);

				return;
			}

		}
		else
		{
			accessforbidden();
		}
	}
}


?>
