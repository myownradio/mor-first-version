<?php

if(user::getCurrentUserId() == 0)
{
    die("Unauthorized");
}

echo json_encode(
        array(
            'user_id' => user::getCurrentUserId(),
            //'user_name' => user::getCurrentUserLogin()
            'user_plan_active' => user::userActivePlan(),
            'user_plan_expire_date' => user::userPlanExpire(),
            'user_upload_time_left' => user::userUploadLeft()
        )
    );