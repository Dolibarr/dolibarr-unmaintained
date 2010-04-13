<?php
/*  Copyright (C) 2006      Jean Heimburger     <jean@tiaris.info>
* Copyright (C) 2009      Jean-Francois FERRY    <jfefe@aternatik.fr>
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
 * $Id: thelia_customer.class.php,v 1.4 2010/04/13 14:05:42 grandoc Exp $
 */

/**
        \file       htdocs/thelia/clients/thelia_customer.class.php
        \ingroup    thelia
        \brief      Fichier de la classe des clients issus de Thelia
        \version    $Revision: 1.4 $
*/


/**
        \class      Thelia_customer
        \brief      Classe permettant la gestion des clients/prospects issus d'une base THELIA
*/


class Thelia_customer
{
	var $db;

	var $id;
   var $raison;
	var $entreprise;
   var $siret;
   var $intracom;
	var $nom;
	var $prenom;
	var $adresse1;
   var $adresse2;
   var $adresse3;
	var $cpostal;
	var $ville;
   var $pays;
	var $telfixe;
	var $telpro;
	var $email;
	var $type;
	var $pourcentage;

	var $error;

    /**
     *    \brief      Constructeur de la classe
     *    \param      id          Id client (0 par defaut)
     */
	function Thelia_customer($DB, $id=0) {

        global $langs;
         global $conf;

        $this->custid = $id ;

        /* les initialisations n�cessaires */
		$this->db = $DB;

	}


/**
*      \brief      Charge le client THELIA en mémoire
*      \param      id      Id du client dans OsC
*      \return     int     <0 si ko, >0 si ok
*/
   function fetch($id='')
    {
        global $langs;
		global $conf;

		$this->error = '';
		dol_syslog("Thelia_customer::fetch $id=$id ref=$ref");
      // Verification parametres
      if (! $id )
        {
            $this->error=$langs->trans('ErrorWrongParameters');
            return -1;
        }

		set_magic_quotes_runtime(0);

		//WebService Client.
		require_once(NUSOAP_PATH."/nusoap.php");
		require_once("../includes/configure.php");

		// Set the parameters to send to the WebService
		$parameters = array("custid"=>$id);

		// Set the WebService URL
		$client = new soapclient_nusoap(THELIA_WS_URL."/ws_customers.php");
	    if ($client)
		{
			$client->soap_defencoding='UTF-8';
		}

		// Call the WebSeclient->fault)rvice and store its result in $obj
		$obj = $client->call("get_Client",$parameters );
      // Attention c'est un tableau !!

		if ($client->fault) 
      {
			$this->error="Fault detected ".$client->getError();
			return -1;
		}
		elseif (!($err=$client->getError()) ) 
      {
  			$this->id = $obj[0][id];
         $this->ref = $obj[0][ref];
         $this->raison = $obj[0][raison];
  			$this->entreprise = $obj[0][entreprise];
         $this->siret = $obj[0][siret];
         $this->intracom = $obj[0][intracom];
  			$this->nom = $obj[0][nom];
  			$this->prenom = $obj[0][prenom];
  			$this->adresse1 = $obj[0][adresse1];
         $this->adresse2 = $obj[0][adresse2];
         $this->adresse3 = $obj[0][adresse3];
  			$this->cpostal = $obj[0][cpostal];
  			$this->ville = $obj[0][ville];
			$this->telfixe = $obj[0][telfixe];
			$this->telport = $obj[0][telport];
			$this->email = $obj[0][email];
			$this->pays = $obj[0][pays];
			$this->custcodecountry = $obj[0][countries_iso_code_2];
			$this->custcountry = $obj[0][countries_name];
  		}
  		else {
		    $this->error = 'Erreur '.$err ;
          
			return -1;
		}
		return 0;
	}

/**
*      \brief      Mise à jour de la table de transition
*      \param      theliaid      Id du client dans Thelia
*	   \param	   socid	  champ société.rowid
*      \return     int     <0 si ko, >0 si ok
*/
	function transcode($theliaid, $socid)
	{

		/* suppression et insertion */
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."thelia_customer WHERE rowid = ".$theliaid.";";
		$result=$this->db->query($sql);
      if ($result)
      {
         dol_syslog("thelia_customer::transcode : suppression ok ".$sql."  * ".$result);
      }
      else
      {
         dol_syslog("thelia_customer::transcode echec suppression");
         return -1;
		}
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."thelia_customer VALUES (".$theliaid.", ".$this->db->idate(mktime()).", ".$socid.") ;";
		$result=$this->db->query($sql);
      if ($result)
      {
         dol_syslog("thelia_customer::transcode Success");
		}
        else
        {
            dol_syslog("thelia_customer::transcode echec insert");
//            return -1;
		}
	return 0;
     }
     
     

   // converti l'id dolibarr en fonction du client thelia
	function get_clientid($thelia_client)
	{
		$sql = "SELECT fk_soc";
		$sql.= " FROM ".MAIN_DB_PREFIX."thelia_customer";
		$sql.= " WHERE rowid = ".$thelia_client;
     
		$resql=$this->db->query($sql);
		$obj = $this->db->fetch_object($resql);
      // test d'erreurs
		if ($obj) return $obj->fk_soc;
		else return '';
	}
   
   // retourne l'id client thelia en fonction de l'id doliabarr
   function get_thelia_clientid($doli_clientid)
   {
      $sql = "SELECT fk_soc";
      $sql.= " FROM ".MAIN_DB_PREFIX."thelia_customer";
      $sql.= " WHERE rowid = ".$doli_clientid;
      dol_syslog("get_thelia_clientid : " .$sql);
      $resql=$this->db->query($sql);
      $obj = $this->db->fetch_object($resql);
      // test d'erreurs
      if ($obj) return $obj->rowid;
      else return 0;
   }

}

?>
