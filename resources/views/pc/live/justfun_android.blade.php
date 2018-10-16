<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script type="text/javascript">
        function onTest(cid) {
            // js执行的代码
            url = 'http://m.justfun.live/tv/' + cid;

            $.ajaxPrefilter(function (options) {
                if (options.crossDomain && jQuery.support.cors) {
                    var http = (window.location.protocol === 'http:' ? 'http:' : 'https:');
                    options.url = http + '//cors-anywhere.herokuapp.com/' + options.url;
                    //options.url = "http://cors.corsproxy.io/url=" + options.url;
                }
            });

            $.get(url, function (response) {
                var tempStr = response.split("fengyuncid: \"")[1];
                tempStr = tempStr.split("\",")[0];
//                var matches = response.match(/<video _src='(.*?)' class=/is);
//                m3u8Url = matches[1];
                var infoUrl = "http://www.justfun.live/live-channel-info/channel/info?cid=" + tempStr;
                $.get(infoUrl, function (response) {
//                    console.log(response);
                    var data = JSON.parse(response);
//                    console.log(data);
                    var infoData = base64Decode(data['vipPlayInfo']);
                    while (!infoData.endsWith('}')) {
                        infoData = infoData.substr(0, infoData.length - 1);
                    }
//                    console.log(infoData);
                    infoData = JSON.parse(infoData);
//                    console.log(infoData);
                    var rtmpUrl = infoData['origin'];
                    var flvUrl = infoData['origin_flv'];

                    location.href = rtmpUrl;
                });
            });
        }

        onTest(getUrlParam("cid"));

        //paraName 等找参数的名称
        function getUrlParam(paraName) {
            var url = document.location.toString();
            var arrObj = url.split("?");

            if (arrObj.length > 1) {
                var arrPara = arrObj[1].split("&");
                var arr;

                for (var i = 0; i < arrPara.length; i++) {
                    arr = arrPara[i].split("=");

                    if (arr != null && arr[0] == paraName) {
                        return arr[1];
                    }
                }
                return "";
            }
            else {
                return "";
            }
        }
        
        function base64Decode(vipInfo) {
            var testInfo = window.atob(vipInfo);

            var testArray = [];
            for (var i = 0; i < testInfo.length; i++) {
                testArray[i] = testInfo.charCodeAt(i);
            }

            testArray = decrypt(testArray);

            var testStr = "";
            for (i = 0; i < testArray.length; i++) {
                testStr += String.fromCharCode(testArray[i]);
            }
            return testStr;
        }

        function decrypt(arg1)
        {
            var loc1 =undefined;
            var loc2 =undefined;
            var loc3 =undefined;
            var loc4 =undefined;
            var loc5 =0;
            var loc6 =null;
            var loc7 =0;
            var loc8 =arg1.length;
            if (arg1.length > 12)
            {
                if (arg1[0] == 255 && arg1[1] == 255 && arg1[2] == 255 && arg1[3] == 254)
                {
                    loc1 = arg1[4];
                    loc2 = arg1[5];
                    loc3 = arg1[6];
                    loc4 = arg1[7];
                    if ((loc5 = (arg1[loc3 + 8] & 255 ^ loc1) << 24 | (arg1[loc3 + 9] & 255 ^ loc2) << 16 | (arg1[loc3 + 10] & 255 ^ loc1) << 8 | arg1[loc3 + 11] & 255 ^ loc2) == arg1.length - 12 - loc3 - loc4)
                    {
                        loc6 = [];
                        loc7 = loc3 + 12;
                        --loc8;
                        while (loc8 >= 0)
                        {
                            loc6[loc8] = arg1[loc7 + loc8] ^ ((loc8 & 1) != 0 ? loc2 : loc1);
                            --loc8;
                        }
                        return loc6;
                    }
                }
            }
            return [];
        }
    </script>
</head>
<body></body>
</html>