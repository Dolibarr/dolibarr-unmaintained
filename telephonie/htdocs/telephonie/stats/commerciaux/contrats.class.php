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
 * $Id: contrats.class.php,v 1.1 2009/10/20 16:19:28 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/stats/commerciaux/contrats.class.php,v $
 *
 */

require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/pie.class.php");

class GraphCommerciauxContrats extends GraphPie {

  Function GraphCommerciauxContrats($DB, $file)
  {
    $this->db = $DB;
    $this->file = $file;

    $this->client = 0;
    $this->titre = "Nb de contrat sign� par commercial";

    $this->barcolor = "yellow";
    $this->showframe = true;
  }

  Function GraphMakeGraph($type)
  {
    $num = 0;

    if ($type == 'suivi')
      {
	$this->titre = "Contrats suivis par commercial";
      }
    else
      {
	$this->titre = "Contrats sign�s par commercial";
      }

    $sql = "SELECT u.firstname, u.name, count(*) as cc";
    $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_contrat as c";
    $sql .= " , ".MAIN_DB_PREFIX."user as u";
    if ($type == 'suivi')
      {
	$sql .= " WHERE c.fk_commercial_suiv = u.rowid";
      }
    else
      {
	$sql .= " WHERE c.fk_commercial_sign = u.rowid";
      }
    $sql .= " GROUP BY u.rowid ORDER BY CC desc";
    
    $result = $this->db->query($sql);
    if ($result)
      {
	$num = $this->db->num_rows();
	$i = 0;
	$datas = array();
	$labels = array();
	
	while ($i < $num)
	  {
	    $row = $this->db->fetch_row();
	    
	    $labels[$i] = $row[0] . " ".$row[1];
	    $datas[$i] = $row[2];
	    
	    $i++;
	  }
	
	$this->db->free();
      }
    else 
      {
	print $this->db->error() . ' ' . $sql;
      }
    
    $this->GraphDraw($this->file, $datas, $labels);
  }
}   
?>
