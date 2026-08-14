/**
 * External dependencies
 */
// WordPress provides `react` as a script handle; it is not a package dependency.
// eslint-disable-next-line import/no-extraneous-dependencies
import React from 'react';

/*
 * Minimal react-jsx-runtime polyfill for WordPress versions that do not
 * register the script handle (pre-6.6). Uses React.createElement so modern
 * automatic-JSX builds can load on those versions.
 *
 * Only registered from PHP when core has not registered `react-jsx-runtime`.
 */
if ( ! window.ReactJSXRuntime ) {
	function jsx( type, props, key ) {
		props = props || {};
		if ( key !== undefined && key !== null ) {
			props = Object.assign( {}, props, { key } );
		}
		return React.createElement( type, props );
	}

	window.ReactJSXRuntime = {
		jsx,
		jsxs: jsx,
		Fragment: React.Fragment,
	};
}
