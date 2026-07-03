<?php

namespace Database\Seeders\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait SeedsUsers
{
    protected function seedUser(array $attributes): User
    {
        $attributes = $this->prepareUserAttributes($attributes);

        return User::updateOrCreate(
            ['email' => $attributes['email']],
            $attributes
        );
    }

    protected function prepareUserAttributes(array $attributes): array
    {
        if (empty($attributes['nama'])) {
            $attributes['nama'] = trim(
                ($attributes['firstname'] ?? '').' '.($attributes['lastname'] ?? '')
            ) ?: ($attributes['username'] ?? 'User PANTAU');
        }

        unset($attributes['username'], $attributes['firstname'], $attributes['lastname'], $attributes['slug']);

        if (! isset($attributes['password'])) {
            $attributes['password'] = Hash::make('password123');
        }

        $attributes['is_password_changed'] = $attributes['is_password_changed'] ?? false;

        return $attributes;
    }
}
