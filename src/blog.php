<?php

$blog = $app['controllers_factory'];
$blog->get('/', function() {
    return 'Blog home page';
});

return $blog;