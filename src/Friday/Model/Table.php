<?php
/**
 * IronPHP : PHP Development Framework
 * Copyright (c) IronPHP (https://github.com/IronPHP/IronPHP).
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) IronPHP (https://github.com/IronPHP/IronPHP)
 *
 * @link
 * @since         0.0.1
 *
 * @license       MIT License (https://opensource.org/licenses/mit-license.php)
 * @auther        Gaurang Parmar <gaurangkumarp@gmail.com>
 */

namespace Friday\Model;

class Table
{
    /**
     * Name of the table as it can be found in the database.
     *
     * @var string
     */
    protected $_table;

    /**
     * Connection instance.
     *
     * @var \Cake\Database\Connection
     */
    protected $_connection;

    /**
     * The schema object containing a description of this table fields.
     *
     * @var \Cake\Database\Schema\TableSchema
     */
    protected $_schema;

    /**
     * The name of the field that represents the primary key in the table.
     *
     * @var string|array
     */
    protected $_primaryKey;

    /**
     * The name of the field that represents a human readable representation of a row.
     *
     * @var string
     */
    protected $_displayField;

    /**
     * The associations container for this Table.
     *
     * @var \Cake\ORM\AssociationCollection
     */
    protected $_associations;

    /**
     * BehaviorRegistry for this table.
     *
     * @var \Cake\ORM\BehaviorRegistry
     */
    protected $_behaviors;

    /**
     * The name of the class that represent a single row for this table.
     *
     * @var string
     */
    protected $_entityClass;

    /**
     * Registry key used to create this table object.
     *
     * @var string
     */
    protected $_registryAlias;

    /**
     * Instance of MYSQLi.
     *
     * @var \MYSQLi
     */
    private $connection;

    /**
     * Connection error no.
     *
     * @var int
     */
    private $connect_errno;

    /**
     * Connection error name.
     *
     * @var string
     */
    private $connect_error;

    /**
     * Query error no.
     *
     * @var int
     */
    private $errno;

    /**
     * Query error name.
     *
     * @var string
     */
    private $error;

    /**
     * Table name.
     *
     * @var string
     */
    private $table;

    /**
     * WHERE clause.
     *
     * @var string
     */
    private $where;

    /**
     * ORDER BY clause.
     *
     * @var array
     */
    private $order;

    /**
     * Instance of the Pagination.
     *
     * @var \Friday\Helper\Pagination
     */
    private $pagination = null;

    /**
     * LIMIT clause.
     *
     * @var string
     */
    private $limit = null;

    /**
     * Create a new Table instance.
     *
     * @param array $config
     *
     * @return \MYSQLi
     */
    public function __construct(array $config = [])
    {
        $mysqli = new \mysqli($config['host'], $config['username'], $config['password'], $config['database'], $config['port']);

        /*
         * This is the 'official' OO way to do it,
         * BUT $connect_error was broken until PHP 5.2.9 and 5.3.0.
         */
        $this->connect_errno = $mysqli->connect_errno;
        $this->connect_error = $mysqli->connect_error;
        if ($mysqli->connect_errno) {
            die('Connect Error: '.$this->connect_error);
        }
        $this->connection = $mysqli;
        /*
        if (!empty($config['registryAlias'])) {
            $this->setRegistryAlias($config['registryAlias']);
        }
        if (!empty($config['table'])) {
            $this->setTable($config['table']);
        }
        if (!empty($config['alias'])) {
            $this->setAlias($config['alias']);
        }
        if (!empty($config['connection'])) {
            $this->setConnection($config['connection']);
        }
        if (!empty($config['schema'])) {
            $this->setSchema($config['schema']);
        }
        if (!empty($config['entityClass'])) {
            $this->setEntityClass($config['entityClass']);
        }
        $eventManager = $behaviors = $associations = null;
        if (!empty($config['eventManager'])) {
            $eventManager = $config['eventManager'];
        }
        if (!empty($config['behaviors'])) {
            $behaviors = $config['behaviors'];
        }
        if (!empty($config['associations'])) {
            $associations = $config['associations'];
        }
        if (!empty($config['validator'])) {
            if (!is_array($config['validator'])) {
                $this->setValidator(static::DEFAULT_VALIDATOR, $config['validator']);
            } else {
                foreach ($config['validator'] as $name => $validator) {
                    $this->setValidator($name, $validator);
                }
            }
        }
        $this->_eventManager = $eventManager ?: new EventManager();
        $this->_behaviors = $behaviors ?: new BehaviorRegistry();
        $this->_behaviors->setTable($this);
        $this->_associations = $associations ?: new AssociationCollection();

        $this->initialize($config);
        $this->_eventManager->on($this);
        $this->dispatchEvent('Model.initialize');
        */
    }

    /**
     * Initialize a table instance. Called after the constructor.
     *
     * You can use this method to define associations, attach behaviors
     * define validation and do any other initialization logic you need.
     *
     * ```
     *  public function initialize(array $config)
     *  {
     *      $this->belongsTo('Users');
     *      $this->belongsToMany('Tagging.Tags');
     *      $this->setPrimaryKey('something_else');
     *  }
     * ```
     *
     * @param array $config Configuration options passed to the constructor
     *
     * @return void
     */
    public function initialize(array $config)
    {
    }

    /**
     * Sets the database table name.
     *
     * @param string                    $table
     * @param \Friday\Helper\Pagination $pagination
     *
     * @return $this
     */
    public function setTable($table, $pagination)
    {
        $this->pagination = $pagination;
        $this->table = $table;

        return $this;
    }

    /**
     * Returns the database table name.
     *
     * @return string
     */
    public function getTable()
    {
        if ($this->table === null) {
            /*
            $table = namespaceSplit(get_class($this));
            $table = substr(end($table), 0, -5);
            if (!$table) {
                $table = $this->getAlias();
            }
            $this->_table = Inflector::underscore($table);
            */
        }

        return $this->table;
    }

    /**
     * Get field from table.
     *
     * @param array|null $field
     * @rturn  array
     */
    public function num_rows()
    {
        $sql = $this->query('select');
        $result = $this->execute($sql);
        $data = $result->num_rows;

        return $data;
    }

    /**
     * Get field from table.
     *
     * @param array|null $field
     * @rturn  array
     */
    public function get($fields = null)
    {
        $sql = $this->query('select', $fields);
        $result = $this->execute($sql);
        $data = $result->fetch_array(MYSQLI_ASSOC);

        return $data;
    }

    /**
     * Get all fields from table.
     *
     * @param array|null $field
     * @rturn  array
     */
    public function getAll($fields = null)
    {
        $sql = $this->query('select', $fields);
        $result = $this->execute($sql);
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $data[] = $row;
        }

        return $data;

        /*
        $key = ['id']; //(array)$this->getPrimaryKey();
        $alias = 'sid';//$this->getAlias();
        foreach ($key as $index => $keyname) {
            $key[$index] = $alias . '.' . $keyname;
        }
        $primaryKey = ['mid'];//(array)$primaryKey;
        if (count($key) !== count($primaryKey)) {
            $primaryKey = $primaryKey ?: [null];
            $primaryKey = array_map(function ($key) {
                return var_export($key, true);
            }, $primaryKey);

            echo sprintf(
                'Record not found in table "%s" with primary key [%s]',
                $this->getTable(),
                implode($primaryKey, ', ')
            );
        } // error handling
        $conditions = array_combine($key, $primaryKey);

        $cacheConfig = isset($options['cache']) ? $options['cache'] : false;
        $cacheKey = isset($options['key']) ? $options['key'] : false;
        $finder = isset($options['finder']) ? $options['finder'] : 'all';
        unset($options['key'], $options['cache'], $options['finder']);

        $query = $this->find($finder, $options)->where($conditions);

        if ($cacheConfig) {
            if (!$cacheKey) {
                $cacheKey = sprintf(
                    'get:%s.%s%s',
                    $this->getConnection()->configName(),
                    $this->getTable(),
                    json_encode($primaryKey)
                );
            }
            $query->cache($cacheKey, $cacheConfig);
        }

        return $query->firstOrFail();
        */
    }

    /**
     * Get paginated fields from table.
     *
     * @param array|null $limit
     * @rturn  array
     */
    public function getPaginated($limit = 1, $fields = null)
    {
        $sql = $this->query('select', $fields, ['count'=>null, 'field'=>'num']);
        $result = $this->execute($sql);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $total = $row['num'];
        $this->pagination->initialize($limit, $total);

        return $this->limit($limit, $this->pagination->getStartPoint())->getAll();
    }

    /**
     * Add data to table.
     *
     * @param string|null $field
     * @rturn  bool
     */
    public function add()
    {
        $field = func_get_args();
        if (func_num_args() == 0) {
            echo 'no data to save'; //no argument
            exit;
        } elseif (func_num_args() == 1) {
            if (is_array($field[0])) {
                $data = $field[0]; //single array with all data
            } else {
                $data = $field[0]; //single data without field //string with all/single data
            }
        } else {
            $data = $field; //more than 1 data without field //string with all/single data
        }
        $sql = $this->query('insert', $data);
        $result = $this->execute($sql);

        return $result;
    }

    /**
     * Update data to table.
     *
     * @param string|null $field
     * @rturn  bool
     */
    public function update()
    {
        $field = func_get_args();
        if (func_num_args() == 0) {
            echo 'no data to save'; //no argument
            exit;
        } elseif (func_num_args() == 1) {
            if (is_array($field[0])) {
                $data = $field[0]; //single array with all data
            } else {
                $data = $field[0]; //sql fraction
            }
        } else {
            echo 'invalid data';
            exit;
        }
        $sql = $this->query('update', $data);
        $result = $this->execute($sql);

        return $result;
    }

    /**
     * Delete data from table.
     *
     * @rturn  bool
     */
    public function delete()
    {
        if (func_num_args() != 0) {
            echo 'invalid';
            exit;
        }
        $sql = $this->query('delete');
        $result = $this->execute($sql);

        return $result;
    }

    /**
     * Create WHERE clause.
     *
     * @param array  $where
     * @param string $glue
     *
     * @return $this
     */
    public function where($where, $glue = 'AND')
    {
        if (is_array($where) && count($where) != 0) {
            foreach ($where as $field => $value) {
                $array[] = " `$field` = ".((is_string($value) ? "'$value'" : $value));
            }
            $this->where = ' WHERE'.implode(" $glue", $array);
        } elseif (is_string($where) && trim($where) != '') {
            $where = trim($where);
            $where = trim($where, 'WHERE ');
            $where = rtrim($where);
            $this->where = ' WHERE '.$where;
        }

        return $this;
    }

    /**
     * Create ORDER BY clause.
     *
     * @param string $field
     * @param string $order
     *
     * @return $this
     */
    public function orderBy($field, $order = 'ASC')
    {
        if (is_string($field) && trim($field) != '') {
            $field = trim($field);
            $field = ltrim($field, 'ORDER BY ');
            $this->order = ' ORDER BY `'.$field.'`'.(($order == 'DESC') ? ' DESC' : ' ASC');
        }

        return $this;
    }

    /**
     * Create LIMIT clause.
     *
     * @param int $start
     * @param int $limit
     *
     * @return $this
     */
    public function limit($limit, $start = null)
    {
        if (is_int($limit)) {
            $build = $limit;
            if (is_int($start)) {
                $build = $start.', '.$limit;
            }
            $this->limit = ' LIMIT '.$build;
        }

        return $this;
    }

    /**
     * Create sql query.
     *
     * @param string      $type
     * @param string|null $field
     *
     * @return string
     */
    public function query($type, $field = null, $extra = null)
    {
        //SELECT COUNT(*) as `num`
        if ($type == 'select') {
            if ($field == null) {
                $field = '*';
            } elseif (is_array($field)) {
                foreach ($field as $i => $value) {
                    $field[$i] = '`'.trim($value).'`';
                }
                $field = trim(implode(' ,', $field));
            }
            $ex = '';
            if ($extra != null && is_array($extra)) {
                foreach ($extra as $i => $v) {
                    if ($i == 'count') {
                        $ex .= 'COUNT';
                        if ($v == null) {
                            $ex .= '(*)';
                        }
                    }
                    if ($i == 'field') {
                        $ex .= ' as `'.$v.'`';
                    }
                }
                $sql = "SELECT $ex FROM `".$this->getTable().'` '.$this->where;
            } else {
                $sql = "SELECT $field FROM `".$this->getTable().'` '.$this->where.$this->order.$this->limit;
            }
        } elseif ($type == 'insert') {
            if (is_array($field)) {
                $keys = array_keys($field);
                if ($keys[0] === 0) {
                    $keys = '';
                } else {
                    foreach ($keys as $i => $key) {
                        $keys[$i] = ' `'.trim($key).'`';
                    }
                    $keys = implode(',', $keys);
                }
                $values = array_values($field);
                foreach ($values as $i => $val) {
                    $values[$i] = is_string($val) ? "'$val'" : $val;
                }
                $values = implode(' ,', $values);
            } else {
                $keys = '';
                $values = trim($field);
            }
            $values = "($values)";
            $keys = ($keys !== '') ? "($keys)" : '';
            $sql = 'INSERT INTO `'.$this->getTable()."` $keys VALUES $values";
        } elseif ($type == 'update') {
            if (is_array($field)) {
                foreach ($field as $key => $value) {
                    $values[] = " `$key` = ".(is_string($value) ? "'$value'" : $value);
                }
                $values = implode(' ,', $values);
            } else {
                $values = trim($field);
            }
            $sql = 'UPDATE `'.$this->getTable()."` SET $values ".$this->where;
        } elseif ($type == 'delete') {
            $sql = 'DELETE FROM `'.$this->getTable().'` '.$this->where;
        }

        return $sql;
    }

    /**
     * Run sql query.
     *
     * @param string $sql
     *
     * @return string
     */
    public function execute($sql)
    {
        $result = $this->connection->query($sql);
        $this->errno = $this->connection->errno;
        $this->error = $this->connection->error;
        if ($this->connection->errno) {
            echo 'query error: '.$this->error;
        }
        if ($this->connection->errno == 1054) {
            echo 'database table not set properly';
            exit;
        }

        return $result;
    }

    /**
     * Sets the connection instance.
     *
     * @param \Cake\Database\Connection|\Cake\Datasource\ConnectionInterface $connection The connection instance
     *
     * @return $this
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->_connection = $connection;

        return $this;
    }

    /**
     * Returns the connection instance.
     *
     * @return \Cake\Database\Connection
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Returns the schema table object describing this table's properties.
     *
     * @return \Cake\Database\Schema\TableSchema
     */
    public function getSchema()
    {
        if ($this->_schema === null) {
            $this->_schema = $this->_initializeSchema(
                $this->getConnection()
                    ->getSchemaCollection()
                    ->describe($this->getTable())
            );
        }

        return $this->_schema;
    }

    /**
     * Sets the schema table object describing this table's properties.
     *
     * If an array is passed, a new TableSchema will be constructed
     * out of it and used as the schema for this table.
     *
     * @param array|\Cake\Database\Schema\TableSchema $schema Schema to be used for this table
     *
     * @return $this
     */
    public function setSchema($schema)
    {
        if (is_array($schema)) {
            $constraints = [];

            if (isset($schema['_constraints'])) {
                $constraints = $schema['_constraints'];
                unset($schema['_constraints']);
            }

            $schema = new TableSchema($this->getTable(), $schema);

            foreach ($constraints as $name => $value) {
                $schema->addConstraint($name, $value);
            }
        }

        $this->_schema = $schema;

        return $this;
    }

    /**
     * Override this function in order to alter the schema used by this table.
     * This function is only called after fetching the schema out of the database.
     * If you wish to provide your own schema to this table without touching the
     * database, you can override schema() or inject the definitions though that
     * method.
     *
     * ### Example:
     *
     * ```
     * protected function _initializeSchema(\Cake\Database\Schema\TableSchema $schema) {
     *  $schema->setColumnType('preferences', 'json');
     *  return $schema;
     * }
     * ```
     *
     * @param \Cake\Database\Schema\TableSchema $schema The table definition fetched from database.
     *
     * @return \Cake\Database\Schema\TableSchema the altered schema
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        return $schema;
    }

    /**
     * Test to see if a Table has a specific field/column.
     *
     * Delegates to the schema object and checks for column presence
     * using the Schema\Table instance.
     *
     * @param string $field The field to check for.
     *
     * @return bool True if the field exists, false if it does not.
     */
    public function hasField($field)
    {
        $schema = $this->getSchema();

        return $schema->getColumn($field) !== null;
    }

    /**
     * Sets the primary key field name.
     *
     * @param string|array $key Sets a new name to be used as primary key
     *
     * @return $this
     */
    public function setPrimaryKey($key)
    {
        $this->_primaryKey = $key;

        return $this;
    }

    /**
     * Returns the primary key field name.
     *
     * @return string|array
     */
    public function getPrimaryKey()
    {
        if ($this->_primaryKey === null) {
            $key = (array) $this->getSchema()->primaryKey();
            if (count($key) === 1) {
                $key = $key[0];
            }
            $this->_primaryKey = $key;
        }

        return $this->_primaryKey;
    }

    /**
     * Sets the display field.
     *
     * @param string $key Name to be used as display field.
     *
     * @return $this
     */
    public function setDisplayField($key)
    {
        $this->_displayField = $key;

        return $this;
    }

    /**
     * Returns the display field.
     *
     * @return string
     */
    public function getDisplayField()
    {
        if ($this->_displayField === null) {
            $schema = $this->getSchema();
            $primary = (array) $this->getPrimaryKey();
            $this->_displayField = array_shift($primary);
            if ($schema->getColumn('title')) {
                $this->_displayField = 'title';
            }
            if ($schema->getColumn('name')) {
                $this->_displayField = 'name';
            }
        }

        return $this->_displayField;
    }

    /**
     * Returns the class used to hydrate rows for this table.
     *
     * @return string
     */
    public function getEntityClass()
    {
        if (!$this->_entityClass) {
            $default = Entity::class;
            $self = get_called_class();
            $parts = explode('\\', $self);

            if ($self === __CLASS__ || count($parts) < 3) {
                return $this->_entityClass = $default;
            }

            $alias = Inflector::classify(Inflector::underscore(substr(array_pop($parts), 0, -5)));
            $name = implode('\\', array_slice($parts, 0, -1)).'\\Entity\\'.$alias;
            if (!class_exists($name)) {
                return $this->_entityClass = $default;
            }

            $class = App::className($name, 'Model/Entity');
            if (!$class) {
                throw new MissingEntityException([$name]);
            }

            $this->_entityClass = $class;
        }

        return $this->_entityClass;
    }

    /**
     * Sets the class used to hydrate rows for this table.
     *
     * @param string $name The name of the class to use
     *
     * @throws \Cake\ORM\Exception\MissingEntityException when the entity class cannot be found
     *
     * @return $this
     */
    public function setEntityClass($name)
    {
        $class = App::className($name, 'Model/Entity');
        if (!$class) {
            throw new MissingEntityException([$name]);
        }

        $this->_entityClass = $class;

        return $this;
    }

    /**
     * Add a behavior.
     *
     * Adds a behavior to this table's behavior collection. Behaviors
     * provide an easy way to create horizontally re-usable features
     * that can provide trait like functionality, and allow for events
     * to be listened to.
     *
     * Example:
     *
     * Load a behavior, with some settings.
     *
     * ```
     * $this->addBehavior('Tree', ['parent' => 'parentId']);
     * ```
     *
     * Behaviors are generally loaded during Table::initialize().
     *
     * @param string $name    The name of the behavior. Can be a short class reference.
     * @param array  $options The options for the behavior to use.
     *
     * @throws \RuntimeException If a behavior is being reloaded.
     *
     * @return $this
     *
     * @see \Cake\ORM\Behavior
     */
    public function addBehavior($name, array $options = [])
    {
        $this->_behaviors->load($name, $options);

        return $this;
    }

    /**
     * Adds an array of behaviors to the table's behavior collection.
     *
     * Example:
     *
     * ```
     * $this->addBehaviors([
     *      'Timestamp',
     *      'Tree' => ['level' => 'level'],
     * ]);
     * ```
     *
     * @param array $behaviors All of the behaviors to load.
     *
     * @throws \RuntimeException If a behavior is being reloaded.
     *
     * @return $this
     */
    public function addBehaviors(array $behaviors)
    {
        foreach ($behaviors as $name => $options) {
            if (is_int($name)) {
                $name = $options;
                $options = [];
            }

            $this->addBehavior($name, $options);
        }

        return $this;
    }

    /**
     * Removes a behavior from this table's behavior registry.
     *
     * Example:
     *
     * Remove a behavior from this table.
     *
     * ```
     * $this->removeBehavior('Tree');
     * ```
     *
     * @param string $name The alias that the behavior was added with.
     *
     * @return $this
     *
     * @see \Cake\ORM\Behavior
     */
    public function removeBehavior($name)
    {
        $this->_behaviors->unload($name);

        return $this;
    }

    /**
     * Returns the behavior registry for this table.
     *
     * @return \Cake\ORM\BehaviorRegistry The BehaviorRegistry instance.
     */
    public function behaviors()
    {
        return $this->_behaviors;
    }

    /**
     * Get a behavior from the registry.
     *
     * @param string $name The behavior alias to get from the registry.
     *
     * @throws \InvalidArgumentException If the behavior does not exist.
     *
     * @return \Cake\ORM\Behavior
     */
    public function getBehavior($name)
    {
        /** @var \Cake\ORM\Behavior $behavior */
        $behavior = $this->_behaviors->get($name);
        if ($behavior === null) {
            throw new InvalidArgumentException(sprintf(
                'The %s behavior is not defined on %s.',
                $name,
                get_class($this)
            ));
        }

        return $behavior;
    }

    /**
     * Check if a behavior with the given alias has been loaded.
     *
     * @param string $name The behavior alias to check.
     *
     * @return bool Whether or not the behavior exists.
     */
    public function hasBehavior($name)
    {
        return $this->_behaviors->has($name);
    }

    /**
     * Creates a new BelongsTo association between this table and a target
     * table. A "belongs to" association is a N-1 relationship where this table
     * is the N side, and where there is a single associated record in the target
     * table for each one in this table.
     *
     * Target table can be inferred by its name, which is provided in the
     * first argument, or you can either pass the to be instantiated or
     * an instance of it directly.
     *
     * The options array accept the following keys:
     *
     * - className: The class name of the target table object
     * - targetTable: An instance of a table object to be used as the target table
     * - foreignKey: The name of the field to use as foreign key, if false none
     *   will be used
     * - conditions: array with a list of conditions to filter the join with
     * - joinType: The type of join to be used (e.g. INNER)
     * - strategy: The loading strategy to use. 'join' and 'select' are supported.
     * - finder: The finder method to use when loading records from this association.
     *   Defaults to 'all'. When the strategy is 'join', only the fields, containments,
     *   and where conditions will be used from the finder.
     *
     * This method will return the association object that was built.
     *
     * @param string $associated the alias for the target table. This is used to
     *                           uniquely identify the association
     * @param array  $options    list of options to configure the association definition
     *
     * @return \Cake\ORM\Association\BelongsTo
     */
    public function belongsTo($associated, array $options = [])
    {
        $options += ['sourceTable' => $this];

        /** @var \Cake\ORM\Association\BelongsTo $association */
        $association = $this->_associations->load(BelongsTo::class, $associated, $options);

        return $association;
    }

    /**
     * Creates a new HasOne association between this table and a target
     * table. A "has one" association is a 1-1 relationship.
     *
     * Target table can be inferred by its name, which is provided in the
     * first argument, or you can either pass the class name to be instantiated or
     * an instance of it directly.
     *
     * The options array accept the following keys:
     *
     * - className: The class name of the target table object
     * - targetTable: An instance of a table object to be used as the target table
     * - foreignKey: The name of the field to use as foreign key, if false none
     *   will be used
     * - dependent: Set to true if you want CakePHP to cascade deletes to the
     *   associated table when an entity is removed on this table. The delete operation
     *   on the associated table will not cascade further. To get recursive cascades enable
     *   `cascadeCallbacks` as well. Set to false if you don't want CakePHP to remove
     *   associated data, or when you are using database constraints.
     * - cascadeCallbacks: Set to true if you want CakePHP to fire callbacks on
     *   cascaded deletes. If false the ORM will use deleteAll() to remove data.
     *   When true records will be loaded and then deleted.
     * - conditions: array with a list of conditions to filter the join with
     * - joinType: The type of join to be used (e.g. LEFT)
     * - strategy: The loading strategy to use. 'join' and 'select' are supported.
     * - finder: The finder method to use when loading records from this association.
     *   Defaults to 'all'. When the strategy is 'join', only the fields, containments,
     *   and where conditions will be used from the finder.
     *
     * This method will return the association object that was built.
     *
     * @param string $associated the alias for the target table. This is used to
     *                           uniquely identify the association
     * @param array  $options    list of options to configure the association definition
     *
     * @return \Cake\ORM\Association\HasOne
     */
    public function hasOne($associated, array $options = [])
    {
        $options += ['sourceTable' => $this];

        /** @var \Cake\ORM\Association\HasOne $association */
        $association = $this->_associations->load(HasOne::class, $associated, $options);

        return $association;
    }

    /**
     * Creates a new HasMany association between this table and a target
     * table. A "has many" association is a 1-N relationship.
     *
     * Target table can be inferred by its name, which is provided in the
     * first argument, or you can either pass the class name to be instantiated or
     * an instance of it directly.
     *
     * The options array accept the following keys:
     *
     * - className: The class name of the target table object
     * - targetTable: An instance of a table object to be used as the target table
     * - foreignKey: The name of the field to use as foreign key, if false none
     *   will be used
     * - dependent: Set to true if you want CakePHP to cascade deletes to the
     *   associated table when an entity is removed on this table. The delete operation
     *   on the associated table will not cascade further. To get recursive cascades enable
     *   `cascadeCallbacks` as well. Set to false if you don't want CakePHP to remove
     *   associated data, or when you are using database constraints.
     * - cascadeCallbacks: Set to true if you want CakePHP to fire callbacks on
     *   cascaded deletes. If false the ORM will use deleteAll() to remove data.
     *   When true records will be loaded and then deleted.
     * - conditions: array with a list of conditions to filter the join with
     * - sort: The order in which results for this association should be returned
     * - saveStrategy: Either 'append' or 'replace'. When 'append' the current records
     *   are appended to any records in the database. When 'replace' associated records
     *   not in the current set will be removed. If the foreign key is a null able column
     *   or if `dependent` is true records will be orphaned.
     * - strategy: The strategy to be used for selecting results Either 'select'
     *   or 'subquery'. If subquery is selected the query used to return results
     *   in the source table will be used as conditions for getting rows in the
     *   target table.
     * - finder: The finder method to use when loading records from this association.
     *   Defaults to 'all'.
     *
     * This method will return the association object that was built.
     *
     * @param string $associated the alias for the target table. This is used to
     *                           uniquely identify the association
     * @param array  $options    list of options to configure the association definition
     *
     * @return \Cake\ORM\Association\HasMany
     */
    public function hasMany($associated, array $options = [])
    {
        $options += ['sourceTable' => $this];

        /** @var \Cake\ORM\Association\HasMany $association */
        $association = $this->_associations->load(HasMany::class, $associated, $options);

        return $association;
    }

    /**
     * Creates a new BelongsToMany association between this table and a target
     * table. A "belongs to many" association is a M-N relationship.
     *
     * Target table can be inferred by its name, which is provided in the
     * first argument, or you can either pass the class name to be instantiated or
     * an instance of it directly.
     *
     * The options array accept the following keys:
     *
     * - className: The class name of the target table object.
     * - targetTable: An instance of a table object to be used as the target table.
     * - foreignKey: The name of the field to use as foreign key.
     * - targetForeignKey: The name of the field to use as the target foreign key.
     * - joinTable: The name of the table representing the link between the two
     * - through: If you choose to use an already instantiated link table, set this
     *   key to a configured Table instance containing associations to both the source
     *   and target tables in this association.
     * - dependent: Set to false, if you do not want junction table records removed
     *   when an owning record is removed.
     * - cascadeCallbacks: Set to true if you want CakePHP to fire callbacks on
     *   cascaded deletes. If false the ORM will use deleteAll() to remove data.
     *   When true join/junction table records will be loaded and then deleted.
     * - conditions: array with a list of conditions to filter the join with.
     * - sort: The order in which results for this association should be returned.
     * - strategy: The strategy to be used for selecting results Either 'select'
     *   or 'subquery'. If subquery is selected the query used to return results
     *   in the source table will be used as conditions for getting rows in the
     *   target table.
     * - saveStrategy: Either 'append' or 'replace'. Indicates the mode to be used
     *   for saving associated entities. The former will only create new links
     *   between both side of the relation and the latter will do a wipe and
     *   replace to create the links between the passed entities when saving.
     * - strategy: The loading strategy to use. 'select' and 'subquery' are supported.
     * - finder: The finder method to use when loading records from this association.
     *   Defaults to 'all'.
     *
     * This method will return the association object that was built.
     *
     * @param string $associated the alias for the target table. This is used to
     *                           uniquely identify the association
     * @param array  $options    list of options to configure the association definition
     *
     * @return \Cake\ORM\Association\BelongsToMany
     */
    public function belongsToMany($associated, array $options = [])
    {
        $options += ['sourceTable' => $this];

        /** @var \Cake\ORM\Association\BelongsToMany $association */
        $association = $this->_associations->load(BelongsToMany::class, $associated, $options);

        return $association;
    }

    /**
     * Creates a new Query for this repository and applies some defaults based on the
     * type of search that was selected.
     *
     * ### Model.beforeFind event
     *
     * Each find() will trigger a `Model.beforeFind` event for all attached
     * listeners. Any listener can set a valid result set using $query
     *
     * By default, `$options` will recognize the following keys:
     *
     * - fields
     * - conditions
     * - order
     * - limit
     * - offset
     * - page
     * - group
     * - having
     * - contain
     * - join
     *
     * ### Usage
     *
     * Using the options array:
     *
     * ```
     * $query = $articles->find('all', [
     *   'conditions' => ['published' => 1],
     *   'limit' => 10,
     *   'contain' => ['Users', 'Comments']
     * ]);
     * ```
     *
     * Using the builder interface:
     *
     * ```
     * $query = $articles->find()
     *   ->where(['published' => 1])
     *   ->limit(10)
     *   ->contain(['Users', 'Comments']);
     * ```
     *
     * ### Calling finders
     *
     * The find() method is the entry point for custom finder methods.
     * You can invoke a finder by specifying the type:
     *
     * ```
     * $query = $articles->find('published');
     * ```
     *
     * Would invoke the `findPublished` method.
     *
     * @param string             $type    the type of query to perform
     * @param array|\ArrayAccess $options An array that will be passed to Query::applyOptions()
     *
     * @return \Cake\ORM\Query The query builder
     */
    public function find($type = 'all', $options = [])
    {
        $query = $this->query();
        $query->select();

        return $this->callFinder($type, $query, $options);
    }

    /**
     * Returns the query as passed.
     *
     * By default findAll() applies no conditions, you
     * can override this method in subclasses to modify how `find('all')` works.
     *
     * @param \Cake\ORM\Query $query   The query to find with
     * @param array           $options The options to use for the find
     *
     * @return \Cake\ORM\Query The query builder
     */
    public function findAll(Query $query, array $options)
    {
        return $query;
    }

    /**
     * Sets up a query object so results appear as an indexed array, useful for any
     * place where you would want a list such as for populating input select boxes.
     *
     * When calling this finder, the fields passed are used to determine what should
     * be used as the array key, value and optionally what to group the results by.
     * By default the primary key for the model is used for the key, and the display
     * field as value.
     *
     * The results of this finder will be in the following form:
     *
     * ```
     * [
     *  1 => 'value for id 1',
     *  2 => 'value for id 2',
     *  4 => 'value for id 4'
     * ]
     * ```
     *
     * You can specify which property will be used as the key and which as value
     * by using the `$options` array, when not specified, it will use the results
     * of calling `primaryKey` and `displayField` respectively in this table:
     *
     * ```
     * $table->find('list', [
     *  'keyField' => 'name',
     *  'valueField' => 'age'
     * ]);
     * ```
     *
     * Results can be put together in bigger groups when they share a property, you
     * can customize the property to use for grouping by setting `groupField`:
     *
     * ```
     * $table->find('list', [
     *  'groupField' => 'category_id',
     * ]);
     * ```
     *
     * When using a `groupField` results will be returned in this format:
     *
     * ```
     * [
     *  'group_1' => [
     *      1 => 'value for id 1',
     *      2 => 'value for id 2',
     *  ]
     *  'group_2' => [
     *      4 => 'value for id 4'
     *  ]
     * ]
     * ```
     *
     * @param \Cake\ORM\Query $query   The query to find with
     * @param array           $options The options for the find
     *
     * @return \Cake\ORM\Query The query builder
     */
    public function findList(Query $query, array $options)
    {
        $options += [
            'keyField'   => $this->getPrimaryKey(),
            'valueField' => $this->getDisplayField(),
            'groupField' => null,
        ];

        if (isset($options['idField'])) {
            $options['keyField'] = $options['idField'];
            unset($options['idField']);
            deprecationWarning('Option "idField" is deprecated, use "keyField" instead.');
        }

        if (!$query->clause('select') &&
            !is_object($options['keyField']) &&
            !is_object($options['valueField']) &&
            !is_object($options['groupField'])
        ) {
            $fields = array_merge(
                (array) $options['keyField'],
                (array) $options['valueField'],
                (array) $options['groupField']
            );
            $columns = $this->getSchema()->columns();
            if (count($fields) === count(array_intersect($fields, $columns))) {
                $query->select($fields);
            }
        }

        $options = $this->_setFieldMatchers(
            $options,
            ['keyField', 'valueField', 'groupField']
        );

        return $query->formatResults(function ($results) use ($options) {
            /* @var \Cake\Collection\CollectionInterface $results */
            return $results->combine(
                $options['keyField'],
                $options['valueField'],
                $options['groupField']
            );
        });
    }

    /**
     * Results for this finder will be a nested array, and is appropriate if you want
     * to use the parent_id field of your model data to build nested results.
     *
     * Values belonging to a parent row based on their parent_id value will be
     * recursively nested inside the parent row values using the `children` property
     *
     * You can customize what fields are used for nesting results, by default the
     * primary key and the `parent_id` fields are used. If you wish to change
     * these defaults you need to provide the keys `keyField`, `parentField` or `nestingKey` in
     * `$options`:
     *
     * ```
     * $table->find('threaded', [
     *  'keyField' => 'id',
     *  'parentField' => 'ancestor_id'
     *  'nestingKey' => 'children'
     * ]);
     * ```
     *
     * @param \Cake\ORM\Query $query   The query to find with
     * @param array           $options The options to find with
     *
     * @return \Cake\ORM\Query The query builder
     */
    public function findThreaded(Query $query, array $options)
    {
        $options += [
            'keyField'    => $this->getPrimaryKey(),
            'parentField' => 'parent_id',
            'nestingKey'  => 'children',
        ];

        if (isset($options['idField'])) {
            $options['keyField'] = $options['idField'];
            unset($options['idField']);
            deprecationWarning('Option "idField" is deprecated, use "keyField" instead.');
        }

        $options = $this->_setFieldMatchers($options, ['keyField', 'parentField']);

        return $query->formatResults(function ($results) use ($options) {
            /* @var \Cake\Collection\CollectionInterface $results */
            return $results->nest($options['keyField'], $options['parentField'], $options['nestingKey']);
        });
    }

    /**
     * Out of an options array, check if the keys described in `$keys` are arrays
     * and change the values for closures that will concatenate the each of the
     * properties in the value array when passed a row.
     *
     * This is an auxiliary function used for result formatters that can accept
     * composite keys when comparing values.
     *
     * @param array $options the original options passed to a finder
     * @param array $keys    the keys to check in $options to build matchers from
     *                       the associated value
     *
     * @return array
     */
    protected function _setFieldMatchers($options, $keys)
    {
        foreach ($keys as $field) {
            if (!is_array($options[$field])) {
                continue;
            }

            if (count($options[$field]) === 1) {
                $options[$field] = current($options[$field]);
                continue;
            }

            $fields = $options[$field];
            $options[$field] = function ($row) use ($fields) {
                $matches = [];
                foreach ($fields as $field) {
                    $matches[] = $row[$field];
                }

                return implode(';', $matches);
            };
        }

        return $options;
    }

    /**
     * Handles the logic executing of a worker inside a transaction.
     *
     * @param callable $worker The worker that will run inside the transaction.
     * @param bool     $atomic Whether to execute the worker inside a database transaction.
     *
     * @return mixed
     */
    protected function _executeTransaction(callable $worker, $atomic = true)
    {
        if ($atomic) {
            return $this->getConnection()->transactional(function () use ($worker) {
                return $worker();
            });
        }

        return $worker();
    }

    /**
     * Checks if the caller would have executed a commit on a transaction.
     *
     * @param bool $atomic  True if an atomic transaction was used.
     * @param bool $primary True if a primary was used.
     *
     * @return bool Returns true if a transaction was committed.
     */
    protected function _transactionCommitted($atomic, $primary)
    {
        return !$this->getConnection()->inTransaction() && ($atomic || (!$atomic && $primary));
    }

    /**
     * Finds an existing record or creates a new one.
     *
     * A find() will be done to locate an existing record using the attributes
     * defined in $search. If records matches the conditions, the first record
     * will be returned.
     *
     * If no record can be found, a new entity will be created
     * with the $search properties. If a callback is provided, it will be
     * called allowing you to define additional default values. The new
     * entity will be saved and returned.
     *
     * If your find conditions require custom order, associations or conditions, then the $search
     * parameter can be a callable that takes the Query as the argument, or a \Cake\ORM\Query object passed
     * as the $search parameter. Allowing you to customize the find results.
     *
     * ### Options
     *
     * The options array is passed to the save method with exception to the following keys:
     *
     * - atomic: Whether to execute the methods for find, save and callbacks inside a database
     *   transaction (default: true)
     * - defaults: Whether to use the search criteria as default values for the new entity (default: true)
     *
     * @param array|\Cake\ORM\Query $search   The criteria to find existing
     *                                        records by. Note that when you pass a query object you'll have to use
     *                                        the 2nd arg of the method to modify the entity data before saving.
     * @param callable|null         $callback A callback that will be invoked for newly
     *                                        created entities. This callback will be called *before* the entity
     *                                        is persisted.
     * @param array                 $options  The options to use when saving.
     *
     * @return \Cake\Datasource\EntityInterface An entity.
     */
    public function findOrCreate($search, callable $callback = null, $options = [])
    {
        $options = new ArrayObject($options + [
            'atomic'   => true,
            'defaults' => true,
        ]);

        $entity = $this->_executeTransaction(function () use ($search, $callback, $options) {
            return $this->_processFindOrCreate($search, $callback, $options->getArrayCopy());
        }, $options['atomic']);

        if ($entity && $this->_transactionCommitted($options['atomic'], true)) {
            $this->dispatchEvent('Model.afterSaveCommit', compact('entity', 'options'));
        }

        return $entity;
    }

    /**
     * Performs the actual find and/or create of an entity based on the passed options.
     *
     * @param array|callable $search   The criteria to find an existing record by, or a callable tha will
     *                                 customize the find query.
     * @param callable|null  $callback A callback that will be invoked for newly
     *                                 created entities. This callback will be called *before* the entity
     *                                 is persisted.
     * @param array          $options  The options to use when saving.
     *
     * @return \Cake\Datasource\EntityInterface An entity.
     */
    protected function _processFindOrCreate($search, callable $callback = null, $options = [])
    {
        if (is_callable($search)) {
            $query = $this->find();
            $search($query);
        } elseif (is_array($search)) {
            $query = $this->find()->where($search);
        } elseif ($search instanceof Query) {
            $query = $search;
        } else {
            throw new InvalidArgumentException('Search criteria must be an array, callable or Query');
        }
        $row = $query->first();
        if ($row !== null) {
            return $row;
        }
        $entity = $this->newEntity();
        if ($options['defaults'] && is_array($search)) {
            $entity->set($search, ['guard' => false]);
        }
        if ($callback !== null) {
            $entity = $callback($entity) ?: $entity;
        }
        unset($options['defaults']);

        return $this->save($entity, $options) ?: $entity;
    }

    /**
     * Gets the query object for findOrCreate().
     *
     * @param array|\Cake\ORM\Query|string $search The criteria to find existing records by.
     *
     * @return \Cake\ORM\Query
     */
    protected function _getFindOrCreateQuery($search)
    {
        if ($search instanceof Query) {
            return $search;
        }

        return $this->find()->where($search);
    }

    public function updateAll($fields, $conditions)
    {
        $query = $this->query();
        $query->update()
            ->set($fields)
            ->where($conditions);
        $statement = $query->execute();
        $statement->closeCursor();

        return $statement->rowCount();
    }

    public function deleteAll($conditions)
    {
        $query = $this->query()
            ->delete()
            ->where($conditions);
        $statement = $query->execute();
        $statement->closeCursor();

        return $statement->rowCount();
    }

    public function exists($conditions)
    {
        return (bool) count(
            $this->find('all')
            ->select(['existing' => 1])
            ->where($conditions)
            ->limit(1)
            ->enableHydration(false)
            ->toArray()
        );
    }

    /**
     * ### Options.
     *
     * The options array accepts the following keys:
     *
     * - atomic: Whether to execute the save and callbacks inside a database
     *   transaction (default: true)
     * - checkRules: Whether or not to check the rules on entity before saving, if the checking
     *   fails, it will abort the save operation. (default:true)
     * - associated: If `true` it will save 1st level associated entities as they are found
     *   in the passed `$entity` whenever the property defined for the association
     *   is marked as dirty. If an array, it will be interpreted as the list of associations
     *   to be saved. It is possible to provide different options for saving on associated
     *   table objects using this key by making the custom options the array value.
     *   If `false` no associated records will be saved. (default: `true`)
     * - checkExisting: Whether or not to check if the entity already exists, assuming that the
     *   entity is marked as not new, and the primary key has been set.
     *
     * ### Events
     *
     * When saving, this method will trigger four events:
     *
     * - Model.beforeRules: Will be triggered right before any rule checking is done
     *   for the passed entity if the `checkRules` key in $options is not set to false.
     *   Listeners will receive as arguments the entity, options array and the operation type.
     *   If the event is stopped the rules check result will be set to the result of the event itself.
     * - Model.afterRules: Will be triggered right after the `checkRules()` method is
     *   called for the entity. Listeners will receive as arguments the entity,
     *   options array, the result of checking the rules and the operation type.
     *   If the event is stopped the checking result will be set to the result of
     *   the event itself.
     * - Model.beforeSave: Will be triggered just before the list of fields to be
     *   persisted is calculated. It receives both the entity and the options as
     *   arguments. The options array is passed as an ArrayObject, so any changes in
     *   it will be reflected in every listener and remembered at the end of the event
     *   so it can be used for the rest of the save operation. Returning false in any
     *   of the listeners will abort the saving process. If the event is stopped
     *   using the event API, the event object's `result` property will be returned.
     *   This can be useful when having your own saving strategy implemented inside a
     *   listener.
     * - Model.afterSave: Will be triggered after a successful insert or save,
     *   listeners will receive the entity and the options array as arguments. The type
     *   of operation performed (insert or update) can be determined by checking the
     *   entity's method `isNew`, true meaning an insert and false an update.
     * - Model.afterSaveCommit: Will be triggered after the transaction is commited
     *   for atomic save, listeners will receive the entity and the options array
     *   as arguments.
     *
     * This method will determine whether the passed entity needs to be
     * inserted or updated in the database. It does that by checking the `isNew`
     * method on the entity. If the entity to be saved returns a non-empty value from
     * its `errors()` method, it will not be saved.
     *
     * ### Saving on associated tables
     *
     * This method will by default persist entities belonging to associated tables,
     * whenever a dirty property matching the name of the property name set for an
     * association in this table. It is possible to control what associations will
     * be saved and to pass additional option for saving them.
     *
     * ```
     * // Only save the comments association
     * $articles->save($entity, ['associated' => ['Comments']]);
     *
     * // Save the company, the employees and related addresses for each of them.
     * // For employees do not check the entity rules
     * $companies->save($entity, [
     *   'associated' => [
     *     'Employees' => [
     *       'associated' => ['Addresses'],
     *       'checkRules' => false
     *     ]
     *   ]
     * ]);
     *
     * // Save no associations
     * $articles->save($entity, ['associated' => false]);
     * ```
     *
     * @param \Cake\Datasource\EntityInterface $entity
     * @param array                            $options
     *
     * @throws \Cake\ORM\Exception\RolledbackTransactionException If the transaction is aborted in the afterSave event.
     *
     * @return \Cake\Datasource\EntityInterface|false
     */
    public function save(EntityInterface $entity, $options = [])
    {
        if ($options instanceof SaveOptionsBuilder) {
            $options = $options->toArray();
        }

        $options = new ArrayObject((array) $options + [
            'atomic'        => true,
            'associated'    => true,
            'checkRules'    => true,
            'checkExisting' => true,
            '_primary'      => true,
        ]);

        if ($entity->getErrors()) {
            return false;
        }

        if ($entity->isNew() === false && !$entity->isDirty()) {
            return $entity;
        }

        $success = $this->_executeTransaction(function () use ($entity, $options) {
            return $this->_processSave($entity, $options);
        }, $options['atomic']);

        if ($success) {
            if ($this->_transactionCommitted($options['atomic'], $options['_primary'])) {
                $this->dispatchEvent('Model.afterSaveCommit', compact('entity', 'options'));
            }
            if ($options['atomic'] || $options['_primary']) {
                $entity->clean();
                $entity->isNew(false);
                $entity->setSource($this->getRegistryAlias());
            }
        }

        return $success;
    }

    /**
     * Try to save an entity or throw a PersistenceFailedException if the application rules checks failed,
     * the entity contains errors or the save was aborted by a callback.
     *
     * @param \Cake\Datasource\EntityInterface $entity  the entity to be saved
     * @param array|\ArrayAccess               $options The options to use when saving.
     *
     * @throws \Cake\ORM\Exception\PersistenceFailedException When the entity couldn't be saved
     *
     * @return \Cake\Datasource\EntityInterface
     *
     * @see \Cake\ORM\Table::save()
     */
    public function saveOrFail(EntityInterface $entity, $options = [])
    {
        $saved = $this->save($entity, $options);
        if ($saved === false) {
            throw new PersistenceFailedException($entity, ['save']);
        }

        return $saved;
    }

    /**
     * Performs the actual saving of an entity based on the passed options.
     *
     * @param \Cake\Datasource\EntityInterface $entity  the entity to be saved
     * @param \ArrayObject                     $options the options to use for the save operation
     *
     * @throws \RuntimeException                                  When an entity is missing some of the primary keys.
     * @throws \Cake\ORM\Exception\RolledbackTransactionException If the transaction
     *                                                            is aborted in the afterSave event.
     *
     * @return \Cake\Datasource\EntityInterface|bool
     */
    protected function _processSave($entity, $options)
    {
        $primaryColumns = (array) $this->getPrimaryKey();

        if ($options['checkExisting'] && $primaryColumns && $entity->isNew() && $entity->has($primaryColumns)) {
            $alias = $this->getAlias();
            $conditions = [];
            foreach ($entity->extract($primaryColumns) as $k => $v) {
                $conditions["$alias.$k"] = $v;
            }
            $entity->isNew(!$this->exists($conditions));
        }

        $mode = $entity->isNew() ? RulesChecker::CREATE : RulesChecker::UPDATE;
        if ($options['checkRules'] && !$this->checkRules($entity, $mode, $options)) {
            return false;
        }

        $options['associated'] = $this->_associations->normalizeKeys($options['associated']);
        $event = $this->dispatchEvent('Model.beforeSave', compact('entity', 'options'));

        if ($event->isStopped()) {
            return $event->getResult();
        }

        $saved = $this->_associations->saveParents(
            $this,
            $entity,
            $options['associated'],
            ['_primary' => false] + $options->getArrayCopy()
        );

        if (!$saved && $options['atomic']) {
            return false;
        }

        $data = $entity->extract($this->getSchema()->columns(), true);
        $isNew = $entity->isNew();

        if ($isNew) {
            $success = $this->_insert($entity, $data);
        } else {
            $success = $this->_update($entity, $data);
        }

        if ($success) {
            $success = $this->_onSaveSuccess($entity, $options);
        }

        if (!$success && $isNew) {
            $entity->unsetProperty($this->getPrimaryKey());
            $entity->isNew(true);
        }

        return $success ? $entity : false;
    }

    /**
     * Handles the saving of children associations and executing the afterSave logic
     * once the entity for this table has been saved successfully.
     *
     * @param \Cake\Datasource\EntityInterface $entity  the entity to be saved
     * @param \ArrayObject                     $options the options to use for the save operation
     *
     * @throws \Cake\ORM\Exception\RolledbackTransactionException If the transaction
     *                                                            is aborted in the afterSave event.
     *
     * @return bool True on success
     */
    protected function _onSaveSuccess($entity, $options)
    {
        $success = $this->_associations->saveChildren(
            $this,
            $entity,
            $options['associated'],
            ['_primary' => false] + $options->getArrayCopy()
        );

        if (!$success && $options['atomic']) {
            return false;
        }

        $this->dispatchEvent('Model.afterSave', compact('entity', 'options'));

        if ($options['atomic'] && !$this->getConnection()->inTransaction()) {
            throw new RolledbackTransactionException(['table' => get_class($this)]);
        }

        if (!$options['atomic'] && !$options['_primary']) {
            $entity->clean();
            $entity->isNew(false);
            $entity->setSource($this->getRegistryAlias());
        }

        return true;
    }

    /**
     * Auxiliary function to handle the insert of an entity's data in the table.
     *
     * @param \Cake\Datasource\EntityInterface $entity the subject entity from were $data was extracted
     * @param array                            $data   The actual data that needs to be saved
     *
     * @throws \RuntimeException if not all the primary keys where supplied or could
     *                           be generated when the table has composite primary keys. Or when the table has no primary key.
     *
     * @return \Cake\Datasource\EntityInterface|bool
     */
    protected function _insert($entity, $data)
    {
        $primary = (array) $this->getPrimaryKey();
        if (empty($primary)) {
            $msg = sprintf(
                'Cannot insert row in "%s" table, it has no primary key.',
                $this->getTable()
            );

            throw new RuntimeException($msg);
        }
        $keys = array_fill(0, count($primary), null);
        $id = (array) $this->_newId($primary) + $keys;

        // Generate primary keys preferring values in $data.
        $primary = array_combine($primary, $id);
        $primary = array_intersect_key($data, $primary) + $primary;

        $filteredKeys = array_filter($primary, 'strlen');
        $data += $filteredKeys;

        if (count($primary) > 1) {
            $schema = $this->getSchema();
            foreach ($primary as $k => $v) {
                if (!isset($data[$k]) && empty($schema->getColumn($k)['autoIncrement'])) {
                    $msg = 'Cannot insert row, some of the primary key values are missing. ';
                    $msg .= sprintf(
                        'Got (%s), expecting (%s)',
                        implode(', ', $filteredKeys + $entity->extract(array_keys($primary))),
                        implode(', ', array_keys($primary))
                    );

                    throw new RuntimeException($msg);
                }
            }
        }

        $success = false;
        if (empty($data)) {
            return $success;
        }

        $statement = $this->query()->insert(array_keys($data))
            ->values($data)
            ->execute();

        if ($statement->rowCount() !== 0) {
            $success = $entity;
            $entity->set($filteredKeys, ['guard' => false]);
            $schema = $this->getSchema();
            $driver = $this->getConnection()->getDriver();
            foreach ($primary as $key => $v) {
                if (!isset($data[$key])) {
                    $id = $statement->lastInsertId($this->getTable(), $key);
                    $type = $schema->getColumnType($key);
                    $entity->set($key, Type::build($type)->toPHP($id, $driver));
                    break;
                }
            }
        }
        $statement->closeCursor();

        return $success;
    }

    /**
     * Generate a primary key value for a new record.
     *
     * By default, this uses the type system to generate a new primary key
     * value if possible. You can override this method if you have specific requirements
     * for id generation.
     *
     * Note: The ORM will not generate primary key values for composite primary keys.
     * You can overwrite _newId() in your table class.
     *
     * @param array $primary The primary key columns to get a new ID for.
     *
     * @return null|string|array Either null or the primary key value or a list of primary key values.
     */
    protected function _newId($primary)
    {
        if (!$primary || count((array) $primary) > 1) {
            return;
        }
        $typeName = $this->getSchema()->getColumnType($primary[0]);
        $type = Type::build($typeName);

        return $type->newId();
    }

    /**
     * Auxiliary function to handle the update of an entity's data in the table.
     *
     * @param \Cake\Datasource\EntityInterface $entity the subject entity from were $data was extracted
     * @param array                            $data   The actual data that needs to be saved
     *
     * @throws \InvalidArgumentException When primary key data is missing.
     *
     * @return \Cake\Datasource\EntityInterface|bool
     */
    protected function _update($entity, $data)
    {
        $primaryColumns = (array) $this->getPrimaryKey();
        $primaryKey = $entity->extract($primaryColumns);

        $data = array_diff_key($data, $primaryKey);
        if (empty($data)) {
            return $entity;
        }

        if (count($primaryColumns) === 0) {
            $entityClass = get_class($entity);
            $table = $this->getTable();
            $message = "Cannot update `$entityClass`. The `$table` has no primary key.";

            throw new InvalidArgumentException($message);
        }

        if (!$entity->has($primaryColumns)) {
            $message = 'All primary key value(s) are needed for updating, ';
            $message .= get_class($entity).' is missing '.implode(', ', $primaryColumns);

            throw new InvalidArgumentException($message);
        }

        $query = $this->query();
        $statement = $query->update()
            ->set($data)
            ->where($primaryKey)
            ->execute();

        $success = false;
        if ($statement->errorCode() === '00000') {
            $success = $entity;
        }
        $statement->closeCursor();

        return $success;
    }

    /**
     * Persists multiple entities of a table.
     *
     * The records will be saved in a transaction which will be rolled back if
     * any one of the records fails to save due to failed validation or database
     * error.
     *
     * @param \Cake\Datasource\EntityInterface[]|\Cake\ORM\ResultSet $entities Entities to save.
     * @param array|\ArrayAccess                                     $options  Options used when calling Table::save() for each entity.
     *
     * @return bool|\Cake\Datasource\EntityInterface[]|\Cake\ORM\ResultSet False on failure, entities list on success.
     */
    public function saveMany($entities, $options = [])
    {
        $isNew = [];
        $cleanup = function ($entities) use (&$isNew) {
            foreach ($entities as $key => $entity) {
                if (isset($isNew[$key]) && $isNew[$key]) {
                    $entity->unsetProperty($this->getPrimaryKey());
                    $entity->isNew(true);
                }
            }
        };

        try {
            $return = $this->getConnection()
                ->transactional(function () use ($entities, $options, &$isNew) {
                    foreach ($entities as $key => $entity) {
                        $isNew[$key] = $entity->isNew();
                        if ($this->save($entity, $options) === false) {
                            return false;
                        }
                    }
                });
        } catch (\Exception $e) {
            $cleanup($entities);

            throw $e;
        }

        if ($return === false) {
            $cleanup($entities);

            return false;
        }

        return $entities;
    }

    /**
     * For HasMany and HasOne associations records will be removed based on
     * the dependent option. Join table records in BelongsToMany associations
     * will always be removed. You can use the `cascadeCallbacks` option
     * when defining associations to change how associated data is deleted.
     *
     * ### Options
     *
     * - `atomic` Defaults to true. When true the deletion happens within a transaction.
     * - `checkRules` Defaults to true. Check deletion rules before deleting the record.
     *
     * ### Events
     *
     * - `Model.beforeDelete` Fired before the delete occurs. If stopped the delete
     *   will be aborted. Receives the event, entity, and options.
     * - `Model.afterDelete` Fired after the delete has been successful. Receives
     *   the event, entity, and options.
     * - `Model.afterDeleteCommit` Fired after the transaction is committed for
     *   an atomic delete. Receives the event, entity, and options.
     *
     * The options argument will be converted into an \ArrayObject instance
     * for the duration of the callbacks, this allows listeners to modify
     * the options used in the delete operation.
     *
     * public function delete(EntityInterface $entity, $options = [])
     * {
     * $options = new ArrayObject((array)$options + [
     * 'atomic' => true,
     * 'checkRules' => true,
     * '_primary' => true,
     * ]);
     *
     * $success = $this->_executeTransaction(function () use ($entity, $options) {
     * return $this->_processDelete($entity, $options);
     * }, $options['atomic']);
     *
     * if ($success && $this->_transactionCommitted($options['atomic'], $options['_primary'])) {
     * $this->dispatchEvent('Model.afterDeleteCommit', [
     * 'entity' => $entity,
     * 'options' => $options
     * ]);
     * }
     *
     * return $success;
     * }
     */

    /**
     * Try to delete an entity or throw a PersistenceFailedException if the entity is new,
     * has no primary key value, application rules checks failed or the delete was aborted by a callback.
     *
     * @param \Cake\Datasource\EntityInterface $entity  The entity to remove.
     * @param array|\ArrayAccess               $options The options for the delete.
     *
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     *
     * @return bool success
     *
     * @see \Cake\ORM\Table::delete()
     * public function deleteOrFail(EntityInterface $entity, $options = [])
     * {
     * $deleted = $this->delete($entity, $options);
     * if ($deleted === false) {
     * throw new PersistenceFailedException($entity, ['delete']);
     * }
     *
     * return $deleted;
     * }
     */

    /**
     * Perform the delete operation.
     *
     * Will delete the entity provided. Will remove rows from any
     * dependent associations, and clear out join tables for BelongsToMany associations.
     *
     * @param \Cake\Datasource\EntityInterface $entity  The entity to delete.
     * @param \ArrayObject                     $options The options for the delete.
     *
     * @throws \InvalidArgumentException if there are no primary key values of the
     *                                   passed entity
     *
     * @return bool success
     */
    protected function _processDelete($entity, $options)
    {
        if ($entity->isNew()) {
            return false;
        }

        $primaryKey = (array) $this->getPrimaryKey();
        if (!$entity->has($primaryKey)) {
            $msg = 'Deleting requires all primary key values.';

            throw new InvalidArgumentException($msg);
        }

        if ($options['checkRules'] && !$this->checkRules($entity, RulesChecker::DELETE, $options)) {
            return false;
        }

        $event = $this->dispatchEvent('Model.beforeDelete', [
            'entity'  => $entity,
            'options' => $options,
        ]);

        if ($event->isStopped()) {
            return $event->getResult();
        }

        $this->_associations->cascadeDelete(
            $entity,
            ['_primary' => false] + $options->getArrayCopy()
        );

        $query = $this->query();
        $conditions = (array) $entity->extract($primaryKey);
        $statement = $query->delete()
            ->where($conditions)
            ->execute();

        $success = $statement->rowCount() > 0;
        if (!$success) {
            return $success;
        }

        $this->dispatchEvent('Model.afterDelete', [
            'entity'  => $entity,
            'options' => $options,
        ]);

        return $success;
    }

    /**
     * Returns true if the finder exists for the table.
     *
     * @param string $type name of finder to check
     *
     * @return bool
     */
    public function hasFinder($type)
    {
        $finder = 'find'.$type;

        return method_exists($this, $finder) || ($this->_behaviors && $this->_behaviors->hasFinder($type));
    }

    /**
     * Calls a finder method directly and applies it to the passed query,
     * if no query is passed a new one will be created and returned.
     *
     * @param string          $type    name of the finder to be called
     * @param \Cake\ORM\Query $query   The query object to apply the finder options to
     * @param array           $options List of options to pass to the finder
     *
     * @throws \BadMethodCallException
     *
     * @return \Cake\ORM\Query
     */
    public function callFinder($type, Query $query, array $options = [])
    {
        $query->applyOptions($options);
        $options = $query->getOptions();
        $finder = 'find'.$type;
        if (method_exists($this, $finder)) {
            return $this->{$finder}($query, $options);
        }

        if ($this->_behaviors && $this->_behaviors->hasFinder($type)) {
            return $this->_behaviors->callFinder($type, [$query, $options]);
        }

        throw new BadMethodCallException(
            sprintf('Unknown finder method "%s"', $type)
        );
    }

    /**
     * Provides the dynamic findBy and findByAll methods.
     *
     * @param string $method The method name that was fired.
     * @param array  $args   List of arguments passed to the function.
     *
     * @throws \BadMethodCallException when there are missing arguments, or when
     *                                 and & or are combined.
     *
     * @return mixed
     */
    protected function _dynamicFinder($method, $args)
    {
        $method = Inflector::underscore($method);
        preg_match('/^find_([\w]+)_by_/', $method, $matches);
        if (empty($matches)) {
            // find_by_ is 8 characters.
            $fields = substr($method, 8);
            $findType = 'all';
        } else {
            $fields = substr($method, strlen($matches[0]));
            $findType = Inflector::variable($matches[1]);
        }
        $hasOr = strpos($fields, '_or_');
        $hasAnd = strpos($fields, '_and_');

        $makeConditions = function ($fields, $args) {
            $conditions = [];
            if (count($args) < count($fields)) {
                throw new BadMethodCallException(sprintf(
                    'Not enough arguments for magic finder. Got %s required %s',
                    count($args),
                    count($fields)
                ));
            }
            foreach ($fields as $field) {
                $conditions[$this->aliasField($field)] = array_shift($args);
            }

            return $conditions;
        };

        if ($hasOr !== false && $hasAnd !== false) {
            throw new BadMethodCallException(
                'Cannot mix "and" & "or" in a magic finder. Use find() instead.'
            );
        }

        $conditions = [];
        if ($hasOr === false && $hasAnd === false) {
            $conditions = $makeConditions([$fields], $args);
        } elseif ($hasOr !== false) {
            $fields = explode('_or_', $fields);
            $conditions = [
            'OR' => $makeConditions($fields, $args),
            ];
        } elseif ($hasAnd !== false) {
            $fields = explode('_and_', $fields);
            $conditions = $makeConditions($fields, $args);
        }

        return $this->find($findType, [
            'conditions' => $conditions,
        ]);
    }

    /**
     * Handles behavior delegation + dynamic finders.
     *
     * If your Table uses any behaviors you can call them as if
     * they were on the table object.
     *
     * @param string $method name of the method to be invoked
     * @param array  $args   List of arguments passed to the function
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        echo $method;
        print_r($args);
        exit;
        if ($this->_behaviors && $this->_behaviors->hasMethod($method)) {
            return $this->_behaviors->call($method, $args);
        }
        if (preg_match('/^find(?:\w+)?By/', $method) > 0) {
            return $this->_dynamicFinder($method, $args);
        }

        throw new BadMethodCallException(
            sprintf('Unknown method "%s"', $method)
        );
    }

    /**
     * Returns the association named after the passed value if exists, otherwise
     * throws an exception.
     *
     * @param string $property the association name
     *
     * @throws \RuntimeException if no association with such name exists
     *
     * @return \Cake\ORM\Association
     */
    public function __get($property)
    {
        $association = $this->_associations->get($property);
        if (!$association) {
            throw new RuntimeException(sprintf(
                'Table "%s" is not associated with "%s"',
                get_class($this),
                $property
            ));
        }

        return $association;
    }

    /**
     * Returns whether an association named after the passed value
     * exists for this table.
     *
     * @param string $property the association name
     *
     * @return bool
     */
    public function __isset($property)
    {
        return $this->_associations->has($property);
    }

    /**
     * Get the object used to marshal/convert array data into objects.
     *
     * Override this method if you want a table object to use custom
     * marshalling logic.
     *
     * @return \Cake\ORM\Marshaller
     *
     * @see \Cake\ORM\Marshaller
     */
    public function marshaller()
    {
        return new Marshaller($this);
    }

    /*
     * {@inheritDoc}
     *
     * By default all the associations on this table will be hydrated. You can
     * limit which associations are built, or include deeper associations
     * using the options parameter:
     *
     * ```
     * $article = $this->Articles->newEntity(
     *   $this->request->getData(),
     *   ['associated' => ['Tags', 'Comments.Users']]
     * );
     * ```
     *
     * You can limit fields that will be present in the constructed entity by
     * passing the `fields` option, which is also accepted for associations:
     *
     * ```
     * $article = $this->Articles->newEntity($this->request->getData(), [
     *  'fields' => ['title', 'body', 'tags', 'comments'],
     *  'associated' => ['Tags', 'Comments.Users' => ['fields' => 'username']]
     * ]
     * );
     * ```
     *
     * The `fields` option lets remove or restrict input data from ending up in
     * the entity. If you'd like to relax the entity's default accessible fields,
     * you can use the `accessibleFields` option:
     *
     * ```
     * $article = $this->Articles->newEntity(
     *   $this->request->getData(),
     *   ['accessibleFields' => ['protected_field' => true]]
     * );
     * ```
     *
     * By default, the data is validated before being passed to the new entity. In
     * the case of invalid fields, those will not be present in the resulting object.
     * The `validate` option can be used to disable validation on the passed data:
     *
     * ```
     * $article = $this->Articles->newEntity(
     *   $this->request->getData(),
     *   ['validate' => false]
     * );
     * ```
     *
     * You can also pass the name of the validator to use in the `validate` option.
     * If `null` is passed to the first param of this function, no validation will
     * be performed.
     *
     * You can use the `Model.beforeMarshal` event to modify request data
     * before it is converted into entities.
    public function newEntity($data = null, array $options = [])
    {
        if ($data === null) {
            $class = $this->getEntityClass();

            return new $class([], ['source' => $this->getRegistryAlias()]);
        }
        if (!isset($options['associated'])) {
            $options['associated'] = $this->_associations->keys();
        }
        $marshaller = $this->marshaller();

        return $marshaller->one($data, $options);
    }
     */

    /*
     * {@inheritDoc}
     *
     * By default all the associations on this table will be hydrated. You can
     * limit which associations are built, or include deeper associations
     * using the options parameter:
     *
     * ```
     * $articles = $this->Articles->newEntities(
     *   $this->request->getData(),
     *   ['associated' => ['Tags', 'Comments.Users']]
     * );
     * ```
     *
     * You can limit fields that will be present in the constructed entities by
     * passing the `fields` option, which is also accepted for associations:
     *
     * ```
     * $articles = $this->Articles->newEntities($this->request->getData(), [
     *  'fields' => ['title', 'body', 'tags', 'comments'],
     *  'associated' => ['Tags', 'Comments.Users' => ['fields' => 'username']]
     *  ]
     * );
     * ```
     *
     * You can use the `Model.beforeMarshal` event to modify request data
     * before it is converted into entities.
    public function newEntities(array $data, array $options = [])
    {
        if (!isset($options['associated'])) {
            $options['associated'] = $this->_associations->keys();
        }
        $marshaller = $this->marshaller();

        return $marshaller->many($data, $options);
    }
     */

    /*
     * {@inheritDoc}
     *
     * When merging HasMany or BelongsToMany associations, all the entities in the
     * `$data` array will appear, those that can be matched by primary key will get
     * the data merged, but those that cannot, will be discarded.
     *
     * You can limit fields that will be present in the merged entity by
     * passing the `fields` option, which is also accepted for associations:
     *
     * ```
     * $article = $this->Articles->patchEntity($article, $this->request->getData(), [
     *  'fields' => ['title', 'body', 'tags', 'comments'],
     *  'associated' => ['Tags', 'Comments.Users' => ['fields' => 'username']]
     *  ]
     * );
     * ```
     *
     * By default, the data is validated before being passed to the entity. In
     * the case of invalid fields, those will not be assigned to the entity.
     * The `validate` option can be used to disable validation on the passed data:
     *
     * ```
     * $article = $this->patchEntity($article, $this->request->getData(),[
     *  'validate' => false
     * ]);
     * ```
     *
     * You can use the `Model.beforeMarshal` event to modify request data
     * before it is converted into entities.
     *
     * When patching scalar values (null/booleans/string/integer/float), if the property
     * presently has an identical value, the setter will not be called, and the
     * property will not be marked as dirty. This is an optimization to prevent unnecessary field
     * updates when persisting entities.
    public function patchEntity(EntityInterface $entity, array $data, array $options = [])
    {
        if (!isset($options['associated'])) {
            $options['associated'] = $this->_associations->keys();
        }
        $marshaller = $this->marshaller();

        return $marshaller->merge($entity, $data, $options);
    }
     */

    /*
     * {@inheritDoc}
     *
     * Those entries in `$entities` that cannot be matched to any record in
     * `$data` will be discarded. Records in `$data` that could not be matched will
     * be marshalled as a new entity.
     *
     * When merging HasMany or BelongsToMany associations, all the entities in the
     * `$data` array will appear, those that can be matched by primary key will get
     * the data merged, but those that cannot, will be discarded.
     *
     * You can limit fields that will be present in the merged entities by
     * passing the `fields` option, which is also accepted for associations:
     *
     * ```
     * $articles = $this->Articles->patchEntities($articles, $this->request->getData(), [
     *  'fields' => ['title', 'body', 'tags', 'comments'],
     *  'associated' => ['Tags', 'Comments.Users' => ['fields' => 'username']]
     *  ]
     * );
     * ```
     *
     * You can use the `Model.beforeMarshal` event to modify request data
     * before it is converted into entities.
    public function patchEntities($entities, array $data, array $options = [])
    {
        if (!isset($options['associated'])) {
            $options['associated'] = $this->_associations->keys();
        }
        $marshaller = $this->marshaller();

        return $marshaller->mergeMany($entities, $data, $options);
    }
     */

    /*
     * Validator method used to check the uniqueness of a value for a column.
     * This is meant to be used with the validation API and not to be called
     * directly.
     *
     * ### Example:
     *
     * ```
     * $validator->add('email', [
     *  'unique' => ['rule' => 'validateUnique', 'provider' => 'table']
     * ])
     * ```
     *
     * Unique validation can be scoped to the value of another column:
     *
     * ```
     * $validator->add('email', [
     *  'unique' => [
     *      'rule' => ['validateUnique', ['scope' => 'site_id']],
     *      'provider' => 'table'
     *  ]
     * ]);
     * ```
     *
     * In the above example, the email uniqueness will be scoped to only rows having
     * the same site_id. Scoping will only be used if the scoping field is present in
     * the data to be validated.
     *
     * @param mixed $value The value of column to be checked for uniqueness.
     * @param array $options The options array, optionally containing the 'scope' key.
     *   May also be the validation context, if there are no options.
     * @param array|null $context Either the validation context or null.
     * @return bool True if the value is unique, or false if a non-scalar, non-unique value was given.
    public function validateUnique($value, array $options, array $context = null)
    {
        if ($context === null) {
            $context = $options;
        }
        $entity = new Entity(
            $context['data'],
            [
                'useSetters' => false,
                'markNew' => $context['newRecord'],
                'source' => $this->getRegistryAlias()
            ]
        );
        $fields = array_merge(
            [$context['field']],
            isset($options['scope']) ? (array)$options['scope'] : []
        );
        $values = $entity->extract($fields);
        foreach ($values as $field) {
            if ($field !== null && !is_scalar($field)) {
                return false;
            }
        }
        $rule = new IsUnique($fields, $options);

        return $rule($entity, ['repository' => $this]);
    }
     */

    /*
     * Get the Model callbacks this table is interested in.
     *
     * By implementing the conventional methods a table class is assumed
     * to be interested in the related event.
     *
     * Override this method if you need to add non-conventional event listeners.
     * Or if you want you table to listen to non-standard events.
     *
     * The conventional method map is:
     *
     * - Model.beforeMarshal => beforeMarshal
     * - Model.buildValidator => buildValidator
     * - Model.beforeFind => beforeFind
     * - Model.beforeSave => beforeSave
     * - Model.afterSave => afterSave
     * - Model.afterSaveCommit => afterSaveCommit
     * - Model.beforeDelete => beforeDelete
     * - Model.afterDelete => afterDelete
     * - Model.afterDeleteCommit => afterDeleteCommit
     * - Model.beforeRules => beforeRules
     * - Model.afterRules => afterRules
     *
     * @return array
    public function implementedEvents()
    {
        $eventMap = [
            'Model.beforeMarshal' => 'beforeMarshal',
            'Model.buildValidator' => 'buildValidator',
            'Model.beforeFind' => 'beforeFind',
            'Model.beforeSave' => 'beforeSave',
            'Model.afterSave' => 'afterSave',
            'Model.afterSaveCommit' => 'afterSaveCommit',
            'Model.beforeDelete' => 'beforeDelete',
            'Model.afterDelete' => 'afterDelete',
            'Model.afterDeleteCommit' => 'afterDeleteCommit',
            'Model.beforeRules' => 'beforeRules',
            'Model.afterRules' => 'afterRules',
        ];
        $events = [];

        foreach ($eventMap as $event => $method) {
            if (!method_exists($this, $method)) {
                continue;
            }
            $events[$event] = $method;
        }

        return $events;
    }
     */

    /*
     * {@inheritDoc}
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
    public function buildRules(RulesChecker $rules)
    {
        return $rules;
    }
     */

    /*
     * Gets a SaveOptionsBuilder instance.
     *
     * @param array $options Options to parse by the builder.
     * @return \Cake\ORM\SaveOptionsBuilder
    public function getSaveOptionsBuilder(array $options = [])
    {
        return new SaveOptionsBuilder($this, $options);
    }
     */

    /*
     * Loads the specified associations in the passed entity or list of entities
     * by executing extra queries in the database and merging the results in the
     * appropriate properties.
     *
     * ### Example:
     *
     * ```
     * $user = $usersTable->get(1);
     * $user = $usersTable->loadInto($user, ['Articles.Tags', 'Articles.Comments']);
     * echo $user->articles[0]->title;
     * ```
     *
     * You can also load associations for multiple entities at once
     *
     * ### Example:
     *
     * ```
     * $users = $usersTable->find()->where([...])->toList();
     * $users = $usersTable->loadInto($users, ['Articles.Tags', 'Articles.Comments']);
     * echo $user[1]->articles[0]->title;
     * ```
     *
     * The properties for the associations to be loaded will be overwritten on each entity.
     *
     * @param \Cake\Datasource\EntityInterface|array $entities a single entity or list of entities
     * @param array $contain A `contain()` compatible array.
     * @see \Cake\ORM\Query::contain()
     * @return \Cake\Datasource\EntityInterface|array
    public function loadInto($entities, array $contain)
    {
        return (new LazyEagerLoader)->loadInto($entities, $contain, $this);
    }
     */

    /*
     * {@inheritDoc}
    protected function validationMethodExists($method)
    {
        return method_exists($this, $method) || $this->behaviors()->hasMethod($method);
    }
     */

    /*
    public function __debug()
    {
        return ['debug', 'three' => 2];
    }
     */

    /*
     * Returns an array that can be used to describe the internal state of this
     * object.
     *
     * @return array
    public function __debugInfo()
    {
        return ['debugInfo', 'two' => 1];
        $conn = $this->getConnection();
        $associations = $this->_associations;
        $behaviors = $this->_behaviors;

        return [
            'registryAlias' => $this->getRegistryAlias(),
            'table' => $this->getTable(),
            'alias' => $this->getAlias(),
            'entityClass' => $this->getEntityClass(),
            'associations' => $associations ? $associations->keys() : false,
            'behaviors' => $behaviors ? $behaviors->loaded() : false,
            'defaultConnection' => static::defaultConnectionName(),
            'connectionName' => $conn ? $conn->configName() : null
        ];
    }
     */
}
