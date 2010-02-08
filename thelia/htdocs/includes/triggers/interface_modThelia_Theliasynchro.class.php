<?php
/* Copyright (C) 2005-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2008 Regis Houssin        <regis@dolibarr.fr>
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
        \file       htdocs/includes/triggers/interface_modThelia_Theliasynchro.class.php
        \ingroup    phenix
        \brief      Fichier de gestion des triggers phenix
		\version	$Id: interface_modThelia_Theliasynchro.class.php,v 1.2 2010/02/08 00:50:30 jfefe Exp $
*/

include_once(DOL_DOCUMENT_ROOT.'/thelia/produits/thelia_product.class.php');
include_once(DOL_DOCUMENT_ROOT.'/thelia/produits/thelia_categories.class.php');
include_once(DOL_DOCUMENT_ROOT.'/thelia/commandes/thelia_order.class.php');
include_once(DOL_DOCUMENT_ROOT.'/thelia/clients/thelia_customer.class.php');

/**
        \class      InterfaceTheliasynchro
        \brief      Classe des fonctions triggers des actions Thelia
*/

class InterfaceTheliasynchro
{
    var $db;
    var $error;

    var $date;
    var $duree;
    var $texte;
    var $desc;

    /**
     *   \brief      Constructeur.
     *   \param      DB      Handler d'acces base
     */
    function InterfaceTheliasynchro($DB)
    {
        $this->db = $DB ;

        $this->name = preg_replace('/^Interface/i','',get_class($this));
        $this->family = "theliaws";
        $this->description = "Triggers of this module allows to synchronize Thelia WebSite for each Dolibarr business event.";
        $this->version = 'experimental';                        // 'experimental' or 'dolibarr' or version
    }

    /**
     *   \brief      Renvoi nom du lot de triggers
     *   \return     string      Nom du lot de triggers
     */
    function getName()
    {
        return $this->name;
    }

    /**
     *   \brief      Renvoi descriptif du lot de triggers
     *   \return     string      Descriptif du lot de triggers
     */
    function getDesc()
    {
        return $this->description;
    }

    /**
     *   \brief      Renvoi version du lot de triggers
     *   \return     string      Version du lot de triggers
     */
    function getVersion()
    {
        global $langs;
        $langs->load("admin");

        if ($this->version == 'experimental') return $langs->trans("Experimental");
        elseif ($this->version == 'dolibarr') return DOL_VERSION;
        elseif ($this->version) return $this->version;
        else return $langs->trans("Unknown");
    }

    /**
     *      \brief      Fonction appelee lors du declenchement d'un evenement Dolibarr.
     *                  D'autres fonctions run_trigger peuvent etre presentes dans includes/triggers
     *      \param      action      Code de l'evenement
     *      \param      object      Objet concerne
     *      \param      user        Objet user
     *      \param      lang        Objet lang
     *      \param      conf        Objet conf
     *      \return     int         <0 si ko, 0 si aucune action faite, >0 si ok
     */
    function run_trigger($action,$object,$user,$langs,$conf)
    {
        // Mettre ici le code a executer en reaction de l'action
        // Les donnees de l'action sont stockees dans $object

        if (! $conf->theliaws->enabled) return 0;     // Module non actif

        // Suppression d'un produit : on supprime l'entreé dans la table de relations si le produit est dans le catalogue THELIA
        if ($action == 'PRODUCT_DELETE')
        {
            dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
            $langs->load("other");
            $product = new Thelia_product($this->db);
            // Le produit est aussi sur le site Thelia
            if($product->get_thelia_productid($object->id) > 0 ) 
            {
               $sql = "DELETE ";
               $sql.= " FROM ".MAIN_DB_PREFIX."thelia_product";
               $sql.= " WHERE fk_product = ".$object->id;
               $result = $this->db->query($sql);
               if($result > 0 ) 
               {
                  dol_syslog("Trigger '".$this->name."' for action '$action' : Suppression du produit  SUCCESS ");
                  return 1;
               } 
               else 
               {
                  dol_syslog("Trigger '".$this->name."' for action '$action' : Suppression du produit  FAILED ");
               }
            }
            else 
            {
               dol_syslog("Trigger '".$this->name."' for action '$action' : Suppression du produit NOT NEED ");
            }
        }
        
        // Suppression d'une commande : on supprime l'entrée de la table des relations THELIA
        if ($action == 'ORDER_DELETE')
        {
           dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
         //  $langs->load("other");
           $order = new Thelia_order($this->db);
           // Le produit est aussi sur le site Thelia
           if($order->get_thelia_orderid($object->id) > 0 ) 
           {
              $sql = "DELETE ";
              $sql.= " FROM ".MAIN_DB_PREFIX."thelia_order";
              $sql.= " WHERE fk_commande = ".$object->id;
  
              $result = $this->db->query($sql);
              if($result > 0 ) 
              {
                 dol_syslog("Trigger '".$this->name."' for action '$action' : Suppression de la commande  SUCCESS ");
                 return 1;
              } 
              else 
              {
                 dol_syslog("Trigger '".$this->name."' for action '$action' : Suppression de la commande  FAILED ");
              }
           }
           else 
           {
              dol_syslog("Trigger '".$this->name."' for action '$action' : Suppression de la commande NOT NEED ");
           }
        }
        
        
        // Suppression d'un client 
        if ($action == 'COMPANY_DELETE')
        {
           dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
           //  $langs->load("other");
           $client = new Thelia_customer($this->db);
           
           // on supprime l'entrée de la table des relations THELIA
           if($client->get_thelia_clientid($object->id) > 0 ) 
           {
              $sql = "DELETE ";
              $sql.= " FROM ".MAIN_DB_PREFIX."thelia_customer";
              $sql.= " WHERE fk_soc = ".$object->id;
              
              $result = $this->db->query($sql);
              if($result > 0 ) 
              {
                 dol_syslog("Trigger '".$this->name."' for action '$action' : Suppression du client  SUCCESS ");
                 return 1;
              } 
              else 
              {
                 dol_syslog("Trigger '".$this->name."' for action '$action' : Suppression du client  FAILED ");
              }
           }
           else 
           {
              dol_syslog("Trigger '".$this->name."' for action '$action' : Suppression du client NOT NEED ");
           }
        }
        
        // Suppression d'un produit : on supprime l'entreé dans la table de relations si le produit est dans le catalogue THELIA
        if ($action == 'CATEGORIE_DELETE')
        {
           dol_syslog("Trigger '".$this->name."' for action '$action' launched by ".__FILE__.". id=".$object->id);
           $langs->load("other");
           $cat = new Thelia_Categorie($this->db);
           // On regarde s'il y a une catégorie thelia associée
           $cat->fetch_dolicat($object->id);
           // Si oui on efface l'entre dans la table de relations
           if($cat->theliacatid > 0)
           {
              $sql = "DELETE ";
              $sql.= " FROM ".MAIN_DB_PREFIX."thelia_categories";
              $sql.= " WHERE dolicatid = ".$object->id;
              $result = $this->db->query($sql);
              if($result > 0 ) 
              {
                 dol_syslog("Trigger '".$this->name."' for action '$action' : Suppression de la catégorie  SUCCESS ");
                 return 1;
              } 
              else 
              {
                 dol_syslog("Trigger '".$this->name."' for action '$action' :  Suppression de la catégorie  FAILED ");
              }
           }
           else 
           {
              dol_syslog("Trigger '".$this->name."' for action '$action' :  Suppression de la catégorie NOT NEED ");
           }
        }
		return 0;
    }

}
?>
