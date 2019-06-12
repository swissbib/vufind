const path = require('path');

module.exports = {
    entry: [
        './themes/sbvfrd/ts/app.ts',
        //'./themes/sbvfrd/ts/Hydra.ts',
        './themes/sbvfrd/ts/RdfApi.ts',
        './themes/sbvfrd/ts/RecordRenderer.ts',
        './themes/sbvfrd/ts/autosuggest/Translator.ts',
        './themes/sbvfrd/ts/autosuggest/Configuration.ts',
        './themes/sbvfrd/ts/autosuggest/Settings.ts',
        './themes/sbvfrd/ts/autosuggest/Templates.ts',
        './themes/sbvfrd/ts/autosuggest/SearchResultConverter.ts',
        './themes/sbvfrd/ts/autosuggest/Section.ts',
        './themes/sbvfrd/ts/autosuggest/SectionLoader.ts',
        './themes/sbvfrd/ts/autosuggest/SectionLimitValidator.ts',
        './themes/sbvfrd/ts/autosuggest/ResultCallback.ts',
        './themes/sbvfrd/ts/autosuggest/Item.ts',
        './themes/sbvfrd/ts/autosuggest/ItemCollection.ts',
        './themes/sbvfrd/ts/autosuggest/AutoSuggest.ts'
    ],
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