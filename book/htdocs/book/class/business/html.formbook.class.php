<?php
/* Copyright (C) 2010      Auguria		   <contact@auguria.net>
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
 *	\file       htdocs/book/class/business/html.formbook.class.php
 *	\brief      Fichier de la classe des fonctions predefinie de composants html pour le module Book
 *	\version	$Id: html.formbook.class.php,v 1.2 2010/06/07 14:16:32 pit Exp $
 */


/**
 *	\class      Formbook
 *	\brief      Classe permettant la generation de composants html pour le module Book
 *	\remarks	Only common components must be here.
 */
class Formbook extends Form
{
	var $cache_paper_format=array();
	var $cache_parcel=array();
	
	/**
     *      \brief      Charge dans cache la liste des formats de papier
     *      \return     int             Nb lignes chargées, 0 si déjà chargées, <0 si ko
     */
    function load_cache_paper_format()
    {
        global $langs;

       if (sizeof($this->cache_paper_format)) return 0;    // Cache déja chargé
        

        $sql = "SELECT rowid, code, label, width, height, unit";
        $sql.= " FROM ".MAIN_DB_PREFIX."c_paper_format";
        $sql.= " WHERE active = 1";
        $sql.= " ORDER by code";
        
        dolibarr_syslog('Form::paper_format_array sql='.$sql,LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $num = $this->db->num_rows($resql);
            $i = 0;

            while ($i < $num)
            {
                $obj = $this->db->fetch_object($resql);
            	
            	$this->cache_paper_format[$obj->rowid]['code'] = $obj->code;
            	$this->cache_paper_format[$obj->rowid]['label'] = $obj->label;
            	$this->cache_paper_format[$obj->rowid]['width'] = $obj->width;
            	$this->cache_paper_format[$obj->rowid]['height'] = $obj->height;
            	$this->cache_paper_format[$obj->rowid]['unit'] = $obj->unit;
            	$i++;

            }
            return $i;
            
        }
        else {
			dolibarr_print_error($this->db);
			return -1;
        }
    }
    
    /**
     *      \brief      Charge dans cache la liste des types de colis
     *      \return     int             Nb lignes chargées, 0 si déjà chargées, <0 si ko
     */
    function load_cache_parcel()
    {
        global $langs;

       if (sizeof($this->cache_parcel)) return 0;    // Cache déja chargé
        

        $sql = "SELECT rowid, code, label, width, height, length, unit, maxweight, weightunit";
        $sql.= " FROM ".MAIN_DB_PREFIX."c_parcel";
        $sql.= " WHERE active = 1";
        $sql.= " ORDER by code";
        
        dolibarr_syslog('Form::parcel_array sql='.$sql,LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            $num = $this->db->num_rows($resql);
            $i = 0;

            while ($i < $num)
            {
                $obj = $this->db->fetch_object($resql);
            	
            	$this->cache_parcel[$obj->rowid]['code'] = $obj->code;
            	$this->cache_parcel[$obj->rowid]['label'] = $obj->label;
            	$this->cache_parcel[$obj->rowid]['width'] = $obj->width;
            	$this->cache_parcel[$obj->rowid]['height'] = $obj->height;
            	$this->cache_parcel[$obj->rowid]['length'] = $obj->length;
            	$this->cache_parcel[$obj->rowid]['unit'] = $obj->unit;
            	$this->cache_parcel[$obj->rowid]['maxweight'] = $obj->maxweight;
            	$this->cache_parcel[$obj->rowid]['weightunit'] = $obj->weightunit;
            	$i++;

            }
            return $i;
            
        }
        else {
			dolibarr_print_error($this->db);
			return -1;
        }
    }

/**
     *      \brief      Retourne la liste des modes de paiements possibles
     *      \param      selected        Id du paper format pré-sélectionné
     *      \param      htmlname        Nom de la zone select
     *      \param      format          array()
     *      \param      empty			1=peut etre vide, 0 sinon
     */
    function select_paper_format($selected='',$htmlname='paper_format',$format='', $empty=0)
    {
        global $langs,$user;
		
		dolibarr_syslog("Form::select_paper_format $selected, $htmlname, $format",LOG_DEBUG);
        
		$this->load_cache_paper_format();


        print '<select class="flat" name="'.$htmlname.'">';
		if ($empty) print '<option value="">&nbsp;</option>';
		
		
	
		
        foreach($this->cache_paper_format as $id => $arrayformat)
        {

            print '<option value="'.$id.'"';

            // Si selected est text, on compare avec code, sinon avec id
            if ($selected == $id) print ' selected="true"';
            print '>';
            
			
            if ($format == '') $value=$arrayformat['code'];
            else
            {
            	$value_array = array();
            	foreach($format as $field)
            	{
            		$value_array[]=$arrayformat[$field];
            	}
            	$value = implode(' ',$value_array);
            }

            print $value.'</option>';
        }
        print '</select>';

    }
    
    
    /**
     *      \brief      Retourne la liste des types de colis
     *      \param      selected        Id du type de colis pré-sélectionné
     *      \param      htmlname        Nom de la zone select
     *      \param      format          array()
     *      \param      empty			1=peut etre vide, 0 sinon
     */
    function select_parcel($selected='',$htmlname='parcel',$format='', $empty=0)
    {
        global $langs,$user;
		
		dolibarr_syslog("Form::select_parcel $selected, $htmlname, $format",LOG_DEBUG);
        
		$this->load_cache_parcel();


        print '<select class="flat" name="'.$htmlname.'">';
		if ($empty) print '<option value="">&nbsp;</option>';
		
		
	
		
        foreach($this->cache_parcel as $id => $arrayformat)
        {

            print '<option value="'.$id.'"';

            // Si selected est text, on compare avec code, sinon avec id
            if ($selected == $id) print ' selected="true"';
            print '>';
            
			
            if ($format == '') $value=$arrayformat['code'];
            else
            {
            	$value_array = array();
            	foreach($format as $field)
            	{
            		$value_array[]=$arrayformat[$field];
            	}
            	$value = implode(' ',$value_array);
            }

            print $value.'</option>';
        }
        print '</select>';

    }

}

?>
