<script type="text/javascript">var mor = <?= json_encode(array(
    'user_id'               => user::getCurrentUserId(),
    'user_token'            => user::getCurrentUserToken(),
    'tracks_count'          => track::getTracksCount(user::getCurrentUserId()),
    'tracks_duration'       => (int)track::getTracksDuration(user::getCurrentUserId()),
    'streams_count'         => stream::getStreamsCount(user::getCurrentUserId()),
    'streams_limit'         => user::userStreamsMax(),
    'tracks_time_limit'     => user::userUploadLimit(),
    'active_plan'           => user::userActivePlan(),
    'last_event'            => events::getLastID(),
    'filter_type_delay'     => 150,
)); ?>
</script>