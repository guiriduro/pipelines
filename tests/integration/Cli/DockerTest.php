<?php

/* this file is part of pipelines */

namespace Ktomk\Pipelines\Integration\Cli;

use Ktomk\Pipelines\Cli\Docker;
use Ktomk\Pipelines\Cli\Exec;
use PHPUnit\Framework\TestCase;

/**
 * Class ProcTest
 *
 * @coversNothing
 */
class DockerTest extends TestCase
{
    public function testCreation()
    {
        $exec = new Exec();
        $docker = new Docker($exec);
        $this->assertInstanceOf('Ktomk\Pipelines\Cli\Docker', $docker);
    }

    public function testHasCommand()
    {
        $exec = new Exec();
        $docker = new Docker($exec);
        $actual = $docker->hasCommand();
        $this->assertInternalType('bool', $actual);

        return $actual;
    }

    public function testVersion()
    {
        $exec = new Exec();
        $docker = new Docker($exec);

        $version = $docker->getVersion();
        if (null === $version) {
            $this->assertNull($version);
        } else {
            $this->assertInternalType('string', $version);
        }
    }
}
