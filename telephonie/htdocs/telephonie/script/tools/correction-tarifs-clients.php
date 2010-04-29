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
 * $Id: correction-tarifs-clients.php,v 1.3 2010/04/29 14:54:33 grandoc Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/script/tools/correction-tarifs-clients.php,v $
 *
 *
 * Ce script se veut plus un squelette pour effectuer des op�rations sur la base
 * qu'un r�el scrip de production.
 *
 * Recalcul le montant d'une facture lors d'une erreur de tarif
 *
 */

require ("../../../master.inc.php");

require_once (DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/lignetel.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/facturetel.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/telephonie-tarif.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/communication.class.php");

$tarifs = array(1290,1291,1292);
$corrections = array();

$corrections[1290] = array();
$corrections[1291] = array();
$corrections[1292] = array();

$clients = array();

$sql = "SELECT fk_client FROM ".MAIN_DB_PREFIX."telephonie_tarif_client";
$sql .= " WHERE fk_tarif = 1289";

$resql = $db->query($sql);
  
if ( $resql )
{
  $num = $db->num_rows($resql);
  $i = 0;

  print "$num clients trouv�s\n";

  while ($i < $num)
    {
      $row = $db->fetch_row($resql);

      array_push($clients, $row[0]);
      $i++;
    }
  $db->free($resql);
}
else
{
  $error++;
}


foreach ($tarifs as $tarif)
{
  foreach ($clients as $client)
    {
      $sql = "SELECT fk_client FROM ".MAIN_DB_PREFIX."telephonie_tarif_client";
      $sql .= " WHERE fk_client = ".$client;
      $sql .= " AND fk_tarif = ".$tarif;
  
      $resql = $db->query($sql);
  
      if ( $resql )
	{
	  $num = $db->num_rows($resql);

	  if ($num == 0)
	    {
	      array_push($corrections[$tarif], $client);

	      correct_tarif($db, $client, $tarif);

	    }

	  $db->free($resql);
	}
      else
	{
	  $error++;
	} 
    }
}


foreach ($tarifs as $tarif)
{
  print "Tarif $tarif : ".sizeof($corrections[$tarif])."\n";
}

function correct_tarif($db, $client, $tarif)
{
  $error = 0;

  $sql = "SELECT temporel, fixe FROM ".MAIN_DB_PREFIX."telephonie_tarif_client";
  $sql .= " WHERE fk_tarif = 1289";
  $sql .= " AND fk_client = $client";
  
  $resql = $db->query($sql);

  if ($resql)
    {
      $row = $db->fetch_row($resql);

      $temporel = $row[0];
      $fixe = $row[1];
    }
  else
    {
      print $db->error();
      $error++;
    }

  $db->begin();
  
  $sql = "REPLACE INTO ".MAIN_DB_PREFIX."telephonie_tarif_client";
  $sql .= " (fk_tarif, fk_client, temporel, fixe, fk_user) VALUES ";
  $sql .= " (".$tarif.",".$client.",'".$temporel."','".$fixe."',1)";
  
  if (! $db->query($sql) )
    {
      $error++;
      print $db->error();
    }
  
  $sql = "INSERT INTO ".MAIN_DB_PREFIX."telephonie_tarif_client_log";
  $sql .= " (fk_tarif, fk_client, temporel, fixe, fk_user, datec) VALUES ";
  $sql .= " (".$tarif.",".$client.",'".$temporel."','".$fixe."',1,now())";
  
  if (! $db->query($sql) )
    {
      $error++;
      print $db->error();
    }
  
  if ( $error == 0 )
    {
      $db->commit();
    }
  else
    {
      $db->rollback();
      print $db->error();
    }     
}


$db->close();
?>
