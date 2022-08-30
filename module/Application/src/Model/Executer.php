<?php

namespace Application\Model;

use InvalidArgumentException;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Combine;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\InsertIgnore;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Update;
use RuntimeException;

class Executer
{
    const COMBINE = 'combine';
    const DELETE = 'delete';
    const INSERT_IGNORE = 'insert ignore';
    const INSERT = 'insert';
    const SELECT = 'select';
    const UPDATE = 'update';

    public static function extractArray($sql, $select, $hydrator, $prototype)
    {
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        $return = [];

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            return $return;
        }

        $resultSet = new HydratingResultSet($hydrator, $prototype);
        $resultSet->initialize($result);

        foreach ($resultSet as $obj) {
            $return[] = $obj;
        }

        return $return;
    }

    public static function extractValue(
        $sql,
        $select,
        $hydrator,
        $prototype,
        $runtimeExceptionMessage = 'Failed to retrieve the object; unknown database error.',
        $invalidArgumentExceptionMessage = 'Object not found.'
    ) {
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if (!$result instanceof ResultInterface || !$result->isQueryResult()) {
            throw new RuntimeException($runtimeExceptionMessage);
        }

        $resultSet = new HydratingResultSet($hydrator, $prototype);
        $resultSet->initialize($result);
        $object = $resultSet->current();

        if (!$object) {
            throw new InvalidArgumentException($invalidArgumentExceptionMessage);
        }

        return $object;
    }

    public static function executeSql(
        $sql,
        $preparable,
        $operationDescription = 'sql',
        $runtimeExceptionFormat = 'Database error occurred during %s operation.'
    ) {
        $statement = $sql->prepareStatementForSqlObject($preparable);
        $result = $statement->execute();

        if ($preparable instanceof Combine) {
            $operationDescription = self::COMBINE;
        } elseif ($preparable instanceof Delete) {
            $operationDescription = self::DELETE;
        } elseif ($preparable instanceof InsertIgnore) {
            $operationDescription = self::INSERT_IGNORE;
        } elseif ($preparable instanceof Insert) {
            $operationDescription = self::INSERT;
        } elseif ($preparable instanceof Select) {
            $operationDescription = self::SELECT;
        } elseif ($preparable instanceof Update) {
            $operationDescription = self::UPDATE;
        }

        if (!$result instanceof ResultInterface) {
            throw new RuntimeException(
                sprintf(
                    $runtimeExceptionFormat,
                    $operationDescription
                )
            );
        }

        return $result->getGeneratedValue();
    }
}