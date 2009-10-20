<?PHP
/* Copyright (C) 2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 * $Id: tools.lib.php,v 1.1 2009/10/20 16:19:30 eldy Exp $
 * $Source: /cvsroot/dolibarr/dolibarrmod/telephonie/htdocs/telephonie/stats/tools.lib.php,v $
 *
 */
function stat_year_bar($year)
{
  $cyear = strftime("%Y",time());
  $x = 2005;
  $string = '';

  $query = array();
  $query = explode("&",$_SERVER["QUERY_STRING"]);

  $qs = '';
  foreach($query as $q)
    {
      if (substr($q,0,4) <> 'year')
	{
	  $qs .= '&amp;'.$q;
	}
    }

  while ($x <= $cyear)
    {
      if ($x == $year)
	{
	  $string .= $x.'&nbsp;|&nbsp;';
	}
      else
	{
	  $string .= '<a href="'.$_SERVER["SCRIPT_URI"].'?year='.$x;
	  if ($qs)
	    {
	      $string .=$qs;
	    }
	  $string .= '">'.$x.'</a>&nbsp;|&nbsp;';
	}
      $x++;
    }
  print substr($string,0,strlen($string) - 13);
}
?>
