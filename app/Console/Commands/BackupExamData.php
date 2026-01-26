<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupExamData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exam:backup 
                            {--path= : Optional custom backup path}
                            {--compress : Compress backup with gzip}
                            {--cleanup : Remove backups older than 30 days}
                            {--keep-days=30 : Number of days to keep old backups}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup all exam-related data to a SQL file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting backup process...');
        
        try {
            // Generate timestamp and filename
            $timestamp = Carbon::now()->format('Ymd_His');
            $filename = "examinease_backup_{$timestamp}.sql";
            
            // Determine backup directory (cross-platform)
            $backupDir = $this->getBackupDirectory();
            $backupPath = $backupDir . DIRECTORY_SEPARATOR . $filename;
            
            // Ensure backup directory exists
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
                $this->info("📁 Created backup directory: {$backupDir}");
            }

            // Get all relevant tables
            $tables = $this->getAllTables();
            $this->info('📋 Backing up ' . count($tables) . ' tables...');

            // Perform backup
            $success = $this->performBackup($backupPath, $tables);

            if (!$success) {
                throw new \Exception('Backup command failed');
            }

            // Verify backup file
            if (!$this->verifyBackup($backupPath)) {
                throw new \Exception('Backup verification failed');
            }

            // Compress if requested
            if ($this->option('compress')) {
                $backupPath = $this->compressBackup($backupPath);
            }

            // Cleanup old backups if requested
            if ($this->option('cleanup')) {
                $this->cleanupOldBackups($backupDir);
            }

            // Success message
            $size = $this->formatBytes(filesize($backupPath));
            $this->info("✅ Backup created successfully!");
            $this->info("📦 Location: {$backupPath}");
            $this->info("💾 Size: {$size}");

            // Open file explorer (Windows only)
            if (PHP_OS_FAMILY === 'Windows') {
                $this->openFileExplorer($backupPath);
            }

            // Log success
            Log::info('Backup completed successfully', [
                'file' => $backupPath,
                'size' => $size,
                'tables' => count($tables)
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Backup failed: ' . $e->getMessage());
            Log::error('Backup failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Get backup directory (cross-platform)
     */
    protected function getBackupDirectory(): string
    {
        if ($customPath = $this->option('path')) {
            return $customPath;
        }

        // Use Laravel storage by default
        $storageBackupPath = storage_path('app/backups');
        
        // For development, can use Desktop on Windows
        if (PHP_OS_FAMILY === 'Windows' && app()->environment('local')) {
            $desktopPath = getenv('USERPROFILE') . '\\Desktop\\ExamInEaseBackups';
            return is_dir(dirname($desktopPath)) ? $desktopPath : $storageBackupPath;
        }

        return $storageBackupPath;
    }

    /**
     * Get all exam-related tables
     */
    protected function getAllTables(): array
    {
        return [
            // Core exam tables
            'exams',
            'exam_approvals',
            'exam_assignments',
            'exam_attempts',
            'exam_answers',
            'exam_items',
            'exam_collaboration',
            'exam_review_settings',
            'sections',
            'collab_comments',
            
            // Class and enrollment
            'class',
            'class_enrolment',
            'subjects',
            
            // User tables
            'users',
            'user_teacher',
            'user_student',
            'user_program_chair',
            'user_admin',
            'teacher_assignments',
            
            // Authentication and roles
            'roles',
            'role_user',
            'password_reset_tokens',
            
            // Notifications
            'notifications',
            'system_notifications',
            
            // Activity logs
            'student_exam_activity_logs',
        ];
    }

    /**
     * Perform the database backup
     */
    protected function performBackup(string $backupPath, array $tables): bool
    {
        $db = config('database.connections.mysql');
        $database = $db['database'];
        $tableList = implode(' ', $tables);

        // Create MySQL config file for security (no password in command line)
        $configFile = $this->createMySQLConfigFile();

        // Build mysqldump command with better error handling
        $errorLog = storage_path('logs/backup_error.log');
        $command = sprintf(
            'mysqldump --defaults-extra-file="%s" --single-transaction --routines --triggers %s %s > "%s" 2> "%s"',
            $configFile,
            $database,
            $tableList,
            $backupPath,
            $errorLog
        );

        $this->info('Executing backup command...');
        
        // Execute backup
        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        // Clean up config file
        @unlink($configFile);

        // Check for errors
        if ($returnVar !== 0) {
            $this->error('Backup command returned error code: ' . $returnVar);
            
            // Read error log
            if (file_exists($errorLog)) {
                $errorContent = file_get_contents($errorLog);
                $this->error('Error details: ' . $errorContent);
                Log::error('mysqldump error', ['error' => $errorContent]);
            }
            
            return false;
        }

        return true;
    }

    /**
     * Create temporary MySQL config file for secure authentication
     */
    protected function createMySQLConfigFile(): string
    {
        $db = config('database.connections.mysql');
        
        $config = "[client]\n";
        $config .= "user=\"{$db['username']}\"\n";
        $config .= "password=\"{$db['password']}\"\n";
        $config .= "host=\"{$db['host']}\"\n";
        $config .= "port={$db['port']}\n";

        $configPath = sys_get_temp_dir() . '/mysql_backup_' . uniqid() . '.cnf';
        file_put_contents($configPath, $config);
        chmod($configPath, 0600); // Secure permissions

        return $configPath;
    }

    /**
     * Verify backup file is valid
     */
    protected function verifyBackup(string $backupPath): bool
    {
        if (!file_exists($backupPath)) {
            $this->error('Backup file does not exist');
            return false;
        }

        $size = filesize($backupPath);
        if ($size === 0) {
            $this->error('Backup file is empty (0 bytes)');
            return false;
        }

        // Check if file is too small (less than 100 bytes is suspicious)
        if ($size < 100) {
            $this->error('Backup file is too small (' . $size . ' bytes)');
            
            // Show file contents for debugging
            $content = file_get_contents($backupPath);
            $this->error('File content: ' . $content);
            return false;
        }

        // Read first few lines to verify SQL content
        $handle = fopen($backupPath, 'r');
        $content = '';
        for ($i = 0; $i < 10; $i++) {
            $line = fgets($handle);
            if ($line === false) break;
            $content .= $line;
        }
        fclose($handle);

        // More flexible validation - check for SQL keywords
        $hasSQLContent = (
            stripos($content, 'MySQL dump') !== false ||
            stripos($content, 'CREATE TABLE') !== false ||
            stripos($content, 'INSERT INTO') !== false ||
            stripos($content, 'DROP TABLE') !== false ||
            stripos($content, 'Database:') !== false ||
            preg_match('/--.*MySQL/', $content)
        );

        if (!$hasSQLContent) {
            $this->error('Backup file does not contain valid SQL content');
            $this->error('First 10 lines preview:');
            $this->line($content);
            return false;
        }

        $this->info('✓ Backup verification passed (' . $this->formatBytes($size) . ')');
        return true;
    }

    /**
     * Compress backup file with gzip
     */
    protected function compressBackup(string $backupPath): string
    {
        $this->info('🗜️  Compressing backup...');
        
        // Get original size BEFORE compression
        $originalSize = filesize($backupPath);
        $compressedPath = $backupPath . '.gz';
        
        // Check if gzip is available
        if (PHP_OS_FAMILY === 'Windows') {
            // Use PHP's gzip functions for Windows
            $this->info('Using PHP gzip compression...');
            
            $data = file_get_contents($backupPath);
            $compressed = gzencode($data, 9); // Maximum compression
            
            if ($compressed !== false) {
                file_put_contents($compressedPath, $compressed);
                
                if (file_exists($compressedPath)) {
                    unlink($backupPath); // Delete original
                    
                    $compressedSize = filesize($compressedPath);
                    $savings = 100 - round(($compressedSize / $originalSize) * 100);
                    
                    $this->info("✓ Compressed (saved {$savings}%)");
                    return $compressedPath;
                }
            }
        } else {
            // Use command line gzip for Linux/Mac
            $command = sprintf('gzip -c "%s" > "%s"', $backupPath, $compressedPath);
            exec($command, $output, $returnVar);

            if ($returnVar === 0 && file_exists($compressedPath)) {
                unlink($backupPath); // Delete original
                
                $compressedSize = filesize($compressedPath);
                $savings = 100 - round(($compressedSize / $originalSize) * 100);
                
                $this->info("✓ Compressed (saved {$savings}%)");
                return $compressedPath;
            }
        }

        $this->warn('⚠️  Compression failed, keeping uncompressed backup');
        return $backupPath;
    }

    /**
     * Clean up old backup files
     */
    protected function cleanupOldBackups(string $backupDir): void
    {
        $this->info('🧹 Cleaning old backups...');
        
        $keepDays = (int) $this->option('keep-days');
        $cutoffDate = Carbon::now()->subDays($keepDays);
        
        // Match both old and new naming patterns
        $patterns = [
            $backupDir . '/exam_backup_*.sql*',
            $backupDir . '/examinease_backup_*.sql*'
        ];
        
        $deletedCount = 0;
        
        foreach ($patterns as $pattern) {
            $files = glob($pattern);
            
            foreach ($files as $file) {
                $fileTime = Carbon::createFromTimestamp(filemtime($file));
                
                if ($fileTime->lt($cutoffDate)) {
                    unlink($file);
                    $deletedCount++;
                }
            }
        }
        
        if ($deletedCount > 0) {
            $this->info("✓ Deleted {$deletedCount} old backup(s)");
        } else {
            $this->info('✓ No old backups to delete');
        }
    }

    /**
     * Open file explorer and select backup file (Windows only)
     */
    protected function openFileExplorer(string $path): void
    {
        $explorerCmd = 'explorer.exe /select,"' . $path . '"';
        pclose(popen('start /B ' . $explorerCmd, 'r'));
    }

    /**
     * Format bytes to human readable size
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}