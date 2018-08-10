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
    $('#Result .content p').html(prophecyArr[Con].replace('XX',Info[0]));

    //微信自定义分享
    var url = location.protocol + '//' + location.hostname + location.pathname + '?name=' + tounicode(Name) + '&club=' + tounicode(Info[0]) + '&money=' + tounicode(Info[1]) + '&content=' + Con;
    customShare(Name + "以" + $('#Result p.cost').html() + "转会到" + $('#Result p.club').html() + "!", "国际足坛夏季转会风云榜", url, '', '');

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

    customShare('英雄莫问出处，总得有个去处。转会窗口，容许我跳！个！槽！', '国际足坛夏季转会风云榜', location.protocol + '//' + location.hostname + location.pathname, '', '');
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
    "呃⋯被转手卖掉⋯(好尴尬= =)",
    "诱惑苏亚雷斯咬人然后被罚下。",
    "多家中超俱乐部表示对你垂涎欲滴。",
    "今年的世界足球先生竞争因为你将变得更加激烈。",
    "内马尔表示在你面前他永远是第二。",
    "弗格森感叹当年没签下你是一生最大的遗憾。",
    "因为接受不了你转会，穆里尼奥毅然辞职了。",
    "中东的土豪俱乐部正在调动大批资金准备重购你。",
    "5000名球迷将会去机场迎接你的到来。",
    "英超10+俱乐部主教练同时在响你经纪人门德斯的手机。",
    "一名狂热的女球迷写信给俱乐部，表示要和你生猴子。",
    "FM的中国妖人因为你又得重新设计了。",
    "你的肥猪流发型已经成为球队的新风尚。",
    "你永远无法铲倒一个在散步的人。",
    "狂热的女球迷被警卫拉走的时候上前解围并满足粉丝要求，暖男形象深入人心。",
    "从未见过如此厚颜无耻之人。不要再抢对方守门员的门球了！",
    "随着一声猪叫，球迷们欢天喜地下树了！",
    "转会照片肚腩突出？WTF！你们可以说我胖，但我的身材从未走样。我就是我，是人间不一样的烟火",
    "听说乾贵士加盟贝蒂斯后唱了《哆啦A梦》，我可以想象你会唱个什么“一起喵喵喵喵喵”~",
    "俱乐部官员给你一个球让你solo，你二话不说一脚蒙在柱子上，向世界杯时的巴舒亚伊致敬",
    "去XX是我毕生的梦想，这次我发誓不会再改了！",
    "你有姆巴佩的速度？别浪费了，跟我回去送餐吧！",
    "你有C罗的弹跳？别浪费了，跟我回去送餐吧！",
    "你有浩克的强壮？别浪费了，跟我回去搬砖吧！",
    "你有梅西的技术？别浪费了，跟我回去绣个蒂花之秀吧！",
    "你有内马尔的抖机灵？别浪费了，跟我回去培训老太太如何碰瓷不被看穿吧！",
    "这队里个个都是淫才，说话又吼听，我敲稀饭这里的！",
    "我就是饿死，也绝不会加盟这个烂队！（←真香警告）",
    "对唔住，有钱真系可以为所欲为的！",
    "但愿你家的传真机没有在关键时刻坏掉！",
    "再给你三秒钟，擦干口水就该醒醒搬砖了！",
    "你可能会做出成千上万个关键扑救，但是人们记住的却是那些洞穿你十指关的进球",
    "擅长拌蒜式带球的盘带鬼才，解围式射门流派创始人！",
    "你就是球队的刹车垫，高速反击终结者，球队需要你！",
    "翘臀神锋，造点狂魔，吃饼达人，不会射门的射手王！",
    "擅长一个人逆转战局，唤醒即将睡着的球迷之人！",
    "未来打脸球迷型球员，球迷讨论群的流量担当！",
    "上场自带10打12效果，射门直击摄影师！",
    "有着解围型前锋属性，球队的未来战士！",
    "球队中场唯一的持球人，未来足坛的中场巨星！",
    "球技不要求世界级，发型倒是可以搞个世界级！",
    "我觉得你可以出一本书《如何打好每一台飞机》！",
    "未来你将是快乐足球忠实拥护者！",
    "小伙子有前途，从不缺质疑的男人！",
    "世界波专业户，球队前场独逼双骄之一！",
    "常规赛划水，淘汰赛超神的男人！",
    "远射大神，隐藏的任意球大师！",
    "防守型前锋，适合待着饮水机旁的男人！",
    "未来足坛20年B2B领袖，极具王者之风！",
    "古代中锋与现代中锋相结合，出道即巅峰！",
    "前叉世界级，有保利尼奥的风范！",
    "脑子以下是世界级，所有门将的福音！",
    "天选之子，闭着眼都能进球！",
    "一上场，10分钟发动5次进攻的大师！",
    "未来你将是最为踢球的模特！",
    "球技和颜值兼可得，未来球队的颜值担当！",
    "一上场，自带进攻终结属性！",
    "拥有极高后卫天赋，擅长人球分过，伟大的天才！",
    "广场舞大妈男神，微博热度仅次于C罗！",
    "颜值与能力并存的人，对，你就是那个人！",
    "一个集颜值和优雅于一身的好球员、好丈夫！",
    "你的演技，和内马尔有得一拼！",
    "未来蒙牛的代言人，有着梅西的待遇！"
];
var mesArr=[ //球队分级
    {
        p:20,
        pRange:[],
        star:5,
        salary:[50000000,200000000],
        team:[
            "皇马",
            "巴萨",
            "曼联",
            "切尔西",
            "阿森纳",
            "曼城",
            "尤文图斯",
            "拜仁慕尼黑",
            "利物浦",
            "巴黎圣日耳曼"
        ]
    },
    {
        p:20,
        pRange:[],
        star:4,
        salary:[10000000,80000000],
        team:[
            "马赛",
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
            "波尔图",
            "广州恒大淘宝"
        ]
    },
    {
        p:10,
        pRange:[],
        star:3,
        salary:[5000000,15000000],
        team:[
            "天津权健",
            "北京国安",
            "上海申花",
            "山东鲁能",
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
            "江苏苏宁",
            "广州富力",
            "浦和红钻",
            "柏太阳神",
            "广岛三箭",
            "鸟栖沙岩",
            "神户胜利船",
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
            "拉普拉塔体操击剑（阿根廷）",
            "马尔默（瑞典）",
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
            "麦德黑队（英南北）",
            "天朝某某街道办"
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
        $.ajax({
            url: 'http://mp.dlfyb.com/api/transfer/save',
            type: 'POST',
            dataType: 'json',
            data: {
                money: parseInt(salary/10000),
                club: team,
                name: $('#Result p.name').html()
            },
            success: function () {
                //保存成功
            },
            error: function () {
                //保存失败
            }
        })
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
    // var url = location.protocol + '//' + location.hostname + location.pathname + '?name=' + name + '&club=' + club + '&money=' + money + '&content=' + con;
    customShare($('#Result p.name').html() + "以" + $('#Result p.cost').html() + "转会到" + $('#Result p.club').html() + "!", '国际足坛夏季转会风云榜', '', '', '');
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
    data = data.split(",");
    var str ='';
    for(var i=0;i<data.length;i++){
        str+=String.fromCharCode(parseInt(data[i],16).toString(10));
    }
    return str;
}
















