<?php

namespace App\Support;

class NtbKabupatenMap
{
    /** @return array<string, array{lat: float, lng: float}> */
    public static function centroids(): array
    {
        return [
            'Lombok Barat' => ['lat' => -8.6469, 'lng' => 116.1123],
            'Lombok Tengah' => ['lat' => -8.6978, 'lng' => 116.2827],
            'Lombok Timur' => ['lat' => -8.6500, 'lng' => 116.5333],
            'Sumbawa' => ['lat' => -8.4817, 'lng' => 117.4167],
            'Sumbawa Barat' => ['lat' => -8.7333, 'lng' => 116.8500],
            'Dompu' => ['lat' => -8.5369, 'lng' => 118.4631],
            'Bima' => ['lat' => -8.4600, 'lng' => 118.7333],
            'Kota Mataram' => ['lat' => -8.5833, 'lng' => 116.1167],
            'Kota Bima' => ['lat' => -8.4600, 'lng' => 118.7270],
        ];
    }
}
