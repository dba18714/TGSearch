<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chat_id' => Chat::factory(),
            'source_id' => fake()->unique()->numberBetween(1000, 999999),
            'text' => fake()->paragraph(),
            'view_count' => fake()->numberBetween(0, 10000),
            'source' => fake()->randomElement(['crawler', 'manual']),
            'user_id' => fake()->randomElement([null, User::factory()]),
            'is_valid' => fake()->boolean(),
            'verified_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'verified_start_at' => fake()->optional()->dateTimeBetween('-2 months', '-1 month'),
        ];
    }

    /**
     * 标记为已验证的消息
     */
    public function verified()
    {
        return $this->state(fn (array $attributes) => [
            'is_valid' => true,
            'verified_at' => now(),
            'verified_start_at' => now()->subHour(),
        ]);
    }

    /**
     * 标记为爬虫添加的消息
     */
    public function crawler()
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'crawler',
            'user_id' => null,
        ]);
    }

    /**
     * 标记为手动添加的消息
     */
    public function manual()
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'manual',
            'user_id' => User::factory(),
        ]);
    }
}