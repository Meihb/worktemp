<?php

/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/3/22
 * Time: 15:30
 */
class UserRoleRelations_model extends CI_Model
{
    private $_tableName = 'ac_userRoleRelations';//表名

    public function __construct()
    {
        parent::__construct();
        $this->load->database('assess');
    }

    /**
     * @param $uid
     * @return mixed
     */
    public function getRoleLevelByUid($uid)
    {
        return $this->db->query("
                SELECT u_login_name,role_id,role_name FROM {$this->_tableName} 
                LEFT JOIN ac_userRoleRelations ON urr_uid = $uid  AND urr_status>-1
                LEFT JOIN ac_roles ON  urr_roleid = role_id 
                WHERE u_id = {$uid}  AND u_status>-1 ")->result_array();
    }

}