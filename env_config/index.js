// see http://vuejs-templates.github.io/webpack for documentation.
var path = require('path')

module.exports = {
  build: {
    site: {
      env: require('./prod.env'),
      index: path.resolve(__dirname, '../resources/views/layouts/site_base.blade.php'),
      assetsRoot: path.resolve(__dirname, '../public'),
      assetsSubDirectory: 'static',
      assetsPublicPath: '/',
      productionSourceMap: true,
      // Gzip off by default as many popular static hosts such as
      // Surge or Netlify already gzip all static assets for you.
      // Before setting to `true`, make sure to:
      // npm install --save-dev compression-webpack-plugin
      productionGzip: false,
      productionGzipExtensions: ['js', 'css']
    },
    admin: {
      env: require('./prod.env'),
      index: path.resolve(__dirname, '../resources/views/layouts/admin_base.blade.php'),
      assetsRoot: path.resolve(__dirname, '../public'),
      assetsSubDirectory: 'static/admin',
      assetsPublicPath: '/',
      productionSourceMap: true,
      // Gzip off by default as many popular static hosts such as
      // Surge or Netlify already gzip all static assets for you.
      // Before setting to `true`, make sure to:
      // npm install --save-dev compression-webpack-plugin
      productionGzip: false,
      productionGzipExtensions: ['js', 'css']
    }
  },
  dev: {
    site: {
      env: require('./dev.env'),
      port: 8088,
      assetsSubDirectory: 'static',
      assetsPublicPath: '/',
      proxyTable: {
        '**': {
          target: process.env.APP_URL,
          changeOrigin: true,
          xfwd: true
        }
      },
      // CSS Sourcemaps off by default because relative paths are "buggy"
      // with this option, according to the CSS-Loader README
      // (https://github.com/webpack/css-loader#sourcemaps)
      // In our experience, they generally work as expected,
      // just be aware of this issue when enabling this option.
      cssSourceMap: false
    },
    admin: {
      env: require('./dev.env'),
      port: 8089,
      assetsSubDirectory: 'static',
      assetsPublicPath: '/',
      proxyTable: {
        '**': {
          target: process.env.APP_URL,
          changeOrigin: true,
          xfwd: true
        }
      },
      // CSS Sourcemaps off by default because relative paths are "buggy"
      // with this option, according to the CSS-Loader README
      // (https://github.com/webpack/css-loader#sourcemaps)
      // In our experience, they generally work as expected,
      // just be aware of this issue when enabling this option.
      cssSourceMap: false
    }
  }
}
