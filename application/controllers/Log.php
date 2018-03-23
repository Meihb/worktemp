<?php

/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/3/22
 * Time: 15:50
 */

/**
 * Class Log
 * USER_session_data_protocol userid,username,roleid
 */
class Log extends CI_Controller
{
    /**
     * Login constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('StandardOutPut');
    }

    static function getLogInInfo()
    {
        if (!session_id()) @session_start();
        return $_SESSION;
    }

    private function checkUserLog()
    {
        if (!session_id()) @session_start();
        if ($uid = $_SESSION['UID']) {
            return false;
        } else {
            return $uid;
        }
    }

    public function getRoleLevel($uid)
    {
        $this->load->model('UserRoleRelations_model');
        $role_info = $this->UserRoleRelations_model->
    }

    public function login()
    {
        $name = $this->input->post('name');
        $pwd = $this->input->post('pwd');
        $name = 'admin';
        $pwd = md5('admin1');

        if (empty($name) || empty($pwd)) {
            echo $res = $this->standardoutput->returnmsg(true, [], -401, '请重新提交登录信息');
            return false;
        }
        if (!session_id()) @session_start();
        $this->load->model('User_model');
        if (empty($user_info = $this->User_model->checkNamePwd($name, $pwd))) {//检查账号密码
            echo $res = $this->standardoutput->returnmsg(true, [], -401, '用户账号与密码不一致');
            return false;
        }
        $this->User_model->updateUserLoginDate($user_info['u_id']);//更新用户登录时间
        $role_info = $this->User->
        $user_info = $user_info[0];
        if (!isset($_SESSION['UID'])) {//用户未登录
            $_SESSION['UID'] = (int)$user_info['u_id'];
            $_SESSION['NAME'] = $user_info['u_login_name'];
        }
    }


}