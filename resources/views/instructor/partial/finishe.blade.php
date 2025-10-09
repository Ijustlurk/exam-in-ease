@can('teacher-access')
    @extends('layouts.teacher.app')

    @section('content')


        <style>
            body {
                background-color: #f4f4f4;
            }

            .sidebar {
                background-color: #3390ff;
                min-height: 100vh;
                padding: 1rem;
            }

            .sidebar .btn {
                width: 100%;
                margin-bottom: 0.5rem;
                background-color: #dcdcdc;
                font-weight: bold;
            }

            .dashboard-header {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 20px;
            }


            .status-close {
                color: red;
                font-weight: bold;
            }

            .section-title {
                font-weight: bold;
                margin-top: 2rem;
            }

            .top-icons {
                font-size: 20px;
                cursor: pointer;
            }

            .exm a {
                text-decoration: none;
                font-size: large;
            }
        </style>


        <div class="container ">

            <!-- Main Content -->
            <div class=" m-5 p-4">


                <div class="dashboard-header px-4" style="display: flex; justify-content: space-between; align-items: center;">
                    <h2>Exams Drafts</h2>

                    <div class="border rounded bg-white  shadow text-center p-4">

                        <ul class="list-inline mb-0">
                            <li class="list-inline-item"><a href="#" class="text-decoration-none">Draft</a></li><br>
                            <li class="list-inline-item"><a href="#" class="text-decoration-none">Published</a></li><br>
                            <li class="list-inline-item"><a href="#" class="text-decoration-none">Finished</a></li><br>
                        </ul>
                    </div>

                </div>


                <div class="container w-50">
                    <div class="card exm card-dashboard p-3 bg-white shadow">

                        <h3>Title:</h3>
                        <a href="">Subjecct</a>
                        <a href="">Duration</a>
                        <a href="">Schedule</a>

                    </div>
                </div>
                <a href="new-exam">
                    <button class="btn btn-success px-4 py-2 shadow" style="position: absolute; top: 415px; right:430px; ">
                       Save & Cretae
                    </button>
                </a>

            </div>
        </div>


    @endsection
@endcan