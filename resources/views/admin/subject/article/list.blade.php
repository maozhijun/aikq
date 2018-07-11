@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">专题资讯列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <form action="/admin/subject/articles/save" enctype="multipart/form-data" method="post" onsubmit="return checkFormData(this);">
                        <th>
                            {{ csrf_field() }}
                        </th>
                        <th>
                            <input type="text" name="title" class="form-control" placeholder="标题"
                                   value="{{ session('title','') }}" required>
                        </th>
                        <th>
                            <input style="width: 160px;"  type="file" name="cover">
                        </th>
                        <th>
                            <select name="s_lid" class="form-control">
                                <option>专题联赛</option>
                                @foreach($s_leagues as $league)
                                <option value="{{$league->id}}">{{$league->getName()}}</option>
                                @endforeach
                            </select>
                        </th>
                        <th><input name="link" value="" class="form-control" placeholder="文章链接"></th>
                        <th>
                            <select style="width: 80px;" name="status" class="form-control" required>
                                <option value="2">草稿</option>
                                <option value="1">发布</option>
                            </select>
                        </th>
                        <th>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <span class="glyphicon glyphicon-plus"></span>新建
                            </button>
                        </th>
                    </form>
                </tr>
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">标题</th>
                    <th width="10%">封面</th>
                    <th>联赛</th>
                    <th>链接</th>
                    <th width="10%">状态</th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody style="text-align: left;">
                @foreach($page as $article)
                    <form action="/admin/subject/articles/save" enctype="multipart/form-data" method="post" onsubmit="return checkFormData(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $article->id }}">
                        <tr>
                            <td><h5>{{ $article->id }}</h5></td>
                            <td>
                                <input type="text" name="title" class="form-control" placeholder="标题" value="{{ $article->title }}" required>
                            </td>
                            <td>
                                @if(!empty($article->cover)) <p style="width: 180px;"><img style="max-width: 180px;max-height: 200px;" src="{{$article->cover}}"></p> @endif
                                <p style="width: 180px;"><input type="file" name="cover"></p>
                            </td>
                            <td>
                                <select name="s_lid" class="form-control">
                                    <option>选择专题联赛</option>
                                    @foreach($s_leagues as $league)
                                    <option value="{{$league->id}}" @if($league->id == $article->s_lid) selected @endif >{{$league->getName()}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input name="link" class="form-control" value="{{$article->link}}">
                            </td>
                            <td>
                                <select name="status" class="form-control" required>
                                    <option value="1" {{ $article->status == 1 ? 'selected' : '' }}>发布</option>
                                    <option value="0" {{ $article->status == 2 ? 'selected' : '' }}>隐藏</option>
                                </select>
                            </td>
                            <td>
                                <p>
                                    <button type="submit" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-ok"></span>保存</button>
                                    <a href="javascript:delArticle('{{$article->id}}');" class="btn btn-sm btn-danger">删除</a>
                                </p>
                            </td>
                        </tr>
                    </form>
                @endforeach
                </tbody>
            </table>
            {{$page or ''}}
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        function submit() {
            console.info('submit...');
        }
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });
        function delArticle(id) {
            if (!confirm('是否确认删除该文章')) {
                return;
            }
            $.ajax({
                "url": "/admin/subject/articles/del",
                "type": "post",
                "data": {"id": id},
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.reload();
                        }
                    } else {
                        alert("删除失败");
                    }
                },
                "error": function () {
                    alert("删除失败");
                }
            });
        }

        /**
         * 保存外链
         * @param formObj
         * @returns {boolean}
         */
        function checkFormData(formObj) {
            var title = formObj.title.value;
            var link = formObj.link.value;
            title = $.trim(title);
            link = $.trim(link);
            if (title == "") {
                alert("标题不能为空");
                return false;
            }
            if (title.length > 30) {
                alert("标题不能大于30字");
                return false;
            }
            if (link == "") {
                alert("文章链接不能为空");
                return false;
            }
            if (link.length > 255) {
                alert("文章链接不能大于255字符");
                return false;
            }
            return true;
        }

        function setStatusStyle(thisObj) {
            var value = thisObj.value;
            if (value == 1) {
                thisObj.style.backgroundColor = '#5cb85c';
                thisObj.style.color = '#fff';
            } else {
                thisObj.style.backgroundColor = '';
                thisObj.style.color = '';
            }
        }
        var statusObj = $("select[name=status]");
        statusObj.change(function () {
            setStatusStyle(this);
        });
        statusObj.each(function (index, obj) {
            setStatusStyle(obj);
        })
    </script>
@endsection