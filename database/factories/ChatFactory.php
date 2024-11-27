<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    protected $model = Chat::class;
    
    public function definition(): array
    {
        $type = $this->faker->randomElement(['bot', 'channel', 'group', 'person']);
        $source = $this->faker->randomElement(['manual', 'crawler']);
        $name = $this->getNameByType($type);
        $username = strtolower(str_replace(' ', '_', $name));

        return [
            'name' => $name,
            'introduction' => $this->faker->sentence(),
            'type' => $type,
            'username' => "@{$username}",
            'member_count' => $this->faker->numberBetween(100, 100000),
            // 'view_count' => $this->faker->numberBetween(1000, 1000000),
            'source' => $source,
            'user_id' => User::factory(),
            'is_valid' => $this->faker->boolean(90), // 90% 概率是有效的
            'verified_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'verified_start_at' => $this->faker->dateTimeBetween('-2 years', '-1 year'),
        ];
    }

    private function getNameByType(string $type): string
    {
        return match ($type) {
            'bot' => $this->faker->name() . 'Bot',
            'channel' => $this->faker->words(3, true) . ' Channel',
            'group' => $this->faker->words(2, true) . ' Group',
            'person' => $this->faker->name(),
            default => $this->faker->name(),
        };
    }

    public function bot(): self
    {
        return $this->state(fn (array $attributes) => ['type' => 'bot']);
    }

    public function channel(): self
    {
        return $this->state(fn (array $attributes) => ['type' => 'channel']);
    }

    public function group(): self
    {
        return $this->state(fn (array $attributes) => ['type' => 'group']);
    }

    public function person(): self
    {
        return $this->state(fn (array $attributes) => ['type' => 'person']);
    }
}