<?php

namespace Ktomk\Pipelines;

use Ktomk\Pipelines\File\ParseException;
use PHPUnit\Framework\TestCase;

/**
 * Class ServiceDefinitionTest
 *
 * @covers \Ktomk\Pipelines\ServiceDefinition
 */
class ServiceDefinitionTest extends TestCase
{
    /**
     * Helper functions
     */
    public function goodValues()
    {
        $serviceDefArr = ['test' => ['image' => 'docker']];
        $fileDefArr = ['pipelines' => ['default' => []] /*, 
    'definitions' => ['services' => &$serviceDefArr]*/];

        return [$fileDefArr, $serviceDefArr];
    }

    public function badValuesEmpty()
    {
        /*$data = $this->goodValues();
        $badServiceDefArr = ['badEmpty' => []];

        $data[0]['definitions']['services'] = $badServiceDefArr;
        return [$data[0], $badServiceDefArr];*/
    }

    public function testCreation()
    {
        $data = $this->goodValues();
        $fileDefArr = $data[0];
        $fileDummy = new File($fileDefArr);

        $serviceDefArr = $data[1];
        $serviceDef = new ServiceDefinition($fileDummy, $serviceDefArr);
        $this->assertInstanceOf('Ktomk\Pipelines\ServiceDefinition', $serviceDef);
        $this->assertSame('test', $serviceDef->getLabel(), "Service label must be initialised as 'test' in this case.");
    }

    /**
     * @expectedException \Ktomk\Pipelines\File\ParseException
     * @expectedExceptionMessage Service definitions require an image
     */
    public function testParseErrorNoImage()
    {
        $data = $this->goodValues();
        $fileDummy = new File($data[0]);

        new ServiceDefinition($fileDummy, [/* empty */]);
    }

    public function testHasImage()
    {
        $data = $this->goodValues();
        $fileDummy = new File($data[0]);

        $serviceDef = new ServiceDefinition($fileDummy, $data[1]);
        $this->assertInstanceOf('Ktomk\Pipelines\File\Image', $serviceDef->getImage());
    }

}