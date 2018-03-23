<?php
/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/1/29
 * Time: 16:20
 */

class JapanGamesTurnoverEvaluation
{
    /**
     * @SWG\GET(path="/index.php/JapanGamesTurnoverEvaluation/getGameCondition",
     *      tags={"JapanGamesTurnoverEvaluation"},
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

    public function evalueate()
    {

    }
}