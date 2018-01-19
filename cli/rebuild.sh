#!/usr/bin/env bash

here="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

${here}/cssBuilder.sh;
${here}/removeLocalCache.sh;
npm run build