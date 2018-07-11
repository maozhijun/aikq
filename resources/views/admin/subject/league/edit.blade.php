@extends('admin.layout.nav')
@section('css')
<style>
    .combo-select-div{
        line-height: 30px;
        border-bottom: 1px solid #ccc;
        height:30px;
        width:100%;
        cursor:pointer;
        text-align: center;
        border-buttom:solid 1px gray;
    }
    .combo-select-div:hover{
        background-color: #ccc;
    }
    .td-combo-div
    {
        display:none;
        width:134px;
        height:200px;
        background-color:white;
        overflow:auto;
        overflow-y:scroll;
        position:absolute;
        z-index:1000;
        box-shadow:0 6px 12px rgba(0,0,0,.175);
        width: 200px
    }
    .btn_comb {
        margin:1px;
        float:left;
        height: 33px;
        vertical-align: bottom;
    }
    #coverImage {
        max-width: 200px;
        max-height: 200px;
    }
</style>
@endsection
@section('content')
    <h1 class="page-header">修改专题赛事</h1>
    <div class="row">
        <div class="col-lg-12">
            <form class="form" method="post" action="/admin/subject/leagues/save" onsubmit="return saveSL(this);">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{ isset($sl) ? $sl->id : '' }}">
                <input type="hidden" name="lid" value="{{ isset($sl) ? $sl->lid : '' }}">
                <input type="hidden" name="type" value="{{ isset($sl) ? $sl->type : '' }}">
                <input type="hidden" name="icon" id="coverImageInput"  value="{{ isset($sl) ? $sl->icon : '' }}">

                <div class="input-group form-group">
                    <span class="input-group-addon">类型</span>
                    <select name="sport" class="form-control" required>
                        <option value="1" @if(isset($sl) && $sl->sport == 1) selected @endif>足球</option>
                        <option value="2" @if(isset($sl) && $sl->sport == 2) selected @endif>篮球</option>
                    </select>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">赛事</span>
                    <input type="text"
                           value="{{ $sl->name or '' }}"
                           name="name"
                           style="display: inline;vertical-align: bottom;width: 200px;height: 35px;"
                           class="form-control"
                           placeholder="请输入赛事名称">
                    <button onclick="findLeague();" type="button" class="btn btn-default btn_comb">搜索</button>
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">排序</span>
                    <input type="number" name="od" class="form-control" value="{{$sl->od or ''}}">
                </div>

                <div class="input-group form-group">
                    <span class="input-group-addon">简介</span>
                    <textarea rows="10" name="content" class="form-control">{{$sl->content or ''}}</textarea>
                </div>

                <img @if(!isset($sl) || empty($sl->icon)) style="display: none;" @endif
                        class="img-thumbnail" id="coverImage" src="{{ $sl->icon or '' }}">

                <div class="input-group form-group">
                    <span class="input-group-addon">上传图标</span>
                    <button type="button" class="btn btn-sm btn-default" onclick="uploadCover()">
                        <span class="glyphicon glyphicon-upload"></span>上传图标
                    </button>
                </div>

                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-2">
                        <button type="submit" id="save" class="btn btn-success">保存</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <form id="imageUploadForm" enctype="multipart/form-data" action="/admin/upload/cover" method="post">
        {{ csrf_field() }}
        <input type="file" id="ImageBrowse" name="cover" onchange="changeCoverImage()" style="position:absolute;clip:rect(0 0 0 0);"/>
    </form>
    <div  class="td-combo-div" id="modalLabelCombo"></div>
@endsection
@section('js')
    <script type="text/javascript">
        function submit() {
            console.info('submit...');
        }
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        });

        /**
         * 保存外链
         * @param formObj
         * @returns {boolean}
         */
        function saveSL(thisForm) {
            //判断参数
            // var sport = thisForm.sport.value;
            var name = thisForm.name.value;
            var od = thisForm.od.value;
            var content = thisForm.content.value;
            var lid = thisForm.lid.value;

            name = $.trim(name);
            content = $.trim(content);
            if (name.length == 0) {
                alert("请填写赛事");
                return false;
            }
            if (lid == "") {
                alert("请选择赛事");
                return false;
            }
            if (od != "" && !/\d+/.test(od)) {
                alert("排序只能填写正整数");
                return false;
            }

            if (content.length == 0) {
                alert("简介不能为空");
                return false;
            }
            if (content.length > 500) {
                alert("简介不能大于500字");
                return false;
            }
            //判断参数

            var $form = $(thisForm);
            var data = $form.serialize();
            var btn = $("#save")[0];
            btn.setAttribute('disabled', 'disabled');
            $.ajax({
                "url": "/admin/subject/leagues/save",
                "data": data,
                "type": "post",
                "dataType": "json",
                "success": function (json) {
                    if (json) {
                        alert(json.msg);
                        if (json.code == 200) {
                            location.href = "/admin/subject/leagues";
                        }
                    }
                    btn.removeAttribute('disabled');
                },
                "error": function () {
                    alert("保存失败");
                    btn.removeAttribute('disabled');
                }
            });
            return false;
        }

        /**
         * 上传图片
         */
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
                    //console.log("success");
                    $('#coverImageInput')[0].value = $('#coverImage')[0].src = data;
                    $('#coverImage').show();
                    $('.btn').button('reset');
                },
                error: function (data) {
                    //console.log("error");
                    $('.btn').button('reset');
                }
            });
        }

        /**
         * 选择文件
         */
        function uploadCover() {
            $('#ImageBrowse').click();
        }

        /**
         * 统计标题字数
         */
        $("input[name='title']").keyup(function () {
            var len = this.value.length;
            var $next = $(this).next();
            if (len > 30) {
                $next.css({"background-color": "red"});
            } else {
                $next.css({"background-color": "#eee"});
            }
            $next.html(len + '字');
        });

    </script>

    <script>
        var onceSearch = false;
        $("input[name='name']").click(function () {
            if (onceSearch) showCombo(this);
        });

        function selectCombo(thisObj) {
            var lid = thisObj.getAttribute('value');
            var name = thisObj.innerHTML;
            var type = thisObj.getAttribute('type');

            $("input[name='lid']").val(lid);
            $("input[name='name']").val(name);
            $("input[name='type']").val(type);
        }

        $("body").click(function () {
            $('#modalLabelCombo').hide();
        });

        /**
         * 修改类型时，清空lid
         */
        $("select[name=sport]").change(function () {
            $("input[name=lid]").val("");
        });

        /**
         * 显示下拉框
         * @param thisObj
         */
        function showCombo(thisObj) {
            $('#modalLabelCombo').show();
            if(thisObj && thisObj.stopPropagation){
                //W3C取消冒泡事件
                thisObj.stopPropagation();
            }else{
                //IE取消冒泡事件
                window.event.cancelBubble = true;
            }
        }

        /**
         * 查找赛事
         */
        function findLeague() {
            var nameObj = $("input[name=name]");
            var btObj = nameObj[0];
            var sport = $("select[name=sport]").val();
            var name = nameObj.val();
            if (name != "") {
                $.ajax({
                    "url": "/admin/subject/leagues/find-league",
                    "type": "post",
                    "dataType": "json",
                    "data": {"name": name, "sport": sport},
                    "success": function (json) {
                        if (json && json.leagues) {
                            var comboObj = $('#modalLabelCombo');
                            var index = btObj.offsetTop;
                            var left = btObj.offsetLeft;
                            comboObj.css({"left":left + 40,"top":index + 175,"display":"block"});
                            var str = "";
                            var leagues = json.leagues;
                            for(var i = 0; i < leagues.length; i++) {
                                var league = leagues[i];
                                str += '<div value="'+ league.id +'" type="' + league.type + '" onclick="selectCombo(this);" class="combo-select-div">'+ league.name +'</div>';
                            }
                            comboObj.html(str);
                            onceSearch = true;
                        }
                    },
                    "error": function () {
                        alert("查找赛事失败");
                    }
                });
            }
        }

    </script>
@endsection