@extends('layouts.ProgramChair.app')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header with Search -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Exams for Approval</h2>

        <!-- Search -->
        <div class="position-relative" style="width: 350px;">
            <input type="text" class="form-control ps-3 pe-5 rounded-pill shadow-sm" placeholder="Search for exams">
            <span class="position-absolute top-50 end-0 translate-middle-y pe-3 text-muted">
                <i class="bi bi-search"></i>
            </span>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">
                            <input type="checkbox" class="form-check-input">
                        </th>
                        <th scope="col">Exam Name <i class="bi bi-caret-down-fill small"></i></th>
                        <th scope="col">Subject <i class="bi bi-caret-down-fill small"></i></th>
                        <th scope="col">Approval Status <i class="bi bi-caret-down-fill small"></i></th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row 1 -->
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td>
                            <div class="fw-semibold">Midterm Computer Programming</div>
                            <small class="text-muted fst-italic">Author Name, 1 other</small>
                        </td>
                        <td>Computer Programming 1</td>
                        <td>
                            <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                        </td>
                        <td>
                            <div class="d-flex gap-3">
                                <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-check-circle me-1"></i> Approve</a>
                                <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-x-circle me-1"></i> Revise</a>
                                <a href="{{ route('programchair.manage-approval.show') }}" class="text-secondary text-decoration-none"><i class="bi bi-search me-1"></i> View</a>
                            </div>
                        </td>
                    </tr>
                    <!-- Row 2 -->
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td>
                            <div class="fw-semibold">Midterm Discrete Structures</div>
                            <small class="text-muted fst-italic">Author Name, 2 others</small>
                        </td>
                        <td>Discrete Structures</td>
                        <td>
                            <span class="badge bg-warning text-dark px-3 py-2">Pending</span>
                        </td>
                        <td>
                            <div class="d-flex gap-3">
                                <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-check-circle me-1"></i> Approve</a>
                                <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-x-circle me-1"></i> Revise</a>
                                <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-search me-1"></i> View</a>
                            </div>
                        </td>
                    </tr>
                    <!-- Row 3 -->
                    <tr>
                        <td><input type="checkbox" class="form-check-input"></td>
                        <td>
                            <div class="fw-semibold">Midterm System Administration</div>
                            <small class="text-muted fst-italic">Author Name, 1 other</small>
                        </td>
                        <td>System Administration and Management</td>
                        <td>
                            <span class="badge bg-success px-3 py-2">Approved</span>
                        </td>
                        <td>
                            <div class="d-flex gap-3">
                                <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-arrow-counterclockwise me-1"></i> Rescind</a>
                                <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-pencil-square me-1"></i> Edit Settings</a>
                                <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-search me-1"></i> View</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex align-items-center mt-4">
        <nav>
            <ul class="pagination mb-0">
                <li class="page-item"><a class="page-link" href="#">Prev</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">4</a></li>
                <li class="page-item"><a class="page-link" href="#">5</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav>
    </div>

</div>

<!-- Custom CSS -->
<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .table thead th {
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #555;
    }
</style>
@endsection
