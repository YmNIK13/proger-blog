import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components';

const LANGUAGES = [
	{ label: 'Plain text', value: 'plaintext' },
	{ label: 'JavaScript', value: 'javascript' },
	{ label: 'TypeScript', value: 'typescript' },
	{ label: 'PHP', value: 'php' },
	{ label: 'Bash', value: 'bash' },
	{ label: 'JSON', value: 'json' },
	{ label: 'CSS', value: 'css' },
	{ label: 'SCSS', value: 'scss' },
	{ label: 'HTML', value: 'html' },
	{ label: 'SQL', value: 'sql' },
	{ label: 'YAML', value: 'yaml' },
	{ label: 'Diff', value: 'diff' },
	{ label: 'Markdown', value: 'markdown' },
];

export default function Edit({ attributes, setAttributes }) {
	const { content, language, filename, showLineNumbers } = attributes;
	const blockProps = useBlockProps({ className: `proger-code proger-code--lang-${language}` });

	return (
		<>
			<InspectorControls>
				<PanelBody title={__('Code settings', 'proger-blog')}>
					<SelectControl
						label={__('Language', 'proger-blog')}
						value={language}
						options={LANGUAGES}
						onChange={(value) => setAttributes({ language: value })}
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<TextControl
						label={__('Filename (optional)', 'proger-blog')}
						value={filename}
						onChange={(value) => setAttributes({ filename: value })}
						__nextHasNoMarginBottom
						__next40pxDefaultSize
					/>
					<ToggleControl
						label={__('Show line numbers', 'proger-blog')}
						checked={showLineNumbers}
						onChange={(value) => setAttributes({ showLineNumbers: value })}
						__nextHasNoMarginBottom
					/>
				</PanelBody>
			</InspectorControls>

			<figure {...blockProps}>
				<header className="proger-code__header">
					<span className="proger-code__lang">{language.toUpperCase()}</span>
					{filename && <span className="proger-code__filename">{filename}</span>}
				</header>
				<TextareaControl
					className="proger-code__editor"
					value={content}
					onChange={(value) => setAttributes({ content: value })}
					rows={10}
					spellCheck={false}
					__nextHasNoMarginBottom
				/>
			</figure>
		</>
	);
}
