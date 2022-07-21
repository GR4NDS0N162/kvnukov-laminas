<?php

use Laminas\Form\Element\Password;

return [
    'name' => 'password',
    'type' => Password::class,
    'attributes' => [
        'class' => 'form-control',
        'placeholder' => 'qwerty123',
        'required' => 'required',
        'pattern' => '^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=])(?=\S+$).{8,32}$',
    ],
    'options' => [
        'label' => 'Пароль',
    ],
];
