@extends('layouts.Admin.app') {{-- Assume this is your main admin layout --}}

@section('content')
    <style>
        /* Container and Main Layout Styles (Adjust based on your sidebar) */
        .main-content {
            padding: 2rem;
            margin-left: 60px; /* Adjust if needed to fit sidebar */
        }
        
        /* Main Dashboard Container (White Card) */
        .approval-dashboard-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            min-height: 80vh; /* Para magkaroon ng space sa baba */
        }

        /* Header Section: Title and Search Bar */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            gap: 20px;
        }
        .header-section h2 {
            margin: 0;
            font-weight: 600;
            color: #333;
            font-size: 1.5rem; /* Adjusted to look closer to the image */
        }
        .search-bar {
            display: flex;
            flex-grow: 1; 
            max-width: 400px; 
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            overflow: hidden; 
        }
        .search-bar input {
            border: none;
            padding: 0.5rem 1rem;
            flex-grow: 1;
            outline: none;
            font-size: 1rem;
        }
        .search-bar button {
            background-color: #f8f9fa;
            border: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
            color: #495057;
        }

        /* Table Structure and Head */
        .exams-table-container {
            border: 1px solid #e0e0e0; /* Border around the entire table area in the image */
            border-radius: 4px;
            overflow: hidden; /* Important for border-radius */
        }
        .exams-table {
            width: 100%;
            border-collapse: collapse;
        }
        .exams-table thead {
            background-color: #f8f9fa; /* Light gray header background */
        }
        .exams-table th,
        .exams-table td {
            padding: 10px 15px; /* Adjust padding to match image density */
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
            font-size: 0.95rem;
        }
        .exams-table th {
            color: #495057;
            text-align: left;
            font-weight: 600;
        }
        
        /* Table Rows */
        .exams-table tbody tr:last-child td {
            border-bottom: none; /* Remove border from the last row */
        }

        /* Column Styles */
        /* Exam Name Column */
        .exam-info .title {
            font-weight: 500;
            color: #333;
        }
        .exam-info .author {
            font-size: 0.8em;
            color: #6c757d;
            margin-top: 2px;
        }

        /* Approval Status Badges/Spans */
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85em;
            font-weight: 600;
            display: inline-block;
        }
        /* Matching the image colors */
        .status-pending { background-color: #fff3cd; color: #856404; border: 1px solid #ffc821; } /* Yellow/Orange (Lighter background, darker text) */
        .status-approved { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; } /* Green */
        
        /* Actions Column */
        .action-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .action-buttons button, 
        .action-buttons a {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            text-decoration: none; /* Remove underline from <a> */
            font-size: 0.95rem;
            color: #6c757d; /* Default icon color */
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .action-buttons .text-success { color: #28a745; }
        .action-buttons .text-danger { color: #dc3545; }
        .action-buttons .text-info { color: #17a2b8; }
        
        /* Pagination Area */
        .pagination-area {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }
        .pagination-controls {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 5px;
        }
        .page-link-custom {
            display: block;
            padding: 6px 12px;
            border: 1px solid #dee2e6;
            color: #007bff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .page-item-active .page-link-custom {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .page-link-nav {
            color: #333;
            font-weight: 500;
        }
        .page-link-nav:hover {
            text-decoration: underline;
        }

    </style>

    <div class="main-content">
        <div class="approval-dashboard-container">
            <div class="header-section">
                <h2>Exams for Approval</h2>
                <div class="search-bar">
                    <input type="text" placeholder="Search for exams" value="" readonly>
                    <button type="button"><i class="bi bi-search"></i></button>
                </div>
            </div>

            <div class="exams-table-container">
                <table class="exams-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;"><input type="checkbox"></th>
                            <th style="width: 35%;">EXAM NAME <i class="bi bi-caret-down-fill ms-1"></i></th>
                            <th style="width: 20%;">SUBJECT <i class="bi bi-caret-down-fill ms-1"></i></th>
                            <th style="width: 20%;">APPROVAL STATUS <i class="bi bi-caret-down-fill ms-1"></i></th>
                            <th style="width: 20%;">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Static Row 1: Pending --}}
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>
                                <div class="exam-info">
                                    <div class="title">Midterm Computer Programming</div>
                                    <div class="author">Author Name, 1 other</div>
                                </div>
                            </td>
                            <td>Computer Programming 1</td>
                            <td>
                                <span class="status-badge status-pending">Pending</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="text-success" title="Approve"><i class="bi bi-check2-circle"></i> Approve</button>
                                    <button class="text-danger" title="Revise"><i class="bi bi-x"></i> Revise</button>
                                    <a href="#" title="View" style="color: #6c757d;"><i class="bi bi-eye"></i> View</a>
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Static Row 2: Pending --}}
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>
                                <div class="exam-info">
                                    <div class="title">Midterm Discrete Structures</div>
                                    <div class="author">Author Name, 2 others</div>
                                </div>
                            </td>
                            <td>Discrete Structures</td>
                            <td>
                                <span class="status-badge status-pending">Pending</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="text-success" title="Approve"><i class="bi bi-check2-circle"></i> Approve</button>
                                    <button class="text-danger" title="Revise"><i class="bi bi-x"></i> Revise</button>
                                    <a href="#" title="View" style="color: #6c757d;"><i class="bi bi-eye"></i> View</a>
                                </div>
                            </td>
                        </tr>

                        {{-- Static Row 3: Approved --}}
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>
                                <div class="exam-info">
                                    <div class="title">Midterm System Administration</div>
                                    <div class="author">Author Name, 1 other</div>
                                </div>
                            </td>
                            <td>System Administration and Management</td>
                            <td>
                                <span class="status-badge status-approved">Approved</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button title="Rescind" style="color: #6c757d;"><i class="bi bi-arrow-counterclockwise"></i> Rescind</button>
                                    <button title="Edit Settings" style="color: #6c757d;"><i class="bi bi-pencil-square"></i> Edit Settings</button>
                                    <a href="#" title="View" style="color: #6c757d;"><i class="bi bi-eye"></i> View</a>
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Static Placeholder Row (Optional, remove if only showing exact image content) --}}
                        {{-- <tr>
                            <td><input type="checkbox"></td>
                            <td>
                                <div class="exam-info">
                                    <div class="title">Finals Data Structures</div>
                                    <div class="author">Author Name</div>
                                </div>
                            </td>
                            <td>Data Structures and Algorithms</td>
                            <td>
                                <span class="status-badge status-pending">Pending</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="text-success" title="Approve"><i class="bi bi-check2-circle"></i> Approve</button>
                                    <button class="text-danger" title="Revise"><i class="bi bi-x"></i> Revise</button>
                                    <a href="#" title="View" style="color: #6c757d;"><i class="bi bi-eye"></i> View</a>
                                </div>
                            </td>
                        </tr> --}}
                        
                    </tbody>
                </table>
            </div>

            {{-- Pagination Control --}}
            <div class="pagination-area">
                <span style="color: #6c757d; font-size: 0.9rem;">Prev</span>
                <ul class="pagination-controls">
                    <li class="page-item page-item-active"><a class="page-link-custom" href="#">1</a></li>
                    <li class="page-item"><a class="page-link-custom" href="#">2</a></li>
                    <li class="page-item"><a class="page-link-custom" href="#">3</a></li>
                    <li class="page-item"><a class="page-link-custom" href="#">4</a></li>
                    <li class="page-item"><a class="page-link-custom" href="#">5</a></li>
                </ul>
                <span style="color: #6c757d; font-size: 0.9rem;">Next</span>
            </div>

        </div>
    </div>
    
    {{-- Note: You need to ensure you have Bootstrap Icons loaded in your app.blade.php for the bi-* classes to work. --}}
    
@endsection