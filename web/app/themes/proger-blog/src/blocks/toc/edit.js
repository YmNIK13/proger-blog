import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
	const { maxLevel, title } = attributes;
	const blockProps = useBlockProps({ className: 'toc toc--editor-preview' });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('TOC settings', 'proger-blog')}>
					<TextControl
						label={__('Title', 'proger-blog')}
						value={title}
						onChange={(value) => setAttributes({ title: value })}
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<SelectControl
						label={__('Max heading level', 'proger-blog')}
						value={String(maxLevel)}
						options={[
							{ label: 'H2', value: '2' },
							{ label: 'H3', value: '3' },
							{ label: 'H4', value: '4' },
						]}
						onChange={(value) => setAttributes({ maxLevel: Number(value) })}
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
				</PanelBody>
			</InspectorControls>

			<nav {...blockProps} aria-label={title}>
				<details open>
					<summary>{title}</summary>
					<p style={{ color: 'var(--wp--preset--color--text-secondary)' }}>
						{__('Автогенерується з заголовків статті на фронтенді.', 'proger-blog')}
					</p>
				</details>
			</nav>
		</>
	);
}
