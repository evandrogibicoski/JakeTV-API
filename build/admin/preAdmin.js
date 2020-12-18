// https://github.com/shelljs/shelljs
require('shelljs/global')

var path = require('path')

var source = path.resolve(__dirname, '../../resources/assets/admin/index.dev.html')
var destination = path.resolve(__dirname, '../../resources/views/layouts/admin_base.blade.php')

cp(source, destination)
