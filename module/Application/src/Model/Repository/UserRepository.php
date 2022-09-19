<?php

namespace Application\Model\Repository;

use Application\Controller\AdminController;
use Application\Model\Entity\Email;
use Application\Model\Entity\User;
use InvalidArgumentException;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Select;
use Laminas\Hydrator\HydratorAwareInterface;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var AdapterInterface
     */
    private $db;
    /**
     * @var User|HydratorAwareInterface
     */
    private $prototype;
    /**
     * @var EmailRepositoryInterface
     */
    private $emailRepository;
    /**
     * @var PhoneRepositoryInterface
     */
    private $phoneRepository;
    /**
     * @var StatusRepositoryInterface
     */
    private $statusRepository;

    /**
     * @param AdapterInterface            $db
     * @param User|HydratorAwareInterface $prototype
     * @param EmailRepositoryInterface    $emailRepository
     * @param PhoneRepositoryInterface    $phoneRepository
     * @param StatusRepositoryInterface   $statusRepository
     */
    public function __construct(
        $db,
        $prototype,
        $emailRepository,
        $phoneRepository,
        $statusRepository
    ) {
        $this->db = $db;
        $this->prototype = $prototype;
        $this->emailRepository = $emailRepository;
        $this->phoneRepository = $phoneRepository;
        $this->statusRepository = $statusRepository;
    }

    public function findProfile($identifier)
    {
        return $this->findUser($identifier);
    }

    public function findUser($identifier)
    {
        if (is_int($identifier)) {
            return $this->findUserById($identifier);
        } elseif ($identifier instanceof Email) {
            return $this->findUserByEmail($identifier);
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    'The type of the $identifier parameter must be compatible with %1$s; %2$s used',
                    implode(' or ', [gettype(1), Email::class]),
                    (get_class($identifier) ?: gettype($identifier))
                )
            );
        }
    }

    private function findUserById($id)
    {
        $select = new Select(['u' => 'user']);
        $select->columns([
            'id'           => 'u.id',
            'password'     => 'u.password',
            'tempPassword' => 'u.temp_password',
            'tpCreatedAt'  => 'u.tp_created_at',
            'positionId'   => 'u.position_id',
            'positionName' => 'pos.name',
            'surname'      => 'u.surname',
            'name'         => 'u.name',
            'patronymic'   => 'u.patronymic',
            'gender'       => 'u.gender',
            'birthday'     => 'u.birthday',
            'image'        => 'u.image',
            'skype'        => 'u.skype',
        ], false);
        $select->where(['u.id = ?' => $id]);
        $select->join(
            ['pos' => 'position'],
            'pos.id = u.position_id',
            [],
        );

        /** @var User $user */
        $user = Extracter::extractValue(
            $select,
            $this->db,
            $this->prototype->getHydrator(),
            $this->prototype
        );

        $this->pullExtraInfo($user);

        return $user;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    private function pullExtraInfo($user)
    {
        $userStatuses = $this->statusRepository->findStatusesOfUser($user->getId());
        $statusMap = [];
        foreach ($userStatuses as $status) {
            $statusMap[$status->getName()] = true;
        }
        $user->setStatus($statusMap);

        $emails = $this->emailRepository->findEmailsOfUser($user->getId());
        $user->setEmails($emails);

        $phones = $this->phoneRepository->findPhonesOfUser($user->getId());
        $user->setPhones($phones);

        return $user;
    }

    private function findUserByEmail($email)
    {
        $foundEmail = $this->emailRepository->findEmail($email->getAddress());

        return $this->findUserById($foundEmail->getUserId());
    }

    public function findUsers(
        $where = [],
        $order = [],
        $limit = false,
        $page = 1
    ) {
        $select = new Select(['u' => 'user']);
        $select->quantifier(Select::QUANTIFIER_DISTINCT);
        $select->columns([
            'id'           => 'u.id',
            'password'     => 'u.password',
            'tempPassword' => 'u.temp_password',
            'tpCreatedAt'  => 'u.tp_created_at',
            'positionId'   => 'u.position_id',
            'positionName' => 'pos.name',
            'surname'      => 'u.surname',
            'name'         => 'u.name',
            'patronymic'   => 'u.patronymic',
            'gender'       => 'u.gender',
            'birthday'     => 'u.birthday',
            'image'        => 'u.image',
            'skype'        => 'u.skype',
        ], false);
        $select->join(
            ['pos' => 'position'],
            'pos.id = u.position_id',
            [],
        );
        $select->where($where);
        $select->order($order);
        if ($limit) {
            $select->limit(AdminController::maxPageCount);
        }
        $select->offset(($page - 1) * AdminController::maxPageCount);

        /** @var User[] $users */
        $users = Extracter::extractValues(
            $select,
            $this->db,
            $this->prototype->getHydrator(),
            $this->prototype
        );

        foreach ($users as $user) {
            $this->pullExtraInfo($user);
        }

        return $users;
    }
}