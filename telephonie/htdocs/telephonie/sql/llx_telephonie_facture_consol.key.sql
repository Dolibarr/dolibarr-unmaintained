-- ========================================================================
-- Copyright (C) 2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
-- $Id: llx_telephonie_facture_consol.key.sql,v 1.1 2009/10/20 16:19:20 eldy Exp $
-- $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/sql/llx_telephonie_facture_consol.key.sql,v $
--
-- ========================================================================
--
-- Consolidation des factures de t�l�phonie
--

ALTER TABLE llx_telephonie_facture_consol ADD INDEX (groupe);
ALTER TABLE llx_telephonie_facture_consol ADD INDEX (agence);
ALTER TABLE llx_telephonie_facture_consol ADD INDEX (ligne);
ALTER TABLE llx_telephonie_facture_consol ADD INDEX (statut);
ALTER TABLE llx_telephonie_facture_consol ADD INDEX (repre);
ALTER TABLE llx_telephonie_facture_consol ADD INDEX (distri);
ALTER TABLE llx_telephonie_facture_consol ADD INDEX (repre_ib);
