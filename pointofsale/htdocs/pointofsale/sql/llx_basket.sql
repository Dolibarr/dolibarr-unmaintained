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
-- $Id: llx_basket.sql,v 1.1 2010/10/29 16:40:50 hregis Exp $
-- ============================================================================

create table llx_basket
(
	rowid		integer		AUTO_INCREMENT PRIMARY KEY,
	fk_user		varchar(50)	NOT NULL,
	fk_contact	integer		NULL,
	tms		timestamp	,
	total_ttc	real		,
	total_tva	real		,
	total_ht	real		,
	statut		int		
)type=innodb;
