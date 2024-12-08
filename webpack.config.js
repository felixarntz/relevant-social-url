/**
 * WordPress dependencies
 */
const config = require( '@wordpress/scripts/config/webpack.config' );

const { sync: glob } = require( 'fast-glob' );
const { getWebpackEntryPoints } = require( '@wordpress/scripts/utils' );

/**
 * Gets the entry points for the webpack configuration.
 *
 * This function extends the original entry points from the relevant '@wordpress/scripts' function with any index files
 * in one level deep directories in src.
 *
 * @return {Function} A function returning the entry points object.
 */
function getEntryPoints() {
	const entryPoints = getWebpackEntryPoints();

	const [ entryFile ] = glob(
		`${ process.env.WP_SRC_DIRECTORY }/index.[jt]s?(x)`,
		{
			absolute: true,
		}
	);
	if ( entryFile ) {
		entryPoints.index = entryFile;
	}

	return entryPoints;
}

module.exports = {
	...config,
	entry: getEntryPoints,
};
