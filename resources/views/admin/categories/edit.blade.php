<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Update Category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden p-10 shadow-sm sm:rounded-lg">

                @if($errors->any())
                    @foreach($errors->all() as $error)
                        <div class="py-3 w-full rounded-3xl bg-red-500 text-white">
                            {{$error}}
                        </div>
                    @endforeach
                @endif
                
                <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input value="{{ $category ->name}}" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="icon" :value="__('icon')" />
                        <img src="{{Storage::url($category->icon)}}" alt="" class="rounded-2xl object-cover w-[90px] h-[90px]">
                        <x-text-input id="icon" class="block mt-1 w-full" type="file" name="icon" required autofocus autocomplete="icon" />
                        <x-input-error :messages="$errors->get('icon')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="course_type" :value="__('Course Type')" />
                        <select id="course_type" name="course_type" class="block mt-1 w-full border-gray-300 rounded">
                            <option value="online" {{ $category->course_type == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="onsite" {{ $category->course_type == 'onsite' ? 'selected' : '' }}>Onsite</option>
                        </select>
                        <x-input-error :messages="$errors->get('course_type')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="level" :value="__('Level')" />
                        <select id="level" name="level" class="block mt-1 w-full border-gray-300 rounded">
                            <option value="beginner" {{ $category->level == 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ $category->level == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advance" {{ $category->level == 'advance' ? 'selected' : '' }}>Advance</option>
                        </select>
                        <x-input-error :messages="$errors->get('level')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
            
                        <button type="submit" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                            Update Category
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
