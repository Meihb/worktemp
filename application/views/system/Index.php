<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>数据与模型</title>
    <link href="/application/views/system/minh/css/main.css" rel="stylesheet" type="text/css"/>
    <script src="/application/views/system/minh/js/jquery.js" type="text/javascript"></script>
    <script src="/application/views/system/minh/js/frame1.js" type="text/javascript"></script>
</head>
<body class="showmenu" scroll="no">
<div class="pagemask">
</div>
<iframe class="iframemask"></iframe>
<!--@RenderPage("Head.cshtml")-->
<!--@RenderPage("Left.cshtml")-->
<?php
//include_once __DIR__ . "/Head.php";
//include_once __DIR__ . "/Left.php";
//?>
<!-- header start -->
<div class="head" id='hidhead'>
    <div class="top_logo">
        游戏数据
    </div>
    <div class="top_link">
        <ul>
            <!--            <li class="welcome">欢迎您,@Session["CurrentUser_report"] </li>-->
            <li class="welcome">欢迎您,<?php echo isset($_SESSION['CurrentUser_report'])?$_SERVER['CurrentUser_report']:'用户' ?> </li>

            <li class="menuact"><a href="#" id="togglemenu">[隐藏菜单]</a></li>
            <li><a href="/cq/Author/Logout" target="_top">[退出]</a></li>
        </ul>
        <!--
		<div class="quick">
			<a href="#" class="ac_qucikmenu" id="ac_qucikmenu">1</a>
			<a href="#" class="ac_qucikadd" id="ac_qucikadd">2</a>
		</div>
		-->
    </div>
    <div class="nav" id="nav">
        <ul>
            <li><a class="thisclass" href="javascript:;" _for="top_1" target="main">游戏数据概览与模型</a></li>
            <li><a href="javascript:;" _for="top_2" target="main">待补充</a></li>
        </ul>
    </div>
</div>
<!-- header end -->
<!-- Left start-->
<div class="left" id="hidleft">
    <div class="menu" id="menu">
        <div class="items_top_1">
            <dl class="dl_items_1_1">
                <dt>游戏数据概览</dt>
                <dd>
                    <ul>
                        <li><a href="/index.php?/GameDataOverview/indexv2" target="main">游戏数据概览</a></li>
                    </ul>
                </dd>
            </dl>
        </div>
        <div class="items_top_1">
            <dl class="dl_items_1_2">
                <dt>留存率评估模型</dt>
                <dd>
                    <ul>
                        <li><a href="/index.php?/RetentionEvaluationModel/index" target="main">游戏等级评估</a></li>
                        <li><a href="/index.php?/RetentionEvaluationModel/getNDRRPage" target="main">次留预测</a></li>
                    </ul>
                </dd>
            </dl>
        </div>
        <div class="items_top_1">
            <dl class="dl_items_1_3">
                <dt>流水预估模型</dt>
                <dd>
                    <ul>
                        <li><a href="/index.php?/GameDataOverview/" target="main">预估流水</a></li>
                        <li><a href="/index.php?/GameDataOverview/" target="main">预估利润率</a></li>
                        <li><a href="/index.php?/GameDataOverview/" target="main">预估最优投放市场费</a></li>
                    </ul>
                </dd>
            </dl>
        </div>
        <div class="items_top_1">
            <dl class="dl_items_1_4">
                <dt>用户规模模型</dt>
                <dd>
                    <ul>
                        <li><a href="/index.php?/GameDataOverview/" target="main">用户规模模型</a></li>
                    </ul>
                </dd>
            </dl>
        </div>
        <div class="items_top_1">
            <dl class="dl_items_1_5">
                <dt>日本游戏流水预估</dt>
                <dd>
                    <ul>
                        <li><a href="/index.php?/GameDataOverview/" target="main">日本游戏流水预估</a></li>
                    </ul>
                </dd>
            </dl>
        </div>
        <div class="items_top_1">
            <dl class="dl_items_1_6">
                <dt>外部游戏人数预估</dt>
                <dd>
                    <ul>
                        <li><a href="/index.php?/GameDataOverview/" target="main">外部游戏人数预估</a></li>
                    </ul>
                </dd>
            </dl>
        </div>
        <!--        <div class="items_top_2">-->
        <!--            <dl class="dl_items_2_1">-->
        <!--                <dt>日常管理</dt>-->
        <!--                <dd>-->
        <!--                    <ul>-->
        <!--                        <li><a href="/index.php?/GameDataOverview/" target="main">游戏数据概览</a></li>-->
        <!--                        <li><a href="/index.php?/GameDataOverview/" target="main">留存率评估模型</a></li>-->
        <!--                    </ul>-->
        <!--                </dd>-->
        <!--            </dl>-->
        <!--        </div>-->
    </div>
</div>
<!-- Left end-->
<script type="text/javascript">
    function reSetIframe() {
        var iframe = document.getElementById("iframeId");
        try {
            var bHeight = iframe.contentWindow.document.body.scrollHeight;
            var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
            var tmp_height = Math.max(bHeight, dHeight);
            console.log(tmp_height)
            iframe.height = tmp_height;
        } catch (ex) {
        }
    }

    $(function () {
        // default selected menu link
        ChangeNav("top_1", "/Admin/Sys_Role");
    });
</script>
<!-- right begin -->
<div class="right" id="hidright">
    <div class="main">
        <!--        <iframe id="main" name="main" frameborder="0" src="/cq/general/index" onload="javascript:reSetIframe();">-->
        <iframe id="main" name="main" frameborder="0" src="" onload="javascript:reSetIframe();">
        </iframe>
        <div style="clear:both">
        </div>
    </div>
</div>
<!-- right end -->
<!-- qucikmenu begin -->
<!--<div class="qucikmenu" id="qucikmenu">-->
<!--    <ul>-->
<!--        @*-->
<!--        <li><a href="javascript:;" target="main">1</a></li>-->
<!--        <li><a href="javascript:;" target="main">2</a></li>-->
<!--        <li><a href="javascript:;" target="main">3</a></li>-->
<!--        *@-->
<!--    </ul>-->
<!--</div>-->
<!-- qucikmenu end -->
</body>
</html>
