<?php
/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/1/26
 * Time: 11:53
 * @name ExternalGameHeadcountEvaluation 外部游戏人数预估
 */

class ExternalGameHeadcountEvaluation
{
    /**
     * 获取游戏游戏名称列表,类型列表,游戏IP选项列表，发行公司列表
     * @return bool
     */
    /**
     * @SWG\GET(path="/index.php/ExternalGameHeadcountEvaluation/getGameCondition",
     *      tags={"ExternalGameHeadcountEvaluation"},
     *      summary="获取流水预估条件列表",
     *      description="游戏名称列表、游戏类型列表、是否有ip,发行商列表",
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
            'distributor' => GameInfoDefination::$Distributor,

        ];
        echo json_encode($result);
        return true;
    }

    /**
     * @SWG\GET(
     *     path="/index.php/ExternalGameHeadcountEvaluation/evaluate",
     *     tags={"ExternalGameHeadcountEvaluation"},
     *     summary="预估流水",
     *     @SWG\Parameter(name="name", type="string", required=false, in="query",description="游戏名称"),
     *     @SWG\Parameter(name="type", type="string", required=false ,in="query",description="游戏类型"),
     *     @SWG\Parameter(name="ip", type="string", required=false, in="query",description="是否有ip"),
     *     @SWG\Parameter(name="distributor", type="string", required=false, in="query",description="发行商"),
     *     @SWG\Parameter(name="average_kg_perday", type="integer", required=true ,in="query",description="近30天日均百度知识"),
     *     @SWG\Parameter(name="max_kg", type="integer", required=true ,in="query",description="历史最高百度指数"),
     *     @SWG\Parameter(name="min_kg", type="integer", required=true ,in="query",description="最低百度指数"),
     *     @SWG\Parameter(name="cumulative_days", type="integer", required=true ,in="query",description="累计天数"),
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
    public function evaluate()
    {
        include_once "GameInfoDefination.php";
        $gdf_obj = new GameInfoDefination();

        //获取传参
        $type = isset($_GET['type']) ? (string)$_GET['type'] : '';
        $ip = isset($_GET['ip']) ? (string)$_GET['ip'] : '';
        $name = isset($_GET['name']) ? (string)$_GET['name'] : '';
        $distributor = isset($_GET['distributor']) ? (string)$_GET['distributor'] : '';

        //检测参数
        $errormsg = [];
        $name = $gdf_obj->getNameKey($name, 'name', $errormsg);
        $type = $gdf_obj->getNameKey($type, 'type', $errormsg);
        $ip = $gdf_obj->getNameKey($ip, 'ip', $errormsg);
        $distributor = $gdf_obj->getNameKey($distributor, 'distributor', $errormsg);

        $lambda = function ($param) {
            return !empty($param);
        };
        /*获取非空定值*/
        $this->standardoutput->checkParam($average_kg_perday = isset($_GET['average_kg_perday']) ? (int)$_GET['average_kg_perday'] : 0, $lambda, '请提供百度知识图谱', 400) or die(); //百度知识图谱
        $this->standardoutput->checkParam($max_kg = isset($_GET['max_kg']) ? (int)$_GET['max_kg'] : 0, $lambda, '请提供历史最高百度指数', 400) or die(); //历史最高百度指数
        $this->standardoutput->checkParam($min_kg = isset($_GET['min_kg']) ? (float)$_GET['min_kg'] : 0, $lambda, '请提供历史最低百度指数', 400) or die();//历史最低百度指数
        $this->standardoutput->checkParam($cumulative_days = isset($_GET['cumulative_days']) ? (float)$_GET['cumulative_days'] : 0, $lambda, '请提供累计天数', 400) or die();//累计天数


    }
}