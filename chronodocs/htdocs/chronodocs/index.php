<?php
/* Copyright (C) 2002-2003 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2006 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2007 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2008 	   Raphael Bertrand (Resultic)  <raphael.bertrand@resultic.fr>
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
     	\file       htdocs/chronodocs/index.php
		\brief      Page accueil espace fiches chronodocs
		\ingroup    chronodocs
		\version    $Id: index.php,v 1.3 2010/04/28 07:56:22 grandoc Exp $
*/

require("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_entries.class.php");
require_once(DOL_DOCUMENT_ROOT."/chronodocs/chronodocs_types.class.php");
require_once(DOL_DOCUMENT_ROOT."/lib/date.lib.php");

$langs->load("companies");
$langs->load("chronodocs");

$sortorder=$_GET["sortorder"]?$_GET["sortorder"]:$_POST["sortorder"];
$sortfield=$_GET["sortfield"]?$_GET["sortfield"]:$_POST["sortfield"];
$socid=$_GET["socid"]?$_GET["socid"]:$_POST["socid"];
$page=$_GET["page"]?$_GET["page"]:$_POST["page"];

// Security check
$chronodocsid = isset($_GET["id"])?$_GET["id"]:'';
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'chronodocs', $chronodocsid, 'chronodocs_entries','entries','socid');

if (! $sortorder) $sortorder="DESC";
if (! $sortfield) $sortfield="f.date_c";
if ($page == -1) { $page = 0 ; }

$limit = $conf->liste_limit;
$offset = $limit * $page ;
$pageprev = $page - 1;
$pagenext = $page + 1;

  if (!empty($_REQUEST["year"]))
	$year = $_REQUEST["year"];
  else 
	$year = 0;
	
  if (!empty($_REQUEST["month"]))
    $month = $_REQUEST["month"];
  else 
	$month = "";

  if (!empty($_GET['search_ref']))
	$search_ref = $_GET['search_ref'];  
  else
    $search_ref = "";

  if (!empty($_GET['search_societe']))
	$search_societe = $_GET['search_societe'];  
  else
    $search_societe = "";

  if (!empty($_GET['search_title']))
	$search_title = $_GET['search_title'];
  else
	$search_title = "";
  
/*
*	View
*/

llxHeader();

$chronodocs_static=new Chronodocs_entries($db);
$result=$chronodocs_static->get_list($limit,$offset,$sortfield,$sortorder,$socid,$search_ref,$search_societe,$search_title,$year,$month);
if (is_array($result))
{
    $num = sizeOf($result);
	if($num>0 && $socid>0 && is_object($result[0]) &&!empty($result[0]->nom))
		$search_societe=$result[0]->nom; // (pre)set search_societe field with soc name if socid set
	
	$urlparam="&amp;socid=$socid";
    print_barre_liste($langs->trans("ListOfChronodocs"), $page, "index.php",$urlparam,$sortfield,$sortorder,'',$num);

    $i = 0;
    print '<table class="noborder" width="100%">';
    print "<tr class=\"liste_titre\">";
    print_liste_field_titre($langs->trans("Ref"),"index.php","f.ref","",$urlparam,'width="15%"',$sortfield,$sortorder);
    print_liste_field_titre($langs->trans("Company"),"index.php","s.nom","",$urlparam,'',$sortfield,$sortorder);
    print '<td>'.$langs->trans("Title").'</td>';
    print_liste_field_titre($langs->trans("Date"),"index.php","f.date_c","",$urlparam,'align="center"',$sortfield);
	print_liste_field_titre($langs->trans("DateU"),"index.php","f.date_u","",$urlparam,'align="center"',$sortfield);
    print "</tr>\n";
	
	// Lignes des champs de filtre
	$html = new Form($db);
	
	print '<form method="get" action="'.$_SERVER["PHP_SELF"].'">';

	print '<tr class="liste_titre">';
	print '<td valign="right">';
	print '<input class="flat" size="24" type="text" name="search_ref" value="'.$search_ref.'">';
	print '</td>';
	print '<td align="left">';
	print '<input class="flat" type="text" size="30" name="search_societe" value="'.$search_societe.'">';
	print '</td>';
	print '<td align="left">';
	print '<input class="flat" type="text" size="40" name="search_title" value="'.$search_title.'">';
	print '</td>';
	print '<td class="liste_titre" colspan="1" align="right">';
	print $langs->trans('Month').': <input class="flat" type="text" size="2" maxlength="2" name="month" value="'.$month.'">';
	print '&nbsp;'.$langs->trans('Year').': ';
	$max_year = date("Y");
	$syear = $year;
	if($syear == '')
		$syear = date("Y");
	$html->select_year($syear,'year',1, '', $max_year);
	print '</td>';
	print '<td align="right"><input class="liste_titre" type="image" src="'.DOL_URL_ROOT.'/theme/'.$conf->theme.'/img/search.png" alt="'.$langs->trans("Search").'">';
	print '</td>';
	print "</tr>\n";
	print '</form>';
	
    $var=True;
    $total = 0;
    while ($i < min($num, $limit))
    {
        $objp = array_shift($result);
        $var=!$var;
        print "<tr $bc[$var]>";
        print "<td><a href=\"fiche.php?id=".$objp->fichid."\">".img_object($langs->trans("Show"),"task").' '.$objp->ref."</a></td>\n";
        print '<td><a href="'.DOL_URL_ROOT.'/comm/fiche.php?socid='.$objp->socid.'">'.img_object($langs->trans("ShowCompany"),"company").' '.dolibarr_trunc($objp->nom,44)."</a></td>\n";
        print '<td>'.nl2br($objp->title).'</td>';
        print '<td align="center">'.dolibarr_print_date($objp->dp)."</td>\n";
		print '<td align="center">'.dolibarr_print_date($objp->date_u)."</td>\n";

        print "</tr>\n";
        $i++;
    }

    print '</table>';
	
	/**
	 * Barre d'actions
	 *
	 */
	print '<div class="tabsAction">';

	if ($user->societe_id == 0 && $_GET["action"] != 'editAll')
	{
		// Create
		if ($chronodocstype->statut == 0 && $user->rights->chronodocs->entries->write)
		{
			print '<a class="butActionDelete" ';
			print 'href=fiche.php?action=create';
			print '>'.$langs->trans('Create').'</a>';
		}
	}

	print '</div>';
}
else
{
    dolibarr_print_error($db);
}

$db->close();

llxFooter('$Date: 2010/04/28 07:56:22 $ - $Revision: 1.3 $');
?>
