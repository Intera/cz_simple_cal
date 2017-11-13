#!/usr/bin/env bash

pushd `dirname $0` > /dev/null
SCRIPTPATH=`pwd -P`
popd > /dev/null

set -e

BASEPATH="${SCRIPTPATH}/../../.."

cd ${BASEPATH}

if [ -z "$typo3DatabaseName" ]; then
    echo "Reading DB config from .env file"
    source "${BASEPATH}/.env"
fi

export typo3DatabaseName
export typo3DatabaseUsername
export typo3DatabasePassword
export typo3DatabaseHost
export typo3InstallToolPassword

export TYPO3_PATH_WEB="$PWD/.Build/Web"

${BASEPATH}/.Build/bin/phpunit \
    -c .Build/vendor/nimut/testing-framework/res/Configuration/FunctionalTests.xml  \
    Tests/Functional
