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


namespace Admin42\Form\User;

use Admin42\FormElements\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class ManageForm extends Form implements InputFilterProviderInterface
{
    /**
     *
     */
    public function init()
    {
        $this->add([
            'name' => 'csrf',
            'type' => 'csrf',
        ]);

        $this->add([
            'name' => 'username',
            'type' => 'text',
            'label' => 'field.username',
        ]);

        $this->add([
            'name' => 'email',
            'type' => 'email',
            'label' => 'field.email',
        ]);

        $this->add([
            'name' => 'passwordOld',
            'type' => 'password',
            'label' => 'field.password-old',
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'password',
            'label' => 'field.password',
        ]);

        $this->add([
            'name' => 'passwordRepeat',
            'type' => 'password',
            'label' => 'field.password-repeat',
        ]);

        $this->add([
            'name' => 'displayName',
            'type' => 'text',
            'label' => 'field.display-name',
        ]);

        $this->add([
            'name' => 'shortName',
            'type' => 'text',
            'label' => 'field.short-name',
            'maxLength' => 2,
        ]);

        $this->add([
            'name' => 'locale',
            'type' => 'adminLanguage',
            'label' => 'field.language',
        ]);
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'passwordRepeat' => [
                'required' => false,
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'password',
                        ],
                    ],
                ],
            ],
        ];
    }
}
