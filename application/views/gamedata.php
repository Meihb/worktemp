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
        $(document).ready(function () {
            $("#act").click(function () {
                var name = encodeURIComponent($("#name").val());
                var year = encodeURIComponent($("#time").val());
                var node = encodeURIComponent($("#node").val());
                var limit = encodeURIComponent($("#limit").val());
                $.ajax({
                    type: 'GET',
                    url: "../index.php?/GameDataOverview/getGameData?name=" + name + "&year=" + year + "&node=" + node + "&limit=" + limit,
                    dataType: "json",
                    success: function (result) {
                        var ajax_result = result;
                        if (!ajax_result.IsSuccess) {
                            alert('Error!')
                        } else {//生成table
                            $("#data_table").html("");
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
            $("#export").click(function () {
                var name = encodeURIComponent($("#name").val());
                var year = encodeURIComponent($("#time").val());
                var node = encodeURIComponent($("#node").val());
                var limit = encodeURIComponent($("#limit").val());
                window.location.href = "../index.php?/GameDataOverview/getGameData?export=true&name=" + name + "&year=" + year + "&node=" + node + "&limit=" + limit;
            })

        });

        function generateTr(rows, head=true) {
            var start_string = "";
            var end_string = "";
            if (head) {
                var start_string = "<th>";
                var end_string = "</th>";
            } else {
                var start_string = "<td>";
                var end_string = "</td>";
            }
            var rowbody = "<tr>";
            $.each(rows, function (index, value) {
                if (value === '-1') {
                    value = ' ';
                }
                rowbody += start_string + value + end_string;
            });
            rowbody += "</tr>";
            return rowbody;
        }


    </script>

</head>
<style>

</style>
<!--<body class="showmenu" scroll="no">-->
<!--<iframe class="iframemask" ></iframe>-->
<form role="form" action="" class="iframemask">
    <div id='quests' class="form-control">
        <!--        <label for="name">游戏名称:</label>-->
        <select id='name'>
            <option value="">游戏名称</option>
            <?php foreach ($init['name'] as $item): ?>
                <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
            <?php endforeach; ?>
        </select>
        <!--        <label for="time">测试时间:</label>-->
        <select id='time'>
            <option value="">测试时间</option>
            <?php foreach ($init['year'] as $item): ?>
                <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
            <?php endforeach; ?>
        </select>
        <!--        <label for="name">测试节点:</label>-->
        <select id='node'>
            <option value="">测试节点</option>
            <?php foreach ($init['node'] as $item): ?>
                <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
            <?php endforeach; ?>
        </select>
        <!--        <label for="name">测试性质:</label>-->
        <select id='limit'>
            <option value="">测试性质</option>
            <?php foreach ($init['limit'] as $item): ?>
                <option value="<?php echo $item; ?>"><?php echo $item; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="button" id='act' style="float:right;" value="查询">
    </div>
    <div id=check class="form-control">
        数据明细
        <input type="button" id='export' style="float:right;" value="导出">
    </div>
    <div id=result class="form-control table table-bordered">
        <table id='data_table'>

        </table>
    </div>

</form>


</body>
</html>