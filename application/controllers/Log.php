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
    private $_rid;//用户角色id

    /**
     * Login constructor.
     */
    public function __construct()
    {
        @session_start();
        parent::__construct();
        $this->load->library('StandardOutPut');
        $this->_rid = 0;
    }

    static function getLogInInfo()
    {
        if (!session_id()) @session_start();
        return $_SESSION;
    }

    /**
     * 检查当前是否登录
     * @return bool
     */
    private function checkUserLog()
    {

        if ($uid = $_SESSION['UID']) {
            return false;
        } else {
            return $uid;
        }
    }

    public function alertErrorOld($Str)
    {
        echo "<script>".chr(10);
        if(!empty($Str)){
            echo "alert(\"\\n\\n{$Str}\\n\\n\");".chr(10);
        }
        echo "</script>".chr(10);
    }
    function alertError($Str,$Typ="back",$TopWindow="",$Tim=100){
        echo "<script>".chr(10);
        if(!empty($Str)){
            echo "alert(\"\\n\\n{$Str}\\n\\n\");".chr(10);
        }
        echo "function _r_r_(){";
        $WinName=(!empty($TopWindow))?"top":"self";
        switch (StrToLower($Typ)){
            case "#":
                break;
            case "back":
                echo $WinName.".history.go(-1);".chr(10);
                break;
            case "reload":
                echo $WinName.".window.location.reload();".chr(10);
                break;
            case "close":
                echo "window.opener=null;window.close();".chr(10);
                break;
            case "function":
                echo "var _T=new function('return {$TopWindow}')();_T();".chr(10);
                break;
            //Die();
            Default:
                if($Typ!=""){
                    //echo "window.{$WinName}.location.href='{$Typ}';";
                    echo "window.{$WinName}.location=('{$Typ}');";
                }
        }
        echo "}".chr(10);
        //為防止Firefox不執行setTimeout
        echo "if(setTimeout(\"_r_r_()\",".$Tim.")==2){_r_r_();}";
        if($Tim==100){
            echo "_r_r_();".chr(10);
        }else{
            echo "setTimeout(\"_r_r_()\",".$Tim.");".chr(10);
        }
        echo "</script>".chr(10);
        Exit();
    }

    /**
     * roleLevel 根管理员为1,其余已绑定角色的用户角色等级为2,未绑定的用户角色等级为0
     * @param $uid
     * @return int
     */
    public function getRoleLevel($uid)
    {
        if ($uid == 1) {
            return 1;
        }
        $this->load->model('UserRoleRelations_model');
        $role_info = $this->UserRoleRelations_model->getRoleLevelByUid($uid);

        $role_info = $role_info[0];
        if (empty($role_info['role_id'])) {
            $this->_rid = (int)$role_info['role_id'];
            return 2;
        } else {
            return 0;
        }
    }

    /**
     * 用户登录
     * @return bool
     */
    public function logIn()
    {
        $name = @$_POST['name'];
        $pwd = @$_POST['pwd'];

        if (empty($name) || empty($pwd)) {
            echo $res = $this->standardoutput->returnmsg(false, [], '登录信息不足');
            return false;
        }
        $this->load->model('User_model');
        if (empty($user_info = $this->User_model->checkNamePwd($name, $pwd))) {//检查账号密码
            echo $res = $this->standardoutput->returnmsg(false, [], '用户账号与密码不一致', -401);
//            $this->load->view('login', ['errormsg' => '用户账号与密码不一致']);
            return false;
        }
        $user_info = $user_info[0];
        $this->User_model->updateUserLoginDate($user_info['u_id']);//更新用户登录时间
        $role_level = $this->getRoleLevel((int)$user_info['u_id']);//获取用户角色等级

        if (!isset($_SESSION['UID'])) {//用户未登录
            $_SESSION['UID'] = (int)$user_info['u_id'];
            $_SESSION['NAME'] = $user_info['u_login_name'];
            $_SESSION['LEVEL'] = $role_level;
            $_SESSION['RID'] = $this->_rid;

            $expire = 24 * 3600;
            setcookie('user_name', $user_info['u_login_name'], time() + $expire);
            echo $res = $this->standardoutput->returnmsg(true);
            return true;
        } else {//重新登录
            echo $res = $this->standardoutput->returnmsg(true);
            return true;
        }
    }

    /**
     * 退出登录
     * @return bool
     */
    public function logOut()
    {
        if (@$_SESSION['UID']) {
            $_SESSION = [];
            session_destroy();
            $this->alertError('退出成功');
//            echo $res = $this->standardoutput->returnmsg(true, [], 200, '退出成功');
//            sleep(3);
//            header("location:" . $_SERVER['HTTP_REFERER']);
//            return false;
        } else {
//            echo $res = $this->standardoutput->returnmsg(false, [], -401, '当前非登录状态');
            header("location:../index.php?/Log/userLogin");
            return false;
        }
    }


    public function userLogin()
    {
        $this->load->view('login', ['errormsg' => '']);
    }

    public function test()
    {
        $this->load->view('test', ['errormsg' => '']);
    }

}