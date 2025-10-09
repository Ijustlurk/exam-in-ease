@extends('layouts.Instructor.app')

@section('content')
    <style>
        body {
            background-color: #f1f5f8;
            font-family: Arial, sans-serif;
        }

        .header-bar {
            background-color: #6aa7c8;
            padding: 1rem;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #3c9acb;
            font-weight: bold;
            color: #000;
        }

        .question-card {
            background-color: white;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            width: 75%;
        }

        .answer {
            border: 1px solid #ccc;
            border-radius: 20px;
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .answer.correct {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            border-color: #4CAF50;
        }

        .answer.correct small {
            color: white;
        }

        .answer small {
            color: #666;
            font-style: italic;
        }

        .question-footer {
            font-weight: bold;
            margin-top: 0.5rem;
        }

        .response-count {
            float: right;
            font-size: 0.9rem;
            color: #666;
        }

        .content {
            padding-left: 80px;
            padding-right: 20px;
        }
    </style>

    <div class="content">
        <!-- Header -->
        <div class="bg-white p-0 rounded-top  shadow mt-4">
            <div class="header-bar rounded-top ">
                <span>Exam No. 1</span>
                <select class="form-select w-auto">
                    <option selected>Select Class</option>
                    <option>Class A</option>
                    <option>Class B</option>
                </select>
            </div>


            <!-- Tabs -->
            <ul class="nav nav-tabs px-3 pt-2 bg-white">
                <li class="nav-item">
                    <a class="nav-link" href="#">Summary</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="#">Questions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Individual</a>
                </li>
            </ul>

            <!-- Questions List -->
            <div class="container mt-4">

                <!-- Question 1 -->
                <div class="question-card ">
                    <div class="d-flex justify-content-between">
                        <p><strong>Question 1.</strong> This is the question asked?</p>
                        <span class="response-count">242 responses</span>
                    </div>

                    <div class="answer correct">
                        <span>A. This is the one of the choices that is correct.</span>
                        <small>194 responses</small>
                    </div>
                    <div class="answer">
                        <span>B. This is the one of the choices that is wrong.</span>
                        <small>12 responses</small>
                    </div>
                    <div class="answer">
                        <span>C. This is the one of the choices that is wrong.</span>
                        <small>20 responses</small>
                    </div>
                    <div class="answer">
                        <span>D. This is the one of the choices that is wrong.</span>
                        <small>18 responses</small>
                    </div>

                    <div class="question-footer">88% (194) got the correct answer (A).</div>
                </div>

                <!-- Question 2 -->
                <div class="question-card">
                    <div class="d-flex justify-content-between">
                        <p><strong>Question 2.</strong> This is the question asked?</p>
                        <span class="response-count">242 responses</span>
                    </div>

                    <div class="answer correct">
                        <span>A. This is the one of the choices that is correct.</span>
                        <small>194 responses</small>
                    </div>
                    <div class="answer">
                        <span>B. This is the one of the choices that is wrong.</span>
                        <small>12 responses</small>
                    </div>
                    <div class="answer">
                        <span>C. This is the one of the choices that is wrong.</span>
                        <small>20 responses</small>
                    </div>
                    <div class="answer">
                        <span>D. This is the one of the choices that is wrong.</span>
                        <small>18 responses</small>
                    </div>

                    <div class="question-footer">88% (194) got the correct answer (A).</div>
                </div>

                <!-- Question 3 -->
                <div class="question-card">
                    <div class="d-flex justify-content-between">
                        <p><strong>Question 3.</strong> This is the question asked?</p>
                        <span class="response-count">242 responses</span>
                    </div>

                    <div class="answer correct">
                        <span>A. This is the one of the choices that is correct.</span>
                        <small>194 responses</small>
                    </div>
                    <div class="answer">
                        <span>B. This is the one of the choices that is wrong.</span>
                        <small>12 responses</small>
                    </div>
                    <div class="answer">
                        <span>C. This is the one of the choices that is wrong.</span>
                        <small>20 responses</small>
                    </div>
                    <div class="answer">
                        <span>D. This is the one of the choices that is wrong.</span>
                        <small>18 responses</small>
                    </div>

                    <div class="question-footer">88% (194) got the correct answer (A).</div>
                </div>

            </div>

        </div>


@endsection