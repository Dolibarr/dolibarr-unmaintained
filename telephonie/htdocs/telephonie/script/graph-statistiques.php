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
 * $Id: graph-statistiques.php,v 1.1 2009/10/20 16:19:23 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/script/graph-statistiques.php,v $
 *
 *
 * Generation des graphiques
 *
 *
 *
 */
require ("../../master.inc.php");

$verbose = 0;

for ($i = 1 ; $i < sizeof($argv) ; $i++)
{
  if ($argv[$i] == "-v")
    {
      $verbose = 1;
    }
  if ($argv[$i] == "--verbose")
    {
      $verbose = 1;
    }
}

require_once (DOL_DOCUMENT_ROOT."/telephonie/lignetel.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/facturetel.class.php");

require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/bar.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/camenbert.class.php");

require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/ca.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/camoyen.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/gain.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/heureappel.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/joursemaine.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/appelsdureemoyenne.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/graph/comm.nbmensuel.class.php");
require_once (DOL_DOCUMENT_ROOT."/telephonie/stats/lignes/actives.class.php");

$error = 0;

$datetime = time();

$date = strftime("%d%h%Y%Hh%Mm%S",$datetime);

$month = strftime("%m", $datetime);
$year = strftime("%Y", $datetime);

if ($month == 1)
{
  $month = "12";
  $year = $year - 1;
}
else
{
  $month = substr("00".($month - 1), -2) ;
}


$img_root = DOL_DATA_ROOT."/graph/telephonie/";


/***********************************************************************/
/*
/* Lignes actives
/*
/***********************************************************************/

$file = $img_root . "lignes/lignes.actives.png";
if ($verbose) print "Lignes actives ($file)\n";
$graph = new GraphLignesActives($db, $file);
$graph->GraphMakeGraph();

/***********************************************************************/
/*
/* Chiffre d'affaire mensuel
/*
/***********************************************************************/

$file = $img_root . "ca/ca.mensuel.png";
if ($verbose) print "Chiffre d'affaire mensuel ($file)\n";
$graphca = new GraphCa($db, $file);
$graphca->GraphDraw();

/************************************************************************/
/*
/* Chiffre d'affaire moyen
/*
/*
/************************************************************************/

$file = $img_root . "ca/gain_moyen_par_client.png";
if ($verbose) print "Graph ca moyen\n";
$graphgain = new GraphCaMoyen ($db, $file);
$graphgain->show_console = 0 ;
$graphgain->GraphDraw();

/*************************************************************************/
/*
/* Stats sur les communications
/*
/*
/*************************************************************************/

$file = $img_root . "communications/heure_appel_nb.png";
if ($verbose) print "Heures d'appels\n";
$graphha = new GraphHeureAppel ($db, $file);
$graphha->GraphDraw();

$file = $img_root . "communications/joursemaine_nb.png";
if ($verbose) print "Jours de semaines\n";
$graphha = new GraphJourSemaine ($db, $file);
$graphha->GraphDraw();

repart_comm($db);

$year = strftime("%Y", $datetime);
$month = strftime("%m", $datetime);

for ($i = 1 ; $i < 4 ; $i++)
{
  $month = $month - 1;

  if ($month == 0)
    {
      $year = $year - 1;
      $month = 12;
    }

  repart($db,$year, $month);
  repart_comm($db,$year, $month);

}


function repart_comm($db, $year = 0, $month = 0)
{
  if ($verbose) print "R�partition des communications\n";

  $sql = "SELECT duree, numero";
  $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_communications_details";

  if ($year && $month)
    {
      if ($verbose) print "R�partition des communications pour $month/$year\n";
      $month = substr("00".$month, -2);
      $sql .= " WHERE date_format(date,'%Y%m') = '$year$month'";
    }
  
  if ($db->query($sql))
    {
      $labels_duree = array();
      $repart_duree = array(0,0,0,0,0,0);
      $repart_dureelong = array(0,0);

      $labels_dest= array();
      $repart_dest = array(0,0,0);
      $repart_dest_temps = array(0,0,0);

      $num = $db->num_rows();
      
      $i = 0;
      
      while ($i < $num)
	{
	  $row = $db->fetch_row();	
	  
	  if ($row[0] < 10)
	    {
	      $repart_duree[0]++;
	    }
	  elseif ($row[0] >= 10 && $row[0] < 30)
	    {
	      $repart_duree[1]++;
	    }
	  elseif ($row[0] >= 30 && $row[0] < 60)
	    {
	      $repart_duree[2]++;
	    }
	  elseif ($row[0] >= 60 && $row[0] < 120)
	    {
	      $repart_duree[3]++;
	    }
	  elseif ($row[0] >= 120 && $row[0] < 300)
	    {
	      $repart_duree[4]++;
	    }
	  else
	    {
	      $repart_duree[5]++;
	    }
	  
	  if ($row[0] < 600)
	    {
	      $repart_dureelong[0]++;
	    }
	  else
	    {
	      $repart_dureelong[1]++;
	    }

	  if (substr($row[1],0,2) == '00')
	    {
	      $repart_dest[0]++;
	      $repart_dest_temps[0] += $row[0];
	    }
	  elseif (substr($row[1],0,2) == '06')
	    {
	      $repart_dest[1]++;
	      $repart_dest_temps[1] += $row[0];
	    }
	  else
	    {
	      $repart_dest[2]++;
	      $repart_dest_temps[2] += $row[0];
	    }
	  $i++;
	}
    }
  else
    {
      print $sql ;
    }

  if ($num > 0)
    {  
      $labels_duree[0] = "< 10 sec";
      $labels_duree[1] = "10-30 sec";
      $labels_duree[2] = "30-60 sec";
      $labels_duree[3] = "60-120 sec";
      $labels_duree[4] = "120-300 sec";
      $labels_duree[5] = "> 300 sec";
      
      $labels_dureelong[0] = "< 600 sec";
      $labels_dureelong[1] = "> 600 sec";

      $labels_dest[0] = 'International';
      $labels_dest[1] = 'Mobile';
      $labels_dest[2] = 'Local/National';


      $filem  = DOL_DATA_ROOT."/graph/telephonie/communications/duree_repart.png";
      $filec  = DOL_DATA_ROOT."/graph/telephonie/communications/dureelong_repart.png";
      $filed  = DOL_DATA_ROOT."/graph/telephonie/communications/dest_repart.png";
      $filedt = DOL_DATA_ROOT."/graph/telephonie/communications/dest_temps_repart.png";
      
      if ($year && $month)
	{
	  $filem = DOL_DATA_ROOT."/graph/telephonie/communications/duree_repart-$year$month.png";
	  $filec = DOL_DATA_ROOT."/graph/telephonie/communications/dureelong_repart-$year$month.png";
	  $filed = DOL_DATA_ROOT."/graph/telephonie/communications/dest_repart-$year$month.png";
	  $filedt = DOL_DATA_ROOT."/graph/telephonie/communications/dest_temps_repart-$year$month.png";
	}
      
      $graphm  = new GraphCamenbert ($db, $filem);
      $graphc  = new GraphCamenbert ($db, $filec);
      $graphd  = new GraphCamenbert ($db, $filed);
      $graphdt = new GraphCamenbert ($db, $filedt);
      
      $graphm->titre = "R�partition du nombre de communications par duree";
      $graphc->titre = "R�partition du nombre de communications par duree";
      $graphd->titre = "R�partition du nombre de communications par destination";
      $graphdt->titre = "R�partition du nombre de communications par destination";
      
      if ($year && $month)
	{
	  $graphm->titre = "R�part. du nbre de communications par duree $month/$year";
	  $graphc->titre = "R�part. du nbre de communications par duree $month/$year";
	  $graphd->titre = "R�part. du nbre de communications par destination $month/$year";
	  $graphdt->titre = "R�part. du temps de communications par destination $month/$year";
	}

      $graphm->colors= array('#993333','#66cc99','#6633ff','#33ff33','#336699','#00ffff');     
      $graphd->colors= array('#FFC0FF','#FF00FF','#C000C0');
      $graphdt->colors= array('#FFFFC0','#FFFF0F','#C0C000');

      $graphm->GraphDraw($repart_duree, $labels_duree);  
      $graphc->GraphDraw($repart_dureelong, $labels_dureelong);  
      $graphd->GraphDraw($repart_dest, $labels_dest);  
      $graphdt->GraphDraw($repart_dest_temps, $labels_dest);  
    }
}



/**********************************************************************/
/*
/* Stats sur les factures
/*
/*
/**********************************************************************/

$sql = "SELECT date_format(date,'%Y%m'), sum(cout_vente), sum(cout_achat), sum(gain), count(cout_vente)";
$sql .= " FROM ".MAIN_DB_PREFIX."telephonie_facture";
$sql .= " GROUP BY date_format(date,'%Y%m') ASC ";

if ($db->query($sql))
{
  $cout_vente = array();
  $cout_vente_moyen = array();
  $nb_factures = array();
  $jour_semaine_nb = array();
  $jour_semaine_duree = array();
  $gain = array();
  $gain_moyen = array();

  $num = $db->num_rows();
  if ($verbose) print "$num lignes de comm a traiter\n";
  $i = 0;

  while ($i < $num)
    {
      $row = $db->fetch_row();	

      $cout_vente[$i] = $row[1];

      $gain[$i] = $row[3];
      $gain_moyen[$i] = ($row[3]/$row[4]);
      $cout_vente_moyen[$i] = ($row[1]/$row[4]);
      $nb_factures[$i] = $row[4];
      $labels[$i] = substr($row[0],4,2) . '/'.substr($row[0],2,2);
      $i++;
    }
}


$file = $img_root . "/factures/facture_moyenne.png";
$graph = new GraphBar ($db, $file, $labels);
$graph->titre = "Facture moyenne";
if ($verbose) print $graph->titre."\n";
$graph->barcolor = "blue";
$graph->GraphDraw($file, $cout_vente_moyen, $labels);

$file = $img_root . "/factures/gain_mensuel.png";
$graph = new GraphBar ($db, $file);
$graph->titre = "Gain par mois en euros HT";
if ($verbose) print $graph->titre."\n";
$graph->GraphDraw($file, $gain, $labels);

$file = $img_root . "/factures/gain_moyen.png";
$graph = new GraphBar ($db, $file);
$graph->titre = "Gain moyen par facture par mois";
if ($verbose) print $graph->titre."\n";
$graph->barcolor = "blue";
$graph->GraphDraw($file, $gain_moyen, $labels);

$file = $img_root . "/factures/nb_facture.png";
$graph = new GraphBar ($db, $file);
$graph->titre = "Nb de facture mois";
if ($verbose) print $graph->titre."\n";
$graph->barcolor = "yellow";
$graph->GraphDraw($file, $nb_factures, $labels);

/*
 * R�partition des factures
 *
 *
 */
repart($db);

function repart($db, $year = 0, $month = 0)
{
  if ($verbose) print "R�partition des factures\n";

  $sql = "SELECT cout_vente, gain";
  $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_facture";

  if ($year && $month)
    {
      if ($verbose) print "R�partition des factures pour $month/$year\n";
      $month = substr("00".$month, -2);
      $sql .= " WHERE date_format(date,'%Y%m') = '$year$month'";
    }
  
  if ($db->query($sql))
    {
      $labels = array();
      $repart_montant = array();
      $num = $db->num_rows();
      
      $i = 0;
      
      while ($i < $num)
	{
	  $row = $db->fetch_row();	
	  
	  if ($row[0] < 10)
	    {
	      $repart_montant[0]++;
	    }
	  elseif ($row[0] >= 10 && $row[0] < 20)
	    {
	      $repart_montant[1]++;
	    }
	  elseif ($row[0] >= 20 && $row[0] < 40)
	    {
	      $repart_montant[2]++;
	    }
	  elseif ($row[0] >= 40 && $row[0] < 70)
	    {
	      $repart_montant[3]++;
	    }
	  elseif ($row[0] >= 70 && $row[0] < 100)
	    {
	      $repart_montant[4]++;
	    }
	  else
	    {
	      $repart_montant[5]++;
	    }
	  
	  
	  if ($row[1] < 1)
	    {
	      $repart_gain[0]++;
	    }
	  elseif ($row[1] >= 1 && $row[1] < 5)
	    {
	      $repart_gain[1]++;
	    }
	  elseif ($row[1] >= 5 && $row[1] < 10)
	    {
	      $repart_gain[2]++;
	    }
	  elseif ($row[1] >= 10 && $row[1] < 20)
	    {
	      $repart_gain[3]++;
	    }
	  elseif ($row[1] >= 20 && $row[1] < 50)
	    {
	      $repart_gain[4]++;
	    }
	  else
	    {
	      $repart_gain[5]++;
	    }
	  $i++;
	}
    }
  else
    {
      print $sql ;
    }

  if ($num > 0)
    {  
      $labels_montant[0] = "< 10";
      $labels_montant[1] = "10-20";
      $labels_montant[2] = "20-40";
      $labels_montant[3] = "40-70";
      $labels_montant[4] = "70-100";
      $labels_montant[5] = "> 100";
      
      $labels_gain[0] = "< 1";
      $labels_gain[1] = "1-5";
      $labels_gain[2] = "5-10";
      $labels_gain[3] = "10-20";
      $labels_gain[4] = "20-50";
      $labels_gain[5] = "> 50";
      
      $filem = DOL_DATA_ROOT."/graph/telephonie/factures/montant_repart.png";
      $fileg = DOL_DATA_ROOT."/graph/telephonie/factures/gain_repart.png";
      
      if ($year && $month)
	{
	  $filem = DOL_DATA_ROOT."/graph/telephonie/factures/montant_repart-$year$month.png";
	  $fileg = DOL_DATA_ROOT."/graph/telephonie/factures/gain_repart-$year$month.png";
	}
      
      $graphm = new GraphCamenbert ($db, $filem);
      $graphg = new GraphCamenbert ($db, $fileg);
      
      
      $graphm->titre = "R�partition du nombre de factures par montant";
      $graphg->titre = "R�partition du nombre de factures par gain";
      
      if ($year && $month)
	{
	  $graphm->titre = "R�part. du nbre de factures par montant $month $year";
	  $graphg->titre = "R�part. du nbre de factures par gain $month $year";
	}

      $graphm->colors= array('#993333','#66cc99','#6633ff','#33ff33','#336699','#00ffff');
      
      //      $graphm->GraphDraw($filem, $repart_montant, $labels_montant);  
      //      $graphg->GraphDraw($fileg, $repart_gain, $labels_gain);
    }
}
?>
