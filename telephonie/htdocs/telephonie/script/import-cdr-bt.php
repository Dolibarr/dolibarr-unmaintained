<?PHP
/* Copyright (C) 2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * $Id: import-cdr-bt.php,v 1.1 2009/10/20 16:19:23 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/script/import-cdr-bt.php,v $
 *
 * Script d'import des CDR BT
 */

require ("../../master.inc.php");

$opt = getopt("f:");

$file = $opt['f'];
$id_fourn = 2;

if (strlen($file) == 0)
{
  print "Usage :\n php import-cdr-bt.php -f <filename>\n";
  exit;
}

/*
 * Traitement
 *
 */

$files = array();

if (is_dir($file))
{
  $handle=opendir($file);

  if ($handle)
    {
      $i = 0 ;
      $var=True;
      
      while (($xfile = readdir($handle))!==false)
	{
	  if (is_file($file.$xfile) )
	    {
	      if (substr($xfile, -4) == ".csv")
		{
		  $files[$i] = $file.$xfile;
		  dol_syslog($file.$xfile." ajout�");
		  $i++;
		}
	      else
		{
		  dol_syslog($file.$xfile." ignor�");
		}
	    }
	}
      
      closedir($handle);
    }
  else
    {
      dol_syslog("Impossible de libre $file");
      exit ;
    }
}
elseif (is_file($file))
{
  $files[0] = $file;
}
else
{
  dol_syslog("Impossible de libre $file");
  exit ;
}

/*
 * Renommage des fichiers
 *
 */
$datetime = time();
$month = strftime("%m", $datetime);
$year = strftime("%y", $datetime);

if ($month == 1)
{
  $month = "12";
  $year = substr("00".($year - 1), -2) ;
}
else
{
  $month = substr("00".($month - 1), -2) ;
}
$newfiles = array();
$j = 0;
foreach ($files as $xfile)
{
  if (is_readable($xfile))
    {
      dol_syslog("Traitement de ".basename($xfile));
      $newname = str_replace(".csv","-$month$year.csv",$xfile);
      if (substr($xfile,-9) <> "-$month$year.csv")
	{
	  if (rename($xfile, $newname))
	    {
	      dol_syslog("Renomme en ".$newname);
	      $newfiles[$j] = $newname;
	      $j++;
	    }
	  else
	    {
	      print "Erreur de renommage de $xfile\n";
	      exit;
	    }
	}
      else
	{
	  $newfiles[$j] = $xfile;
	  $j++;
	}
    }
}

/*
 * V�rification du fournisseur
 *
 */

$sql = "SELECT f.rowid, f.nom";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_fournisseur as f";
$sql .= " WHERE f.rowid = ".$id_fourn;

if ($db->query($sql))
{  
  $num = $db->num_rows();

  if ($num == 1)
    {
      $row = $db->fetch_row();
      dol_syslog ("Import fichier ".$file);
      dol_syslog("Fournisseur [".$row[0]."] ".$row[1]);
    }
  else
    {
      dol_syslog("Erreur Fournisseur inexistant : ".$id_fourn);
      exit ;
    }
}
else
{
  dol_syslog("Erreur recherche fournisseur");
  exit ;
}


/*
 * Charge les ID de lignes
 *
 */

$sql = "SELECT ligne, rowid ";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_societe_ligne";

$resql = $db->query($sql);

if ($resql)
{  
  $num = $db->num_rows($resql);
  dol_syslog ($num . " lignes charg�es");
  $i = 0;
  $ligneids = array();

  while ($i < $num)
    {
      $row = $db->fetch_row($resql);
      $ligneids[$row[0]] = $row[1];
      $i++;
    }
}
else
{
  dol_syslog("Erreur chargement des lignes");
  dol_syslog($sql);
  exit ;
}


$line_inserted_total = 0;
foreach ($newfiles as $xfile)
{
  if (is_readable($xfile))
    {
      if ( _verif($db, $xfile) == 0)
	{
	  dol_syslog("Lecture du fichier $xfile");
      
	  $error = 0;
	  $line = 0;
	  $line_inserted = 0;
	  $hf = fopen ($xfile, "r");
	  $line = 0;
	  
	  if ($db->query("BEGIN"))
	    {  
	      while (!feof($hf) )
		{
		  $cont = fgets($hf, 1024);
		  
		  if (strlen(trim($cont)) > 0)
		    {
		      // 297400910,2005-03-23 08:08:08,Appels Mob.-ORANGE,0680301933, 106, .3445
		      // 297400910,2005-03-23 09:24:36,Appels Mob.-ORANGE,0675621805, 5, .0162
		      // 297400910,2005-03-23 09:36:55,Appels Mob.-ORANGE,0680301933, 57, .1852
		      
		      $tabline = explode(",", $cont);
		      if (sizeof($tabline) == 6)
			{
			  $index             = $line;
			  $ligne             = "0".$tabline[0];
			  $date              = substr($tabline[1],0,10);
			  
			  //Retournment de la date
			  $date              = substr($date, 8,2)."/".substr($date, 5,2)."/".substr($date, 0,4);
			  
			  $heure             = substr($tabline[1],11,8);
			  
			  $numero            = $tabline[3];
			  $tarif             = $tabline[2];

			  $h = floor(trim($tabline[4]) / 3600);
			  $m = floor((trim($tabline[4]) - ($h * 3600)) / 60);
			  $s = (trim($tabline[4]) - ( ($h * 3600 ) + ($m * 60) ) );
			  
			  if ($h > 0)
			    {
			      $dt = $h . " h " . substr("00".$m, -2) ."mn" . substr("00".$s, -2);
			    }
			  else
			    {
			      if ($m > 0)
				{
				  $dt = substr("00".$m,-2) ."mn" . substr("00".$s, -2);
				}
			      else
				{
				  $dt =  "00mn".substr("00".$s, -2);
				}
			    }
			  
			  $duree_text        = $dt;
			  $tarif_fourn       = "NONE";
			  $montant           = trim($tabline[5]);
			  $duree_secondes    = trim($tabline[4]);
		      
			  if ($ligneids[$ligne] > 0)
			    {
			      $sql = "INSERT INTO ".MAIN_DB_PREFIX."telephonie_import_cdr";
			  
			      $sql .= "(idx,fk_ligne,ligne,date,heure,num,dest,dureetext,tarif,montant,duree";
			      $sql .= ", fichier, fk_fournisseur)";
			  
			      $sql .= " VALUES (";
			      $sql .= "$index";
			      $sql .= ",'".$ligneids[$ligne]."'";
			      $sql .= ",'".$ligne."'";
			      $sql .= ",'".ereg_replace('"','',$date)."'";
			      $sql .= ",'".ereg_replace('"','',$heure)."'";
			      $sql .= ",'".ereg_replace('"','',$numero)."'";
			      $sql .= ",'".addslashes(ereg_replace('"','',$tarif))."'";
			      $sql .= ",'".ereg_replace('"','',$duree_text)."'";
			      $sql .= ",'".ereg_replace('"','',$tarif_fourn)."'";
			      $sql .= ",".ereg_replace(',','.',$montant);
			      $sql .= ",".$duree_secondes;
			      $sql .= ",'".basename($xfile)."'";
			      $sql .= " ,".$id_fourn;
			      $sql .= ")";
			  
			      if(ereg("^[0-9]+$", $duree_secondes))
				{
				  if ($db->query($sql))
				    {
				      $line_inserted++;
				      $line_inserted_total++;
				    }
				  else
				    {
				      dol_syslog("Erreur de traitement de ligne $index");
				      dol_syslog($db->error());
				      dol_syslog($sql);
				      $error++;
				    }
				}
			      else
				{
				  print "Ligne : $cont ignor�e\n";
				  $error++;
				}			  
			    }
			  else
			    {
			      dol_syslog("Ligne : $ligne ignor�e!");
			      $error++;
			    }
			}
		      else
			{
			  dol_syslog("Mauvais format de fichier ligne $line ".sizeof($tabline));
			  dol_syslog($cont);
			  $error++;
			}
		    }
		  $line++;
		}
	      if ($line <> $line_inserted)
		{
		  dol_syslog($line."/".$line_inserted." lignes trait�es/insert effectu�s");
		}
	  
	      if ($error == 0)
		{	  
		  $db->query("COMMIT");
		}
	      else
		{
		  $db->query("ROLLBACK");
		  dol_syslog("ROLLBACK");
		}	  
	    }      
	  fclose($hf);
	}
    }
  else
    {
      print "Erreur lecture : $xfile";
      dol_syslog($xfile . " not readable");
    }
}

dol_syslog($line_inserted_total." insert effectu�s total");

/*
 * Fin
 *
 */

function _verif($db, $file)
{
  $result = 0;
  /*
   * V�rifie que le fichier n'a pas d�j� �t� charg�
   *
   */

  $sql = "SELECT count(fichier)";
  $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_import_cdr";
  $sql .= " WHERE fichier = '".basename($file)."'";
  
  if ($db->query($sql))
    {  
      $num = $db->num_rows();
      
      if ($num == 1)
	{
	  $row = $db->fetch_row();
	  if ($row[0] > 0)
	    {
	      dol_syslog ("Fichier ".$file." d�j� charg� dans import-log");	     
	      $result = -1;
	    }
	}
      else
	{
	  dol_syslog("Erreur v�rif du fichier");
	  $result = -1;
	}
    }
  else
    {
      dol_syslog("Erreur SQL v�rification du fichier");
      $result = -1;
    }

  /*
   * V�rifie que le fichier n'a pas d�j� �t� trait�
   *
   */
  
  $sql = "SELECT count(fichier_cdr)";
  $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_communications_details";
  $sql .= " WHERE fichier_cdr = '".basename($file)."'";
  
  if ($db->query($sql))
    {  
      $num = $db->num_rows();
      
      if ($num == 1)
	{
	  $row = $db->fetch_row();
	  if ($row[0] > 0)
	    {
	      dol_syslog ("Fichier ".$file." d�j� trait�");
	      $result = -1;
	    }
	}
      else
	{
	  dol_syslog("Erreur v�rif du fichier dans les comm");
	  $result = -1;
	}
    }
  else
    {
      dol_syslog("Erreur SQL v�rification du fichier dans les comm");
      dol_syslog($sql);
      $result = -1;
    } 

  return $result;
}


return $error;
