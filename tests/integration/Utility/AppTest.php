<?php

/* this file is part of pipelines */

namespace Ktomk\Pipelines\Integration\Utility;

use Ktomk\Pipelines\Cli\Streams;
use Ktomk\Pipelines\Utility\App;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ktomk\Pipelines\Utility\App
 */
class AppTest extends TestCase
{
    function provideArguments()
    {
        return array(
            array(array('--version')),
            array(array('--help')),
            array(array('--show')),
            array(array('--images')),
            array(array('--list')),
            array(array('--dry-run')),
            array(array('--verbose', '--dry-run')),
            array(array('--keep', '--no-run')),
            array(array('--docker-list', '--dry-run')),
        );
    }

    /**
     * @param array $arguments
     * @dataProvider provideArguments
     */
    function testSuccessfulCommands(array $arguments)
    {
        $app = new App(new Streams());
        $args = array_merge((array)'pipelines-test', $arguments);
        $status = $app->main($args);
        $this->assertSame(0, $status);
    }

    function testInvalidPrefixGivesError()
    {
        $app = new App(new Streams(null, null, 'php://output'));
        $this->expectOutputString("Invalid prefix: '!\$\"'\n");
        $args = array(
            'pipelines-test',
            '--prefix',
            '!$"',
        );
        $status = $app->main($args);
        $this->assertSame(1, $status);
    }

    function testEmptyBasenameGivesError()
    {
        $app = new App(new Streams(null, null, 'php://output'));
        $this->expectOutputString("Empty basename\n");
        $args = array(
            'pipelines-test',
            '--basename',
            '',
        );
        $status = $app->main($args);
        $this->assertSame(1, $status);
    }

    function testFileOverridesBasenameVerbose()
    {
        $app = new App(new Streams(null, 'php://output'));
        $this->expectOutputRegex(
            "{^pipelines version (@\.@\.@|[a-f0-9]{7}|\d+\.\d+\.\d+)(-\d+-g[a-f0-9]{7})?\+?\n" .
            "info: --file overrides non-default --basename\n}"
        );
        $args = array(
            'pipelines-test',
            '--verbose',
            '--file',
            'super.yml',
            '--basename',
            'my.yml',
            '--no-run',
        );
        $status = $app->main($args);
        $this->assertSame(1, $status);
    }

    function testNonReadableFilename()
    {
        $app = new App(new Streams(null, null, 'php://output'));
        $this->expectOutputString(
            "Not a readable file: " .
            "/rooter/home/galore/not/found/super.yml\n"
        );
        $args = array(
            'pipelines-test',
            '--file',
            '/rooter/home/galore/not/found/super.yml',
            '--no-run',
        );
        $status = $app->main($args);
        $this->assertSame(1, $status);
    }

    function testUnknownOption()
    {
        $app = new App(new Streams(null, null, 'php://output'));
        $this->expectOutputString(
            "Unknown option: --for-the-fish-thank-you\n"
        );
        $args = array(
            'pipelines-test',
            '--for-the-fish-thank-you',
        );
        $status = $app->main($args);
        $this->assertSame(1, $status);
    }

    function testInvalidWrongPipelineNameArgumentException()
    {
        $this->expectOutputString(
            "Pipeline 'test/more' unavailable\n"
        );
        $app = new App(new Streams(null, null, 'php://output'));
        $args = array(
            'pipelines-test',
            '--debug', '--pipeline', 'test/more'
        );
        $status = $app->main($args);
        $this->assertSame(1, $status);
    }

    function testCopyDeployMode()
    {
        $app = new App(new Streams(null, null, null));
        $args = array(
            'pipelines-test',
            '--deploy', 'copy', '--dry-run'
        );
        $status = $app->main($args);
        $this->assertSame(0, $status);
    }
}