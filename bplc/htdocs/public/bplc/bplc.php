<?php
/* Copyright (C) 2002-2003 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2009      Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\ingroup    banque
 * 	\brief		Gestion du retour du systeme de Cyberpaiement
 * 				Cette page est appellee par le serveur de la BPLC lors de
 * 				l'utilisation au systeme RSTS.
 * 	\version	$Id: bplc.php,v 1.3 2010/08/24 20:27:25 grandoc Exp $
 */

define("NOLOGIN",1);	// This means this output page does not require to be logged.

require("../../main.inc.php");
require_once(DOL_DOCUMENT_ROOT ."/compta/dons/class/don.class.php");
require_once(DOL_DOCUMENT_ROOT."/public/bplc/retourbplc.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/dons/class/don.class.php");

// Security check
if (empty($conf->bplc->enabled)) accessforbidden('',1,1,1);


$retbplc = new Retourbplc($db);

$retbplc->num_compte        = $conf->bplc->num_compte;

$retbplc->montant           = $CHAMP201;
$retbplc->num_contrat       = $CHAMP002;
$retbplc->ref_commande      = $CHAMP200;
$retbplc->ipclient          = $CHAMP105;
$retbplc->num_transaction   = $CHAMP901;
$retbplc->date_transaction  = $CHAMP902;
$retbplc->heure_transaction = $CHAMP903;
$retbplc->num_autorisation  = $CHAMP904;
$retbplc->cle_acceptation   = $CHAMP905;
$retbplc->code_retour       = $CHAMP906;

$retbplc->ref_commande      = $CHAMP200;

/*
 * Insertion de la transaction dans la base
 */

$return =  $retbplc->insertdb();


$don_id = substr($retbplc->ref_commande, 0, dol_strlen($retbplc->ref_commande) -2);
print $don_id;

if($return)
{
  if ($retbplc->check_key($retbplc->cle_acceptation))
    {


      /*
       * Validation de la commande
       *
       */

      $don = new Don($db);

      $don_id = strstr($retbplc->ref_commande, 0, dol_strlen($retbplc->ref_commande) -2);

      // 5 correspond au paiement en ligne voir table llx_c_paiement

      $don->set_paye($don_id, 5);

    }

}
