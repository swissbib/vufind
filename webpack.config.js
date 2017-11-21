const path = require('path');

module.exports = {
    entry: ['./themes/sbvfrd/ts/Hydra.ts', './themes/sbvfrd/ts/app.ts'],
    output: {
        path: path.resolve(__dirname),
        filename: './themes/sbvfrd/js/swissbib/swissbib.ts.js'
        //, publicPath: '/dist'
    },
    resolve: {
        // Add `.ts` and `.tsx` as a resolvable extension.
        extensions: ['.webpack.js', '.web.js', '.ts', '.tsx', '.js']
    },
    module: {
        loaders: [
            // all files with a `.ts` or `.tsx` extension will be handled by `ts-loader`
            {include: /^(?!.*_spec\.ts?$).*\.ts?$/, loader: 'ts-loader'}
        ]
    },
    node: {
        fs: "empty"
    },
    externals: {
        "jquery": "jQuery"
    }
};