@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">文章列表</h1>
    <div class="row">
        <div>
            <form class="form-inline">
                <div class="form-group" style="margin-left: 10px;margin-right: 10px;">
                    <label>类型：</label>
                    <select name="sport" style="height: 26px;">
                        <option value="">全部</option>
                        <option @if(1 == request('sport')) selected @endif value="1">足球</option>
                        <option @if(2 == request('sport')) selected @endif value="2">篮球</option>
                    </select>
                </div>
                <div class="form-group" style="margin-left: 10px;margin-right: 10px;">
                    <label>来源：</label>
                    <select name="from" style="height: 26px;">
                        <option value="">全部</option>
                        <option @if('skysport' == request('from')) selected @endif value="skysport">skysport</option>
                        <option @if('espn' == request('from')) selected @endif value="espn">espn</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <span class="glyphicon glyphicon-search"></span>搜索
                </button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>标题(英)</th>
                    <th>标题(中)</th>
                    <th width="60px;">字数</th>
                    <th>url</th>
                    <th width="88px;">创建时间</th>
                    <th width="80px;">详情</th>
                    <th width="170px;">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($articles as $article)
                    <tr>
                        <td>{{$article->id}}
                            {{--原创：<input @if($article->original == 1) checked @endif type="checkbox" onchange="setOriginal(this, '{{$article->id}}');">--}}
                        </td>
                        <td>{{$article->title_en}}</td>
                        <td>{{$article->title_ch}}</td>
                        <td>{{$article->content_len}}</td>
                        <td>{{$article->url}}</td>
                        <td>{{ $article->created_at }}</td>
                        <td>
                            <a class="btn btn-xs btn-info" href="/admin/foreign/detail?fid={{ $article->id }}"
                               target="_blank">
                                <span class="glyphicon glyphicon-edit"></span>详情
                            </a>
                            @if(!empty($article->aid))
                                <div style="margin-top: 2px">
                                    @if($article->status == 1)<label class="label label-success">已发布</label>@endif
                                    @if($article->status == 0)<label class="label label-warning">已保存</label>@endif
                                </div>
                                <div style="margin-top: 5px">
                                    <a class="btn btn-xs btn-default" href="/admin/article/new?id={{$article->aid}}"
                                       target="_blank">
                                        <span class="glyphicon glyphicon-edit"></span>查看
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td>
                            <p>
                                <a class="btn btn-xs btn-danger" href="javascript:delArticle('{{ $article->id }}');">
                                    <span class="glyphicon glyphicon-remove"></span>删除
                                </a>
                            </p>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $articles->links() }}
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript">
        function delArticle(id) {
            if (!confirm("是否确认删除此文章？")) {
                return false;
            }
            var data = {};
            data["id"] = id;
            data["status"] = -1;
            $.ajax({
                "url": "/admin/foreign/update",
                "type": "post",
                "data": data,
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 0) {
                            location.reload();
                        }
                    } else {
                        alert("保存失败");
                    }
                }
            });
        }
    </script>
@endsection