#!/usr/bin/env sh

# -e = exit when one command returns != 0, -v print each command before executing
set -ev

old=$(pwd)

mkdir ${HUMHUB_PATH}
cd ${HUMHUB_PATH}

git clone --branch ${HUMHUB_VERSION} --depth 1 https://github.com/humhub/humhub.git .
composer install --prefer-dist --no-interaction

npm install

if [ "${HUMHUB_VERSION}" != 'v1.2.5' ] ; then
  grunt build-assets
fi

cd ${HUMHUB_PATH}/protected/humhub/tests

sed -i -e "s|'installed' => true,|'installed' => true,\n\t'moduleAutoloadPaths' => ['$(dirname $old)']|g" config/common.php

mysql -e 'CREATE DATABASE humhub_test;'
php codeception/bin/yii migrate/up --includeModuleMigrations=1 --interactive=0
php codeception/bin/yii installer/auto
php codeception/bin/yii search/rebuild

php ${HUMHUB_PATH}/protected/vendor/bin/codecept build