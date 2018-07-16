var app = require('express');
var http = require('http').Server(app);
var io = require('socket.io')(http, {'transports': ['websocket', 'polling']});
var Redis = require('ioredis');
var redis = require("redis"),
    client = redis.createClient();

var crypto = require('crypto');

io.on('connect', function (socket) {
    console.log('a user connected');

    var mid = '';

    //绑定监听事件
    //接收用户发的消息
    socket.on('user_send_message', function (info) {
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

            //验证
            var md5 = crypto.createHash('md5');

            var time = info.time;
            var verification = info.verification;
            var key = message + '?' + time.substring(time.length - 1) + '_' + time.substring(time.length - 2);
            var result = md5.update(key).digest('hex');
            // console.log('message ' + message);
            // console.log('time ' + time);
            // console.log('verification ' + verification);
            // console.log('key ' + key);
            // console.log('result ' + result);
            var current_time = Date.parse( new Date())/1000 + '';
            if (result == verification && Math.abs(current_time - time) < 10) {
                io.to('mid:' + mid).emit('notification', info.message);
                var nickname = '匿名';
                if (info.nickname && info.nickname.length > 0){
                    nickname = info.nickname;
                }
                var tmp = {
                    'message':info.message,
                    'nickname':nickname,
                    'time':info.time
                }
                io.to('mid:' + mid).emit('server_send_message', tmp);
            }
            else {
                console.log('in error');
            }
        }
        catch (e){
            console.log(e);
        }
        // io.to('mid:'+'1234').emit('notification', info.message+'2');
    });

    //接收用户说自己在哪个房间
    socket.on('user_mid', function (info) {
        try {
            mid = info.mid;
            //验证
            var md5 = crypto.createHash('md5');

            var time = info.time;
            var verification = info.verification;
            var message = mid;
            var key = message + '?' + time.substring(time.length - 1) + '_' + time.substring(time.length - 2);
            var result = md5.update(key).digest('hex');
            var current_time = Date.parse( new Date())/1000 + '';
            if (result == verification && Math.abs(current_time - time) < 10) {
                socket.join('mid:' + mid);

                //直播人数
                client.get(mid+'_userCount', function(err, object) {
                    var count = 0;
                    if (null == err) {
                        count = parseInt(object);
                        if (myIsNaN2(count)){
                            count = 0;
                        }
                        // console.log('count ' + count);
                    }
                    client.set(mid+'_userCount', (count+1), function(err) {
                        // console.log(err)
                    });
                });
            }
            else{

            }
        }
        catch (e){
            console.log(e);
        }
    });

    socket.on('disconnect', function (socket) {
        client.get(mid+'_userCount', function (err, object) {
            var count = 0;
            if (null == err) {
                count = parseInt(object);
                if (myIsNaN2(count)){
                    count = 0;
                }
            }
            if (count <= 0){
                count = 1;
            }
            client.set(mid+'_userCount', (count - 1), function (err) {

            });
        });
    });
});

function myIsNaN2(value) {
    return typeof value === 'number' && isNaN(value);
}

// 監聽 6001 port
http.listen(6001, function() {
    console.log('Listening on Port 6001');
});