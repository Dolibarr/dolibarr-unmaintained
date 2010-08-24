<?php


/**
*    \brief      Calcule la clef d'un numero ISBN
*    \param      isbn        Clef International Standard Book Number
*    \note       source http://fr.wikipedia.org/wiki/ISBN
*/
function calculate_isbn_key($isbn)
{
    $sum = 0;
	for ($i = 0 ; $i < 9 ; $i++)
	{
		$sum += $isbn{$i} * (10 - $i);
	}

	$key = 11 - ($sum % 11);

	if ($key == 0) $key = 1;

	if ($key == 11) $key = 'X';

	return $key;
}

/**
*    \brief      Calcule la clef d'un numero EAN 13
*    \param      ean        Clef EAN
*    \note       source http://fr.wikipedia.org/wiki/ISBN
*/
function calculate_ean_key($ean)
{
	$sum = 0;
	for ($i = 0 ; $i < 12 ; $i = $i+2)
	{
		$sum += $ean{$i};
	}
	
	for ($i = 1 ; $i < 12 ; $i = $i+2)
	{
		$sum += 3 * $ean{$i};
	}

	$key = (10 - ($sum % 10));

	return $key;
}

/**
*    \brief      Conversion code bare
*    \param      typeIn type en entrée
*    \param      typeOut type en sortie
*    \param      value code bare
*    \return     retourne le code barre sour le format typeOut
*/
function convert_code_barre($typeIn,$typeOut,$value)
{
	
	$fonction = "convert_".$typeIn."_to_".$typeOut;

	return $fonction($value);

}

/**
*    \brief      Conversion code bare de isbn à isbn13
*    \param      isbn
*    \return     retourne isbn13
*/
function convert_isbn_to_isbn13($isbn)
{
	$isbn_array = explode("-",$isbn);

	$isbn13 = "978".'-'.$isbn_array[0].'-'.$isbn_array[1].'-'.$isbn_array[2];

	$isbn13_key = calculate_ean_key("978".$isbn_array[10].$isbn_array[1].$isbn_array[2]);
	$isbn13.= '-'.$isbn13_key;
	
	return $isbn13;
}

/**
*    \brief      Conversion code bare de isbn à ean13
*    \param      isbn
*    \return     retourne ean13
*/
function convert_isbn_to_ean13($isbn)
{
	$isbn_array = explode("-",$isbn);

	$ean13 = "978".$isbn_array[0].$isbn_array[1].$isbn_array[2];
	$ean13_key = calculate_ean_key($ean13);
	$ean13.= $ean13_key;
	
	return $ean13;	
}

/**
*    \brief      Conversion code bare de isbn13 à isbn
*    \param      isbn13
*    \return     retourne isbn
*/
function convert_isbn13_to_isbn($isbn13)
{
	$isbn13_array = explode("-",$isbn13);
	$isbn = $isbn13_array[1].'-'.$isbn13_array[2].'-'.$isbn13_array[3].calculate_isbn_key($isbn13_array[1].$isbn13_array[2].$isbn13_array[3]);
	
	return $isbn;
}

/**
*    \brief      Conversion code bare de isbn13 à ean13
*    \param      isbn13
*    \return     retourne ean13
*/
function convert_isbn13_to_ean13($isbn13)
{
	$ean13 = str_replace("-","",$isbn13);
	
	return $ean13;
}

/**
*    \brief      Conversion code bare de ean13 à isbn
*    \param      ean13
*    \return     retourne isbn
*/
function convert_ean13_to_isbn($ean13)
{
	$code = substr($ean13,3,dol_strlen($ean13));
	$groupSize = getSizeGroupISBN($code);
	$isbn = substr($code,0,$groupSize)."-";
	
	//Pas terminé
	
}

/**
*    \brief      Conversion code bare de ean13 à isbn13
*    \param      ean13
*    \return     retourne isbn13
*/
function convert_ean13_to_isbn13($ean13)
{
	//Pas terminé
}

/**
*    \brief      Retourne le Group Identifier du code ISBN
*    \param      code
*    \return     Group Identifier
*/
function getGroupISBN($code)
{
	$codeSize[1] = array(1,2,3,4,5,7);
	$codeSize[2] = array(8);
	$codeSize[3] = array(6);
	$codeSize9[2] = array(0,1,2,3);
	$codeSize9[3] = array(5,6,7,8);
	$codeSize9[4] = array(4,5,6,7,8);
	
	if(in_array($code{1},$codeSize[1]))
	{
		return substr($code,0,1);
	}
	else if(in_array($code{1},$codeSize[2]))
	{
		return substr($code,0,2);
	}
	else if(in_array($code{1},$codeSize[3]))
	{
		return substr($code,0,3);
	}
	elseif($code{1} == 9)
	{
		if(in_array($code{2},$codeSize9[2]))
		{
			return substr($code,0,2);
		}
		else if(in_array($code{2},$codeSize9[3]))
		{
			return substr($code,0,3);
		}
		elseif($ocde{2} == 9)
		{
			if(in_array($code{3},$codeSize9[4]))
			{
				return substr($code,0,4);
			}
			else if(in_array($code{3},$codeSize9[5]))
			{
				return substr($code,0,5);
			}
		}
	}
	
	return -1;
}

function getSizeGroupISBN($code)
{
	return dol_strlen(getGroupISBN($code));
}




















?>