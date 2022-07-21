<?php

namespace Application\Form;

use Laminas\Form\Form;

class RecoverForm extends Form
{
    public function __construct()
    {
        parent::__construct();

        $this->add(include __DIR__ . '/../Element/EmailElement.php');
        $this->add(include __DIR__ . '/../Element/SubmitElement.php');
    }
}