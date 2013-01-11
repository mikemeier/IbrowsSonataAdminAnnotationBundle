<?php

namespace Ibrows\Bundle\SonataAdminAnnotationBundle\Reader;

use Ibrows\Bundle\SonataAdminAnnotationBundle\Annotation\ListInterface;
use Ibrows\Bundle\SonataAdminAnnotationBundle\Annotation\ListMapper;

use Ibrows\Bundle\SonataAdminAnnotationBundle\Annotation\ShowInterface;
use Ibrows\Bundle\SonataAdminAnnotationBundle\Annotation\ShowMapper;

use Ibrows\Bundle\SonataAdminAnnotationBundle\Annotation\FormInterface;
use Ibrows\Bundle\SonataAdminAnnotationBundle\Annotation\FormMapper;

use Ibrows\Bundle\SonataAdminAnnotationBundle\Annotation\DatagridInterface;
use Ibrows\Bundle\SonataAdminAnnotationBundle\Annotation\DatagridMapper;

use Sonata\AdminBundle\Datagrid\ListMapper as SonataListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper as SonataDatagridMapper;
use Sonata\AdminBundle\Form\FormMapper as SonataFormMapper;
use Sonata\AdminBundle\Show\ShowMapper as SonataShowMapper;

class SonataAdminAnnotationReader extends AbstractAnnotationReader implements SonataAdminAnnotationReaderInterface
{
    /**
     * @param mixed $entity
     * @return ListInterface[]
     */
    public function getListMapperAnnotations($entity)
    {
        $listAnnotations = $this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_LIST, self::SCOPE_PROPERTY);
        if(!$this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_LIST_ALL, self::SCOPE_CLASS)){
            return $listAnnotations;
        }

        $listExcludeAnnotations = $this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_LIST_EXCLUDE, self::SCOPE_PROPERTY);

        return $this->collectAllAnnotations(
            $listAnnotations,
            $listExcludeAnnotations,
            $this->getReflectionProperties($entity),
            new ListMapper()
        );
    }

    /**
     * @param mixed $entity
     * @return ShowInterface[]
     */
    public function getShowMapperAnnotations($entity)
    {
        $showAnnotations = $this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_SHOW, self::SCOPE_PROPERTY);
        if(!$this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_SHOW_ALL, self::SCOPE_CLASS)){
            return $showAnnotations;
        }

        $showExcludeAnnotations = $this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_SHOW_EXCLUDE, self::SCOPE_PROPERTY);

        return $this->collectAllAnnotations(
            $showAnnotations,
            $showExcludeAnnotations,
            $this->getReflectionProperties($entity),
            new ShowMapper()
        );
    }

    /**
     * @param mixed $entity
     * @return FormInterface[]
     */
    public function getFormMapperAnnotations($entity)
    {
        $formAnnotations = $this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_FORM, self::SCOPE_PROPERTY);
        if(!$this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_FORM_ALL, self::SCOPE_CLASS)){
            return $formAnnotations;
        }

        $formExcludeAnnotations = $this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_FORM_EXCLUDE, self::SCOPE_PROPERTY);

        return $this->collectAllAnnotations(
            $formAnnotations,
            $formExcludeAnnotations,
            $this->getReflectionProperties($entity),
            new FormMapper()
        );
    }

    /**
     * @param mixed $entity
     * @return DatagridInterface[]
     */
    public function getDatagridMapperAnnotation($entity)
    {
        $datagridAnnotations = $this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_DATAGRID, self::SCOPE_PROPERTY);
        if(!$this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_DATAGRID_ALL, self::SCOPE_CLASS)){
            return $datagridAnnotations;
        }

        $datagridExcludeAnnotations = $this->getAnnotationsByType($entity, self::ANNOTATION_TYPE_ADMIN_DATAGRID_EXCLUDE, self::SCOPE_PROPERTY);

        return $this->collectAllAnnotations(
            $datagridAnnotations,
            $datagridExcludeAnnotations,
            $this->getReflectionProperties($entity),
            new DatagridMapper()
        );
    }

    /**
     * @param mixed $entity
     * @param SonataListMapper $listMapper
     */
    public function configureListFields($entity, SonataListMapper $listMapper)
    {
        $annotations = $this->getListMapperAnnotations($entity);

        foreach($annotations as $propertyName => $annotation){
            $method = $annotation->isIdentifier() ? 'addIdentifier' : 'add';

            $fieldDescriptionOptions = $annotation->getFieldDescriptionOptions();
            $routeName = $annotation->getRouteName();
            if($routeName){
                $fieldDescriptionOptions['route'] = array('name' => $routeName);
            }

            $listMapper->$method(
                $annotation->getName() ?: $propertyName,
                $annotation->getType(),
                $fieldDescriptionOptions
            );
        }
    }

    /**
     * @param mixed $entity
     * @param SonataFormMapper $formMapper
     */
    public function configureFormFields($entity, SonataFormMapper $formMapper)
    {
        $annotations = $this->getFormMapperAnnotations($entity);

        foreach($annotations as $propertyName => $annotation){
            $formMapper->add(
                $annotation->getName() ?: $propertyName,
                $annotation->getType(),
                $annotation->getOptions(),
                $annotation->getFieldDescriptionOptions()
            );
        }
    }

    /**
     * @param mixed $entity
     * @param SonataShowMapper $showMapper
     */
    public function configureShowFields($entity, SonataShowMapper $showMapper)
    {
        $annotations = $this->getShowMapperAnnotations($entity);

        foreach($annotations as $propertyName => $annotation){
            $showMapper->add(
                $annotation->getName() ?: $propertyName,
                $annotation->getType(),
                $annotation->getFieldDescriptionOptions()
            );
        }
    }

    /**
     * @param mixed $entity
     * @param SonataDatagridMapper $datagridMapper
     */
    public function configureDatagridFilters($entity, SonataDatagridMapper $datagridMapper)
    {
        $annotations = $this->getDatagridMapperAnnotation($entity);

        foreach($annotations as $propertyName => $annotation){
            $datagridMapper->add(
                $annotation->getName() ?: $propertyName,
                $annotation->getType(),
                $annotation->getFilterOptions(),
                $annotation->getFieldType(),
                $annotation->getFIeldOptions()
            );
        }
    }

    /**
     * @param array $foundAnnotations
     * @param array $excludedAnnotations
     * @param array $properties
     * @param mixed $prototypeAnnotation
     * @return array
     */
    protected function collectAllAnnotations(array $foundAnnotations, array $excludedAnnotations, array $properties, $prototypeAnnotation)
    {
        $annotations = array();

        foreach($properties as $reflectionProperty){
            $propertyName = $reflectionProperty->getName();
            if(isset($excludedAnnotations[$propertyName])){
                continue;
            }
            if(isset($foundAnnotations[$propertyName])){
                $annotations[$propertyName] = $foundAnnotations[$propertyName];
                continue;
            }
            $annotations[$propertyName] = clone $prototypeAnnotation;
        }

        return $annotations;
    }
}