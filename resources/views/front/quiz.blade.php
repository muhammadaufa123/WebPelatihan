<!doctype html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="{{asset('css//output.css')}}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
</head>
<body class="text-black font-poppins pt-10 pb-[50px]">

    <!-- HERO SECTION -->
    <div style="background-image: url('{{ asset('assets/background/Hero-Banner.png') }}');"
        id="hero-section"
        class="max-w-[1200px] mx-auto w-full flex flex-col gap-10 bg-center bg-no-repeat bg-cover rounded-[32px] overflow-hidden">

        <!-- Navigation Bar -->
@include('front.partials.nav')
        </nav>
    </div>

        <!-- Page Title -->
        <div class="flex flex-col gap-[10px] items-center mt-8"> {{-- Added mt-8 for spacing --}}
            <h2 class="font-bold text-[40px] leading-[60px] text-gray-800">Course Quiz</h2> {{-- Changed text-white to text-gray-800 --}}
            @if(isset($course)) {{-- Jika $course juga dikirim ke view --}}
                <p class="text-xl text-gray-700">{{ $course->name }}</p> {{-- Changed text-white to text-gray-700 --}}
            @endif
        </div>

        <!-- Quiz Content Section -->
        <div class="flex flex-col items-center justify-center px-[50px] md:px-[100px] relative z-10 mt-8"> {{-- Removed text-white, Added mt-8 for spacing --}}
            <div class="w-full bg-white/20 backdrop-blur-md p-8 rounded-2xl">
                <h3 class="font-bold text-2xl mb-6 text-center text-gray-800">{{ $quiz->title ?? 'Judul Quiz' }}</h3> {{-- Changed to text-gray-800 --}}

                @if(session('result'))
                    <div class="mb-6 p-4 rounded-lg {{ session('result.passed') ? 'bg-green-500/70' : 'bg-red-500/70' }} text-white text-center">
                        <h4 class="font-bold text-xl">Hasil Kuis Anda</h4>
                        <p>Skor: {{ session('result.score') }}%</p>
                        <p>{{ session('result.passed') ? 'Selamat, Anda Lulus!' : 'Maaf, Anda belum lulus. Coba lagi nanti.' }}</p>
                        <a href="{{ route('front.details', $quiz->course->slug) }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                            Kembali ke Kursus
                        </a>
                    </div>
                @endif

                @if(isset($quiz) && $quiz->questions->count() > 0 && !session('result'))
                    <form action="{{ route('learning.quiz.submit', ['quiz' => $quiz->id]) }}" method="POST">
                        @csrf
                        {{-- Konten form (pertanyaan dan opsi) --}}
                        @foreach($quiz->questions as $index => $question)
                            <div class="mb-8 p-6 bg-white/30 rounded-lg shadow">
                                <p class="block text-xl font-semibold mb-3 text-gray-800">{{ $index + 1 }}. {{ $question->question }}</p>
                                @if($question->options && $question->options->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($question->options as $option)
                                            <label class="block"> {{-- Mengubah label menjadi block untuk styling --}}
                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="sr-only peer" required> {{-- sr-only untuk menyembunyikan radio button asli, 'peer' untuk state --}}
                                                <div class="p-4 rounded-md border border-gray-400 peer-checked:bg-[#FF6129] peer-checked:border-[#FF6129] peer-checked:text-white text-gray-700 bg-white/70 hover:bg-white/90 hover:border-gray-500 cursor-pointer transition-all duration-200 ease-in-out">
                                                    {{-- Pastikan $option->text ada dan berisi teks opsi --}}
                                                    <span class="font-medium">{{ $option->option_text ?? 'Opsi tidak valid' }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-gray-500">Tidak ada opsi jawaban untuk pertanyaan ini.</p>
                                @endif
                            </div>
                        @endforeach

                        <button type="submit" class="w-full mt-8 p-[20px_32px] bg-[#FF6129] text-white rounded-full text-center font-semibold transition-all duration-300 hover:shadow-[0_10px_20px_0_#FF612980]">
                            Kirim Jawaban
                        </button>
                    </form>
                @elseif(!session('result'))
                    <p class="text-center text-lg text-gray-700">Belum ada pertanyaan untuk quiz ini atau quiz tidak ditemukan.</p> {{-- Changed to text-gray-700 --}}
                @endif
            </div>
        </div>

        <div class="flex justify-center absolute transform -translate-x-1/2 left-1/2 bottom-0 w-full">
            <img src="{{ asset('assets/background/alqowy.svg') }}" alt="background">
        </div>
    </div>

    <!-- JavaScript -->
    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/main.js') }}"></script>
    {{-- Add any specific JS for your quiz page if needed --}}

</body>
</html>