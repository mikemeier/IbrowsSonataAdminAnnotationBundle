<?php

namespace Ibrows\Bundle\SonataAdminAnnotationBundle\Annotation;

/**
 * @Annotation
 */
class FormMapper extends AbstractMapper implements FormInterface
{
    /**
     * @var array
     */
    public $options = array();

    /**
     * @var string
     */
    public $with = null;

    /**
     * @var array
     */
    public $withOptions = array();

    /**
     * @var bool
     */
    public $ignoreOnParent = false;

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return null|string
     */
    public function getWith()
    {
        return $this->with;
    }

    /**
     * @return array
     */
    public function getWithOptions()
    {
        return $this->withOptions;
    }

    /**
     * @return boolean
     */
    public function isIgnoreOnParent()
    {
        return $this->ignoreOnParent;
    }
}