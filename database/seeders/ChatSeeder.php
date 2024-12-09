<?php

namespace Database\Seeders;

use App\Models\Chat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Uid\Ulid;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 关闭一些检查来提升性能
        DB::disableQueryLog();
        $this->command->info('Disabled query log');
        
        // 2. 分批插入数据
        $batchSize = 300; // 增加批量大小以提高性能
        $totalRecords = 200 * 10000; // 总记录数
        $batches = ceil($totalRecords / $batchSize); // 计算需要多少批次
        
        $this->command->info("Starting to seed {$totalRecords} records in {$batches} batches");
        
        // 3. 使用事务来提升性能
        DB::beginTransaction();
        
        try {
            for ($i = 0; $i < $batches; $i++) {
                $records = Chat::factory()
                    ->count($batchSize)
                    ->make()
                    ->map(function ($model) {
                        $attributes = $model->attributesToArray();
                        $attributes['id'] = Ulid::generate();
                        
                        // 处理日期时间格式
                        if (isset($attributes['verified_at'])) {
                            $attributes['verified_at'] = $attributes['verified_at'] 
                                ? Carbon::parse($attributes['verified_at'])->format('Y-m-d H:i:s')
                                : null;
                        }
                        if (isset($attributes['verified_start_at'])) {
                            $attributes['verified_start_at'] = $attributes['verified_start_at']
                                ? Carbon::parse($attributes['verified_start_at'])->format('Y-m-d H:i:s')
                                : null;
                        }
                        
                        return $attributes;
                    })
                    ->toArray();
                
                Chat::insert($records);
                
                // 4. 显示进度
                if ($i % 10 == 0) {
                    $progress = round(($i / $batches) * 100, 2);
                    $this->command->info("Progress: {$progress}% completed");
                }
            }
            
            DB::commit();
            
            // 5. 确保每种类型至少有一个
            $specialTypes = [
                Chat::factory()->bot(),
                Chat::factory()->channel(),
                Chat::factory()->group(),
                Chat::factory()->person(),
            ];
            
            $specialRecords = collect($specialTypes)
                ->map(function ($factory) {
                    $model = $factory->make();
                    $attributes = $model->attributesToArray();
                    $attributes['id'] = Ulid::generate();
                    
                    // 处理日期时间格式
                    if (isset($attributes['verified_at'])) {
                        $attributes['verified_at'] = $attributes['verified_at']
                            ? Carbon::parse($attributes['verified_at'])->format('Y-m-d H:i:s')
                            : null;
                    }
                    if (isset($attributes['verified_start_at'])) {
                        $attributes['verified_start_at'] = $attributes['verified_start_at']
                            ? Carbon::parse($attributes['verified_start_at'])->format('Y-m-d H:i:s')
                            : null;
                    }
                    
                    return $attributes;
                })
                ->toArray();
                
            Chat::insert($specialRecords);
            
            $this->command->info('Seeding completed successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }
}