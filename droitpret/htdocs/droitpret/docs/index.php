<?php
/* Copyright (C) 2007      Rodolphe Quiedeville <rodolphe@quiedeville.org>
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
 */

/**
 *  \file       htdocs/docs/index.php
 *  \ingroup    document
 *  \brief      Page d'accueil module document
 *  \version    $Id: index.php,v 1.2 2011/04/17 12:27:27 eldy Exp $
 */

require("../main.inc.php");


/*
 * 	View
 */

llxHeader();

print_titre($langs->trans("DocumentsBuilder"));

print "<table class=\"noborder\" width=\"100%\">\n";
print "<tr class=\"liste_titre\">\n";
print '  <td>'.$langs->trans("Name").'</td>';
print '  <td>'.$langs->trans("GenerationDate").'</td>';
print "</tr>\n";


$sql = "SELECT dg.rowid,dg.name, dg.date_generation as date_generation";
$sql.= " FROM ".MAIN_DB_PREFIX."document as dg";
$sql.=" ORDER BY dg.name ASC;";

$resql = $db->query($sql);
if ($resql)
{
  $var=True;
  while ($obj = $db->fetch_object($resql) )
    {
      $var=!$var;

      print "<tr $bc[$var]>";
      print '<td>';
      $loc = get_exdir($obj->rowid).$obj->rowid.".pdf";
      $file = stripslashes($obj->name);
      echo '<a href="'.DOL_URL_ROOT.'/document.php?modulepart=ged&attachment=1&file='.urlencode($loc).'">'.$file.'</a></td>';
      print '<td>'.dol_print_date($$db->jdate($obj->date_generation),'dayhour').'</td>';

      print "</tr>\n";
    }

    $db->free($resql);
}
else
{
  dol_print_error($db);
}



print '</table>';

$db->close();

llxFooter('$Date: 2011/04/17 12:27:27 $ - $Revision: 1.2 $');
?>
