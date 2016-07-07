<?php
/**
 * admin42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2014 raum42 OG (http://www.raum42.at)
 *
 */

namespace Admin42\Form\User;

use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Password;
use Zend\Form\Element\Text;
use Zend\Form\Form;

class LoginForm extends Form
{
    /**
     *
     */
    public function init()
    {
        $this->add(new Csrf("csrf"));

        $username = new Text("identity");
        $username->setLabel("field.usernameoremail");
        $this->add($username);

        $password = new Password("password");
        $password->setLabel("field.password");
        $this->add($password);

        $redirectTo = new Hidden("redirectTo");
        $this->add($redirectTo);
    }
}