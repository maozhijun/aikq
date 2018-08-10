<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
    <div>
        <input id="name" value=""><br/>
        <input id="club" value=""><br/>
        <input id="money" value="" type="number"><br/>
        <button onclick="transfer();">转会</button>
    </div>
</body>
<script type="text/javascript" src="//apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
    function transfer() {
        var name = $("#name").val();
        var club = $("#club").val();
        var money = $("#money").val();
        var data = {"name": name, "club": club, "money": money};
        $.ajax({
            "url": "/api/transfer/save",
            "type": "post",
            "dataType": "json",
            "data": data,
            "success": function (json) {
                alert(json.code + json.mes + json.rank);
            },
            "error": function () {
                alert("保存失败");
            }
        });
    }
</script>
</html>