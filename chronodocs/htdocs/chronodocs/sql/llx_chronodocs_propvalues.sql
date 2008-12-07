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
-- $Id: llx_chronodocs_propvalues.sql,v 1.1 2008/12/07 18:14:35 eldy Exp $
-- ===================================================================

-- drop table llx_chronodocs_types;

create table llx_chronodocs_propvalues
(
  rowid           integer AUTO_INCREMENT PRIMARY KEY,
  content         text, -- Prop Value
  fk_propfield    integer NOT NULL, -- Id Propriete concerne
  fk_objectid     integer NOT NULL -- Id Chronodoc concerne
) type=innodb;
