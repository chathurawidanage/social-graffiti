<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Twitter Wall</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/animate.css">
</head>
<body>

<div class="wall-wrapper" id="wall-wrapper">
    <button id="fullscreen-btn">
        <img src="img/ic_all_out_white_24dp_2x.png">
    </button>
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
<div class="search-wrapper">
    <input type="text" id="search-box" value="#gsoc">
    <button id="search-btn">
        <img src="img/ic_track_changes_white_18dp_2x.png">
    </button>
</div>

</body>
<script type="text/javascript" src="jquery-2.2.3.min.js"></script>
<script type="text/javascript">

    $(document).ready(function () {
        doKeywordSearch();//intialize with default value
        /**Full screen**/
        var fullScreenMode = false;
        $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange', function (ev) {
            if (fullScreenMode) {
                fullScreenMode = false;
                $("#fullscreen-btn").show();
                $("#wall-wrapper").css('height', '400px');
                $("#wall-wrapper").css('width', '800px');
            } else {
                fullScreenMode = true;
                $("#fullscreen-btn").hide();
                $("#wall-wrapper").css('height', '100%');
                $("#wall-wrapper").css('width', '100%');
            }
            resizeText();
        });

        function fullscreen() {
            var element = document.getElementById('wall-wrapper');
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

        $("#fullscreen-btn").click(fullscreen);

        /**Animations**/
        $.fn.extend({
            animateCss: function (animationName, callback) {
                var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
                $(this).addClass('animated ' + animationName).one(animationEnd, function () {
                    $(this).removeClass('animated ' + animationName);
                    if (callback != undefined) {
                        callback();
                    }
                });
            }
        });

        var tweets = [];
        var index = 0;//currently displaying index

        /**
         * starts the wall
         **/
        function start() {
            if (tweets.length > 0) {
                setTweet();
            }
        }

        const defaultFontSize = 14;
        var setTweetCaller;

        function setTweet() {
            clearTimeout(setTweetCaller);
            console.log('called set tweet');
            $('#wall-left').animateCss('bounceOutLeft');
            $('#wall-right').animateCss('bounceOutRight', function () {
                $(".wall-part").css('visibility', 'hidden');
                if (tweets != undefined && tweets.length != 0) {
                    if (index >= tweets.length) {
                        index = 0;
                    }

                    var tweet = tweets[index++];
                    $(".content-text").text(tweet.text);
                    $(".content-user-name").text(tweet.user.name);
                    $(".content-user-id").text("@" + tweet.user.screen_name);

                    var media = tweet.entities.media;
                    var imageUrl = tweet.user.profile_image_url.toString().replace("_normal", "");//show profile pic if no media
                    if (media && media[0]) {
                        imageUrl = media[0].media_url;
                    }
                    $("#wall-right").css('background-image', 'url(' + imageUrl + ")");
                    var finalFontSize = resizeText();
                    if (finalFontSize < defaultFontSize) {
                        setTweet();//try next if current is too small
                        return;
                    }
                }

                $(".wall-part").css('visibility', 'visible');
                $('#wall-left').animateCss('bounceInLeft');
                $('#wall-right').animateCss('bounceInRight');

                setTweetCaller=setTimeout(setTweet, getInterval());
            });
        }

        /**
         * Resize the text to fit the content
         */
        function resizeText() {
            var textSize = defaultFontSize;
            var container = $("#wall-left");
            var content = $(".content-wrapper");
            while (content.width() <= container.width() && content.height() < container.height()) {
                textSize++;
                setFontSize(textSize);
            }
            return setFontSize(textSize - 1);
        }

        function setFontSize(size) {
            $(".content-user-name").css('fontSize', size + 2 + "px");
            $(".content-user-id").css('fontSize', size + 2 + "px");
            $(".content-text").css('fontSize', size + "px");

            return size;
        }

        var continousSearchInterval;
        var nextUrl;

        /**
         * fetchTweets by keyword
         * */
        function fetchTweets(keyword, callback) {
            clearInterval(continousSearchInterval);
            if (keyword === undefined || keyword.toString().trim() === "") {
                keyword = "cwidanage"
            }
            $.get("tweets.php?q=" + keyword+" -RT", function (data, error) {
                console.log(error);
                tweets = data.statuses;
                console.log(tweets);

                nextUrl = data.search_metadata.next_results;

                if (nextUrl) {//lesser than 15 means no more results. Then should keep refreshing
                    continousSearchInterval = setInterval(function () {
                        fetchNextResult();
                    }, 1000000);
                }else{
                    console.log("no next url");
                }

                if (callback != undefined) {
                    callback();
                }
            });
        }

        function fetchNextResult() {
            console.log("fetching next set : "+nextUrl);
            $.get("tweets.php" + nextUrl, function (data, error) {
                console.log(error);
                tweets = tweets.concat(data.statuses);
                console.log(tweets);

                nextUrl = data.search_metadata.next_results;
                if (!nextUrl) {
                    clearInterval(continousSearchInterval);
                }
            });
        }

        function doKeywordSearch() {
            var keyword = $("#search-box").val();
            fetchTweets(encodeURIComponent(keyword), start);
        }

        /**Search functionality**/
        $("#search-btn").click(function () {
            doKeywordSearch();
        });


        /**Interval**/
        function getInterval() {
            return 10000;
        }
    });
</script>
</html>