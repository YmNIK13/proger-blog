<?php

declare(strict_types=1);

/**
 * Unit tests for the TOC slug generator.
 *
 * These tests exercise pure string logic and do not boot WordPress.
 */

if (! function_exists('sanitize_title')) {
    function sanitize_title(string $title): string
    {
        $title = mb_strtolower(trim($title), 'UTF-8');
        $map = [
            'а'=>'a','б'=>'b','в'=>'v','г'=>'h','ґ'=>'g','д'=>'d','е'=>'e','є'=>'ie','ж'=>'zh','з'=>'z',
            'и'=>'y','і'=>'i','ї'=>'i','й'=>'i','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p',
            'р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'kh','ц'=>'ts','ч'=>'ch','ш'=>'sh','щ'=>'shch',
            'ь'=>'','ю'=>'iu','я'=>'ia','ё'=>'e','ъ'=>'','ы'=>'y','э'=>'e',
        ];
        $title = strtr($title, $map);
        $title = preg_replace('/[^a-z0-9]+/u', '-', $title) ?? '';
        return trim($title, '-');
    }
}

if (! function_exists('wp_strip_all_tags')) {
    function wp_strip_all_tags(string $string): string
    {
        return trim(strip_tags($string));
    }
}

require_once __DIR__ . '/../../web/app/themes/proger-blog/inc/toc-anchors.php';

test('generate_slug produces ASCII slug from Latin text', function () {
    $used = [];
    $slug = ProgerBlog\Toc\generate_slug('Hello World', $used);
    expect($slug)->toBe('hello-world');
});

test('generate_slug transliterates Cyrillic', function () {
    $used = [];
    $slug = ProgerBlog\Toc\generate_slug('Приклад коду', $used);
    expect($slug)->toBe('pryklad-kodu');
});

test('generate_slug disambiguates duplicate text', function () {
    $used = [];
    $first = ProgerBlog\Toc\generate_slug('Приклад', $used);
    $second = ProgerBlog\Toc\generate_slug('Приклад', $used);
    $third = ProgerBlog\Toc\generate_slug('Приклад', $used);

    expect($first)->toBe('pryklad');
    expect($second)->toBe('pryklad-2');
    expect($third)->toBe('pryklad-3');
});

test('generate_slug falls back to "section" for empty input', function () {
    $used = [];
    $slug = ProgerBlog\Toc\generate_slug('   ', $used);
    expect($slug)->toBe('section');
});

test('extract_headings returns level/text/slug for nested h2/h3', function () {
    $html = <<<HTML
<h2>Огляд</h2>
<p>intro</p>
<h3>Вступ</h3>
<h3>Деталі</h3>
<h2>Висновок</h2>
HTML;

    $headings = ProgerBlog\Toc\extract_headings($html, 4);
    expect($headings)->toHaveCount(4);
    expect($headings[0]['level'])->toBe(2);
    expect($headings[0]['slug'])->toBe('ohliad');
    expect($headings[1]['slug'])->toBe('vstup');
    expect($headings[2]['slug'])->toBe('detali');
    expect($headings[3]['slug'])->toBe('vysnovok');
});

test('extract_headings respects max_level', function () {
    $html = '<h2>Two</h2><h3>Three</h3><h4>Four</h4><h5>Five</h5>';
    $headings = ProgerBlog\Toc\extract_headings($html, 3);
    $levels = array_column($headings, 'level');
    expect($levels)->toBe([2, 3]);
});
