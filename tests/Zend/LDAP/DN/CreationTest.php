<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_LDAP
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\LDAP\Dn;
use Zend\LDAP;

/**
 * Test helper
 */
/**
 * Zend_LDAP_Dn
 */

/**
 * @category   Zend
 * @package    Zend_LDAP
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_LDAP
 * @group      Zend_LDAP_Dn
 */
class CreationTest extends \PHPUnit_Framework_TestCase
{
    public function testDnCreation()
    {
        LDAP\DN::setDefaultCaseFold(LDAP\DN::ATTR_CASEFOLD_NONE);

        $dnString1='CN=Baker\\, Alice,CN=Users+OU=Lab,DC=example,DC=com';
        $dnArray1=array(
            array('CN' => 'Baker, Alice'),
            array('CN' => 'Users', 'OU' => 'Lab'),
            array('DC' => 'example'),
            array('DC' => 'com'));

        $dnString2='cn=Baker\\, Alice,cn=Users+ou=Lab,dc=example,dc=com';
        $dnArray2=array(
            array('cn' => 'Baker, Alice'),
            array('cn' => 'Users', 'ou' => 'Lab'),
            array('dc' => 'example'),
            array('dc' => 'com'));

        $dnString3='Cn=Baker\\, Alice,Cn=Users+Ou=Lab,Dc=example,Dc=com';
        $dnArray3=array(
            array('Cn' => 'Baker, Alice'),
            array('Cn' => 'Users', 'Ou' => 'Lab'),
            array('Dc' => 'example'),
            array('Dc' => 'com'));

        $dn11=LDAP\DN::fromString($dnString1);
        $dn12=LDAP\DN::fromArray($dnArray1);
        $dn13=LDAP\DN::factory($dnString1);
        $dn14=LDAP\DN::factory($dnArray1);

        $this->assertEquals($dn11, $dn12);
        $this->assertEquals($dn11, $dn13);
        $this->assertEquals($dn11, $dn14);

        $this->assertEquals($dnString1, $dn11->toString());
        $this->assertEquals($dnString1, $dn11->toString(LDAP\DN::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnString2, $dn11->toString(LDAP\DN::ATTR_CASEFOLD_LOWER));
        $this->assertEquals($dnArray1, $dn11->toArray());
        $this->assertEquals($dnArray1, $dn11->toArray(LDAP\DN::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnArray2, $dn11->toArray(LDAP\DN::ATTR_CASEFOLD_LOWER));

        $dn21=LDAP\DN::fromString($dnString2);
        $dn22=LDAP\DN::fromArray($dnArray2);
        $dn23=LDAP\DN::factory($dnString2);
        $dn24=LDAP\DN::factory($dnArray2);

        $this->assertEquals($dn21, $dn22);
        $this->assertEquals($dn21, $dn23);
        $this->assertEquals($dn21, $dn24);

        $this->assertEquals($dnString2, $dn21->toString());
        $this->assertEquals($dnString1, $dn21->toString(LDAP\DN::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnString2, $dn21->toString(LDAP\DN::ATTR_CASEFOLD_LOWER));
        $this->assertEquals($dnArray2, $dn21->toArray());
        $this->assertEquals($dnArray1, $dn21->toArray(LDAP\DN::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnArray2, $dn21->toArray(LDAP\DN::ATTR_CASEFOLD_LOWER));
        $this->assertEquals($dnArray2, $dn22->toArray());

        $dn31=LDAP\DN::fromString($dnString3);
        $dn32=LDAP\DN::fromArray($dnArray3);
        $dn33=LDAP\DN::factory($dnString3);
        $dn34=LDAP\DN::factory($dnArray3);

        $this->assertEquals($dn31, $dn32);
        $this->assertEquals($dn31, $dn33);
        $this->assertEquals($dn31, $dn34);

        $this->assertEquals($dnString3, $dn31->toString());
        $this->assertEquals($dnString1, $dn31->toString(LDAP\DN::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnString2, $dn31->toString(LDAP\DN::ATTR_CASEFOLD_LOWER));
        $this->assertEquals($dnArray3, $dn31->toArray());
        $this->assertEquals($dnArray1, $dn31->toArray(LDAP\DN::ATTR_CASEFOLD_UPPER));
        $this->assertEquals($dnArray2, $dn31->toArray(LDAP\DN::ATTR_CASEFOLD_LOWER));

        try {
            $dn=LDAP\DN::factory(1);
            $this->fail('Expected Zend_LDAP_Exception not thrown');
        } catch (LDAP\Exception $e) {
            $this->assertEquals('Invalid argument type for $dn', $e->getMessage());
        }
    }

    public function testDnCreationWithDifferentCaseFoldings()
    {
        LDAP\DN::setDefaultCaseFold(LDAP\DN::ATTR_CASEFOLD_NONE);

        $dnString1='Cn=Baker\\, Alice,Cn=Users+Ou=Lab,Dc=example,Dc=com';
        $dnString2='CN=Baker\\, Alice,CN=Users+OU=Lab,DC=example,DC=com';
        $dnString3='cn=Baker\\, Alice,cn=Users+ou=Lab,dc=example,dc=com';

        $dn=LDAP\DN::fromString($dnString1, null);
        $this->assertEquals($dnString1, (string)$dn);
        $dn->setCaseFold(LDAP\DN::ATTR_CASEFOLD_UPPER);
        $this->assertEquals($dnString2, (string)$dn);
        $dn->setCaseFold(LDAP\DN::ATTR_CASEFOLD_LOWER);
        $this->assertEquals($dnString3, (string)$dn);

        $dn=LDAP\DN::fromString($dnString1, LDAP\DN::ATTR_CASEFOLD_UPPER);
        $this->assertEquals($dnString2, (string)$dn);
        $dn->setCaseFold(null);
        $this->assertEquals($dnString1, (string)$dn);
        $dn->setCaseFold(LDAP\DN::ATTR_CASEFOLD_LOWER);
        $this->assertEquals($dnString3, (string)$dn);

        $dn=LDAP\DN::fromString($dnString1, LDAP\DN::ATTR_CASEFOLD_LOWER);
        $this->assertEquals($dnString3, (string)$dn);
        $dn->setCaseFold(LDAP\DN::ATTR_CASEFOLD_UPPER);
        $this->assertEquals($dnString2, (string)$dn);
        $dn->setCaseFold(LDAP\DN::ATTR_CASEFOLD_LOWER);
        $this->assertEquals($dnString3, (string)$dn);
        $dn->setCaseFold(LDAP\DN::ATTR_CASEFOLD_UPPER);
        $this->assertEquals($dnString2, (string)$dn);

        LDAP\DN::setDefaultCaseFold(LDAP\DN::ATTR_CASEFOLD_UPPER);
        $dn=LDAP\DN::fromString($dnString1, null);
        $this->assertEquals($dnString2, (string)$dn);

        LDAP\DN::setDefaultCaseFold(null);
        $dn=LDAP\DN::fromString($dnString1, null);
        $this->assertEquals($dnString1, (string)$dn);

        LDAP\DN::setDefaultCaseFold(LDAP\DN::ATTR_CASEFOLD_NONE);
    }

    public function testGetRdn()
    {
        LDAP\DN::setDefaultCaseFold(LDAP\DN::ATTR_CASEFOLD_NONE);

        $dnString='cn=Baker\\, Alice,cn=Users,dc=example,dc=com';
        $dn=LDAP\DN::fromString($dnString);

        $this->assertEquals(array('cn' => 'Baker, Alice'), $dn->getRdn());
        $this->assertEquals('cn=Baker\\, Alice', $dn->getRdnString());

        $dnString = 'Cn=Users+Ou=Lab,dc=example,dc=com';
        $dn=LDAP\DN::fromString($dnString);
        $this->assertEquals(array('Cn' => 'Users', 'Ou' => 'Lab'), $dn->getRdn());
        $this->assertEquals('Cn=Users+Ou=Lab', $dn->getRdnString());
    }

    public function testGetParentDn()
    {
        $dnString='cn=Baker\\, Alice,cn=Users,dc=example,dc=com';
        $dn=LDAP\DN::fromString($dnString);

        $this->assertEquals('cn=Users,dc=example,dc=com', $dn->getParentDn()->toString());
        $this->assertEquals('cn=Users,dc=example,dc=com', $dn->getParentDn(1)->toString());
        $this->assertEquals('dc=example,dc=com', $dn->getParentDn(2)->toString());
        $this->assertEquals('dc=com', $dn->getParentDn(3)->toString());

        try {
            $dn->getParentDn(0)->toString();
            $this->fail('Expected Zend_LDAP_Exception not thrown');
        } catch (LDAP\Exception $e) {
            $this->assertEquals('Cannot retrieve parent DN with given $levelUp', $e->getMessage());
        }
        try {
            $dn->getParentDn(4)->toString();
            $this->fail('Expected Zend_LDAP_Exception not thrown');
        } catch (LDAP\Exception $e) {
            $this->assertEquals('Cannot retrieve parent DN with given $levelUp', $e->getMessage());
        }
    }

    public function testEmptyStringDn()
    {
        $dnString='';
        $dn=LDAP\DN::fromString($dnString);

        $this->assertEquals($dnString, $dn->toString());
    }
}
