<?php
/* Copyright (C) 2007 Patrick Raguin  <patrick.raguin@gmail.com>
 * Copyright (C) 2010 Regis Houssin   <regis@dolibarr.fr>
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
 *     \file       htdocs/droitpret/droitpret.class.php
 *     \ingroup    droitpret
 *     \brief      Fichier de la classe ddes droits de prets
 *     \version    $Id: droitpret.class.php,v 1.7 2010/10/06 16:29:39 cdelambert Exp $
 */


/**
 *    \class 		DroitPret
 *    \brief 		Classe permettant la gestion des droits de prets
 */

class DroitPret
{


	var $index;
	var $dated;
	var $datef;
	var $dateEnvoie;
	var $format;
	var $refFile;
	var $fp;
	var $nbfact;

    function DroitPret($DB,$dated,$datef)
    {
       	global $conf;

        $this->db=$DB;
        $this->index = 0;
        $this->dated = $dated;
        $this->datef = $datef;
        $this->dateEnvoie = getdate();
        $this->format = "aa";
        $this->refFile = $conf->global->MAIN_INFO_SOCIETE_GENCOD."_".date("dmY",mktime($this->dateEnvoie['hours'],$this->dateEnvoie['minutes'],$this->dateEnvoie['seconds'],$this->dateEnvoie['mon'],$this->dateEnvoie['mday'],$this->dateEnvoie['year'])).".csv";


    }

    function createNewRapport()
    {
    	global $conf;

    	$this->nbfact = 0;

    	$dateenvoi = date("Y-m-d H:i:s",mktime($this->dateEnvoie['hours'],$this->dateEnvoie['minutes'],$this->dateEnvoie['seconds'],$this->dateEnvoie['mon'],$this->dateEnvoie['mday'],$this->dateEnvoie['year']));
		$sql =	"DELETE FROM ".MAIN_DB_PREFIX."droitpret_rapport WHERE fichier like '%".$this->refFile."%'";
		$this->db->query($sql);	
    	$sql = "INSERT INTO ".MAIN_DB_PREFIX."droitpret_rapport(date_envoie,format,date_debut,date_fin,fichier,nbfact) VALUES('".$dateenvoi."','".$this->format."','".date("Y-m-d H:i:s",$this->dated)."','".date("Y-m-d H:i:s",$this->datef)."','".$this->refFile."',0)";
    	$this->db->query($sql);
    	if (!is_dir($conf->droitpret->dir_output)){
    		mkdir($conf->droitpret->dir_output);
    	} 
    	if (!is_dir($conf->droitpret->dir_temp)){
    		mkdir($conf->droitpret->dir_temp);
    	}
    	
    	
		$lien = $conf->droitpret->dir_temp."/".$this->refFile;

		$ref = $this->db->last_insert_id(MAIN_DB_PREFIX."droitpret_rapport");
		
		$this->fp = fopen($lien,"w");
		$this->writeDEB($ref);
		$this->writeTET();
		$this->writeFin();
		fclose($this->fp);
		
		$sql = "UPDATE ".MAIN_DB_PREFIX."droitpret_rapport SET nbfact = ".$this->nbfact." WHERE rowid = ".$ref;
		$this->db->query($sql);
    }

    function writeDEB($ref)
    {

		$dateEnvoie = date("Ymd",mktime($this->dateEnvoie['hours'],$this->dateEnvoie['minutes'],$this->dateEnvoie['seconds'],$this->dateEnvoie['mon'],$this->dateEnvoie['mday'],$this->dateEnvoie['year']));
		$ligne = "DEB".$this->complChar($ref,"0",8).$dateEnvoie;
		$ligne .= $this->complChar($this->format," ",10);
		fwrite($this->fp,$ligne."\n");

    }

    function writeTET()
    {
    		global $conf;


    		$sql = "SELECT f.rowid, f.facnumber, f.datec, f.total_ttc, f.total ";
    		$sql.= "FROM llx_facture AS f, llx_facturedet AS d, llx_product AS p, llx_societe AS s, llx_categorie_societe AS c ";
    		$sql.= "WHERE f.fk_soc = s.rowid ";
    		$sql.= "AND c.fk_societe = s.rowid ";
    		$sql.= "AND d.fk_product = p.rowid ";
      		$sql.= "AND f.rowid = d.fk_facture ";
      		$sql.= "AND f.datec >= '".date("Y-m-d H:i:s",$this->dated)."' ";
      		$sql.= "AND f.datec < '".date("Y-m-d H:i:s",$this->datef)."' ";
    		$sql.= "AND c.fk_categorie = ".$conf->global->DROITPRET_CAT." ";
			$sql.= "GROUP BY f.rowid";

    		$result = $this->db->query($sql);

			if ($result)
			{
				$num = $this->db->num_rows($result);
		    	$i = 0;

			    while ($i < $num)
	    		{
	        		$obj = $this->db->fetch_object($result);
	        		$ligne = "TET380".$this->complChar($obj->facnumber,"0",25);
	        		$ligne.= $this->formatDate($obj->datec);
	        		$ligne.= $this->complChar("","",25).$this->complChar(str_replace(".","",$obj->total_ttc),"0",10);
	        		$ligne.= $this->complChar(str_replace(".","",$obj->total),"0",10)."EUR";
	        		fwrite($this->fp,$ligne."\n");

	        		$this->writeInt($obj->rowid);
	        		$this->writeLin($obj->rowid);

	        		$this->nbfact++;
	        		$i++;
	    		}
			}

    }

    function writeINT($fac)
    {
    	global $conf;
		$sql = "SELECT f.rowid, s.rowid as socid ";
		$sql.= "FROM llx_facture AS f, llx_societe AS s ";
		$sql.= "WHERE f.fk_soc = s.rowid ";
		$sql.= "AND f.rowid = ".$fac." ";

		$result = $this->db->query($sql);

		if ($result)
		{
			$num = $this->db->num_rows($result);
	    	$i = 0;

		    while ($i < $num)
    		{
        		$obj = $this->db->fetch_object($result);
				$ligne = "INT".$this->complChar($conf->global->MAIN_INFO_SOCIETE_GENCOD,"0",13).$this->complChar($obj->socid,"0",13);
        		fwrite($this->fp,$ligne."\n");
        		$i++;
    		}
		}



    }

    function writeLIN($fac)
    {
		$sql = "SELECT p.barcode, d.total_ttc,d.qty ";
		$sql.= "FROM llx_facture AS f, llx_facturedet AS d, llx_product AS p ";
		$sql.= "WHERE d.fk_product = p.rowid ";
		$sql.= "AND f.rowid = d.fk_facture ";
		$sql.= "AND f.rowid = ".$fac." ";

		$result = $this->db->query($sql);

		if ($result)
		{
			$num = $this->db->num_rows($result);
	    	$i = 0;

		    while ($i < $num)
    		{
        		$obj = $this->db->fetch_object($result);
				$ligne = "LIN01".$this->complChar($obj->barcode,"0",13);
				$ligne.= $this->complChar(""," ",250).str_replace(".","",$obj->total_ttc);
				$ligne.= $this->complChar($obj->qty,"0",4);
        		fwrite($this->fp,$ligne."\n");
        		$i++;
    		}
		}

    }

	function writeFIN()
    {

    	$ligne = "FIN".$this->complChar($obj->nbfact,"0",8);
    	fwrite($this->fp,$ligne);
    }


    function envoiMail()
    {
		global $langs, $conf;

		$subject = ":::EDLFDT01".$this->complChar($conf->global->MAIN_INFO_SOCIETE_GENCOD,"0",13);
		$sendto = $conf->global->DROITPRET_MAIL;
		$from = $conf->global->MAIN_INFO_SOCIETE_MAIL;
		$message = "";
		$filepath[0] = $conf->droitpret->dir_temp."/".$this->refFile;
		$filename[0] = $this->refFile;
		$mimetype[0] = 'text/csv';

		$sendtocc = "";
		$deliveryreceipt = "";

		require_once(DOL_DOCUMENT_ROOT.'/lib/CMailFile.class.php');
		$mailfile = new CMailFile($subject,$sendto,$from,$message,$filepath,$mimetype,$filename,$sendtocc,'',$deliveryreceipt);
		if ($mailfile->error)
		{
			$mesg='<div class="error">'.$mailfile->error.'</div>';
			echo $mesg;
		}
		else
		{
			$result=$mailfile->sendfile();
			if ($result)
			{
				$mesg='<div class="ok">'.$langs->trans('MailSuccessfulySent',$from,$sendto).'.</div>';
			}
			else
			{
				$mesg='<div class="error">';
				if ($mailfile->error)
				{
					$mesg.="error";
					$mesg.='<br>'.$mailfile->error;
				}
				else
				{
					$mesg.='No mail sent. Feature is disabled by option MAIN_DISABLE_ALL_MAILS';
				}
				$mesg.='</div>';

			}
		}
    	return $mesg;
    }

    function complChar($chaine,$char,$size)
    {
		$chaineSize=dol_strlen ($chaine);
		$complChar = $chaine;
		for ($i = $chaineSize; $i < $size;$i++)
		{
			$complChar = $char.$complChar;
		}

		return $complChar;

    }

    function formatDate($datetime)
    {
    	$formatDate = str_replace("-","",$datetime);
    	$formatDate = substr($formatDate,0,8);
    	return $formatDate;
    }

}

?>