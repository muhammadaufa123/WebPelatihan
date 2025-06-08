<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Module') }} - {{ $course->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden p-10 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.modules.store', $course) }}">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required />
                    </div>
                    <div class="mt-4">
                        <x-input-label for="order" :value="__('Order')" />
                        <x-text-input id="order" class="block mt-1 w-20" type="number" name="order" />
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <button type="submit" class="font-bold py-2 px-6 bg-indigo-700 text-white rounded-full">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
