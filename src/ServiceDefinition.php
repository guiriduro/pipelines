<?php

/* this file is part of pipelines */

namespace Ktomk\Pipelines;

use Ktomk\Pipelines\File\ParseException;
use Ktomk\Pipelines\File\Image;

class ServiceDefinition
{
    /**
     * @var File
     */
    private $file;
    /** 
     * @var Image
    */
    private $image;

    /**
     * ServiceDefinition constructor.
     * @param File $file
     * @param array $definition
     * @throws \Ktomk\Pipelines\File\ParseException
     */
    public function __construct(File $file, array $definition)
    {
        // quick validation
        if (!isset($definition['image'])) {
            ParseException::__("Service definitions require an image");
        }

        $this->file = $file;
    }
}
