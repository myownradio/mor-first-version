<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>myownradio.biz - create your own web radiostation</title>
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
        <script src="/js/new.design.ui.js"></script>
        <script src="/js/mod.tracklist.ui.js"></script>
        <!--<script src="/js/JQuery.JSAjaxFileUploader.min.js"></script>-->
        <link rel='stylesheet' type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400,600&subset=latin,cyrillic">
        <link rel="stylesheet" type="text/css" href="/css/reset.css" />
        <link rel="stylesheet" type="text/css" href="/css/new.design.css" />
    </head>
    <body>
        <div class="rm_header_wrap rm_max_width">
            <ul class="rm_menu_wrap rm_fl_left">
                <li><a href="#">Radiomanager</a></li>
                <li><a href="#">Create stream</a></li>
                <li><a href="#">Upload track</a></li>
            </ul>
            <ul class="rm_menu_wrap rm_fl_right">
                <li><a href="#">Login</a></li>
                <li><a href="#">Register</a></li>
                <li><a href="#">Logout</a></li>
            </ul>
        </div>
        <div class="rm_body_wrap rm_max_width">
            <div class="rm_sidebar_wrap rm_fl_left">
                <div class="rm_sidebar_content">
                    <h1><a href="#">Library</a></h1>
                    <div class="rm_sep"></div>
                    <h1><a href="#">Streams</a></h1>
                    <ul class="rm_sidebar_list rm_streamlist">
                        <li><a href="#">Stream 1</a></li>
                        <li><a href="#">Stream 2</a></li>
                        <li><a href="#">Stream 3</a></li>
                        <li><a href="#">Stream 4</a></li>
                    </ul>
                    <div class="rm_sep"></div>
                    <h1><a href="#">Playlists</a></h1>
                    <ul class="rm_sidebar_list rm_playlist">
                        <li><a href="#">Playlist 1</a></li>
                        <li><a href="#">Playlist 2</a></li>
                        <li><a href="#">Playlist 3</a></li>
                        <li><a href="#">Playlist 4</a></li>
                    </ul>
                </div>
            </div>
            <div class="rm_content_wrap">
                <div class="rm_status_wrap">
                    <ul class="rm_status_list rm_fl_left">
                        <li>Total tracks: <span>0</span></li>
                    </ul>
                    <ul class="rm_status_list rm_fl_right">
                        <li>Total time: <span>00:00:00</span></li>
                        <li>Time left: <span>00:00:00</span></li>
                    </ul>
                </div>
                <div class="rm_tracks_wrap">
                    <div class="rm_tracks_table">
                        <div class="rm_tracks_head">
                            <div class="rm_tracks_cell">#</div>
                            <div class="rm_tracks_cell">Title</div>
                            <div class="rm_tracks_cell">Artist</div>
                            <div class="rm_tracks_cell">Album</div>
                            <div class="rm_tracks_cell">Genre</div>
                            <div class="rm_tracks_cell">Duration</div>
                            <div class="rm_tracks_cell">Track #</div>
                        </div>
                        <!-- repeat -->
                        <div class="rm_tracks_item" track-id="1" track-duration="432">
                            <div class="rm_tracks_cell">1</div>
                            <div class="rm_tracks_cell">Northern Lights</div>
                            <div class="rm_tracks_cell">Ted Irens</div>
                            <div class="rm_tracks_cell">Northern Lights (test)</div>
                            <div class="rm_tracks_cell">Chillout</div>
                            <div class="rm_tracks_cell">3:18</div>
                            <div class="rm_tracks_cell">1</div>
                        </div>
                        <!-- end repeaat -->
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>