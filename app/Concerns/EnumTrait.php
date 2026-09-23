<?php

namespace App\Concerns;

trait EnumTrait
{
    /**
     * Resolve a single requested selection item into a raw enum value.
     *
     * Accepts either a raw value, a bensampo enum instance, or an enum KEY
     * (e.g. "ADMIN"). Returns null when the item is not part of the enum, so
     * unknown selections are simply skipped instead of throwing.
     *
     * @param  mixed  $item  Raw value, enum instance, or enum key.
     * @return mixed|null
     */
    protected static function resolveEnumSelection(mixed $item): mixed
    {
        if ($item instanceof static) {
            return $item->value;
        }

        if (is_string($item) && static::hasKey($item)) {
            return static::getValue($item);
        }

        return static::hasValue($item) ? $item : null;
    }

    /**
     * Get options for select inputs: [value => description].
     *
     * Built on top of bensampo's asSelectArray(). When $value is provided
     * (a single value/key/enum instance, or an array of them) only the
     * matching options are returned, keeping the requested order.
     */
    public static function getOptions(mixed $value = null): array
    {
        $all = static::asSelectArray(); // [value => description]

        if ($value === null || $value === []) {
            return $all;
        }

        $requested = is_array($value) ? $value : [$value];
        $options = [];

        foreach ($requested as $item) {
            $resolved = static::resolveEnumSelection($item);

            if ($resolved !== null && array_key_exists($resolved, $all)) {
                $options[$resolved] = $all[$resolved];
            }
        }

        return $options;
    }

    /**
     * Get API format: [['id' => value, 'name' => description], ...].
     *
     * Same selection rules as getOptions(): values, keys, or enum instances.
     */
    public static function getApi(mixed $value = null): array
    {
        $options = static::getOptions($value);

        return array_map(
            fn (string $desc, mixed $val): array => ['id' => $val, 'name' => $desc],
            $options,
            array_keys($options),
        );
    }
}
