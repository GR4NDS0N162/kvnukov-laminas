<?php

namespace Application\Model\Entity;

use Laminas\Filter\ToInt;
use Laminas\Hydrator\Strategy\NullableStrategy;
use Laminas\Hydrator\Strategy\ScalarTypeStrategy;
use Laminas\Validator\GreaterThan;
use Laminas\Validator\IsCountable;
use Laminas\Validator\Regex;
use Laminas\Validator\StringLength;

class User extends Profile
{
    /**
     * @var string
     */
    protected $password;
    /**
     * @var string|null
     */
    protected $tempPassword;
    /**
     * @var int
     */
    protected $positionId;
    /**
     * @var string
     */
    protected $positionName;
    /**
     * @var bool[]
     */
    protected $status;
    /**
     * @var bool
     */
    protected $genNewPassword;

    /**
     * @param int         $positionId
     * @param string      $positionName
     * @param bool[]      $status
     * @param string      $password
     * @param Email[]     $emails
     * @param Phone[]     $phones
     * @param string|null $surname
     * @param string|null $name
     * @param string|null $patronymic
     * @param int|null    $gender
     * @param string|null $birthday
     * @param string|null $image
     * @param string|null $skype
     * @param string|null $tempPassword
     * @param int|null    $id
     * @param bool        $genNewPassword
     */
    public function __construct(
        $password = '',
        $positionId = 0,
        $positionName = '',
        $status = [],
        $emails = [],
        $phones = [],
        $surname = null,
        $name = null,
        $patronymic = null,
        $gender = null,
        $birthday = null,
        $image = null,
        $skype = null,
        $tempPassword = null,
        $id = null,
        $genNewPassword = false
    ) {
        parent::__construct(
            $emails,
            $phones,
            $surname,
            $name,
            $patronymic,
            $gender,
            $birthday,
            $image,
            $skype,
            $id
        );

        $this->password = $password;
        $this->tempPassword = $tempPassword;
        $this->positionId = $positionId;
        $this->positionName = $positionName;
        $this->status = $status;
        $this->genNewPassword = $genNewPassword;

        $this->inputFilter->add([
            'name'       => 'password',
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
                [
                    'name'    => Regex::class,
                    'options' => [
                        'pattern' => '/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[!"#\$%&\'\(\)\*\+,-\.\/:;<=>\?@[\]\^_`\{\|}~])[a-zA-Z0-9!"#\$%&\'\(\)\*\+,-\.\/:;<=>\?@[\]\^_`\{\|}~]*$/',
                    ],
                ],
            ],
        ]);
        $this->inputFilter->add([
            'name'       => 'tempPassword',
            'required'   => false,
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min'      => 8,
                        'max'      => 32,
                    ],
                ],
                [
                    'name'    => Regex::class,
                    'options' => [
                        'pattern' => '/^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[!"#\$%&\'\(\)\*\+,-\.\/:;<=>\?@[\]\^_`\{\|}~])[a-zA-Z0-9!"#\$%&\'\(\)\*\+,-\.\/:;<=>\?@[\]\^_`\{\|}~]*$/',
                    ],
                ],
            ],
        ]);
        $this->inputFilter->add([
            'name'       => 'positionId',
            'required'   => true,
            'filters'    => [
                ['name' => ToInt::class],
            ],
            'validators' => [
                [
                    'name'    => GreaterThan::class,
                    'options' => [
                        'min' => 0,
                    ],
                ],
            ],
        ]);
        $this->inputFilter->add([
            'name'       => 'positionName',
            'required'   => false,
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'max'      => 100,
                    ],
                ],
            ],
        ]);
        $this->inputFilter->add([
            'name'       => 'status',
            'required'   => false,
            'validators' => [
                ['name' => IsCountable::class],
            ],
        ]);
        $this->inputFilter->add([
            'name'     => 'genNewPassword',
            'required' => false,
        ]);

        $this->hydrator->addStrategy('password', ScalarTypeStrategy::createToString());
        $this->hydrator->addStrategy('tempPassword', new NullableStrategy(ScalarTypeStrategy::createToString(), true));
        $this->hydrator->addStrategy('positionId', ScalarTypeStrategy::createToInt());
        $this->hydrator->addStrategy('positionName', ScalarTypeStrategy::createToString());
        $this->hydrator->addStrategy('genNewPassword', ScalarTypeStrategy::createToBoolean());
    }

    public function toArray(bool $isAdminPage): array
    {
        return [
            'isActive'     => $this->status['active'] ?? false,
            'isAdmin'      => $isAdminPage && ($this->status['admin'] ?? false),
            'positionName' => $this->positionName,
            'fullname'     => implode(' ', [
                $this->surname ?: '—',
                $this->name ?: '—',
                $this->patronymic ?: '—',
            ]),
            'genderString' => $this->getGenderString(),
            'ageString'    => $this->getAgeString(),
            'imagePath'    => $this->getImagePath(),
            'userId'       => $this->id,
        ];
    }

    /**
     * @return int
     */
    public function getPositionId()
    {
        return $this->positionId;
    }

    /**
     * @param int $positionId
     */
    public function setPositionId($positionId)
    {
        $this->positionId = $positionId;
    }

    /**
     * @return bool[]
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool[] $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     *
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getTempPassword()
    {
        return $this->tempPassword;
    }

    /**
     * @param string|null $tempPassword
     */
    public function setTempPassword($tempPassword)
    {
        $this->tempPassword = $tempPassword;
    }

    /**
     * @return bool
     */
    public function isGenNewPassword()
    {
        return $this->genNewPassword;
    }

    /**
     * @param bool $genNewPassword
     */
    public function setGenNewPassword($genNewPassword)
    {
        $this->genNewPassword = $genNewPassword;
    }

    /**
     * @return string
     */
    public function getPositionName()
    {
        return $this->positionName;
    }

    /**
     * @param string $positionName
     */
    public function setPositionName($positionName)
    {
        $this->positionName = $positionName;
    }
}