<?php
/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/1/25
 * Time: 17:01
 */

class UserScaleModel extends CI_Controller
{

    /**
     * 获取游戏游戏名称列表,类型列表,限量列表,游戏IP选项列表
     * @return bool
     */
    /**
     * @SWG\GET(path="/index.php/UserScaleModel/getGameCondition",
     *      tags={"UserScaleModel"},
     *      summary="获取游戏条件详情",
     *      description="游戏名称列表、测试节点列表、测试性质列表,测试时间为2013年至今",
     *     @SWG\Response(
     *      response="default",
     *      description="操作成功",
     *     @SWG\Schema(ref="#/definitions/GameInfoDefination"),
     *     )
     * )
     */

    public function getGameCondition()
    {
        include_once "GameInfoDefination.php";
        $result = [
            'name' => GameInfoDefination::$nameList,
            'type' => GameInfoDefination::$typeList,
            'limit' => GameInfoDefination::$limitTypeList,
            'ip' => GameInfoDefination::$IPList,

        ];
        echo json_encode($result);
        return true;
    }


    /**
     * 留存率计算
     * all param get from  HTTP GET
     * @param string $name 游戏名称
     * @param string $type 游戏类别
     * @param string $limit 测试细致
     * @param string $ip ip
     * @param int $scale 用户规模
     * @param float $1dayret 次留
     * @param float $3dayret 3留
     * @param float $7dayret 7留
     * @param float $30dayret 30留
     * @param float $1dayltv 日LTV
     * @param float $7dayltv 7日LTV
     * @param float $30dayltv 30日LTV
     * @param int $modified_scale 变动后用户规模
     */
    /**
     * @SWG\GET(
     *     path="/index.php/UserScaleModel/evaluate",
     *     tags={"UserScaleModel"},
     *     summary="预估用户模型",
     *     @SWG\Parameter(name="name", type="string", required=false, in="query",description="游戏名称"),
     *     @SWG\Parameter(name="type", type="string", required=false ,in="query",description="游戏类型"),
     *     @SWG\Parameter(name="limit", type="string", required=false ,in="query",description="测试性质"),
     *     @SWG\Parameter(name="ip", type="string", required=false, in="query",description="是否有ip",default=""),
     *     @SWG\Parameter(name="scale", type="integer", required=true ,in="query",description="用户规模"),
     *     @SWG\Parameter(name="1dayret", type="number", required=true ,in="query",description="次留"),
     *     @SWG\Parameter(name="3dayret", type="number", required=true ,in="query",description="3留"),
     *     @SWG\Parameter(name="7dayret", type="number", required=true ,in="query",description="7留"),
     *     @SWG\Parameter(name="30dayret", type="number", required=true ,in="query",description="30日留"),
     *     @SWG\Parameter(name="1dayltv", type="number", required=true ,in="query",description="一天LTV"),
     *     @SWG\Parameter(name="7dayltv", type="number", required=true ,in="query",description="7天LTV"),
     *     @SWG\Parameter(name="30dayltv", type="number", required=true ,in="query",description="30天LTV"),
     *     @SWG\Parameter(name="modified_scale", type="integer", required=true ,in="query",description="修改后的用户规模"),
     *     @SWG\Response(
     *     response="default",
     *     description="操作成功",
     *     @SWG\Schema(
     *              @SWG\Property(type="integer",property="UserScale",description="用户规模"),
     *              @SWG\Property(type="number",property="first_day_ret",description="次留"),
     *              @SWG\Property(type="number",property="third_day_ret",description="3留"),
     *              @SWG\Property(type="number",property="seven_day_ret",description="7留"),
     *              @SWG\Property(type="number",property="thirty_day_ret",description="30留"),
     *              @SWG\Property(type="number",property="first_day_ltv",description="1天LTV"),
     *              @SWG\Property(type="number",property="seven_day_ltv",description="7天LTV"),
     *              @SWG\Property(type="number",property="thirty_day_ltv",description="30天LTV"),
*               ),
     *     )
     * )
     */
    public function evaluate()
    {
        include_once "GameInfoDefination.php";
        $gdf_obj = new GameInfoDefination();

        //获取传参
        $name = isset($_GET['name']) ? (string)$_GET['name'] : '';
        $type = isset($_GET['type']) ? (string)$_GET['type'] : '';
        $limit = isset($_GET['limit']) ? (string)$_GET['limit'] : '';
        $ip = isset($_GET['ip']) ? (string)$_GET['ip'] : '';

        //检测参数
        $errormsg = [];
        $nameKey = $gdf_obj->getNameKey($name, 'name', $errormsg);
        $type = $gdf_obj->getNameKey($type, 'type', $errormsg);
        $limit = $gdf_obj->getNameKey($limit, 'limit', $errormsg);
        $ip = $gdf_obj->getNameKey($ip, 'ip', $errormsg);

        $this->load->library('StandardOutPut');

        $lambda = function ($param) {
            return !empty($param);
        };
        /*获取非空定值*/
        $this->standardoutput->checkParam($scale = isset($_GET['scale']) ? (int)$_GET['scale'] : 0, $lambda, '请提供用户规模数', 400) or die(); //用户规模
        $this->standardoutput->checkParam($first_day_ret = isset($_GET['1dayret']) ? (float)$_GET['1dayret'] : 0, $lambda, '请提供首日留存率', 400) or die();//次留
        $this->standardoutput->checkParam($three_day_ret = isset($_GET['3dayret']) ? (float)$_GET['3dayret'] : 0, $lambda, '请提供三日留存率', 400) or die();//3日留存率
        $this->standardoutput->checkParam($seven_day_ret = isset($_GET['7dayret']) ? (float)$_GET['7dayret'] : 0, $lambda, '请提供七日留存率', 400) or die();//7日留存率
        $this->standardoutput->checkParam($thirty_day_ret = isset($_GET['30dayret']) ? (float)$_GET['30dayret'] : 0, $lambda, '请提供三十日留存率', 400) or die();//三十日留存率
        $this->standardoutput->checkParam($first_day_ltv = isset($_GET['1dayltv']) ? (float)$_GET['1dayltv'] : 0, $lambda, '请提供一天LTV', 400) or die();//ltv，lifetime value
        $this->standardoutput->checkParam($seven_day_ltv = isset($_GET['7dayltv']) ? (float)$_GET['7dayltv'] : 0, $lambda, '请提供七天LTV', 400) or die();//ltv，lifetime value
        $this->standardoutput->checkParam($thirty_day_ltv = isset($_GET['30dayltv']) ? (float)$_GET['30dayltv'] : 0, $lambda, '请提供三十天LTV', 400) or die();//ltv，lifetime value
        $this->standardoutput->checkParam($thirty_day_ltv = isset($_GET['modified_scale']) ? (float)$_GET['modified_scale'] : 0, $lambda, '请提供变化后的用户规模', 400) or die();//ltv，lifetime value


        var_dump($scale,$first_day_ret,$three_day_ret,$seven_day_ret,$thirty_day_ret,$first_day_ltv,$seven_day_ltv,$thirty_day_ltv);
        //todo 调用函数
    }
}