<?php

namespace Database\Factories;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdFactory extends Factory
{
    protected $model = Ad::class;

    public function definition(): array
    {
        $positions = ['sidebar', 'header', 'footer', 'content'];
        $hasImage = fake()->boolean(70); // 70% 概率有图片
        $startAt = fake()->boolean(80) ? fake()->dateTimeBetween('-30 days', '+30 days') : null;
        $endAt = $startAt ? fake()->dateTimeBetween($startAt, '+60 days') : null;

        return [
            'name' => fake()->words(3, true),
            'position' => fake()->randomElement($positions),
            'content' => $hasImage ? null : fake()->paragraphs(2, true),
            'url' => fake()->boolean(80) ? fake()->url() : null,
            'image_url' => $hasImage ? 'ads/' . fake()->numberBetween(1, 5) . '.jpg' : null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'is_active' => fake()->boolean(80), // 80% 概率激活
            'view_count' => fake()->numberBetween(0, 10000),
            'click_count' => function (array $attributes) {
                // 点击数不应该大于查看数
                return fake()->numberBetween(0, min($attributes['view_count'], 1000));
            },
        ];
    }

    /**
     * 当前有效的广告
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'start_at' => fake()->dateTimeBetween('-10 days', 'now'),
            'end_at' => fake()->dateTimeBetween('now', '+30 days'),
        ]);
    }

    /**
     * 侧边栏广告
     */
    public function sidebar(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'sidebar',
        ]);
    }

    /**
     * 顶部广告
     */
    public function header(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'header',
        ]);
    }

    /**
     * 底部广告
     */
    public function footer(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'footer',
        ]);
    }

    /**
     * 内容区域广告
     */
    public function content(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 'content',
        ]);
    }

    /**
     * 图片广告
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_url' => 'ads/' . fake()->numberBetween(1, 5) . '.jpg',
            'content' => null,
        ]);
    }

    /**
     * 文字广告
     */
    public function withText(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_url' => null,
            'content' => fake()->paragraphs(2, true),
        ]);
    }
}