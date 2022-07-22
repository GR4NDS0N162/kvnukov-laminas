<?php

namespace Application\Form;

use Laminas\Form\Form;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct();

        $this->add(include __DIR__ . '/../ElementOrFieldsetArray/EmailElement.php');
        $this->add(include __DIR__ . '/../ElementOrFieldsetArray/PasswordElement.php');
        $this->add(include __DIR__ . '/../ElementOrFieldsetArray/SubmitElement.php');
    }
}
