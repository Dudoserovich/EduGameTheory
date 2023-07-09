const path = require("path");
const HtmlWebpackPlugin = require("html-webpack-plugin");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const FaviconsWebpackPlugin = require('favicons-webpack-plugin')
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const TerserPlugin = require("terser-webpack-plugin");
const webpack = require("webpack");

const srcDir = path.resolve(__dirname, "src");
const outputDir = path.resolve(__dirname, "dist");
const polyfillsPath = path.resolve(__dirname, "polyfills")
const publicDir = path.resolve(__dirname, "public");

module.exports = (env, argv) => {
  process.env.NODE_ENV = argv.mode;

  const isDevelopment = argv.mode !== "production";

  return {
    devtool: isDevelopment ? "inline-source-map" : "source-map",
    devServer: isDevelopment
        ? {
          hot: true,
          static: outputDir,
          compress: true,
          allowedHosts: "all",
          historyApiFallback: {
            index: "/",
          },
          host: "0.0.0.0",
          port: 8090,
          client: false,
          webSocketServer: false
        }
        : undefined,
    entry: path.resolve(srcDir, "index.tsx"),
    output: {
      filename: "[name].bundle.[fullhash].js",
      chunkFilename: "[name].chunk.[chunkhash].js",
      path: outputDir,
      publicPath: "/",
    },
    optimization: {
      minimize: !isDevelopment,
      splitChunks: {
        chunks: "all",
        maxInitialRequests: Infinity,
        minSize: 0,
        cacheGroups: {
          defaultVendors: {
            test: /[\\/]node_modules[\\/]/,
            priority: -10,
            reuseExistingChunk: true,
          },
          default: {
            minChunks: 2,
            priority: -20,
            reuseExistingChunk: true,
          },
        },
      },
      minimizer: [!isDevelopment && new TerserPlugin()].filter(Boolean),
    },
    module: {
      rules: [
        {
          test: /\.(js|jsx|ts|tsx)$/,
          exclude: /node_modules/,
          use: ["babel-loader"],
        },
        {
          test: /.svg$/,
          loader: 'svg-inline-loader'
        },
        {
          test: /\.css$/,
          include: [srcDir, /node_modules/],
          use: [
            {
              loader: MiniCssExtractPlugin.loader,
              options: {},
            },
            {
              loader: "css-loader",
              options: {
                sourceMap: isDevelopment,
                url: false,
              },
            },
          ],
        },
        {
          test: /\.s[ac]ss$/i,
          use: [
            // Creates `style` nodes from JS strings
            "style-loader",
            // Translates CSS into CommonJS
            "css-loader",
            'resolve-url-loader',
            // Compiles Sass to CSS
            "sass-loader",
          ],
        },
      ],
    },
    plugins: [
      new webpack.EnvironmentPlugin({
        NODE_ENV: argv.mode,
      }),
      new webpack.DefinePlugin({
        "process.env.NODE_ENV": JSON.stringify(argv.mode),
      }),
      new MiniCssExtractPlugin({
        filename: "[name].[contenthash].css",
        chunkFilename: "[name].[contenthash].chunk.css",
      }),
      new CopyWebpackPlugin({
        patterns: [
          {
            from: "./public",
            to: "./",
          },
        ],
      }),
      new HtmlWebpackPlugin({
        filename: "index.html",
        template: path.resolve(srcDir, "index.html"),
      }),
      // new FaviconsWebpackPlugin({
      //   logo: path.resolve(publicDir, "favicon.ico"),
      // }),
    ].filter(Boolean),
    resolve: {
      mainFiles: ["index"],
      extensions: [".js", ".jsx", ".ts", ".tsx"],
      alias: {
        "head": path.resolve(polyfillsPath, 'head.tsx'),
        "link": path.resolve(polyfillsPath, 'link.tsx'),
        "router": path.resolve(polyfillsPath, 'router.ts'),
      },
    },
  };
};
