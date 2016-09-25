<?php
/**
 * admin42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Admin42\Form\User;

use Admin42\FormElements\Form;
use Zend\InputFilter\InputFilterProviderInterface;

class RecoverPasswordForm extends Form  implements InputFilterProviderInterface
{
    /**
     *
     */
    public function init()
    {
        $this->add([
            'name' => "csrf",
            "type" => "csrf",
        ]);

        $this->add([
            'name' => "password",
            "type" => "password",
            "label" => "field.password",
            "template" => "partial/admin42/form/no-layout/password",
        ]);

        $this->add([
            'name' => "passwordRepeat",
            "type" => "password",
            "label" => "field.password-repeat",
            "template" => "partial/admin42/form/no-layout/password",
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
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'password'
                        ],
                    ],
                ],
            ],
        ];
    }
}
