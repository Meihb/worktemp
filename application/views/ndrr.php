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
</head>
<script>
    $(document).ready(function () {
        $.ajax({
            type: 'GET',
            url: "..?/RetentionEvaluationModel/getNDRRCondition",
            dataType: "json",
            success: function (result) {
                if (!result.IsSuccess) {
                    alert(result.Errormsg);
                } else {//初始化select
                    var types = result.Data.type;
                    var levels = result.Data.level;
                    $.each(types, function (key, value) {//初始化类型 option
                        $("#game_type").append('<option value=' + value + '>' + value + '</option>')
                    });
                    $.each(levels, function (key, value) {//初始化level option
                        $("#game_level").append('<option value=' + value + '>' + value + '</option>')
                    });
                }
            }
        });

    });

    function onClickButton() {
        var url = '..?/RetentionEvaluationModel/showNDDR?';

        if (!$("#game_scale").html()) {
            $("#game_scale").html("").focus();
            return false;
        }
        if (!$("#game_bi").html()) {
            $("#game_bi").html("").focus();
            return false;
        }
        var game_type = encodeURIComponent($("#game_type").val());
        var game_level = encodeURIComponent($("#game_level").val());
        var game_scale = encodeURIComponent($("#game_scale").html());
        var game_bi = encodeURIComponent($("#game_bi").html());

        console.log(game_scale, game_bi, !isNaN(game_scale));
        if (isNaN(game_scale)) {
            $("#game_scale").html("").focus();
            return false;
        }

        if (isNaN(game_bi)) {
            $("#game_bi").html("").focus();
            return false;
        }

        $.ajax({
            type: 'GET',
            url: url + 'game_type=' + game_type + '&game_level=' + game_level + '&game_scale=' + game_scale + '&game_bi=' + game_bi,
            dataType: "json",
            success: function (result) {
                if (!result.IsSuccess) {
                    alert(result.Errormsg);
                } else {//初始化select
                    $("#nddr").attr('style', 'display:block');
                    $("#nddr").html(result.Data.ndrr);
                }
            }
        })
    }
</script>
<style>

</style>
<body>
<div id=result class="table table-bordered">
    <table id='data_table'>
        <tr id='head_tr'>
            <th id="key_h" class="fixed">游戏类型</th>
            <th class="fixed">游戏级别</th>
            <th class="fixed">用户规模</th>
            <th class="fixed">百度指数</th>

        </tr>
        <tr id="result_tr">
            <td class="fixed"><select id="game_type" "></select></td>
            <td class="fixed"><select id="game_level" "></select></td>
            <td contentEditable="true" id="game_scale" class="fixed"></td>
            <td contentEditable="true" id="game_bi" class="fixed"></td>
        </tr>

    </table>

    <button type="button" class="btn btn-primary " onclick="onClickButton()">预测</button>
    <table>
        <tr>
            <td id='nddr' style="display:none"></td>
        </tr>
    </table>
</div>
<div>
    <span style="color:red">注</span>:游戏百度指数（BI） = 游戏上线一周内指数峰值 — 未上线时指数均值。(可根据上线当日是否存在其他影响因素来适当调整）
</div>
</body>
</html>