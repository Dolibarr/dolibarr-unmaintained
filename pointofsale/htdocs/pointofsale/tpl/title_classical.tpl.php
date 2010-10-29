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
 *   	\file       pointofsale/tpl/title_classical.tpl.php
 *		\ingroup    pointofsale
 *		\brief      Template page to display a classical title zone for cashdesk
 *		\version    $Id: title_classical.tpl.php,v 1.1 2010/10/29 16:40:51 hregis Exp $
 *		\author		dmartin
 */

$date = dol_print_date(dol_now(), "%d/%m/%Y") ;

?>

<table width="100%" height="100%">
	<tr height="100%">
		<td width="33%" align="center"><a class="butAction" style="font-size:20px;padding:4% 20%;" href="<?php print DOL_URL_ROOT . '/pointofsale/cashdesk.php?action=newsale'?>"><?php print $langs->trans('NewSale') ; ?></a></td>
		<td width="33%" align="center"><a class="butAction" style="font-size:20px;padding:4% 20%;" href="<?php print DOL_URL_ROOT . '/pointofsale/index.php?action=deletelastsale' ; ?>"><?php print $langs->trans('Cancel') ; ?></a></td>
		<td align="center"><p style="font-size:18px;"> <?php print $langs->trans('SaleNumberAndDate', $this->ref, $date) ; ?><br /> Login : <?php print $_SESSION['dol_login'] ;?> </p></td>
	</tr>
</table>

<?php
