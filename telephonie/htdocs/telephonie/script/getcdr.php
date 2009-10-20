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
 * $Id: getcdr.php,v 1.1 2009/10/20 16:19:22 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/script/getcdr.php,v $
 *
 *
 * Recup�ration des fichiers CDR
 *
 */
require ("../../master.inc.php");

$nbdays = 1;

for ($i = 1 ; $i < sizeof($argv) ; $i++)
{
  if ($argv[$i] == "-n")
    {
      $nbdays = $argv[$i+1];
    }
  if ($argv[$i] == "-v")
    {
      $verbose = 1;
    }
}

if (! is_numeric($nbdays))
{
  die("Bad argument $nbdays\n");
}

$ftp_server    = GETCDR_FTP_SERVER;
$ftp_user_name = GETCDR_FTP_USER;
$ftp_user_pass = GETCDR_FTP_PASS;

// Mise en place d'une connexion basique
$conn_id = ftp_connect($ftp_server);

// Identification avec un nom d'utilisateur et un mot de passe
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// V�rification de la connexion
if (!$conn_id)
{
  echo "La connexion FTP a �chou�e !";
  echo "Tentative de connexion au serveur $ftp_server";
  exit;
}

// V�rification de la connexion
if (!$login_result)
{
  echo "L'authentification  FTP a �chou�e !";
  echo "Tentative de connexion au serveur $ftp_server pour l'utilisateur $ftp_user_name";
  exit;
}

if (!file_exists(DOL_DATA_ROOT.'/telephonie/CDR/temp/'))
{
  create_dir(DOL_DATA_ROOT.'/telephonie/CDR/temp/');
}

$date = time() - (24 * 3600 * $nbdays); 

$file = "daily_report_".strftime("%Y%m%d", $date).".zip";

$remote_file = 'cdr/'.$file;

$remote_size = ftp_size($conn_id, $remote_file);
if ($verbose)
  echo "R�cup�ration de ".$remote_size." Ko\n";

$local_file = DOL_DATA_ROOT.'/telephonie/CDR/temp/'.$file;
$handle = fopen($local_file, 'w');

if (ftp_fget($conn_id, $handle, $remote_file, FTP_BINARY, 0))
{
  if ($verbose)
    echo "Le chargement a r�ussi dans ".$local_file."\n";
}
else
{
  echo "Echec de recuperation du fichier ".$remote_file."\n";
}

// Fermeture du flux FTP
ftp_close($conn_id);

$local_size = filesize($local_file);

if (file_exists($local_file) && $local_size === $remote_size && $local_size > 0)
{
  // Dezippage du fichier
  $zip = zip_open($local_file);
  
  if ($zip) {
    
    while ($zip_entry = zip_read($zip))
      {
	if ($verbose)	
	  {
	    echo "Nom du fichier    : " . zip_entry_name($zip_entry) . "\n";
	    echo "Taille r�elle     : " . zip_entry_filesize($zip_entry) . "\n";
	    echo "Taille compress�e : " . zip_entry_compressedsize($zip_entry) . "\n";
	    echo "M�thode           : " . zip_entry_compressionmethod($zip_entry) . "\n";
	  }
	
	if (zip_entry_open($zip, $zip_entry, "r"))
	  {
	    if ($verbose)
	      echo "Decompression dans ".DOL_DATA_ROOT.'/telephonie/CDR/atraiter/'.zip_entry_name($zip_entry)."\n";
	    
	    $fp = fopen(DOL_DATA_ROOT.'/telephonie/CDR/atraiter/'.zip_entry_name($zip_entry),"w");
	    
	    if ($fp)
	      {
		$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
		
		if (fwrite($fp, $buf) === FALSE)
		  {
		    echo "Erreur d'ecriture\n";
		  }
		fclose($fp);
	      }
	    zip_entry_close($zip_entry);
	  }
      }
    zip_close($zip);
  }
  
  // Archivage du fichier
    
}
else
{
  print "Erreur de r�cup�ration du fichier ".$local_file."\n";
  print "Remote size ".$remote_size."\n";
  print "Local  size ".$local_size."\n";
}

if (!file_exists(DOL_DATA_ROOT.'/telephonie/CDR/archive/'))
{
  create_dir(DOL_DATA_ROOT.'/telephonie/CDR/archive/');
}

$dir = DOL_DATA_ROOT.'/telephonie/CDR/archive/'.strftime("%Y", $date);
if (!file_exists($dir))
  create_dir($dir);

$dir = DOL_DATA_ROOT.'/telephonie/CDR/archive/'.strftime("%Y", $date).'/'.strftime("%m", $date);
if (!file_exists($dir))
  create_dir($dir);

function create_dir($dir)
{
  if (! file_exists($dir))
    {
      umask(0);
      if (! @mkdir($dir, 0755))
	{
	  die ("Erreur: Le r�pertoire ".$dir." n'existe pas et Dolibarr n'a pu le cr�er.");
	}
    }
}

?>
