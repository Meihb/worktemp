<?php
/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/1/18
 * Time: 17:18
 */

/*
 * 游戏数据概览
 * 游戏id     ==>名 起始时间 测试节点 测试性质(限量(发放激活码),不限量分两种非付费与付费两种?) 是否有IP
 * 游戏类型id   ==》
 */

class GameDataOverview extends CI_Controller
{
    public function index()
    {
        $data['init'] = $this->getGameCondition(true);
        $this->load->view('gamedata', $data);
    }

    public function sysindex()
    {
        $this->load->view('system\Index');
    }

    public function indexv2()
    {
        $this->load->view('newgamedata');
    }

    /**
     * @param $number
     * 小数点保留一位,整数千位制
     */
    public function formatNumber($number, $percentage = false)
    {
        if (is_numeric($number)) {
            if ($number == -1) {
                $number = "";
            } else {
                if (!$percentage) {
                    $number = ($number == (int)$number) ? number_format($number, 0, '.', ',') : number_format($number, 1, '.', ',');//去掉一位小数为0
                } else {
                    $number = sprintf("%.1f", $number * 100) . "%";
                }
            }

        }
        return $number;
    }

    public function testUserSql()
    {
        $this->load->database('assess');
        $query = $this->db->query('SELECT * FROM game_data_accesslist WHERE 1');
        var_dump($query->result_array());

    }

    /**
     * @return array|bool
     */
    public function testExcel()
    {
        $filename = __DIR__ . "/games.xlsx";
        include_once dirname(__FILE__) . "/../libraries/ExcelClass.php";
        $excel = new ExcelClass();
        try {
            $array = $excel->excelToArray($filename, 0, 54, 0, true, true);
            echo json_encode($array);
            return $array;
        } catch (PHPExcel_Exception $e) {
            echo 'error';
            return false;
        }


    }

    /**
     * 测试数据库
     */
    public function testMysql()
    {
        $this->load->database('dqx');
        $query = $this->db->query('SELECT * FROM game_detail_data WHERE 1');
        var_dump($query->result_array());
    }

    /**
     * 获取游戏查询条件
     */
    public function getGameInfo($bool = false)
    {
        include_once "GameInfoDefination.php";
        if ($bool) {
            return GameInfoDefination::getInfo();
        } else {
            echo json_encode(GameInfoDefination::getInfo());
            return true;
        }

    }

    /**
     * @SWG\GET(path="/index.php/GameDataOverview/getGameCondition", tags={"GameDataOverview"},
     *   summary="获取游戏条件详情",
     *   description="游戏名称列表、测试节点列表、测试性质列表,测试时间为2013年至今",
     *     @SWG\Response(
     *     response="default",
     *     description="操作成功",
     *     @SWG\Schema(ref="#/definitions/GameInfoDefination"),
     *     )
     * )
     */

    public function getGameCondition($bool = false)
    {
        include_once "GameInfoDefination.php";
        $result = [
            'name' => GameInfoDefination::$nameList,
            'node' => GameInfoDefination::$testNodeList,
            'limit' => GameInfoDefination::$limitTypeList,
            'year' => range(2013, (int)date("Y"))
        ];
        $this->load->library('StandardOutPut');
        if ($bool) {
            return $result;
        } else {
            echo $res = $this->standardoutput->returnmsg(true, $result);
            return true;
        }


    }


    /**
     * @SWG\GET(
     *     path="/index.php/GameDataOverview/getGameData",
     *     tags={"GameDataOverview"},
     *     summary="筛选游戏数据",
     *     description="根据之前接口条件筛选",
     *     @SWG\Parameter(name="name", type="string", required=false, in="query",description="游戏名称"),
     *     @SWG\Parameter(name="year", type="string", required=false, in="query",description="测试时间"),
     *     @SWG\Parameter(name="node", type="string", required=false ,in="query",description="测试节点"),
     *     @SWG\Parameter(name="limit", type="string", required=false ,in="query",description="测试性质"),
     *     @SWG\Parameter(name="orderby", type="string", required=false, in="query",description="排序字段",default=""),
     *     @SWG\Parameter(name="export", type="boolean", required=false ,in="query",description="导出标志,true导出excel,false返回json数据"),
     *     @SWG\Response(
     *     response="default",
     *     description="操作成功",
     *     @SWG\Schema(ref="#/definitions/GameDatas"),
     *     )
     * )
     */

    public function getGameData()
    {
        include_once "GameInfoDefination.php";
        $name = isset($_GET['name']) ? (string)$_GET['name'] : '';
        $year = isset($_GET['year']) ? (string)$_GET['year'] : '';
        $node = isset($_GET['node']) ? (string)$_GET['node'] : '';
        $limit = isset($_GET['limit']) ? (string)$_GET['limit'] : '';
        $orderby = isset($_GET['orderby']) ? (string)$_GET['orderby'] : '';
        $export = isset($_GET['export']) ? (bool)$_GET['export'] : '';

        $GameDefinition_obj = new GameInfoDefination();
        $errormsg = [];
        $nameKey = $GameDefinition_obj->getNameKey($name, 'name', $errormsg);
        $yearKey = !empty($year) && (int)$year <= date('Y') && (int)$year >= 2013 ? (int)$year : 0;//按照原型图上,年份为2013年至今
        $nodeKey = $GameDefinition_obj->getNameKey($node, 'node', $errormsg);
        $limitKey = $GameDefinition_obj->getNameKey($limit, 'limit', $errormsg);

        //todo 修改真实字段名
        $sql = "SELECT * FROM game_detail_data WHERE 1";
        $condition = '';
        $bind = [];
        if (!empty($nameKey)) {
            $condition .= " and game_name = ?";
            array_push($bind, $nameKey);
        }
        if (!empty($yearKey)) {
            $condition .= " and date_format(start_date,'%Y') = ?";
            array_push($bind, $yearKey);
        }
        if (!empty($nodeKey)) {
            $condition .= " and test_period  = ?";
            array_push($bind, $nodeKey);
        }
        if (!empty($limitKey)) {
            $condition .= " and limit_type = ?";
            array_push($bind, $limitKey);
        }
        //排序条件
        $sql_order = " game_name ,start_date asc";
        if (!empty($orderby)) {
            $sql_order = " $orderby";
        }
        $this->load->database('dqx');
        $query = $this->db->query("SELECT 
        game_name,start_date,test_period,limit_type,new_player_num_first_week,second_keeper_num,third_keeper_num,seventh_keeper_num,daily_pay_rate_week,
        daily_arppu_week,week_arpu,income_first_month,income_first_quarter,game_quality
        FROM game_detail_data WHERE 1 " . $condition . " ORDER BY " . $sql_order, $bind);

        $data = $query->result_array();
        $cellname = ['游戏名称', '测试时间', '测试节点', '是否限量', '首周用户规模', '次留', '3留', '7留', '一周内付费率', '一周内ARPPU', '一周内ARPU', '首月流水',
            '3月流水', '游戏评级'];
        foreach ($data as $key => $value) {
            $value['game_name'] = GameInfoDefination::$nameList[((int)$value['game_name'])];
            $value['start_date'] = date("Y", strtotime($value['start_date']));
            $value['test_period'] = isset(GameInfoDefination::$testNodeList[(int)$value['test_period']]) ? GameInfoDefination::$testNodeList[(int)$value['test_period']] : "无数据";
            $value['limit_type'] = isset(GameInfoDefination::$limitTypeList[(int)$value['limit_type']]) ? GameInfoDefination::$limitTypeList[(int)$value['limit_type']] : '无数据';

            foreach ($value as $sub_key => $sub_value) {
                if (in_array($sub_key, ["second_keeper_num", 'third_keeper_num', 'seventh_keeper_num', 'daily_pay_rate_week'])) {
                    $value[$sub_key] = $this->formatNumber($sub_value, true);
                } elseif (is_numeric($sub_value)) {
                    $value[$sub_key] = $this->formatNumber($sub_value);
                }
            }
            $data[$key] = $value;
        }

        if (!$export) {//非导出
            $this->load->library('StandardOutPut');
            echo $res = $this->standardoutput->returnmsg(true, ['cell' => $cellname, 'info' => array_values($data)], implode(',', $errormsg));
        } else {//导出excel
            include_once dirname(__FILE__) . "/../libraries/ExcelClass.php";
            $excel = new ExcelClass();
            $excel->push(
                'GameDataExport' . date("Y-m-d H:i:s"),
                [
                    [
                        'cellName' => $cellname,
                        'data' => $data,
                        'sheetName' => 'sheet1'
                    ]
                ]

            );
        }
        return true;
    }

    public function getGameDataOption()
    {
        $node_list = [
            ['name' => 'name', 'refer' => 'game_name'],
            ['name' => 'year', 'refer' => "date_format(start_date,'%Y')"],
            ['name' => 'node', 'refer' => 'test_period'],
            ['name' => 'limit', 'refer' => 'limit_type']];


        $select = "DISTINCT game_name as 'name'";
        $condition = " FROM game_detail_data WHERE 1";
        $bind = [];
        $order_by = " ORDER BY game_name ,start_date asc";

        foreach ($node_list as $key => $value) {
            $param = isset($_GET[$value['name']]) && !empty($_GET[$value['name']]) ? (int)$_GET[$value['name']] : 0;
            if ($param !== 0) {
                $select = "DISTINCT " . $node_list[$key + 1]['refer'] . ' as ' . "'" . $node_list[$key + 1]['name'] . "'";
                $condition .= " AND {$value['refer']} = ? ";
                array_push($bind, $param);
            } else {
                break;
            }
        }


//        var_dump("SELECT " . $select . $condition, $bind);
        $this->load->database('dqx');
        $query = $this->db->query("SELECT " . $select . $condition . $order_by, $bind);

        $data = $query->result_array();

        include_once "GameInfoDefination.php";
        foreach ($data as $key => $value) {
            if (isset($value['name'])) {
                $value['name'] = [$value['name'], GameInfoDefination::$nameList[((int)$value['name'])]];
            }
            if (isset($value['year'])) {
                $value['year'] = [$value['year'], $value['year']];
            }
            if (isset($value['node'])) {
                $value['node'] = [$value['node']
                    , isset(GameInfoDefination::$testNodeList[(int)$value['node']]) ? GameInfoDefination::$testNodeList[(int)$value['node']] : "无数据"];
            }
            if (isset($value['limit'])) {
                $value['limit'] = [$value['limit'] ,
                isset(GameInfoDefination::$limitTypeList[(int)$value['limit']]) ? GameInfoDefination::$limitTypeList[(int)$value['limit']] : '无数据'];

            }


            $data[$key] = $value;
        }

        $this->load->library('StandardOutPut');
        echo $res = $this->standardoutput->returnmsg(true, $data);

    }
}