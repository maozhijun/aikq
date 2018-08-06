@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">文章列表</h1>
    <div class="row">
        <div>
            <form class="form-inline">
                <div class="form-group">
                    <label>标题：</label>
                    <input type="text" name="title" value="{{ request('title', '') }}">
                </div>
                <div class="form-group" style="margin-left: 10px;">
                    <label>作者：</label>
                    <input type="text" name="author" value="{{ request('author', '') }}">
                </div>
                <div class="form-group" style="margin-left: 10px;margin-right: 10px;">
                    <label>分类：</label>
                    <select name="type" style="height: 26px;">
                        <option value="">全部</option>
                        @foreach($t_names as $id=>$type)
                            <option @if($id == request('type')) selected @endif value="{{$id}}">{{$type}}</option>
                        @endforeach
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
                    <th>作者</th>
                    <th>标题</th>
                    <th>类型</th>
                    <th width="80px;">状态</th>
                    <th width="88px;">发布时间</th>
                    <th width="50px;">阅读</th>
                    <th width="80px;">录入</th>
                    <th width="170px;">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($articles as $article)
                    <tr>
                        <td>{{$article->id}}
                            {{--原创：<input @if($article->original == 1) checked @endif type="checkbox" onchange="setOriginal(this, '{{$article->id}}');">--}}
                        </td>
                        <td>{{$article->author}}</td>
                        <td><a @if($article->status == 1) href="{{asset($article->getUrl())}}" target="_blank" @endif>{{$article->title}}</a></td>
                        <td>
                            {{$article->type == 99 ? '公告' : (isset($t_names[$article->type]) ? $t_names[$article->type] : '')}}
                        </td>
                        <td>{{ $article->statusCN() }}</td>
                        <td>{{ $article->publish_at }}</td>
                        <td>{{ $article->read_count }}</td>
                        <td>{{isset($article->c_user) ? ($article->c_user->name) : ''}}</td>
                        <td>
                            <p>
                                <a class="btn btn-xs btn-info" href="/admin/article/new?id={{ $article->id }}"
                                   target="_blank">
                                    <span class="glyphicon glyphicon-edit"></span>编辑
                                </a>
                                <a class="btn btn-xs btn-danger" href="javascript:delArticle('{{ $article->id }}');">
                                    <span class="glyphicon glyphicon-remove"></span>删除
                                </a>
                            @if($article->status == 1)
                                <a class="btn btn-xs btn-warning" href="javascript:hideArticle('{{ $article->id }}');">
                                    <span class="glyphicon glyphicon-remove-sign"></span>隐藏
                                </a>
                            @elseif($article->status == 2)
                                <a class="btn btn-xs btn-success" href="javascript:showArticle('{{ $article->id }}');">
                                    <span class="glyphicon glyphicon-ok-sign"></span>显示
                                </a>
                            @elseif($article->status == 0)
                                <a class="btn btn-xs btn-success" href="javascript:publishArticle('{{ $article->id }}');">
                                    <span class="glyphicon glyphicon-share-alt"></span>发布
                                </a>
                            @endif
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
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        function delArticle(id) {
            if (!confirm("是否确认删除此文章？")) {
                return false;
            }
            location.href = "/admin/article/delete?id=" + id;
        }

        function hideArticle(id) {
            if (!confirm("是否确认隐藏此文章？")) {
                return false;
            }
            location.href = '/admin/article/hide?id=' + id;
        }

        function showArticle(id) {
            if (!confirm("是否确认显示此文章？")) {
                return false;
            }
            location.href = '/admin/article/show?id=' + id;
        }

        function publishArticle(id) {
            if (!confirm("是否确认发布此文章？")) {
                return false;
            }
            location.href = '/admin/article/publish?id=' + id;
        }

        function setOriginal(thisObj, article_id) {
            var checked = thisObj.checked;
            var original = checked ? 1 : 0;
            var conFirmMsg = checked ? "设置原创" : "取消原创";
            if (!confirm("是否确认" + conFirmMsg + "？")) {
                thisObj.checked = !checked;
                return false;
            }

            $.ajax({
                "url": "/admin/article/set-original",
                "type": "post",
                "data": {"id": article_id, "original": original},
                "dataType": "json",
                "success": function (json) {
                    if (json && json.code == 200) {
                        alert(conFirmMsg + "成功");
                        location.reload();
                    } else if(json) {
                        alert(json.msg);
                        thisObj.checked = false;
                    } else {
                        alert(conFirmMsg + "失败");
                        thisObj.checked = false;
                    }
                }
            });
        }
    </script>
@endsection