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
 * $Id: barmoy.class.php,v 1.1 2009/10/20 16:19:25 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/stats/graph/barmoy.class.php,v $
 *
 */

require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/graph.class.php");

class GraphBarMoy extends DolibarrGraph {

  Function GraphBarMoy($DB, $file)
  {
    $this->db = $DB;
    $this->file = $file;
    $this->bgcolor = "#DEE7EC";
    $this->barcolor = "green";
    $this->client = 0;
    $this->showframe = true;
  }
  
  Function GraphDraw($file, $datas, $labels, $moys)
  {
    // Create the graph. These two calls are always required

    $height = 240;
    $width = 320;

    if ($this->width <> $width && $this->width > 0)
      $width = $this->width;

    if ($this->height <> $height && $this->height > 0)
      $height = $this->height;

    $graph = new Graph($width, $height,"auto");    
    $graph->SetScale("textlin");

    $graph->yaxis->scale->SetGrace(20);

    $graph->SetFrame($this->showframe);
    
    $graph->img->SetMargin(40,20,20,40);
    
    $b2plot = new BarPlot($datas);

    $b2plot->SetFillColor($this->barcolor);
    
    $graph->xaxis->scale->SetGrace(20);

    $LabelAngle = 45;
    if ($this->LabelAngle <> $LabelAngle && $this->LabelAngle > 0)
      $LabelAngle = $this->LabelAngle;

    $graph->xaxis->SetLabelAngle($LabelAngle);

    $graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,8);

    
    $b1plot = new BarPlot($moys);
    $b1plot->SetFillColor("gray");
    
    $gbplot = new GroupBarPlot(array($b2plot,$b1plot));

    $graph->Add($gbplot);
    
    $graph->title->Set($this->titre);
    
    $graph->title->SetFont(FF_VERDANA,FS_NORMAL);

    $graph->yaxis->title->SetFont(FF_FONT2);
    $graph->xaxis->title->SetFont(FF_FONT1);
    
    $graph->xaxis->SetTickLabels($labels);
    
    // Display the graph
    
    $graph->img->SetImgFormat("png");
    $graph->Stroke($file);
  }
}   
?>
