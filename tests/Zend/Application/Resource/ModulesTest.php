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
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Resource_ModulesTest::main');
}

/**
 * Test helper
 */

/**
 * Zend_Loader_Autoloader
 */

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class Zend_Application_Resource_ModulesTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        Zend_Loader_Autoloader::resetInstance();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();

        $this->application = new Zend_Application('testing');

        $this->bootstrap = new ZfAppBootstrap($this->application);

        $this->front = Zend_Controller_Front::getInstance();
        $this->front->resetInstance();
    }

    public function tearDown()
    {
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Reset autoloader instance so it doesn't affect other tests
        Zend_Loader_Autoloader::resetInstance();
    }

    public function testInitializationTriggersNothingIfNoModulesRegistered()
    {

        $this->bootstrap->registerPluginResource('Frontcontroller', array());
        $resource = new Zend_Application_Resource_Modules(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertFalse(isset($this->bootstrap->default));
        $this->assertFalse(isset($this->bootstrap->foo));
        $this->assertFalse(isset($this->bootstrap->bar));
    }

    /**
     * @group ZF-6803
     * @group ZF-7158
     */
    public function testInitializationTriggersDefaultModuleBootstrapWhenDiffersFromApplicationBootstrap()
    {

        $this->bootstrap->registerPluginResource('Frontcontroller', array(
            'moduleDirectory' => dirname(__FILE__) . '/../_files/modules',
        ));
        $resource = new Zend_Application_Resource_Modules(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertTrue(isset($this->bootstrap->default));
    }

    public function testInitializationShouldTriggerModuleBootstrapsWhenTheyExist()
    {

        $this->bootstrap->registerPluginResource('Frontcontroller', array(
            'moduleDirectory' => dirname(__FILE__) . '/../_files/modules',
        ));
        $resource = new Zend_Application_Resource_Modules(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertTrue($this->bootstrap->foo, 'foo failed');
        $this->assertTrue($this->bootstrap->bar, 'bar failed');
    }

    /**
     * @group ZF-6803
     * @group ZF-7158
     */
    public function testInitializationShouldSkipModulesWithoutBootstraps()
    {

        $this->bootstrap->registerPluginResource('Frontcontroller', array(
            'moduleDirectory' => dirname(__FILE__) . '/../_files/modules',
        ));
        $resource = new Zend_Application_Resource_Modules(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $bootstraps = $resource->getExecutedBootstraps();
        $this->assertEquals(4, count((array)$bootstraps));
        $this->assertArrayHasKey('bar',     (array)$bootstraps);
        $this->assertArrayHasKey('foo-bar', (array)$bootstraps);
        $this->assertArrayHasKey('foo',     (array)$bootstraps);
        $this->assertArrayHasKey('default', (array)$bootstraps);
    }

    /**
     * @group ZF-6803
     * @group ZF-7158
     */
    public function testShouldReturnExecutedBootstrapsWhenComplete()
    {

        $this->bootstrap->registerPluginResource('Frontcontroller', array(
            'moduleDirectory' => dirname(__FILE__) . '/../_files/modules',
        ));
        $resource = new Zend_Application_Resource_Modules(array());
        $resource->setBootstrap($this->bootstrap);
        $bootstraps = $resource->init();
        $this->assertEquals(4, count((array)$bootstraps));
        $this->assertArrayHasKey('bar',     (array)$bootstraps);
        $this->assertArrayHasKey('foo-bar', (array)$bootstraps);
        $this->assertArrayHasKey('foo',     (array)$bootstraps);
        $this->assertArrayHasKey('default', (array)$bootstraps);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Resource_ModulesTest::main') {
    Zend_Application_Resource_ModulesTest::main();
}
