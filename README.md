#### 创建
    调用FactoryModel::creatSdkFactory($arg1, $arg2);
    
    $arg1：XXX::class 例如WanWanSdk::class
   
    $arg2：数据库链接参数array
    [
      'username' => '',
      'password' => '',
      'port' => '',
      'host' => '',
      'dbName' => ''
    ];
    result：对应sdk的操作类
#### Orm
    可用过FactoryModel::creatSdkFactory()返回的对象进行操作
    如:
    $sdk = FactoryModel::creatSdkFactory($arg1, $arg2);
    $sdk->getUserInfo();
---
    对应sdk方法可以在对应sdk中添加，比如玩玩sdk需要一个获取订单的方法
    在WanWanSdk中添加
    public function getOrder(){};
---
#### 操作ORM举例(select)
     $this->db->table($table)//设置表名
          ->where([
              ['uid','=',$userId],
              ['uid','>','1']
          ])
          ->field('*')//查询字段
          ->find();//查询一条记录 ->select()查询多条记录
     where 常用举例
     ["uid = {$uid}"] 可以以字符串形式传入 注意双引号
     ['uid', '=', $uid] 一纬数组
     [
        ['uid', '=', $uid],
        ['uid', '>' , 1],
     ]二维数组
#### 原生语法操作
    BaseSdk提供一个querySql($arg1, $arg2);方法
    arg1 : 'INSERT'，'UPDATE','DELETE','SELECT'
    arg2 : 原生sql
    
#### TODO.....
