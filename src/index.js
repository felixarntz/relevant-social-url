/**
 * WordPress dependencies
 */
import { registerPlugin } from '@wordpress/plugins';
import {
	PluginDocumentSettingPanel,
	store as editorStore,
} from '@wordpress/editor';
import { TextControl } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Renders the plugin's editor sidebar panel.
 *
 * @return {Element} Component.
 */
function RelevantTweetSettingPanel() {
	const tweetUrl = useSelect( ( select ) => {
		const meta = select( editorStore ).getEditedPostAttribute( 'meta' );
		return meta.reltwe_url || '';
	} );

	const { editPost } = useDispatch( editorStore );
	const onChange = ( value ) => editPost( { meta: { reltwe_url: value } } );

	return (
		<PluginDocumentSettingPanel
			name="relevant-tweet"
			title={ __( 'Relevant Tweet', 'relevant-tweet' ) }
		>
			<TextControl
				label={ __( 'Relevant Tweet URL', 'relevant-tweet' ) }
				help={ __(
					'The URL of a tweet that is associated with this content.',
					'relevant-tweet'
				) }
				type="url"
				value={ tweetUrl }
				onChange={ onChange }
				__nextHasNoMarginBottom
			/>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'relevant-tweet', {
	render: RelevantTweetSettingPanel,
	icon: 'twitter',
} );
