-- ============================================================================
-- Copyright (C) 2006-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
-- $Id: llx_product_cnv_livre.sql,v 1.2 2010/03/17 11:51:51 eldy Exp $
-- ============================================================================

--
-- Produit specifique livre
--
create table llx_product_cnv_livre
(
  rowid              integer PRIMARY KEY,
  isbn               varchar(13),       -- code ISBN
  ean                varchar(13),       -- code EAN
  format             varchar(7),        -- format de l'ouvrage

  px_feuillet        float(12,4),       -- prix au feuillet
  px_reliure         float(12,4),       -- prix de la reliure
  px_couverture      float(12,4),       -- prix de la couverture
  px_revient         float(12,4),       -- prix de revient

  pages              smallint UNSIGNED, -- nombre de page

  fk_couverture      integer,
  fk_contrat         integer,
  fk_auteur          integer DEFAULT 0           -- auteur lien vers llx_societe
)type=innodb;



