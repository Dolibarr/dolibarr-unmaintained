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
-- $Id: llx_chronodocs_entries.sql,v 1.1 2008/12/07 18:14:35 eldy Exp $
-- ===================================================================

-- drop table llx_chronodocs_entries;

create table llx_chronodocs_entries
(
  rowid           integer AUTO_INCREMENT PRIMARY KEY,
  ref             varchar(32)  NOT NULL, -- Reference du document
  title           varchar(255) NOT NULL, -- Titre du document 
  filename        varchar(255), -- Nom de stockage du fichier joint (si existe)
  filename_orig   varchar(255), -- Nom d'origine du fichier joint (si existe)
  filesize        integer, -- Taille du fichier joint (si existe)
  brief           text, -- Description
  date_c	      date, -- Date du document (saisie)
  date_u		  timestamp, -- Date edition fiche
  fk_chronodocs_type integer NOT NULL, -- Type document
  socid           integer NOT NULL, -- Societe concernee
  fk_status		  smallint DEFAULT '0',
  fk_user_c       integer,
  fk_user_u       integer
) type=innodb;
