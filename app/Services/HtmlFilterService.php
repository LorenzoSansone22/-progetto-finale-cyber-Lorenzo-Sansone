<?php

namespace App\Services;

class HtmlFilterService
{
    public function filter(string $html): string
    {
       
        $clean = strip_tags($html, '<p><br><b><strong><i><em><ul><ol><li><a><h1><h2><h3>');

        
        $clean = preg_replace('/\s*on\w+\s*=\s*("|\')[^"\']*\1/i', '', $clean) ?? $clean;

        return $clean;
    }
}