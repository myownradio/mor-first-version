<?php

$tracks = db::query("SELECT `tid`,`title`,`artist`,`album` FROM `r_tracks` WHERE 1");

foreach($tracks as $track)
{
    db::query_update("UPDATE `r_tracks` SET `title` = ?, `artist` = ?, `album` = ? WHERE `tid` = ?", array(
        misc::cp1252dec($track['title']),
        misc::cp1252dec($track['artist']),
        misc::cp1252dec($track['album']),
        $track['tid']
    ));
}
