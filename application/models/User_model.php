<?php

/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/3/22
 * Time: 11:41
 */
class User_model extends CI_Model
{
    private $_tableName = 'ac_users';//表名

    public function __construct()
    {
        parent::__construct();
        $this->load->database('default');
    }

    /**
     * 根据账号与密码查找正常用户
     * @param $name
     * @param $pwd
     * @return mixed
     */
    public function checkNamePwd($name, $pwd)
    {
        return ($this->db->query("SELECT u_id,u_login_name  FROM {$this->_tableName} 
        WHERE u_login_name = '{$this->db->escape_str($name)} '
        AND u_login_pwd = '{$this->db->escape_str($pwd)}'
        AND u_status =1")->result_array());
    }

    /**
     * 更新最近登录时间
     * @param $uid
     */
    public function updateUserLoginDate($uid)
    {
        $this->db->query("UPDATE {$this->_tableName} SET u_latest_date = NOW() WHERE u_id = $uid  ");
    }

    /**
     * 获取该用户能够查看的用户列表，即该用户参与创建的用户
     * @param $uid
     * @return array
     */
    public function getUserList($uid)
    {
        $query = $this->db->query("SELECT u_id,u_login_name,u_latest_date,u_reamrk FROM" . $this->_tableName . "
        WHERE 
        u_creator like '" . $this->db->escape_like_str("%/{$uid}/%") . "' 
        AND u_status>-1
        ");
        return $query->result_array();
    }

    /**
     * 创建用户
     * @param $name
     * @param $pwd
     * @param $remark
     * @param $creatorid
     * @return int
     */
    public function addUser($name, $pwd, $remark, $creatorid)
    {
        $this->db->query("INSERT INTO {$this->_tableName}
        SET 
        u_id = 0,
        u_login_name = '{$this->db->escape($name)}',
        u_latest_date = '',
        u_reamrk = '{$this->db->escape($remark)}',
        u_creator = '$creatorid',
        u_login_pwd = '{$this->db->escape($pwd)}'
        ");
        return $this->db->insert_id();
    }

    /**
     * 检查重名
     * @param $name
     * @param int $except_id
     * @return int
     */
    public function checkUserName($name, $except_id = 0)
    {
        $query = $this->db->query("SELECT count(u_id ) as total FROM {$this->_tableName } 
          WHERE  u_login_name ='{$this->db->escape($name)} 'AND u_id <>{$except_id}");
        return intval(($query->result_array())[0]['total']);
    }

    /**
     * 改变用户状态
     * @param $uid
     * @return bool
     */
    public function changeStatus($uid, $status)
    {
        $this->db->query("UPDATE  {$this->_tableName }
         SET u_status = $status
         WHERE u_id ={$uid}");
        return $this->db->affected_rows();
    }


}