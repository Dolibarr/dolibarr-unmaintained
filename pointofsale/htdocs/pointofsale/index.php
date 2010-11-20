<?php
/* Copyright (C) 2007-2010 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2010 Denis Martin	<denimartin@hotmail.fr>
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
 *   	\file       htdocs/pointofsale/index.php
 *		\ingroup    pointofsale
 *		\brief      Main page of the PointOfSale module
 *		\version    $Id: index.php,v 1.2 2010/11/20 13:50:43 denismartin Exp $
 *		\author		Denis Martin
 *		\remarks	Pages that will display "new sale" link and stats about pointofsale
 */

//if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
//if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');
//if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1');
//if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
//if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined("NOLOGIN"))        define("NOLOGIN",'1');

require("../main.inc.php");

// Load traductions files requiredby by page
$langs->load("companies");
$langs->load("products") ;
$langs->load("other");

$langs->load("@pointofsale") ;

// Get parameters
$action = isset($_GET["action"])?$_GET["action"]:'';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($_REQUEST["action"] == "deletelastsale")
{
	require_once(DOL_DOCUMENT_ROOT."/pointofsale/class/cashdesk.class.php");
	$cashdesk = new Cashdesk($db, $user) ;
	$cashdesk->dumpBasket($user) ;
}




//Check if there is a sale started for this user. If yes, we will display the "continue last sale" button
$saleinprogress = false ;
$sql = "SELECT rowid FROM ".MAIN_DB_PREFIX."basket WHERE fk_user='".$user->login."'" ;
dol_syslog("pointofsale/index.php sql=".$sql, LOG_DEBUG);
$resql=$db->query($sql);
if ($resql) {
	if ($db->num_rows($resql) == 1) {
		$saleinprogress = true ;
	}
	$db->free($resql);
}
else {
	$error="Error ".$db->lasterror();
	dol_syslog("pointofsale/index.php ".$error, LOG_ERR);
	//TODO Error display
}




/***************************************************
* PAGE
*
* Put here all code to build page
****************************************************/

llxHeader('',$langs->trans('PointOfSale'),'');

print_fiche_titre($langs->trans('PointOfSaleArea')) ;

$form=new Form($db);

// Put here content of your page
// ...
?>
<!-- Main div -->
<div style="height:500px; background-color:#EEEEEE;">


<div align="center" style="height:10%;padding-top:2%">
	<?php if($saleinprogress) print '<a class="butAction" style="padding:1% 5% 1% 5%;font-size:20px" href="'.DOL_URL_ROOT.'/pointofsale/cashdesk.php">'.$langs->trans("ContinueLastSale").'</a>' ;?>	
	<a class="butAction" style="padding:1% 5% 1% 5%;font-size:20px" href="<?php print DOL_URL_ROOT . '/pointofsale/cashdesk.php?action=newsale' ;?>"> <?php print $langs->trans('NewSale') ;?></a>
</div>

<table border="0" class="notopnoleftnoright" width="100%">
	<tr>
		<td valign="top" width="50%" class="notopnoleft">
			<table class="noborder" width="100%">
				<tr class="liste_titre">
					<td>Last Sales (Sales of the Month)
						<a class="butAction" href="<?php print DOL_URL_ROOT.'/pointofsale/monthsales.php' ;?>">MonthSales</a>
					</td>
				</tr>
	
			</table>
		</td>
		<td valign="top" width="50%" class="notopnoleft">
			<table class="noborder" width="100%">
				<tr class="liste_titre">
					<td>Stats</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<!-- End of main div -->
</div>


<?php


/***************************************************
* LINKED OBJECT BLOCK
*
* Put here code to view linked object
****************************************************/
/*$myobject->load_object_linked($myobject->id,$myobject->element);
				
foreach($myobject->linked_object as $object => $objectid)
{
	if($conf->$object->enabled)
	{
		$somethingshown=$myobject->showLinkedObjectBlock($object,$objectid,$somethingshown);
	}
}*/

// End of page
$db->close();
llxFooter('$Date: 2010/11/20 13:50:43 $ - $Revision: 1.2 $');
?>
