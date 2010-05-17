<?php
/* Copyright (C) 2009 Laurent Léonard <laurent@open-minds.org>
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

class VATList
{
	const MINIMUM_TURN_OVER = 250;
	const SEQUENCE_NUMBER_MAX = 9999;
	const SEQUENCE_NUMBER_MIN = 1;
	private $date;
	private $db;
	private $city;
	private $clientList;
	private $country;
	private $nameID;
	private $period;
	private $proposedNames;
	private $sequenceNumber;
	private $street;
	private $vatNumber;
	private $zipCode;
	
	function __construct($db)
	{
		$this->db = $db;
		
		$this->city = '';
		$this->country = '';
		$this->date = -1;
		$this->nameID = 0;
		$this->period = date('Y');
		$this->sequenceNumber = '';
		$this->street = '';
		$this->vatNumber = '';
		$this->zipCode = '';
	}
	
	private function addCompanyInfoXML($xml, $vat_number, $name = NULL, $street = NULL, $zip_code = NULL, $city = NULL, $country = NULL)
	{
		$xml->startElement('CompanyInfo');
		$xml->startElement('VATNum');
		$xml->text($this->formatVATNumberXML($vat_number));
		$xml->endElement();
		if (isset($name))
		{
			$xml->startElement('Name');
			$xml->text($name);
			$xml->endElement();
		}
		if (isset($street))
		{
			$xml->startElement('Street');
			$xml->text($street);
			$xml->endElement();
		}
		if (isset($city) && isset($zip_code))
		{
			$xml->startElement('CityAndZipCode');
			$xml->text($zip_code . ' ' . $city);
			$xml->endElement();
		}
		if (isset($country))
		{
			$xml->startElement('Country');
			$xml->text($country);
			$xml->endElement();
		}
		$xml->endElement();
	}
	
	private function formatAmountHTML($amount)
	{
		return sprintf('%.2f', $amount);
	}
	
	private function formatAmountXML($amount)
	{
		return $amount * 100;
	}
	
	private function formatControlReferenceXML($vat_number, $sequence_number)
	{
		return $this->formatVATNumberXML($vat_number) . sprintf('%04d', $sequence_number);
	}
	
	private function formatVATNumberXML($vat_number)
	{
		return substr($vat_number, 2);
	}
	
	private function getAmountTotal()
	{
		$total = 0;
		foreach ($this->clientsList as $client)
			$total += $client['amount'];
		return $total;
	}
	
	private function getTurnOverTotal()
	{
		$total = 0;
		foreach ($this->clientsList as $client)
			$total += $client['turn_over'];
		return $total;
	}
	
	private function getName()
	{
		return $this->proposedNames[$this->nameID];
	}
	
	function addProposedName($proposedName)
	{
		$this->proposedNames[] = $proposedName;
	}
	
	function fillClientsList()
	{
		$this->db->query('SELECT s.tva_intra, SUM(f.total) AS turn_over, SUM(f.tva) AS amount FROM ' . MAIN_DB_PREFIX . 'facture f, ' . MAIN_DB_PREFIX . 'societe s WHERE f.fk_soc = s.rowid AND f.fk_statut > 0 AND DATE_FORMAT(f.datef, \'%Y\') = \'' . addslashes($this->period) . '\' AND s.tva_assuj = 1 AND SUBSTRING(s.tva_intra, 1, 2) = \'BE\' GROUP BY s.tva_intra HAVING turn_over >= ' . VATList::MINIMUM_TURN_OVER);
		$this->clientsList = array();
		$sequence_number = 0;
		while ($client = $this->db->fetch_array())
		{
			$sequence_number++;
			$this->clientsList[] = array(
				'amount' => $client['amount'],
				'sequence_number' => $sequence_number,
				'turn_over' => $client['turn_over'],
				'vat_number' => $client['tva_intra']
			);
		}
	}
	
	function selectName($name_id)
	{
		global $langs;
		
		if (!is_numeric($name_id) || $name_id < 0 || $name_id > count($this->proposedNames) - 1)
			throw new Exception($langs->trans('ErrorBadNameID'));
		
		$this->nameID = $name_id;
	}
	
	function setCity($city)
	{
		$this->city = $city;
	}
	
	function setCountry($country)
	{
		$this->country = $country;
	}
	
	function setDate($day, $month, $year)
	{
		global $langs;
		
		if ($day == '' || $month == '' || $year == '')
			throw new Exception($langs->trans('ErrorFieldRequired', $langs->transnoentities('Date')));
		
		$date = mktime(0, 0, 0, $month, $day, $year);
		if (!$date)
			throw new Exception($langs->trans('ErrorBadDate'));
		
		$this->date = $date;
	}
	
	function setPeriod($period)
	{
		global $langs;
		
		if (!checkdate(1, 1, $period))
			throw new Exception($langs->trans('ErrorBadPeriod'));
		
		$this->period = $period;
	}
	
	function setSequenceNumber($sequence_number)
	{
		global $langs;
		
		if ($sequence_number == '')
			throw new Exception($langs->trans('ErrorFieldRequired', $langs->transnoentities('SequenceNumber')));
		
		if (!is_numeric($sequence_number) || $sequence_number < VATList::SEQUENCE_NUMBER_MIN || $sequence_number > VATList::SEQUENCE_NUMBER_MAX)
			throw new Exception($langs->trans('ErrorBadSequenceNumber', VATList::SEQUENCE_NUMBER_MIN, VATList::SEQUENCE_NUMBER_MAX));
		
		$this->sequenceNumber = $sequence_number;
	}
	
	function setStreet($street)
	{
		$this->street = $street;
	}
	
	function setVATNumber($vat_number)
	{
		$this->vatNumber = $vat_number;
	}
	
	function setZipCode($zip_code)
	{
		$this->zipCode = $zip_code;
	}
	
	function outputXML()
	{
		$xml = new XMLWriter();
		$xml->openMemory();
		$xml->startDocument('1.0', 'UTF-8');
		
		$xml->startElement('VatList');
		$xml->writeAttribute('xmlns', 'http://www.minfin.fgov.be/VatList');
		$xml->writeAttributeNS('xsi', 'schemaLocation', 'http://www.w3.org/2001/XMLSchema-instance', 'http://www.minfin.fgov.be/VatList http://www.minfin.fgov.be/portail1/fr/vatlist/VatList.xsd');
		$xml->writeAttribute('SenderId', $this->formatVATNumberXML($this->vatNumber));
		$xml->writeAttribute('ControlRef', $this->formatControlReferenceXML($this->vatNumber, $this->sequenceNumber));
		$xml->writeAttribute('SenderDate', date('Y-m-d', $this->date));
		
		$xml->startElement('AgentRepr');
		$xml->writeAttribute('DecNumber', '1');
		$this->addCompanyInfoXML($xml, $this->vatNumber, $this->getName(), $this->street, $this->zipCode, $this->city, $this->country);
		$xml->endElement();
		
		$xml->startElement('DeclarantList');
		$xml->writeAttribute('SequenceNum', '1');
		$xml->writeAttribute('DeclarantNum', $this->formatControlReferenceXML($this->vatNumber, $this->sequenceNumber) . '00000');
		$xml->writeAttribute('ClientNbr', count($this->clientsList));
		$xml->writeAttribute('TurnOverSum', $this->formatAmountXML($this->getTurnOverTotal()));
		$xml->writeAttribute('TaxSum', $this->formatAmountXML($this->getAmountTotal()));
		$this->addCompanyInfoXML($xml, $this->vatNumber, $this->getName(), $this->street, $this->zipCode, $this->city, $this->country);
		$xml->startElement('Period');
		$xml->text($this->period);
		$xml->endElement();
		foreach ($this->clientsList as $client)
		{
			$xml->startElement('ClientList');
			$xml->writeAttribute('SequenceNum', $client['sequence_number']);
			$this->addCompanyInfoXML($xml, $client['vat_number']);
			$xml->startElement('Amount');
			$xml->text($this->formatAmountXML($client['amount']));
			$xml->endElement();
			$xml->startElement('TurnOver');
			$xml->text($this->formatAmountXML($client['turn_over']));
			$xml->endElement();
			$xml->endElement();
		}
		$xml->endElement();
		
		$xml->endElement();
		
		return $xml->outputMemory(true);
	}
	
	function printHTML()
	{
		global $bc, $langs, $user;
		
		$form = new Form($this->db);
		echo '<form action="" method="post">';
		echo '<input type="hidden" name="action" value="export">';
		echo '<input type="hidden" name="year" value="' . $this->period . '">';
		
		echo '<table width="100%" class="border">';
		
		echo '<tr>';
		echo '<td>' . $langs->trans('DeclarantVATNumber') . '</td>';
		echo '<td>' . $this->vatNumber . '</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<td>' . $langs->trans('Period') . '</td>';
		echo '<td>' . $langs->trans('Year') . ' ' . $this->period . ' <a href="?year=' . ($this->period - 1) . '">' . img_previous() . '</a> <a href="?year=' . ($this->period + 1) . '">' . img_next() . '</a></td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<td>' . $langs->trans('DeclarantNameAndAddress') . '</td>';
		echo '<td>';
		$form->select_array('name_id', $this->proposedNames, $this->nameID);
		echo '<br />' . $this->street . '<br />' . $this->country . '-' . $this->zipCode . ' ' . $this->city . '</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<td>' . $langs->trans('Date') . '</td>';
		echo '<td>';
		$form->select_date($this->date, 'date');
		echo '</td>';
		echo '</tr>';
		
		if ($user->rights->belgium->vat_list->export)
		{
			echo '<tr>';
			echo '<td>' . $langs->trans('SequenceNumber') . '</td>';
			echo '<td><input maxlength="4" name="sequence_number" size="4" type="text" value="' . $this->sequenceNumber . '"></td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<td>' . $langs->trans('XMLExport') . '</td>';
			echo '<td><input type="submit" class="button" value="'.$langs->trans("Generate").'"></td>';
			echo '</tr>';
		}
		
		echo '</table>';
		
		echo '<br />';
		
		echo '<table class="noborder" width="100%">';
		
		echo '<tr class="liste_titre">';
		echo '<td>' . $langs->trans('#') . '</td>';
		echo '<td>' . $langs->trans('ClientVATNumber') . '</td>';
		echo '<td>' . $langs->trans('TurnOverVATExcluded') . '</td>';
		echo '<td>' . $langs->trans('VATAmount') . '</td>';
		echo '</tr>';
		
		$var = true;
		foreach ($this->clientsList as $client)
		{
			$var = !$var;
			echo '<tr ' . $bc[$var] . '>';
			echo '<td>' . $client['sequence_number'] . '</td>';
			echo '<td>' . $client['vat_number'] . '</td>';
			echo '<td>' . $this->formatAmountHTML($client['turn_over']) . '</td>';
			echo '<td>' . $this->formatAmountHTML($client['amount']) . '</td>';
			echo '</tr>';
		}
		
		echo '<tr class="liste_total">';
		echo '<td>' . $langs->trans('Total') . '</td>';
		echo '<td></td>';
		echo '<td>' . $this->formatAmountHTML($this->getTurnOverTotal()) . '</td>';
		echo '<td>' . $this->formatAmountHTML($this->getAmountTotal()) . '</td>';
		echo '</tr>';
		
		echo '</table>';
		
		echo '</form>';
	}
}

?>
