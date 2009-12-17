<?php
/* Copyright (C) 2003-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2007      Jean Heimburger      <jean@tiaris.info>
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
 * $Id: configure.php,v 1.1 2009/12/17 14:57:01 hregis Exp $
 */

/**
 *	\file       htdocs/thelia/includes/configure.php
 *	\ingroup    thelia
 *	\brief      Configuration client Webservice
 *	\version    $Revision: 1.1 $
 */

// URL To reach web services
define(THELIA_DIR,'http://thelia/ws_server/');

// URL To reach Thelia
define(THELIA_URL, 'http://yourdomain.com/'); // url du site OSC



//affichages dans la page d'accueil
define(THELIA_MAXNBCOM, 5);
define(THELIA_ORDWAIT,'1'); // code du statut de commande en attente
define(THELIA_ORDPROCESS,'3'); // code du statut de commande en traitement

define(THELIA_ENTREPOT, 1); //l'entrepot lie au stock du site web
define(TX_CURRENCY, 1); // le taux de conversion monnaie site osc - monnaie dolibarr (1 euro = 119.33 XPF)
define(NB_DECIMALS, 2);
define(NB_DECIMALSITE, 2); // nb de decimales sur le site
define(FK_PORT, 2); // l'id du service frais de port defini.

// fonctions

/**
 *      \brief      assure la conversion en monnaie de dolibarr
 *      \param      oscid      Id du produit dans OsC
 *	   \param	   prodid	  champ r�f�rence
 *      \return     int     <0 si ko, >0 si ok
 */
function convert_price($price)
{
	return round($price * TX_CURRENCY, NB_DECIMALS);
}

/**
 *      \brief      assure la conversion en monnaie de dolibarr
 *      \param      oscid      Id du produit dans OsC
 *	   \param	   prodid	  champ r�f�rence
 *      \return     int     <0 si ko, >0 si ok
 */
function convert_backprice($price)
{
	return round($price / TX_CURRENCY, NB_DECIMALSITE);
}

?>
