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
-- $Id: llx_basket_det.sql,v 1.1 2010/10/29 16:40:50 hregis Exp $
-- ============================================================================

create table llx_basket_det
(
	rowid			integer		AUTO_INCREMENT	PRIMARY KEY,
	fk_basket		integer		NOT NULL,
	fk_product		integer		NOT NULL,
	qty			real		NOT NULL,
	remise			real		,
	remise_percent		real		,
	tva_taux		real		,
	subprice		real		,
	price			real		,
	total_ht		real		,
	total_tva		real		,
	total_ttc		real		,
	product_type		integer		,
	date_start		datetime	,
	date_end		datetime	
)type=innodb;
