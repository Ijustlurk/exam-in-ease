# Automatic Exam Archiving Setup

This document explains how the automatic exam archiving system works and how to set it up.

## Overview

The system automatically archives exams when their `schedule_end` date/time has passed. Exams with status `ongoing` or `approved` will be changed to `archived` status.

## Components

### 1. Artisan Command
**File:** `app/Console/Commands/ArchiveExpiredExams.php`

This command checks for exams that have passed their end schedule and archives them.

**Usage:**
```bash
php artisan exams:archive-expired
```

### 2. Scheduled Task
**File:** `app/Console/Kernel.php`

The command is scheduled to run automatically every 5 minutes.

### 3. Exam Model Methods
**File:** `app/Models/Exam.php`

New helper methods:
- `hasScheduleEnded()` - Check if schedule_end has passed
- `shouldBeArchived()` - Check if exam qualifies for archiving
- `archive()` - Archive the exam if conditions are met

## Setup Instructions

### For Windows (XAMPP) - Task Scheduler

1. **Create a batch file** (`archive_exams.bat`):
```batch
@echo off
cd C:\xampp\htdocs\exam1
C:\xampp\php\php.exe artisan exams:archive-expired
```

2. **Open Task Scheduler:**
   - Press `Win + R`, type `taskschd.msc`, press Enter
   - Click "Create Basic Task"

3. **Configure the task:**
   - **Name:** Archive Expired Exams
   - **Trigger:** Daily
   - **Recur every:** 1 day
   - **Repeat task every:** 5 minutes
   - **Duration:** Indefinitely
   - **Action:** Start a program
   - **Program/script:** `C:\xampp\htdocs\exam1\archive_exams.bat`

### For Windows - Keep Terminal Running

If you prefer, you can keep a terminal open running:
```bash
php artisan schedule:work
```
This will run all scheduled tasks automatically (keep the terminal open).

### For Linux/Production Server - Cron Job

Add to crontab:
```bash
* * * * * cd /path/to/exam1 && php artisan schedule:run >> /dev/null 2>&1
```

## Manual Testing

You can manually run the archiving command anytime:
```bash
php artisan exams:archive-expired
```

## How It Works

1. **Every 5 minutes**, the scheduler runs the `exams:archive-expired` command
2. The command queries for exams where:
   - Status is `ongoing` OR `approved`
   - `schedule_end` is not null
   - `schedule_end` is in the past
3. Each matching exam's status is updated to `archived`
4. The command outputs a summary of archived exams

## Configuration

You can adjust the frequency in `app/Console/Kernel.php`:

```php
// Current setting: Every 5 minutes
$schedule->command('exams:archive-expired')->everyFiveMinutes();

// Other options:
$schedule->command('exams:archive-expired')->everyMinute();
$schedule->command('exams:archive-expired')->everyTenMinutes();
$schedule->command('exams:archive-expired')->hourly();
```

## Logs

Check the Laravel logs for any errors:
```
storage/logs/laravel.log
```

## Database Migration

No migration needed - uses existing `status` and `schedule_end` columns.

## Status Flow

```
draft → for approval → approved → ongoing → archived
                          ↓           ↓
                      (auto-archive when schedule_end passes)
```
