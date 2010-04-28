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
 * $Id: statut.class.php,v 1.2 2010/04/28 07:41:31 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/stats/lignes/statut.class.php,v $
 *
 */

require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/pie.class.php");

class GraphLignesStatut extends GraphPie {

  Function GraphLignesStatut($DB, $file)
  {
    $this->db = $DB;
    $this->file = $file;

    $this->client = 0;
    $this->titre = "Statuts des lignes";

    $this->barcolor = "yellow";
    $this->showframe = true;
  }

  Function GraphMakeGraph($commercial_id=0)
  {
    $num = 0;

    if ($commercial_id > 0)
      {
	$cuser = new User($this->db);
	$cuser->fetch($commercial_id);
	$this->titre = "Statuts des lignes de ".$cuser->fullname;
      }


    $sql = "SELECT statut, count(*)";
    $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_societe_ligne";
    $sql .= " WHERE statut in (2,3,6,7)";

    if ($commercial_id > 0)
      {
	$sql .= " AND fk_commercial_sign =".$commercial_id;
      }

    $sql .= " GROUP BY statut ASC";


    $statuts[-1] = "Attente";
    $statuts[1] = "A commander";
    $statuts[2] = "Command�e";
    $statuts[3] = "Activ�e";
    $statuts[4] = "A r�silier";
    $statuts[5] = "Resil en cours";
    $statuts[6] = "Resili�e";
    $statuts[7] = "Rejet�e";


    $colors_def[2] = 'blue';
    $colors_def[3] = 'green';
    $colors_def[6] = 'red';
    $colors_def[7] = 'black';

    $this->colors = array();

    if ($this->db->query($sql))
      {
	$num = $this->db->num_rows();
	$i = 0;
	$datas = array();
	$labels = array();

	while ($i < $num)
	  {
	    $row = $this->db->fetch_row();

	    $datas[$i] = $row[1];
	    $labels[$i] = $statuts[$row[0]];
	    array_push($this->colors, $colors_def[$row[0]]);
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
