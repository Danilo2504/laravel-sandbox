<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TailLogCommand extends Command
{
    protected $signature = 'log:tail {--lines=20 : Número de líneas iniciales}';
    protected $description = 'Muestra en tiempo real el log más reciente de Laravel con formato de color.';

    public function handle()
    {
        $logPath = storage_path('logs');

        // Encontrar el último archivo de log
        $latestLogFile = collect(glob($logPath . '/*.log'))
            ->sortByDesc(fn($file) => filemtime($file))
            ->first();

        if (!$latestLogFile) {
            $this->error('No se encontró ningún archivo de log.');
            return;
        }

        $this->info("📄 Siguiendo archivo de log: {$latestLogFile}\n");

        $lines = $this->option('lines');
        $process = popen("tail -n {$lines} -f " . escapeshellarg($latestLogFile), 'r');

        if (!$process) {
            $this->error('No se pudo abrir el archivo de log.');
            return;
        }

        while (!feof($process)) {
            $line = fgets($process);
            if ($line === false) {
                usleep(100000); // 0.1s
                continue;
            }

            $this->outputLine($line);
        }

        pclose($process);
    }

    protected function outputLine(string $line): void
    {
        // Detectar nivel de log y aplicar color
        if (preg_match('/\b(EMERGENCY|ALERT|CRITICAL|ERROR)\b/i', $line)) {
            $color = 'red';
        } elseif (preg_match('/\b(WARNING|NOTICE)\b/i', $line)) {
            $color = 'yellow';
        } elseif (preg_match('/\b(INFO)\b/i', $line)) {
            $color = 'blue';
        } elseif (preg_match('/\b(DEBUG)\b/i', $line)) {
            $color = 'gray';
        } else {
            $color = 'white';
        }

        // Opcional: resaltar timestamp
        $line = preg_replace('/^\[([^\]]+)\]/', "<fg=bright-white>[$1]</>", $line);

        // Imprimir con formato
        $this->line("<fg={$color}>{$line}</>");
    }
}
