<?php

namespace App\Support;

class NtbKabupatenMap
{
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
