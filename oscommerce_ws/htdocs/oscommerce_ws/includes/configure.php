<?php
/* Copyright (C) 2003-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2007      Jean Heimburger      <jean@tiaris.info>
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
 * $Id: configure.php,v 1.1 2010/04/13 13:41:13 grandoc Exp $
 */

/**
 *	\file       htdocs/oscommmerce_ws/includes/configure.php
 *	\ingroup    oscommerce_ws
 *	\brief      Configuration client Webservice
 *	\version    $Revision: 1.1 $
 */

// URL To reach web services
//define(OSCWS_DIR,'http://myoscserver/ws_server/');
define(OSCWS_DIR,'http://127.0.0.1/oscommerce_ws/ws_server/');

// URL To reach OSCommerce
define(OSC_URL, 'http://myoscserver/'); // url du site OSC



//affichages dans la page d'accueil
define(OSC_MAXNBCOM, 5);
define(OSC_ORDWAIT,'4'); // code du statut de commande en attente
define(OSC_ORDPROCESS,'1'); // code du statut de commande en traitement

define(OSC_ENTREPOT, 1); //l'entrepot lie au stock du site web
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
