<?php

/*
 * admin42
 *
 * @package admin42
 * @link https://github.com/kiwi-suite/admin42
 * @copyright Copyright (c) 2010 - 2017 kiwi suite (https://www.kiwi-suite.com)
 * @license MIT License
 * @author kiwi suite <tech@kiwi-suite.com>
 */


namespace Admin42\FormElements;

use Admin42\FormElements\Service\Factory;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\FormInterface;
use Zend\Hydrator\HydratorInterface;

class Fieldset extends \Zend\Form\Fieldset implements AngularAwareInterface
{
    use HydratorTrait;
    use ElementTrait;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var bool
     */
    protected $showLabel = false;

    /**
     * @var bool
     */
    protected $collapseAble = false;

    /**
     * @return bool
     */
    public function getShowLabel()
    {
        return $this->showLabel;
    }

    /**
     * @param bool $showLabel
     * @return Fieldset
     */
    public function setShowLabel($showLabel)
    {
        $this->showLabel = $showLabel;
        return $this;
    }

    /**
     * @return bool
     */
    public function getCollapseAble()
    {
        return $this->collapseAble;
    }

    /**
     * @param bool $collapseAble
     * @return Fieldset
     */
    public function setCollapseAble($collapseAble)
    {
        $this->collapseAble = $collapseAble;
        return $this;
    }


    /**
     * @param array|\Traversable $options
     * @return $this
     */
    public function setOptions($options)
    {
        if (\array_key_exists('showLabel', $options)) {
            $this->setShowLabel((bool) $options['showLabel']);
        }

        if (\array_key_exists('collapseAble', $options)) {
            $this->setCollapseAble((bool) $options['collapseAble']);
        }

        return $this;
    }

    /**
     * @param FormInterface $form
     * @return mixed|void
     */
    public function prepareElement(FormInterface $form)
    {
        $name = $this->getName();

        $this->setOption('formServiceHash', $form->getOption('formServiceHash'));

        foreach ($this->iterator as $elementOrFieldset) {
            if ($elementOrFieldset->getOption('originalName') === null) {
                $elementOrFieldset->setOption('originalName', $elementOrFieldset->getName());
            }
            $elementOrFieldset->setOption('formServiceHash', $form->getOption('formServiceHash'));

            $elementOrFieldset->setName($name . '[' . $elementOrFieldset->getName() . ']');

            // Recursively prepare elements
            if ($elementOrFieldset instanceof ElementPrepareAwareInterface) {
                $elementOrFieldset->prepareElement($form);
            }
        }
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (!($this->hydrator instanceof HydratorInterface)) {
            $this->setHydrator($this->prepareHydrator($this));
        }

        return parent::getHydrator();
    }

    /**
     * Retrieve composed form factory
     *
     * Lazy-loads one if none present.
     *
     * @return Factory
     */
    public function getFormFactory()
    {
        if (null === $this->factory) {
            $this->setFormFactory(new Factory());
        }

        return $this->factory;
    }
}
