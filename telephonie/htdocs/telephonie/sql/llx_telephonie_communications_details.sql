-- ========================================================================
-- Copyright (C) 2005-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
-- $Id: llx_telephonie_communications_details.sql,v 1.1 2009/10/20 16:19:20 eldy Exp $
-- $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/sql/llx_telephonie_communications_details.sql,v $
--
-- ========================================================================
--
-- Communications t�l�phoniques trait�es

create table llx_telephonie_communications_details (
  fk_ligne              integer,
  ligne                 varchar(255) NOT NULL,
  date                  datetime,
  numero                varchar(255),
  dest                  varchar(255),
  dureetext             varchar(255),
  fourn_cout            varchar(255),
  fourn_montant         real,
  duree                 integer,        -- duree en secondes
  tarif_achat_temp      real,  
  tarif_achat_fixe      real,  
  tarif_vente_temp      real,  
  tarif_vente_fixe      real,  
  cout_achat            real,
  cout_vente            real,
  remise                real,
  fichier_cdr           varchar(255),
  fk_fournisseur        integer,
  fk_telephonie_facture integer,
  ym                    char(4),
  num_prefix            char(2),

  key (fk_fournisseur),
  key (fk_ligne),
  key (fk_telephonie_facture),
  key (ym),
  key (num_prefix)

)type=innodb;
