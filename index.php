<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Twitter Wall</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="wall-wrapper">
    <button id="fullscreen-btn" onclick="full()">+</button>
    <div id="wall">
        <div id="wall-left" class="wall-part">
            <div class="content-wrapper">
                <p class="content-user-name"></p>
                <p class="content-text"></p>
                <p class="content-user-id"></p>
            </div>
        </div>
        <div id="wall-right" class="wall-part"></div>
    </div>
</div>

</body>
<script type="text/javascript" src="jquery-2.2.3.min.js"></script>
<script type="text/javascript">
    /**Full screen**/
    function requestFullScreen(element) {
        var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullScreen;

        if (requestMethod) { // Native full screen.
            requestMethod.call(element);
        } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
            var wscript = new ActiveXObject("WScript.Shell");
            if (wscript !== null) {
                wscript.SendKeys("{F11}");
            }
        }
    }

    var elem = document.getElementById('wall'); // Make the body go full screen.

    function full() {
        requestFullScreen(elem);
    }

    $.fn.extend({
        animateCss: function (animationName) {
            var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
            $(this).addClass('animated ' + animationName).one(animationEnd, function () {
                $(this).removeClass('animated ' + animationName);
            });
        }
    });

    $(document).ready(function () {
        var tweets = [];
        $.get("data.json", function (data) {
            tweets = data.statuses;
            console.log(tweets);
        });

        setTweet();
        setInterval(setTweet, 5000);
        var index = 0;

        function setTweet() {
            if (tweets != undefined && tweets.length != 0) {
                if (index >= tweets.length) {
                    index = 0;
                }

                var tweet = tweets[index++];
                $(".content-text").text(tweet.text);
                $(".content-user-name").text(tweet.user.name);
                $(".content-user-id").text("@"+tweet.user.screen_name);

                console.log(tweet.user.profile_image_url.toString().replace("_normal",""));
                $("#wall-right").css('background-image','url('+tweet.user.profile_image_url.toString().replace("_normal","")+")");
                resizeText();
            }
        }

        /**
         * Resize the text to fit the content
         */
        function resizeText() {
            var textSize = 14;
            var container = $("#wall-left");
            var content = $(".content-wrapper");
            while (/*content.width() <= container.width() && */content.height() < container.height()) {
                textSize++;
                setFontSize(textSize);
            }
            setFontSize(textSize - 1);
        }

        function setFontSize(size) {
            $(".content-user-name").css('fontSize', size + 2 + "px");
            $(".content-user-id").css('fontSize', size + 2 + "px");
            $(".content-text").css('fontSize', size + "px");
        }
    })
</script>
</html>