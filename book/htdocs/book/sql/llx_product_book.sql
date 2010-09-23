-- ============================================================================
-- Copyright (C) 2008 Patrick Raguin <patrick.raguin@auguria.net> 
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
-- $Id: llx_product_book.sql,v 1.3 2010/09/23 16:20:00 cdelambert Exp $
-- ============================================================================

CREATE TABLE llx_product_book (
  rowid 		integer NOT NULL PRIMARY KEY,
  tms 			timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  nbpages 		integer default NULL,
  format 		varchar(1) default NULL,
  fk_contract 	integer default NULL
) type=innodb;















