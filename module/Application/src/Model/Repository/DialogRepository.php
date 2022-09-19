<?php

namespace Application\Model\Repository;

use Application\Model\Command\DialogCommandInterface;
use Application\Model\Entity\Dialog;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Expression;
use Laminas\Db\Sql\Predicate;
use Laminas\Db\Sql\Select;
use Laminas\Hydrator\HydratorAwareInterface;

class DialogRepository implements DialogRepositoryInterface
{
    /**
     * @var AdapterInterface
     */
    private $db;
    /**
     * @var Dialog|HydratorAwareInterface
     */
    private $prototype;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var DialogCommandInterface
     */
    private $dialogCommand;

    /**
     * @param AdapterInterface              $db
     * @param Dialog|HydratorAwareInterface $prototype
     * @param UserRepositoryInterface       $userRepository
     */
    public function __construct(
        $db,
        $prototype,
        $userRepository,
        $dialogCommand
    ) {
        $this->db = $db;
        $this->prototype = $prototype;
        $this->userRepository = $userRepository;
        $this->dialogCommand = $dialogCommand;
    }

    public function getDialogId($userId, $buddyId)
    {
        $userDialogsId = array_column($this->getDialogList($userId), 'id');
        $buddyDialogsId = array_column($this->getDialogList($buddyId), 'id');

        $commonDialogsId = array_intersect($userDialogsId, $buddyDialogsId);

        if (empty($commonDialogsId)) {
            return $this->dialogCommand->createDialog($userId, $buddyId);
        }

        return array_values($commonDialogsId)[0];
    }

    public function getDialogList($userId, $where = [])
    {
        $select = new Select(['u' => 'user']);
        $select->columns([
            'id'      => 'mem.dialog_id',
            'buddyId' => 'u.id',
        ], false);
        $select->join(
            ['mem' => 'member'],
            new Predicate\Expression(
                'u.id = mem.user_id AND ' .
                'mem.dialog_id IN ( SELECT dialog_id FROM member ' .
                'WHERE user_id = ? )', $userId
            ),
            [],
            Select::JOIN_LEFT
        );
        $select->where(array_merge(['u.id != ?' => $userId], $where));
        $select->order([
            new Expression('ISNULL(mem.dialog_id)'),
        ]);

        return Extracter::extractValues(
            $select,
            $this->db,
            $this->prototype->getHydrator(),
            $this->prototype
        );
    }
}