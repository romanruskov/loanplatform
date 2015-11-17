<?php

$params = array(
    'database_host',
    'database_port',
    'database_name',
    'database_user',
    'database_password',
    'mailer_transport',
    'mailer_host',
    'mailer_user',
    'mailer_password',
    'secret',
    'elastica_url',
    'rabbitmq_host',
    'rabbitmq_port',
    'rabbitmq_user',
    'rabbitmq_password',
    'rabbitmq_vhost',
    'amazon_s3_key',
    'amazon_s3_secret',
    'amazon_s3_region',
    'amazon_s3_version'
);

foreach($params as $param){
    $container->setParameter($param, getenv(strtoupper($param)));
}
