<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Course') }}
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
                
                <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <input type="text" name="name" value="{{ old('name') }}" class="border rounded px-3 py-2 w-full">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="path_trailer" :value="__('Path Trailer')" />
                        <input type="text" name="path_trailer" value="{{ old('path_trailer') }}" class="border rounded px-3 py-2 w-full" required>
                        <x-input-error :messages="$errors->get('path_trailer')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="thumbnail" :value="__('Thumbnail')" />
                        <input type="file" name="thumbnail" class="border rounded px-3 py-2 w-full" required>
                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label for="price" class="block font-bold">Harga (Rp)</label>
                        <input type="number" name="price" id="price" value="{{ old('price', $course->price ?? 0) }}"
                        class="form-input w-full" min="0">
                    </div>

                    <div class="mt-4">
                        <x-input-label for="category" :value="__('Category')" />
                        <select name="category_id" id="category_id" class="py-3 rounded-lg pl-3 w-full border border-slate-300">
                            <option value="">Choose category</option>
                            @forelse($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @empty
                            @endforelse
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="course_mode_id" :value="__('Mode')" />
                        <select name="course_mode_id" id="course_mode_id" class="py-3 rounded-lg pl-3 w-full border border-slate-300">
                            <option value="">Choose mode</option>
                            @foreach($modes as $mode)
                                <option value="{{$mode->id}}">{{$mode->name}}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('course_mode_id')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="course_level_id" :value="__('Level')" />
                        <select name="course_level_id" id="course_level_id" class="py-3 rounded-lg pl-3 w-full border border-slate-300">
                            <option value="">Choose level</option>
                            @foreach($levels as $level)
                                <option value="{{$level->id}}">{{$level->name}}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('course_level_id')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="about" :value="__('About')" />
                        <textarea name="about" id="about" cols="30" rows="5" class="border border-slate-300 rounded-xl w-full">{{ old('about') }}</textarea>
                        <x-input-error :messages="$errors->get('about')" class="mt-2" />
                    </div>

                    <hr class="my-5">

                    <div class="mt-4">
                        <div class="flex flex-col gap-y-5">
                            <x-input-label for="keypoints" :value="__('Keypoints')" />
                            @for ($i = 0; $i < 4; $i++)
                                <input type="text" class="py-3 rounded-lg border-slate-300 border" placeholder="Write your copywriting" name="course_keypoints[]">
                            @endfor
                        </div>
                        <x-input-error :messages="$errors->get('course_keypoints')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <button type="submit" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                            Add New Course
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
