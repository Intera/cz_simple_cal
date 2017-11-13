#!/usr/bin/env bash

pushd `dirname $0` > /dev/null
SCRIPTPATH=`pwd -P`
popd > /dev/null

set -e

BASEPATH="${SCRIPTPATH}/../../.."

cd ${BASEPATH}

${BASEPATH}/.Build/bin/phpunit \
    -c .Build/vendor/nimut/testing-framework/res/Configuration/UnitTests.xml  \
    Tests/Unit
