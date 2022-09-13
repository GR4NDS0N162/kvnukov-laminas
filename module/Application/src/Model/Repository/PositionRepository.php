<?php

namespace Application\Model\Repository;

use Application\Model\Entity\Position;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Select;
use Laminas\Hydrator\HydratorAwareInterface;

class PositionRepository implements PositionRepositoryInterface
{
    /**
     * @var AdapterInterface
     */
    private $db;
    /**
     * @var Position
     */
    private $prototype;

    /**
     * @param AdapterInterface                $db
     * @param Position|HydratorAwareInterface $prototype
     */
    public function __construct($db, $prototype)
    {
        $this->db = $db;
        $this->prototype = $prototype;
    }

    public function findAllPositions()
    {
        $select = new Select(['pos' => 'position']);
        $select->columns([
            'id'   => 'pos.id',
            'name' => 'pos.name',
        ], false);
        $select->order(['pos.name ASC']);

        return Extracter::extractValues(
            $select,
            $this->db,
            $this->prototype->getHydrator(),
            $this->prototype
        );
    }

    public function findPositionById($id)
    {
        $select = new Select(['pos' => 'position']);
        $select->columns([
            'id'   => 'pos.id',
            'name' => 'pos.name',
        ], false);
        $select->where(['pos.id = ?' => $id]);

        return Extracter::extractValue(
            $select,
            $this->db,
            $this->prototype->getHydrator(),
            $this->prototype
        );
    }
}