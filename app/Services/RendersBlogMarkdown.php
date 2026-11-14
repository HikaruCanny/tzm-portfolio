<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Str;

class RendersBlogMarkdown
{
    /**
     * @return array{html: string, toc: list<array{id: string, text: string, level: int}>}
     */
    public function render(string $markdown): array
    {
        $html = (string) Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        if (trim($html) === '') {
            return ['html' => '', 'toc' => []];
        }

        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8"><body>'.$html.'</body>', LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($document);
        $toc = [];
        $used = [];
        $headings = $xpath->query('//h2|//h3');

        if ($headings !== false) {
            foreach ($headings as $heading) {
                if (! $heading instanceof DOMElement) {
                    continue;
                }

                $text = trim($heading->textContent ?? '');
                $base = Str::slug($text);
                $base = $base === '' ? 'section' : $base;
                $id = $base;
                $suffix = 2;

                while (isset($used[$id])) {
                    $id = $base.'-'.$suffix;
                    $suffix++;
                }

                $used[$id] = true;
                $heading->setAttribute('id', $id);
                $toc[] = [
                    'id' => $id,
                    'text' => $text,
                    'level' => (int) substr($heading->nodeName, 1),
                ];
            }
        }

        $links = $xpath->query('//a');

        if ($links !== false) {
            foreach ($links as $link) {
                if (! $link instanceof DOMElement) {
                    continue;
                }

                $href = $link->getAttribute('href');

                if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
                    $link->setAttribute('target', '_blank');
                    $link->setAttribute('rel', 'noopener noreferrer');
                }
            }
        }

        $body = $document->getElementsByTagName('body')->item(0);
        $output = '';

        if ($body !== null) {
            foreach ($body->childNodes as $child) {
                $output .= $document->saveHTML($child);
            }
        }

        return ['html' => $output, 'toc' => $toc];
    }
}
