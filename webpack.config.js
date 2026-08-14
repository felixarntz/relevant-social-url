/**
 * External dependencies
 */
const path = require( 'path' );

/**
 * WordPress dependencies
 */
const config = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...config,
	entry: {
		index: path.resolve( process.cwd(), 'src/index.js' ),
		'react-jsx-runtime-polyfill': path.resolve(
			process.cwd(),
			'src/react-jsx-runtime-polyfill.js'
		),
	},
};
