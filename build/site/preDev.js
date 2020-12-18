// https://github.com/shelljs/shelljs
require('shelljs/global')

var path = require('path')

var source = path.resolve(__dirname, '../../resources/assets/site/index.dev.html')
var destination = path.resolve(__dirname, '../../resources/views/layouts/site_base.blade.php')

cp(source, destination)
