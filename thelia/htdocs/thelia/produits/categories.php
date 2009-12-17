<?php
/* Copyright (C) 2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2007 Jean Heimburger      <jean@tiaris.info>
 * Copyright (C) 2009      Jean-Francois FERRY    <jfefe@aternatik.fr>
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
 * $Id: categories.php,v 1.1 2009/12/17 14:57:00 hregis Exp $
 */

/**
    	\file       dev/skeletons/skeleton_page.php
		\ingroup    core
		\brief      Example of a php page
		\version    $Revision: 1.1 $
*/

require("./pre.inc.php");
require_once(DOL_DOCUMENT_ROOT."/categories/categorie.class.php");
require_once("thelia_categories.class.php");
//require_once(DOL_DOCUMENT_ROOT."/../dev/skeletons/skeleton_class.class.php");

// Load traductions files
$langs->load("companies");
$langs->load("other");


// Get parameters
$socid = isset($_GET["socid"])?$_GET["socid"]:'';

// Protection quand utilisateur externe
if ($user->societe_id > 0)
{
    $action = '';
    $socid = $user->societe_id;
}
//if ($socid == '') accessforbidden();



/*******************************************************************
* ACTIONS
*
* Put here all code to do according to value of "action" parameter
********************************************************************/

if ($_REQUEST["action"] == 'maj')
{
	if ($_POST["dolicat"]) $dolicatid = $_POST["dolicat"];
	if ($_POST["catMere"]) $dolicatid = $_POST["catMere"];


	$myobject=new Thelia_categorie($db);
	if ($myobject->fetch_dolicat($dolicatid) <0)
	{
		$mesg = "erreur dans fetch_dolicat";
	}
	elseif ($myobject->id > 0)
	{
		$myobject->dolicatid=$dolicatid;
		$myobject->theliacatid=$_POST["theliacat"];

		$result=$myobject->update($user);
		if ($result > 0)
		{
			// Creation OK
			$mesg="Update OK";
		}
		else
		{
			// Creation KO
			$mesg="Update KO ".$myobject->error;
		}

	}
	else
	{
		$myobject->dolicatid=$dolicatid;
		$myobject->theliacatid=$_POST["theliacat"];

		$result=$myobject->create($user);
		if ($result > 0)
		{
			// Creation OK
			$mesg="catégorie crée avec succès";
		}
		else
		{
			// Creation KO
			$mesg="Erreur dans la création de la catégorie ! ".$myobject->error;
		}
	}
//	$mesg.= " ### ".$_POST["dolicat"]." - " . $_POST["theliacat"]." - ".$_POST["catMere"]. "<br/>"."variable dolicat ".$dolicatid."<br/>";
}

else if ($_REQUEST["action"] == 'create')
{
	$categorie = new Categorie($db);

	$categorie->label          = $_POST["nom"];
	$categorie->description    = $_POST["description"];
	$categorie->visible        = $_POST["visible"];
	$categorie->type		   = $_POST["type"];
	if($_POST['catMere'] != "-1")
	{
		$mere = new	Thelia_categorie($db);
		$res = $mere->fetch_theliacat($_POST['catMere']);
      
      //echo $_POST['catMere']." <- POST ";
		if ($res == 1)
		{
			 $categorie->id_mere = $mere->dolicatid;
			 if (! $categorie->id_mere)
			 {
			 	$categorie->error = $langs->trans("ErrorNoParentCategory",$langs->transnoentities("Catmere"));
			 	$_GET["action"] = 'create';
				$mesg = "* catmerem ".$categorie->id_mere."* ".$_POST["nom"]. " * ".$_POST["description"]." * ".$_POST["visible"]." * ".$_POST["type"]." * ".$_POST['catMere'];
			 }
		}
		else
		{
			$categorie->error = $langs->trans("ErrorParent",$langs->transnoentities("Catmere"));
		 	$_GET["action"] = 'create';
			$mesg = "* catmerem ".$categorie->id_mere."* ".$_POST["nom"]. " * ".$_POST["description"]." * ".$_POST["visible"]." * ".$_POST["type"]." * ".$_POST['catMere'];
		}

	}
	else $categorie->id_mere = "-1";

	if (! $categorie->label)
	{
		$categorie->error = $langs->trans("ErrorFieldRequired",$langs->transnoentities("Ref"));
		$_GET["action"] = 'create';
		$mesg = "* ".$_POST["nom"]. " * ".$_POST["description"]." * ".$_POST["visible"]." * ".$_POST["type"]." * ".$_POST['catMere'];
	}
	else if (! $categorie->description)
	{
		$categorie->error = $langs->trans("ErrorFieldRequired",$langs->transnoentities("Description"));
		$_GET["action"] = 'create';
		$mesg = "* ".$_POST["nom"]. " * ".$_POST["description"]." * ".$_POST["visible"]." * ".$_POST["type"]." * ".$_POST['catMere'];
	}

	if ($categorie->error =="")
	{
		if ($cat_id = $categorie->create() > 0)
		{
			$_GET["action"] = 'confirmed';
			$_POST["addcat"] = '';
			$myobject=new Thelia_categorie($db);
			$myobject->dolicatid=$categorie->id;
			$mesg="cat_id recu ".$cat_id." categorie ".$categorie->id." ";
			$myobject->theliacatid=$_POST["theliacat"];

			$result=$myobject->create($user);
			if ($result > 0)
			{
				// Creation OK
				$mesg.="creation de ".$myobject->dolicatid.' - '.$myobject->theliacatid ;
			}
			else
			{
				// Creation KO
				$mesg.=$myobject->error;
			}
		}
	}
	$mesg .= ' sortie<br/>'.$categorie->error;
}

if ($_REQUEST["action"] == 'import')
{
	$theliacat = $_GET['catid'];

}

//echo $mesg;
/***************************************************
* PAGE
*
* Put here all code to  build page
****************************************************/

llxHeader();
$html=new Form($db);

if ($_REQUEST["action"] == 'import')
{
		//titre
		print '<table width="100%" class="noborder">';
		print '<tr class="liste_titre">';
		print '<td>Id</td><td>Label</td><td>Osc_id</td><td>Action</td>';
		print '</tr>'."\n";
		print '<tr>';
		print '<form method="post" action="categories.php">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<td><input name="theliacat" value="'.$_POST["catid"].'"></td><td>';
		print '<input type="hidden" name="action" value="maj"/>';
//   	print '<input type="hidden" name="dolicat" value="'.$obj->dolicatid.'"/>';
	   print $langs->trans("ChooseCategory").' ';
	   print $html->select_all_categories(0,$categorie->id_mere).' <input type="submit" name="doit" class="button" value="'.$langs->trans("Classify").'"></td>';
		print "</form>\n";
      
      print '<form method="post" action="categories.php">';
		print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		print '<input type="hidden" name="action" value="create"/>';
		print '<input type="hidden" name="nom" value="'.$_POST["nom"].'"/>';
		print '<input type="hidden" name="description" value="'.$_POST["description"].'"/>';
		print '<input type="hidden" name="visible" value="1"/>';
		$parent = -1;
		if ($_POST["catMere"] > 0) $parent = $_POST["catMere"];
		print '<input type="text" name="catMere" value="'.$parent.'"/>';
		print '<input type="hidden" name="type" value="0"/>';
		print '<input type="hidden" name="theliacat" value="'.$_POST["catid"].'"/>';
		print '<td><input type="submit" name="create" value="'.$langs->trans("create").'"></td>';
		print '</form>';
		print '</tr>';
		print "</table>\n";
}
else
{


	if ($mesg) print '<div class="ok">'.$mesg.'</div>';

	// Put here content of your page
	// ...
	if ($page == -1) { $page = 0 ; }
	$limit = $conf->liste_limit;
	$offset = $limit * $page ;

	$sql = "SELECT  c.label, c.rowid dolicatid, oc.theliacatid FROM ".MAIN_DB_PREFIX."categorie as c ";
	$sql .= "LEFT OUTER JOIN ".MAIN_DB_PREFIX."thelia_categories as oc ON oc.dolicatid = c.rowid ";
	$sql .= "WHERE c.visible = 1 AND c.type = 0";

	print_barre_liste("Correspondance des cat�gories", $page, "categories.php");

	dol_syslog("Osc_Categorie.class::get_Osccat sql=".$sql);
   $resql=$db->query($sql);
   if ($resql)
	{
	   $num = $db->num_rows($resql);
	   $i = 0;

		//titre
		print '<table width="100%" class="noborder">';
		print '<tr class="liste_titre">';
		print '<td>Id</td><td>Label</td><td>Osc_id</td><td>Action</td>';
		print '</tr>'."\n";

   	$var=true;
// $theliaid = 1;
    while ($i < min($num,$limit))
    {
         $obj = $db->fetch_object($resql);
         $var=!$var;
   	   print "\t<tr ".$bc[$var].">\n";
   	   print "\t\t<td><a href='../../categories/viewcat.php?id=".$obj->dolicatid."'>".$obj->dolicatid."</a></td>\n";
   	   print "\t\t<td><a href='../../categories/viewcat.php?id=".$obj->dolicatid."'>".$obj->label."</a></td>\n";
   	   print '<td><form action="categories.php" METHOD="POST">';
   	   print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
   	   print '<input type="text" size="5" name="theliacat" value="'.$obj->theliacatid.'"/></td>'."\n";
   	   print '<input type="hidden" name="action" value="maj"/>';
   	   print '<input type="hidden" name="dolicat" value="'.$obj->dolicatid.'"/>';
   	   print '<td align="center"><input type="submit" class="button" value="'.$langs->trans('maj').'"></td>';
   	   print "\t</tr></form>\n";
   	   $i++;
   	 }

		print '</table>';
	}
	else
	{
  		dol_print_error();
	}
}
	//WebService Client.
	require_once(NUSOAP_PATH."/nusoap.php");
	require_once("../includes/configure.php");

// Set the parameters to send to the WebService
if ($_GET["catid"]) $catid = $_GET["catid"];
else $catid= 0;
$parameters = array("catid"=>$catid);

// Set the WebService URL
$client = new nusoap_client(THELIA_DIR."ws_articles.php");
if ($client)
{
	$client->soap_defencoding='UTF-8';
}

$result = $client->call("get_categorylist",$parameters );

if ($client->fault) {
	if ($client->faultcode == 'Server') print '<p>Il n\'y a pas de cat�gorie fille pour la cat�gorie '.$catid.'</p>';
	else dol_print_error('',"erreur de connexion ".$client->getError());

}
elseif ( !($err = $client->getError()) )
	{
		$num=0;
  		if ($result) $num = sizeof($result);
		$var=True;
	  	$i=0;
		print '<br/>liste categories '.$catid.'<br/>';

  		if ($num > 0) {
			print "<TABLE width=\"100%\" class=\"noborder\">";
			print '<TR class="liste_titre">';
			print "<td>id</td>";
			print "<td>nom</td>";
			print "<td>parent</td>";
//		print '<td>desc</td>';
			print "<td>id dolibarr</td>";
			print "<td>Importer</td>";
			print "</tr>";

			$dolicat = new Thelia_Categorie($db);

			while ($i < $num) {
     			$var=!$var;
     			print "<tr $bc[$var]>";
     			print '<td><a href="categories.php?catid='.$result[$i]['id'].'">'.$result[$i]['id'].'</a></td>';
            print '<td><a href="categories.php?catid='.$result[$i]['id'].'">'.$result[$i]['titre'].'</a></td>';
     			print '<td>'.$result[$i]['parent'].'</td>';
				$dolicatid = $dolicat->fetch_theliacat($result[$i]['id']);
				if ($dolicat->dolicatid) print '<td><a href="../../categories/viewcat.php?id='.$dolicat->dolicatid.'">'.$dolicat->dolicatid.'</a></td>';
				else print '<td></td>';

     		//print '<td><a href="categories.php?action=import&catid='.$result[$i]['categories_id'].'">Importer</a></td>';
				print '<form method="POST" action="categories.php">';
				print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
            print '<input type="hidden" name="description" value="'.$result[$i]['chapo'].$result[$i]['description'].'"/>';
				print '<input type="hidden" name="nom" value="'.$result[$i]['titre'].'"/>';
				print '<input type="hidden" name="visible" value="1"/>';
				print '<input type="hidden" name="action" value="import"/>';
				print '<input type="hidden" name="catMere" value="'.$result[$i]['parent'].'"/>';
				print '<input type="hidden" name="catid" value="'.$result[$i]['id'].'"/>';
				print '<td align="center"><input type="submit" class="button" value="'.$langs->trans('Import').'"></td>';
				print '</form> ';
     			print "</tr>";
     			$i++;
     		}
   	}
	else print '<p>cleint : '.$client->getError().'</p>';

}

// End of page
$db->close();
llxFooter('$Date: 2009/12/17 14:57:00 $ - $Revision: 1.1 $');
?>
