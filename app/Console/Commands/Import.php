<?php

namespace App\Console\Commands;

use App\Models\Tender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Import extends Command
{
    private const DEFAULT_FILENAME = 'test_task_data.csv';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenders:import {file=' . self::DEFAULT_FILENAME . '}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import tenders from CSV file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filename = $this->argument('file');
        $filePath = storage_path("app/csv/{$filename}");

        if (!file_exists($filePath)) {
            $filePath = storage_path('app/csv/' . self::DEFAULT_FILENAME);
            if (!$this->downloadFromGitHub($filePath)) {
                $this->error("Не удалось получить файл импорта, нет данных");
                return 1;
            }
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $this->error("Не удалось открыть файл {$filePath}");
            return 1;
        }

        try {
            // пропускаем первую строку и заодно проверяем формат CSV
            $headers = fgetcsv($handle, 0);
            if ($headers === false) {
                $this->error("Ошибка чтения CSV: " . (error_get_last()['message'] ?? 'неизвестная ошибка'));
                return 1;
            }

            $this->info("Начинаем импорт данных...");
            $count = 0;

            $batch_size = 1000;
            $batch = [];

            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                // Берем только строки с целостными данными.
                // На боевой задаче я бы добавил обработку в
                // зависимости от требований задачи (возможно, нам нужны даже неполные данные)
                if (count($row) < 5) continue;

                $date = \DateTime::createFromFormat('d.m.Y H:i:s', $row[4]);

                $batch[] = [
                    'external_code' => $row[0],
                    'number' => $row[1],
                    'status' => $row[2],
                    'name' => $row[3],
                    'updated_at' => $date ? $date->format('Y-m-d H:i:s') : now(),
                ];

                if (count($batch) >= $batch_size) {
                    $this->insertBatch($batch);
                    $count += count($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                $this->insertBatch($batch);
                $count += count($batch);
            }

            fclose($handle);

            $this->info("Импорт завершен. Всего обработано {$count} записей.");

        } catch (\Throwable $e) {
            $this->error('Произошла ошибка: ' . $e->getMessage());
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }

        return 0;
    }

    private function insertBatch($batch): void
    {
        Tender::upsert($batch, ['external_code'], ['status', 'name', 'updated_at']);
    }

    private function downloadFromGitHub(string $filePath): bool
    {
        try {
            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->get('https://raw.githubusercontent.com/bigfootdary/z-test-backend/main/test_task_data.csv');

            if ($response->successful() && $response->body()) {
                if (!is_dir(dirname($filePath))) {
                    mkdir(dirname($filePath), 0755, true);
                }
                file_put_contents($filePath, $response->body());
                $this->info("Файл импорта успешно скачан из GitHub и сохранен.");
            } else {
                $this->error("Не удалось получить файл из GitHub. Код ответа: " . $response->status());
                return false;
            }
        } catch (\Exception $e) {
            $this->error("Ошибка при попытке загрузки файла из GitHub: " . $e->getMessage());
            return false;
        }

        return true;
    }

}
