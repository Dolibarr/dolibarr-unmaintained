<?php
declare(encoding='windows-1251');
/* vim: set expandtab tabstop=4 shiftwidth=4: */
//
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Kouber Saparev                                              |
// +----------------------------------------------------------------------+
//
// Numbers_Words class extension to spell numbers in Bulgarian.
//
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Numbers_Words_BulgarianTest::main');
}

require_once 'Numbers/Words.php';
require_once 'PHPUnit/Framework.php';

class Numbers_Words_BulgarianTest extends PHPUnit_Framework_TestCase
{
    var $handle;

    public static function main()
    {
        require_once 'PHPUnit/TextUI/TestRunner.php';
        PHPUnit_TextUI_TestRunner::run(
            new PHPUnit_Framework_TestSuite('Numbers_Words_BulgarianTest')
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
        $digits = array('����',
                        '����',
                        '���',
                        '���',
                        '������',
                        '���',
                        '����',
                        '�����',
                        '����',
                        '�����'
                       );
        for ($i = 0; $i < 10; $i++) {
            $number = $this->handle->toWords($i, 'bg');
            $this->assertEquals($digits[$i], $number);
        }
    }

    /**
     * Testing numbers between 10 and 99
     */
    function testTens()
    {
        $tens = array(11 => '����������',
                      12 => '����������',
                      16 => '�����������',
                      19 => '������������',
                      20 => '��������',
                      21 => '�������� � ����',
                      26 => '�������� � ����',
                      30 => '��������',
                      31 => '�������� � ����',
                      40 => '�����������',
                      43 => '����������� � ���',
                      50 => '��������',
                      55 => '�������� � ���',
                      60 => '���������',
                      67 => '��������� � �����',
                      70 => '����������',
                      79 => '���������� � �����'
                     );
        foreach ($tens as $number => $word) {
            $this->assertEquals($word, $this->handle->toWords($number, 'bg'));
        }
    }

    /**
     * Testing numbers between 100 and 999
     */
    function testHundreds()
    {
        $hundreds = array(100 => '���',
                          101 => '��� � ����',
                          199 => '��� ���������� � �����',
                          203 => '������ � ���',
                          287 => '������ ��������� � �����',
                          300 => '������',
                          356 => '������ �������� � ����',
                          410 => '������������ � �����',
                          434 => '������������ �������� � ������',
                          578 => '��������� ���������� � ����',
                          689 => '���������� ��������� � �����',
                          729 => '����������� �������� � �����',
                          894 => '���������� ���������� � ������',
                          999 => '����������� ���������� � �����'
                         );
        foreach ($hundreds as $number => $word) {
            $this->assertEquals($word, $this->handle->toWords($number, 'bg'));
        }
    }

    /**
     * Testing numbers between 1000 and 9999
     */
    function testThousands()
    {
        $thousands = array(1000 => '������',
                           1001 => '������ � ����',
                           1097 => '������ � ���������� � �����',
                           1104 => '������ ��� � ������',
                           1243 => '������ ������ ����������� � ���',
                           2385 => '��� ������ ������ ��������� � ���',
                           3766 => '��� ������ ����������� ��������� � ����',
                           4196 => '������ ������ ��� ���������� � ����',
                           5846 => '��� ������ ���������� ����������� � ����',
                           6459 => '���� ������ ������������ �������� � �����',
                           7232 => '����� ������ ������ �������� � ���',
                           8569 => '���� ������ ��������� ��������� � �����',
                           9539 => '����� ������ ��������� �������� � �����'
                          );
        foreach ($thousands as $number => $word) {
            $this->assertEquals($word, $this->handle->toWords($number, 'bg'));
        }
    }
}

if (PHPUnit_MAIN_METHOD == 'Numbers_Words_BulgarianTest::main') {
    Numbers_Words_BulgarianTest::main();
}
?>