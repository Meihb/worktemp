<!-- header begin -->
<?php
//defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="head" id='hidhead'>
    <div class="top_logo">
        游戏数据
    </div>
    <div class="top_link">
        <ul>
<!--            <li class="welcome">欢迎您,@Session["CurrentUser_report"] </li>-->
            <li class="welcome">欢迎您,<?php echo $_SESSION['CurrentUser_report'] ?> </li>

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
