<?php
declare(encoding='iso-8859-15');
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 autoindent: */
/**
 * Numbers_Words class extension to spell numbers in German.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Numbers
 * @package   Numbers_Words
 * @author    Christian Weiske <cweiske@php.net>
 * @copyright 1997-2008 The PHP Group
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version   CVS: $Id: GermanTest.php,v 1.1 2009/08/25 20:50:39 eldy Exp $
 * @link      http://pear.php.net/package/Numbers_Words
 * @since     File available only in CVS
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Numbers_Words_GermanTest::main');
}

require_once 'Numbers/Words.php';
require_once 'PHPUnit/Framework.php';

class Numbers_Words_GermanTest extends PHPUnit_Framework_TestCase
{
    var $handle;

    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        PHPUnit_TextUI_TestRunner::run(
            new PHPUnit_Framework_TestSuite('Numbers_Words_GermanTest')
        );
    }

    function setUp()
    {
        $this->handle = new Numbers_Words();
    }

    /**
     * Testing numbers between 0 and 9
     */
    function testDigits()
    {
        $digits = array('null',
                        'eins',
                        'zwei',
                        'drei',
                        'vier',
                        'f�nf',
                        'sechs',
                        'sieben',
                        'acht',
                        'neun'
                       );
        for ($i = 0; $i < 10; $i++) {
            $number = $this->handle->toWords($i, 'de');
            $this->assertEquals($digits[$i], $number);
        }
    }

    /**
     * Testing numbers between 10 and 99
     */
    function testTens()
    {
        $tens = array(11 => 'elf',
                      12 => 'zw�lf',
                      16 => 'sechzehn',
                      19 => 'neunzehn',
                      20 => 'zwanzig',
                      21 => 'einundzwanzig',
                      26 => 'sechsundzwanzig',
                      30 => 'drei�ig',
                      31 => 'einunddrei�ig',
                      40 => 'vierzig',
                      43 => 'dreiundvierzig',
                      50 => 'f�nfzig',
                      55 => 'f�nfundf�nfzig',
                      60 => 'sechzig',
                      67 => 'siebenundsechzig',
                      70 => 'siebzig',
                      79 => 'neunundsiebzig'
                     );
        foreach ($tens as $number => $word) {
            $this->assertEquals($word, $this->handle->toWords($number, 'de'));
        }
    }

    /**
     * Testing numbers between 100 and 999
     */
    function testHundreds()
    {
        $hundreds = array(100 => 'einhundert',
                          101 => 'einhunderteins',
                          199 => 'einhundertneunundneunzig',
                          203 => 'zweihundertdrei',
                          287 => 'zweihundertsiebenundachtzig',
                          300 => 'dreihundert',
                          356 => 'dreihundertsechsundf�nfzig',
                          410 => 'vierhundertzehn',
                          434 => 'vierhundertvierunddrei�ig',
                          578 => 'f�nfhundertachtundsiebzig',
                          689 => 'sechshundertneunundachtzig',
                          729 => 'siebenhundertneunundzwanzig',
                          894 => 'achthundertvierundneunzig',
                          999 => 'neunhundertneunundneunzig'
                         );
        foreach ($hundreds as $number => $word) {
            $this->assertEquals($word, $this->handle->toWords($number, 'de'));
        }
    }

    /**
     * Testing numbers between 1000 and 9999
     */
    function testThousands()
    {
        $thousands = array(1000 => 'eintausend',
                           1001 => 'eintausendeins',
                           1097 => 'eintausendsiebenundneunzig',
                           1104 => 'eintausendeinhundertvier',
                           1243 => 'eintausendzweihundertdreiundvierzig',
                           2385 => 'zweitausenddreihundertf�nfundachtzig',
                           3766 => 'dreitausendsiebenhundertsechsundsechzig',
                           4196 => 'viertausendeinhundertsechsundneunzig',
                           5846 => 'f�nftausendachthundertsechsundvierzig',
                           6459 => 'sechstausendvierhundertneunundf�nfzig',
                           7232 => 'siebentausendzweihundertzweiunddrei�ig',
                           8569 => 'achttausendf�nfhundertneunundsechzig',
                           9539 => 'neuntausendf�nfhundertneununddrei�ig'
                          );
        foreach ($thousands as $number => $word) {
            $this->assertEquals($word, $this->handle->toWords($number, 'de'));
        }
    }
}

if (PHPUnit_MAIN_METHOD == 'Numbers_Words_GermanTest::main') {
    Numbers_Words_GermanTest::main();
}
?>
