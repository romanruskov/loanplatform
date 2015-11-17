# Loan platform
<http://loanplatform.herokuapp.com>

### Requirements
-   [symfony/symfony](https://github.com/symfony/symfony)
-   [doctrine/doctrine-bundle](https://github.com/doctrine/DoctrineBundle)
-   [doctrine/doctrine-fixtures-bundle](https://github.com/doctrine/DoctrineFixturesBundle)
-   [friendsofsymfony/rest-bundle](https://github.com/FriendsOfSymfony/FOSRestBundle)
-   [jms/serializer-bundle](https://github.com/schmittjoh/JMSSerializerBundle)
-   [friendsofsymfony/elastica-bundle](https://github.com/FriendsOfSymfony/FOSElasticaBundle)
-   [liip/imagine-bundle](https://github.com/liip/LiipImagineBundle)
-   [oldsound/rabbitmq-bundle](https://github.com/videlalvaro/rabbitmqbundle)
-   [aws/aws-sdk-php](https://github.com/aws/aws-sdk-php)

### Deploy
#### create heroku app
```
cd projects/loan_platform/
sudo heroku login
sudo heroku apps:create loanplatform
```

#### setup postgresql
`auth data isn't actual`
```
sudo heroku addons:create heroku-postgresql:hobby-dev
sudo heroku config -s | grep DATABASE_URL

DATABASE_URL=postgres://dgxycbzsfgnfpu:1BaANvQHONTOmluOfbs_yvFrrk@ec2-54-195-252-202.eu-west-1.compute.amazonaws.com:5432/d9334kls31ei6c

sudo heroku config:set DATABASE_HOST=ec2-54-195-252-202.eu-west-1.compute.amazonaws.com
sudo heroku config:set DATABASE_PORT=5432
sudo heroku config:set DATABASE_NAME=d9334kls31ei6c
sudo heroku config:set DATABASE_USER=dgxycbzsfgnfpu
sudo heroku config:set DATABASE_PASSWORD=1BaANvQHONTOmluOfbs_yvFrrk
```

#### postgresql cli
```
sudo heroku pg:psql --app loanplatform
---> Connecting to DATABASE_URL
psql (9.4.5, server 9.4.4)

loanplatform::DATABASE=> \c d9334kls31ei6c
You are now connected to database "d9334kls31ei6c" as user "dgxycbzsfgnfpu".

loanplatform::DATABASE=> \dt
              List of relations
 Schema |    Name    | Type  |     Owner      
--------+------------+-------+----------------
 public | avatar     | table | dgxycbzsfgnfpu
 public | investment | table | dgxycbzsfgnfpu
 public | investor   | table | dgxycbzsfgnfpu
 public | loan       | table | dgxycbzsfgnfpu
(4 rows)

loanplatform::DATABASE=> select * from loan limit 3;
```

#### setup cloudamqp (rabbitmq)
`auth data isn't actual`
```
sudo heroku addons:create cloudamqp:lemur
sudo heroku config -s | grep CLOUDAMQP

CLOUDAMQP_URL: amqp://cxhnpauh:bPcxhEF-071rM9iiE2gIWeqn-yJ2TBQ4@hare.rmq.cloudamqp.com/cxhnpauh

sudo heroku config:set RABBITMQ_HOST=hare.rmq.cloudamqp.com
sudo heroku config:set RABBITMQ_PORT=5672
sudo heroku config:set RABBITMQ_USER=cxhnpauh
sudo heroku config:set RABBITMQ_PASSWORD=bPcxhEF-071rM9iiE2gIWeqn-yJ2TBQ4
sudo heroku config:set RABBITMQ_VHOST=cxhnpauh
```

#### setup searchbox (elasticsearch)
`auth data isn't actual`
```
sudo heroku addons:create searchbox:starter
sudo heroku config -s | grep SEARCHBOX

SEARCHBOX_SSL_URL=https://paas:d57117c968362633e2870c9bcb498c31@kili-eu-west-1.searchly.com
SEARCHBOX_URL=http://paas:d57117c968362633e2870c9bcb498c31@kili-eu-west-1.searchly.com

sudo heroku config:set ELASTICA_URL=http://paas:d57117c968362633e2870c9bcb498c31@kili-eu-west-1.searchly.com
```

#### register at amazon and create a user with s3 access
```
https://console.aws.amazon.com/iam/home#home
```

#### open "users page", select user, click "manage access keys" to create one
```
https://console.aws.amazon.com/iam/home#users
```

#### open "s3 page" and create a bucket "img-avatar"
```
https://console.aws.amazon.com/s3/home
```

#### click "properties", "permissions", "edit bucket policy"
```
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AddPerm",
            "Effect": "Allow",
            "Principal": {
                "AWS": "*"
            },
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::img-avatar/*"
        }
    ]
}
```

#### set amazon s3 config
`auth data isn't actual`
```
sudo heroku config:set AMAZON_S3_KEY=AKIAIHNFPI32OEKHOZ7Q
sudo heroku config:set AMAZON_S3_SECRET=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
sudo heroku config:set AMAZON_S3_REGION=eu-west-1
sudo heroku config:set AMAZON_S3_VERSION=latest
```

#### setup mailer, symfony secret and environment
```
sudo heroku config:set MAILER_TRANSPORT=smtp
sudo heroku config:set MAILER_HOST=127.0.0.1
sudo heroku config:set MAILER_USER=
sudo heroku config:set MAILER_PASSWORD=
sudo heroku config:set SECRET=ONi0yX16gPVdUg8ZkWBRCb5ed75tgCb7
sudo heroku config:set SYMFONY_ENV=prod
```

#### push changes to "heroku remote" from git "master branch"
```
sudo git push heroku master
```

#### ensure we have one web dyno and one worker dyno running
```
sudo heroku ps:scale web=1 worker=1
```

#### create database schema and load fixtures
```
sudo heroku run "php app/console doctrine:schema:update --force"
sudo heroku run "php app/console doctrine:fixtures:load"
```

#### if searchbox throws errors
##### remove existing index "platform"
```
curl -XDELETE "http://paas:d57117c968362633e2870c9bcb498c31@kili-eu-west-1.searchly.com/platform/";
```

##### create new index "platform" and load fixtures once again
```
curl -X PUT "http://paas:d57117c968362633e2870c9bcb498c31@kili-eu-west-1.searchly.com/platform/" -d '{
    "index":{
        "analysis":{
            "analyzer":{
                "custom_analyzer":{
                    "type": "custom",
                    "tokenizer": "whitespace",
                    "filter": ["lowercase", "asciifolding", "word_delimiter", "snowball"]
                }
            },
            "filter":{
                "ngram_filter":{
                    "type":"ngram",
                    "min_gram":2,
                    "max_gram":16
                }
            }
        }
    }
}';
```

#### see heroku logs
```
sudo heroku logs --tail
```

#### list running processes
```
sudo heroku ps
=== web (Free): bin/heroku-php-apache2 web/
web.1: up 2015/11/11 14:00:00
=== worker (Free): php app/console rabbitmq:consumer -w generate_thumbnail
worker.1: up 2015/11/11 14:00:00
```

#### restart dynos
```
sudo heroku ps:restart
```

#### useful symfony development commands
```
php app/console list
php app/console server:run
php app/console rabbitmq:consumer -w generate_thumbnail

php app/console assets:install web/
php app/console cache:clear --env=dev

php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load
php app/console doctrine:database:create
php app/console doctrine:generate:entities PlatformBundle

php app/console debug:translation en PlatformBundle --only-unused
php app/console debug:translation en PlatformBundle --only-missing

php app/console debug:container | grep platform
php app/console debug:router --show-controllers | grep api_
php app/console router:match /auth/login

php app/console fos:elastica:populate --env=dev
php app/console fos:elastica:reset
php app/console fos:elastica:search loan "hello" --show-source

curl -i -d "platform_loan_search[query]=hello" -b "PHPSESSID=9u5j41um9oc1cavh0d7urp0an3" -H "Accept: application/json" -X POST http://127.0.0.1:8000/api/v1/loan/search

php app/console config:dump-reference AppBundle
php app/console config:dump-reference templating
php app/console config:dump-reference framework

php app/console security:check
php app/console remove:bundle --namespace=AppBundle

php app/console liip:imagine:cache:resolve relative/path/to/image.jpg relative/path/to/image2.jpg --filters=my_thumb --filters=thumbnail_default
```