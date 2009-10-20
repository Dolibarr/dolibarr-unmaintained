-- ========================================================================
-- Copyright (C) 2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
-- $Id: llx_telephonie_societe_options.sql,v 1.1 2009/10/20 16:19:21 eldy Exp $
-- $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/sql/llx_telephonie_societe_options.sql,v $
--
-- ========================================================================
--
--
create table llx_telephonie_societe_options (
  rowid              integer AUTO_INCREMENT PRIMARY KEY,
  datec              datetime,
  fk_client_comm     integer NOT NULL,      -- Client d�cideur

  type               enum('ligne','contrat'),
  propriete          varchar(255),
  valeur             varchar(255)

)type=innodb;


