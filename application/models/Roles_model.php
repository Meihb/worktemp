<?php

/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/3/22
 * Time: 12:15
 */
class Roles_model extends CI_Model
{
    private $_tableName = 'ac_roles';//表名

    public function __construct()
    {
        parent::__construct();
        $this->load->database('assess');
    }

    /**
     * 获取该用户枝下的角色信息
     * @param $creatoruid
     * @return mixed
     */
    public function getRolesList($creatoruid)
    {
        $query = $this->db->query("
                  SELECT role_id,role_name,role_remark  FROM {$this->_tableName}
                  WHERE role_creatoruid = '{$creatoruid}' AND role_status>-1
                  ");
        return $query->result_array();
    }

    /**
     * 创建角色
     * @param $name
     * @param $remark
     * @param $creatoruid
     * @return int
     */
    public function addRole($name, $remark, $creatoruid)
    {
        $this->db->query("INSERT INTO {$this->_tableName} SET 
                          role_name = {$this->db->escape_str($name)},
                          role_remark = {$this->db->escape_str($remark)},
                          role_creatoruid = '{$creatoruid}',
                          role_status = 1
                          ");
        return $this->db->insert_id();
    }

    /**
     * 删除角色
     * @param $rid
     * @return int
     */
    public function delRole($rid)
    {
        $this->db->query("UPDATE {$this->_tableName} SET role_status = -1 WHERE role_id = $rid");
        return $this->db->affected_rows();
    }

}