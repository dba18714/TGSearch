<?php

namespace App\Console\Commands;

use App\Models\Chat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportTelegramChats extends Command
{
    protected $signature = 'telegram:import-chats {file : Path to the URLs file}';
    protected $description = 'Import Telegram chat usernames from a file';

    // sail php artisan telegram:import-chats urls_export.txt
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info('Starting import...');
        $bar = $this->output->createProgressBar(count(file($filePath)));
        $bar->start();

        // 使用生成器读取文件，避免内存占用过大
        $fileHandle = fopen($filePath, 'r');
        $batchSize = 1000; // 每批处理1000条记录
        $batch = [];

        while (($line = fgets($fileHandle)) !== false) {
            $username = $this->extractUsername(trim($line));
            
            if ($username) {
                $batch[] = [
                    'id' => (string) \Illuminate\Support\Str::ulid(),
                    'username' => $username,
                    'source' => 'crawler',
                    'source_str' => trim($line),
                    'is_valid' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // 当批次达到指定大小时，执行插入
                if (count($batch) >= $batchSize) {
                    $this->insertBatch($batch);
                    $batch = [];
                }
            }

            $bar->advance();
        }

        // 处理剩余的记录
        if (!empty($batch)) {
            $this->insertBatch($batch);
        }

        fclose($fileHandle);
        $bar->finish();
        $this->newLine();
        $this->info('Import completed!');
    }

    private function extractUsername(string $url): ?string
    {
        if (preg_match('#^https://t\.me/(.+)$#', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function insertBatch(array $batch)
    {
        try {
            // 使用事务和批量插入
            DB::transaction(function () use ($batch) {
                // 忽略重复的username
                Chat::insertOrIgnore($batch);
            });
        } catch (\Exception $e) {
            $this->error("Error inserting batch: " . $e->getMessage());
        }
    }
}