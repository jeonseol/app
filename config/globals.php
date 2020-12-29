<?php

use DI\Container;

return function(Container $container) {

    $settings = $container->get("settings");

    return [
        "apptitle" => $settings['app.title'],
        "UserAgent" => filter_input(INPUT_SERVER, "HTTP_USER_AGENT"),
        "isAjaxRequest" => filter_input(INPUT_SERVER, "HTTP_X_REQUESTED_WITH") !== null,
        "ServerSignature" => ($sig = filter_input(INPUT_SERVER, "SERVER_SIGNATURE")) ? strip_tags($sig) : null
    ];
};



