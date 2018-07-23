@extends('admin.layout.nav')
@section('content')
    <h1 class="page-header">预约列表</h1>
    <div class="row placeholders">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="10%">主播</th>
                    <th width="30%">比赛</th>
                    <th width="10%">热门</th>
                    <th width="15%">操作</th>
                </tr>
                </thead>
                <tbody style="text-align: left;">
                @foreach($page as $tag)
                    @if(isset($tag->room) && isset($tag->room->anchor))
                    <form action="/admin/anchor/tag/update" enctype="multipart/form-data" method="post" onsubmit="return checkFormData(this);">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $tag->id }}">
                        <tr>
                            <td><h5>{{ $tag->id }}</h5></td>
                            <td>
                                {{$tag->room->anchor->name}}
                            </td>
                            <td>
                                <?php
                                $match = $tag->match;
                                ?>
                                    {{$match['league']['name']}} {{$match['hname']}} vs {{$match['aname']}} {{$match['time']}}
                            </td>
                            <td>
                                <select name="hot" class="form-control" required>
                                    <option value="1" {{ $tag->hot == \App\Models\Anchor\AnchorRoomTag::kIsHot ? 'selected' : '' }}>热门</option>
                                    <option value="0" {{ $tag->hot == \App\Models\Anchor\AnchorRoomTag::kNotHot ? 'selected' : '' }}>一般</option>
                                </select>
                            </td>
                            <td>
                                <p>
                                    <button type="submit" class="btn btn-sm btn-info"><span class="glyphicon glyphicon-ok"></span>保存</button>
                                </p>
                            </td>
                        </tr>
                    </form>
                    @endif
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

        /**
         * 保存
         * @param formObj
         * @returns {boolean}
         */
        function checkFormData(formObj) {
            return true;
        }
    </script>
@endsection