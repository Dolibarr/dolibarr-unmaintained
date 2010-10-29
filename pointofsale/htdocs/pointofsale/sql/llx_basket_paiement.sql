-- ============================================================================
-- Copyright (C) 2010 Denis Martin <denimartin@hotmail.fr>
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
-- $Id: llx_basket_paiement.sql,v 1.1 2010/10/29 16:40:50 hregis Exp $
-- ============================================================================

create table llx_basket_paiement
(
	rowid		integer		AUTO_INCREMENT PRIMARY KEY,
	datec		date		NOT NULL,
	tms		timestamp	,
	datep		date		,
	amount		real		NOT NULL,
	fk_paiement	integer		NOT NULL,
	fk_bank		integer		,
	fk_basket	integer		NOT NULL
)type=innodb;
