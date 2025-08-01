<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Categories') }}
            </h2>
            <a href="{{ route('admin.categories.create') }}" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                Add New
            </a>
        </div>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 flex flex-col gap-y-5">
            @forelse($categories as $category)
    <div class="item-card flex flex-row justify-between items-center">
        <!-- Kiri: Icon + Nama + Date -->
        <div class="flex flex-row items-center gap-x-5">
            <img src="{{Storage::url($category->icon)}}" alt="" class="rounded-2xl object-cover w-[90px] h-[90px]">
            <div class="flex flex-col">
                <h3 class="text-indigo-950 text-xl font-bold">{{$category->name}}</h3>
                <p class="text-slate-500 text-sm">Created at:</p>
                <h3 class="text-indigo-950 text-md font-bold">{{$category->created_at->format('d M Y')}}</h3>
            </div>
        </div>

        <!-- Kanan: Edit dan Delete -->
        <div class="flex flex-row items-center gap-x-3">
            <a href="{{ route('admin.categories.edit', $category) }}" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                Edit
            </a>
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="font-bold py-4 px-6 bg-red-700 text-white rounded-full">
                    Delete
                </button>
            </form>
        </div>
    </div>
@empty
    <p>
        Tidak ada data kategori terbaru.
    </p>
@endforelse


                </div>
            </div>
        </div>
    </div>
</x-app-layout>
