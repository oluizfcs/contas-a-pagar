<?php

echo "welcome";

if(isset($_GET['url'])) {
    $url = explode('/', filter_input(INPUT_GET, 'url', FILTER_DEFAULT));
}
