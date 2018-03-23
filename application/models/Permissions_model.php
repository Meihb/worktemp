<?php

/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/3/22
 * Time: 12:41
 */
class Permissions_model extends CI_Model
{
    private $_tableName = 'ac_permissions';//表名

    public function __construct()
    {
        parent::__construct();
        $this->load->database('assess');
    }

    /**
     *获取权限列表
     * @param int $permission_id
     * @param int $status
     * @return array
     */
    public function getPermissions($permission_id = 0, $status = -1)
    {
        $sql = "SELECT menu_id,menu_name,menu_type,menu_parent,menu_api,menu_remark,menu_status,menu_plist
                        FROM {$this->_tableName} WHERE  menu_status >$status  ";
        if ($permission_id > 0) {
            $sql .= " AND menu_id = {$permission_id}";
        }
        return $this->db->query($sql)->result_array();
    }

    /**
     * 删除权限
     * @param $permission_id
     * @return int
     */
    public function delPermission($permission_id)
    {
        $this->db->query("UPDATE {$this->_tableName} SET
          menu_status = -1 WHERE menu_id = {$permission_id}");
        return $this->db->affected_rows();
    }

    /**
     * 新增权限
     * @param $name
     * @param $type
     * @param $parent
     * @param $api
     * @param $remark
     * @param $uid
     * @param $plist
     * @return int
     */
    public function addPermission($name, $type, $parent, $api, $remark, $uid, $plist)
    {
        $this->db->query("INSERT INTO {$this->_tableName} SET 
            menu_name = {$this->db->escape_str($name)},
            menu_type = {$type},
            menu_parent ={$parent},
            menu_api = {$this->db->escape_str($api)},
            menu_remark = {$this->db->escape_str($remark)},
            menu_status = 1,
            menu_operator = $uid,
            menu_operate_time = NOW(),
            menu_plist = {$this->db->escape_str($plist)}
            ");

        return $this->db->insert_id();
    }
}