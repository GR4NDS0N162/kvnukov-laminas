<?php

namespace Application\Form;

use Laminas\Form\Element;
use Laminas\Form\Form;

class RecoverForm extends Form
{
    public function __construct()
    {
        parent::__construct('recover-form');

        $this->setAttribute('class','row gy-3');

        $this->add([
            'name' => 'email-input',
            'type' => Element\Email::class,
            'attributes' => [
                'class' => 'form-control',
                'placeholder' => 'name@example.com',
                'required' => 'required',
                'pattern' => '^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$',
            ],
            'options' => [
                'label' => 'E-mail',
                'label_attributes' => [
                    'class' => 'form-label',
                ],
            ],
        ]);

        $this->add([
            'name' => 'submit-button',
            'type' => Element\Button::class,
            'attributes' => [
                'type' => 'submit',
                'class' => 'btn btn-lg btn-outline-danger w-100',
            ],
            'options' => [
                'label' => 'Восстановить',
            ],
        ]);
    }
}
