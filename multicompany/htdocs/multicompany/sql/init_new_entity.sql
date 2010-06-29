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
-- $Id: init_new_entity.sql,v 1.1 2010/06/29 14:57:06 hregis Exp $
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
-- Mail Adherent
--
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAIL_REQUIRED','1','yesno','Le mail est obligatoire pour créer un adhérent',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAIL_FROM','adherents@domain.com','chaine','From des mails adherents',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAIL_RESIL','Votre adhesion vient d\'etre resiliee.\r\nNous esperons vous revoir tres bientot','texte','Mail de Resiliation',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAIL_VALID','Votre adhesion vient d\'etre validee. \r\nVoici le rappel de vos coordonnees (toute information erronee entrainera la non validation de votre inscription) :\r\n\r\n%INFOS%\r\n\r\n','texte','Mail de validation',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAIL_COTIS','Bonjour %PRENOM%,\r\nMerci de votre inscription.\r\nCet email confirme que votre cotisation a ete recue et enregistree.\r\n\r\n','texte','Mail de validation de cotisation',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAIL_VALID_SUBJECT','Votre adhésion a ete validée','chaine','Sujet du mail de validation',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAIL_RESIL_SUBJECT','Resiliation de votre adhesion','chaine','Sujet du mail de resiliation',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAIL_COTIS_SUBJECT','Recu de votre cotisation','chaine','Sujet du mail de validation de cotisation',0,__ENTITY__);

--
-- Mail Mailing
--
insert into llx_const (name, value, type, note, visible, entity) values ('MAILING_EMAIL_FROM','dolibarr@domain.com','chaine','EMail emmetteur pour les envois d emailings',0,__ENTITY__);

--
-- Mailman
--
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_USE_MAILMAN','0','yesno','Utilisation de Mailman',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAILMAN_UNSUB_URL','http://lists.domain.com/cgi-bin/mailman/admin/%LISTE%/members?adminpw=%MAILMAN_ADMINPW%&user=%EMAIL%','chaine','Url de desinscription aux listes mailman',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAILMAN_URL','http://lists.domain.com/cgi-bin/mailman/admin/%LISTE%/members?adminpw=%MAILMAN_ADMINPW%&send_welcome_msg_to_this_batch=1&subscribees=%EMAIL%','chaine','Url pour les inscriptions mailman',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAILMAN_LISTS','test-test,test-test2','chaine','Listes auxquelles inscrire les nouveaux adherents',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAILMAN_ADMINPW','','chaine','Mot de passe Admin des liste mailman',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAILMAN_SERVER','lists.domain.com','chaine','Serveur hebergeant les interfaces d Admin des listes mailman',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_MAILMAN_LISTS_COTISANT','','chaine','Liste(s) auxquelles les nouveaux cotisants sont inscris automatiquement',0,__ENTITY__);

--
-- SPIP
--
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_USE_SPIP','0','yesno','Utilisation de SPIP ?',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_USE_SPIP_AUTO','0','yesno','Utilisation de SPIP automatiquement',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_SPIP_USER','user','chaine','user spip',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_SPIP_PASS','pass','chaine','Pass de connection',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_SPIP_SERVEUR','localhost','chaine','serveur spip',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_SPIP_DB','spip','chaine','db spip',0,__ENTITY__);

--
-- Cartes adherents
--
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_CARD_HEADER_TEXT','%ANNEE%','chaine','Texte imprime sur le haut de la carte adherent',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_CARD_FOOTER_TEXT','Association AZERTY','chaine','Texte imprime sur le bas de la carte adherent',0,__ENTITY__);
insert into llx_const (name, value, type, note, visible, entity) values ('ADHERENT_CARD_TEXT','%TYPE% n° %ID%\r\n%PRENOM% %NOM%\r\n<%EMAIL%>\r\n%ADRESSE%\r\n%CP% %VILLE%\r\n%PAYS%','texte','Texte imprime sur la carte adherent',0,__ENTITY__);

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
