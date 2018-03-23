<?php
/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/3/22
 * Time: 15:31
 */

class RolePermissionRelations_model extends CI_Model
{
    private $_tableName = 'ac_rolePermissionRelations';//表名

    public function __construct()
    {
        parent::__construct();
        $this->load->database('assess');
    }

}