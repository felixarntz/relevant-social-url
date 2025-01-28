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
function RelevantSocialURLSettingPanel() {
	const socialUrl = useSelect( ( select ) => {
		const meta = select( editorStore ).getEditedPostAttribute( 'meta' );
		return meta.relsoc_url || '';
	} );

	const { editPost } = useDispatch( editorStore );
	const onChange = ( value ) => editPost( { meta: { relsoc_url: value } } );

	return (
		<PluginDocumentSettingPanel
			name="relevant-social-url"
			title={ __( 'Relevant Social URL', 'relevant-social-url' ) }
		>
			<TextControl
				label={ __( 'Relevant Social URL URL', 'relevant-social-url' ) }
				help={ __(
					'The URL of a social media post that is associated with this content.',
					'relevant-social-url'
				) }
				type="url"
				value={ socialUrl }
				onChange={ onChange }
				__nextHasNoMarginBottom
			/>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'relevant-social-url', {
	render: RelevantSocialURLSettingPanel,
	icon: 'twitter',
} );
