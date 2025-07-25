<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Migrations\Db\Adapter;

use Cake\Database\Connection;
use Cake\I18n\Date;
use Cake\I18n\DateTime;
use InvalidArgumentException;
use Migrations\Db\AlterInstructions;
use Migrations\Db\Literal;
use Migrations\Db\Table\Column;
use Migrations\Db\Table\ForeignKey;
use Migrations\Db\Table\Index;
use Migrations\Db\Table\Table;
use Phinx\Util\Literal as PhinxLiteral;

class PostgresAdapter extends AbstractAdapter
{
    public const GENERATED_ALWAYS = 'ALWAYS';
    public const GENERATED_BY_DEFAULT = 'BY DEFAULT';
    /**
     * Allow insert when a column was created with the GENERATED ALWAYS clause.
     * This is required for seeding the database.
     */
    public const OVERRIDE_SYSTEM_VALUE = 'OVERRIDING SYSTEM VALUE';

    /**
     * @var string[]
     */
    protected static array $specificColumnTypes = [
        self::PHINX_TYPE_JSON,
        self::PHINX_TYPE_JSONB,
        self::PHINX_TYPE_CIDR,
        self::PHINX_TYPE_INET,
        self::PHINX_TYPE_MACADDR,
        self::PHINX_TYPE_INTERVAL,
        self::PHINX_TYPE_BINARYUUID,
        self::PHINX_TYPE_NATIVEUUID,
    ];

    private const GIN_INDEX_TYPE = 'gin';

    /**
     * Columns with comments
     *
     * @var \Migrations\Db\Table\Column[]
     */
    protected array $columnsWithComments = [];

    /**
     * Use identity columns if available (Postgres >= 10.0)
     *
     * @var bool
     */
    protected bool $useIdentity;

    /**
     * {@inheritDoc}
     */
    public function setConnection(Connection $connection): AdapterInterface
    {
        // always set here since connect() isn't always called
        $version = $connection->getDriver()->version();
        $this->useIdentity = (float)$version >= 10;

        return parent::setConnection($connection);
    }

    /**
     * Quotes a schema name for use in a query.
     *
     * @param string $schemaName Schema Name
     * @return string
     */
    public function quoteSchemaName(string $schemaName): string
    {
        return $this->quoteColumnName($schemaName);
    }

    /**
     * @inheritDoc
     */
    public function quoteTableName(string $tableName): string
    {
        $parts = $this->getSchemaName($tableName);

        return $this->quoteSchemaName($parts['schema']) . '.' . $this->quoteColumnName($parts['table']);
    }

    /**
     * @inheritDoc
     */
    public function hasTable(string $tableName): bool
    {
        if ($this->hasCreatedTable($tableName)) {
            return true;
        }
        $parts = $this->getSchemaName($tableName);
        $tableName = $parts['table'];

        $dialect = $this->getSchemaDialect();
        [$query, $params] = $dialect->listTablesSql(['schema' => $parts['schema']]);

        $rows = $this->query($query, $params)->fetchAll();
        $tables = array_column($rows, 0);

        return in_array($tableName, $tables, true);
    }

    /**
     * @inheritDoc
     */
    public function createTable(Table $table, array $columns = [], array $indexes = []): void
    {
        $queries = [];

        $options = $table->getOptions();
        $parts = $this->getSchemaName($table->getName());

         // Add the default primary key
        if (!isset($options['id']) || $options['id'] === true) {
            $options['id'] = 'id';
        }

        if (is_string($options['id'])) {
            // Handle id => "field_name" to support AUTO_INCREMENT
            $column = new Column();
            $column->setName($options['id'])
                   ->setType('integer')
                   ->setOptions(['identity' => true]);

            array_unshift($columns, $column);
            if (isset($options['primary_key']) && (array)$options['id'] !== (array)$options['primary_key']) {
                throw new InvalidArgumentException('You cannot enable an auto incrementing ID field and a primary key');
            }
            $options['primary_key'] = $options['id'];
        }

        // TODO - process table options like collation etc
        $sql = 'CREATE TABLE ';
        $sql .= $this->quoteTableName($table->getName()) . ' (';

        $this->columnsWithComments = [];
        foreach ($columns as $column) {
            $sql .= $this->quoteColumnName((string)$column->getName()) . ' ' . $this->getColumnSqlDefinition($column);
            if ($this->useIdentity && $column->getIdentity() && $column->getGenerated() !== null) {
                $sql .= sprintf(' GENERATED %s AS IDENTITY', (string)$column->getGenerated());
            }
            $sql .= ', ';

            // set column comments, if needed
            if ($column->getComment()) {
                $this->columnsWithComments[] = $column;
            }
        }

         // set the primary key(s)
        if (isset($options['primary_key'])) {
            $sql = rtrim($sql);
            $sql .= sprintf(' CONSTRAINT %s PRIMARY KEY (', $this->quoteColumnName($parts['table'] . '_pkey'));
            if (is_string($options['primary_key'])) { // handle primary_key => 'id'
                $sql .= $this->quoteColumnName($options['primary_key']);
            } elseif (is_array($options['primary_key'])) { // handle primary_key => array('tag_id', 'resource_id')
                $sql .= implode(',', array_map([$this, 'quoteColumnName'], $options['primary_key']));
            }
            $sql .= ')';
        } else {
            $sql = rtrim($sql, ', '); // no primary keys
        }

        $sql .= ')';
        $queries[] = $sql;

        // process column comments
        if ($this->columnsWithComments) {
            foreach ($this->columnsWithComments as $column) {
                $queries[] = $this->getColumnCommentSqlDefinition($column, $table->getName());
            }
        }

        // set the indexes
        if ($indexes) {
            foreach ($indexes as $index) {
                $queries[] = $this->getIndexSqlDefinition($index, $table->getName());
            }
        }

        // process table comments
        if (isset($options['comment'])) {
            $queries[] = sprintf(
                'COMMENT ON TABLE %s IS %s',
                $this->quoteTableName($table->getName()),
                $this->quoteString($options['comment']),
            );
        }

        foreach ($queries as $query) {
            $this->execute($query);
        }

        $this->addCreatedTable($table->getName());
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function getChangePrimaryKeyInstructions(Table $table, array|string|null $newColumns): AlterInstructions
    {
        $parts = $this->getSchemaName($table->getName());

        $instructions = new AlterInstructions();

        // Drop the existing primary key
        $primaryKey = $this->getPrimaryKey($table->getName());
        if (!empty($primaryKey['constraint'])) {
            $sql = sprintf(
                'DROP CONSTRAINT %s',
                $this->quoteColumnName($primaryKey['constraint']),
            );
            $instructions->addAlter($sql);
        }

        // Add the new primary key
        if ($newColumns) {
            $sql = sprintf(
                'ADD CONSTRAINT %s PRIMARY KEY (',
                $this->quoteColumnName($parts['table'] . '_pkey'),
            );
            if (is_string($newColumns)) { // handle primary_key => 'id'
                $sql .= $this->quoteColumnName($newColumns);
            } else { // handle primary_key => array('tag_id', 'resource_id')
                $sql .= implode(',', array_map([$this, 'quoteColumnName'], $newColumns));
            }
            $sql .= ')';
            $instructions->addAlter($sql);
        }

        return $instructions;
    }

    /**
     * @inheritDoc
     */
    protected function getChangeCommentInstructions(Table $table, ?string $newComment): AlterInstructions
    {
        $instructions = new AlterInstructions();

        // passing 'null' is to remove table comment
        $newComment = $newComment !== null
            ? $this->quoteString($newComment)
            : 'NULL';
        $sql = sprintf(
            'COMMENT ON TABLE %s IS %s',
            $this->quoteTableName($table->getName()),
            $newComment,
        );
        $instructions->addPostStep($sql);

        return $instructions;
    }

    /**
     * @inheritDoc
     */
    protected function getRenameTableInstructions(string $tableName, string $newTableName): AlterInstructions
    {
        $this->updateCreatedTableName($tableName, $newTableName);
        $sql = sprintf(
            'ALTER TABLE %s RENAME TO %s',
            $this->quoteTableName($tableName),
            $this->quoteColumnName($newTableName),
        );

        return new AlterInstructions([], [$sql]);
    }

    /**
     * @inheritDoc
     */
    protected function getDropTableInstructions(string $tableName): AlterInstructions
    {
        $this->removeCreatedTable($tableName);
        $sql = sprintf('DROP TABLE %s', $this->quoteTableName($tableName));

        return new AlterInstructions([], [$sql]);
    }

    /**
     * @inheritDoc
     */
    public function truncateTable(string $tableName): void
    {
        $sql = sprintf(
            'TRUNCATE TABLE %s RESTART IDENTITY',
            $this->quoteTableName($tableName),
        );

        $this->execute($sql);
    }

    /**
     * @inheritDoc
     */
    public function getColumns(string $tableName): array
    {
        $parts = $this->getSchemaName($tableName);
        $columns = [];

        // TODO We can't use cakephp/database here as several attributes are missing
        // from the query cakephp prepares. We'll need to expand the cakephp/database
        // query in a future release.
        $sql = sprintf(
            'SELECT column_name, data_type, udt_name, is_identity, is_nullable,
             column_default, character_maximum_length, numeric_precision, numeric_scale,
             datetime_precision
             %s
             FROM information_schema.columns
             WHERE table_schema = ? AND table_name = ?
             ORDER BY ordinal_position',
            $this->useIdentity ? ', identity_generation' : '',
        );
        $params = [
            $parts['schema'],
            $parts['table'],
        ];
        $columnsInfo = $this->query($sql, $params)->fetchAll('assoc');
        foreach ($columnsInfo as $columnInfo) {
            $isUserDefined = strtoupper(trim($columnInfo['data_type'])) === 'USER-DEFINED';

            if ($isUserDefined) {
                $columnType = Literal::from($columnInfo['udt_name']);
            } else {
                $columnType = $this->getPhinxType($columnInfo['data_type']);
            }

            // If the default value begins with a ' or looks like a function mark it as literal
            if (isset($columnInfo['column_default'][0]) && $columnInfo['column_default'][0] === "'") {
                if (preg_match('/^\'(.*)\'::[^:]+$/', $columnInfo['column_default'], $match)) {
                    // '' and \' are replaced with a single '
                    $columnDefault = preg_replace('/[\'\\\\]\'/', "'", $match[1]);
                } else {
                    $columnDefault = Literal::from($columnInfo['column_default']);
                }
            } elseif (
                $columnInfo['column_default'] !== null &&
                preg_match('/^\D[a-z_\d]*\(.*\)$/', $columnInfo['column_default'])
            ) {
                $columnDefault = Literal::from($columnInfo['column_default']);
            } else {
                $columnDefault = $columnInfo['column_default'];
            }

            $column = new Column();

            $column->setName($columnInfo['column_name'])
                   ->setType($columnType)
                   ->setNull($columnInfo['is_nullable'] === 'YES')
                   ->setDefault($columnDefault)
                   ->setIdentity($columnInfo['is_identity'] === 'YES')
                   ->setScale($columnInfo['numeric_scale']);

            if ($this->useIdentity) {
                $column->setGenerated($columnInfo['identity_generation']);
            }

            if (preg_match('/\bwith time zone$/', $columnInfo['data_type'])) {
                $column->setTimezone(true);
            }

            if (isset($columnInfo['character_maximum_length'])) {
                $column->setLimit($columnInfo['character_maximum_length']);
            }

            if (in_array($columnType, [static::PHINX_TYPE_TIME, static::PHINX_TYPE_DATETIME], true)) {
                $column->setPrecision($columnInfo['datetime_precision']);
            } elseif ($columnType === self::PHINX_TYPE_DECIMAL) {
                $column->setPrecision($columnInfo['numeric_precision']);
            }
            $columns[] = $column;
        }

        return $columns;
    }

    /**
     * @inheritDoc
     */
    public function hasColumn(string $tableName, string $columnName): bool
    {
        $parts = $this->getSchemaName($tableName);
        $connection = $this->getConnection();
        $sql = 'SELECT count(*)
            FROM information_schema.columns
            WHERE table_schema = ? AND table_name = ? AND column_name = ?';

        $result = $connection->execute($sql, [$parts['schema'], $parts['table'], $columnName]);
        $row = $result->fetch('assoc');
        $result->closeCursor();

        return $row['count'] > 0;
    }

    /**
     * @inheritDoc
     */
    protected function getAddColumnInstructions(Table $table, Column $column): AlterInstructions
    {
        $instructions = new AlterInstructions();
        $instructions->addAlter(sprintf(
            'ADD %s %s %s',
            $this->quoteColumnName((string)$column->getName()),
            $this->getColumnSqlDefinition($column),
            $column->isIdentity() && $column->getGenerated() !== null && $this->useIdentity ?
                sprintf('GENERATED %s AS IDENTITY', (string)$column->getGenerated()) : '',
        ));

        if ($column->getComment()) {
            $instructions->addPostStep($this->getColumnCommentSqlDefinition($column, $table->getName()));
        }

        return $instructions;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function getRenameColumnInstructions(
        string $tableName,
        string $columnName,
        string $newColumnName,
    ): AlterInstructions {
        $parts = $this->getSchemaName($tableName);
        $sql = 'SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END AS column_exists
             FROM information_schema.columns
             WHERE table_schema = ? AND table_name = ? AND column_name = ?';
        $params = [
            $parts['schema'],
            $parts['table'],
            $columnName,
        ];
        $result = $this->query($sql, $params)->fetch('assoc');
        if (!$result || !(bool)$result['column_exists']) {
            throw new InvalidArgumentException("The specified column does not exist: $columnName");
        }

        $instructions = new AlterInstructions();
        $instructions->addPostStep(
            sprintf(
                'ALTER TABLE %s RENAME COLUMN %s TO %s',
                $this->quoteTableName($tableName),
                $this->quoteColumnName($columnName),
                $this->quoteColumnName($newColumnName),
            ),
        );

        return $instructions;
    }

    /**
     * @inheritDoc
     */
    protected function getChangeColumnInstructions(
        string $tableName,
        string $columnName,
        Column $newColumn,
    ): AlterInstructions {
        $quotedColumnName = $this->quoteColumnName($columnName);
        $instructions = new AlterInstructions();
        if ($newColumn->getType() === 'boolean') {
            $sql = sprintf('ALTER COLUMN %s DROP DEFAULT', $quotedColumnName);
            $instructions->addAlter($sql);
        }
        $sql = sprintf(
            'ALTER COLUMN %s TYPE %s',
            $quotedColumnName,
            $this->getColumnSqlDefinition($newColumn),
        );
        if (in_array($newColumn->getType(), ['smallinteger', 'integer', 'biginteger'], true)) {
            $sql .= sprintf(
                ' USING (%s::bigint)',
                $quotedColumnName,
            );
        }
        if (in_array($newColumn->getType(), ['uuid', 'nativeuuid', 'binaryuuid'])) {
            $sql .= sprintf(
                ' USING (%s::uuid)',
                $quotedColumnName,
            );
        }
        //NULL and DEFAULT cannot be set while changing column type
        $sql = preg_replace('/ NOT NULL/', '', $sql);
        $sql = preg_replace('/ NULL/', '', $sql);
        //If it is set, DEFAULT is the last definition
        $sql = preg_replace('/DEFAULT .*/', '', $sql);
        if ($newColumn->getType() === 'boolean') {
            $sql .= sprintf(
                ' USING (CASE WHEN %s IS NULL THEN NULL WHEN %s::int=0 THEN FALSE ELSE TRUE END)',
                $quotedColumnName,
                $quotedColumnName,
            );
        }
        $instructions->addAlter($sql);

        $column = $this->getColumn($tableName, $columnName);
        assert($column !== null, 'Column must exist');

        if ($this->useIdentity) {
            // process identity
            $sql = sprintf(
                'ALTER COLUMN %s',
                $quotedColumnName,
            );
            if ($newColumn->isIdentity() && $newColumn->getGenerated() !== null) {
                if ($column->isIdentity()) {
                    $sql .= sprintf(' SET GENERATED %s', (string)$newColumn->getGenerated());
                } else {
                    $sql .= sprintf(' ADD GENERATED %s AS IDENTITY', (string)$newColumn->getGenerated());
                }
            } else {
                $sql .= ' DROP IDENTITY IF EXISTS';
            }
            $instructions->addAlter($sql);
        }

        // process null
        $sql = sprintf(
            'ALTER COLUMN %s',
            $quotedColumnName,
        );

        if (!$newColumn->getIdentity() && !$column->getIdentity() && $newColumn->isNull()) {
            $sql .= ' DROP NOT NULL';
        } else {
            $sql .= ' SET NOT NULL';
        }

        $instructions->addAlter($sql);

        if ($newColumn->getDefault() !== null) {
            $instructions->addAlter(sprintf(
                'ALTER COLUMN %s SET %s',
                $quotedColumnName,
                $this->getDefaultValueDefinition($newColumn->getDefault(), (string)$newColumn->getType()),
            ));
        } elseif (!$newColumn->getIdentity()) {
            //drop default
            $instructions->addAlter(sprintf(
                'ALTER COLUMN %s DROP DEFAULT',
                $quotedColumnName,
            ));
        }

        // rename column
        if ($columnName !== $newColumn->getName()) {
            $instructions->addPostStep(sprintf(
                'ALTER TABLE %s RENAME COLUMN %s TO %s',
                $this->quoteTableName($tableName),
                $quotedColumnName,
                $this->quoteColumnName((string)$newColumn->getName()),
            ));
        }

        // change column comment if needed
        if ($newColumn->getComment()) {
            $instructions->addPostStep($this->getColumnCommentSqlDefinition($newColumn, $tableName));
        }

        return $instructions;
    }

    /**
     * @param string $tableName Table name
     * @param string $columnName Column name
     * @return ?\Migrations\Db\Table\Column
     */
    protected function getColumn(string $tableName, string $columnName): ?Column
    {
        $columns = $this->getColumns($tableName);
        foreach ($columns as $column) {
            if ($column->getName() === $columnName) {
                return $column;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    protected function getDropColumnInstructions(string $tableName, string $columnName): AlterInstructions
    {
        $alter = sprintf(
            'DROP COLUMN %s',
            $this->quoteColumnName($columnName),
        );

        return new AlterInstructions([$alter]);
    }

    /**
     * Get an array of indexes from a particular table.
     *
     * @param string $tableName Table name
     * @return array
     */
    protected function getIndexes(string $tableName): array
    {
        $dialect = $this->getSchemaDialect();
        $parts = $this->getSchemaName($tableName);

        [$query, $params] = $dialect->describeIndexSql($parts['table'], [
            'schema' => $parts['schema'],
            'database' => $this->getOption('database'),
        ]);
        $rows = $this->query($query, $params)->fetchAll('assoc');

        $indexes = [];
        foreach ($rows as $row) {
            if (!isset($indexes[$row['relname']])) {
                $indexes[$row['relname']] = [
                    'isPrimary' => false,
                    'columns' => [],
                ];
            }
            $indexes[$row['relname']]['columns'][] = $row['attname'];
            $indexes[$row['relname']]['isPrimary'] = $row['indisprimary'];
        }

        return $indexes;
    }

    /**
     * @inheritDoc
     */
    public function hasIndex(string $tableName, string|array $columns): bool
    {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        $indexes = $this->getIndexes($tableName);
        foreach ($indexes as $index) {
            if (array_diff($index['columns'], $columns) === array_diff($columns, $index['columns'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function hasIndexByName(string $tableName, string $indexName): bool
    {
        $indexes = $this->getIndexes($tableName);
        foreach ($indexes as $name => $index) {
            if ($name === $indexName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    protected function getAddIndexInstructions(Table $table, Index $index): AlterInstructions
    {
        $instructions = new AlterInstructions();
        $instructions->addPostStep($this->getIndexSqlDefinition($index, $table->getName()));

        return $instructions;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function getDropIndexByColumnsInstructions(string $tableName, $columns): AlterInstructions
    {
        $parts = $this->getSchemaName($tableName);

        if (is_string($columns)) {
            $columns = [$columns]; // str to array
        }

        $indexes = $this->getIndexes($tableName);
        foreach ($indexes as $indexName => $index) {
            $a = array_diff($columns, $index['columns']);
            if (!$a) {
                return new AlterInstructions([], [sprintf(
                    'DROP INDEX IF EXISTS %s',
                    '"' . ($parts['schema'] . '".' . $this->quoteColumnName($indexName)),
                )]);
            }
        }

        throw new InvalidArgumentException(sprintf(
            'The specified index on columns `%s` does not exist',
            implode(',', $columns),
        ));
    }

    /**
     * @inheritDoc
     */
    protected function getDropIndexByNameInstructions(string $tableName, string $indexName): AlterInstructions
    {
        $parts = $this->getSchemaName($tableName);

        $sql = sprintf(
            'DROP INDEX IF EXISTS %s',
            '"' . ($parts['schema'] . '".' . $this->quoteColumnName($indexName)),
        );

        return new AlterInstructions([], [$sql]);
    }

    /**
     * @inheritDoc
     */
    public function hasPrimaryKey(string $tableName, $columns, ?string $constraint = null): bool
    {
        $primaryKey = $this->getPrimaryKey($tableName);
        if (!$primaryKey) {
            return false;
        }

        if ($constraint) {
            return $primaryKey['constraint'] === $constraint;
        } else {
            if (is_string($columns)) {
                $columns = [$columns]; // str to array
            }
            $missingColumns = array_diff($columns, $primaryKey['columns']);

            return empty($missingColumns);
        }
    }

    /**
     * Get the primary key from a particular table.
     *
     * @param string $tableName Table name
     * @return array
     */
    public function getPrimaryKey(string $tableName): array
    {
        $indexes = $this->getIndexes($tableName);

        foreach ($indexes as $name => $index) {
            if ($index['isPrimary']) {
                $index['constraint'] = $name;

                return $index;
            }
        }

        return ['columns' => []];
    }

    /**
     * @inheritDoc
     */
    public function hasForeignKey(string $tableName, $columns, ?string $constraint = null): bool
    {
        $foreignKeys = $this->getForeignKeys($tableName);
        if ($constraint) {
            if (isset($foreignKeys[$constraint])) {
                return !empty($foreignKeys[$constraint]);
            }

            return false;
        }

        if (is_string($columns)) {
            $columns = [$columns];
        }

        foreach ($foreignKeys as $key) {
            if ($key['columns'] === $columns) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get an array of foreign keys from a particular table.
     *
     * @param string $tableName Table name
     * @return array
     */
    protected function getForeignKeys(string $tableName): array
    {
        $parts = $this->getSchemaName($tableName);
        $dialect = $this->getSchemaDialect();

        [$query, $params] = $dialect->describeForeignKeySql($parts['table'], [
            'schema' => $parts['schema'],
            'database' => $this->getOption('database'),
        ]);
        $rows = $this->query($query, $params)->fetchAll('assoc');
        $foreignKeys = [];
        foreach ($rows as $row) {
            $name = $row['name'];
            $foreignKeys[$name]['table'] = $parts['table'];
            $foreignKeys[$name]['columns'][] = $row['column_name'];
            $foreignKeys[$name]['referenced_table'] = $row['references_table'];
            $foreignKeys[$name]['referenced_columns'][] = $row['references_field'];
        }
        foreach ($foreignKeys as $name => $key) {
            $foreignKeys[$name]['columns'] = array_values(array_unique($key['columns']));
            $foreignKeys[$name]['referenced_columns'] = array_values(array_unique($key['referenced_columns']));
        }

        return $foreignKeys;
    }

    /**
     * @inheritDoc
     */
    protected function getAddForeignKeyInstructions(Table $table, ForeignKey $foreignKey): AlterInstructions
    {
        $alter = sprintf(
            'ADD %s',
            $this->getForeignKeySqlDefinition($foreignKey, $table->getName()),
        );

        return new AlterInstructions([$alter]);
    }

    /**
     * @inheritDoc
     */
    protected function getDropForeignKeyInstructions($tableName, $constraint): AlterInstructions
    {
        $alter = sprintf(
            'DROP CONSTRAINT %s',
            $this->quoteColumnName($constraint),
        );

        return new AlterInstructions([$alter]);
    }

    /**
     * @inheritDoc
     */
    protected function getDropForeignKeyByColumnsInstructions(string $tableName, array $columns): AlterInstructions
    {
        $instructions = new AlterInstructions();

        $matches = [];
        $foreignKeys = $this->getForeignKeys($tableName);
        foreach ($foreignKeys as $name => $key) {
            if ($key['columns'] === $columns) {
                $matches[] = $name;
            }
        }

        if (!$matches) {
            throw new InvalidArgumentException(sprintf(
                'No foreign key on column(s) `%s` exists',
                implode(', ', $columns),
            ));
        }

        foreach ($matches as $name) {
            $instructions->merge(
                $this->getDropForeignKeyInstructions($tableName, $name),
            );
        }

        return $instructions;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Migrations\Db\Adapter\UnsupportedColumnTypeException
     */
    public function getSqlType(Literal|string $type, ?int $limit = null): array
    {
        $type = (string)$type;
        switch ($type) {
            case static::PHINX_TYPE_TEXT:
            case static::PHINX_TYPE_TIME:
            case static::PHINX_TYPE_DATE:
            case static::PHINX_TYPE_BOOLEAN:
            case static::PHINX_TYPE_JSON:
            case static::PHINX_TYPE_JSONB:
            case static::PHINX_TYPE_UUID:
            case static::PHINX_TYPE_CIDR:
            case static::PHINX_TYPE_INET:
            case static::PHINX_TYPE_MACADDR:
            case static::PHINX_TYPE_TIMESTAMP:
            case static::PHINX_TYPE_INTEGER:
                return ['name' => $type];
            case static::PHINX_TYPE_TINY_INTEGER:
                return ['name' => 'smallint'];
            case static::PHINX_TYPE_SMALL_INTEGER:
                return ['name' => 'smallint'];
            case static::PHINX_TYPE_DECIMAL:
                return ['name' => $type, 'precision' => 18, 'scale' => 0];
            case static::PHINX_TYPE_DOUBLE:
                return ['name' => 'double precision'];
            case static::PHINX_TYPE_STRING:
                return ['name' => 'character varying', 'limit' => 255];
            case static::PHINX_TYPE_CHAR:
                return ['name' => 'character', 'limit' => 255];
            case static::PHINX_TYPE_BIG_INTEGER:
                return ['name' => 'bigint'];
            case static::PHINX_TYPE_FLOAT:
                return ['name' => 'real'];
            case static::PHINX_TYPE_DATETIME:
                return ['name' => 'timestamp'];
            case static::PHINX_TYPE_BINARYUUID:
            case static::PHINX_TYPE_NATIVEUUID:
                return ['name' => 'uuid'];
            case static::PHINX_TYPE_BLOB:
            case static::PHINX_TYPE_BINARY:
                return ['name' => 'bytea'];
            case static::PHINX_TYPE_INTERVAL:
                return ['name' => 'interval'];
            // Geospatial database types
            // Spatial storage in Postgres is done via the PostGIS extension,
            // which enables the use of the "geography" type in combination
            // with SRID 4326.
            case static::PHINX_TYPE_GEOMETRY:
                return ['name' => 'geography', 'type' => 'geometry', 'srid' => 4326];
            case static::PHINX_TYPE_POINT:
                return ['name' => 'geography', 'type' => 'point', 'srid' => 4326];
            case static::PHINX_TYPE_LINESTRING:
                return ['name' => 'geography', 'type' => 'linestring', 'srid' => 4326];
            case static::PHINX_TYPE_POLYGON:
                return ['name' => 'geography', 'type' => 'polygon', 'srid' => 4326];
            default:
                if ($this->isArrayType($type)) {
                    return ['name' => $type];
                }
                // Return array type
                throw new UnsupportedColumnTypeException('Column type `' . $type . '` is not supported by Postgresql.');
        }
    }

    /**
     * Returns Phinx type by SQL type
     *
     * @param string $sqlType SQL type
     * @throws \Migrations\Db\Adapter\UnsupportedColumnTypeException
     * @return string Phinx type
     */
    public function getPhinxType(string $sqlType): string
    {
        switch ($sqlType) {
            case 'character varying':
            case 'varchar':
                return static::PHINX_TYPE_STRING;
            case 'character':
            case 'char':
                return static::PHINX_TYPE_CHAR;
            case 'text':
                return static::PHINX_TYPE_TEXT;
            case 'json':
                return static::PHINX_TYPE_JSON;
            case 'jsonb':
                return static::PHINX_TYPE_JSONB;
            case 'smallint':
                return static::PHINX_TYPE_SMALL_INTEGER;
            case 'int':
            case 'int4':
            case 'integer':
                return static::PHINX_TYPE_INTEGER;
            case 'decimal':
            case 'numeric':
                return static::PHINX_TYPE_DECIMAL;
            case 'bigint':
            case 'int8':
                return static::PHINX_TYPE_BIG_INTEGER;
            case 'real':
            case 'float4':
                return static::PHINX_TYPE_FLOAT;
            case 'double precision':
                return static::PHINX_TYPE_DOUBLE;
            case 'bytea':
                return static::PHINX_TYPE_BINARY;
            case 'interval':
                return static::PHINX_TYPE_INTERVAL;
            case 'time':
            case 'timetz':
            case 'time with time zone':
            case 'time without time zone':
                return static::PHINX_TYPE_TIME;
            case 'date':
                return static::PHINX_TYPE_DATE;
            case 'timestamp':
            case 'timestamptz':
            case 'timestamp with time zone':
            case 'timestamp without time zone':
                return static::PHINX_TYPE_DATETIME;
            case 'bool':
            case 'boolean':
                return static::PHINX_TYPE_BOOLEAN;
            case 'uuid':
                return static::PHINX_TYPE_UUID;
            case 'cidr':
                return static::PHINX_TYPE_CIDR;
            case 'inet':
                return static::PHINX_TYPE_INET;
            case 'macaddr':
                return static::PHINX_TYPE_MACADDR;
            default:
                throw new UnsupportedColumnTypeException(
                    'Column type `' . $sqlType . '` is not supported by Postgresql.',
                );
        }
    }

    /**
     * @inheritDoc
     */
    public function createDatabase(string $name, array $options = []): void
    {
        $charset = $options['charset'] ?? 'utf8';
        $this->execute(sprintf("CREATE DATABASE %s WITH ENCODING = '%s'", $name, $charset));
    }

    /**
     * @inheritDoc
     */
    public function hasDatabase(string $name): bool
    {
        $sql = sprintf("SELECT count(*) FROM pg_database WHERE datname = '%s'", $name);
        $result = $this->fetchRow($sql);
        if (!$result) {
            return false;
        }

        return $result['count'] > 0;
    }

    /**
     * @inheritDoc
     */
    public function dropDatabase($name): void
    {
        $this->disconnect();
        $this->execute(sprintf('DROP DATABASE IF EXISTS %s', $name));
        $this->createdTables = [];
        $this->connect();
    }

    /**
     * Gets the PostgreSQL Column Definition for a Column object.
     *
     * @param \Migrations\Db\Table\Column $column Column
     * @return string
     */
    protected function getColumnSqlDefinition(Column $column): string
    {
        $buffer = [];

        if ($column->isIdentity() && (!$this->useIdentity || $column->getGenerated() === null)) {
            if ($column->getType() === 'smallinteger') {
                $buffer[] = 'SMALLSERIAL';
            } elseif ($column->getType() === 'biginteger') {
                $buffer[] = 'BIGSERIAL';
            } else {
                $buffer[] = 'SERIAL';
            }
        } elseif ($column->getType() instanceof Literal) {
            $buffer[] = (string)$column->getType();
        } else {
            $sqlType = $this->getSqlType($column->getType(), $column->getLimit());
            $buffer[] = strtoupper($sqlType['name']);

            // integers cant have limits in postgres
            if ($sqlType['name'] === static::PHINX_TYPE_DECIMAL && ($column->getPrecision() || $column->getScale())) {
                $buffer[] = sprintf(
                    '(%s, %s)',
                    $column->getPrecision() ?: $sqlType['precision'],
                    $column->getScale() ?: $sqlType['scale'],
                );
            } elseif ($sqlType['name'] === self::PHINX_TYPE_GEOMETRY) {
                // geography type must be written with geometry type and srid, like this: geography(POLYGON,4326)
                $buffer[] = sprintf(
                    '(%s,%s)',
                    strtoupper($sqlType['type']),
                    $column->getSrid() ?: $sqlType['srid'],
                );
            } elseif (in_array($sqlType['name'], [self::PHINX_TYPE_TIME, self::PHINX_TYPE_TIMESTAMP], true)) {
                if (is_numeric($column->getPrecision())) {
                    $buffer[] = sprintf('(%s)', (string)$column->getPrecision());
                }

                if ($column->isTimezone()) {
                    $buffer[] = strtoupper('with time zone');
                }
            } elseif (
                !in_array($column->getType(), [
                    self::PHINX_TYPE_TINY_INTEGER,
                    self::PHINX_TYPE_SMALL_INTEGER,
                    self::PHINX_TYPE_INTEGER,
                    self::PHINX_TYPE_BIG_INTEGER,
                    self::PHINX_TYPE_BOOLEAN,
                    self::PHINX_TYPE_TEXT,
                    self::PHINX_TYPE_BINARY,
                ], true)
            ) {
                if ($column->getLimit() || isset($sqlType['limit'])) {
                    $buffer[] = sprintf('(%s)', $column->getLimit() ?: $sqlType['limit']);
                }
            }
        }

        $buffer[] = $column->isNull() ? 'NULL' : 'NOT NULL';

        if ($column->getDefault() !== null) {
            $buffer[] = $this->getDefaultValueDefinition($column->getDefault(), (string)$column->getType());
        }

        return implode(' ', $buffer);
    }

    /**
     * Gets the PostgreSQL Column Comment Definition for a column object.
     *
     * @param \Migrations\Db\Table\Column $column Column
     * @param string $tableName Table name
     * @return string
     */
    protected function getColumnCommentSqlDefinition(Column $column, string $tableName): string
    {
        $comment = (string)$column->getComment();
        // passing 'null' is to remove column comment
        $comment = strcasecmp($comment, 'NULL') !== 0
                 ? $this->quoteString($comment)
                 : 'NULL';

        return sprintf(
            'COMMENT ON COLUMN %s.%s IS %s;',
            $this->quoteTableName($tableName),
            $this->quoteColumnName((string)$column->getName()),
            $comment,
        );
    }

    /**
     * Gets the PostgreSQL Index Definition for an Index object.
     *
     * @param \Migrations\Db\Table\Index $index Index
     * @param string $tableName Table name
     * @return string
     */
    protected function getIndexSqlDefinition(Index $index, string $tableName): string
    {
        $parts = $this->getSchemaName($tableName);
        $columnNames = (array)$index->getColumns();

        if (is_string($index->getName())) {
            $indexName = $index->getName();
        } else {
            $indexName = sprintf('%s_%s', $parts['table'], implode('_', $columnNames));
        }

        $order = $index->getOrder() ?? [];
        $columnNames = array_map(function ($columnName) use ($order) {
            $ret = '"' . $columnName . '"';
            if (isset($order[$columnName])) {
                $ret .= ' ' . $order[$columnName];
            }

            return $ret;
        }, $columnNames);

        $include = $index->getInclude();
        $includedColumns = $include ? sprintf(' INCLUDE ("%s")', implode('","', $include)) : '';

        $createIndexSentence = 'CREATE %sINDEX%s %s ON %s ';
        if ($index->getType() === self::GIN_INDEX_TYPE) {
            $createIndexSentence .= ' USING ' . $index->getType() . '(%s) %s;';
        } else {
            $createIndexSentence .= '(%s)%s%s;';
        }
        $where = (string)$index->getWhere();
        if ($where) {
            $where = ' WHERE ' . $where;
        }

        return sprintf(
            $createIndexSentence,
            $index->getType() === Index::UNIQUE ? 'UNIQUE ' : '',
            $index->getConcurrently() ? ' CONCURRENTLY' : '',
            $this->quoteColumnName((string)$indexName),
            $this->quoteTableName($tableName),
            implode(',', $columnNames),
            $includedColumns,
            $where,
        );
    }

    /**
     * Gets the MySQL Foreign Key Definition for an ForeignKey object.
     *
     * @param \Migrations\Db\Table\ForeignKey $foreignKey Foreign key
     * @param string $tableName Table name
     * @return string
     */
    protected function getForeignKeySqlDefinition(ForeignKey $foreignKey, string $tableName): string
    {
        $parts = $this->getSchemaName($tableName);

        $constraintName = $foreignKey->getName() ?: (
            $parts['table'] . '_' . implode('_', $foreignKey->getColumns()) . '_fkey'
        );
        $def = ' CONSTRAINT ' . $this->quoteColumnName($constraintName) .
        ' FOREIGN KEY ("' . implode('", "', $foreignKey->getColumns()) . '")' .
        " REFERENCES {$this->quoteTableName($foreignKey->getReferencedTable()->getName())} (\"" .
        implode('", "', $foreignKey->getReferencedColumns()) . '")';
        if ($foreignKey->getOnDelete()) {
            $def .= " ON DELETE {$foreignKey->getOnDelete()}";
        }
        if ($foreignKey->getOnUpdate()) {
            $def .= " ON UPDATE {$foreignKey->getOnUpdate()}";
        }
        if ($foreignKey->getDeferrableMode()) {
            $def .= " {$foreignKey->getDeferrableMode()}";
        }

        return $def;
    }

    /**
     * @inheritDoc
     */
    public function createSchemaTable(): void
    {
        // Create the public/custom schema if it doesn't already exist
        if ($this->hasSchema($this->getGlobalSchemaName()) === false) {
            $this->createSchema($this->getGlobalSchemaName());
        }

        $this->setSearchPath();

        parent::createSchemaTable();
    }

    /**
     * @inheritDoc
     */
    public function getVersions(): array
    {
        $this->setSearchPath();

        return parent::getVersions();
    }

    /**
     * @inheritDoc
     */
    public function getVersionLog(): array
    {
        $this->setSearchPath();

        return parent::getVersionLog();
    }

    /**
     * Creates the specified schema.
     *
     * @param string $schemaName Schema Name
     * @return void
     */
    public function createSchema(string $schemaName = 'public'): void
    {
        // from postgres 9.3 we can use "CREATE SCHEMA IF NOT EXISTS schema_name"
        $sql = sprintf('CREATE SCHEMA IF NOT EXISTS %s', $this->quoteSchemaName($schemaName));
        $this->execute($sql);
    }

    /**
     * Checks to see if a schema exists.
     *
     * @param string $schemaName Schema Name
     * @return bool
     */
    public function hasSchema(string $schemaName): bool
    {
        $sql = 'SELECT count(*) FROM pg_namespace WHERE nspname = ?';
        $result = $this->query($sql, [$schemaName])->fetch('assoc');
        if (!$result) {
            return false;
        }

        return $result['count'] > 0;
    }

    /**
     * Drops the specified schema table.
     *
     * @param string $schemaName Schema name
     * @return void
     */
    public function dropSchema(string $schemaName): void
    {
        $sql = sprintf('DROP SCHEMA IF EXISTS %s CASCADE', $this->quoteSchemaName($schemaName));
        $this->execute($sql);

        foreach ($this->createdTables as $idx => $createdTable) {
            if ($this->getSchemaName($createdTable)['schema'] === $this->quoteSchemaName($schemaName)) {
                unset($this->createdTables[$idx]);
            }
        }
    }

    /**
     * Drops all schemas.
     *
     * @return void
     */
    public function dropAllSchemas(): void
    {
        foreach ($this->getAllSchemas() as $schema) {
            $this->dropSchema($schema);
        }
    }

    /**
     * Returns schemas.
     *
     * @return array
     */
    public function getAllSchemas(): array
    {
        $sql = "SELECT schema_name
                FROM information_schema.schemata
                WHERE schema_name <> 'information_schema' AND schema_name !~ '^pg_'";
        $items = $this->fetchAll($sql);
        $schemaNames = [];
        foreach ($items as $item) {
            $schemaNames[] = $item['schema_name'];
        }

        return $schemaNames;
    }

    /**
     * @inheritDoc
     */
    public function getColumnTypes(): array
    {
        return array_merge(parent::getColumnTypes(), static::$specificColumnTypes);
    }

    /**
     * @inheritDoc
     */
    public function isValidColumnType(Column $column): bool
    {
        // If not a standard column type, maybe it is array type?
        return parent::isValidColumnType($column) || $this->isArrayType($column->getType());
    }

    /**
     * Check if the given column is an array of a valid type.
     *
     * @param string|\Migrations\Db\Literal $columnType Column type
     * @return bool
     */
    protected function isArrayType(string|Literal $columnType): bool
    {
        if (!preg_match('/^([a-z]+)(?:\[\]){1,}$/', (string)$columnType, $matches)) {
            return false;
        }

        $baseType = $matches[1];

        return in_array($baseType, $this->getColumnTypes(), true);
    }

    /**
     * @param string $tableName Table name
     * @return array
     */
    protected function getSchemaName(string $tableName): array
    {
        $schema = $this->getGlobalSchemaName();
        $table = $tableName;
        if (strpos($tableName, '.') !== false) {
            [$schema, $table] = explode('.', $tableName);
        }

        return [
            'schema' => $schema,
            'table' => $table,
        ];
    }

    /**
     * Gets the schema name.
     *
     * @return string
     */
    protected function getGlobalSchemaName(): string
    {
        $options = $this->getOptions();
        $config = $options['connection']->config() ?? [];

        return empty($config['schema']) ? 'public' : $config['schema'];
    }

    /**
     * @inheritDoc
     */
    public function castToBool($value): mixed
    {
        return (bool)$value ? 'TRUE' : 'FALSE';
    }

    /**
     * Sets search path of schemas to look through for a table
     *
     * @return void
     */
    public function setSearchPath(): void
    {
        $this->execute(
            sprintf(
                'SET search_path TO %s,"$user",public',
                $this->quoteSchemaName($this->getGlobalSchemaName()),
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function insert(Table $table, array $row): void
    {
        $sql = sprintf(
            'INSERT INTO %s ',
            $this->quoteTableName($table->getName()),
        );
        $columns = array_keys($row);
        $sql .= '(' . implode(', ', array_map($this->quoteColumnName(...), $columns)) . ')';

        foreach ($row as $column => $value) {
            if (is_bool($value)) {
                $row[$column] = $this->castToBool($value);
            }
        }

        $override = '';
        if ($this->useIdentity) {
            $override = self::OVERRIDE_SYSTEM_VALUE . ' ';
        }

        if ($this->isDryRunEnabled()) {
            $sql .= ' ' . $override . 'VALUES (' . implode(', ', array_map($this->quoteValue(...), $row)) . ');';
            $this->io->out($sql);
        } else {
            $values = [];
            $vals = [];
            foreach ($row as $value) {
                $placeholder = '?';
                if ($value instanceof Literal || $value instanceof PhinxLiteral) {
                    $placeholder = (string)$value;
                }
                $values[] = $placeholder;
                if ($placeholder === '?') {
                    $vals[] = $value;
                }
            }
            $sql .= ' ' . $override . 'VALUES (' . implode(',', $values) . ')';
            $this->getConnection()->execute($sql, $vals);
        }
    }

    /**
     * @inheritDoc
     */
    public function bulkinsert(Table $table, array $rows): void
    {
        $sql = sprintf(
            'INSERT INTO %s ',
            $this->quoteTableName($table->getName()),
        );
        $current = current($rows);
        /** @var array<string> $keys */
        $keys = array_keys($current);

        $override = '';
        if ($this->useIdentity) {
            $override = self::OVERRIDE_SYSTEM_VALUE . ' ';
        }

        $sql .= '(' . implode(', ', array_map($this->quoteColumnName(...), $keys)) . ') ' . $override . 'VALUES ';

        if ($this->isDryRunEnabled()) {
            $values = array_map(function ($row) {
                return '(' . implode(', ', array_map($this->quoteValue(...), $row)) . ')';
            }, $rows);
            $sql .= implode(', ', $values) . ';';
            $this->io->out($sql);
        } else {
            $vals = [];
            $queries = [];
            foreach ($rows as $row) {
                $values = [];
                foreach ($row as $v) {
                    $placeholder = '?';
                    if ($v instanceof Literal || $v instanceof PhinxLiteral) {
                        $placeholder = (string)$v;
                    }
                    $values[] = $placeholder;
                    if ($placeholder == '?') {
                        if ($v instanceof DateTime) {
                            $vals[] = $v->toDateTimeString();
                        } elseif ($v instanceof Date) {
                            $vals[] = $v->toDateString();
                        } elseif (is_bool($v)) {
                            $vals[] = $this->castToBool($v);
                        } else {
                            $vals[] = $v;
                        }
                    }
                }
                $query = '(' . implode(', ', $values) . ')';
                $queries[] = $query;
            }
            $sql .= implode(',', $queries);
            $this->getConnection()->execute($sql, $vals);
        }
    }

    /**
     * Get the adapter type name
     *
     * @return string
     */
    public function getAdapterType(): string
    {
        // Hardcoded because the parent implementation
        // reads an option that is based off of Database\Driver
        // names which is postgres, but pgsql is required for
        // compatibility.
        return 'pgsql';
    }
}
