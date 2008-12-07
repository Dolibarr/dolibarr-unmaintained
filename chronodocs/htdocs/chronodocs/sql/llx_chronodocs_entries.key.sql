-- ============================================================================
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
-- $Id: llx_chronodocs_entries.key.sql,v 1.1 2008/12/07 18:14:36 eldy Exp $
-- ============================================================================


ALTER TABLE llx_chronodocs_entries ADD INDEX idx_chronodocs_entries_ref (ref);
ALTER TABLE llx_chronodocs_entries ADD INDEX idx_chronodocs_entries_socid(socid);
ALTER TABLE llx_chronodocs_entries ADD INDEX idx_chronodocs_entries_date_c (date_c);
ALTER TABLE llx_chronodocs_entries ADD INDEX idx_chronodocs_entries_fk_status (fk_status);
ALTER TABLE llx_chronodocs_entries ADD INDEX idx_chronodocs_entries_fk_chronodocs_type(fk_chronodocs_type);

ALTER TABLE llx_chronodocs_entries ADD CONSTRAINT fk_chronodocs_entries_fk_chronodocs_type FOREIGN KEY (fk_chronodocs_type) REFERENCES llx_chronodocs_types (rowid);
ALTER TABLE llx_chronodocs_entries ADD CONSTRAINT fk_chronodocs_entries_socid         FOREIGN KEY (socid)          REFERENCES llx_societe (rowid);
ALTER TABLE llx_chronodocs_entries ADD CONSTRAINT fk_chronodocs_entries_fk_user_c     FOREIGN KEY (fk_user_c)      REFERENCES llx_user (rowid);
ALTER TABLE llx_chronodocs_entries ADD CONSTRAINT fk_chronodocs_entries_fk_user_u     FOREIGN KEY (fk_user_u)      REFERENCES llx_user (rowid);
