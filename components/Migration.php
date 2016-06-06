<?php

namespace console\components;


use yii\base\InvalidCallException;

class Migration extends \yii\db\Migration
{
    public $tableAdmin;

    public $table;

    public $column;

    public $baseFields = [
        'id' => 'INT UNSIGNED PRIMARY KEY AUTO_INCREMENT',
        'created_by' => 'INT UNSIGNED NOT NULL COMMENT"Created user id of the record"',
        'updated_by' => 'INT UNSIGNED NOT NULL COMMENT"Updated user id of the record"',
        'created_at' => 'INT UNSIGNED NOT NULL COMMENT"Created timestamp of the record"',
        'updated_at' => 'INT UNSIGNED NOT NULL COMMENT"Updated timestamp of the record"',
        'deleted' => 'TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT"0=not-deleted;1=deleted"',
    ];

    /**
     * Create table with base fields, column with empty values will be ignored
     * @param string $table
     * @param array $columns
     * @param array|null $options
     */
    public function createTableWithBaseFields($table, $columns, $options = null)
    {
        // Make sure the base fields comes at last, except for those in the $columns
        $columns = array_merge($columns, array_diff_key($this->baseFields, $columns));
        $columns = array_filter($columns);

        uksort($columns, function ($k1, $k2) {
            return $k1 === 'id' ? -1 : ($k2 === 'id') ? 1 : 0;
        });
        $this->createTable($table, $columns, $options);
    }

    /**
     * Check if constrain of certain table exists
     * @param string $name
     * @return bool
     */
    protected function constrainExists($name)
    {
        $sql = "SELECT COUNT(*) AS `count` FROM information_schema.KEY_COLUMN_USAGE WHERE `CONSTRAINT_NAME`='$name'";
        $query = $this->db->createCommand($sql);
        $data = $query->queryOne();
        return $data['count'] > 0;
    }

    /**
     * Get all table names except for `migration`
     * @return array
     */
    protected function getTables()
    {
        $tables = $this->db->schema->tableNames;
        return array_filter($tables, function ($v) {
            return strpos($v, 'migration') === false;
        });

    }

    /**
     * Create tables at once
     * ```php
     *  $tables = [
     *      'user' => [
     *          'id' => 'pk',
     *          'username' => 'felix',
     *          'role_id' => 'INT'
     *      ],
     *      'role' => [
     *          'id' => 'pk',
     *          'name' => 'VARCHAR(50)',
     *      ]
     *  ]
     * ```
     *
     * @param array $tables Well-formed array of tables and columns
     *
     * @see createTableWithBaseFields
     */

    public function createTablesWithBaseFields($tables)
    {
        foreach ($tables as $table => $info) {
            $this->createTableWithBaseFields($table, $info[0], $info[1]);
        }
    }

    /**
     * Drop tables given as an indexed array
     * @param array $tables
     * @see createTableWithBaseFields()
     */
    public function dropTables($tables)
    {
        array_walk($tables, function($v, $k) {
            $this->dropTable($k);
        });
    }

    /**
     * TODO unfinished
     * @param string $table
     */
    public function addBaseFields($table = '')
    {
        $table = $table ?:$this->table;
        if (!$table) {
            throw new InvalidCallException('table is needed by ::addBaseFields');
        }

        $this->baseFields;
        $tableSchema = $this->getDb()->getTableSchema($table);
        print_r($tableSchema);die;
    }

    /**
     * Add columns to table
     * @param string $table
     * @param array $columns
     */
    public function addColumns($table, $columns)
    {
        foreach ($columns as $column => &$type) {
            $this->addColumn($table, $column, $type);
        }
    }

    /**
     * Drop columns of a table
     * @param string $table
     * @param array $columns
     */
    public function dropColumns($table, $columns)
    {
        foreach ($columns as $column => &$type){
            $this->dropColumn($table, $column);
        }
    }
}