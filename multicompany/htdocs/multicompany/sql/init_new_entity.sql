-- Copyright (C) 2009 Regis Houssin        <regis@dolibarr.fr>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the Free Software
-- Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
--
-- $Id: init_new_entity.sql,v 1.2 2010/09/24 17:25:40 hregis Exp $
--

--
-- Ne pas placer de commentaire en fin de ligne, ce fichier est parsé lors
-- de l'install et tous les sigles '--' sont supprimés.
--


-- Hidden but specific to one entity 
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_MONNAIE','EUR','chaine','Monnaie',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_MAIL_EMAIL_FROM','dolibarr-robot@domain.com','chaine','EMail emetteur pour les emails automatiques Dolibarr',0,__ENTITY__);

--
-- IHM
--

insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_MENU_BARRETOP','eldy_backoffice.php','chaine','Module de gestion de la barre de menu du haut pour utilisateurs internes',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_MENUFRONT_BARRETOP','eldy_frontoffice.php','chaine','Module de gestion de la barre de menu du haut pour utilisateurs externes',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_MENU_BARRELEFT','eldy_backoffice.php','chaine','Module de gestion de la barre de menu gauche pour utilisateurs internes',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_MENUFRONT_BARRELEFT','eldy_frontoffice.php','chaine','Module de gestion de la barre de menu gauche pour utilisateurs externes',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_MENU_SMARTPHONE','iphone_backoffice.php','chaine','Module de gestion de la barre de menu smartphone pour utilisateurs internes',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_MENUFRONT_SMARTPHONE','iphone_frontoffice.php','chaine','Module de gestion de la barre de menu smartphone pour utilisateurs externes',0,__ENTITY__);

insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_THEME','eldy','chaine','Thème par défaut',0,__ENTITY__);

--
-- Delai tolerance
--
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_DELAY_ACTIONS_TODO','7','chaine','Tolérance de retard avant alerte (en jours) sur actions planifiées non réalisées',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_DELAY_ORDERS_TO_PROCESS','2','chaine','Tolérance de retard avant alerte (en jours) sur commandes non traitées',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_DELAY_PROPALS_TO_CLOSE','31','chaine','Tolérance de retard avant alerte (en jours) sur propales à cloturer',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_DELAY_PROPALS_TO_BILL','7','chaine','Tolérance de retard avant alerte (en jours) sur propales non facturées',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_DELAY_SUPPLIER_BILLS_TO_PAY','2','chaine','Tolérance de retard avant alerte (en jours) sur factures fournisseur impayées',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_DELAY_CUSTOMER_BILLS_UNPAYED','31','chaine','Tolérance de retard avant alerte (en jours) sur factures client impayées',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_DELAY_NOT_ACTIVATED_SERVICES','0','chaine','Tolérance de retard avant alerte (en jours) sur services à activer',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_DELAY_RUNNING_SERVICES','0','chaine','Tolérance de retard avant alerte (en jours) sur services expirés',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_DELAY_MEMBERS','31','chaine','Tolérance de retard avant alerte (en jours) sur cotisations adhérent en retard',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_DELAY_TRANSACTIONS_TO_CONCILIATE','62','chaine','Tolérance de retard avant alerte (en jours) sur rapprochements bancaires à faire',0,__ENTITY__);

--
-- Tiers
--
insert into llx_const (name, value, type, note, visible, entity) values('SOCIETE_CODECLIENT_ADDON','mod_codeclient_leopard','yesno','Module to control third parties codes',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values('SOCIETE_CODECOMPTA_ADDON','mod_codecompta_panicum','yesno','Module to control third parties codes',0,__ENTITY__);

--
-- Mail Mailing
--
insert into llx_const (name, value, type, note, visible, entity) values ('MAILING_EMAIL_FROM','dolibarr@domain.com','chaine','EMail emmetteur pour les envois d emailings',0,__ENTITY__);

--
-- FCKEditor
--
insert into llx_const (name, value, type, note, visible, entity) values ('FCKEDITOR_ENABLE_USER',       1,'yesno','Activation fckeditor sur notes utilisateurs',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('FCKEDITOR_ENABLE_SOCIETE',    1,'yesno','Activation fckeditor sur notes societe',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('FCKEDITOR_ENABLE_PRODUCTDESC',1,'yesno','Activation fckeditor sur notes produits',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('FCKEDITOR_ENABLE_MEMBER',     1,'yesno','Activation fckeditor sur notes adherent',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('FCKEDITOR_ENABLE_MAILING',    1,'yesno','Activation fckeditor sur emailing',0,__ENTITY__);

--
-- OsCommerce 1
--
insert into llx_const (name, value, type, note, visible, entity) values ('OSC_DB_HOST','localhost','chaine', 'Host for OSC database for OSCommerce module 1', 0,__ENTITY__);

--
-- Modeles de numerotation et generation document
--
insert into llx_const (name, value, type, note, visible, entity) values ('DON_ADDON_MODEL',     'html_cerfafr','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('PROPALE_ADDON',       'mod_propale_marbre','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('PROPALE_ADDON_PDF',   'azur','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('COMMANDE_ADDON',      'mod_commande_marbre','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('COMMANDE_ADDON_PDF',  'einstein','chaine', '',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('COMMANDE_SUPPLIER_ADDON',      'mod_commande_fournisseur_muguet','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('COMMANDE_SUPPLIER_ADDON_PDF',  'muscadet','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('EXPEDITION_ADDON',    'enlevement','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('EXPEDITION_ADDON_PDF','rouget','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('FICHEINTER_ADDON',    'pacific','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('FICHEINTER_ADDON_PDF','soleil','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('FACTURE_ADDON',       'terre','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('FACTURE_ADDON_PDF',   'crabe','chaine','',0,__ENTITY__);

--
-- Duree de validite des propales
--
insert into llx_const (name, value, type, note, visible, entity) VALUES ('PROPALE_VALIDITY_DURATION',      '15', 'chaine', 'Durée de validitée des propales',0,__ENTITY__);

--
-- Action sur agenda
--
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_COMPANY_CREATE','1','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_CONTRACT_VALIDATE','1','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_PROPAL_VALIDATE','1','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_PROPAL_SENTBYMAIL','1','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_ORDER_VALIDATE','1','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_ORDER_SENTBYMAIL','1','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_BILL_VALIDATE','1','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_BILL_PAYED','1','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_BILL_CANCEL','1','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_BILL_SENTBYMAIL','1','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_ORDER_SUPPLIER_VALIDATE','1','chaine','',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('MAIN_AGENDA_ACTIONAUTO_BILL_SUPPLIER_VALIDATE','1','chaine','',0,__ENTITY__);
