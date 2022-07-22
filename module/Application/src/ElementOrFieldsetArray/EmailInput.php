<?php

namespace Application\ElementOrFieldsetArray;

use Laminas\Form\Element\Email;

return [
    'name' => 'email-input',
    'type' => Email::class,
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
];