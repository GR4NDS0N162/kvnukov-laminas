<?php

namespace Application\Model\Entity;

use DomainException;
use Laminas\Filter\ToInt;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\Validator\StringLength;

class ChangePassword implements InputFilterAwareInterface
{
    /**
     * @var int|null
     */
    private ?int $id;
    /**
     * @var string
     */
    private string $currentPassword;
    /**
     * @var string
     */
    private string $newPassword;
    /**
     * @var string
     */
    private string $passwordCheck;
    /**
     * @var InputFilterInterface
     */
    private InputFilterInterface $inputFilter;

    /**
     * @param int|null $id
     * @param string   $currentPassword
     * @param string   $newPassword
     * @param string   $passwordCheck
     */
    public function __construct(
        $id = null,
        $currentPassword = '',
        $newPassword = '',
        $passwordCheck = ''
    ) {
        $this->id = $id;
        $this->currentPassword = $currentPassword;
        $this->newPassword = $newPassword;
        $this->passwordCheck = $passwordCheck;
        $this->inputFilter = $this->getInputFilter();
    }

    public function getInputFilter()
    {
        if (isset($this->inputFilter)) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name'     => 'id',
            'required' => true,
            'filters'  => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'currentPassword',
            'required'   => true,
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 32,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'newPassword',
            'required'   => true,
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 8,
                        'max'      => 32,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name'       => 'passwordCheck',
            'required'   => true,
            'validators' => [
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => 'newPassword',
                    ],
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(
            sprintf(
                '%s does not allow injection of an alternate input filter',
                __CLASS__
            )
        );
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }

    /**
     * @return string
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @return string
     */
    public function getPasswordCheck()
    {
        return $this->passwordCheck;
    }
}