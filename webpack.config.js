const path = require('path')

const port = process.env.PORT || 5000

const config = {
    devtool: 'cheap-eval-source-map',
    context: path.join(__dirname, './demo'),
    entry: {
        // vendor: ['react', 'react-dom', 'faker', 'interactjs', 'moment'],
        demo: [
            // `webpack-dev-server/client?http://0.0.0.0:${port}`,
            // 'webpack/hot/only-dev-server',
            './index.js'
        ]
    },
    output: {
        path: path.join(__dirname, './build'),
        publicPath: '',
        chunkFilename: '[name].bundle.js',
        filename: '[name].bundle.js'
    },
    mode: 'development',
    module: {
        rules: [
            {
                test: /\.scss$/,
                loader: 'style-loader!css-loader!sass-loader'
            },
            {
                test: /\.(js|jsx)$/,
                exclude: /node_modules/,
                loaders: ['babel-loader']
            },
            {
                test: /\.css$/,
                // exclude: /node_modules/,       there is a css file in node_modules/react-dates
                loaders: ["style-loader", "css-loader"]
            },
        ]
    },
    resolve: {
        extensions: ['.js', '.jsx'],
        modules: [path.resolve('./demo'), 'node_modules'],
        alias: {
            '~': path.join(__dirname, './demo'),
        }
    },
    devServer: {
        contentBase: './demo',
        port
    }
}

module.exports = config
