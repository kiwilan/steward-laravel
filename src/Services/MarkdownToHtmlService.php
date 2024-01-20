<?php

namespace Kiwilan\Steward\Services;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kiwilan\Steward\Utils\SpatieMedia;

class MarkdownToHtmlService
{
    public function __construct(
        public ?string $type = null,
        public ?DOMDocument $document = null,
        public ?DOMNodeList $image_tags = null,
        public ?array $image_paths = [],
        public ?string $path_file = null,
        public ?string $path_image = null,
        public ?string $html = null,
    ) {
    }

    public static function make(object $md, string $type): MarkdownToHtmlService|false
    {
        $service = new MarkdownToHtmlService();

        $service->type = $type;
        $service->path_file = database_path("seeders/data/{$service->type}/{$md->name}.md");
        $service->path_image = database_path("seeders/media/{$service->type}/{$md->image}.webp");

        if (File::exists($service->path_file)) {
            $content = File::get($service->path_file);

            $config = [
                'APP_NAME' => config('app.name'),
                'APP_URL' => config('app.url'),
                'APP_DOCUMENTATION_URL' => config('bookshelves.documentation_url'),
                'APP_REPOSITORY_URL' => config('bookshelves.repository_url'),
                'ROUTE_DOCS' => config('app.url').'/docs',
                'ROUTE_CATALOG' => route('catalog.index'),
                'ROUTE_OPDS' => route('front.opds', ['version' => '1.2']),
                'ROUTE_WEBREADER' => route('front.webreader'),
            ];

            foreach ($config as $key => $value) {
                $content = str_replace($key, $value, $content);
            }
            $service->html = Str::markdown($content);

            $service->document = new DOMDocument();
            $service->document->loadHTML($service->html);

            $service->image_tags = $service->document->getElementsByTagName('img');

            $image_paths = [];

            foreach ($service->image_tags as $tag) {
                $src = $tag->getAttribute('src');
                $path = str_replace('IMAGE/', '', $src);
                array_push($image_paths, $path);
            }
            $service->image_paths = $image_paths;
        }
        $service->html = self::improveHtml($service->html);

        return $service->html ? $service : false;
    }

    public static function improveHtml(string $html): string
    {
        $html = preg_replace('/<a(.*?)>/', '<a$1 target="_blank" rel="noopener noreferrer">', $html);

        return preg_replace('/<img(.*?)>/', '<img$1 loading="lazy">', $html);
    }

    public function setImages(
        Model $model,
        string $featured_image_name,
        string $inside_images_name,
        string $model_name_attr = 'slug',
        string $model_body_attr = 'body'
    ) {
        $image = null;

        if (File::exists($this->path_image)) {
            $image = base64_encode(File::get($this->path_image));
            SpatieMedia::make($model)
                ->addMediaFromBase64($image)
                ->name($model->{$model_name_attr})
                ->collection('media')
                ->color()
                ->save();
        }

        foreach ($this->image_paths as $name) {
            $path_src = database_path("seeders/media/{$this->type}/{$name}");

            if (File::exists($path_src)) {
                $src = base64_encode(File::get($path_src));
                SpatieMedia::make($model)
                    ->addMediaFromBase64($src)
                    ->name($name)
                    ->collection('media')
                    ->color()
                    ->save();
            }
        }
        // @phpstan-ignore-next-line
        $medias = $model->getMedia($inside_images_name);

        /** @var DOMElement $tag */
        foreach ($this->image_tags as $tag) {
            $src = $tag->getAttribute('src');
            $path = str_replace('IMAGE/', '', $src);

            foreach ($medias as $media) {
                if ($path === $media->name) {
                    $tag->setAttribute('src', $media->getFullUrl());
                }
            }
        }

        $this->html = self::saveHtml($this->html, $this->document);
        // $this->html = $this->minifyOutput($this->html);

        $model->{$model_body_attr} = $this->html;
        $model->save();
    }

    public static function setHeadings(
        Model $model,
        string $model_body_attr = 'body'
    ) {
        try {
            $document = new DOMDocument();
            $document->loadHTML($model->{$model_body_attr});
            $xpath = new DOMXPath($document);

            $elements = $xpath->query('(//h1|//h2|//h3|//h4|//h5)');

            /** @var DOMElement $element */
            foreach ($elements as $index => $element) {
                $id = Str::slug($element->textContent);
                $id = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $id);
                $element->setAttribute('id', $id);
            }
            $html = self::saveHtml($model->{$model_body_attr}, $document);

            return self::improveHtml($html);
        } catch (\Throwable $th) {
            // throw $th;
        }
    }

    public static function saveHtml(?string $html, ?DOMDocument $document): ?string
    {
        $html = $document->saveHTML($document->documentElement);

        if ($document->doctype) {
            $document->removeChild($document->doctype);
        }

        $html = mb_convert_encoding($document->saveHTML($document->documentElement), 'ISO-8859-1', 'UTF-8');
        $html = str_replace('<html><body>', '', $html);
        $html = str_replace('</body></html>', '', $html);
        $html = str_replace('<p><img', '<img', $html);

        return str_replace('"></p>', '">', $html);
    }

    public static function minifyOutput(string $html): string
    {
        return preg_replace(
            ['/ {2,}/', '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'],
            [' ', ''],
            $html
        );
    }
}
