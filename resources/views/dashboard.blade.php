@extends('layout.template.dashboardUnified')

@section('dashboard-content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
    </div>

    @role('admin')
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 p-6">
                <div class="text-white">
                    <div class="text-3xl font-bold">{{ $courses }}</div>
                    <div class="text-indigo-100 text-sm font-medium">Courses</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-purple-500 to-purple-700 p-6">
                <div class="text-white">
                    <div class="text-3xl font-bold">{{ $transactions }}</div>
                    <div class="text-purple-100 text-sm font-medium">Transactions</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-6">
                <div class="text-white">
                    <div class="text-3xl font-bold">{{ $students }}</div>
                    <div class="text-blue-100 text-sm font-medium">Students</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-teal-500 to-teal-700 p-6">
                <div class="text-white">
                    <div class="text-3xl font-bold">{{ $teachers }}</div>
                    <div class="text-teal-100 text-sm font-medium">Teachers</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-orange-500 to-orange-700 p-6">
                <div class="text-white">
                    <div class="text-3xl font-bold">{{ $categories }}</div>
                    <div class="text-orange-100 text-sm font-medium">Categories</div>
                </div>
            </div>
        </div>
    </div>
    @endrole

    @role('trainer')
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 p-6">
                <div class="text-white">
                    <div class="text-3xl font-bold">{{ $courses }}</div>
                    <div class="text-indigo-100 text-sm font-medium">Courses</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover-lift">
            <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-6">
                <div class="text-white">
                    <div class="text-3xl font-bold">{{ $students }}</div>
                    <div class="text-blue-100 text-sm font-medium">Students</div>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('admin.courses.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-200 w-max">Create New Course</a>
    @endrole

    @role('trainee')
    <h3 class="text-indigo-950 font-bold text-2xl mb-3">Upgrade Skills Today</h3>
    <p class="text-slate-500 text-base mb-6">Grow your career with experienced teachers in Alqowy Class.</p>
    <a href="{{ route('front.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all duration-200 w-max">Explore Catalog</a>
    @endrole
@endsection
