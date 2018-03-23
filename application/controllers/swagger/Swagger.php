<?php
/**
 * @SWG\Swagger(
 *   schemes={"http"},
 *   host="assess.act.sdo.com:81",
 *   consumes={"multipart/form-data"},
 *   produces={"application/json"},
 *   @SWG\Info(
 *     version="1.0",
 *     title="my project doc",
 *     description="游戏数据评估接口文档, V1.0<br>"
 *   ),
 *
 *   @SWG\Tag(
 *     name="GameDataOverview",
 *     description="游戏数据概览",
 *   ),
 *
 *   @SWG\Tag(
 *     name="RetentionEvaluationModel",
 *     description="留存率评估模型",
 *   ),
 *
 *   @SWG\Tag(
 *     name="TurnoverEvaluationModel",
 *     description="流水预估模型",
 *   ),
 *
 *   @SWG\Tag(
 *     name="UserScaleModel",
 *     description="用户规模模型",
 *   ),
 *
 *   @SWG\Tag(
 *     name="JapanGamesTurnoverEvaluation",
 *     description="日本游戏流水预估",
 *   ),
 *
 *
 *   @SWG\Tag(
 *     name="ExternalGameHeadcountEvaluation",
 *     description="外部游戏人数预估",
 *   ),
 * )
 */

/**
 *  @SWG\DEFINITION(
 *     definition="GameData",
 *     @SWG\Property(
 *             property="name",
 *             description="游戏名称",
 *             type="string",
 *         ),
 *     @SWG\Property(
 *             property="year",
 *             description="测试时间",
 *             type="string",
 *         ),
 *     @SWG\Property(
 *             property="node",
 *             description="测试节点",
 *             type="string",
 *         ),
 *     @SWG\Property(
 *             property="limit",
 *             description="是否限量",
 *             type="string",
 *         ),
 *      @SWG\Property(
 *             property="first_week_scale",
 *             description="首周用户规模",
 *             type="integer",
 *         ),
 *          @SWG\Property(
 *             property="first_dat_retention",
 *             description="次留",
 *             type="float",
 *         ),
 *          @SWG\Property(
 *             property="third_day_retention",
 *             description="3留",
 *             type="float",
 *         ),
 *         @SWG\Property(
 *             property="seven_day_retention",
 *             description="7留",
 *             type="float",
 *         ),
 *         @SWG\Property(
 *             property="payment_ratio",
 *             description="付费率",
 *             type="float",
 *         ),
 *         @SWG\Property(
 *             property="arrpu",
 *             description="平均每付费用户收入",
 *             type="float",
 *         ),
 *         @SWG\Property(
 *             property="arpu",
 *             description="平均每用户收入",
 *             type="float",
 *         ),
 *         @SWG\Property(
 *             property="firsy_month_turnover",
 *             description="首月流水",
 *             type="float",
 *         ),
 *         @SWG\Property(
 *             property="third_month_turnover",
 *             description="三月流水",
 *             type="integer",
 *         ),
 * )
 *  @SWG\Definition(definition="GameDatas",
 *         type="array",
 *         @SWG\Items(ref="#/definitions/GameData")
 *     )
 */

