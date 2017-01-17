#!/usr/bin/env bash

set -e
set -x

BUILD_DIR="/tmp/magento2"

if [ -z $TRAVIS_BUILD_DIR ]; then TRAVIS_BUILD_DIR=`pwd`; fi
if [ -z $TRAVIS_COMMIT ]; then TRAVIS_COMMIT=`git rev-parse HEAD`; fi
if [ -z $MAGENTO_VERSION ]; then MAGENTO_VERSION="2.1.3"; fi
if [ -z $MAGENTO_DB_HOST ]; then MAGENTO_DB_HOST="localhost"; fi
if [ -z $MAGENTO_DB_PORT ]; then MAGENTO_DB_PORT="3306"; fi
if [ -z $MAGENTO_DB_USER ]; then MAGENTO_DB_USER="root"; fi
if [ -z $MAGENTO_DB_PASS ]; then MAGENTO_DB_PASS=""; fi
if [ -z $MAGENTO_DB_NAME ]; then
    MAGENTO_DB_NAME="magento";
fi

MYSQLPASS=""
if [ ! -z $MAGENTO_DB_PASS ]; then MYSQLPASS="-p${MAGENTO_DB_PASS}"; fi

mkdir -p ${BUILD_DIR}

composer global require "squizlabs/php_codesniffer=*"
composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition=${MAGENTO_VERSION} ${BUILD_DIR}

find Test/Fixtures -type f -print0 | xargs -0 -n 1 sed -i -e "s/MAGENTO_DB_HOST/${MAGENTO_DB_HOST}/g"
find Test/Fixtures -type f -print0 | xargs -0 -n 1 sed -i -e "s/MAGENTO_DB_PORT/${MAGENTO_DB_PORT}/g"
find Test/Fixtures -type f -print0 | xargs -0 -n 1 sed -i -e "s/MAGENTO_DB_USER/${MAGENTO_DB_USER}/g"
find Test/Fixtures -type f -print0 | xargs -0 -n 1 sed -i -e "s/MAGENTO_DB_PASS/${MAGENTO_DB_PASS}/g"
find Test/Fixtures -type f -print0 | xargs -0 -n 1 sed -i -e "s/MAGENTO_DB_NAME/${MAGENTO_DB_NAME}/g"

cp -v Test/Fixtures/env.php "${BUILD_DIR}/app/etc/env.php"
cp -v Test/Fixtures/config.php "${BUILD_DIR}/app/etc/config.php"
cp -v Test/Fixtures/install-config-mysql.php "${BUILD_DIR}/dev/tests/integration/etc/install-config-mysql.php"
cp -v Test/Fixtures/phpunit.xml "${BUILD_DIR}/dev/tests/integration/phpunit.xml"

zip --exclude=node_modules/* --exclude=vendor/* --exclude=.git/* -r build.zip .

REPOSITORY_CONFIG="{\"type\": \"package\",\"package\": { \"name\": \"tig/postnl\", \"version\": \"master\", \"dist\": {\"type\": \"zip\",\"url\": \"${TRAVIS_BUILD_DIR}/build.zip\",\"reference\": \"master\" }, \"autoload\": {\"files\": [\"registration.php\"],\"psr-4\": {\"TIG\\\\PostNL\\\\\": \"\"}}}}"

( cd "${BUILD_DIR}/" && composer config minimum-stability dev )
( cd "${BUILD_DIR}/" && composer config repositories.postnl "${REPOSITORY_CONFIG}" )
( cd "${BUILD_DIR}/" && composer require tig/postnl )

mysql -u${MAGENTO_DB_USER} ${MYSQLPASS} -h${MAGENTO_DB_HOST} -P${MAGENTO_DB_PORT} -e "DROP DATABASE IF EXISTS \`${MAGENTO_DB_NAME}\`; CREATE DATABASE \`${MAGENTO_DB_NAME}\`;"
mysql -u${MAGENTO_DB_USER} ${MYSQLPASS} -h${MAGENTO_DB_HOST} -P${MAGENTO_DB_PORT} ${MAGENTO_DB_NAME} < Test/Fixtures/tig-postnl-fixture.sql

chmod 777 "${BUILD_DIR}/var/"
chmod 777 "${BUILD_DIR}/pub/"

( cd ${BUILD_DIR} && php -d memory_limit=2048M bin/magento setup:upgrade )
