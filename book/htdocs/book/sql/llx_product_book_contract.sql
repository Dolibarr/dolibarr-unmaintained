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
-- $Id: llx_product_book_contract.sql,v 1.1 2010/05/31 15:28:23 pit Exp $
-- ============================================================================

CREATE TABLE llx_product_book_contract (
  rowid 				integer NOT NULL auto_increment PRIMARY KEY,
  datec 				datetime default NULL,
  fk_product_book 		integer default NULL,
  fk_product_droit 		integer NOT NULL,
  taux 					float(20,2) default NULL,
  date_deb 				datetime default NULL,
  date_fin 				datetime default NULL,
  fk_soc 				integer NOT NULL,
  fk_user 				integer default NULL,
  quantity 				integer NOT NULL
) type=innodb;















