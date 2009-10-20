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
 * $Id: graph-statistiques-fournisseurs.php,v 1.1 2009/10/20 16:19:23 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/script/graph-statistiques-fournisseurs.php,v $
 *
 *
 * Generation des graphiques sur les founisseurs
 *
 *
 *
 */
require ("../../master.inc.php");

require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/bar.class.php");

$img_root = DOL_DATA_ROOT."/graph/telephonie/";

$Tfourn = array();
$sql = "SELECT rowid, nom";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_fournisseur";
$resql = $db->query($sql);

if ($resql)
{
  while ($row = $db->fetch_row($resql))
    {
      $Tfourn[$row[0]] = $row[1];
    }
}

$sql = "SELECT distinct date_format(date, '%m%Y')";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_communications_details";

$resql = $db->query($sql);

if ($resql)
{
  $Tdate = array();
  $Tlegends = array();
  while ($row = $db->fetch_row($resql))
    {
      array_push($Tdate, $row[0]);
      array_push($Tlegends, substr($row[0],0,2));
    }
}

$sql = "SELECT fk_fournisseur, date_format(date, '%m%Y'), duree, numero";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_communications_details";

$resql = $db->query($sql);

if ($resql)
{
  $Ttotal = array();
  $Tglobal = array();
  $Tinter = array();
  $Tnatio = array();
  $Tmobil = array();

  $num = $db->num_rows($resql);
  $i = 0;

  while ($i < $num)
    {
      $row = $db->fetch_row($resql);

      if (substr($row[3],0,2) == '00')
	{
	  $Tinter[$row[0]][$row[1]] += $row[2];
	}
      elseif (substr($row[3],0,2) == '06')
	{
	  $Tmobil[$row[0]][$row[1]] += $row[2];
	}
      else
	{
	  $Tnatio[$row[0]][$row[1]] += $row[2];
	}

      $Ttotal[$row[0]][$row[1]] += $row[2];
      $Tglobal[$row[1]] += $row[2];
      $i++;
    }
}
$titres = array();
$titres["inter"] = "internationales";
$titres["natio"] = "nationales";
$titres["mobil"] = "mobiles";

$graphs = array("inter","natio","mobil");
$colors = array("yellow","red","blue","pink","orange","green");
foreach($graphs as $graph)
{
  $datas = array();
  $globals = array();

  $tab = "T".$graph;
  foreach ($$tab as $key => $value)
    {
      $j = 0;
      foreach($Tdate as $date)
	{
	  $datas[$key][$j] = ($value[$date]/60000);
	  $j++;
	}
      
    }
  $file = $img_root . "communications/fourn_".$graph.".png";

  $ObjectGraph = new Graph(640,320,"auto");    
  $ObjectGraph->SetScale("textlin");
  $ObjectGraph->yaxis->scale->SetGrace(20);
  $ObjectGraph->xaxis->scale->SetGrace(20);  
  $ObjectGraph->img->SetMargin(40,20,20,40);
  $ObjectGraph->legend->Pos(0.10,0.12,"left","top");
  $i=0;
  $plots = array();
  foreach ($$tab as $key => $value)
    {
      $bplot = new BarPlot($datas[$key]);
      $bplot->SetFillColor($colors[$i]);
      $bplot->SetLegend($Tfourn[$key]);
      array_push($plots, $bplot);
      $i++;
    }
   
  $gbplot = new GroupBarPlot($plots);

  $ObjectGraph->Add($gbplot);
  
  $ObjectGraph->title->Set("Nombre de minutes ".$titres[$graph]." par fournisseurs (en milliers)");
  $ObjectGraph->xaxis->SetTickLabels($Tlegends);
  
  $ObjectGraph->img->SetImgFormat("png");
  $ObjectGraph->Stroke($file);
}

/*
 * Sum
 */
$j = 0;
foreach($Tdate as $date)
{
  $globals[$j] = ($Tglobal[$date]/60000);
  $j++;
}

$file = $img_root . "communications/duree.png";

$ObjectGraph = new Graph(640,200,"auto");
$ObjectGraph->SetScale("textlin");
$ObjectGraph->yaxis->scale->SetGrace(20);
$ObjectGraph->xaxis->scale->SetGrace(20);  
$ObjectGraph->img->SetMargin(40,20,20,40);

$i=0;
$plots = array();

$bplot = new BarPlot($globals);
$bplot->SetFillColor("green");

array_push($plots, $bplot);

$gbplot = new GroupBarPlot($plots);

$ObjectGraph->Add($gbplot);

$ObjectGraph->title->Set("Nombre de minutes (en milliers)");
$ObjectGraph->xaxis->SetTickLabels($Tlegends);

$ObjectGraph->img->SetImgFormat("png");
$ObjectGraph->Stroke($file);

?>
