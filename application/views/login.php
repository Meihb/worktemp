<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js">></script>
    <script type="text/javascript" src="/application/views/jquery/jQuery.md5.js"></script>
</head>
<script>
    $(document).ready(function () {
        // var errormsg;
        // errormsg = $("#error").attr('value');
        // $("#error").attr('value', "sdad ");
        // if (errormsg != '') {
        //     alert(errormsg)
        // }
    });

    function postLogin() {
        var url = "..?/Log/logIn";
        alert('hehe');
        $.ajax({
            type: 'POST',
            url: url,
            data: {'name': $("#u").val(), 'pwd': $.md5($("#p").val())},
            contentType: 'application/json;charset=utf-8',
            success: function (result) {
                if (!result.IsSuccess) {
                    alert(result.Errormsg);
                } else {//初始化select

                }
            }
        })
    }

    function test() {
        if ($("#u").val().length == 0) {
            $("#u").focus();
            return false;
        }
        if ($("#p").val().length == 0) {
            $("#p").focus();
            return false;
        }
        $.ajax({
            url: "/index.php?/Log/logIn",
            type: "POST",
            data: {'name': $("#u").val(), 'pwd': $.md5($("#p").val())},
            dataType: "json",
            success: function (result) {
                if (!result.IsSuccess) {
                    alert(result.Errormsg)
                } else {
                    $(window).attr('location','../index.php?/GameDataOverview/sysindex');
                }
            }
        });

        // return false
    }


</script>
<style>
    html {
        width: 100%;
        height: 100%;
        overflow: hidden;
        font-style: sans-serif;
    }

    body {
        width: 100%;
        height: 100%;
        font-family: 'Open Sans', sans-serif;
        margin: 0;
        background-color: #4A374A;
        /*background-color: #4A374A;*/
    }

    #login {
        position: absolute;
        top: 50%;
        left: 50%;
        margin: -150px 0 0 -150px;
        width: 300px;
        height: 300px;
    }

    #login h1 {
        color: #fff;
        text-shadow: 0 0 10px;
        letter-spacing: 1px;
        text-align: center;
    }

    h1 {
        font-size: 2em;
        margin: 0.67em 0;
    }

    input {
        width: 278px;
        height: 18px;
        margin-bottom: 10px;
        outline: none;
        padding: 10px;
        font-size: 13px;
        color: #fff;
        text-shadow: 1px 1px 1px;
        border-top: 1px solid #312E3D;
        border-left: 1px solid #312E3D;
        border-right: 1px solid #312E3D;
        border-bottom: 1px solid #56536A;
        border-radius: 4px;
        background-color: #2D2D3F;
    }

    .but {
        width: 300px;
        min-height: 20px;
        display: block;
        background-color: #4a77d4;
        border: 1px solid #3762bc;
        color: #fff;
        padding: 9px 14px;
        font-size: 15px;
        line-height: normal;
        border-radius: 5px;
        margin: 0;
    }
</style>
<body>
<div id="login">
    <h1>Login</h1>
    <!--    <form id='loginform' method="post">-->
    <input id='u' type="text" required="required" placeholder="用户名" name="name"/>
    <input id='p' type="password" required="required" placeholder="密码" name="pwd"/>
    <button class="but" onclick="test()">登录</button>
    <!--        <button class="but" type="submit">登录</button>-->
    <!--    </form>-->
</div>
</body>
</html>