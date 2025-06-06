<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
</head>
<body class="font-poppins p-10">
    <h1 class="text-2xl font-bold mb-4">Your Cart</h1>
    <ul class="space-y-4">
        @forelse($items as $item)
            <li class="flex justify-between items-center border-b pb-2">
                <span>{{ $item->course->name }}</span>
                <form action="{{ route('cart.destroy', $item) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600">Remove</button>
                </form>
            </li>
        @empty
            <li>No items in cart.</li>
        @endforelse
    </ul>
</body>
</html>
