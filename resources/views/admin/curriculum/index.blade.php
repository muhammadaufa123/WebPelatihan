<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Manage Curriculum') }}</h2>
            <a href="{{ route('admin.courses.show', $course) }}" class="font-bold py-2 px-4 bg-gray-700 text-white rounded-full">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.curriculum.store', $course) }}" class="mb-4 flex gap-2">
                    @csrf
                    <input type="text" name="name" class="border rounded w-full" placeholder="Module name">
                    <input type="number" name="order" class="border rounded w-24" placeholder="Order">
                    <button class="px-4 py-2 bg-indigo-700 text-white rounded">Add Module</button>
                </form>

                @foreach($modules as $module)
                    <div class="border p-4 rounded mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <form method="POST" action="{{ route('admin.curriculum.update', $module) }}" class="flex flex-1 gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $module->name }}" class="border rounded w-full">
                                <input type="number" name="order" value="{{ $module->order }}" class="border rounded w-24">
                                <button class="px-3 py-1 bg-indigo-700 text-white rounded">Save</button>
                            </form>
                            <form method="POST" action="{{ route('admin.curriculum.destroy', $module) }}">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-1 bg-red-700 text-white rounded">Delete</button>
                            </form>
                        </div>

                        <div class="ml-4">
                            <form method="POST" action="{{ route('admin.curriculum.videos.store', $module) }}" class="flex gap-2 mb-2">
                                @csrf
                                <input type="text" name="name" placeholder="Video name" class="border rounded w-full">
                                <input type="text" name="path_video" placeholder="YouTube ID" class="border rounded w-full">
                                <button class="px-3 py-1 bg-green-700 text-white rounded">Add Video</button>
                            </form>

                            <form method="POST" action="{{ route('admin.curriculum.materials.store', $module) }}" enctype="multipart/form-data" class="flex gap-2 mb-2">
                                @csrf
                                <input type="hidden" name="course_module_id" value="{{ $module->id }}">
                                <input type="text" name="name" placeholder="Material name" class="border rounded w-full">
                                <input type="file" name="file" class="border rounded w-full">
                                <button class="px-3 py-1 bg-green-700 text-white rounded">Add Material</button>
                            </form>

                            <form method="POST" action="{{ route('admin.curriculum.tasks.store', $module) }}" class="flex gap-2 mb-2">
                                @csrf
                                <input type="hidden" name="course_module_id" value="{{ $module->id }}">
                                <input type="text" name="name" placeholder="Task name" class="border rounded w-full">
                                <input type="text" name="description" placeholder="Description" class="border rounded w-full">
                                <input type="number" name="order" placeholder="Order" class="border rounded w-24">
                                <button class="px-3 py-1 bg-green-700 text-white rounded">Add Task</button>
                            </form>

                            <div class="mt-2">
                                <h4 class="font-semibold">Videos</h4>
                                <ul class="list-disc list-inside">
                                    @foreach($module->videos as $v)
                                        <li>{{ $v->name }}</li>
                                    @endforeach
                                </ul>
                                <h4 class="font-semibold mt-2">Materials</h4>
                                <ul class="list-disc list-inside">
                                    @foreach($module->materials as $m)
                                        <li>{{ $m->name }}</li>
                                    @endforeach
                                </ul>
                                <h4 class="font-semibold mt-2">Tasks</h4>
                                <ul class="list-disc list-inside">
                                    @foreach($module->tasks as $t)
                                        <li>{{ $t->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
