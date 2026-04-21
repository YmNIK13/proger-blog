const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');

// @wordpress/scripts auto-discovers src/**/block.json entries (editorScript
// only). Merge those with our extra entries: theme stylesheet, Interactivity
// nav store, and per-block view + style + editor style.
const autoEntry = typeof defaultConfig.entry === 'function'
	? defaultConfig.entry()
	: defaultConfig.entry;

// Override MiniCssExtractPlugin so emitted CSS filenames match the entry name
// literally (default behaviour appends `-style` when entry ends with `style`,
// which breaks our block.json `"style": "file:./style.css"` references).
const plugins = (defaultConfig.plugins || []).map((plugin) => {
	if (plugin && plugin.constructor && plugin.constructor.name === 'MiniCssExtractPlugin') {
		return new MiniCssExtractPlugin({
			filename: '[name].css',
			chunkFilename: '[id].css',
		});
	}
	return plugin;
});

module.exports = {
	...defaultConfig,
	entry: {
		...autoEntry,
		main: path.resolve(__dirname, 'src/styles/main.css'),
		'interactivity-nav': path.resolve(__dirname, 'src/scripts/interactivity/nav.js'),
		'blocks/code/view': path.resolve(__dirname, 'src/blocks/code/view.js'),
		'blocks/code/style': path.resolve(__dirname, 'src/blocks/code/style.scss'),
		'blocks/code/editor': path.resolve(__dirname, 'src/blocks/code/editor.scss'),
		'blocks/toc/view': path.resolve(__dirname, 'src/blocks/toc/view.js'),
		'blocks/toc/style': path.resolve(__dirname, 'src/blocks/toc/style.scss'),
	},
	optimization: {
		...defaultConfig.optimization,
		splitChunks: {
			...defaultConfig.optimization?.splitChunks,
			cacheGroups: {
				...defaultConfig.optimization?.splitChunks?.cacheGroups,
				// Default @wordpress/scripts config rewrites any chunk containing
				// `style.(s)css` to `<dir>/style-<entry>.css`. That breaks our
				// explicit block entries where block.json references `style.css`
				// directly. Disable the rule — our entries map 1:1 to outputs.
				style: false,
			},
		},
	},
	plugins,
};
