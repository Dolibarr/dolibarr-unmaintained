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
 * $Id: appelsdureemoyenne.class.php,v 1.1 2009/10/20 16:19:25 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/stats/graph/appelsdureemoyenne.class.php,v $
 *
 */

require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/bar.class.php");

class GraphAppelsDureeMoyenne extends GraphBar{


  Function GraphAppelsDureeMoyenne($DB, $file)
  {
    $this->db = $DB;
    $this->file = $file;

    $this->datas = array();
    $this->labels = array();

    $this->client = 0;
    $this->contrat = 0;
    $this->ligne = 0;

    $this->titre = "Dur�e moyenne des appels par mois";
    $this->showframe = true;
    $this->barcolor = "pink";
  }

  Function Graph($datas='', $labels='')
  {    
    $this->GetDatas();

    $this->width = 360;

    if (sizeof($this->datas))
      {
	$this->GraphDraw($this->file, $this->datas, $this->labels);
      }
  }

  Function GetDatas()
  {
    $num = 0;

    $sql = "SELECT date_format(td.date,'%Y%m'), sum(td.duree), count(td.duree)";
    $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_communications_details as td";
    
    if ($this->client == 0 && $this->contrat == 0 && $this->ligne == 0)
      {
	$sql .= " GROUP BY date_format(td.date,'%Y%m') ASC ";
      }
    elseif ($this->client > 0 && $this->contrat == 0 && $this->ligne == 0)
      {
	$sql .= " , ".MAIN_DB_PREFIX."telephonie_societe_ligne as s";   

	$sql .= " WHERE td.ligne = s.ligne";
	$sql .= " AND s.fk_client_comm = ".$this->client;
	$sql .= " GROUP BY date_format(td.date,'%Y%m') ASC ";
      }
    elseif ($this->client == 0 && $this->contrat > 0 && $this->ligne == 0)
      {

	$sql .= " , ".MAIN_DB_PREFIX."telephonie_societe_ligne as s";   

	$sql .= " WHERE td.ligne = s.ligne";
	$sql .= " AND s.fk_contrat = ".$this->contrat;
	$sql .= " GROUP BY date_format(td.date,'%Y%m') ASC ";
      }
    elseif ($this->client == 0 && $this->contrat == 0 && $this->ligne > 0)
      {

	$sql .= " , ".MAIN_DB_PREFIX."telephonie_societe_ligne as s";   

	$sql .= " WHERE td.ligne = s.ligne";
	$sql .= " AND s.rowid = ".$this->ligne;
	$sql .= " GROUP BY date_format(td.date,'%Y%m') ASC ";
      }
    
    if ($this->db->query($sql))
      {
	$num = $this->db->num_rows();
	$i = 0;

	while ($i < $num)
	  {
	    $row = $this->db->fetch_row();

	    $this->labels[$i] = substr($row[0],4,2);
	    $this->datas[$i] = ($row[1] / $row[2] ) ;	    
	    
	    $i++;
	  }
	
	$this->db->free();
      }
    else 
      {
	print $this->db->error() . ' ' . $sql;
	exit ;
      }
    

    if ($this->show_console)
      {
	print $this->client . " " . $cv[$i - 1]."\n";
      }    
  }

}   
?>
