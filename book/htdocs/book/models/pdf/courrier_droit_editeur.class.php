<?PHP
/* Copyright (C) 2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2008 Patrick Raguin	   <patrick.raguin@auguria.net>
 * Copyright (C) 2010      Auguria SARL         <info@auguria.org>
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
 */

/**
 \file       	product/book/courrier-droit-editeur.class.php
 \ingroup    	editeurs
 \brief      	Classe de generation des courriers pour les editeurs
 \version		$Id: courrier_droit_editeur.class.php,v 1.2 2010/06/07 14:16:32 pit Exp $
 */

require_once(DOL_DOCUMENT_ROOT.'/includes/fpdf/fpdfi/fpdi_protection.php');

class pdf_courrier_droit_editeur
{

	var $error;


	/**
	 \brief      Constructeur
	 \param	    db		Handler accès base de donnée
	 */
	function pdf_courrier_droit_editeur ($db)
	{
		global $langs;
		
		$this->langs = $langs;

		$this->db = $db;

		// Dimension page pour format A4
		$this->type = 'pdf';
		$this->page_largeur = 210;
		$this->page_hauteur = 297;
		$this->format = array($this->page_largeur,$this->page_hauteur);
		$this->marge_gauche=15;
		$this->marge_droite=15;
		$this->marge_haute=10;
		$this->marge_basse=10;

		$this->name = utf8_decode($langs->transnoentities("BookPDFMailRightsName")).' '.strftime("%Y", time());
		$this->file = '1'.strftime("%Y", time()).'.pdf';
	}

	/**
	 \brief G�n�re le document
	 \return int 0 = ok, <> 0 = ko
	 */
	function Generate($product, $contract,$contract_bill)
	{
		global $conf,$langs;



		dolibarr_syslog("pdf_courrier_droit_editeur::Generate ", LOG_DEBUG );

		require_once(FPDF_PATH.'fpdf.php');

		$error = 0;

		 
		$sql = "SELECT p.ref";
		$sql .= " FROM ".MAIN_DB_PREFIX."product p";
		$sql .= " WHERE p.rowid = ".$product;

		$resql=$this->db->query($sql);

		if ($resql)
		{
			$obj = $this->db->fetch_object($resql);
			 
			
			$dao_contract_bill = new dao_book_contract_bill($this->db);
			$dao_contract_bill->fetch($contract_bill);
			
			//We place the mails in /documents/book/droitauteur/ :
			$dir = $conf->book->dir_output.'/droitauteur/'.$obj->ref;
			$file = $dir."/".$dao_contract_bill->getRef().".pdf";

			if (! file_exists($dir))
			{
	
				if (create_exdir($dir) < 0)
				{
					$this->error = utf8_decode($langs->transnoentities("ErrorCanNotCreateDir",$dir));
					return -1;
				}
			}

			if (file_exists($dir))
			{
				// Protection et encryption du pdf
				if ($conf->global->PDF_SECURITY_ENCRYPTION)
				{
					$pdf=new FPDI_Protection('P','mm',$this->format);
					$pdfrights = array('print'); // Ne permet que l'impression du document
					$pdfuserpass = ''; // Mot de passe pour l'utilisateur final
					$pdfownerpass = NULL; // Mot de passe du propriétire, crée aléatoirement si pas défini
					$pdf->SetProtection($pdfrights,$pdfuserpass,$pdfownerpass);
				}
				else
				{
					$pdf=new FPDI('P','mm',$this->format);
				}
					
				$pdf->Open();
				$pdf->AddPage();
				$pdf->SetDrawColor(128,128,128);

				$pdf->SetTitle($dao_contract_bill->getRef());
				$pdf->SetSubject(utf8_decode($langs->transnoentities("Invoice")));
				$pdf->SetCreator("Dolibarr ".DOL_VERSION);
				$pdf->SetAuthor(utf8_decode($langs->transnoentities("BookPDFMailRightsMe")));

				$this->head($pdf,$contract);
				$this->infos($pdf,$contract);
				$this->foot($pdf);

					
					
			}

			$pdf->Close();
			$pdf->Output($file);

			dolibarr_syslog("droits-editeurs.php write $fileall", LOG_DEBUG );

		}
		else
		{
			dolibarr_syslog("pdf_courrier_droit_editeur::Generate ".$db->error(), LOG_ERR );
			$this->error = "pdf_courrier_droit_editeur::Generate ".$db->error();
			return -1;
		}

		return 0;
	}


	function infos(&$pdf,$contract)
	{
		global $langs;


		//Information contrat/produit
		$sql = "SELECT p.rowid, p.label, p.price, p.price_ttc, s.reel, c.quantity, c.taux";
		$sql .= " FROM ".MAIN_DB_PREFIX."product_stock s";
		$sql .= " JOIN ".MAIN_DB_PREFIX."product_book_contract c ON s.fk_product = c.fk_product_droit";
		$sql .= " JOIN ".MAIN_DB_PREFIX."product p ON p.rowid = c.fk_product_book";
		$sql .= " WHERE c.rowid = ".$contract;

		$resql = $this->db->query($sql);
		$objContract = $this->db->fetch_object($resql);

		 

		$sql = "SELECT year(b.date_deb) as date_year, b.qtySold";
		$sql .= " FROM ".MAIN_DB_PREFIX."product_book_contract c";
		$sql .= " JOIN ".MAIN_DB_PREFIX."product_book_contract_bill b ON c.rowid = b.fk_contract";
		$sql .= " WHERE c.rowid = ".$contract;
		$sql .= " ORDER BY b.date_deb";


		$resql=$this->db->query($sql);

		$qtysell = 0;
		$solde = $objContract->quantity;
		
		
		if ($resql)
		{
			$i = 0;
			while ($obj = $this->db->fetch_object($resql) )
			{
				$i++;

				
				$pdf->SetFont('Arial','',10);
				$pdf->SetXY($this->marge_gauche,140 + ($i * 8));
				$pdf->MultiCell(46,5,utf8_decode($langs->transnoentities("BookPDFMailRightsSoldQuantityInYear")).' '.$obj->date_year." : ");
				
				$pdf->SetFont('Arial','',10);
				$pdf->SetXY($this->marge_gauche + 55, 140 + ($i * 8));
				$pdf->MultiCell(46,5,$obj->qtySold);				
				
				$solde -= $obj->qtySold;
				$last_year = $obj->date_year;
				
				if((140 + ($i * 8)) > ($this->marge_haute + 245)) $i = $i = $i+3; //if text is "touching" the footer, text is "pushed" to a new page
			}
			$this->db->free($resql);
		}
		else
		{
			$this->error = $this->db->error();
			return -1;
		}
		
		
		$pdf->SetXY($this->marge_gauche,100);
		$pdf->MultiCell(180,5,utf8_decode($langs->transnoentities("BookPDFMailRightsIntro")).' '.$last_year.'.');
		 
		$pdf->SetXY($this->marge_gauche,120);
		$pdf->MultiCell(25,5,utf8_decode($langs->transnoentities("BookPDFMailRightsTitle")));
		 
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY($this->marge_gauche + 25,120);
		$pdf->MultiCell(140,5,$objContract->label);

		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($this->marge_gauche,130);
		$pdf->MultiCell(46,5,utf8_decode($langs->transnoentities("BookPDFMailRightsSignedQuantityForContract")));
		 
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY($this->marge_gauche + 17,130);
		$pdf->MultiCell(46,5,$objContract->quantity,0,'R');
		
		
		$posy = 135;

		if((140 + ($i * 8)) > ($this->marge_haute + 245)) $i = $i+3; //if text is "touching" the footer, text is "pushed" to a new page
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY(100,140 + ($i * 8) );
		$pdf->MultiCell(15,5,utf8_decode($langs->transnoentities("BookPDFMailRightsBalance")),0);
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY(115,140 + ($i * 8) );
		$pdf->MultiCell(16,5,$solde,0,'R');

		$i++;
		if(($posy + ($i * 10)) > ($this->marge_haute + 245)) $i = $i+2; //if text is "touching" the footer, text is "pushed" to a new page
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($this->marge_gauche,$posy + ($i * 10) );
		$pdf->MultiCell(50,5,utf8_decode($langs->transnoentities("BookPDFMailRightsAuthorRightsRate")),0);
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY($this->marge_gauche + 15, $posy + ($i * 10) );
		$pdf->MultiCell(50,5, $objContract->taux." %",0,'R');

		$i++;
		if(($posy + ($i * 10)) > ($this->marge_haute + 245)) $i = $i+2; //if text is "touching" the footer, text is "pushed" to a new page
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($this->marge_gauche,$posy + ($i * 10) );
		$pdf->MultiCell(50,5,utf8_decode($langs->transnoentities("BookPDFMailRightsDutyFreePrice")),0);
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY($this->marge_gauche + 15,$posy + ($i * 10) );
		$pdf->MultiCell(50,5, price($objContract->price),0,'R');

		$i++;
		if(($posy + ($i * 10)) > ($this->marge_haute + 245)) $i = $i+2; //if text is "touching" the footer, text is "pushed" to a new page
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($this->marge_gauche,$posy + ($i * 10) );
		$pdf->MultiCell(50,5,utf8_decode($langs->transnoentities("BookPDFMailRightsAllTaxesIncludedPrice")),0);
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY($this->marge_gauche + 15,$posy + ($i * 10) );
		$pdf->MultiCell(50,5, price($objContract->price_ttc),0,'R');

		$i++;
		if((140 + ($i * 10)) > ($this->marge_haute + 245)) $i = $i+2; //if text is "touching" the footer, text is "pushed" to a new page
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($this->marge_gauche,140 + ($i * 10) );
		$pdf->MultiCell(100,5,utf8_decode($langs->transnoentities("BookPDFMailRightsRestToOweOnRightForYear")).' '.$last_year." : ".price(-$solde * $objContract->price_ttc),0);


		$i++;
		if((140 + ($i * 10)) > ($this->marge_haute + 245)) $i = $i+2; //if text is "touching" the footer, text is "pushed" to a new page
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($this->marge_gauche,140 + ($i * 10));
		$pdf->MultiCell(180,5,utf8_decode($langs->transnoentities("BookPDFMailRightsPoliteness")));

		 
	}


	function head(&$pdf,$contract)
	{

		global $langs,$conf,$mysoc;
		
		$logo=$conf->mycompany->dir_output.'/logos/'.$mysoc->logo;
		if (is_readable($logo) && is_file($logo))
		{
			$logoReadableFile = 1;
			$extLogo = strtolower(pathinfo($logo, PATHINFO_EXTENSION));
			if($extLogo=="jpg" || $extLogo=="jpeg" || $extLogo=="png" || $extLogo=="gif")
			{
				$logoCompatibleWithFPDF = 1;
				if($extLogo == "png")
				{
					//Color type of PNG is used to know if the image is using Alpha Channel :
					$logoColorType = ord(file_get_contents($logo, NULL, NULL, 25, 1));
					//Interlace Method byte is used to know if the image is using Interlacing method :
					$logoInterlace = ord(file_get_contents($logo, NULL, NULL, 28, 1));
					//For more infos about those 2 lines, visit http://stackoverflow.com/questions/2057923/how-to-check-a-png-for-grayscale-alpha-color-type or http://www.libpng.org/pub/png/spec/1.2/PNG-Chunks.html
					if(($logoColorType == 6) || ($logoColorType == 4) || ($logoInterlace == 1))
					{
						//In this case, the image is using Interlacing method or is having an Alpha Channel, so it's incompatible with FPDF
						$logoCompatibleWithFPDF = 0;
					}
				}
			}
		}
		
		if(($logoReadableFile==1) && ($logoCompatibleWithFPDF==1))
		{
			$pdf->Image($logo, $this->marge_gauche, $this->marge_haute, 0, (($this->marge_haute + 20) - ($this->marge_haute)));
		}
		else
      	{
			$pdf->SetTextColor(200,0,0);
			$pdf->SetXY($this->marge_gauche, $this->marge_haute);
			$pdf->SetFont('Arial','B',16);
			$pdf->MultiCell(100, 3, utf8_decode($mysoc->nom), 0, 'L');
//			$pdf->MultiCell(100, 3, utf8_decode($langs->transnoentities("ErrorLogoFileNotFound",$logo)), 0, 'L');
//			$pdf->MultiCell(100, 3, utf8_decode($langs->transnoentities("ErrorGoToGlobalSetup")), 0, 'L');
			$pdf->SetTextColor(0,0,0);
        }

		$pdf->Rect($this->marge_gauche, $this->marge_haute + 22, ($this->page_largeur - ($this->marge_gauche + $this->marge_droite)),0);


		//Information entreprise
		$info_entr_pox_x = $this->marge_gauche;
		$info_entr_pox_y = $this->marge_haute + 30;
		$pdf->SetFont('Arial','',10);
		
		$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y );
		$pdf->MultiCell(0,0,utf8_decode($langs->transnoentities("BookPDFMailRightsTel")).' '.$conf->global->MAIN_INFO_SOCIETE_TEL);

		$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y + 10 );
		$pdf->MultiCell(0,0,utf8_decode($langs->transnoentities("BookPDFMailRightsPostalAddress")));

		$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y + 15 );
		//$pdf->MultiCell(0,0,$conf->global->MAIN_INFO_SOCIETE_ADRESSE);
		$society_address = str_replace("\r","\n", str_replace("\r\n","\n",$conf->global->MAIN_INFO_SOCIETE_ADRESSE) );//each new line is delimited by a "\n" alone
		$society_address_tab = explode("\n", $society_address);
		$num_line = 0;
		foreach($society_address_tab as $society_address_line)
		{
			$pdf->MultiCell(0,0,utf8_decode($society_address_line));
			$num_line++;
			$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y + (15+($num_line*5)) );
		}

		//$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y + (20 + ((count($society_address_tab)-1)*5) ) );
		$pdf->MultiCell(0,0,utf8_decode($conf->global->MAIN_INFO_SOCIETE_CP." ".$conf->global->MAIN_INFO_SOCIETE_VILLE));

		//Information client

		 
		$sql = "SELECT s.nom, s.address, s.ville, s.cp, p.firstname, p.name";
		$sql.= " FROM ".MAIN_DB_PREFIX."societe s";
		$sql.= " JOIN ".MAIN_DB_PREFIX."product_book_contract c ON c.fk_soc = s.rowid";
		$sql.= " LEFT OUTER JOIN ".MAIN_DB_PREFIX."socpeople p ON p.fk_soc = s.rowid";
		$sql.= " WHERE c.rowid = ".$contract;
		
		$resql = $this->db->query($sql);

		$obj = $this->db->fetch_object($resql);

		$info_entr_pox_x = $this->marge_gauche + 95;
		$info_entr_pox_y = $this->marge_haute + 33;

		$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y );
		if($obj->firstname)
		{
			$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y );
		}
		else
		{
			$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y + 6);
		}
		$pdf->MultiCell(0,0,$obj->nom);

		$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y + 6 );
		$pdf->MultiCell(0,0,$obj->firstname." ".$obj->name);

		$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y + 12 );
		$pdf->MultiCell(0,0,$obj->address);

		$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y + 25 );
		$pdf->MultiCell(0,0,$obj->cp." ".$obj->ville);

		$pdf->SetXY ($info_entr_pox_x, $info_entr_pox_y + 40 );
		$pdf->MultiCell(0,0,utf8_decode($langs->transnoentities("BookPDFMailRightsLocationAndDate",$conf->global->MAIN_INFO_SOCIETE_VILLE,date(utf8_decode($langs->transnoentities("BookPDFMailRightsDateFormat"))) )));

	}


	function foot(&$pdf)
	{
		global $conf;

		$pdf->SetXY ($this->marge_gauche, $this->marge_haute + 250);
		$pdf->MultiCell(0,0,$conf->global->BOOK_CONTRACT_BILL_SLOGAN,0,"C"); 


		$pdf->Rect($this->marge_gauche, $this->marge_haute + 255, ($this->page_largeur - ($this->marge_gauche + $this->marge_droite)),0);

		$pdf->SetXY ($this->marge_gauche, $this->marge_haute + 260);
		$pdf->MultiCell(0,0,$conf->global->BOOK_CONTRACT_BILL_SOCIETE_INFOS,0,"C"); 

		$pdf->SetXY ($this->marge_gauche, $this->marge_haute + 265);
		$pdf->MultiCell(0,0,$conf->global->BOOK_CONTRACT_BILL_OTHER_INFOS,0,"C");


	}




}

?>
