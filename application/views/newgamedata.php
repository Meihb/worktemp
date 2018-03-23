<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <title>游戏数据概览</title>
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js">></script>
    <script type="text/javascript">
        var node_list = ['name', 'year', 'node', 'limit']
        $(document).ready(function () {

            $.each(node_list, function (key, value) {
                // $('#' + value).attr('keyid', 0);
                var param = key + 1;
                $('#' + value).attr('onChange', 'getOptionList(' + param + ')');

            });

            $("#name").attr('keyid', 0);
            $("#year").attr('keyid', 0);
            $("#node").attr('keyid', 0);
            $("#limit").attr('keyid', 0);

            getOptionList(0);//初始化第一个select
            $("#act").click(function () {
                var name = encodeURIComponent($("#name").val());
                var year = encodeURIComponent($("#year").val());
                var node = encodeURIComponent($("#node").val());
                var limit = encodeURIComponent($("#limit").val());
                $.ajax({
                    type: 'GET',
                    url: "../index.php?/GameDataOverview/getGameData?name=" + name + "&year=" + year + "&node=" + node + "&limit=" + limit,
                    dataType: "json",
                    success: function (result) {
                        var ajax_result = result;
                        if (!ajax_result.IsSuccess) {
                            alert(ajax_result.Errormsg)
                        } else {//生成table
                            $("#head_table_tr").html("");
                            $("#data_table_tr").html("");
                            $(".table-body").attr('style', 'display:block');
                            var cellNames = ajax_result.Data.cell;
                            var cellDatas = ajax_result.Data.info;
                            $("#head_table_tr").append(generateTableHead(cellNames));//生成head table
                            $.each(cellDatas, function (index, value) {
                                $("#data_table_tr").append(generateTableTd(value));//生成data table
                            })

                        }
                    }
                });
            });
            $("#export").click(function () {
                var name = encodeURIComponent($("#name").val());
                var year = encodeURIComponent($("#time").val());
                var node = encodeURIComponent($("#node").val());
                var limit = encodeURIComponent($("#limit").val());
                window.location.href = "../index.php?/GameDataOverview/getGameData?export=true&name=" + name + "&year=" + year + "&node=" + node + "&limit=" + limit;
            })

        });

        //根据select名称获取次轮数据
        function getOptionList(node_id) {
            var url = "../index.php?/GameDataOverview/getGameDataOption?opt=select";
            var ajax_need = true;

            console.log('node_id is' + node_id);
            if (node_id < node_list.length) {//需要更新数据
                var start_index = 0;
                var keyname = node_list[node_id];
                while (start_index < node_id) {//获取截止到当前nodelist之前的的实际参数
                    console.log($('#' + node_list[start_index]).find('option:selected').attr('keyid'));
                    var select_option_leyid = $('#' + node_list[start_index]).find('option:selected').attr('keyid');
                    if (select_option_leyid > 0) {
                        url += '&' + node_list[start_index] + '=' + select_option_leyid;
                        start_index += 1;
                    } else {
                        ajax_need = false;
                        break;
                    }


                }

                while (start_index + 1 < node_list.length) {//清空之后nodelist的当前赋值
                    $("." + node_list[start_index + 1] + '_dynamic').remove();
                    start_index += 1;
                }
                if (ajax_need) {//处理目标栏
                    $("." + node_list[node_id] + "_dynamic").remove();
                    $.ajax({
                        type: 'GET',
                        url: url,
                        dataType: "json",
                        success: function (result) {
                            if (!result.IsSuccess) {
                                alert(result.Errormsg);
                                return false;
                            }

                            console.log(keyname);
                            var temp_list = [];
                            //前栏条件确定之后,后栏数据重置,后栏之后的数据清空
                            $.each(result.Data, function (key, value) {
                                temp_list = value[keyname];
                                $("#" + keyname).append('<option class = ' + keyname + "_dynamic" + ' value = "' + temp_list[1] + '" keyid= ' + temp_list[0] + '>' + temp_list[1] + '</option>');//后栏数据重置
                            });
                        }
                    })
                }
            }


        }


        /*生成head table*/
        function generateTableHead(rows) {
            var rowbody = '';
            $.each(rows, function (index, value) {
                rowbody += '<th >' + value + '</th>';
            });
            return rowbody;
        }

        /*生成数据table*/
        function generateTableTd(rows) {
            var rowbody = "<tr>";
            $.each(rows, function (index, value) {
                rowbody += '<td>' + value + '</td>';
            });
            rowbody += "</tr>";
            return rowbody;
        }


    </script>

</head>
<style>
    table tbody {
        display: block;
        height: 750px;
        overflow-y: scroll;
    }

    table thead, tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }

    table thead {
        width: calc(100% - 1em)
    }
</style>
<body class="showmenu" scroll="no">
<form role="form" action="" class="iframemask">
    <div id='quests' class="form-control">
        <select id='name'>
            <option keyid="0" value="">游戏名称</option>
        </select>
        <select id='year'>
            <option keyid="0" value="">测试时间</option>
        </select>
        <select id='node'>
            <option keyid="0" value="">测试节点</option>
        </select>
        <select id='limit'>
            <option keyid="0" value="">测试性质</option>
        </select>
        <input type="button" id='act' style="float:right;" value="查询">
    </div>
    <div id=check class="form-control">
        数据明细
        <input type="button" id='export' style="float:right;" value="导出">
    </div>
    <div class="table-head  ">
        <table id="head_table" border="1" style="text-align:center ">
            <colgroup>
                <col style="width:90%;"/>
                <col/>
            </colgroup>
            <thead>
            <tr id="head_table_tr">
            </tr>
            </thead>
        </table>
    </div>
    <div class="table-body " style="display: none">
        <table id='data_table' border="1" style="text-align:center ">
            <colgroup>
                <col style="width: 90%;"/>
                <col/>
            </colgroup>
            <tbody>
            <tr id='data_table_tr'></tr>
            </tbody>
        </table>
    </div>
</form>


</body>
</html>