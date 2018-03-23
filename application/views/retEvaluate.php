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
        var tableConfig = [];
        var percentage = ['1RET', '7RET', '1LR', '2LR'];
        $(document).ready(function () {
            $.ajax({//获取条件配置
                type: 'GET',
                url: "../index.php?/RetentionEvaluationModel/getGameCondition",
                dataType: "json",
                success: function (result) {
                    if (!result.IsSuccess) {
                        alert(result.Errormsg);
                        return false;
                    } else {
                        tableConfig = result.Data;
                        initiateSelectTable();//初始化key_select
                        onKeyChanged(true);//初始化table

                    }
                }
            });

            $("#act").click(function () {
                var name = encodeURIComponent($("#name").val());
                var year = encodeURIComponent($("#time").val());
                var node = encodeURIComponent($("#node").val());
                var limit = encodeURIComponent($("#limit").val());
                $.ajax({
                    type: 'GET',
                    url: "../index.php?/GameDataOverview/getGameData?name=" + name + "&year=" + year + "&node" + node + "&limit=" + limit,
                    dataType: "json",
                    success: function (result) {
                        var ajax_result = result;
                        if (!ajax_result.IsSuccess) {
                            alert(ajax_result.Errormsg)
                        } else {//生成table
                            $("#key_select").html("");
                            var cellNames = ajax_result.Data.cell;
                            var cellDatas = ajax_result.Data.info;
                            $("#data_table").append(generateTr(cellNames, true));
                            $.each(cellDatas, function (index, value) {
                                $("#data_table").append(generateTr(value, false));
                            })

                        }
                    }
                });
            });

            function initiateSelectTable() {
                $("#key_select").html("");
                $.each(tableConfig, function (index, value) {
                    $("#key_select").append("<option value=" + index + ">" + value.name + "</option>");
                })
            }


        });

        function onClickButton() {//访问请求
            var url = "../index.php?/RetentionEvaluationModel/evaluate";
            var param = 0;
            url += "?";
            url += "scale=" + $("#scale").html() + "&key=" + $("#key_select").val();
            $.each($(".dynamic_table_result"), function () {
                param = $(this).html();
                if (containValue($(this).attr('value'), percentage) > -1) {
                    param = parseFloat(param / 100);
                }
                url += "&" + $(this).attr('value') + "=" + param;
            });
            console.log(url);

            $.ajax({//获取条件配置
                type: 'GET',
                url: url,
                dataType: "json",
                success: function (result) {

                    if (!result.IsSuccess) {
                        alert(result.Errormsg);
                        return false;
                    } else {
                        $("#level").attr('style', 'display:block');
                        $("#level").html(result.Data);
                    }
                }
            });
        }

        function containValue(key, list) {//list是否存在元素
            var result = -1;
            $.each(list, function (index, value) {
                if (value == key) {
                    result = index;
                    return false;
                }
            });
            return result;
        }

        function onKeyChanged(default_way=false) {//选取框发生变化时

            var key = 0;
            var conditions = [];
            var optionals = [];
            if (default_way) {
                key = 1;
            } else {
                key = $("#key_select").val()
            }
            $.each(tableConfig, function (index, value) {
                if (key == index) {
                    conditions = value.condition;
                    optionals = value.optional;
                    return false;
                }
            });
            $("#scale").html("");
            $(".dynamic_table").remove();
            $(".dynamic_table_result").remove();
            $("#level").attr('style', "display:none");
            $("#level").html("");

            for (var index in conditions) {
                var apendix = "";
                if (containValue(index, percentage) >= 0) {
                    apendix += "(%)";
                }
                apendix += (containValue(index, optionals) >= 0) ? '' : '<span style="color:red">*</span>';
                $("#head_tr").append("<th class=dynamic_table" + " value=" + index + ">" + conditions[index] + apendix + "</th>")
                $("#result_tr").append("<td contentEditable=" + "true class=dynamic_table_result" + " value=" + index + ">" + "</td>")
            }

        }
    </script>

</head>
<style>

</style>
<body class="showmenu" scroll="no">
<!--<iframe class="iframemask" ></iframe>-->
<form role="form" action="" class="iframemask">
    <div id=result class="form-control table table-bordered">
        <table id='data_table'>
            <tr id='head_tr'>
                <th id="key_h" class="fixed">限量类型</th>
                <th id="scale_h" class="fixed">用户规模<span style="color:red">*</span></th>

            </tr>
            <tr id="result_tr">

                <td id="key" class="fixed"><select id="key_select" onchange="onKeyChanged()"></select></td>
                <td contentEditable="true" id="scale" class="fixed"></td>
            </tr>

        </table>

        <button type="button" class="btn btn-primary " onclick="onClickButton()">评估</button>
        <table>
            <tr>
                <td id='level' style="display:none"></td>
            </tr>
        </table>
    </div>
</form>


</body>
</html>