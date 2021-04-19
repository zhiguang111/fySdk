<?php

namespace fysdk\sdk;


class Pdo
{
    private static $_instance;
    private $Pdo;

    private $PdoStmt;
    private $table;
    private $fieldsName;
    private $fieldsVal;
    private $where;
    private $order;
    private $limit;
    private $sql;

    private $transaction = 0;

    private $whereRule = [
        'IN', 'NOT IN', 'IS', 'IS NOT', 'LIKE', 'NOT LIKE', '>', '>=', '=', '<', '<=', '<>', 'BETWEEN',
        'NOT BETWEEN'
    ];

    private function __construct($argument)
    {
        $this->connect($argument);
    }

    /**
     * 连接MySQL PDO方式
     * @param $argument array 连接参数
     * @throws \Exception
     */
    private function connect($argument)
    {
        try {
            $this->Pdo = new \PDO(
                $argument['dsn'],
                $argument['username'],
                $argument['password']
            );
        } catch (\Exception $e) {
            throw new \Exception('Connect Mysql error');
        }
    }

    private function __clone()
    {
    }

    public static function getInstance($argument)
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self($argument);
        }
        return self::$_instance;
    }


    private function getTableOrFields($args, $type)
    {
        if (!is_string($args) && !is_array($args)) {
            return false;
        }

        if (is_string($args)) {
            $this->$type = $args;
            return;
        }

        foreach ($args as $tname => $tas) {
            $str = '';
            if (is_numeric($tname)) {

                if ($type == 'order') {
                    $str = $tas . ' ASC ';
                } else {
                    $str = $tas;
                }

                $this->$type .= (empty($this->$type)) ?
                    $str :
                    ', ' . $str;
            } elseif (is_string($tname)) {

                if ($type == 'order') {
                    $str = $tname . ' ' . $tas . ' ';
                } else {
                    $str = $tname . ' AS ' . $tas;
                }

                $this->$type .= (empty($this->$type)) ?
                    $str :
                    ', ' . $str;
            }
        }

    }


    /**
     * 执行添加INSERT
     * @param array $insertData
     * @return mixed
     * @throws \Exception
     */
    public function insert(array $insertData)
    {
        //设置添加数据
        $this->setFields($insertData);
        return $this->query('INSERT');
    }

    /**
     * 执行修改UPDATE
     * @param array $updateData
     * @return mixed
     * @throws \Exception
     */
    public function update(array $updateData)
    {
        $this->setFields($updateData);
        return $this->query('UPDATE');
    }

    /**
     * 执行删除数据
     * @param null $primaryValue
     * @return mixed
     * @throws \Exception
     */
    public function delete($primaryValue = null)
    {
        if (is_int($primaryValue) || is_array($primaryValue)) {
            $primaryKey = $this->getTablePrimaryKey();

            if (is_int($primaryValue)) {
                $this->where = $primaryKey .'='. $primaryValue;
            } else {
                foreach ($primaryValue as $idVal) {
                    $this->where .= (empty($this->where)) ?
                        $primaryKey .'='. $idVal :
                        ' OR ' . $primaryKey .'='. $idVal;
                }
            }

        }
        return $this->query('DELETE');
    }

    /**
     * 获取单条数据
     * @return array|mixed
     * @throws \Exception
     */
    public function find()
    {
        $result = $this->query('SELECT');
        if (is_array($result) && isset($result[0])) {
            return $result[0];
        }
        return [];
    }

    /**
     * 获取多条数据 SELECT
     * @return mixed
     * @throws \Exception
     */
    public function select()
    {
        return $this->query('SELECT');
    }

    public function where($arg, $condition = null, $pars = null)
    {
        $argNumber =  func_num_args();

        $this->where = $this->getTablePrimaryKey() . ' IS NOT NULL ';
        if ($argNumber == 3 && in_array(strtoupper($condition), $this->whereRule)) {
            $condition = strtoupper($condition);
            if (
                ($condition == 'BETWEEN' || $condition == 'NOT BETWEEN')
                && is_array($pars)
                && count($pars) == 2
            ) {
                $this->where .= 'AND ('. $arg .' '. $condition .' '. $pars[0] . ' AND '. $pars[1] .') ';
            } else {
                $this->where .= 'AND ('. $arg .' '. $condition .' '. $pars . ') ';
            }
        } elseif ($argNumber == 2) {
            $this->where .= 'AND ('. $arg .' = '. $condition . ') ';
        } else {

            if (is_string($arg)) {
                $this->where = $arg;
            } elseif (is_array($arg)) {

                if (count($arg, 1) != count($arg)) {
                    $moreWhereString = '';
                    $moreWhereSet = isset($arg['logic']) ? $arg['logic'] : 'AND';
                    foreach ($arg as $oneRule) {

                        if (is_array($oneRule)) {

                            if (count($oneRule) == 2) {
                                $moreWhereString .= empty($moreWhereString) ?
                                     '  '. $oneRule[0] .' = '. $oneRule[1] . ' ' :
                                    $moreWhereSet . '  '. $oneRule[0] .' = '. $oneRule[1] . ' ';
                            } elseif (count($oneRule) == 3 && in_array(strtoupper($oneRule[1]), $this->whereRule)) {
                                $condition = strtoupper($oneRule[1]);

                                if (
                                    ($condition == 'BETWEEN' || $condition == 'NOT BETWEEN')
                                    && is_array($oneRule[2])
                                    && count($oneRule[2]) == 2
                                ) {
                                    $moreWhereString .= empty($moreWhereString) ?
                                        ' '. $oneRule[0] .' '. $condition .' '. $oneRule[2][0] . ' AND '. $oneRule[2][1] .' ' :
                                        $moreWhereSet . ' '. $oneRule[0] .' '. $condition .' '. $oneRule[2][0] . ' AND '. $oneRule[2][1] .' ';
                                } else {
                                    $moreWhereString .= empty($moreWhereString) ?
                                        ' '. $oneRule[0] .' '. $condition .' '. $oneRule[2] . ' ' :
                                        $moreWhereSet .' '. $oneRule[0] .' '. $condition .' '. $oneRule[2] . ' ';
                                }
                            }
                        }
                    }

                    $this->where .= " AND ({$moreWhereString})";

                } else {
                    if (count($arg) == 2) {
                        $this->where .= 'AND ('. $arg[0] .' = '. $arg[1] . ') ';
                    } elseif (count($arg) == 3 && in_array(strtoupper($arg[1]), $this->whereRule)) {
                        $condition = strtoupper($arg[1]);
                        if (
                            ($condition == 'BETWEEN' || $condition == 'NOT BETWEEN')
                            && is_array($arg[2])
                            && count($arg[2]) == 2
                        ) {
                            $this->where .= 'AND ('. $arg[0] .' '. $condition .' '. $arg[2][0] . ' AND '. $arg[2][1] .') ';
                        } else {
                            $this->where .= 'AND ('. $arg[0] .' '. $condition .' '. $arg[2] . ') ';
                        }

                    }
                }

            }

        }
        return $this;

    }


    /**
     * 设置操作表
     * @param $table mixed
     * @return $this|bool
     */
    public function table($table)
    {
        $this->getTableOrFields($table, 'table');
        return $this;
    }

    /**
     * 设置字段名
     * @param $fields
     * @return $this
     */
    public function field($fields)
    {
        $this->getTableOrFields($fields, 'fieldsName');
        return $this;
    }

    /**
     * 设置LIMIT参数
     * @param $limit
     * @return $this
     */
    public function limit($start, $number = null)
    {
        if (is_string($start)) {
            $this->limit = $start;
        }
        if (is_int($start)) {
            $this->limit = " {$start} ";
            if (is_int($number)) $this->limit .= ", {$number} ";
        }
        return $this;
    }

    /**
     * 设置ORDER排序参数
     * @param $order
     * @return $this
     */
    public function order($order)
    {
        $this->getTableOrFields($order, 'order');
        return $this;
    }

    /**
     * 获取新增ID
     * @return int 新增ID
     */
    public function lastInstertId()
    {
        return $this->Pdo->lastInsertId();
    }


    /**
     * 执行SQL
     * @param $method string CURD
     * @param string $sql  外部SQL
     * @return mixed bool|Object
     * @throws \Exception
     */
    public function query($method, $sql = '')
    {
        if (
            $method == 'INSERT'
            || $method == 'UPDATE'
            || $method == 'DELETE'
            || $method == 'SELECT'
        ) {
            $this->createSql($method);
        }

        if (!empty($sql)) $this->sql = $sql;

//        var_dump($this->sql);

        try {
            $this->PdoStmt = $this->Pdo->prepare($this->sql);
            $result =  $this->PdoStmt->execute(explode(', ', $this->fieldsVal));
            $this->clearAllArguments();
            if (
                $method == 'INSERT'
                || $method == 'UPDATE'
                || $method == 'DELETE'
            ) {
                return $this->PdoStmt->rowCount();
            } else{
                return $this->PdoStmt->fetchAll();
            }
        } catch (\Exception $e) {
            throw new \Exception('SQL is error. ' . $this->sql);
        }
    }


    /**
     * 构建SQL
     * @param $method
     */
    private function createSql($method)
    {
        if ($method == 'INSERT') {
            $execVal = preg_replace('/\w+/', '?', $this->fieldsName);
            $this->sql = "INSERT INTO {$this->table} ({$this->fieldsName}) VALUES({$execVal})";
        }

        if ($method == 'UPDATE') {
            $fieldsArr = explode(', ', $this->fieldsName);
            $setStr = '';
            foreach ($fieldsArr as $one) {
                $setStr .= empty($setStr) ?
                    $one .'=?':
                    ', ' . $one .'=?';
            }

            $this->sql = "UPDATE {$this->table} SET {$setStr}";
            if (!empty($this->where)) {
                $this->sql .= " WHERE {$this->where}";
            }

        }

        if ($method == 'DELETE' && !empty($this->where)) {
            $this->sql = "DELETE FROM {$this->table} WHERE {$this->where}";
        }

        if ($method == 'SELECT') {
            $this->sql = "SELECT {$this->fieldsName} FROM {$this->table} ";
            if (!empty($this->where)) {
                $this->sql .= " WHERE {$this->where} ";
            }
            if (!empty($this->order)) {
                $this->sql .= " ORDER BY {$this->order} ";
            }
            if (!empty($this->limit)) {
                $this->sql .= " LIMIT {$this->limit} ";
            }
        }

    }

    /**
     * 设置添加修改字段参数
     * @param array $insertData
     */
    private function setFields(array $insertData)
    {
        foreach ($insertData as $key => $val) {
            $this->fieldsName .= empty($this->fieldsName) ?
                $key :
                ', '. $key;
            $this->fieldsVal .= empty($this->fieldsVal) ?
                $val :
                ', '. $val;
        }
    }

    /**
     * 获取表主键名
     * @return mixed
     * @throws \Exception
     */
    public function getTablePrimaryKey()
    {
        $fieldsAll = $this->getTableFields();
        foreach ($fieldsAll as $oneField) {
            if ($oneField['Key'] == 'PRI') {
                return $oneField['Field'];
            }
        }
    }

    /**
     * 取表所有字段名
     * @throws \Exception
     */
    public function getTableFields()
    {
        return $this->query('SHOW', 'SHOW columns FROM '.$this->table);
    }

    /**
     * 清除所有执行参数
     */
    private function clearAllArguments()
    {
//        $this->PdoStmt = null;
//        $this->table = null;
        $this->fieldsName = null;
        $this->fieldsVal = null;
        $this->where = null;
        $this->sql = null;
    }

}