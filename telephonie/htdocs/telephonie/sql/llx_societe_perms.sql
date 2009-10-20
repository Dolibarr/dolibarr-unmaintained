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
-- $Id: llx_societe_perms.sql,v 1.1 2009/10/20 16:19:21 eldy Exp $
-- ========================================================================

create table llx_societe_perms
(
  fk_soc    integer,
  fk_user   integer,
  pread     tinyint unsigned DEFAULT 0, -- permission de lecture
  pwrite    tinyint unsigned DEFAULT 0, -- permission d'ecriture
  pperms    tinyint unsigned DEFAULT 0, -- permission sur les permissions

  UNIQUE INDEX(fk_soc, fk_user)
)type=innodb;

