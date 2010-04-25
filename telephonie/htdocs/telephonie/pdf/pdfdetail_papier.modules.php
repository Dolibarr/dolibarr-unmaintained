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
 * or see http://www.gnu.org/
 *
 * $Id: pdfdetail_papier.modules.php,v 1.3 2010/04/25 14:46:44 grandoc Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/pdf/pdfdetail_papier.modules.php,v $
 *
 * G�n�re un PDF de la premi�re page de r�sum� et un tableur des communications
 */
require_once DOL_DOCUMENT_ROOT."/compta/facture/class/facture.class.php";
require_once DOL_DOCUMENT_ROOT."/telephonie/facturetel.class.php";
require_once DOL_DOCUMENT_ROOT."/telephonie/pdf/pdfdetail_standard.modeles.php";
require_once DOL_DOCUMENT_ROOT."/telephonie/pdf/xlsdetail_nodet.modules.php";

class pdfdetail_papier {

  function pdfdetail_papier ($db=0, $ligne, $year, $month, $factel)
    { 
      $this->db = $db;
      $this->description = "Mod�le de facture d�taill�e sans les communications";
      $this->ligne = $ligne;
      $this->year  = $year;
      $this->month = $month;
      $this->factel = $factel;
      $this->pages = 0;
    }

  /*
   *
   *
   */
  function write_pdf_file($factel, $ligne)
  {
    $xpdf = 0;
    $this->_write_pdf_file($factel, $ligne, $xpdf, 0);
  }

  function _write_pdf_file($factel, $ligne, &$pdf, $output)
    {
      $fac = new Facture($this->db,"",$factel->fk_facture);
      $fac->fetch($factel->fk_facture);  
      $fac->fetch_client();

      $objlignetel = new LigneTel($this->db);

      $result = $objlignetel->fetch($ligne);

      if (defined("FAC_OUTPUTDIR"))
	{
	  $dir  = FAC_OUTPUTDIR . "/" . $fac->ref . "/" ;
	  $file = $dir . $fac->ref . "-$ligne-detail.pdf";

	  if (strlen($objlignetel->code_analytique) > 0)
	    {
	      $file = $dir . $fac->ref . "-$ligne-$objlignetel->code_analytique-detail.pdf";
	    }
	  
	  if (! file_exists($dir))
	    {
	      umask(0);
	      if (! mkdir($dir, 0755))
		{
		  $this->error="Erreur: Le r�pertoire '$dir' n'existe pas et Dolibarr n'a pu le cr�er.";
		  return 0;
		}
	    }

	  if (file_exists($dir) OR $output)
	    {
	      if ($output == 0)
		{
		  $pdf = new pdfdetail_standard_modeles('P','mm','A4');
		}

	      $pdf->fac = $fac;
	      $pdf->factel = $factel;

	      $pdf->client_nom     = $fac->client->nom; 
	      $pdf->client_adresse = $fac->client->adresse;
	      $pdf->client_cp      = $fac->client->cp;
	      $pdf->client_ville   = $fac->client->ville;

	      $pdf->ligne = $ligne;
	      $pdf->year  = $this->year;
	      $pdf->month = $this->month;

	      $pdf->ligne_ville = '';
	      if ($objlignetel->code_analytique)
		{
		  $soca = new Societe($this->db);
		  $soca->fetch($objlignetel->client_id);

		  $pdf->ligne = $ligne . " (".$objlignetel->code_analytique.")";
		  $pdf->ligne_ville = $soca->ville;
		}

              $pdf->SetAutoPageBreak(0, 24);
	      $pdf->SetLineWidth(0.1);
	      $pdf->tab_top = 20;
	      $pdf->tab_height = 222;
	      $pdf->FirstPage = 1;
	      /*
	       * Libelle
	       */
	      
	      $pdf->libelle = "Du ".strftime("%d/%m/%Y",$factel->get_comm_min_date($this->year.$this->month));
	      $pdf->libelle .= " au ".strftime("%d/%m/%Y",$factel->get_comm_max_date($this->year.$this->month));


	      /*
	       * Liste des appels
	       *
	       */
	      $sql = "SELECT t.ligne, ".$this->db->pdate("t.date")." as pdate";
	      $sql .= " , t.numero, t.dest, t.duree, t.cout_vente";
	      $sql .= " FROM ".MAIN_DB_PREFIX."telephonie_communications_details as t ";
	      $sql .= " WHERE fk_telephonie_facture =".$factel->id;
	      $sql .= " ORDER BY t.date ASC";
	
	      $resql = $this->db->query($sql) ;

	      if ( $resql )
		{
		  $num = $this->db->num_rows($resql);

		  $pdf->AddPage();
		  $this->pages++;
		  $this->ListHeader($pdf);
		  $i = 0;
		  $var = 1;
		  $line_height = 2;
		  $this->colonne = 1;

		  while ($i < $num)
		    {
		      $obj = $this->db->fetch_object($resql);
		      $Y = $pdf->GetY();
		
		      if ($this->inc > 132 && $this->colonne == 1)
			{
			  $col = 50;
			  $Y = $pdf->tab_top + 6;
			  //$this->inc = 0;
			  $this->colonne = 2;
			  $old_dest='';
			  $old_date='';
			}

		      if ($this->inc > 265 && $this->colonne == 2)
			{
			  $col = 100;
			  $Y = $pdf->tab_top + 6;
			  //$this->inc = 0;
			  $this->colonne = 3;
			  $old_dest='';
			  $old_date='';
			}

		      if ($this->inc > 398 && $this->colonne == 3)
			{
			  $col = 150;
			  $Y = $pdf->tab_top + 6;
			  //$this->inc = 0;
			  $this->colonne = 4;
			  $old_dest='';
			  $old_date='';
			}

		      if ($this->inc > 531 && $this->colonne == 4)
			{
			  $pdf->AddPage();
			  $this->pages++;
			  $this->ListHeader($pdf);
			  $var = 0;
			  $col = 0;
			  $Y = $pdf->tab_top + 6;
			  $this->inc = 0;
			  $old_dest='';
			  $old_date='';
			  $this->colonne = 1;
			}

		      $var=!$var;

		      $pdf->SetFont('Arial','', 6);
		      $pdf->SetXY (5 + $col, $Y);

		      if ($old_date == strftime("%d/%m/%Y", $obj->pdate))
			{
			  $date = "";
			}
		      else
			{
			  $old_date = strftime("%d/%m/%Y", $obj->pdate) ;
			  $date = strftime("%d",$obj->pdate);
			}

		      $pdf->MultiCell(5, $line_height, $date, 0,'L',$var);

		      if ($Y > $pdf->GetY())
			$Y = $pdf->GetY() - $line_height;

		      $pdf->SetXY (9 + $col, $Y);
		      $heure = strftime("%H%M%S",$obj->pdate);
		      $pdf->MultiCell(10, $line_height, $heure, 0,'L',$var);

		      $pdf->SetXY (18 + $col, $Y);
		      $numero = ereg_replace("^00","",$obj->numero);
		      $numero = ereg_replace("^0","",$obj->numero);
		      $pdf->MultiCell(15, $line_height, $numero, 0,'L',$var);

		      $pdf->SetXY (32 + $col, $Y);

		      if ($obj->dest == $old_dest)
			{
			  $dest = ' "';
			}
		      else
			{
			  $old_dest = $obj->dest ;
			  $dest = $obj->dest;
			}

		      $xs = explode(" ",$dest);
		      if (sizeof($xs) == 1)
			$small_dest = substr($xs[0],0,4);

		      if (sizeof($xs) == 2)
			$small_dest = substr($xs[0],0,2).substr($xs[1],0,2);

		      if (sizeof($xs) == 3)
			$small_dest = substr($xs[0],0,2).substr($xs[1],0,1).substr($xs[2],0,1);

		      if (sizeof($xs) == 4)
			$small_dest = substr($xs[0],0,1).substr($xs[1],0,1).substr($xs[2],0,1).substr($xs[3],0,1);

		      if ($dest == ' "')
			$small_dest == ' "';


		      $pdf->MultiCell(9, $line_height, $small_dest, 0, 'L',$var);

		      $pdf->SetXY (39 + $col, $Y);
		      $pdf->MultiCell(8, $line_height, $obj->duree, 0, 'R',$var);

		      $pdf->SetXY (46 + $col, $Y);
		      $pdf->MultiCell(9, $line_height, sprintf("%01.3f", $obj->cout_vente), 0,'R',$var);

		      $i++;
		      $this->inc++;
		    }
		}
	      else
		{
		  dol_syslog("Erreur lecture des communications");
		}

	      /*
	       *
	       */


	      return 0;
	    }
	  else
	    {
	      $this->error="Erreur: Le r�pertoire '$dir' n'existe pas et Dolibarr n'a pu le cr�er.";
	      return -1;
	    }
	}
      else
	{
            $this->error="Erreur: FAC_OUTPUTDIR non d�fini !";
            return -2;
	}
    }

  /*
   * Header
   */

  function Header(&$pdf, $output)
  {
    $pdf->SetXY(10,5);
    $pdf->SetTextColor(0,90,200);
    $pdf->SetFont('Arial','',10);
    $pdf->SetXY(11,31);
    $pdf->MultiCell(89, 4, "Facture d�taill�e : ".$pdf->fac->ref);

    $pdf->SetX(11);
    $pdf->MultiCell(89, 4, "Ligne : " . $pdf->ligne);

    $pdf->SetX(11);
    $pdf->MultiCell(89, 4, $pdf->libelle, 0);

    $pdf->SetX(11);
    if ($output == 0)
      {
	$pdf->MultiCell(80, 4, "Page : ". $pdf->PageNo() ."/{nb}", 0);
      }

    // Clients sp�ciaux

    if ($pdf->ligne_ville)
      {
	$pdf->SetX(11);
	$pdf->MultiCell(80, 4, "Agence : ". $pdf->ligne_ville, 0);
      }

    $pdf->rect(5, 30, 100, 23);
    
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('Arial','',10);

    $pdf->SetXY(107, 31);

    $pdf->MultiCell(66,4, $pdf->client_nom);

    $pdf->SetX(107);
    $pdf->MultiCell(86,4, $pdf->client_adresse . "\n" . $pdf->client_cp . " " . $pdf->client_ville);

    $pdf->rect(105, 30, 100, 23);

    /*
     * On positionne le curseur pour la liste
     */        
    $pdf->SetXY(10,$pdf->tab_top + 6);
    $pdf->colonne = 1;
    $pdf->inc = 0;
  }

  /* 
   * Footer
   */

  function Footer(&$pdf)
  {

    if ($pdf->FirstPage == 1)
      {
	$pdf->FirstPage = 0;
      }
    else
      {

	$pdf->SetFont('Arial','',8);
    
	$pdf->Text(11, $pdf->tab_top + 3,'Date');
	$pdf->Text(106, $pdf->tab_top + 3,'Date');
	
	$w = 33;
	
	$pdf->Text($w+1, $pdf->tab_top + 3,'Num�ro');
	$pdf->Text($w+96, $pdf->tab_top + 3,'Num�ro');
	
	$w = 47;
	
	$pdf->Text($w+1, $pdf->tab_top + 3,'Destination');
	$pdf->Text($w+96, $pdf->tab_top + 3,'Destination');
	
	$w = 86;
	
	$pdf->Text($w+1, $pdf->tab_top + 3,'Dur�e');
	$pdf->Text($w+96, $pdf->tab_top + 3,'Dur�e');
	
	$w = 98;
	
	$pdf->Text($w+1, $pdf->tab_top + 3,'HT');
	$pdf->Text($w+96, $pdf->tab_top + 3,'HT');
	
	$pdf->line(10, $pdf->tab_top + 4, 200, $pdf->tab_top + 4 );

	/* Ligne M�diane */

	$pdf->line(105, $pdf->tab_top, 105, $pdf->tab_top + $pdf->tab_height);
	
      }

    $pdf->Rect(10, $pdf->tab_top, 190, $pdf->tab_height);
  }

  function ListHeader(&$pdf)
  {
    $pdf->SetXY(10,5);
    //$pdf->SetTextColor(0,90,200);
    $pdf->SetFont('Arial','',10);
    $pdf->SetXY(6,5);
    $pdf->MultiCell(89, 4, "Facture d�taill�e : ".$pdf->fac->ref);

    $pdf->SetX(6);
    $pdf->MultiCell(89, 4, "Ligne : " . $pdf->ligne);
    $pdf->SetX(6);

    $libelle = "Du ".strftime("%d/%m/%Y",$pdf->factel->get_comm_min_date($this->year.$this->month));
    $libelle .= " au ".strftime("%d/%m/%Y",$pdf->factel->get_comm_max_date($this->year.$this->month));
    $pdf->MultiCell(89, 4, $libelle, 0);

    //$pdf->SetX(6);
    //$pdf->MultiCell(80, 4, "Page : ". $pdf->PageNo() ."/{nb}", 0);
    // Clients sp�ciaux

    if ($pdf->ligne_ville)
      {
	$pdf->SetX(6);
	$pdf->MultiCell(80, 4, "Agence : ". $pdf->ligne_ville, 0);
      }

    $pdf->rect(5, 4, 100, 19);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('Arial','',10);

    $pdf->SetXY(107, 5);

    $pdf->MultiCell(66,4, $pdf->client_nom);

    $pdf->SetX(107);
    $pdf->MultiCell(86,4, $pdf->client_adresse . "\n" . $pdf->client_cp . " " . $pdf->client_ville);

    $pdf->rect(105, 4, 100, 19);

    /*
     * On positionne le curseur pour la liste
     */        
    $pdf->SetXY(10,$pdf->tab_top + 6);
    $pdf->colonne = 1;
    $pdf->inc = 0;
  }

}
?>
