/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

/**
 * Opens the post document settings sidebar and ensures the Post tab is active.
 *
 * @param {Object}                          opts
 * @param {import('@playwright/test').Page} opts.page
 * @param {Object}                          opts.editor
 * @return {import('@playwright/test').Locator} Editor settings region locator.
 */
async function openPostDocumentSettings( { page, editor } ) {
	// Wait for the editor chrome to finish mounting before interacting with it.
	const settingsToggle = page
		.getByRole( 'region', { name: 'Editor top bar' } )
		.getByRole( 'button', { name: 'Settings', exact: true } );
	await settingsToggle.waitFor();

	const editorSettings = page.getByRole( 'region', {
		name: 'Editor settings',
	} );

	// The sidebar is open by default on a fresh install, but may be closed
	// after a reload depending on persisted UI state.
	if ( ! ( await editorSettings.isVisible() ) ) {
		await editor.openDocumentSettingsSidebar();
		await expect( editorSettings ).toBeVisible();
	}

	const postTab = editorSettings.getByRole( 'tab', { name: 'Post' } );
	if ( await postTab.isVisible() ) {
		await postTab.click();
	}

	return editorSettings;
}

/**
 * Expands the Relevant Social URL panel if collapsed and returns the URL input.
 *
 * @param {import('@playwright/test').Locator} editorSettings
 * @return {import('@playwright/test').Locator} URL textbox locator.
 */
async function getRelevantSocialUrlInput( editorSettings ) {
	const panelButton = editorSettings.getByRole( 'button', {
		name: 'Relevant Social URL',
	} );
	await expect( panelButton ).toBeVisible();

	const isExpanded =
		( await panelButton.getAttribute( 'aria-expanded' ) ) === 'true';
	if ( ! isExpanded ) {
		await panelButton.click();
	}

	const urlInput = editorSettings.getByRole( 'textbox', {
		name: 'Relevant Social URL',
	} );
	await expect( urlInput ).toBeVisible();
	return urlInput;
}

test.describe( 'Relevant Social URL field', () => {
	test.beforeAll( async ( { requestUtils } ) => {
		// Persist preferences before any editor page load so the first-run
		// welcome guide never appears and cannot block the editor chrome.
		await requestUtils.setPreferences( 'core/edit-post', {
			welcomeGuide: false,
			fullscreenMode: false,
		} );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await requestUtils.deleteAllPosts();
	} );

	test( 'Saving an X.com URL on a post persists the value in the field', async ( {
		page,
		admin,
		editor,
	} ) => {
		const socialUrl = 'https://x.com/WordPress/status/1234567890';

		// A title is required for the post to be saveable; meta alone is not enough.
		await admin.createNewPost( {
			title: 'Relevant Social URL E2E Test',
		} );

		const editorSettings = await openPostDocumentSettings( {
			page,
			editor,
		} );
		const urlInput = await getRelevantSocialUrlInput( editorSettings );
		await urlInput.fill( socialUrl );

		await editor.saveDraft();

		// Reload the editor and verify the value was persisted.
		await page.reload();

		const editorSettingsAfterReload = await openPostDocumentSettings( {
			page,
			editor,
		} );
		const urlInputAfterReload = await getRelevantSocialUrlInput(
			editorSettingsAfterReload
		);
		await expect( urlInputAfterReload ).toHaveValue( socialUrl );
	} );
} );
