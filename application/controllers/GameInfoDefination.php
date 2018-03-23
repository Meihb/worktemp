<?php
/**
 * Created by PhpStorm.
 * User: meihaibo
 * Date: 2018/1/22
 * Time: 14:43
 */

/**
 * Class GameInfoDefination
 * @SWG\definition()
 */
class GameInfoDefination
{
    /**
     *
     * @var array
     * @SWG\Property(type="array",description="游戏名称列表",
     *     @SWG\Items(type="string",description="游戏名称")
     * )
     */
    static $nameList = [
        1 => 'LOVE LIVE',
        2 => '超级地城之光',
        3 => '超级女神之光',
        4 => '城与龙',
        5 => '传奇世界手游',
        6 => '传世挂机',
        7 => '封神MM',
        8 => '钢铁骑士团',
        9 => '鬼吹灯昆仑神宫',
        10 => '混沌之理',
        11 => '扩散性百万亚瑟王',
        12 => '龙之谷',
        13 => '龙之谷大陆探险',
        14 => '龙之谷起源',
        15 => '龙之战记',
        16 => '魔界HD',
        17 => '魔王日记',
        18 => '魔物狩猎者',
        19 => '拼战三国志',
        20 => '破晓之光',
        21 => '热血传奇手机版',
        22 => '沙巴克传奇',
        23 => '神无月',
        24 => '守护者传说',
        25 => '锁链战记',
        26 => '血族',
        27 => '佣兵传奇',
        28 => '永恒幻想',
        29 => '勇者世界',
        30 => '纵横天下手游',
        31 => '暗黑血统2',
        32 => '神域召唤',
        33 => '境界之诗',
    ];
    /**
     * @var array
     * @SWG\Property(type="array",description="游戏类型列表",
     *     @SWG\Items(type="string",description="游戏类型"),
     * )
     */
    static $typeList = [
        1 => '卡牌',
        2 => 'ARPG',
        3 => 'MMPRPG',
        4 => '音乐卡牌',
        5 => 'SLG',
        6 => '挂机放置',
        7 => '休闲'
    ];
    /**
     * @var array
     * @SWG\Property(type="array",description="测试节点列表",
     *     @SWG\Items(type="string",description="测试节点名称")
     * )
     */
    static $testNodeList = [
        1 => 'CBT1',
        2 => 'CBT2',
        3 => 'CBT3',
        4 => 'CBT4',
        5 => 'CBT5',
        6 => 'CBT6',
        7 => 'OBT'
    ];
    //测试性质
    /**
     * @var array
     * @SWG\Property(type="array",description="测试性质列表",
     *     @SWG\Items(type="string",description="测试性质"))
     */
    static $limitTypeList = [
        1 => '限量,非付费',
        2 => '限量,付费',
        3 => '不限量,非付费测试',
        4 => '不限量,付费测试',
    ];
    //IP有无
    /**
     * @var array
     * @SWG\Property(type="array",description="IP选项列表",
     *     @SWG\Items(type="string",description="ip选项"))
     */
    static $IPList = [
        1 => '有',
        2 => '无'
    ];
    //发行商
    /**
     * @var array
     * @SWG\Property(type="array",description="发行商列表",
     *     @SWG\Items(type="string",description="发行商"))
     */
    static $Distributor = [
        1 => '盛大游戏',
        2 => '腾讯',
        3 => '完美',
        4 => 'Bilibili'
    ];

    /**获取游戏信息
     * @return array
     */
    public static function getInfo()
    {
        return [
            'name' => self::$nameList,
            'type' => self::$typeList,
            'testNode' => self::$testNodeList,
            'limitType' => self::$limitTypeList,
            'IpType' => self::$IPList
        ];
    }

    /**
     * 根据关键字获取游戏条件列表
     * @param $type
     * @return array|bool
     */
    public static function refer($type)
    {
        switch ($type) {
            case 'name':
                return self::$nameList;
            case 'type':
                return self::$typeList;
            case 'node':
                return self::$testNodeList;
            case 'limit':
                return self::$limitTypeList;
            case 'ip':
                return self::$IPList;
        }
        return false;
    }

    /**
     * 查询相应条件列表中是否存在相应的具体条件,
     * @param  string|int $search string for txt,int for key
     * @param string $refer
     * @param array $errormsg
     * @return int
     */
    public function getNameKey(&$search, $refer, &$errormsg)
    {
        if (empty($search)) {
            return 0;
        }

        switch ($type = is_numeric($search)) {
            case false://字符串类型
                $test_array = array_flip(self::refer($refer));
                $search = urldecode($search);
                $key = @$test_array[$search];
                break;
            case true://整型
                $test_array = self::refer($refer);
                $key = $search;
                break;
        }
        if (!isset($test_array[$search])) {
            array_push($errormsg, "find no results for $search in $refer,default as all");
            return 0;
        } else {
            return $key;
        }
    }


}