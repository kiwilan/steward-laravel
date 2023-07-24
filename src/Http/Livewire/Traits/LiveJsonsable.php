<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Kiwilan\Steward\Services\Datayable\DatayableItem;

/**
 * `Livewire\Component` trait to use JSON property.
 */
trait LiveJsonsable
{
    use LiveValidator;

    public array $jsonsableJson = [];

    public string $keyName = 'name';

    public string $valueName = 'value';

    protected $rules = [
        'jsonsableJson.*' => 'nullable|string',
    ];

    protected $messages = [
        'jsonsableJson.*.string' => 'Username must be a string.',
    ];

    protected $validationAttributes = [
        'jsonsableJson.*' => 'jsonsableJson.*',
    ];

    /** @var DatayableItem[] */
    private array $jsonsableData = [];

    private ?string $modelField = null;

    /**
     * @param  DatayableItem[]  $data
     */
    public function makeJsonsable(array $data, array $json)
    {
        $this->jsonsableData = $data;

        foreach ($this->jsonsableData as $item) {
            $current = null;

            if (array_key_exists($item->name, $json)) {
                $current = $json[$item->name];
            }
            $this->jsonsableJson[$item->name] = $current;
        }
    }

    public function validateJson(): array
    {
        $validate = $this->validator();

        $data = [];

        foreach ($validate['jsonsableJson'] as $key => $value) {
            $data[] = [
                'name' => $key,
                'value' => $value,
            ];
        }

        return $data;
    }
}
