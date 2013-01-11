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
    public $options = array('by_reference' => false);

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}