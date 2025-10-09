@can('admin-access')
@extends('layouts.Admin.app')
@section('content')


@section('content')
    <style>
        .exam-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1rem;
            padding: 1rem;
        }

        .left-panel,
        .right-panel {
            background: #f8f9fa;
            border-radius: .5rem;
            padding: 1rem;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        }

        .search-box {
            display: flex;
            align-items: center;
            margin-bottom: .5rem;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: .5rem;
            padding: .25rem .5rem;
        }

        .search-box input {
            border: none;
            outline: none;
            flex: 1;
            padding: .25rem .5rem;
            background: transparent;
        }

        .recents-title {
            font-weight: 500;
            margin-bottom: .5rem;
        }

        .exam-cards {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .exam-card {
            width: 100px;
            text-align: center;
            background: #fff;
            border: 1px solid transparent;
            border-radius: .5rem;
            padding: .5rem;
            cursor: pointer;
            transition: .2s;
        }

        .exam-card:hover,
        .exam-card.active {
            border-color: #0d6efd;
            box-shadow: 0 0 4px rgba(13, 110, 253, .3);
        }

        .exam-icon {
            font-size: 40px;
            display: block;
            margin-bottom: .25rem;
        }

        .exam-detail-icon {
            font-size: 60px;
            display: block;
            margin-bottom: .5rem;
        }

        .exam-detail-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: .5rem;
        }

        .exam-detail-info {
            font-size: .9rem;
            line-height: 1.4;
        }

        .divider {
            border-top: 1px solid #dee2e6;
            margin-top: .5rem;
        }
    </style>

    <div id="mainContent" class="main border rounded shadow mt-4">
        <!-- Left panel -->
        <div class="left-panel">
            <div class="search-box">
                <input type="text" placeholder="Search for exams">
                <i class="bi bi-search"></i>
            </div>
            <div class="recents-title">Recents</div>
            <div class="exam-cards">
                <div class="exam-card active" data-exam="1">
                    <i class="bi bi-file-earmark exam-icon"></i>
                    <div>Exam No. 1</div>
                </div>
                <div class="exam-card" data-exam="2">
                    <i class="bi bi-file-earmark exam-icon"></i>
                    <div>Exam No. 2</div>
                </div>
                <div class="exam-card" data-exam="3">
                    <i class="bi bi-file-earmark exam-icon"></i>
                    <div>Exam No. 3</div>
                </div>
            </div>
        </div>

        <!-- Right panel -->
        <div class="right-panel">
            <i class="bi bi-file-earmark exam-detail-icon"></i>
            <div class="exam-detail-title">Exam No. 1</div>
            <div class="exam-detail-info">
                Subject:<br>
                Date Created:<br>
                Duration:<br>
                Author:<br>
                No. of Items:<br>
                Total Points:
            </div>
            <div class="divider"></div>
            <div class="exam-detail-info">— Stats:</div>
        </div>
    </div>

    </div>
    <!-- optional script to highlight selected card -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.exam-card').forEach(card => {
                card.addEventListener('click', () => {
                    document.querySelectorAll('.exam-card').forEach(c => c.classList.remove('active'));
                    card.classList.add('active');
                    // you can update right panel info dynamically here
                });
            });
        });
    </script>


@endsection
@endcan