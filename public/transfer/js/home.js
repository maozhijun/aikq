function setPage () {
    //初始化页面
    var name = GetQueryString('name');
    var club = GetQueryString('club');
    var money = GetQueryString('money');
    var con = GetQueryString('content');
    if (name && club && money && con) {
        formShare(name,club,money,con);
        $('#Wanna').css('display','');
        $('.welcome, #Result .rank').removeClass('opacity');
        $('#Result').removeClass('transform');
        setTimeout(function () {
            $('#Wanna').removeClass('opacity');
        },800)
    }else{ //无数据
        $('#Again').css('display','');
        Reset()
    }

    //设置界面按钮触发函数
    $('#Apply').click(function () {
        if ($('input').val() != '') {
            if ($('input').val().length > 20) {
                Alert('error','球员名字太长啦')
            }else{
                Transfer()
            }
        }else{
            Alert('error','请先填写转会球员名字')
        }
    })

    $('#Again, #Wanna').click(function () {
        Reset(1000);
    })

    $('.money').click(function () {
        $('#Money, #Money .inner').removeClass('hidden').removeClass('hid');
    })
    $('#Money button').click(function () {
        $('#Money .inner').addClass('hid');
        setTimeout(function () {
            $('#Money').addClass('hidden');
        },450);
    });

    //初始化数据
    setPRange();
}

var n = 0, stop = false;
window.raf = window.requestAnimationFrame       
          || window.webkitRequestAnimationFrame 
          || window.mozRequestAnimationFrame    
          || function( callback ){ window.setTimeout(callback, 1000 / 60);};

function Transfer () {
    //loading
    function energy(){
        n+=2;
        if(n>100) n=100;
        $('#Loading .line p').css("width",n+"%");
        if(n===100){
            stop=true;
            setTimeout(function(){
                showResult();
            },200)
        }
        if(!stop){
            raf(energy);
        }
    }
    $('#Loading').removeClass('hidden')
    setTimeout(function () {
        energy();
    },500)

    //数据处理
    var Name = $('input').val();
    $('#Result p.name').html(Name);

    var Info = getMes(getRange());
    $('#Result p.club').html(Info[0]);
    $('#Result p.cost').html(Info[1]);

    var Con = getRrophecy();
    $('#Result .content p').html(prophecyArr[Con]);

    var money = Info[1];
    money = money.replace("欧元", "");
    money = money.replace("万", "");
    var data = {"name": Name, "club": Info[0], "money": money};
    $.ajax({
        "url": "/api/transfer/save",
        "type": "post",
        "dataType": "json",
        "data": data,
        "success": function (json) {
            //alert(json.code + json.mes + json.rank);
        },
        "error": function () {
            //alert("保存失败");
        }
    });

    //微信自定义分享
    var url = location.protocol + '//' + location.hostname + location.pathname + '?name=' + tounicode(Name) + '&club=' + tounicode(Info[0]) + '&money=' + tounicode(Info[1]) + '&content=' + Con;
    //customShare(Name + "以" + $('#Result p.cost').html() + "转会到" + $('#Result p.club').html() + "!", "国际足坛夏季转会风云榜", url, '', '');
    customShare(Name + "以" + Info[1] + "转会到" + Info[0] + "!", "国际足坛夏季转会风云榜", url, '', '');

    //交互效果
    function showResult () {
        n = 0;
        stop = false;
        $('#Loading').addClass('hidden');
        $('#Loading .line p').css("width",n+"%");

        if ($('#Result .content p').height() >= 190) {
            $('#Result .inner').css('margin-bottom','54px');
            $('#Result a.rank').css('margin-top','40px');
        }

        $('#Again').css('display','');
        $('#Wanna').css('display','none');

        $('.title, input, #Apply, .rank:first').addClass('opacity');

        setTimeout(function () {
            $('.welcome').removeClass('opacity');
            $('#Result').removeClass('transform');
        },1300)
        setTimeout(function () {
            $('#Again').removeClass('opacity');
        },1800)
        setTimeout(function () {
            $('#Result .rank').removeClass('opacity');
        },2200)
    }
}

function Reset (time) {
    time = time ? time : 0;
    $('.welcome, #Result .rank, #Again, #Wanna').addClass('opacity');
    $('#Result .inner').css('margin-bottom','');
    $('#Result a.rank').css('margin-top','');
    setTimeout(function () {
        $('#Result').addClass('transform');
    },500)
    setTimeout(function () {
        $('.title, input, #Apply, .rank:first').removeClass('opacity').val('')
    }, time)

    customShare('转会啦！英雄终归何处！', '国际足坛夏季转会风云榜', location.protocol + '//' + location.hostname + location.pathname, '', '');
}

//转会数据处理
var prophecyArr=[ //文案
    "郑大世表示想和你做队友。",
    "在联赛揭幕战最后一刻罚中点球，完美的处子秀！",
    "长期养伤，跟迪亚比、维尔马伦和斯图里奇并称“医疗室四君子”。",
    "充当饮水机+白毛巾+肥皂管理员，与张稀哲成为结拜基友。",
    "替补上场时进了一个世界波，从此进入首发名单。",
    "转会后因为高颜值杀入演艺圈。",
    "球技一流，人渣了点。",
    "艺高人胆大，盲眼传球术闻名球坛。",
    "处子秀最后时刻第89分钟被委以重任换上场，岂不知替对方球队打进一颗关键的精彩绝伦的乌龙球，帮助对方1：0取得胜利，从此与板凳长伴。",
    "球队保级的重任就交给你了。",
    "你的假动作非常逼真，经常佯装传球，然后制造一个界外球。 ",
    "替补上场29秒即领到红牌，与红军前队魂杰拉德齐名！（红军球迷得罪了）",
    "终场前点球绝杀的机会被你一脚踢飞！好吧，你终于证明自己和梅西一样，还是个人。 ",
    "成功弥补球队边卫的空缺，以后再也不用担心侧漏了！",
    "距离入选国家队只差一步了。",
    "你的进球数绝对能赶超张稀哲。",
    "炮上了名模，队友还不明白：为什么医疗室门前总是挂着一把伞。",
    "呃…被转手卖掉…(好尴尬= =)",
    "诱惑苏亚雷斯咬人然后被罚下。",
    "多家中超俱乐部表示对你垂涎欲滴。",
    "今年的世界足球先生竞争因为你将变得更加激烈。",
    "博格巴表示在你面前他永远是第二。",
    "弗格森感叹当年没签下你是一生最大的遗憾。",
    "因为接受不了你转会，布拉特毅然辞职了。",
    "中东的土豪俱乐部正在调动大批资金准备重购你。",
    "5000名球迷将会去机场迎接你的到来。",
    "英超10+俱乐部主教练同时在响你经纪人门德斯的手机。",
    "一名狂热的女球迷写信给俱乐部，表示要和你生猴子。",
    "FM的中国妖人因为你又得重新设计了。",
    "你的肥猪流发型已经成为球队的新风尚。",
    "你永远无法铲倒一个在散步的人。",
    "狂热的女球迷被警卫拉走的时候上前解围并满足粉丝要求，暖男形象深入人心。",
    "从未见过如此厚颜无耻之人。不要再抢对方守门员的门球了！"
];
var mesArr=[ //球队分级
    {
        p:20,
        pRange:[],
        star:5,
        salary:[40000000,100000000],
        team:[
            "皇马",
            "巴萨",
            "曼联",
            "切尔西",
            "阿森纳",
            "曼城",
            "尤文图斯",
            "拜仁"
        ]
    },
    {
        p:20,
        pRange:[],
        star:4,
        salary:[10000000,40000000],
        team:[
            "巴黎圣日耳曼",
            "里昂",
            "马德里竞技",
            "瓦伦西亚",
            "热刺",
            "利物浦",
            "AC米兰",
            "国际米兰",
            "罗马",
            "沃尔夫斯堡",
            "多特蒙德",
            "波尔图"
        ]
    },
    {
        p:10,
        pRange:[],
        star:3,
        salary:[5000000,15000000],
        team:[
            "广州恒大",
            "北京国安",
            "凯尔特人",
            "阿贾克斯",
            "PSV埃因霍温",
            "顿涅茨克矿工",
            "基辅迪纳摩",
            "加拉塔萨雷",
            "莫斯科中央陆军",
            "泽尼特",
            "巴塞尔",
            "奥林匹亚科斯",
            "圣保罗",
            "河床",
            "博卡青年"
        ]
    },
    {
        p:20,
        pRange:[],
        star:2,
        salary:[1000000,7000000],
        team:[
            "山东鲁能",
            "广州富力",
            "浦和红钻",
            "柏太阳神",
            "广岛三箭",
            "鸟栖沙岩",
            "大宫松鼠",
            "首尔FC",
            "全北现代",
            "釜山偶像",
            "大田市民",
            "吉达阿赫利（沙特）",
            "春武里（泰国）",
            "曼谷玻璃（泰国）",
            "西悉尼流浪者",
            "洛杉矶银河",
            "纽维尔老男孩（阿根廷）",
            "拉普拉塔体操击剑俱乐部（阿根廷）",
            "咸史泰斯（瑞典）",
            "英特杜古（芬兰）",
            "利尼史特朗（挪威）",
            "洛斯查兰特（丹麦）",
            "ZTE（匈牙利）",
            "女王公园巡游者"
        ]
    },
    {
        p:30,
        pRange:[],
        star:1,
        salary:[0,10000],
        team:[
            "4.25体育团（朝鲜）",
            "鸭绿江体育团（朝鲜）",
            "武装兵团（新加坡）",
            "京都不死鸟（日本）",
            "警察队（伊拉克）",
            "天水围飞马（香港）",
            "黄大仙（香港）",
            "SBV精英（荷兰）",
            "天狼星（瑞典）",
            "中日德兰（丹麦）",
            "布咸美恩斯1905（捷克）",
            "萨兰特兵工厂（阿根廷）",
            "飓风队（阿根廷）",
            "防卫者（阿根廷）",
            "阿比让含羞草（科特迪瓦）",
            "阿克拉橡树之心（加纳）",
            "强者（玻利维亚）",
            "马拉松队（洪都拉斯）",
            "空中巴士（威尔士）",
            "米尔顿凯恩斯（英甲）",
            "牛津联（英乙）",
            "切尔滕汉姆（英乙）",
            "联曼（英南北）",
            "麦德黑队（英南北）"
        ]
    }
];

function setPRange(){
    for(var i=0,l=mesArr.length;i<l;i++){
        if(i===0){
            mesArr[i].pRange=[0,mesArr[i].p];
        } else {
            mesArr[i].pRange=[mesArr[i-1].pRange[1],mesArr[i-1].pRange[1]+mesArr[i].p];
        }
    }
}
function getRange(){
    var _random=Math.floor(Math.random()*100);
    console.log("random is "+_random);
    for(var i=0,l=mesArr.length;i<l;i++){
        if(_random>=mesArr[i].pRange[0] && _random<mesArr[i].pRange[1]){
            console.log("get. range is "+mesArr[i].pRange+" star is "+mesArr[i].star);
            return i;
        }
    }
    return 0;
}
function getMes(range){
    console.log("range is "+range);
    var _mesObj=mesArr[range];
    var _teamArr=_mesObj.team;
    var team=_teamArr[Math.floor(Math.random()*_teamArr.length)];
    console.log("team is "+team);
    var salary=Math.floor(Math.random()*(_mesObj.salary[1]-_mesObj.salary[0]))+_mesObj.salary[0];
    console.log("salary is "+salary);
    if(salary>10000){
        salary=salary+"";
        salary=salary.slice(0,-4)+"万欧元";
    }else{
        salary=salary+"欧元";
    }
    return [team,salary]
}
function getRrophecy(){
    var _l=prophecyArr.length;
    var _random=Math.floor(Math.random()*_l);
    return _random;
}

//从分享进入
function formShare (name,club,money,con) {
    var Name = tohanzi(name);
    var Club = tohanzi(club);
    var Money = tohanzi(money);

    $('#Result p.name').html(Name);
    $('#Result p.club').html(Club);
    $('#Result p.cost').html(Money);

    $('#Result .content p').html(prophecyArr[con]);

    if ($('#Result .content p').height() >= 190) {
        $('#Result .inner').css('margin-bottom','54px');
        $('#Result a.rank').css('margin-top','40px');
    }
    //初始化微信自定义分享
    var url = location.protocol + '//' + location.hostname + location.pathname + '?name=' + tounicode(Name) + '&club=' + tounicode(Club) + '&money=' + tounicode(Money) + '&content=' + tounicode(con);
    customShare(Name + "以" + Money + "转会到" + Club + "!", '国际足坛夏季转会风云榜', url, '', '');
}


//转码
function tounicode(data){
   var str =''; 
   for(var i=0;i<data.length;i++){
      str+=","+parseInt(data[i].charCodeAt(0),10).toString(16);
   }
   return str;
}

function tohanzi(data){
    data = decodeURIComponent(data);
    data = data.split(",");
    var str ='';
    for(var i=0;i<data.length;i++){
        str+=String.fromCharCode(parseInt(data[i],16).toString(10));
    }
    return str;
}
















