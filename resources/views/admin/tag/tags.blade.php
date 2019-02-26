@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">标签列表</h1>
    <div class="row">
        <div>
            <form class="form-inline">
                <div class="form-group" style="margin-left: 10px;margin-right: 10px;">
                    <label>标签名：</label>
                    <input name="name" class="form-control" value="{{request("name")}}">
                </div>
                <div class="form-group" style="margin-right: 10px;">
                    <label>级别：</label>
                    <select name="level" class="form-control">
                        <option value="">全部</option>
                        <option @if(1 == request('level')) selected @endif value="1">一级</option>
                        <option @if(2 == request('level')) selected @endif value="2">二级</option>
                        <option @if(3 == request('level')) selected @endif value="3">三级</option>
                        <option @if(4 == request('level')) selected @endif value="4">四级</option>
                    </select>
                </div>
                <div class="form-group" style="margin-right: 10px;">
                    <label>类型：</label>
                    <select name="sport" class="form-control">
                        <option value="">全部</option>
                        <option @if(1 == request('sport')) selected @endif value="1">足球</option>
                        <option @if(2 == request('sport')) selected @endif value="2">篮球</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <span class="glyphicon glyphicon-search"></span>搜索
                </button>
            </form>
        </div>
        <hr/>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            <input name="id" type="hidden" value="">
                        </th>
                        <th>
                            <input name="name" value="" class="form-control">
                        </th>
                        <th>
                            <select name="sport" class="form-control">
                                <option value="1">足球</option>
                                <option value="2">篮球</option>
                            </select>
                        </th>
                        <th>
                            <select name="level" class="form-control">
                                <option value="1">一级（足球、篮球）</option>
                                <option value="2">二级（赛事、联赛）</option>
                                <option value="3">三级（球队）</option>
                                <option value="4">四级（球员）</option>
                            </select>
                        </th>
                        <th>
                            <button type="button" class="btn btn-sm btn-success" onclick="saveTag(this)">新建</button>
                        </th>
                    </tr>
                    <tr>
                        <th>ID</th>
                        <th>标签名</th>
                        <th>类型</th>
                        <th>级别</th>
                        <th width="170px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($pages))
                    @foreach($pages as $tag)
                        <tr>
                            <td>{{$tag->id}}</td>
                            <td>{{$tag->name}}</td>
                            <td>{{$tag->sportCn()}}</td>
                            <td>{{$tag->levelCn()}}</td>
                            <td>
                                <p>
                                    <a class="btn btn-xs btn-danger" href="javascript:delTag(this, '{{ $tag->id }}');">
                                        <span class="glyphicon glyphicon-remove"></span>删除
                                    </a>
                                </p>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            {{ isset($pages) ? $pages->links() : "" }}
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        function saveTag(thisBtn) {
            if (!confirm("是否确认保存？")) {
                return false;
            }
            var $thisBtn = $(thisBtn);
            var $tr = $thisBtn.parent().parent();

            var id = $tr.find("input[name=id]").val();
            var name = $tr.find("input[name=name]").val();
            var sport = $tr.find("select[name=sport]").val();
            var level = $tr.find("select[name=level]").val();

            var data = {};
            data["id"] = id;
            data["name"] = name;
            data["sport"] = sport;
            data["level"] = level;
            data["_token"] = "{{ csrf_token() }}";

            $thisBtn.button("loading");
            $.ajax({
                "url": "/admin/tags/save",
                "type": "post",
                "data": data,
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.message);
                        if (json.code == 200) {
                            location.reload();
                        }
                        $thisBtn.button("reset");
                    } else {
                        $thisBtn.button("reset");
                        alert("保存失败");
                    }
                },
                "error": function () {
                    $thisBtn.button("reset");
                    alert("保存失败");
                }
            });
        }

        function delTag(thisBtn, id) {
            if (!confirm("是否确认删除标签？")) {
                return false;
            }
            var $thisBtn = $(thisBtn);
            $thisBtn.button("loading");
            $.ajax({
                "url": "/admin/tags/del",
                "data": {"id": id},
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.message);
                        if (json.code == 200) {
                            location.reload();
                        } else {
                            $thisBtn.button("reset");
                        }
                    } else {
                        $thisBtn.button("reset");
                        alert("保存失败");
                    }
                },
                "error": function () {
                    $thisBtn.button("reset");
                    alert("保存失败");
                }
            });
        }
    </script>
@endsection