<?php

namespace Application\Form\Admin;

use Application\Fieldset\UserFieldset;
use Application\Helper\FieldsetMapper;
use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class UserForm extends Form
{
    public function init()
    {
        $this->setAttribute('class', 'row g-3 needs-validation');
        $this->setAttribute('novalidate', true);

        $this->add([
            'name'       => 'user',
            'type'       => UserFieldset::class,
            'attributes' => [
                'class' => 'row g-3 align-items-start',
            ],
            'options'    => [
                'use_as_base_fieldset' => true,
            ],
        ]);

        $this->add([
            'name'       => 'gen_new_password',
            'type'       => Checkbox::class,
            'attributes' => [
                'class' => 'form-check-input',
                'id'    => uniqid('checkbox_', true),
            ],
            'options'    => [
                'label'              => 'Сгенерировать пароль и отправить на почту',
                'label_attributes'   => [
                    'class' => 'form-check-label',
                ],
                'use_hidden_element' => false,
            ],
        ]);

        $this->add([
            'name'       => 'submit',
            'type'       => Submit::class,
            'attributes' => [
                'value' => 'Сохранить изменения',
                'class' => 'btn btn-outline-success w-100',
            ],
        ]);

        FieldsetMapper::setAttributes($this, [
            'children' => [
                'user'             => [
                    'value'    => 'col-12',
                    'children' => [
                        'id'         => 'd-none',
                        'image'      => 'col-12',
                        'imageFile'  => 'col-12',
                        'surname'    => 'col-12 col-lg-4',
                        'name'       => 'col-12 col-sm-6 col-lg-4',
                        'patronymic' => 'col-12 col-sm-6 col-lg-4',
                        'gender'     => 'col-12 col-sm-6 col-lg-4',
                        'birthday'   => 'col-12 col-sm-6 col-lg-4',
                        'skype'      => 'col-12 col-lg-4',
                        'emails'     => [
                            'value'          => 'col-12 col-lg-6',
                            'target_element' => 'col-12',
                        ],
                        'phones'     => [
                            'value'          => 'col-12 col-lg-6',
                            'target_element' => 'col-12',
                        ],
                        'positionId' => 'col-12',
                        'password'   => 'col-12',
                        'status'     => [
                            'value'    => 'col-12',
                            'children' => [
                                'admin'  => 'col-12 col-sm-6',
                                'active' => 'col-12 col-sm-6',
                            ],
                        ],
                    ],
                ],
                'gen_new_password' => 'col-12',
                'submit'           => 'col-12',
            ]
        ]);
    }
}