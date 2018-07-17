<?php

/* this file is part of pipelines */

namespace Ktomk\Pipelines;

use Ktomk\Pipelines\File\ParseException;
use Ktomk\Pipelines\File\Image;

class ServiceDefinition
{
    /**
     * @var File file parent
     */
    private $file;
    /** 
     * @var Image docker image for this service
    */
    private $image;
    /**
     * @var string
     */
    private $label;

    /**
     * ServiceDefinition constructor.
     * @param File $file
     * @param array $definition
     * @throws \Ktomk\Pipelines\File\ParseException
     */
    public function __construct(File $file, array $definition)
    {
        $label = $this->label = array_keys($definition)[0];
        // quick validation
        if (!isset($definition[$label]['image'])) {
            ParseException::__("Service definitions require an image");
        }
        $this->image = new Image($definition[$label]['image']);
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
    }
}
