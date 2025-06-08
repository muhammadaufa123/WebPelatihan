<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modules') }} - {{ $course->name }}
            </h2>
            <a href="{{ route('admin.modules.create', $course) }}" class="font-bold py-2 px-4 bg-indigo-700 text-white rounded-full">Add Module</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 flex flex-col gap-y-5">
                @forelse($modules as $module)
                    <div class="flex items-center justify-between">
                        <div>{{ $module->name }}</div>
                        <input type="number" name="orders[{{ $module->id }}]" value="{{ $module->order }}" class="border rounded w-20 p-1">
                    </div>
                @empty
                    <p>No modules available.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
