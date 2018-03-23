<?php
/**
 * Created by PhpStorm.
 * User: slyvanas_mhb
 * Date: 2017-4-19
 * Time: 16:18
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * @param info 参数示例
$test_data = array(
    array(
        'cellName' => array('名称', '电话', '性别'),
        'data' => array(array('周', 13006311640, '男'), array('吴', 13006311641, '女'), array('郑', 13006311642, '男')),
        'sheetName' => 'sheet1',
    ),
    array(
        'cellName' => array('省', '市'),
        'data' => array(array('江苏省', '南京'), array('山东省', '烟台'), array('上海市', '嘉定区')),
        'sheetName' => 'sheet2',
    ),
);
 */

class ExcelClass
{

    public function __construct()
    {

        /*导入phpExcel核心类 */
        require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel.php';
    }

    //根据列数字获取列英文
    public function getOrdinate($value)
    {
        // Determine column string
        if ($value < 26) {
            $index = chr(65 + $value);
        } elseif ($value < 702) {
            $index = chr(64 + ($value / 26)) .
                chr(65 + $value % 26);
        } else {
            $index = chr(64 + (($value - 26) / 676)) .
                chr(65 + ((($value - 26) % 676) / 26)) .
                chr(65 + $value % 26);
        }

        return $index;
    }

    public function getNumByOrdinate($ordinate)
    {
        $num = 0;
        $length = strlen($ordinate);
        for ($i = 0; $i < $length; $i++) {
            $num += (ord($ordinate[$i]) - 64) * pow(26, $length - $i - 1);
        }
        return $num - 1;
    }

    /* 导出excel函数*/
    public function push($name = 'Excel', array $info = array(array('cellName' => array(), 'data' => array(), 'sheetName' => 'worksheet')))
    {

        error_reporting(E_ALL);
        date_default_timezone_set('Asia/Shanghai');
        $objPHPExcel = new PHPExcel();

        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("Herbert Mei")
            ->setLastModifiedBy("Herbert Mei")
            ->setTitle("数据EXCEL导出")
            ->setSubject("数据EXCEL导出")
            ->setDescription("数据EXCEL导出")
            ->setKeywords("excel")
            ->setCategory("result file");
        /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
        foreach ($info as $infokey => $infoValue) {

            $cellName = isset($infoValue['cellName']) && !empty($infoValue['cellName']) ? $infoValue['cellName'] : array();//cell名
            $data = isset($infoValue['data']) && !empty($infoValue['data']) ? $infoValue['data'] : array();//cell值
            $title = isset($infoValue['sheetName']) && !empty($infoValue['sheetName']) ? $infoValue['sheetName'] : 'Worksheet' . $infokey;

            if ($infokey > 0) {
                $objPHPExcel->createSheet();
            }
            $objPHPExcel->setactivesheetindex($infokey);
            $objPHPExcel->getActiveSheet()->setTitle($title);
            if (!empty($cellName)) {
                foreach ($cellName as $key => $value) {//按序设置cell名
                    $valueOrdinate = self::getOrdinate((int)$key);
                    $objPHPExcel->getActiveSheet()->setCellValue($valueOrdinate . '1', $value);
                }
            }

            if (!empty($data)) {
                $data = array_values($data);

                foreach ($data as $k => $v) {//按序设置cell值
                    $v = array_values($v);
                    foreach ($v as $key => $value) {
                        $valueOrdinate = self::getOrdinate((int)$key);
                        $objPHPExcel->getActiveSheet()->setCellValue($valueOrdinate . ($k + 2), "" . $value . " ");
                    };

                }
            }

        }
        $objPHPExcel->setactivesheetindex(0);//设置默认打开第一个sheet

        ob_end_clean();//清除输出缓冲区,以免BOM头之类的影响excel生成
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /* 把excel文件转为数组 */
    /**
     * @param $excel_file_path
     * @param int $sheet
     * @param int $max_columns 最大行数
     * @param int $max_rows 最大列数
     * @param bool $head 是否设置第一行为key名
     * @param bool $formula 对于计算公式是获取计算结果(true)或是计算公式本身(false)
     * @return array|bool
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function excelToArray($excel_file_path, $sheet = 0, $max_columns = 0, $max_rows = 0, $header = false, $formula = true)
    {
        if (empty($excel_file_path) or !file_exists($excel_file_path)) {
            die('file not exists');
        }
        PHPExcel_Shared_Date::ExcelToPHP();//转化时间
        $PHPReader = new PHPExcel_Reader_Excel2007();        //建立reader对象
        if (!$PHPReader->canRead($excel_file_path)) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($excel_file_path)) {
                echo 'no Excel';
                return false;
            }
        }
        $PHPExcel = $PHPReader->load($excel_file_path);        //建立excel对象
        $currentSheet = $PHPExcel->getSheet($sheet);        //**读取excel文件中的指定工作表*/
        $allColumn = $this->getNumByOrdinate($currentSheet->getHighestColumn());//**最大列号的数字值*/
        $max_column2num = $allColumn > $max_columns && $max_columns > 0 ? $max_columns : $allColumn;//**取得最大的列号，若当前设置max_columns且符合实际情况,即小鱼最大列号,则按照配置参数获取*/
        $allRow = $currentSheet->getHighestRow() > $max_rows && $max_rows > 0 ? $max_rows : $currentSheet->getHighestRow();//**取得一共有多少行,若当前配置max_rows参数不为0,切满足实际,即小鱼实际行数,则按照当前配置最大行数计算*/


        $data = array();
        $key_array = [];
        for ($rowIndex = 1; $rowIndex <= $allRow; $rowIndex++) {        //循环读取每个单元格的内容。注意行从1开始，列从A开始
//            for ($colIndex = 'A'; $colIndex <= $allColumn; $colIndex++) {
////                var_dump($colIndex);
//                $addr = $colIndex . $rowIndex;
//                $cell = $currentSheet->getCell($addr)->getValue();
//                if ($cell instanceof PHPExcel_RichText) { //富文本转换字符串
//                    $cell = $cell->__toString();
//                }
//                $data[$rowIndex][$colIndex] = $cell;
//            }
            for ($colIndex = 0; $colIndex <= $max_column2num; $colIndex++) {


                $addr = $this->getOrdinate($colIndex) . $rowIndex;
                if ($currentSheet->getCell($addr)->isFormula() && !$formula) {//是公式且并不要求获取公式本身
                    $cell = $currentSheet->getCell($addr)->getCalculatedValue();//getCalculatedValue()获取的是公式计算结果
                } else {
                    $cell = $currentSheet->getCell($addr)->getFormattedValue();//getValue()获取的是公式本身,getFormattedValue()获取的是格式化的值
                }
//                var_dump($cell,'FormulatedValue', $currentSheet->getCell($addr)->getFormattedValue());//这个是新的公式值获取方法？

                if ($cell instanceof PHPExcel_RichText) { //富文本转换字符串
                    $cell = $cell->__toString();
                }
                if ($rowIndex != 1 && $header) {
                    $data[$rowIndex][$key_array[$colIndex]] = $cell;
                } else {
                    $data[$rowIndex][$colIndex] = $cell;
                }
            }
            //第一行数据查询结束,$header参数为True,需要观察第一行数据是否有重复,以验证是否足以作为key来源
            if ($rowIndex == 1) {
                if ($header) {
                    $check_array = $data[1];
                    if (count($check_array) != count(array_unique($check_array))) {
                        $header = False;//数组中有重复,不适合
                    }
                }
                if ($header) {
                    $key_array = $data[1];
                    array_pop($data);
                }
            }

        }
        return array_values($data);
    }
}
/*调用示例
$test_data = array(
    array(
        'cellName' => array('名称', '电话', '性别'),
        'data' => array(
            array('周', 13006311640, '男'),
            array('吴', 13006311641, '女'),
            array('郑', 13006311642, '男'),
        ),
        'sheetName' => 'sheet1',
    ),
    array(
        'cellName' => array('省', '市'),
        'data' => array(
            array('江苏省', '南京'),
            array('山东省', '烟台'),
            array('上海市', '嘉定'),
        ),
        'sheetName' => 'sheet2',
    ),
);
$test = new ExcelClass();
$test->push('test' . time(), $test_data);
*/