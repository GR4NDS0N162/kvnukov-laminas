<?php

use Laminas\Form\Element\Password;

return [
    'name' => 'password-check',
    'type' => Password::class,
    'attributes' => [
        'class' => 'form-control',
        'placeholder' => 'qwerty123',
        'required' => 'required',
    ],
    'options' => [
        'label' => 'Подтверждение пароля',
    ],
];
