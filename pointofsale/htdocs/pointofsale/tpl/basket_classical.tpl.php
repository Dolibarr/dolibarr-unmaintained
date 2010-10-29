<?php
/* Copyright (C) 2010 Denis Martin  <denimartin@hotmail.fr>
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
 *   	\file       htdocs/pointofsale/tpl/basket_classical.tpl.php
 *		\ingroup    pointofsale
 *		\brief      Template page for basket zone in the cashdesk page
 *		\version    $Id: basket_classical.tpl.php,v 1.1 2010/10/29 16:40:51 hregis Exp $
 *		\author		Denis Martin
 */



require_once(DOL_DOCUMENT_ROOT."/lib/functions.lib.php") ;

?>
<script type="text/javascript">

// Function to select/unselect lines in the basket table
function clickLine(line, classname) {
	var table = document.getElementById("basketTable") ;
	var i = 2 ;
	//unselect each row
	while(i < table.rows.length-3) {
		if(i%2 == 1) {
			table.rows[i].className = "impair" ;
		} else {
			table.rows[i].className = "pair" ;
		}
		i++ ;
	}
	//select clicked row (or unselect if already selected)
	if(classname != "selectedrow") {
		document.getElementById("radio"+line.id).checked = true ;
		line.className = "selectedrow" ;
	}
	else { document.getElementById("radio"+line.id).checked = false ; }
}

function clickPaiement(li, classname) {
	var list = document.getElementById("listPaiements") ;
	var i = 0 ;
	while(i < list.childNodes.length) {
		list.childNodes[i].className = "paiement" ;
		i++ ;
	}
	if(classname != "selectedpaiement") {
		document.getElementById("radio"+li.id).checked = true ;
		li.className = "selectedpaiement" ;
	}
	else { document.getElementById("radio"+li.id).checked = false ; }
}

</script>

<table border="solid" width="100%" height="100%" id="basketTable">

	<tr height="5%">
		<td align="center" colspan="4">
			<p style="font-size:17px;padding:0px;margin:0px;">Panier</p>
		</td>
	</tr>
	
	<tr height="5%">
		<td align="center">Qt&eacute;</td>
		<td width="70%" align="center">D&eacute;signation</td>
		<td align="center">P.U</td>
		<td align="center">Tot.</td>
	</tr>
	
<?php
$class = "pair" ;
$t = '    ' ;
$l = "\n" ;

foreach($this->lines as $basketDet) {
	print $t.'<tr class="'.$class.'" height="5%" id="l'.$basketDet->id.'" onclick="clickLine(this, this.className);">'.$l ;
	print $t.$t.'<td>'. $basketDet->qty .'</td>'.$l ;
	print $t.$t.'<td><input type="radio" style="display:none;" id="radiol'.$basketDet->id.'" name="selectedLine" value="'.$basketDet->id.'"/>'. $basketDet->product->ref .'</td>'.$l ;
	print $t.$t.'<td align="right">'. price($basketDet->product->price_ttc) .'&euro;</td>'.$l ;
	print $t.$t.'<td align="right">'. price($basketDet->total_ttc) .'&euro;</td>'.$l ;
	
	$class = $class == "pair" ? "impair" : "pair" ;
}
?>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>		
	
	<tr height="7%">
		<td colspan="3" align="right">Total TTC : &nbsp;<br />Dont TVA : &nbsp;</td>
		<td align="right"> <?php print price(round($this->total_ttc,2))."&euro;<br />".price(round($this->total_tva, 2))."&euro;" ; ?></td>
	</tr>
	
	<tr height="15%" valign="top">
		<td colspan="4" align="center" style="padding:0px;">
			<p>Paiements - reste <?php print price($this->total_ttc - $this->amount_paid) ;?> &euro; à payer :<br />
<?php
				if(count($this->paiements) == 0) {
					print "<i>Aucun paiement enregistr&eacute;</i>" ;
				}
				else {
					print '<ul class="listpaiements" id="listPaiements">'.$l ;
					foreach($this->paiements as $paiement) {
						print $t.'<li class="paiement" id="p'.$paiement->id.'" onclick="clickPaiement(this, this.className);">' ;
						print '<input type="radio" style="display:none;" id="radiop'.$paiement->id.'" name="selectedPaiement" value="'.$paiement->id.'"/>' ;
						print $paiement->toString()."</li>".$l ;
					}
					print "</ul>" ;
				}			
?>
			</p>
		</td>
	</tr>
</table>

<?php

