<?PHP
/* Copyright (C) 2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 * $Id: onglets.php,v 1.1 2009/10/20 16:19:29 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/stats/consultation/onglets.php,v $
 *
 */
$h = 0;
$head[$h][0] = DOL_URL_ROOT.'/telephonie/stats/contrats/index.php';
$head[$h][1] = "Global";
$h++;

for ($hii = 0 ; $hii < sizeof($head) ; $hii++)
{
  if ($_SERVER["PHP_SELF"] == $head[$hii][0])
    {
      $hselected = $hii;
    }
}
?>
