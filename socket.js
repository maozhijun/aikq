var app = require('express');
var http = require('http').Server(app);
var io = require('socket.io')(http, {'transports': ['websocket', 'polling']});
var Redis = require('ioredis');
var php_redis = new Redis();
var redis = require("redis"),
    client = redis.createClient();

var crypto = require('crypto');

php_redis.subscribe('akq_chat_notification', function(err, count) {
    console.log('akq_chat_notification connect!');
});

php_redis.on('message', function(channel, notification) {
    // console.log('mid:'+notification);
    notification = JSON.parse(notification);
    // 將訊息推播給使用者
    userPostChat(notification.data,notification.data.mid);
    // io.emit('bjtest', notification.data.message);
    console.log('api send done');
});


io.on('connect', function (socket) {
    // console.log('a user connected');

    var mid = '';
    var vaildUser = 0;

    //绑定监听事件
    //接收用户发的消息
    socket.on('user_send_message', function (info) {
        //之后用post,不是直接socket过去,所以这里不用通用变量了,做个兼容
        try {
            var socket_mid = mid;
            if (info.mid && parseInt(info.mid) > 0) {
                socket_mid = parseInt(info.mid);
            }
            userPostChat(info,socket_mid);
        }
        catch (e){
            console.log(e);
        }
    });

    //接收用户说自己在哪个房间
    socket.on('user_mid', function (info) {
        try {
            mid = info.mid;
            var isPc = info.isPc;
            vaildUser = info.vaildUser;
            //验证
            var md5 = crypto.createHash('md5');

            var time = info.time;
            var verification = info.verification;
            var message = mid;
            var key = message + '?' + time.substring(time.length - 1) + '_' + time.substring(time.length - 2);
            var result = md5.update(key).digest('hex');
            var current_time = Date.parse( new Date())/1000 + '';
            if (result == verification && Math.abs(current_time - time) < 60) {
                socket.join('mid:' + mid);

                if (info.nickname && info.nickname.length > 0){
                    var nickname = info.nickname;
                    var tmp = {
                        'message': '进入了直播间',
                        'nickname':nickname ,
                        'type':99 ,
                        'time':info.time
                    }
                    // io.to('mid:' + mid).emit('server_send_message', tmp);
                }

                if (vaildUser == 1) {
                    //直播人数
                    client.get(mid + '_userCount', function (err, object) {
                        var count = 0;
                        if (null == err) {
                            count = parseInt(object);
                            if (myIsNaN2(count)) {
                                count = 0;
                            }
                            // console.log('count ' + count);
                        }
                        client.set(mid + '_userCount', (count + 1), function (err) {
                            // console.log(err)
                        });
                    });
                }

                if (isPc == 1 || 1) {
                    //发送之前的 历史记录
                    client.get(mid + '_history', function (err, object) {
                        if (null == err) {
                            // console.log('send 1 '+JSON.stringify(object));
                            if (null == object) {
                                return;
                            }
                            object = JSON.parse(object.toString());
                            // console.log(JSON.stringify(object));
                            if (object.length == 0) {
                                return;
                            }
                            io.to('mid:' + mid).emit('server_history_message', object);
                        }
                    });
                }
            }
            else{

            }
        }
        catch (e){
            console.log(e);
        }
    });

    socket.on('disconnect', function (socket) {
        if (vaildUser == 1) {
            client.get(mid + '_userCount', function (err, object) {
                var count = 0;
                if (null == err) {
                    count = parseInt(object);
                    if (myIsNaN2(count)) {
                        count = 0;
                    }
                }
                if (count <= 0) {
                    count = 1;
                }
                client.set(mid + '_userCount', (count - 1), function (err) {

                });
            });
        }
    });
});

function userPostChat(info,socket_mid) {
    console.log('socket start')
    try {
        var message = info.message;
// console.log(message);
        if (message == null || message.length <= 0){
            return;
        }

        //匹配不应该发的内容
        var phoneReg = new RegExp("^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\\d{8}$");
        var tmp = phoneReg.exec(message);
        if (tmp && tmp.length > 0){
            return;
        }
        if (message.indexOf('足彩') >= 0){
            return;
        }

        //验证
        var md5 = crypto.createHash('md5');

        var time = info.time;
        var verification = info.verification;
        var key = message + '?' + time.substring(time.length - 1) + '_' + time.substring(time.length - 2);
        var result = md5.update(key).digest('hex');
        //


        var current_time = Date.parse( new Date())/1000 + '';
        // console.log(result);
        // console.log(verification);
        // console.log(current_time);
        // console.log(time);
        if (result == verification && Math.abs(current_time - time) < 60) {
            var nickname = '匿名';
            if (info.nickname && info.nickname.length > 0){
                nickname = info.nickname;
            }
            var tmp = {
                'message':info.message,
                'nickname':nickname,
                'time':info.time
            }
            io.to('mid:' + socket_mid).emit('server_send_message', tmp);
            console.log('socket send ' + socket_mid);
            //保存历史记录
            client.get(socket_mid+'_history', function(err, object) {
                if (null == err) {
                    if (null == object){
                        object = new Array();
                    }
                    else{
                        object = JSON.parse(object.toString());
                    }
                }
                // console.log('bj1 '+JSON.stringify(object));
                // console.log('bj2 '+JSON.stringify(tmp));
                object.push(tmp);
                // console.log('bj3 '+JSON.stringify(object));
                client.set(socket_mid+'_history', JSON.stringify(object), function(err) {
                    // console.log(err);
                });
                client.expire(socket_mid+'_history', 60*60*3);
            });
        }
        else {
            console.log('in error');
        }
    }
    catch (e){
        console.log(e);
    }
}

function myIsNaN2(value) {
    return typeof value === 'number' && isNaN(value);
}

//定时任务
var schedule = require('node-schedule');
function scheduleCronstyle(){
    // for (var i = 0 ; i < 12 ; i++){
    //     schedule.scheduleJob(i*5 + ' * * * * *',function(){
    //         // console.log('scheduleCronstyle:'+new Date());
    //         postScore();
    //     });
    // }
    // schedule.scheduleJob('5 * * * * *',function(){
    //     // console.log('scheduleCronstyle:'+new Date());
    //     postColor();
    // });
}

function postScore() {
    //缓存里面拿数据
    client.get('redis_refresh_match', function(err, object) {
        if (null == err && object != null) {
            var datas = JSON.parse(object);
            for (var i = 0 ; i < datas.length ; i++){
                var data = datas[i];
                if (data['sport'] == 1){
                    var score = {
                        'hscore':data['hscore'],
                        'ascore':data['ascore'],
                        'time':data['time'],
                        'status':data['status'],
                        'sport':data['sport'],
                    }
                    // console.log(score);
                    io.to('mid:' + '99_'+data['room_id']).emit('server_match_change', score);
                }
                else if(data['sport'] == 2){
                    var score = {
                        'hscore':data['hscore'],
                        'ascore':data['ascore'],
                        'time':data['time'],
                        'status':data['status'],
                        'sport':data['sport'],
                        'time2':data['time2']
                    }
                    // console.log(score);
                    io.to('mid:' + '99_'+data['room_id']).emit('server_match_change', score);
                }
            }
        }
    });
}

function postColor() {
    //缓存里面拿数据
    client.get('redis_refresh_color', function(err, object) {
        if (null == err && object != null && object.length > 0) {
            var datas = JSON.parse(object);
            for (var i = 0 ; i < datas.length ; i++){
                var data = datas[i];
                var score = {
                    'h_color':data['h_color'],
                    'a_color':data['a_color']
                }
                // console.log(score);
                io.to('mid:' + '99_'+data['room_id']).emit('server_color_change', score);
            }

            // var tmp = [];
            client.set('redis_refresh_color',  '', function(err) {
                // console.log(err)
            });
        }
    });
}

scheduleCronstyle();


// 監聽 6001 port
http.listen(6001, function() {
    console.log('Listening on Port 6001');
});