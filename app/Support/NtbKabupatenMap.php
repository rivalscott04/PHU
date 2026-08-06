<?php

namespace App\Support;

class NtbKabupatenMap
{
    /** @var array<string, list<string>> */
    private static array $aliases = [
        'Kota Mataram' => ['Mataram'],
        'Kota Bima' => ['Bima'],
    ];

    /** @return list<string> */
    public static function names(): array
    {
        return array_keys(self::centroids());
    }

    public static function normalize(?string $name): ?string
    {
        if ($name === null || trim($name) === '') {
            return null;
        }

        $trimmed = trim($name);

        foreach (self::names() as $canonical) {
            if (strcasecmp($canonical, $trimmed) === 0) {
                return $canonical;
            }
        }

        foreach (self::$aliases as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                if (strcasecmp($alias, $trimmed) === 0) {
                    return $canonical;
                }
            }
        }

        return $trimmed;
    }

    public static function matches(?string $a, ?string $b): bool
    {
        $left = self::normalize($a);
        $right = self::normalize($b);

        return $left !== null && $right !== null && $left === $right;
    }

    /** @return list<string> */
    public static function queryValues(?string $kabupaten): array
    {
        $normalized = self::normalize($kabupaten);

        if ($normalized === null) {
            return [];
        }

        $values = [$normalized];

        foreach (self::$aliases as $canonical => $aliases) {
            if ($canonical === $normalized) {
                return array_values(array_unique(array_merge($values, $aliases)));
            }
        }

        return $values;
    }

    /** @param  list<string>  $kabupatens
     * @return list<string>
     */
    public static function expandKabupatenList(array $kabupatens): array
    {
        return collect($kabupatens)
            ->flatMap(fn (string $kabupaten) => self::queryValues($kabupaten))
            ->unique()
            ->values()
            ->all();
    }

    /** @return array<string, array{lat: float, lng: float}> */
    public static function centroids(): array
    {
        return [
            'Lombok Barat' => ['lat' => -8.6781, 'lng' => 116.1319],
            'Lombok Tengah' => ['lat' => -8.7050, 'lng' => 116.2747],
            'Lombok Timur' => ['lat' => -8.6517, 'lng' => 116.5367],
            'Sumbawa' => ['lat' => -8.4931, 'lng' => 117.4203],
            'Sumbawa Barat' => ['lat' => -8.7444, 'lng' => 116.8522],
            'Dompu' => ['lat' => -8.5369, 'lng' => 118.4631],
            'Bima' => ['lat' => -8.5270, 'lng' => 118.7370],
            'Kota Mataram' => ['lat' => -8.5833, 'lng' => 116.1167],
            'Kota Bima' => ['lat' => -8.4606, 'lng' => 118.7274],
        ];
    }
}
