<?php
    $stream = application::singular('stream', $_MODULE['stream_id']);
    $stream_owner = user::getUserByUid($stream->getOwner());
?><!DOCTYPE html>
<html>
    <head>
        <title><?= $stream->getStreamName() ?> - myownradio.biz</title>
        <noscript><meta http-equiv="refresh" content="0; URL=/badbrowser"></noscript>
        <link rel="stylesheet" type="text/css" href="/css/reset.css" />
        <link href='http://fonts.googleapis.com/css?family=PT+Sans&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" type="text/css" href="/css/mod.main.css" />
        <link rel="stylesheet" type="text/css" href="/css/mod.main.listen.css" />
        <link rel="stylesheet" type="text/css" href="/css/mod.main.listen.css" />
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
        <script src="/js/jquery.livequery.js"></script>
        <script src="/js/jquery.jplayer.min.js"></script>
        <script src="/js/radioplayer.js"></script>
        <link rel="stylesheet" type="text/css" href="/icomoon/style.css" />
        <script>var myRadio = <?= json_encode($stream->getStreamStatus()); ?></script>
    </head>
    <body>
        <div id="jplayer"></div>
        <div class="rh_wrap">
            <div class="header">
                <div class="rh_width">
                    <ul class="bar fl_l">
                        <li><a class="logo" href="/">myownradio.biz</a></li>
                        <li><a href="#">Categories</a></li>
                        <li><a href="#">Search</a></li>
                    </ul>
                    <ul class="bar fl_r">
                        <li><a target="_blank" href="/signup">Sign Up</a></li>
                        <li><a target="_blank" href="/login">Login to RadioManager</a></li>
                    </ul>
                </div>
            </div>
            <div class="container">
                <div class="rh_width">
                    <div class="rh_infobar">
                        <ul class="info">
                            <li class="name"><?= $stream->getStreamName() ?></li>
                            <li class="owner">Stream Owner: <span><?= $stream_owner['name'] ?></span></li>
                        </ul>
                    </div>
                    <div class="rh_playbar stopped waiting">
                        <div class="play_cover fl_l">
                            
                        </div>
                        <div class="play_button fl_l">
                            <div class="pb_border" title="Play / Stop">
                                <div class="pb_fore">
                                    <i class="play_sw icon-play"></i>
                                </div>
                            </div>
                        </div>
                        <div class="play_info">
                            <div class="pl_status">Stopped</div>
                            <div class="hidable">
                                <div class="pl_title"></div>
                                <div class="pl_progress">
                                    <div class="pr_bg">
                                        <div class="fg"></div>
                                    </div>
                                </div>
                                <div class="pl_time_wrap">
                                    <div class="tm_fore fl_l">0:00</div>
                                    <div class="tm_back fl_r">0:00</div>
                                </div>
                            </div>
                        </div>
                        <div class="stream_info"><?= $stream->getStreamInfo() ?></div>
                    </div>
                </div>
            </div>
            <div class="footer">
                <div class="rh_width">
                    <ul class="bar fl_l">
                        <li><a href="/">Home</a></li>
                        <li><a href="#">Categories</a></li>
                        <li><a href="#">Search</a></li>
                        <li><a href="#">Terms of Use</a></li>
                        <li><a href="#">Copyright Infringement</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                    <ul class="bar fl_r">
                        <li>myownradio.biz &copy; 2014</li>
                    </ul>
                </div>
            </div>
        </div>
    </body>
</html>