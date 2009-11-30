<?php
/* Copyright (C) 2009 Marc STUDER  <dev@garstud.com>
 *
 * Licensed under the GNU GPL v3 or higher (See file gpl-3.0.html)
 */

/**
    	\file       htdocs/speedfinder/formsearch.php
		\ingroup    speedfinder
		\brief      Main speedfinder area page
		\version    $Id: formsearch.php,v 1.1 2009/11/30 22:37:40 hregis Exp $
		\author		Marc STUDER
*/

include("./pre.inc.php");

// Load traductions files
$langs->load("speedfinder");

$CURRWIDTH=empty($conf->global->SFINDER_WIDTH)?'500':$conf->global->SFINDER_WIDTH;
$SEARCH_AUTOFOCUS=empty($conf->global->SFINDER_AUTOFOCUS)?0:$conf->global->SFINDER_AUTOFOCUS;
$SEARCH_TXTSELECT=empty($conf->global->SFINDER_TXTSELECT)?"":"onClick='this.select()'";
$SEARCH_TXTSIZE=empty($conf->global->SFINDER_TXTSIZE)?12:$conf->global->SFINDER_TXTSIZE;
$SEARCH_NBCAR=empty($conf->global->SFINDER_NBCAR)?2:$conf->global->SFINDER_NBCAR;

?>

<html>
<head>
	<script type="text/javascript" src="livesearch.js"></script>
	<style type="text/css">
	#livesearch {  margin:1px; padding:5px; width:<?php echo $CURRWIDTH-40 ?>px; font-size:12px; color:#444; line-height:20px  }
	#livesearch A {  text-decoration:none; color:#446  }
	#livesearch A:hover {  color:#000; background-color: #b6c9d0  }
	#txt1  {  margin:0px;  }
	.desc_label { padding:5px; font-size:10px; color:#444; }
	</style>
	<title>SpeedFinder - Dolibarr</title>
</head>

<body>

    <form name="fSFinder">
	
	<input type="text" id="txtSpeedSearch" name="txtSpeedSearch" size="<?php echo $SEARCH_TXTSIZE ?>" onkeyup="showResult(this.value)" autocomplete="off" <?php echo $SEARCH_TXTSELECT ?>/>

<?php 
	$pluriel=($SEARCH_NBCAR>1)?"s":"";
	print "	<span class='desc_label'>".$langs->trans("SFinderSearchLimit",$SEARCH_NBCAR, $pluriel)."</span>";
?>

	<div id="livesearch"></div>

    </form>

<?php
	if ($SEARCH_AUTOFOCUS==1) { ?>
    <script language="JavaScript">
	// selectionne automatiquement la textbox a l'affichage de l'ecran
	document.fSFinder.txtSpeedSearch.focus();
    </script>
<?php	}	
?>
</body>
</html> 
