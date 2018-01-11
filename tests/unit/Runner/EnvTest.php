<?php

/* this file is part of pipelines */

namespace Ktomk\Pipelines\Runner;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Ktomk\Pipelines\Runner\Env
 */
class EnvTest extends TestCase
{
    function testCreation()
    {
        $env = new Env();
        $this->assertInstanceOf('Ktomk\Pipelines\Runner\Env', $env);
        $this->assertNotNull($env->getArgs('-e'));

        $env = Env::create();
        $this->assertInstanceOf('Ktomk\Pipelines\Runner\Env', $env);
        $this->assertNotNull($env->getArgs('-e'));
    }

    function testDefaultEnvsEmptyUnlessInitialized()
    {
        $env = new Env();
        $array = $env->getArgs('-e');
        $this->assertInternalType('array', $array);
        $this->assertCount(0, $array);

        $env->initDefaultVars(array());
        $array = $env->getArgs('-e');
        $this->assertCount(10, $array);
    }

    function testUserInheritance()
    {
        $user = 'adele';
        $env = Env::create(array('USER' => $user));
        $this->assertNull($env->getValue('USER'));
        $this->assertSame($user, $env->getValue('BITBUCKET_REPO_OWNER'));
    }

    function testInheritionOnInit()
    {
        $env = new Env();
        $env->initDefaultVars(array('BITBUCKET_BUILD_NUMBER' => '123'));
        $array = $env->getArgs('-e');
        $this->assertTrue(in_array('BITBUCKET_BUILD_NUMBER=123', $array, true));
    }

    function testGetOptionArgs()
    {
        $env = Env::create();
        $args = $env->getArgs('-e');
        $this->assertInternalType('array', $args);
        while ($args) {
            $argument = array_pop($args);
            $this->assertInternalType('string', $argument);
            $this->assertGreaterThan(0, strpos($argument, '='), 'must be a variable definition');
            $this->assertGreaterThan(0, count($args));
            $option = array_pop($args);
            $this->assertSame('-e', $option);
        }
    }

    function testUnsetVariables()
    {
        $env = new Env();
        $env->initDefaultVars(array());
        # start count has some vars unset
        $default = count($env->getArgs('-e'));

        $env->initDefaultVars(array('BITBUCKET_BRANCH' => 'test'));
        # start count has some vars unset
        $new = count($env->getArgs('-e'));
        $this->assertEquals($default + 2, $new);
    }

    function testAddRefType()
    {
        $env = Env::create();
        $default = count($env->getArgs('-e'));

        $env->addReference(Reference::create());
        $this->assertSame($default, count($env->getArgs('-e')), 'null refrence does not add any variables');

        $env->addReference(Reference::create('branch:testing'));
        $this->assertSame($default + 2, count($env->getArgs('-e')), 'full refrence does add variables');
    }

    function testAddRefTypeIfSet()
    {
        $env = Env::create(array('BITBUCKET_TAG' => 'inherit'));
        $default = count($env->getArgs('-e'));

        $env->addReference(Reference::create('tag:testing'));
        $actual = $env->getArgs('-e');
        $this->assertCount($default, $actual);

        $this->assertTrue(in_array('BITBUCKET_TAG=inherit', $actual, true));
    }

    function testSetContainerName()
    {
        $env = Env::create();
        $count = count($env->getArgs('-e'));

        $env->setContainerName('blue-seldom');
        $args = $env->getArgs('-e');
        $this->assertCount($count + 2, $args);
        $this->assertTrue(in_array('PIPELINES_CONTAINER_NAME=blue-seldom', $args, true));


        $env->setContainerName('solar-bottom');
        $args = $env->getArgs('-e');
        $this->assertCount($count + 4, $args);
        $this->assertTrue(in_array('PIPELINES_PARENT_CONTAINER_NAME=blue-seldom', $args, true));
        $this->assertTrue(in_array('PIPELINES_CONTAINER_NAME=solar-bottom', $args, true));
    }

    function testInheritedContainerName()
    {
        $inherit = array(
            'PIPELINES_CONTAINER_NAME' => 'cloud-sea',
        );
        $env = Env::create($inherit);
        $env->setContainerName('dream-blue');
        $args = $env->getArgs('-e');
        $this->assertTrue(in_array('PIPELINES_PARENT_CONTAINER_NAME=cloud-sea', $args, true));
        $this->assertTrue(in_array('PIPELINES_CONTAINER_NAME=dream-blue', $args, true));
    }

    function testGetVar()
    {
        $env = Env::create();
        $actual = $env->getValue('BITBUCKET_BUILD_NUMBER');
        $this->assertSame('0', $actual);
        $actual = $env->getValue('BITBUCKET_BRANCH');
        $this->assertNull($actual);
    }

    function testSetPipelinesId()
    {
        $env = Env::create();
        $this->assertNull($env->getValue('PIPELINES_ID'));
        $this->assertNull($env->getValue('PIPELINES_IDS'));

        // set the first id
        $result = $env->setPipelinesId('default');
        $this->assertFalse($result);
        $this->assertSame('default', $env->getValue('PIPELINES_ID'));

        // set the second id (next run)
        $result = $env->setPipelinesId('default');
        $this->assertTrue($result);
        $actual = $env->getValue('PIPELINES_IDS');
        $this->assertNotNull($actual);
        $this->assertRegExp('~^([a-z0-9]+) \1$~', $actual, 'list of hashes');
    }

    function testInheritPipelinesId()
    {
        $inherit = array('PIPELINES_ID', 'custom/the-other-day');
        $env = Env::create($inherit);
        $this->assertNull($env->getValue('PIPELINES_ID'));
    }
}