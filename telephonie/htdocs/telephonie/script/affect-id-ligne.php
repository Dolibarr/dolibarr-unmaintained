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
 * $Id: affect-id-ligne.php,v 1.1 2009/10/20 16:19:22 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/script/affect-id-ligne.php,v $
 *
 *
 * Synchronise les id dans la tables des comm
 * scrip temporaire le temps de r�-�crire la facturation
 *
 */
require ("../../master.inc.php");

$error = 0;
$stop = 0 ;

$sql = "SELECT distinct(ligne) as l, count(*)";
$sql .= " FROM llx_telephonie_societe_ligne";
$sql .= " GROUP by l DESC "; 

if ($db->query($sql))
{
  $num = $db->num_rows();
  
  if ($num)
    {
      $row = $db->fetch_row();	
      if ( $row[1] > 1)
	{
	  print "Doublons ligne $row[0]\n";
	  $stop = 1;
	}
    }
  $db->free(); 
}

if ($stop == 0)
{

  for ($i = 0 ; $i < 5000 ; $i++)
    {

      $sql = "SELECT fk_ligne, ligne";
      $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_communications_details";
      $sql .= " WHERE fk_ligne is null";
      $sql .= " LIMIT 1";
      
      if ($db->query($sql))
	{
	  $num = $db->num_rows();
	  
	  if ($num)
	    {
	      $row = $db->fetch_row();	
	      $ligne = $row[1];
	    }
	  $db->free(); 
	}
      
      $sql = "SELECT rowid, ligne";
      $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_societe_ligne";
      $sql .= " WHERE ligne =".$ligne;
      
      if ($db->query($sql))
	{
	  $num = $db->num_rows();
	  
	  if ($num)
	    {
	      $row = $db->fetch_row();
	      $id = $row[0];
	    }
	  $db->free();
	}
      
      $sql = "UPDATE ".MAIN_DB_PREFIX."telephonie_communications_details";
      $sql .= " SET fk_ligne = ".$id;
      $sql .= " WHERE ligne =".$ligne;
      
      if ($db->query($sql))
	{
	  print "$ligne -> $id -> "; 
	  print $db->affected_rows()."\n";
	}
    }
}

?>
