<?php

namespace Ibrows\Bundle\SonataAdminAnnotationBundle\Annotation;

interface FormInterface extends AdminInterface
{
    /**
     * @return array
     */
    public function getOptions();
}