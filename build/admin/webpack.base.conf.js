var path = require('path')
var config = require('../../env_config')
var utils = require('./utils')
var projectRoot = path.resolve(__dirname, '../../')

var env = process.env.NODE_ENV
// check env & config/index.js to decide whether to enable CSS Sourcemaps for the
// various preprocessor loaders added to vue-loader at the end of this file
var cssSourceMapDev = (env === 'development' && config.dev.admin.cssSourceMap)
var cssSourceMapProd = (env === 'production' && config.build.admin.productionSourceMap)
var useCssSourceMap = cssSourceMapDev || cssSourceMapProd

module.exports = {
  context: projectRoot + '/resources/assets/admin',
  entry: {
    app: './js/index.js'
  },
  output: {
    path: config.build.admin.assetsRoot,
    publicPath: process.env.NODE_ENV === 'production' ? config.build.admin.assetsPublicPath : config.dev.admin.assetsPublicPath,
    filename: '[name].js'
  },
  resolve: {
    extensions: ['', '.js', '.vue'],
    fallback: [path.join(__dirname, '../../node_modules')],
    alias: {
      'src': path.resolve(__dirname, '../../resources/'),
      'assets': path.resolve(__dirname, '../../resources/assets/admin'),
      'components': path.resolve(__dirname, '../resources/assets/admin/js/components')
    }
  },
  resolveLoader: {
    fallback: [path.join(__dirname, '../../node_modules')]
  },
  module: {
    preLoaders: [
      {
        test: /\.vue$/,
        loader: 'eslint',
        include: projectRoot,
        exclude: /node_modules/
      },
      {
        test: /\.js$/,
        loader: 'eslint',
        include: projectRoot,
        exclude: /node_modules/
      }
    ],
    loaders: [
      {
        test: /\.vue$/,
        loader: 'vue'
      },
      {
        test: /\.js$/,
        loader: 'babel',
        include: projectRoot,
        exclude: /node_modules/
      },
      {
        test: /\.json$/,
        loader: 'json'
      },
      {
        test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
        loader: 'url',
        query: {
          limit: 10000,
          name: utils.assetsPath('img/[name].[hash:7].[ext]')
        }
      },
      {
        test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
        loader: 'url',
        query: {
          limit: 10000,
          mimetype: "application/font-woff",
          name: utils.assetsPath('css/[name].[hash:7].[ext]')
        }
      },
      {
        test: /\.(eot|ttf|otf)(\?.*)?$/,
        loader: 'url',
        query: {
          limit: 10000,
          name: utils.assetsPath('css/[name].[hash:7].[ext]')
        }
      }
    ]
  },
  eslint: {
    formatter: require('eslint-friendly-formatter')
  },
  postcss: [
    require('autoprefixer')
  ],
  vue: {
    loaders: utils.cssLoaders({ sourceMap: useCssSourceMap }),
    postcss: [
      require('autoprefixer')({
        browsers: ['last 2 versions']
      })
    ]
  }
}
