@extends('layouts.ProgramChair.app')

@section('content')
<div class="min-vh-100 bg-light">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-teal text-white">
        <h1 class="h4 mb-0 fw-bold">Midterm Computer Programming</h1>
        <button class="btn btn-dark fw-semibold px-4">Approve Exam</button>
    </div>

    <!-- Questions Tab -->
    <div class="bg-white border-bottom">
        <div class="text-center py-3">
            <h2 class="h5 fw-bold text-dark d-inline-block border-bottom border-4 border-teal pb-1 px-3">
                Questions
            </h2>
        </div>
    </div>

    <!-- Content Area -->
    <div class="container py-4">
        
        <!-- Part 1 Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-1">Part 1.</h5>
                <p class="text-muted mb-0">Directions for Part I</p>
            </div>
        </div>

        <!-- Question 1 -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <p class="fw-bold mb-3">
                    Question 1. <span class="fw-normal">This is the question asked?</span>
                </p>

                <div class="list-group">
                    <!-- Correct Answer -->
                    <div class="list-group-item list-group-item-success fw-semibold rounded mb-2">
                        A. This is the one of the choices that is correct.
                    </div>

                    <!-- Wrong Answers -->
                    <div class="list-group-item border rounded mb-2">
                        B. This is the one of the choices that is wrong.
                    </div>
                    <div class="list-group-item border rounded mb-2">
                        C. This is the one of the choices that is wrong.
                    </div>
                    <div class="list-group-item border rounded">
                        D. This is the one of the choices that is wrong.
                    </div>
                </div>
            </div>
        </div>

        <!-- Question 2 (same format) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <p class="fw-bold mb-3">
                    Question 2. <span class="fw-normal">This is the question asked?</span>
                </p>

                <div class="list-group">
                    <div class="list-group-item list-group-item-success fw-semibold rounded mb-2">
                        A. This is the one of the choices that is correct.
                    </div>
                    <div class="list-group-item border rounded mb-2">
                        B. This is the one of the choices that is wrong.
                    </div>
                    <div class="list-group-item border rounded mb-2">
                        C. This is the one of the choices that is wrong.
                    </div>
                    <div class="list-group-item border rounded">
                        D. This is the one of the choices that is wrong.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Custom CSS -->
<style>
    .bg-teal { background-color: #3BA5A4; } /* Teal Header */
    .border-teal { border-color: #3BA5A4 !important; }
</style>
@endsection
