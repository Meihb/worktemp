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

    public function getRole()
    {
        
    }

}