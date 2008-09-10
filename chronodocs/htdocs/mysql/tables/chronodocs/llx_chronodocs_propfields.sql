-- ===================================================================
-- Copyright (C) 2008      Raphael Bertrand (Resultic) <raphael.bertrand@resultic.fr>
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
-- $Id: llx_chronodocs_propfields.sql,v 1.2 2008/09/03 15:31:52 Raphael Exp $
-- ===================================================================

-- drop table llx_chronodocs_types;

create table llx_chronodocs_propfields
(
  rowid           integer AUTO_INCREMENT PRIMARY KEY,
  ref             varchar(16)  NOT NULL, -- Reference de la propriete, utilise pour parsing
  title           varchar(255) NOT NULL, -- Titre de la propriete
  brief           text, -- Description
  fk_type         integer NOT NULL, -- Type document concerne
  fk_status		  smallint DEFAULT '0'
) type=innodb;
