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
-- $Id: llx_product_cnv_livre_contrat.sql,v 1.1 2009/10/07 18:18:09 eldy Exp $
-- ============================================================================

create table llx_product_cnv_livre_contrat
(
  rowid              integer AUTO_INCREMENT PRIMARY KEY,
  fk_cnv_livre       integer,

  quantite           integer,          -- quantite achete
  taux               float(3,2),       -- taux contractuel

  date_app           datetime,         -- date d'application
  duree              varchar(50),      -- duree du contrat
  fk_user            integer,          -- utilisateur qui a saisi le contrat
  locked             tinyint default 0 -- indique si le contrat est verrouille a la modification
)type=innodb;



