<?php
/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/1/25
 * Time: 17:41
 * 流水预估模型
 */

class TurnoverEvaluationModel extends CI_Controller
{
    /**
     * 获取游戏游戏名称列表,类型列表,游戏IP选项列表
     * @return bool
     */
    /**
     * @SWG\GET(path="/index.php/TurnoverEvaluateModel/getGameCondition",
     *      tags={"TurnoverEvaluationModel"},
     *      summary="获取流水预估条件列表",
     *      description="游戏名称列表、游戏类型列表、是否有ip",
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
            'ip' => GameInfoDefination::$IPList,

        ];
        echo json_encode($result);
        return true;
    }

    /**
     * 流水预估
     * all param get from  HTTP GET
     * @param string $type 游戏类别
     * @param string $name 游戏名
     * @param string $ip ip
     * @param int $market_cost 用户规模
     * @param int $estimated_quantity 预定量
     * @param float $1dayret 次留
     * @param float $7dayret 7留
     * @param float $30dayret 30留
     * @param float $7dayltv 7日LTV
     * @param float $30dayltv 30日LTV
     * @param float $arpv 每用户平均收入
     */
    /**
     * @SWG\GET(
     *     path="/index.php/TurnoverEvaluateModel/currentEvaluate",
     *     tags={"TurnoverEvaluationModel"},
     *     summary="预估流水",
     *     @SWG\Parameter(name="name", type="string", required=false, in="query",description="游戏名称"),
     *     @SWG\Parameter(name="type", type="string", required=false ,in="query",description="游戏类型"),
     *     @SWG\Parameter(name="ip", type="string", required=false, in="query",description="是否有ip"),
     *     @SWG\Parameter(name="market_cost", type="integer", required=true ,in="query",description="市场费金额"),
     *     @SWG\Parameter(name="estimated_quantity", type="number", required=true ,in="query",description="预定量"),
     *     @SWG\Parameter(name="1dayret", type="number", required=true ,in="query",description="次留"),
     *     @SWG\Parameter(name="7dayret", type="number", required=true ,in="query",description="7留"),
     *     @SWG\Parameter(name="30dayret", type="number", required=true ,in="query",description="30日留"),
     *     @SWG\Parameter(name="7dayltv", type="number", required=true ,in="query",description="7天LTV"),
     *     @SWG\Parameter(name="arpu", type="number", required=true ,in="query",description="ARPV"),
     *     @SWG\Parameter(name="modified_scale", type="integer", required=true ,in="query",description="修改后的用户规模"),
     *     @SWG\Response(
     *     response="default",
     *     description="操作成功",
     *     @SWG\Schema(
     *              @SWG\Property(type="integer",property="first_month_turnover",description="首月流水"),
     *              @SWG\Property(type="integer",property="third_month_turnover",description="三月流水"),
     *              @SWG\Property(type="integer",property="six_month_turnover",description="半年流水"),
     *               ),
     *     )
     * )
     */
    public function currentEvaluate()
    {
        include_once "GameInfoDefination.php";
        $gdf_obj = new GameInfoDefination();

        //获取传参
        $type = isset($_GET['type']) ? (string)$_GET['type'] : '';
        $ip = isset($_GET['ip']) ? (string)$_GET['ip'] : '';
        $name = isset($_GET['name']) ? (string)$_GET['name'] : '';

        //检测参数
        $errormsg = [];
        $name = $gdf_obj->getNameKey($name, 'name', $errormsg);
        $type = $gdf_obj->getNameKey($type, 'type', $errormsg);
        $ip = $gdf_obj->getNameKey($ip, 'ip', $errormsg);

        $this->load->library('StandardOutPut');

        $lambda = function ($param) {
            return !empty($param);
        };
        /*获取非空定值*/
        $this->standardoutput->checkParam($market_cost = isset($_GET['market_cost']) ? (int)$_GET['market_cost'] : 0, $lambda, '请提供市场费', 400) or die(); //用户规模
        $this->standardoutput->checkParam($estimated_quantity = isset($_GET['estimated_quantity']) ? (int)$_GET['estimated_quantity'] : 0, $lambda, '请提供预定量', 400) or die(); //预定量
        $this->standardoutput->checkParam($first_day_ret = isset($_GET['1dayret']) ? (float)$_GET['1dayret'] : 0, $lambda, '请提供首日留存率', 400) or die();//次留
        $this->standardoutput->checkParam($seven_day_ret = isset($_GET['7dayret']) ? (float)$_GET['7dayret'] : 0, $lambda, '请提供七日留存率', 400) or die();//7日留存率
        $this->standardoutput->checkParam($thirty_day_ret = isset($_GET['30dayret']) ? (float)$_GET['30dayret'] : 0, $lambda, '请提供三十日留存率', 400) or die();//三十日留存率
        $this->standardoutput->checkParam($seven_day_ltv = isset($_GET['7dayltv']) ? (float)$_GET['7dayltv'] : 0, $lambda, '请提供七天LTV', 400) or die();//ltv，lifetime value
        $this->standardoutput->checkParam($arpv = isset($_GET['arpv']) ? (float)$_GET['arpv'] : 0, $lambda, '请提供ARPV', 400) or die();//arpv


//        var_dump($scale,$first_day_ret,$three_day_ret,$seven_day_ret,$thirty_day_ret,$first_day_ltv,$seven_day_ltv,$thirty_day_ltv);
        //todo 调用函数
    }

    /**
     * 利润率预估
     * all param get from  HTTP GET
     * @param string $type 游戏类别
     * @param string $name 游戏名
     * @param string $ip ip
     *
     * @param int $turnover 流水
     * @param int $copyright_fee 版权金
     * @param float $agent_share 代理分成
     * @param float $dev_share 研发分成
     * @param float $ios_income_ratio ios收入比例
     * @param float $android_income_ratio 安卓收入比例
     * @param float $gplus_income_ratio G+收入比例
     * @param int  $idc_cost IDC成本
     * @param  int $headcount 人员数量
     */
    /**
     * @SWG\GET(
     *     path="/index.php/TurnoverEvaluateModel/profitRateEvaluate",
     *     tags={"TurnoverEvaluationModel"},
     *     summary="预估利润率",
     *     @SWG\Parameter(name="name", type="string", required=false, in="query",description="游戏名称"),
     *     @SWG\Parameter(name="type", type="string", required=false ,in="query",description="游戏类型"),
     *     @SWG\Parameter(name="ip", type="string", required=false, in="query",description="是否有ip"),
     *     @SWG\Parameter(name="turnover", type="integer", required=true ,in="query",description="流水"),
     *     @SWG\Parameter(name="copyright_fee", type="number", required=true ,in="query",description="版权金"),
     *     @SWG\Parameter(name="agent_share", type="number", required=true ,in="query",description="代理分成"),
     *     @SWG\Parameter(name="dev_share", type="number", required=true ,in="query",description="研发分成"),
     *     @SWG\Parameter(name="ios_income_ratio", type="number", required=true ,in="query",description="ios收入比例"),
     *     @SWG\Parameter(name="android_income_ratio", type="number", required=true ,in="query",description="安卓渠道收入比例"),
     *     @SWG\Parameter(name="gplus_income_ratio", type="number", required=true ,in="query",description="官方G+收入比例"),
     *     @SWG\Parameter(name="idc_cost", type="number", required=true ,in="query",description="IDC成本"),
     *     @SWG\Parameter(name="headcount", type="integer", required=true ,in="query",description="人员数量"),
     *     @SWG\Response(
     *     response="default",
     *     description="操作成功",
     *     @SWG\Schema(
     *              @SWG\Property(type="integer",property="first_month_profit_ration",description="一个月利润率"),
     *              @SWG\Property(type="integer",property="six_month_profit_ration",description="半年利润率"),
     *              @SWG\Property(type="integer",property="first_year_profit_ration",description="一年利润率"),
     *               ),
     *     )
     * )
     */
    public function profitRateEvaluate()
    {
        include_once "GameInfoDefination.php";
        $gdf_obj = new GameInfoDefination();

        //获取传参
        $type = isset($_GET['type']) ? (string)$_GET['type'] : '';
        $ip = isset($_GET['ip']) ? (string)$_GET['ip'] : '';
        $name = isset($_GET['name']) ? (string)$_GET['name'] : '';

        //检测参数
        $errormsg = [];
        $name = $gdf_obj->getNameKey($name, 'name', $errormsg);
        $type = $gdf_obj->getNameKey($type, 'type', $errormsg);
        $ip = $gdf_obj->getNameKey($ip, 'ip', $errormsg);

        $this->load->library('StandardOutPut');

        $lambda = function ($param) {
            return !empty($param);
        };
        /*获取非空定值*/
        $this->standardoutput->checkParam($turnover = isset($_GET['turnover']) ? (int)$_GET['turnover'] : 0, $lambda, '请提供流水', 400) or die(); //流水
        $this->standardoutput->checkParam($copyright_fee = isset($_GET['copyright_fee']) ? (int)$_GET['copyright_fee'] : 0, $lambda, '请提供版权金', 400) or die(); //版权金
        $this->standardoutput->checkParam($agent_share = isset($_GET['agent_share']) ? (float)$_GET['agent_share'] : 0, $lambda, '请提供代理分成', 400) or die();//代理分成
        $this->standardoutput->checkParam($dev_share = isset($_GET['dev_share']) ? (float)$_GET['dev_share'] : 0, $lambda, '请提供研发分成', 400) or die();//研发分成
        $this->standardoutput->checkParam($ios_income_ratio = isset($_GET['ios_income_ratio']) ? (float)$_GET['ios_income_ratio'] : 0, $lambda, '请提供ios收入比例', 400) or die();//ios收入比例
        $this->standardoutput->checkParam($android_income_ratio = isset($_GET['android_income_ratio']) ? (float)$_GET['android_income_ratio'] : 0, $lambda, '请提供安卓渠道收入比例', 400) or die();//安卓渠道收入比例
        $this->standardoutput->checkParam($gplus_income_ratio = isset($_GET['gplus_income_ratio']) ? (float)$_GET['gplus_income_ratio'] : 0, $lambda, '请提供G+收入比例', 400) or die();//G+渠道收入比例
        $this->standardoutput->checkParam($idc_cost = isset($_GET['idc_cost']) ? (int)$_GET['idc_cost'] : 0, $lambda, '请提供IDC成本', 400) or die();//IDC成本
        $this->standardoutput->checkParam($headcount = isset($_GET['headcount']) ? (int)$_GET['headcount'] : 0, $lambda, '请提供员工数量', 400) or die();//员工数量


        //todo 调用函数
    }

    /**
     * 流水预估
     * all param get from  HTTP GET
     * @param string $type 游戏类别
     * @param string $name 游戏名
     * @param string $ip ip
     * @param int $estimated_quantity 预定量
     * @param float $1dayret 次留
     * @param float $7dayret 7留
     * @param float $30dayret 30留
     * @param float $7dayltv 7日LTV
     * @param float $30dayltv 30日LTV
     * @param float $arpv 每用户平均收入
     */
    /**
     * @SWG\GET(
     *     path="/index.php/TurnoverEvaluateModel/marketFeeEvaluate",
     *     tags={"TurnoverEvaluationModel"},
     *     summary="预估最有市场费金额",
     *     @SWG\Parameter(name="name", type="string", required=false, in="query",description="游戏名称"),
     *     @SWG\Parameter(name="type", type="string", required=false ,in="query",description="游戏类型"),
     *     @SWG\Parameter(name="ip", type="string", required=false, in="query",description="是否有ip"),
     *     @SWG\Parameter(name="estimated_quantity", type="number", required=true ,in="query",description="预定量"),
     *     @SWG\Parameter(name="1dayret", type="number", required=true ,in="query",description="次留"),
     *     @SWG\Parameter(name="7dayret", type="number", required=true ,in="query",description="7留"),
     *     @SWG\Parameter(name="30dayret", type="number", required=true ,in="query",description="30日留"),
     *     @SWG\Parameter(name="7dayltv", type="number", required=true ,in="query",description="7天LTV"),
     *     @SWG\Parameter(name="arpu", type="number", required=true ,in="query",description="ARPV"),
     *     @SWG\Response(
     *     response="default",
     *     description="操作成功",
     *     @SWG\Schema(
     *              @SWG\Property(type="integer",property="efficiency_market_fee",description="最优市场费"),
     *               ),
     *     )
     * )
     */
    public function marketFeeEvaluate()
    {
        include_once "GameInfoDefination.php";
        $gdf_obj = new GameInfoDefination();

        //获取传参
        $type = isset($_GET['type']) ? (string)$_GET['type'] : '';
        $ip = isset($_GET['ip']) ? (string)$_GET['ip'] : '';
        $name = isset($_GET['name']) ? (string)$_GET['name'] : '';

        //检测参数
        $errormsg = [];
        $name = $gdf_obj->getNameKey($name, 'name', $errormsg);
        $type = $gdf_obj->getNameKey($type, 'type', $errormsg);
        $ip = $gdf_obj->getNameKey($ip, 'ip', $errormsg);

        $this->load->library('StandardOutPut');

        $lambda = function ($param) {
            return !empty($param);
        };
        /*获取非空定值*/
        $this->standardoutput->checkParam($estimated_quantity = isset($_GET['estimated_quantity']) ? (int)$_GET['estimated_quantity'] : 0, $lambda, '请提供预定量', 400) or die(); //预定量
        $this->standardoutput->checkParam($first_day_ret = isset($_GET['1dayret']) ? (float)$_GET['1dayret'] : 0, $lambda, '请提供首日留存率', 400) or die();//次留
        $this->standardoutput->checkParam($seven_day_ret = isset($_GET['7dayret']) ? (float)$_GET['7dayret'] : 0, $lambda, '请提供七日留存率', 400) or die();//7日留存率
        $this->standardoutput->checkParam($thirty_day_ret = isset($_GET['30dayret']) ? (float)$_GET['30dayret'] : 0, $lambda, '请提供三十日留存率', 400) or die();//三十日留存率
        $this->standardoutput->checkParam($seven_day_ltv = isset($_GET['7dayltv']) ? (float)$_GET['7dayltv'] : 0, $lambda, '请提供七天LTV', 400) or die();//ltv，lifetime value
        $this->standardoutput->checkParam($arpv = isset($_GET['arpv']) ? (float)$_GET['arpv'] : 0, $lambda, '请提供ARPV', 400) or die();//arpv


//        var_dump($scale,$first_day_ret,$three_day_ret,$seven_day_ret,$thirty_day_ret,$first_day_ltv,$seven_day_ltv,$thirty_day_ltv);
        //todo 调用函数
    }

}