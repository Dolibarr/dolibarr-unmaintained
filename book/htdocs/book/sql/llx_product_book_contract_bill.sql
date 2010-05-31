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
-- $Id: llx_product_book_contract_bill.sql,v 1.1 2010/05/31 15:28:23 pit Exp $
-- ============================================================================

CREATE TABLE llx_product_book_contract_bill (
  rowid 				integer NOT NULL auto_increment PRIMARY KEY,
  datec 				datetime default NULL,
  tms					timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  ref 					varchar(250) NOT NULL,
  date_deb 				datetime NOT NULL,
  date_fin 				datetime NOT NULL,
  qtyOrder 				int(11) NOT NULL,
  qtySold 				int(11) NOT NULL,
  fk_contract 			int(11) default NULL,
  fk_commande 			int(11) default NULL
) type=innodb;















