<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <title>{{isset($title) ? $title : '爱看球'}}</title>
    <meta name="Keywords" content="{{isset($keywords)?$keywords:''}}">
    <meta name="Description" content="{{isset($description)?$description:''}}">
    <meta http-equiv="X-UA-Compatible" content="edge" />
    <meta name="renderer" content="webkit|ie-stand|ie-comp">
    <meta name="baidu-site-verification" content="nEdUlBWvbw">
    <link rel="stylesheet" type="text/css" href={{env('CDN_URL') . '/css/db/style.css?201712302000'}}>
    @yield('css')
    <!--[if lte IE 8]>
    <link rel="stylesheet" type="text/css" href={{env('CDN_URL') . '/css/ie.css?201712302000'}}>
    @yield('iecss')
    <![endif]-->
    <!--[if IE 9]>
    @yield('ie9css')
    <![endif]-->
    <link rel="Shortcut Icon" data-ng-href="{{env('CDN_URL')}}/img/pc/ico.ico" href="{{env('CDN_URL')}}/img/pc/ico.ico" >
    <style type="text/css">
        tr:nth-child(even) td{background-color:#bfe4ff;}
        tr:nth-child(odd) td {background-color:#e1ffd4;}
    </style>
</head>
<body>
@foreach($matches as $time=>$match_array)
    <table class="list" style="width:900px;table-layout: fixed" border="8">
        <col width="50px" border="8"/>
        <col width="50px"/>
        <col width="50px"/>
        <col width="150px"/>
        <col width="250px"/>
    <?php
    $week = date('w', strtotime($time));
    ?>
    <tr class="date">
        <th colspan="5" style="height: 50px; border-top: 1px solid #e6e6e6">{{date_format(date_create($time),'Y年m月d日')}}</th>
    </tr>
    @foreach($match_array as $match)
        <tr>
            <td style="height: 50px; border-top: 1px solid #e6e6e6">@if($match['sport'] == 2)
                    篮球
                @else
                    足球
                @endif</td>
            <td style="border-top: 1px solid #e6e6e6">{{$match['league_name']}}</td>
            <td style="border-top: 1px solid #e6e6e6">{{date('H:i', strtotime($match['time']))}}</td>
            <td style="border-top: 1px solid #e6e6e6">{{$match['hname']}} vs {{$match['aname']}}</td>
            <td style="border-top: 1px solid #e6e6e6">
                <?php
                //足球 英超、意甲、德甲、法甲、西甲、欧冠、欧联、中超
                $flids = [8,11,26,29,31,46,73,77,139];
                $blids = [1,4];
                $preUrl = 'https:'.env('WWW_URL');
                ?>
                <input style="width: 250px" id="a_{{$match['mid']}}"
                       value="{{$preUrl.'/live/spPlayer/player-'.$match['mid'].'-'.$match['sport'].'.html'}}"
                ><a onclick="copy({{$match['mid']}})">复制</a>
            </td>
        </tr>
    @endforeach
        </table>
@endforeach
</body>
<script type="text/javascript">
function copy(id) {
    var Url2=document.getElementById('a_'+id);
    Url2.select(); // 选择对象
    document.execCommand("Copy"); // 执行浏览器复制命令
    alert(Url2.value + " 已复制好，可贴粘。");
}
</script>
</html>