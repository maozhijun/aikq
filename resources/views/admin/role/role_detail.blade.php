@extends('admin.layout.nav')
@section('css')
    <link rel="stylesheet" href="/css/admin/tree/zTreeStyle/zTreeStyle.css" type="text/css">
@endsection
@section('content')
    <h1 class="page-header">{{isset($role) ? '修改' : '新建'}}角色</h1>
    <form method="post" action="/admin/roles/save" onsubmit="return initData();">
        <input type="hidden" name="id" value="{{$role->id or ''}}">
        <input type="hidden" name="resources" value="">
        <div class="input-group form-group">
            <span class="input-group-addon">角色名称</span>
            <input name="name" value="{{ $role->name or '' }}" class="form-control" placeholder="角色名称" required>
        </div>
        <label class="input-group-addon">拥有权限</label>
        <div id="resources" style="height: 600px;border: 1px solid #ccc;word-wrap: break-word; word-break:break-all;overflow-y: auto">
            <div class="content_wrap">
                <div class="zTreeDemoBackground left">
                    <ul id="treeDemo" class="ztree"></ul>
                </div>
            </div>
        </div>
        <div style="margin-top: 10px;">
            <button class="btn btn-success" type="submit">保存</button>
        </div>
    </form>
@endsection
@section('js')
    <script type="text/javascript" src="/js/admin/tree/ztree/jquery.ztree.core.min.js"></script>
    <script type="text/javascript" src="/js/admin/tree/ztree/jquery.ztree.excheck.min.js"></script>
    <script type="text/javascript">
        var setting = {
            check: {
                enable: true
            },
            data: {
                simpleData: {
                    enable: true
                }
            }
        };

        var zNodes = {!! $zNodes or '[]' !!};

        var code;

        function setCheck() {
            var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
                    py = "p", sy = "s", pn = "p", sn = "s",
                    type = { "Y":py + sy, "N":pn + sn};
            zTree.setting.check.chkboxType = type;
            showCode('setting.check.chkboxType = { "Y" : "' + type.Y + '", "N" : "' + type.N + '" };');
        }
        function showCode(str) {
            if (!code) code = $("#code");
            code.empty();
            code.append("<li>"+str+"</li>");
        }

        $(document).ready(function(){
            $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            setCheck();
            $("#py").bind("change", setCheck);
            $("#sy").bind("change", setCheck);
            $("#pn").bind("change", setCheck);
            $("#sn").bind("change", setCheck);
        });

        function getCheckedResources() {
            var zTree = $.fn.zTree.getZTreeObj("treeDemo");//换成实际的图层的id
            //var changedNodes = zTree.getChangeCheckedNodes(); //获取改变的全部结点
            var checkedNodes = zTree.getCheckedNodes();//获取全部选中的节点
            var resources = "";
            for ( var i=0 ; i < checkedNodes.length ; i++ ){
                var treeNode = checkedNodes[i];
                if (treeNode.checked) {
                    resources += resources.length == 0 ? treeNode.id : ("," + treeNode.id);
                }
                //console.log((treeNode ? treeNode.name : "root") + "checked " +(treeNode.checked ? "true" : "false") + " " + treeNode.id);
            }
            return resources;
        }
    </script>

    <script>
        /**
         * 异步表单验证
         */
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        function initData() {
            var form = document.forms[0];
            var name = form.name.value;
            if ($.trim(name) == "") {
                alert("角色名称不能为空");
                return false;
            }
            var resources = getCheckedResources();
            form.resources.value = resources;
            var data = $(form).serialize();
            $.ajax({
                "url": form.action,
                "type": "post",
                "dataType": "json",
                "data": data,
                "success": function (json) {
                    if (json && json.code == 0) {
                        form.id.value = json.id;
                        alert("保存成功");
                        location.href = "/admin/roles/detail?id=" + json.id;
                    } else if (json) {
                        alert(json.msg);
                    } else {
                        alert("保存失败");
                    }
                },
                "error": function () {
                    alert("保存失败");
                }
            });
            return false;
        }
    </script>
@endsection