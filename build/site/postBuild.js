var replace = require("replace")

replace({
  regex: "\"(\\/static.+?(.js|.css))\"",
  replacement: "\"{{ asset('$1') }}\"",
  paths: ['resources/views/layouts/site_base.blade.php'],
  silent: true
})
