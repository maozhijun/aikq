@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">新建文章</h1>
    <div class="row">
        <div class="col-lg-12">
            <form class="form" method="post" action="/admin/article/save/">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ isset($article)?$article->id:'' }}">
                <input type="hidden" name="action">
                <input type="hidden" name="content">
                <input type="hidden" name="images">

                <div class="input-group form-group">
                    <span class="input-group-addon">标题</span>
                    <input type="text"
                           name="title"
                           value="{{ session('title',isset($article)?$article->title:'') }}"
                           class="form-control"
                           placeholder="标题"
                           required autofocus>
                    <span class="input-group-addon">{{isset($article) ? mb_strlen($article->title) : 0}}字</span>
                </div>
                <div class="input-group form-group">
                    <span class="input-group-addon">摘要</span>
                    <input type="text"
                           name="digest"
                           value="{{ session('digest',isset($article)?$article->digest:'') }}"
                           class="form-control"
                           placeholder="摘要"
                           required>
                    <span class="input-group-addon">{{isset($article) ? mb_strlen($article->digest) : 0}}字</span>
                </div>
                <div class="input-group form-group">
                    <span class="input-group-addon">标签</span>
                    <input type="text"
                           value="{{ session('labels',isset($article)?$article->labels:'') }}"
                           name="labels"
                           class="form-control"
                           placeholder="标签">
                </div>
                <div class="input-group form-group">
                    <span class="input-group-addon">作者</span>
                    <input type="text"
                           name="author"
                           value="{{ session('author',isset($article) ? $article->author: '') }}"
                           class="form-control"
                           placeholder="作者"
                           required>
                </div>
                <div class="input-group form-group">
                    <span class="input-group-addon">来源</span>
                    <input type="text"
                           name="resource"
                           value="{{ session('resource', isset($article) ? $article->resource: '') }}"
                           class="form-control"
                           placeholder="来源"
                           required>
                </div>
                <div class="input-group form-group col-lg-3">
                    <span class="input-group-addon">分类</span>
                    <select name="type" class="form-control" id="type">
                        <option value="">选择分类</option>
                        @foreach($types as $type)
                            <option @if(isset($article) && $article->type == $type->id) selected @endif value="{{$type->id}}">{{$type->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input @if(isset($article) && $article->original == 1) checked @endif value="1" name="original" type="checkbox" class="form-check-input">
                        原创
                    </label>
                </div>
                <br>
                <!-- 编辑器容器 -->
                <script id="ueditor_container" type="text/plain"></script>

                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="useContentImage" onchange="changeCheckbox(this)" disabled="disabled">
                        使用第一张图片为封面
                    </label>
                    <button type="button" class="btn btn-sm btn-default" onclick="uploadCover()">
                        <span class="glyphicon glyphicon-upload"></span>上传封面
                    </button>
                </div>
                <img class="img-thumbnail" id="coverImage" src="{{ isset($article) ? $article->cover:'' }}" style="display: {{!empty($article->cover) ? 'block' : 'none'}};" >
                <input type="hidden" name="cover" id="coverImageInput" value="{{ isset($article) ? $article->cover : '' }}">
                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-2">
                        <button type="button" onclick="save()" class="btn btn-primary">保存</button>
                    </div>
                    <div class="col-lg-2">
                        <button type="button" onclick="preview()" class="btn btn-info">预览</button>
                    </div>
                    <div class="col-lg-2">
                        <a type="button" target="_blank" onclick="xzhPreview();" class="btn btn-info">WAP预览</a>
                    </div>
                    <div class="col-lg-2">
                        <button id="publishBtn" {{ isset($article) && $article->status==1 ? 'disabled' : '' }} type="button" onclick="publish()" class="btn btn-success">
                            保存并发布
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <form id="imageUploadForm" enctype="multipart/form-data" action="/admin/upload/cover" method="post">
        {{ csrf_field() }}
        <input type="file" id="ImageBrowse" name="cover" onchange="changeCoverImage()" style="position:absolute;clip:rect(0 0 0 0);"/>
    </form>
@endsection

@section('js')
    @include('vendor.ueditor.assets')
    <script type="text/html" id="article_content">
    {!! isset($article)?$article->getContent():'' !!}
    </script>
    <!-- 实例化编辑器 -->
    <script type="text/javascript">
        if ($('#coverImage')[0].src == '') {
            $('#coverImage').hide();
        }
        var isContentChange = false;
        var ue = UE.getEditor('ueditor_container');
        ue.ready(function () {
            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
            ue.addListener('contentChange', function (editor) {
                //console.log($(ue.getContent()).find('img').length);
                if ($(ue.getContent()).find('img').length > 0) {
                    $('#useContentImage').prop('disabled', false);
                } else {
                    $('#useContentImage').prop('disabled', true);
                }
                isContentChange = true;
                //console.log('内容改变:');
            });
            ue.addListener("keyup", function (type, event) {
                if (event.keyCode == 13) {//按enter键
                    //新增段落 缩进2字符。
                    ue.execCommand('inserthtml', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                    console.log('enter');
                }
            });
            @if(isset($article))
                ue.setContent($('#article_content').html());
            @else
                ue.setContent("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
            @endif
        });

        function changeCheckbox(checkbox) {
            if (checkbox.checked) {
                $('#coverImageInput')[0].value = $('#coverImage')[0].src = $(ue.getContent()).find('img')[0].src;
                $('#coverImage').show();
            } else {
                $('#coverImage').hide();
            }
        }

        function uploadCover() {
            $('#ImageBrowse').click();
        }

        function changeCoverImage() {
            $('.btn').button('loading');
            var formData = new FormData($('#imageUploadForm')[0]);
            console.log(formData);
            $.ajax({
                type: 'POST',
                url: '/admin/upload/cover',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log("success");
                    $('#coverImageInput')[0].value = $('#coverImage')[0].src = data;
                    $('#coverImage').show();
                    $('.btn').button('reset');
                },
                error: function (data) {
                    console.log("error");
                    $('.btn').button('reset');
                }
            });
        }

        function formVerify(form) {//验证

            if ($.trim(form.title.value).length == 0) {
                toastr.error('必须填写标题');
                return false;
            }
            if ($.trim(form.digest.value).length == 0) {
                toastr.error('必须填写摘要');
                return false;
            }

            var type = form.type.value;
            if (type != 12) {//世界杯类型取消限制
                if ($.trim(form.title.value).length < 11 || $.trim(form.title.value).length > 31) {
                    toastr.error('标题必须不少于10字符，不能多于30字符');
                    return false;
                }
                if ($.trim(form.digest.value).length < 31 || $.trim(form.digest.value).length > 100) {
                    toastr.error('摘要必须不少于30字符，不能多于100字符');
                    return false;
                }
            }
            if ($.trim(form.author.value) == "") {
                toastr.error('请填写作者');
                return false;
            }
            if (ue.getContentTxt().length < 10) {
                toastr.error('内容太少');
                return false;
            }
            if (ue.getContentTxt().length > 10000) {
                toastr.error('内容太多');
                return false;
            }
            return true;
        }

        function save() {
            console.info('save...');
            form = $('form')[0];
            form.content.value = ue.getContent();
            form.action.value = 'save';
            imgs = [];
            $.each($(form.content.value).find('img'), function (index, img) {
                imgs.push(img.src);
            });
            imgs = imgs.join('@@@');
            form.images.value = imgs;
            if (formVerify(form)) {
                postForm(form);
            }
        }
        function preview() {
            console.info('preview...');
            form = $('form')[0];
            form.content.value = ue.getContent();
            form.action.value = 'preview';
            imgs = [];
            $.each($(form.content.value).find('img'), function (index, img) {
                imgs.push(img.src);
            });
            imgs = imgs.join('@@@');
            form.images.value = imgs;
            if (formVerify(form)) {
                postForm(form);
            }
        }
        function publish() {
            console.info('publish...');
            form = $('form')[0];
            form.content.value = ue.getContent();
            form.action.value = 'publish';
            imgs = [];
            $.each($(form.content.value).find('img'), function (index, img) {
                imgs.push(img.src);
            });
            imgs = imgs.join('@@@');
            form.images.value = imgs;
            if (formVerify(form)) {
                postForm(form);
            }
        }

        function postForm(form) {
            $('.btn').button('loading');
            var formData = new FormData(form);
            console.log(formData);
            $.ajax({
                type: 'POST',
                url: '/admin/article/save',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    console.log("success");
                    console.log(data);
                    if (data.code == 0) {
                        if (data.action == 'save') {
                            form.id.value = data.id;
                            toastr.success('保存成功！');
                        } else if (data.action == 'preview') {
                            form.id.value = data.id;
                            toastr.success('保存成功！');
                            window.open(data.url, '_blank');
                        } else if (data.action == 'publish') {
                            form.id.value = data.id;
                            $('#publishBtn').attr('disabled', "true");
                            toastr.success('发布成功！');
                            setTimeout(function () {
                                location.href = '/admin/article/list';
                            }, 1000);
                        }
                    } else {
                        toastr.error(data.error);
                    }
                    $('.btn').button('reset');
                },
                error: function (data) {
                    console.log("error");
                    toastr.error('网络异常');
                    $('.btn').button('reset');
                }
            });
        }

        function selectedAuthor(select) {
            $('#author').val(select.options[select.options.selectedIndex].text);
        }

        setInterval(function () {
            var isBaseChange = '{{!isset($article)}}';
            if (!isBaseChange) {//更新
                var title = $("input[name=title]").val();
                var digest = $("input[name=digest]").val();
                var labels = $("input[name=labels]").val();
                var author = $("input[name=author]").val();
                var resource = $("input[name=resource]").val();
                var type = $("#type").val();

                isBaseChange = (title != "{{$article->title or ''}}")
                        || (digest != "{{$article->digest or ''}}")
                        || (labels != "{{$article->labels or ''}}")
                        || (author != "{{$article->author or ''}}")
                        || (resource != "{{$article->resource or ''}}")
                        || (type != "{{$article->type or ''}}");
            }
            if (isBaseChange || isContentChange) {
                $(window).bind('beforeunload', function() {
                    return "是否离开页面";
                });
            }
        }, 1000);
        $("input[name=title]").keyup(function () {
            var count = this.value.length;
            $(this).next().html(count + '字');
        });
        $("input[name=digest]").keyup(function () {
            var count = this.value.length;
            $(this).next().html(count + '字');
        });

        function xzhPreview() {
            var id = document.forms[0].id.value;
            window.open('/admin/article/preview-xzh?id=' + id);
        }

        $("input[name=for_index]").change(function (){
            var check = $("input[name=for_index]:checked");
            var curObj = this;
            $.each(check, function (ind, obj) {
                if (obj != curObj) {
                    obj.checked = false;
                }
            });
        });
    </script>
@endsection