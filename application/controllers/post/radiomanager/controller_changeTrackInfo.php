<?php

echo track::updateFileInfo(
    application::post('track_id',   NULL, 'int'), 
    application::post('artist',     NULL, 'string'), 
    application::post('title',      NULL, 'string'), 
    application::post('album',     NULL, 'string'), 
    application::post('track_number',     NULL, 'string'), 
    application::post('genre',      NULL, 'string'),
    application::post('date',      NULL, 'string')
);