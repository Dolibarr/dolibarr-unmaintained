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
-- $Id: llx_chronodocs_types.sql,v 1.3 2008/08/19 15:59:57 Raphael Exp $
-- ===================================================================

-- drop table llx_chronodocs_types;

create table llx_chronodocs_types
(
  rowid           integer AUTO_INCREMENT PRIMARY KEY,
  ref             varchar(8)  NOT NULL, -- Reference du type de document, utilise pour numerotation
  title           varchar(255) NOT NULL, -- Titre du type de document
  brief           text, -- Description
  filename        varchar(255), -- Fichier modele
  date_c          timestamp, -- Date creation fiche
  date_u		  timestamp, -- Date edition fiche
  fk_status		  smallint DEFAULT '0',	
  fk_user_c       integer,
  fk_user_u       integer
) type=innodb;
