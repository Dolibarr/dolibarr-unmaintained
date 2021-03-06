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
 * $Id: commande.textp.class.php,v 1.2 2010/08/24 20:27:24 grandoc Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/fournisseur/commande/commande.textp.class.php,v $
 *
 * Classe de commande de ligne au format Texte
 *
 *
 */
require_once DOL_DOCUMENT_ROOT."/telephonie/dolibarrmail.class.php";
require_once DOL_DOCUMENT_ROOT."/telephonie/fournisseur/commande/methode.commande.class.php";

define ('COMMANDETEXT_NOEMAIL', -3);

class CommandeMethodeTextP extends CommandeMethode
{

  function CommandeMethodeTextP ($DB, $USER=0, $fourn=0)
  {
    $this->nom = "M�thode texte, variante sur le nom de fichier";
    $this->db = $DB;
    $this->user = $USER;
    $this->fourn = $fourn;
  }

  function info()
  {
    return "Envoi un fichier texte contenant la liste des lignes � commander";
  }

  function Create()
  {
    $this->date = time();

    $this->datef = "ndi-premium-".strftime("%d%b%y-%HH%M", $this->date);

    $this->filename = $this->datef.".txt";

    $fname = DOL_DATA_ROOT ."/telephonie/ligne/commande/".$this->filename;

    if (dol_strlen(trim($this->fourn->email_commande)) == 0)
      {
	return -3;
      }

    if (file_exists($fname))
      {
	return 2;
      }
    else
      {
	$res = $this->CreateFile($fname);

	if ($res == 0)
	  {
	    $res = $res + $this->LogSql();
	    $res = $res + $this->MailFile($fname);
	  }

	return $res;
      }
  }
  /**
   *
   *
   */
  function MailFile($filename)
  {
    $subject = "Commande de Lignes Numero : ".$this->commande_id;

    $sendto = $this->fourn->email_commande;

    $from = TELEPHONIE_LIGNE_COMMANDE_EMAIL_BCC;

    $message = "Bonjour,\n\nVeuillez trouver ci-joint notre commande num : ".$this->commande_id.".\n\n";
    $message .= "\n\nCordialement,\n\n";

    $message .= "-- \n";
    $message .= $this->user->fullname."\n";


    $mailfile = new DolibarrMail($subject,
				 $sendto,
				 $from,
				 $message);

    $mailfile->addr_bcc = TELEPHONIE_LIGNE_COMMANDE_EMAIL_BCC;

    $mailfile->PrepareFile(array($filename),
			   array("plain/text"),
			   array($this->datef.".txt"));

    if ( $mailfile->sendfile() )
      {
	return 0;
      }

  }

  /**
   * Creation du fichier
   *
   */

  function CreateFile($fname)
  {
    $fp = fopen($fname, "w");

    if ($fp)
      {
	fwrite ($fp, "Numcli;");
	fwrite ($fp, "nomclient;");
	fwrite ($fp, "NDI\n");

	$this->ligneids = array();
	
	$sqlall = "SELECT s.nom, s.rowid as socid, l.ligne, l.statut, l.rowid";
	$sqlall .= " , comm.name, comm.firstname";
	
	$sqlall .= " FROM ".MAIN_DB_PREFIX."societe as s";
	$sqlall .= " , ".MAIN_DB_PREFIX."telephonie_societe_ligne as l";
	$sqlall .= " , ".MAIN_DB_PREFIX."user as comm";
	$sqlall .= " , ".MAIN_DB_PREFIX."telephonie_fournisseur as f";

	$sqlall .= " WHERE l.fk_soc = s.rowid AND l.fk_fournisseur = f.rowid";

	$sqlall .= " AND l.fk_commercial = comm.rowid ";
	$sqlall .= " AND f.rowid =".$this->fourn->id;
	/*
	 *
	 */
	$sql = $sqlall;
	
	$sql .= " AND l.statut in (1,4,8)";
	$sql .= " ORDER BY l.statut ASC";
	
	$resql = $this->db->query($sql);

	if ($resql)
	  {
	    $i = 0;
	    $num = $this->db->num_rows($resql);
	    
	    while ($i < $num)
	      {
		$obj = $this->db->fetch_object($resql);	
		
		if (dol_strlen($obj->ligne)== 10)
		  {		    
		    $soc = new Societe($this->db);
		    $soc->fetch($obj->socid);
		    
		    fwrite ($fp, $this->fourn->num_client);
		    fwrite ($fp, ";");
		    fwrite ($fp, $obj->nom);
		    fwrite ($fp, ";");
		    fwrite ($fp, $obj->ligne);
		    fwrite ($fp, "\n");
		    
		    array_push($this->ligneids, $obj->rowid);
		  }
		$i++;
	      }
	    
	    $this->db->free($resql);
	  }
	else 
	  {
	    print $this->db->error() . ' ' . $sql;
	  }

	fclose($fp);
	
	/*
	 *
	 *
	 */
	
	foreach ($this->ligneids as $lid)
	  {
	    
	    $lint = new LigneTel($this->db);
	    $lint->fetch_by_id($lid);
	    if ($lint->statut == 1)
	      {
		$lint->set_statut($this->user, 2);
	      }
	    if ($lint->statut == 4)
	      {
		$lint->set_statut($this->user, 5);
	      }
	    if ($lint->statut == 8)
	      {
		$lint->set_statut($this->user, 2);
	      }
	  }
	
	return 0;
	
      }
    else
      {
	return -1;
      }
  }
}
