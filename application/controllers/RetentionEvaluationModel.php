<?php
/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/1/25
 * Time: 12:03
 */

class RetentionEvaluationModel extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->tableConfig = [
            1 => [
                "standard" => RetentionEvaluationModel::$retStationaryPoinInLimitedActiveCode,
                'name' => '限量(发放激活码)',
                'condition' => ['1RET' => '次留', "7RET" => '7留'],
                'optional' => []

            ],
            2 => [
                "standard" => RetentionEvaluationModel::$retStationaryPoinInUnlimitedPaymentRequired,
                'name' => '不限量、非付费测试',
                'condition' => ['1RET' => '次留', "7RET" => '7留', "1LR" => "一阶登录比", '2LR' => "二阶登录比", "14LTV" => "14日LTV值"],
                'optional' => ['1LR', '2LR']
            ],

            3 => [
                "standard" => RetentionEvaluationModel::$retStationaryPointInUnlimitedPaymentUnreqeted,
                'name' => '不限量、付费测试',
                'condition' => ['1RET' => '次留', "7RET" => '7留', "1LR" => "一阶登录比", '2LR' => "二阶登录比"],
                'optional' => ['1LR', '2LR']
            ]
        ];
    }

    public $tableConfig;//配置类型;
    static $positionName = [//区域名称
        'S', 'A', 'B', 'C'
    ];//等级名称
    static $retStationaryPoinInLimitedActiveCode = [//激活码限量评级标准包括 次留 和 七留
        '1RET' => [1, 0.65, 0.45, 0.30, 0],
        '7RET' => [1, 0.35, 0.20, 0.11, 0],
    ];
    static $retStationaryPoinInUnlimitedPaymentRequired = [//不限量付费评级标准 包括 次留 七留 一阶登录比 二阶登录比 14LTV
        '1RET' => [1, 0.45, 0.35, 0.25, 0],
        '7RET' => [1, 0.25, 0.15, 0.10, 0],
        '1LR' => [1, 0.75, 0.70, 0.65, 0],
        '2LR' => [1, 0.70, 0.65, 0.60, 0],
        '14LTV' => [null, 10, 5, 1, 0]
    ];
    static $retStationaryPointInUnlimitedPaymentUnreqeted = [//不限量不付费评级标准 包括 次留 七留 二阶登录比 二阶登录比
        '1RET' => [1, 0.45, 0.35, 0.25, 0],
        '7RET' => [1, 0.25, 0.15, 0.10, 0],
        '1LR' => [1, 0.75, 0.70, 0.65, 0],
        '2LR' => [1, 0.70, 0.65, 0.60, 0],
    ];

    static $levelWeight = ['S' => 0.415, 'A' => 0.301, 'B' => 0.034, 'C' => null];//nddr_regression_model 等级权重
    static $BIWeight = [
        [
            'range' => [null, 1000],
            'name' => 'BI_1',
            'weight' => 0,
        ],
        [
            'range' => [1000, 3000],
            'name' => 'BI_2',
            'weight' => 0.129,
        ],
        [
            'range' => [3000, 5000],
            'name' => 'BI_3',
            'weight' => 0.172,
        ],
        [
            'range' => [5000, 7000],
            'name' => 'BI_4',
            'weight' => 0.050,
        ],
        [
            'range' => [7000, 10000],
            'name' => 'BI_5',
            'weight' => 0.110,
        ],
        [
            'range' => [10000, 20000],
            'name' => 'BI_6',
            'weight' => 0.134,
        ],
        [
            'range' => [20000, null],
            'name' => 'BI_7',
            'weight' => 0.013,
        ],
    ];//百度指数 权重

    static $MaxScale = ['S' => 500000, 'A' => 500000, 'B' => 150000];//当前限制各等级下的最大人数规模

    /**
     * 比较数值与给定范围的关系,其中不确定的范围点可以用null表示以取消限制
     * @param $number
     * @param $max
     * @param $min
     * @return bool
     */
    private function compareRange($number, $max, $min)
    {
        $compare_start = true;
        $compare_end = true;
        if ($max !== null) {
            $compare_start = $number < $max;
        }
        if ($min !== null) {
            $compare_end = $number >= $min;
        }
        return $compare_start && $compare_end;
    }

    /**
     * 根据用户规模调整留存率,目前规则是 规模在5000以下时,差值每1K(ceil加一法计算,即不足1K按1K算)留存率向下调整1%
     * @param $scale
     * @param $ret
     * @return float
     */
    public function fineTuningScore($scale, $ret)
    {
        if ($scale >= 5000) {
            return $ret;
        } else {
            return $ret - 0.01 * ceil((5000 - $scale) / 1000);
        }
    }

    /**
     * 根据标注评测 等级
     * @param $score
     * @param array $scoreStandard
     * @param array $levelStandard
     * @return mixed|string
     * @throws Exception
     */
    private function getAssessmentLevcel($score, array $scoreStandard, array $levelStandard)
    {
        if (count($scoreStandard) < 2) {
            throw new Exception("得分评价标准错误", -100);
        }
        if (count($scoreStandard) - 1 != count($levelStandard)) {
            throw new Exception("得分评价标准与得分等级不符", -101);
        }
        $start_index = 0;
        $level = "";
        while (($start_index + 1) < count($scoreStandard)) {
            //当评分标准数组剩余超过两项
            if (!$result = $this->compareRange($score, $scoreStandard[$start_index], $scoreStandard[$start_index + 1])) {// 未找到 合适评级时
                $start_index += 1;
            } else {
                $level = $levelStandard[$start_index];
                break;
            }
        }
        if (empty($level)) {
            throw new Exception('无法正确评级', -102);
        } else {
            return $start_index;
        }
    }

    /**
     * 计算最终评级,以最高结果为准，若只有一项满足标准,则为 评级标准-
     * @param array $result
     * @return mixed
     */
    public function addUp(array $result)
    {
        $level = static::$positionName[min(array_values($result))];

        return (count(array_unique($result)) == 1) ? $level : $level . "-";
    }


    /**
     * 获取游戏条件
     * @return array
     */
    public function getGameCondition()
    {

        $data = array_map(function (array $arr) {
            if (isset($arr['standard'])) {
                unset($arr['standard']);
            }
            return $arr;
        },
            $this->tableConfig);
        $this->load->library('StandardOutPut');
        echo $this->standardoutput->returnmsg(true, $data, '正确');
    }

    /**
     * 访问html
     */
    public function index()
    {
        $info['data'] = array_map(function (array $arr) {
            if (isset($arr['standard'])) {
                unset($arr['standard']);
            }
            return $arr;
        },
            $this->tableConfig);
        $this->load->view('retEvaluate', $info);
    }


    /**
     * 留存率计算
     * all param get from  HTTP GET
     * @param string $name 游戏名称
     * @param string $type 游戏类别
     * @param string $limit 测试细致
     * @param string $ip ip
     * @param int $scale 用户规模
     * @param float $1day 次留
     * @param float $3day 3留
     * @param float $7day 7留
     * @param float $ltv LTV
     */
    /**
     * @SWG\GET(
     *     path="/index.php/RetentionEvaluationModel/evaluate",
     *     tags={"RetentionEvaluationModel"},
     *     summary="留存率评估",
     *     @SWG\Parameter(name="name", type="string", required=false, in="query",description="游戏名称"),
     *     @SWG\Parameter(name="type", type="string", required=false ,in="query",description="游戏类型"),
     *     @SWG\Parameter(name="limit", type="string", required=false ,in="query",description="测试性质"),
     *     @SWG\Parameter(name="ip", type="string", required=false, in="query",description="是否有ip"),
     *     @SWG\Parameter(name="scale", type="integer", required=true ,in="query",description="用户规模"),
     *     @SWG\Parameter(name="1dayret", type="number", required=true ,in="query",description="次留"),
     *     @SWG\Parameter(name="3dayret", type="number", required=true ,in="query",description="3留"),
     *     @SWG\Parameter(name="7dayret", type="number", required=true ,in="query",description="7留"),
     *     @SWG\Parameter(name="ltv", type="number", required=true ,in="query",description="LTV"),
     *     @SWG\Response(
     *     response="default",
     *     description="操作成功",
     *     @SWG\Schema(
     *              @SWG\Property(type="string",property="class",description="等级"),
     *               ),
     *     )
     * )
     */
    public function evaluate()
    {

        $this->load->library('StandardOutPut');

        if (empty($_GET['key'])) {//获取关键传参 key
            echo $this->standardoutput->returnmsg(false, [], '请提供游戏限量类型', -100);
            return false;
        }
        if (empty($_GET['scale']) || empty((int)$_GET['scale'])) {//获取 市场规模
            echo $this->standardoutput->returnmsg(false, [], '请提供正确的游戏市场规模', -100);
            return false;
        }
        $key = (int)$_GET['key'];
        $scale = (int)$_GET['scale'];
        if (!isset($this->tableConfig[$key])) {
            echo $this->standardoutput->returnmsg(false, [], '错误的游戏限量类型', -100);
            return false;
        };
        $standard = $this->tableConfig[$key]['standard'];
        $condition = $this->tableConfig[$key]['condition'];
        $level = static::$positionName;
        $fineTuning_list = ['1RET', '7RET'];//此两个数据需要根据用户规模调整
        $optional_param = ['1LR', '2LR'];//一阶登录比与二阶登录比可不提供
        $level_result = [];//存储评级结果


        //一阶登录比与二阶登录比可不提供
        $lambda = function ($keyname) use ($optional_param) {
            $check = false;
            if (in_array($keyname, $optional_param)) {//非登录比可不提供
                if (isset($_GET[$keyname]) && !empty($_GET[$keyname])) {
                    $check = true;
                }
            } else {//其他参数必须提供
                if (!isset($_GET[$keyname])) {
                    throw new Exception($keyname);
                }
                $check = true;
            }
            if ($check) {//检查是否为数字类型
                if (!is_numeric($_GET[$keyname])) {
                    throw new Exception($keyname);
                }
                return number_format($_GET[$keyname], 4);
            } else {
                return false;
            }
        };


        /***************循环判断条件参数****************************/
        foreach ($condition as $ckey => $value) {
            try {
                $check = $lambda($ckey);
                if ($check !== false) {//false 即数据不参考
                    $param = in_array($ckey, $fineTuning_list) ? $this->fineTuningScore($scale, $check) : $check;//根据用户规模调整
                    $level_result[$ckey] = $this->getAssessmentLevcel($param, $standard[$ckey], $level);
                }
            } catch (Exception $exception) {
                echo $this->standardoutput->returnmsg(false, [$ckey], '请提供正确的' . $value, -101);
                return false;
            }
        }
        if (!empty($level_result)) {
            $result = $this->addUp($level_result);
            echo $this->standardoutput->returnmsg(true, $result);
            return true;
        } else {
            echo $this->standardoutput->returnmsg(false, [], '数据不足,无法评级', -102);
            return false;
        }
    }


    public function getNDRRPage()
    {
        $this->load->view('ndrr');
    }


    public function compareBI($number, $start, $end)//比较BI阶段
    {
        $flag_start = true;
        $flag_end = true;
        if ($start != null) {
            $flag_start = $number >= $start;
        }
        if ($end != null) {
            $flag_end = $number < $end;
        }
        return $flag_start && $flag_end;

    }

    /**
     * 获取nddr页面条件
     * @return bool
     */
    public function getNDRRCondition()
    {
        $data = [];
        include_once "GameInfoDefination.php";
        $data['type'] = array_values(GameInfoDefination::$typeList);
        $data['level'] = array_slice(static::$positionName, 0, 3);
        $this->load->library('StandardOutPut');
        echo $this->standardoutput->returnmsg(true, $data, '');
        return false;
    }


    /**
     * 计算 次留
     * y=0.311-0.056*ln(规模/1000)+0.034*B类+0.301*A类+0.415*S类+0.129*BI_2+0.172*BI_3+0.050*BI_4+0.110*BI_5+0.134*BI_6+0.013*BI_7;
     * @param $type
     * @param $scale
     * @param $level
     * @param $bi
     * @return float|int|mixed
     */
    public function evaluateNDRR($type, $scale, $level, $bi)
    {
        $result = 0.311;

        if ($scale > 0) {
            $scale = $scale > static::$MaxScale[$level] ? static::$MaxScale[$level] : $scale;
            $result -= 0.056 * log($scale / 1000);//规模权重
        }
        $result += isset(static::$levelWeight[$level]) ? static::$levelWeight[$level] : 0;//level权重
        $bi_weight = 0;
        foreach (static::$BIWeight as $value) {
            if ($this->compareBI($bi, $value['range'][0], $value['range'][1])) {
                $bi_weight = $value['weight'];
                break;
            }
        }
        $result += $bi_weight;//百度指数权重

        return (number_format($result, 3) * 100) . '%';
    }

    /**
     * @return bool
     */
    public function showNDDR()
    {
        include_once "GameInfoDefination.php";
        $this->load->library('StandardOutPut');

        $check = function ($name) {
            if ($_GET[$name] && !empty($_GET[$name])) {
                return $_GET[$name];
            }
            throw  new Exception('参数错误');
        };
        try {
            $type = $check('game_type');
            $level = $check('game_level');
            $scale = $check('game_scale');
            $bi = $check('game_bi');

            echo $this->standardoutput->returnmsg(true, ['ndrr' => $this->evaluateNDRR($type, (int)$scale, $level, (int)$bi)]);
            return true;

        } catch (Exception $e) {
            echo $this->standardoutput->returnmsg(false, [], $e->getMessage());
            return false;
        }

    }


}