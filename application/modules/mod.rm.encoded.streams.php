<?php

echo base64_encode(json_encode(stream::getStreams(user::getCurrentUserId())));
