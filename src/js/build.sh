#!/usr/bin/env bash

cd "$( dirname "${BASH_SOURCE[0]}" )"

npm update\
&& node_modules/browserify/bin/cmd.js -t [ stringify ] main.js -o ../php/public/js/main.js\

echo 'done'