<?php
/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/3/22
 * Time: 10:25
 */

/**
 * Class RoleBaseAccessControl
 * 用户角色与权限
 */
class RoleBaseAccessControl
{
    const Users = 'ac_users';//用户表
    const Roles = 'ac_roles';//角色表
    const Permissions = 'ac_Permissions';//权限表
    const UserRoleRelations = 'ac_userRoleRelations';//用户角色关系表
    const RolePermissionRelations = 'ac_rolePermissionRelations';//角色权限关系表

    private static $_supportTables = ['Users', 'Roles', 'Permissions', 'UserRoleRelations', 'RolePermissionRelations'];

    private $_Table;

    public function __construct($type)
    {
        if (false == empty($type)) {
            $this->createTable($type);
        }
    }

    public function createTable($type)
    {
        if (in_array($type, self::$_supportTables)) {
            include_once "../" . $type . '.php';
            echo self::$type;
            $this->_Table = new $type(self::$type);
        } else {
            throw new Exception('table ' . $type . ' not supported ', 404);
        }
    }


}