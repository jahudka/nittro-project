<?php

declare(strict_types=1);

namespace App\Models;

use Dibi\Connection;
use Dibi\Result;
use Dibi\Row;


abstract class BaseModel {

    /** @var Connection */
    protected $dibi;

    /** @var string */
    protected $table;

    /** @var string */
    protected $rowClass = Row::class;


    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection) {
        $this->dibi = $connection;

        if (!isset($this->table)) {
            throw new \LogicException('No table specified');
        }
    }


    /**
     * @param array|int $criteria
     * @param bool $need
     * @return Row|null
     * @throws NoMatchException
     */
    public function get($criteria, bool $need = true) {
        if (!is_array($criteria)) {
            $criteria = ['id' => $criteria];
        }

        $result = $this->dibi->query('SELECT * FROM %n', $this->table, 'WHERE %and', $criteria, 'LIMIT 1');

        $result->setRowClass($this->rowClass);
        $row = $result->fetch();

        if ($row) {
            $this->expandRow($row);
            return $row;
        } else if ($need) {
            throw new NoMatchException();
        } else {
            return null;
        }
    }

    /**
     * @param array|null $criteria
     * @param array|string $sortBy
     * @param array|int $limit
     * @return Result
     */
    public function find(?array $criteria = null, $sortBy = null, $limit = null) : Result {
        $q = ['SELECT * FROM %n', $this->table];

        if ($criteria) {
            $q[] = 'WHERE %and';
            $q[] = $criteria;
        }

        if ($sortBy) {
            if (!is_array($sortBy)) {
                $sortBy = [$sortBy => true];

            }

            $q[] = 'ORDER BY %by';
            $q[] = $sortBy;
        }

        if ($limit) {
            $q[] = 'LIMIT %i';
            $q[] = array_slice((array) $limit, 0, 2);
        }

        return $this->createResult($q);
    }

    /**
     * @param array|null $criteria
     * @return int
     */
    public function count(array $criteria = null) : int {
        $q = ['SELECT COUNT(*) FROM %n', $this->table];

        if ($criteria) {
            $q[] = 'WHERE %and';
            $q[] = $criteria;
        }

        return (int) $this->dibi->fetchSingle($q);
    }

    /**
     * @param array $entry
     * @return int
     * @throws \Dibi\Exception
     */
    public function save(array $entry) : int {
        $data = $this->beforeSave($entry);

        if (isset($entry['id']) && $entry['id']) {
            $id = $entry['id'];
            unset($entry['id']);
            $this->dibi->query('UPDATE %n SET %a WHERE [id] = %i', $this->table, $entry, $id);
        } else {
            unset($entry['id']);
            $this->dibi->query('INSERT INTO %n %v', $this->table, $entry);
            $id = $this->dibi->getInsertId();
        }

        $this->afterSave($entry, $id, $data);
        return $id;

    }


    /**
     * Intended to be overridden by descendants.
     * @param array $entry
     * @return mixed
     */
    protected function beforeSave(array $entry) {

    }

    /**
     * Intended to be overridden by descendants.
     * @param array $entry
     * @param int $id
     * @param mixed $data
     */
    protected function afterSave(array $entry, int $id, $data) {

    }


    /**
     * @param int $id
     * @return bool
     * @throws \Dibi\Exception
     */
    public function remove(int $id) : bool {
        $this->beforeRemove($id);
        $this->dibi->query('DELETE FROM %n WHERE [id] = %i', $this->table, $id);
        $result = (bool) $this->dibi->getAffectedRows();
        $this->afterRemove($id, $result);
        return $result;

    }


    /**
     * Intended to be overridden by descendants.
     * @param int $id
     */
    protected function beforeRemove(int $id) {

    }

    /**
     * Intended to be overridden by descendants.
     * @param int $id
     * @param bool $deleted
     */
    protected function afterRemove(int $id, bool $deleted) {

    }


    /**
     * Intended to be overridden by descendants
     * @param $row
     */
    protected function expandRow($row) : void {

    }


    /**
     * @param string|array $query
     * @return Result
     */
    protected function createResult($query) : Result {
        $result = $this->dibi->query($query);

        $result->setRowFactory(function($data) {
            $row = new $this->rowClass($data);
            $this->expandRow($row);
            return $row;
        });

        return $result;
    }
}
