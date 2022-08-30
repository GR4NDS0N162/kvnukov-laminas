<?php

namespace Application\Model\Repository;

use Application\Model\EmailRepositoryInterface;
use Application\Model\Entity\Email;
use Application\Model\Executer;
use InvalidArgumentException;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Sql;
use Laminas\Hydrator\HydratorInterface;
use RuntimeException;

class EmailRepository implements EmailRepositoryInterface
{
    /**
     * @var AdapterInterface
     */
    private $db;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @var Email
     */
    private $emailPrototype;

    public function __construct(
        AdapterInterface  $db,
        HydratorInterface $hydrator,
        Email             $emailPrototype
    ) {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->emailPrototype = $emailPrototype;
    }

    public function findEmailsOfUser(int $userId)
    {
        $sql = new Sql($this->db);
        $select = $sql->select('email');
        $select->columns([
            'address',
            'userId' => 'user_id',
        ]);
        $select->where(['user_id = ?' => $userId]);

        return Executer::extractArray(
            $sql,
            $select,
            $this->hydrator,
            $this->emailPrototype,
        );
    }

    /**
     * @inheritdoc
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function findEmail(string $address)
    {
        $sql = new Sql($this->db);
        $select = $sql->select('email');
        $select->columns([
            'address',
            'userId' => 'user_id',
        ]);
        $select->where(['address = ?' => $address]);

        $email = Executer::extractValue(
            $sql,
            $select,
            $this->hydrator,
            $this->emailPrototype,
            sprintf(
                'Failed retrieving email with address "%s"; unknown database error.',
                $address
            ),
            sprintf(
                'Email with address "%s" not found.',
                $address
            ),
        );

        if ($email instanceof Email) {
            return $email;
        }

        throw new RuntimeException(
            sprintf(
                'Failed retrieving email with address "%s"; unknown repository error.',
                $address
            )
        );
    }
}