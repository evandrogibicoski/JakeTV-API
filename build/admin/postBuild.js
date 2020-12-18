var replace = require("replace")

replace({
  regex: "\"(\\/static.+?(.js|.css))\"",
  replacement: "\"{{ asset('$1') }}\"",
  paths: ['resources/views/layouts/admin_base.blade.php'],
  silent: true
})
