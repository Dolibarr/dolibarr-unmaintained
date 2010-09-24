<?php
/* Copyright (C) 2009-2010 Regis Houssin <regis@dolibarr.fr>
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
 * $Id: entity_admin.tpl.php,v 1.1 2010/09/24 16:33:32 hregis Exp $
 */
?>
 
<!-- BEGIN PHP TEMPLATE -->

<table class="noborder" width="100%">

<tr class="liste_titre">

   <td><?php echo $langs->trans('Label'); ?></td>
   <td align="left"><?php echo $langs->trans('Description'); ?></td>
   <td align="left"><?php echo $langs->trans('Name'); ?></td>
   <td align="left"><?php echo $langs->trans('Town'); ?></td>
   <td align="left"><?php echo $langs->trans('Country'); ?></td>
   <td align="left"><?php echo $langs->trans('Currency'); ?></td>
   <td align="center"><?php echo $langs->trans('Visible'); ?></td>
   <td align="center"><?php echo $langs->trans('Status'); ?></td>
   <td align="center" colspan="2">&nbsp;</td>
</tr>

<?php foreach ($this->tpl['entities'] as $entity) { ?>
<tr class="<?php echo $bc[$var]; ?>">
	<td><?php echo $entity['label']; ?></td>
	<td align="left"><?php echo $entity['description']; ?></td>
	<td align="left"><?php echo $entity['details']['MAIN_INFO_SOCIETE_NOM']; ?></td>
	<td align="left"><?php echo $entity['details']['MAIN_INFO_SOCIETE_VILLE']; ?></td>
	<td align="left"><?php echo $entity['details']['MAIN_INFO_SOCIETE_PAYS']; ?></td>
	<td align="left"><?php echo $entity['details']['MAIN_MONNAIE']; ?></td>
	
	<td align="center" width="30">
	<?php 
	if ($entity['visible']) {
		echo '<a href="'.$_SERVER["PHP_SELF"].'?id='.$entity['id'].'&amp;action=setvisible&amp;value=0">'.$this->tpl['img_on'].'</a>';
	} else {
		if ($entity['active']) {
			echo '<a href="'.$_SERVER["PHP_SELF"].'?id='.$entity['id'].'&amp;action=setvisible&amp;value=1">'.$this->tpl['img_off'].'</a>';
		} else {
			echo $this->tpl['img_off'];
		}
	}
	?>
	</td>
	
    <td align="center" width="30">
    <?php
    if ($entity['active']) {
    	echo '<a href="'.$_SERVER["PHP_SELF"].'?id='.$entity['id'].'&amp;action=setactive&amp;value=0">'.$this->tpl['img_on'].'</a>';
    } else {
    	echo '<a href="'.$_SERVER["PHP_SELF"].'?id='.$entity['id'].'&amp;action=setactive&amp;value=1">'.$this->tpl['img_off'].'</a>';
    }
    ?>
    </td>
    
    <td align="center" width="20">
		<?php echo '<a href="'.$_SERVER["PHP_SELF"].'?id='.$entity['id'].'&amp;action=edit">'.$this->tpl['img_modify'].'</a>'; ?>
	</td>
	<td align="center" width="20">
		<?php echo '<a href="'.$_SERVER["PHP_SELF"].'?id='.$entity['id'].'&amp;action=delete">'.$this->tpl['img_delete'].'</a>'; ?>
	</td>
</tr>
<?php } ?>

</table>

<div class="tabsAction">
<a class="butAction" href="<?php echo $_SERVER["PHP_SELF"]; ?>?action=create"><?php echo $langs->trans('AddEntity'); ?></a>
</div>

<!-- END PHP TEMPLATE -->