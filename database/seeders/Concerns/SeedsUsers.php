<?php

namespace Database\Seeders\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use LogicException;

trait SeedsUsers
{
    protected function seedUser(array $attributes): User
    {
        $user = $this->findSeededUser($attributes);
        $exists = $user->exists;
        $attributes = $this->prepareUserAttributes($attributes);

        if ($exists) {
            unset($attributes['password'], $attributes['is_password_changed']);
        }

        $user->fill($attributes);
        $user->save();

        return $user;
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
            $password = env('PHU_SEED_PASSWORD');

            if (app()->environment('production') && blank($password)) {
                throw new LogicException('PHU_SEED_PASSWORD wajib diisi sebelum menjalankan seeder pengguna di production.');
            }

            $attributes['password'] = Hash::make($password ?: 'password123');
        }

        $attributes['is_password_changed'] = $attributes['is_password_changed'] ?? false;

        return $attributes;
    }

    private function findSeededUser(array $attributes): User
    {
        $role = $attributes['role'] ?? null;

        if (in_array($role, ['kabupaten', 'pengawas'], true) && filled($attributes['kabupaten'] ?? null)) {
            return User::query()
                ->where('role', $role)
                ->where('kabupaten', $attributes['kabupaten'])
                ->first() ?? new User();
        }

        if (in_array($role, ['admin', 'pimpinan'], true)) {
            return User::query()
                ->where('role', $role)
                ->first() ?? new User();
        }

        if ($role === 'user' && filled($attributes['travel_id'] ?? null)) {
            return User::query()
                ->where('role', 'user')
                ->where('travel_id', $attributes['travel_id'])
                ->first() ?? new User();
        }

        return User::query()->firstOrNew(['email' => $attributes['email']]);
    }
}
