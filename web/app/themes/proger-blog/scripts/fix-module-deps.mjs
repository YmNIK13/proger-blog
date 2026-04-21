#!/usr/bin/env node
/**
 * Post-build pass: rewrite dependency IDs in *.asset.php files for script
 * modules. DependencyExtractionWebpackPlugin emits classic-script IDs like
 * `wp-interactivity`, but WordPress's Script Modules API expects module IDs
 * (`@wordpress/interactivity`). Run after `wp-scripts build`.
 *
 * Only theme-level script modules are rewritten here. Block view scripts are
 * currently registered as classic viewScript assets because wp-scripts emits
 * classic bundles that depend on window.wp.interactivity.
 *   - build/interactivity-*.asset.php
 */

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const buildRoot = path.resolve(__dirname, '..', 'build');

const MODULE_FILE_PATTERNS = [
	/^build\/interactivity-[^/]+\.asset\.php$/,
];

function walk(dir) {
	const out = [];
	for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
		const full = path.join(dir, entry.name);
		if (entry.isDirectory()) {
			out.push(...walk(full));
		} else if (entry.isFile()) {
			out.push(full);
		}
	}
	return out;
}

const themeRoot = path.resolve(__dirname, '..');
const files = walk(buildRoot);

let rewritten = 0;
for (const file of files) {
	const rel = path.relative(themeRoot, file).replaceAll(path.sep, '/');
	if (!MODULE_FILE_PATTERNS.some((re) => re.test(rel))) continue;

	const original = fs.readFileSync(file, 'utf8');
	const updated = original.replaceAll("'wp-interactivity'", "'@wordpress/interactivity'");
	if (updated !== original) {
		fs.writeFileSync(file, updated);
		rewritten++;
		console.log('Rewrote module deps in:', rel);
	}
}

console.log(`fix-module-deps: ${rewritten} file(s) updated.`);
