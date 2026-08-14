/**
 * WordPress dependencies
 */
import { registerPlugin } from '@wordpress/plugins';
import {
	PluginDocumentSettingPanel as EditorPluginDocumentSettingPanel,
	store as editorStore,
} from '@wordpress/editor';
import { PluginDocumentSettingPanel as EditPostPluginDocumentSettingPanel } from '@wordpress/edit-post';
import { TextControl } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/*
 * Prefer the editor package export (current WordPress). Fall back to edit-post
 * for WordPress 6.1–6.5, where PluginDocumentSettingPanel was only available
 * on wp.editPost.
 */
const PluginDocumentSettingPanel =
	EditorPluginDocumentSettingPanel || EditPostPluginDocumentSettingPanel;

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
				label={ __( 'Relevant Social URL', 'relevant-social-url' ) }
				help={ __(
					'The URL of a social media post that is associated with this content.',
					'relevant-social-url'
				) }
				type="url"
				value={ socialUrl }
				onChange={ onChange }
				__nextHasNoMarginBottom
				__next40pxDefaultSize
			/>
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'relevant-social-url', {
	render: RelevantSocialURLSettingPanel,
	icon: 'twitter',
} );
