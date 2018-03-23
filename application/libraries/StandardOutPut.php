<?php
/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/1/24
 * Time: 14:38
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class StandardOutPut
{
    /**
     * @param bool $bool
     * @param array $data
     * @param int $errorcode
     * @param string $errormsg
     * @return string
     */
    public function returnmsg($bool = true, $data = [], $errormsg = '', $errorcode = 200)
    {
        $res = [
            'IsSuccess' => $bool,
            'Data' => $data,
            'Errorcode' => $errorcode,
            'Errormsg' => $errormsg
        ];
        return json_encode($res);
    }

    /**
     * @param $var
     * @param callable $verify
     * @param string $errormsg
     * @param int $errorcode
     * @return bool|string
     */
    public function checkParam($var, callable $verify, $errormsg = 'Invalid param.', $errorcode = 400)
    {
        if (!$verify($var)) {//未能通过审查
//            $var = $this->returnmsg(false, [], $errormsg, $errorcode);
//            echo $var;
//            return false;
            throw  new Exception($errormsg, $errorcode);
        }
        return $var;
    }
}