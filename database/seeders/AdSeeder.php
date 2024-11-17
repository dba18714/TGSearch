<?php

namespace Database\Seeders;

use App\Models\Ad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;

class AdSeeder extends Seeder
{
    public function run(): void
    {
        // 准备测试图片
        $this->prepareTestImages();

        // 创建当前有效的广告
        // 侧边栏广告
        Ad::factory()
            ->count(3)
            ->active()
            ->sidebar()
            ->withImage()
            ->create();

        Ad::factory()
            ->count(2)
            ->active()
            ->sidebar()
            ->withText()
            ->create();

        // 顶部广告
        Ad::factory()
            ->count(2)
            ->active()
            ->header()
            ->withImage()
            ->create();

        // 底部广告
        Ad::factory()
            ->count(2)
            ->active()
            ->footer()
            ->withImage()
            ->create();

        // 内容区域广告
        Ad::factory()
            ->count(3)
            ->active()
            ->content()
            ->withImage()
            ->create();

        Ad::factory()
            ->count(2)
            ->active()
            ->content()
            ->withText()
            ->create();

        // 创建一些过期或未来的广告
        Ad::factory()
            ->count(10)
            ->create();
    }

    private function prepareTestImages(): void
    {
        $this->command->info('准备广告测试图片...');

        // 确保目录存在
        Storage::disk('public')->makeDirectory('ads');

        // 创建图像管理器实例
        $manager = new ImageManager(new Driver());

        // 生成不同尺寸的测试图片
        $sizes = [
            '1.jpg' => '800x400',  // 适合顶部横幅
            '2.jpg' => '600x300',  // 适合内容区域
            '3.jpg' => '400x400',  // 适合方形广告
            '4.jpg' => '300x600',  // 适合侧边栏
            '5.jpg' => '1200x300', // 适合大横幅
        ];

        foreach ($sizes as $filename => $size) {
            // 检查文件是否已存在
            if (Storage::disk('public')->exists("ads/$filename")) {
                $this->command->info("已存在: ads/$filename ($size)");
                continue;
            }

            [$width, $height] = explode('x', $size);
            
            // 创建图片
            $image = $manager->create($width, $height);
            
            // 生成随机背景色
            $backgroundColor = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            
            // 填充背景色
            $image->fill($backgroundColor);
            
            // 添加文本
            $fontSize = min($width, $height) / 10;
            $image->text(
                "AD {$width}x{$height}", 
                (int)($width / 2),  // x 坐标
                (int)($height / 2), // y 坐标
                function ($font) use ($fontSize) {
                    $font->color('#FFFFFF');
                    $font->size($fontSize);
                    $font->align('center');
                    $font->valign('middle');
                }
            );
            
            // 保存图片
            $encoded = $image->encode(new JpegEncoder(90));
            Storage::disk('public')->put("ads/$filename", $encoded->toString());
            
            $this->command->info("已生成: ads/$filename ($size)");
        }

        $this->command->info('图片准备完成！');
    }
}
