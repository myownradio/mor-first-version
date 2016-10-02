<?php

$startFrom = application::post("s", 0, REQ_INT);
$waitTime  = config::getSetting("status", "event_interval", 15);
$startTime = time();

do 
{
    $data = array();
    foreach(db::query("SELECT * FROM `m_events_log` WHERE `event_id` > ? AND `user_id` = ? ORDER BY `event_id` ASC", array($startFrom, user::getCurrentUserId())) as $row)
    {
        $data[] = $row;
        $startFrom = $row['event_id'];
    }
    usleep(250000);
}
while(time() - $startTime < $waitTime && count($data) == 0);
    
echo misc::outputJSON("STATUS_OK", array(
    'LAST_EVENT_ID' => $startFrom,
    'EVENTS' => $data
));