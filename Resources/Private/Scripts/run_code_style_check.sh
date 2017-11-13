#!/usr/bin/env bash

pushd `dirname $0` > /dev/null
SCRIPTPATH=`pwd -P`
popd > /dev/null

set -e

BASEPATH="${SCRIPTPATH}/../../.."
PHPCSCMD="phpcs"

if [ "$1" = "fix" ]; then
    PHPCSCMD="phpcbf"
fi

cd ${BASEPATH}

${BASEPATH}/.Build/bin/phpcs --config-set installed_paths "${PWD}/.Build/vendor/de-swebhosting/php-codestyle/PhpCodeSniffer/,${PWD}/Tests/CodeSniffer/" > /dev/null

${BASEPATH}/.Build/bin/${PHPCSCMD} --standard=CzSimpleCal Classes
${BASEPATH}/.Build/bin/${PHPCSCMD} --standard=CzSimpleCal Configuration/TCA
${BASEPATH}/.Build/bin/${PHPCSCMD} --standard=CzSimpleCal Tests
${BASEPATH}/.Build/bin/${PHPCSCMD} --standard=CzSimpleCal ext_emconf.php
${BASEPATH}/.Build/bin/${PHPCSCMD} --standard=CzSimpleCal ext_localconf.php
${BASEPATH}/.Build/bin/${PHPCSCMD} --standard=CzSimpleCal ext_tables.php
