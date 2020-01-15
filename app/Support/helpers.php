<?php

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use PHPHtmlParser\Dom;
use Stichoza\GoogleTranslate\GoogleTranslate;

if (!function_exists('add_text_water')) {
    /**
     * 给图片添加文字水印
     *
     * @param $file
     * @param $text
     * @param string $color
     *
     * @return mixed
     */
    function add_text_water($file, $text, $color = '#0B94C1')
    {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if ($extension !== 'gif') {
            $image = Image::make($file);
            $image->text($text, $image->width() - 20, $image->height() - 30, function ($font) use ($color) {
                $font->file(public_path('fonts/msyh.ttf'));
                $font->size(15);
                $font->color($color);
                $font->align('right');
                $font->valign('bottom');
            });
            $image->save($file);
        }
    }
}

if (!function_exists('generate_english_slug')) {
    /**
     * Generate English slug
     *
     * @param $content
     *
     * @throws ErrorException
     *
     * @return string
     */
    function generate_english_slug($content)
    {
        $locale = config('app.locale');

        if ('en' !== $locale) {
            $googleTranslate = new GoogleTranslate();
            $content         =  $googleTranslate->setUrl('http://translate.google.cn/translate_a/single')
                ->setSource($locale)
                ->translate($content);
        }

        return Str::slug($content);
    }
}

if (!function_exists('cdn_url')) {
    /**
     * Generate a url for the CDN.
     *
     * @param $path
     *
     * @return string
     */
    function cdn_url($path)
    {
        return URL::assetFrom(config('bjyblog.cdn_domain'), $path);
    }
}

if (!function_exists('column_in_database_table')) {
    /**
     * Is there a column in the database table?
     *
     * @param $table
     * @param mixed $column
     *
     * @return bool
     */
    function column_in_database_table($table, $column)
    {
        $tablePrefix = DB::getTablePrefix();
        $columns     = collect(DB::select("SHOW COLUMNS FROM {$tablePrefix}{$table}"))->pluck('Field');

        return $columns->contains($column);
    }
}

if (!function_exists('format_url')) {
    /**
     * Format URL
     *
     * @param $url
     *
     * @return string
     */
    function format_url($url)
    {
        if (preg_match('/^http(s)?:\/\//', $url) === 0) {
            $url = 'http://' . $url;
        }

        return strtolower(rtrim($url, '/'));
    }
}

if (!function_exists('mail_is_configured')) {
    /**
     * Check mail config
     */
    function mail_is_configured()
    {
        $mailConfig = [
            config('mail.driver'),
            config('mail.encryption'),
            config('mail.port'),
            config('mail.host'),
            config('mail.username'),
            config('mail.password'),
            config('mail.from.address'),
            config('mail.from.name'),
        ];

        return count(array_filter($mailConfig)) === 8;
    }
}

if (!function_exists('get_image_paths_from_html')) {
    /**
     * @param $html
     *
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     *
     * @return array
     */
    function get_image_paths_from_html($html)
    {
        $dom = new Dom();
        $dom->loadStr($html);
        /** @var \PHPHtmlParser\Dom\HtmlNode[] $image_tags */
        $image_tags  = $dom->find('img');
        $image_paths = [];

        foreach ($image_tags as $image_tag) {
            $image_paths[] = $image_tag->getAttribute('src');
        }

        return $image_paths;
    }
}
