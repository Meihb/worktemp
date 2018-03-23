<script type="text/javascript" src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js">></script>
<script type="text/javascript">
    function sub() {
        $.ajax({
            cache: true,
            type: "POST",
            url: "/index.php?/Log/logIn",
            data: $('#formId').serialize(),// 你的formid
            async: false,
            error: function (request) {
                alert("Connection error:" + request.error);
            },
            success: function (data) {
                alert("SUCCESS!");
            }
        });
    }
</script>
<form id="formId" action="/index.php?/Log/logIn" method="post" onsubmit="return sub();">
    <input id="input1"/>
    <input id="input2"/>
    <input id="input3"/>
    <input type="button" value="提 交" onclick="sub()"/>
</form>