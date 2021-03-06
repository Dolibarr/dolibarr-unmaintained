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
 * $Id: commande_presel_statut_view.php,v 1.1 2009/10/20 16:19:24 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/script/commande_presel_statut_view.php,v $
 *
 *
 * Recup�ration des fichiers CDR
 *
 */
require ("../../master.inc.php");
require_once DOL_DOCUMENT_ROOT."/telephonie/lignetel.class.php";

set_time_limit(0);

$host          = CMD_PRESEL_WEB_HOST;
$user_login    = CMD_PRESEL_WEB_USER;
$user_passwd   = CMD_PRESEL_WEB_PASS;

$sql = "SELECT rowid,ligne";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_societe_ligne";
$sql .= " WHERE fk_fournisseur = 4";
$sql .= " AND statut = 2;";

$resql = $db->query($sql);

if ($resql)
{
  $ids = array();
  while ($row = $db->fetch_row($resql))
    {
      array_push($ids, $row[1]);
    }
}
else
{
  exit(1);
}

GetPreselection_byRef($db, $host, $user_login, $user_passwd, $ids);

/*
 * Fonctions
 *
 */

function GetPreselection_byRef($db, $host, $user_login, $user_passwd, $ids)
{  
  $numcli = sizeof($ids);
  $i = 0;

  foreach($ids as $cli)
    {
      $i++;
      $fp = @fsockopen($host, 80, $errno, $errstr, 30);
      if (!$fp)
	{
	  print "Impossible de se connecter au server $errstr ($errno)";
	}
      else
	{
	  $ligne_numero = "";
	  $ligne_service = "";
	  $ligne_presel = "";

	  //GetPreselection_byRef  
	  $url = "/AzurApp_websvc_b3gdb/account.asmx/GetPreselection_byRef?";

	  $url .= "user_login=".  $user_login;
	  $url .= "&user_passwd=".$user_passwd;
	  $url .= "&telnum=".$cli;

	  $out = "GET $url HTTP/1.1\r\n";
	  $out .= "Host: $host\r\n";
	  $out .= "Connection: Close\r\n\r\n";
	  
	  if (fwrite($fp, $out) )
	    {
	      while (!feof($fp))
		{
		  $line = fgets($fp, 1024);
		  
		  if (preg_match("/<Preselection .* \/>/",$line))
		    {	      
		      $results = explode(" ",trim($line));
		      //print_r($results);
		      
		      $array = array();
		      preg_match('/telnum="([0123456789]*)"/', $line, $array);
		      $ligne_numero = $array[1];

		      $array = array();
		      preg_match('/ServiceActive="([\S]*)"/i', $line, $array);
		      $service_active = $array[1];
		      
		      $array = array();
		      preg_match('/PreSelectionActive="([\S]*)"/i', $line, $array);
		      $presel_active = $array[1];
		      
		      $array = array();
		      preg_match('/Service_Statut="([\S]*)"/i', $line, $array);
		      $ligne_service = $array[1];
		      
		      $array = array();
		      preg_match('/PreSelection_Statut="([\S]*)"/i', $line, $array);
		      $ligne_presel = $array[1];
		      
		      print "$i/$numcli ";
		      print $ligne_numero." ";
		      print "$service_active/$presel_active ";
		      print substr($ligne_service.str_repeat(" ",20),0,20);
		      print substr($ligne_presel.str_repeat(" ",20),0,20);
		      print "\n";
		    }
		  
		  if (preg_match("/<Error .* \/>/",$line))
		    {	      
		      $array = array();
		      preg_match('/libelle="(.*)" xmlns:d4p1/', $line, $array);
		      
		      print "$i/$numcli ";
		      print "$cli ErreurAPI ".$array[1]."\n";
		    }
		}
	    }
	  else
	    {
	      print "Impossible d'ecrire sur la socket\n";
	      print "Host : $host\n";
	      print "URL : $url\n";
	    }
	  fclose($fp);
	}
    }
}

?>
