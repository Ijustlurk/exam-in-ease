@can('admin-access')
@extends('layouts.Admin.app')

@section('content')
<style>
    .stats-container {
        background-color: #e8f1f5;
        min-height: 100vh;
        padding: 30px;
    }

    .stats-header {
        background-color: #6ba5b3;
        color: white;
        padding: 20px 30px;
        border-radius: 12px 12px 0 0;
    }

    .stats-title {
        font-size: 24px;
        font-weight: 600;
        margin: 0 0 5px 0;
    }

    .stats-subtitle {
        font-size: 14px;
        opacity: 0.9;
        margin: 0;
    }

    .stats-body {
        background: white;
        padding: 30px;
        border-radius: 0 0 12px 12px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: linear-gradient(135deg, #6ba5b3 0%, #5a94aa 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(107, 165, 179, 0.2);
    }

    .stat-label {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 36px;
        font-weight: 700;
    }

    .back-btn {
        background-color: #6ba5b3;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 20px;
    }

    .back-btn:hover {
        background-color: #5a94aa;
    }
</style>

<div id="mainContent" class="main stats-container">
    <a href="{{ route('admin.exam-statistics.index') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Exams
    </a>

    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);">
        <div class="stats-header">
            <h1 class="stats-title">{{ $exam->exam_title }}</h1>
            <p class="stats-subtitle">{{ $exam->subject->subject_name }}</p>
        </div>

        <div class="stats-body">
            <h2 style="margin-bottom: 20px; color: #2c3e50;">Exam Statistics</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Attempts</div>
                    <div class="stat-value">{{ $stats['total_attempts'] }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Completed</div>
                    <div class="stat-value">{{ $stats['completed_attempts'] }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Average Score</div>
                    <div class="stat-value">{{ $stats['average_score'] }}%</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Highest Score</div>
                    <div class="stat-value">{{ $stats['highest_score'] }}%</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Lowest Score</div>
                    <div class="stat-value">{{ $stats['lowest_score'] }}%</div>
                </div>
            </div>

            <div style="margin-top: 30px;">
                <h3 style="color: #2c3e50; margin-bottom: 15px;">Exam Details</h3>
                <div style="display: grid; grid-template-columns: 200px 1fr; gap: 15px;">
                    <div style="font-weight: 600;">Duration:</div>
                    <div>{{ $exam->duration }} minutes</div>

                    <div style="font-weight: 600;">Total Points:</div>
                    <div>{{ $exam->total_points }}</div>

                    <div style="font-weight: 600;">Number of Items:</div>
                    <div>{{ $exam->no_of_items }}</div>

                    <div style="font-weight: 600;">Status:</div>
                    <div>{{ ucfirst($exam->status) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@endcan