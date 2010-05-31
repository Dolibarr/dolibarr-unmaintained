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
-- $Id: llx_product_book_contract.key.sql,v 1.1 2010/05/31 15:28:22 pit Exp $
-- ============================================================================

ALTER TABLE llx_product_book_contract ADD INDEX idx_product_book_contract_fk_product_book (fk_product_book);
ALTER TABLE llx_product_book_contract ADD FOREIGN KEY (fk_product_book) REFERENCES llx_product_book(rowid);