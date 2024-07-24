<?php
/*
    This file is part of the eQual framework <http://www.github.com/cedricfrancoys/equal>
    Some Rights Reserved, Cedric Francoys, 2010-2021
    Licensed under GNU LGPL 3 license <http://www.gnu.org/licenses/>
*/
namespace equal\db;

/**
 * This class is used as abstract class providing members and methods signature for DBManipulator class that extend it.
 */
class DBManipulator {

    /**
     * DB server hostname.
     *
     * @var		string
     */
    protected $host;

    /**
     * DB server TCP connection port.
     *
     * @var		integer
     */
    protected $port;

    /**
     * DB name to use for SQL queries.
     *
     * @var		string
     */
    protected $db_name;

    /**
     * Charset encoding to use for communications with DBMS.
     *
     * @var		string
     */
    protected $charset;

    /**
     * Collation to use for storing data (applied on new tables and columns).
     *
     * @var		string
     */
    protected $collation;

    /**
     * DB user
     *
     * @var		string
     */
    protected $user_name;

    /**
     * DB password
     *
     * @var		string
     */
    protected $password;

    /**
     * Latest error id
     *
     * @var		integer
     */
    protected $last_id;

    /**
     * Number of rows affected by last query
     *
     * @var		integer
     */
    protected $affected_rows;

    /**
     * Latest query
     *
     * @var		string
     */
    protected $last_query;

    /**
     * @var     mixed
     */
    protected $dbms_handler;

    /**
     * Array of DBManipulator objects to secondary DBMS / members of the replica, if any
     *
     * @var		array
     * @access	protected
     */
    protected $members;


    /**
     * Class constructor
     *
     * Initialize the DBMS data for SQL transactions
     *
     * @access   public
     * @param    string    DB server hostname
     * @param    string    DB name
     * @param    string    Username to use for the connection
     * @param    string    Password to use for the connection
     * @return   void
     */
    public final function __construct($host, $port, $db, $user, $pass, $charset=null, $collation=null) {
        $this->host         = $host;
        $this->port         = $port;
        $this->db_name      = $db;
        $this->user_name    = $user;
        $this->password     = $pass;
        $this->charset      = $charset;
        $this->collation    = $collation;

        $this->dbms_handler = null;
        $this->members      = [];
    }


    public final function __destruct() {
        $this->disconnect();
    }

    /**
     * Add a replica member
     */
    public function addMember(DBManipulator $member) {
        $this->members[] = $member;
    }

    /**
     * Open the DBMS connection
     *
     * @return   integer   The status of the connect function call (ie: a handler to identify the connection)
     * @access   public
     */
    public function connect($auto_select=true) {
        return $this;
    }

    public function select($db_name) {
    }

    public function connected() {
        return (bool) $this->dbms_handler;
    }

    /**
    * Close the DBMS connection
    *
    * @return   integer   Status of the close function call
    * @access   public
    */
    public function disconnect() {
    }

    /**
     * Returns the SQL type to use for a given ORM type.
     * This method is meant to be overloaded in children DBManipulator classes.
     * @deprecated - use DataAdapterProviderSql::get(<usage>)
     */
    public function getSqlType($type) {
        return '';
    }

    /**
    * Checks the connectivity to an SQL server
    *
    * @return boolean    false if no connection can be made, true otherwise
    *
    */
    public function canConnect() {
        if($fp = fsockopen($this->host, $this->port, $errno, $errstr, 1)) {
            fclose($fp);
            return true;
        }
        return false;
    }

    /**
     * Sends a SQL query.
     *
     * @param string The query to send to the DBMS.
     *
     * @return resource Returns a resource identifier or -1 if the query was not executed correctly.
     */
    public function sendQuery($query) {
	}

    public final function isEmpty($value) {
        return (empty($value) || $value == '0000-00-00' || !strcasecmp($value, 'false') || !strcasecmp($value, 'null'));
    }

    public function getLastId() {
        return $this->last_id;
    }


    public function getAffectedRows() {
        return $this->affected_rows;
    }

    public function getLastQuery() {
        return $this->last_query;
    }

    public static function fetchRow($result) {
        return [];
    }

    public static function fetchArray($result) {
        return [];
    }

    protected function setLastId($id) {
        $this->last_id = $id;
    }

    protected function setAffectedRows($affected_rows) {
        $this->affected_rows = $affected_rows;
    }

    protected function setLastQuery($query) {
        $this->last_query = $query;
    }

    /**
     * Get records from specified table, according to some conditions.
     *
     * @param	array   $tables       name of involved tables
     * @param	array   $fields       list of requested fields
     * @param	array   $ids          ids to which the selection is limited
     * @param	array   $conditions   list of arrays (field, operand, value)
     * @param	string  $id_field     name of the id field ('id' by default)
     * @param	mixed   $order        string holding name of the order field or maps holding field names as keys and sorting as value
     * @param	integer $start
     * @param	integer $limit
     *
     * @return	resource              reference to query resource
     */
    public function getRecords($tables, $fields=NULL, $ids=NULL, $conditions=NULL, $id_field='id', $order=[], $start=0, $limit=0) {}

    public function setRecords($table, $ids, $fields, $conditions=null, $id_field='id') {}

    /**
     * Inserts new records in specified table.
     *
     * @param	string      $table name of the table in which insert the records.
     * @param	array       $fields list of involved fields.
     * @param	array       $values array of arrays specifying the values related to each specified field.
     * @return	resource    Reference to query resource.
     */
    public function addRecords($table, $fields, $values) {}

    public function deleteRecords($table, $ids, $conditions=null, $id_field='id') {}

    /**
     * Fetch and increment the column of a series of records in a single operation.
     * This method implements FAA instruction (fetch-and-add) in order to read and update a column as an atomic operation.
     *
     * @param int $increment    A numeric value used to increment columns (if value positive) or decrement columns (if value is negative).
     */
    public function incRecords($table, $ids, $field, $increment, $id_field='id') {}


    /*
        SQL request generation helpers
    */


    /**
     * Creates a new database.
     *
     * Generates a SQL query and create a new database according to given $db_name.
     *
     * @param string $db_name   The name of the database to create.
     * @return string SQL query to create a database.
     */
    public function createDatabase($db_name) {}

    /**
     * Generates a SQL query to retrieve a list of all tables from the current database.
     *
     * @return string SQL query to retrieve all tables.
     */
    public function getTables() {}

    /**
     * Generates a SQL query to get the schema of the specified table.
     *
     * @param string $table_name    The name of the table whose schema is to be retrieved.
     * @return string SQL query to get the table schema.
     */
    public function getTableSchema($table_name) {}

    /**
     * Generates a SQL query to get the columns of the specified table.
     *
     * @param string $table_name    The name of the table whose columns are to be retrieved.
     * @return string SQL query to get the table columns.
     */
    public function getTableColumns($table_name) {}

    /**
     * Generates a SQL query to get the constraints of the specified table.
     *
     * @param string $table_name    The name of the table whose constraints are to be retrieved.
     * @return string SQL query to get the table constraints.
     */
    public function getTableUniqueConstraints($table_name) {}

    /**
     * Generates the SQL query to create a specific table.
     * The query holds a condition to run only if the table does not exist yet.
     *
     * @param string $table_name    The name of the table for which the creation SQL is generated.
     * @return string SQL query to create the specified table.
     */
    public function getQueryCreateTable($table_name) {}

    /**
     * Generates one or more SQL queries related to a column creation, according to a given column definition.
     *
     * $def structure:
     * [
     *      'type'             => int(11),
     *      'null'             => false,
     *      'default'          => 0,
     *      'auto_increment'   => false,
     *       'primary'         => false,
     *      'index'            => false
     * ]
     * @param string $table_name    The name of the table to modify.
     * @param string $column_name   The name of the column to add.
     * @param array $def            Array describing the column properties such as type, nullability, default value, etc.
     * @return string SQL query to add a column.
    */
    public function getQueryAddColumn($table_name, $column_name, $def) {}

    /**
     * Generates a SQL query to add an index to a table.
     *
     * @param string $table_name    The name of the table.
     * @param string $column        The name of the column to index.
     * @return string SQL query to add an index.
     */
    public function getQueryAddIndex($table_name, $column) {}

    /**
     * Generates a SQL query to add a unique constraint to one or more columns in a table.
     *
     * @param string $table_name        The name of the table.
     * @param array|string $columns     The name(s) of the column(s) to include in the unique constraint.
     * @return string SQL query to add a unique constraint.
     */
    public function getQueryAddUniqueConstraint($table_name, $columns) {}

    /**
     * Generates a SQL query to add records to a table.
     *
     * @param string $table     The name of the table where records will be added.
     * @param array $fields     Array of field names corresponding to the columns in the table.
     * @param array $values     Array of values to be inserted; each sub-array corresponds to a row.
     * @return string SQL query to add records.
     */
    public function getQueryAddRecords($table, $fields, $values) {}

    /**
     * Generates a SQL query to set columns according to an associative array, for all records of a table.
     *
     * @param string $table     Name of the table where records will be added.
     * @param array $fields     Associative array mapping field names (columns) to values those must be updated to.
     * @param array $values     Array of values to be inserted; each sub-array corresponds to a row.
     * @return string SQL query to add records.
     */
    public function getQuerySetRecords($table, $fields) {}
}
