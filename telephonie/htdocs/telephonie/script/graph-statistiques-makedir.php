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
 * $Id: graph-statistiques-makedir.php,v 1.1 2009/10/20 16:19:23 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/script/graph-statistiques-makedir.php,v $
 *
 * G�n�ration des r�pertoires
 *
 *
 */
require ("../../master.inc.php");

$img_root = DOL_DATA_ROOT."/graph/telephonie/";

/*
 * Cr�ation des r�pertoires
 *
 */
$dirs[0] = DOL_DATA_ROOT."/graph/";
$dirs[1] = DOL_DATA_ROOT."/graph/telephonie/";
$dirs[2] = DOL_DATA_ROOT."/graph/telephonie/ca/";
$dirs[3] = DOL_DATA_ROOT."/graph/telephonie/client/";
$dirs[4] = DOL_DATA_ROOT."/graph/telephonie/commercials/";
$dirs[5] = DOL_DATA_ROOT."/graph/telephonie/communications/";
$dirs[6] = DOL_DATA_ROOT."/graph/telephonie/contrats/";
$dirs[7] = DOL_DATA_ROOT."/graph/telephonie/factures/";
$dirs[8] = DOL_DATA_ROOT."/graph/telephonie/lignes/";
$dirs[9] = DOL_DATA_ROOT."/graph/telephonie/commerciaux/";
$dirs[10] = DOL_DATA_ROOT."/graph/telephonie/commerciaux/groupes/";
$dirs[11] = DOL_DATA_ROOT."/graph/telephonie/distributeurs/";

$numdir = (sizeof($dirs) + 2);
$i = $numdir ;

$sql = "SELECT distinct fk_commercial";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_societe_ligne";

if ($db->query($sql))
{
  $num = $db->num_rows();
  $j = 0;
  
  while ($j < $num)
    {
      $row = $db->fetch_row();	
      
      $dirs[$i] = DOL_DATA_ROOT."/graph/telephonie/commercials/".$row[0];
      
      $i++;
      $j++;
    }
}

/* Groupes de commerciaux */

$sql = "SELECT rowid ";
$sql .= " FROM ".MAIN_DB_PREFIX."usergroup";

if ($db->query($sql))
{
  $num = $db->num_rows();
  $j = 0;
  
  while ($j < $num)
    {
      $row = $db->fetch_row();	
      
      $dirs[$i] = DOL_DATA_ROOT."/graph/telephonie/commerciaux/groupes/".$row[0];
      
      $i++;
      $j++;
    }
}

/* Distributeurs */

$sql = "SELECT rowid ";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_distributeur";

if ($db->query($sql))
{
  $num = $db->num_rows();
  $j = 0;
  
  while ($j < $num)
    {
      $row = $db->fetch_row();	
      
      $dirs[$i] = DOL_DATA_ROOT."/graph/telephonie/distributeurs/".$row[0];
      
      $i++;
      $j++;
    }
}


/* Clients */

for ($j = 0 ; $j < 10 ; $j++)
{
  $dirs[$i] = DOL_DATA_ROOT."/graph/telephonie/client/".$j;
  $i++;
}

/*
 *
 */

for ($j = 0 ; $j < 10 ; $j++)
{
  $dirs[$i] = DOL_DATA_ROOT."/graph/".$j;
  $i++;
}

for ($j = 0 ; $j < 10 ; $j++)
{
  $dirs[$i] = DOL_DATA_ROOT."/graph/".$j."/telephonie";
  $i++;
}


for ($j = 0 ; $j < 10 ; $j++)
{
  $dirs[$i] = DOL_DATA_ROOT."/graph/".$j."/telephonie/client/";
  $i++;
  $dirs[$i] = DOL_DATA_ROOT."/graph/".$j."/telephonie/ligne/";
  $i++;
  $dirs[$i] = DOL_DATA_ROOT."/graph/".$j."/telephonie/commercial/";
  $i++;
  $dirs[$i] = DOL_DATA_ROOT."/graph/".$j."/telephonie/contrat/";
  $i++;
}

/*
 *
 */

$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."societe";

if ($db->query($sql))
{
  $num = $db->num_rows();
  $j = 0;
  
  while ($j < $num)
    {
      $row = $db->fetch_row();	
      
      $dirs[$i] = DOL_DATA_ROOT."/graph/telephonie/client/".substr($row[0],0,1)."/".$row[0]."/";     
      $i++;

      $dirs[$i] = DOL_DATA_ROOT."/graph/".substr($row[0],-1)."/telephonie/client/".$row[0]."/";     
      $i++;

      $j++;
    }
}

$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."telephonie_contrat";

if ($db->query($sql))
{
  $num = $db->num_rows();
  $j = 0;
  
  while ($j < $num)
    {
      $row = $db->fetch_row();	
      
      $dirs[$i] = DOL_DATA_ROOT."/graph/".substr($row[0],-1)."/telephonie/contrat/".$row[0]."/";
      $i++;

      $j++;
    }
}

/*
 *
 */ 
$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."telephonie_societe_ligne";

if ($db->query($sql))
{
  $num = $db->num_rows();
  $j = 0;
  
  while ($j < $num)
    {
      $row = $db->fetch_row();	
      
      $dirs[$i] = DOL_DATA_ROOT."/graph/".substr($row[0],-1)."/telephonie/ligne/".$row[0]."/";
      $i++;

      $j++;
    }
}
/*
 *
 */

if (is_array($dirs))
{
  foreach ($dirs as $key => $value)
    {
      $dir = $value;      
      create_dir($dir);
    }
}

$base_dir = DOL_DATA_ROOT.'/graph/telephonie/lignes/';

for ($i = 0 ; $i < 10 ; $i++)
{
  $dir = $base_dir . $i . "/";
      
  create_dir($dir);

  for ($j = 0 ; $j < 10 ; $j++)
    {
      $dir = $base_dir . $i . "/". $j . "/";
      
      create_dir($dir);

      for ($k = 0 ; $k < 10 ; $k++)
	{
	  $dir = $base_dir . $i . "/". $j . "/". $k . "/";
	  
	  create_dir($dir);
	}
    }  
}

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
