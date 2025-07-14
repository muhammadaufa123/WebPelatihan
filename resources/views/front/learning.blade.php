<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{asset('css//output.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
</head>
<body class="text-black font-poppins pt-10 pb-[50px]">
    <div id="hero-section" class="max-w-[1200px] mx-auto w-full h-[393px] flex flex-col gap-10 pb-[50px] bg-[url('assets/background/Hero-Banner.png')] bg-center bg-no-repeat bg-cover rounded-[32px] overflow-hidden absolute transform -translate-x-1/2 left-1/2">
        <!-- Navigation Bar -->
        @include('front.partials.nav')
    </div>

    <section id="video-content" class="max-w-[1100px] w-full mx-auto mt-[130px]">
        <div class="video-player relative flex flex-nowrap gap-5">
            <div class="plyr__video-embed w-full overflow-hidden relative rounded-[20px]" id="player">
        <iframe
                    src="https://www.youtube.com/embed/{{ $video->path_video }}?origin=https://plyr.io&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1"
                    allowfullscreen
                    allowtransparency
                    allow="autoplay"
                ></iframe>
            </div>
            <div class="video-player-sidebar flex flex-col shrink-0 w-[330px] h-[470px] bg-[#F5F8FA] rounded-[20px] p-5 gap-5 pb-0 overflow-y-scroll no-scrollbar">
                <p class="font-bold text-lg text-black">{{ $course->course_videos->count() }} Lessons</p>
                <div class="flex flex-col gap-3">
                    <div class="group p-[12px_16px] flex items-center gap-[10px] bg-[#E9EFF3] rounded-full hover:bg-[#3525B3] transition-all">
                        <div class="text-black group-hover:text-white">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M11.97 2C6.45 2 1.97 6.48 1.97 12s4.48 10 10 10 10-4.48 10-10S17.5 2 11.97 2Zm3 12.23-2.9 1.67c-.36.21-.76.31-1.15.31s-.79-.1-1.15-.31c-.72-.42-1.15-1.16-1.15-2V10.55c0-.83.43-1.57 1.15-1.99.72-.42 1.6-.42 2.32 0l2.9 1.67c.72.42 1.15 1.16 1.15 1.99s-.43 1.57-1.15 1.99Z" fill="currentColor"/></svg>
                        </div>
                        <a href="{{ route('front.details', $course ) }}">
                            <p class="font-semibold group-hover:text-white">Course Trailer</p>
                        </a>
                    </div>

                    @foreach($course->modules as $module)
                        <div class="flex flex-col p-5 rounded-2xl bg-[#FFF8F4] has-[.hide]:bg-transparent border-t-4 border-[#FF6129] has-[.hide]:border-0 w-full">
                            <button class="accordion-button flex justify-between gap-1 items-center" data-accordion="module-{{ $module->id }}">
                                <span class="font-semibold text-lg text-left">{{ $module->name }}</span>
                                <div class="arrow w-9 h-9 flex shrink-0">
                                    <img src="{{ asset('assets/icon/add.svg') }}" alt="icon">
                                </div>
                            </button>
                            <div id="module-{{ $module->id }}" class="accordion-content hide">
                                <div class="flex flex-col gap-4 pt-2">
                                    @foreach($module->videos as $video)
                                        @php
                                            $isActive = request()->get('courseVideoId') == $video->id;
                                            $hasAccess = Auth::check() && Auth::user()->hasActiveSubscription($course);
                                        @endphp

                                        @if($hasAccess || $course->price == 0)
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('front.learning', [$course, 'courseVideoId' => $video->id]) }}" class="flex-1 group p-[12px_16px] flex items-center gap-[10px] rounded-full transition-all duration-300 {{ $isActive ? 'bg-[#3525B3] active-video' : 'bg-[#E9EFF3] hover:bg-[#3525B3]' }}">
                                                    <div class="text-black group-hover:text-white {{ $isActive ? 'text-[#3525B3]' : '' }}">▶️</div>
                                                    <p class="font-semibold {{ $isActive ? 'text-[#3525B3]' : 'group-hover:text-white text-black' }}">{{ $video->name }}</p>
                                                </a>
                                                @if(in_array($video->id, $progress->completed_videos ?? []))
                                                    <span class="text-green-600">✔</span>
                                                @else
                                                    <form method="POST" action="{{ route('learning.item.complete', [$course, $video->id]) }}">
                                                        @csrf
                                                        <input type="hidden" name="type" value="video">
                                                        <button type="submit" class="text-xs text-blue-500">Mark as done</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @else
                                            <div class="group p-[12px_16px] flex items-center gap-[10px] bg-[#E9EFF3] rounded-full opacity-50 cursor-not-allowed">
                                                <div class="text-black">🔒</div>
                                                <p class="font-semibold text-black">{{ $video->name }} <span class="text-xs text-red-500">(PRO)</span></p>
                                            </div>
                                        @endif
                                    @endforeach

                                    @foreach($module->materials as $material)
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('materials.download', $material) }}" class="flex-1 group p-[12px_16px] flex items-center gap-[10px] bg-[#E9EFF3] rounded-full hover:bg-[#3525B3] transition-all">
                                                <div class="text-black group-hover:text-white">📄</div>
                                                <p class="font-semibold group-hover:text-white">{{ $material->name }}</p>
                                            </a>
                                            @if(in_array($material->id, $progress->completed_materials ?? []))
                                                <span class="text-green-600">✔</span>
                                            @else
                                                <form method="POST" action="{{ route('learning.item.complete', [$course, $material->id]) }}">
                                                    @csrf
                                                    <input type="hidden" name="type" value="material">
                                                    <button type="submit" class="text-xs text-blue-500">Mark as done</button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach

                                    @foreach($module->tasks as $task)
                                        <div class="flex items-center gap-2">
                                            <div class="group p-[12px_16px] flex items-center gap-[10px] bg-[#E9EFF3] rounded-full">
                                                <div class="text-black">📝</div>
                                                <p class="font-semibold text-black">
                                                    {{ $task->name }}
                                                    @if($task->deadline)
                                                        <span class="text-xs text-gray-500 ml-2">{{ $task->deadline->format('d M Y H:i') }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                            @if(in_array($task->id, $progress->completed_tasks ?? []))
                                                <span class="text-green-600">✔</span>
                                            @else
                                                <a href="{{ route('task.submit.create', $task) }}" class="text-xs text-blue-500">Submit Task</a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="Video-Resources" class="flex flex-col mt-5">
        <div class="max-w-[1100px] w-full mx-auto flex flex-col gap-3">
            <h1 class="title font-extrabold text-[30px] leading-[45px]">{{ $course->name }}</h1>
            <div class="flex items-center gap-5 flex-wrap">
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/icon/crown.svg') }}" alt="icon">
                    <p class="font-semibold">{{ $course->category->name }}</p>
                </div>
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/icon/profile-2user.svg') }}" alt="icon">
                    <p class="font-semibold">{{ $course->trainees->count() }} Trainees</p>
                </div>
                @if($course->mode)
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/icon/book.svg') }}" alt="icon">
                    <p class="font-semibold">{{ $course->mode->name }}</p>
                </div>
                @endif
                @if($course->level)
                <div class="flex items-center gap-[6px]">
                    <img src="{{ asset('assets/icon/3dcube.svg') }}" alt="icon">
                    <p class="font-semibold">{{ $course->level->name }}</p>
                </div>
                @endif
            </div>

            @isset($progress)
            <div class="mt-4 w-full">
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-[#3525B3] h-2.5 rounded-full" style="width: {{ $progress->progress }}%"></div>
                </div>
                <p class="mt-1 text-sm font-medium">{{ $progress->progress }}% completed</p>
            </div>
            @endisset

            <div class="max-w-[1100px] w-full mx-auto mt-10 tablink-container flex gap-3 px-4 sm:p-0 no-scrollbar overflow-x-scroll">
                <div class="tablink font-semibold text-lg h-[47px] transition-all duration-300 cursor-pointer hover:text-[#FF6129]" onclick="openPage('About', this)" id="defaultOpen">About</div>
                <div class="tablink font-semibold text-lg h-[47px] transition-all duration-300 cursor-pointer hover:text-[#FF6129]" onclick="openPage('Rewards', this)">Rewards</div>
                <div class="tablink font-semibold text-lg h-[47px] transition-all duration-300 cursor-pointer hover:text-[#FF6129]" onclick="openPage('Quiz', this)">Quiz</div>
            </div>

            <div class="bg-[#F5F8FA] py-[50px]">
                <div class="max-w-[1100px] w-full mx-auto flex gap-[50px]">
                    <div class="tabs-container w-[700px] flex shrink-0">
                        <div id="About" class="tabcontent hidden">
                            <div class="flex flex-col gap-5 w-[700px] shrink-0">
                                <h3 class="font-bold text-2xl">Grow Your Career</h3>
                                <p class="font-medium leading-[30px]">{{ $course->about }}</p>
                                <div class="grid grid-cols-2 gap-x-[30px] gap-y-5">
                                    @foreach($course->course_keypoints as $keypoint)
                                    <div class="benefit-card flex items-center gap-3">
                                        <img src="{{ asset('assets/icon/tick-circle.svg') }}" class="w-6 h-6" alt="icon">
                                        <p class="font-medium leading-[30px]">{{ $keypoint->name }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div id="Rewards" class="tabcontent hidden">
                            <div class="flex flex-col gap-5 w-[700px] shrink-0">
                                <h3 class="font-bold text-2xl">Rewards</h3>
                                @if(isset($certificate))
                                    <a href="{{ route('certificate.download', $certificate) }}" class="text-white font-semibold rounded-[30px] p-[16px_32px] bg-[#FF6129] transition-all duration-300 hover:shadow-[0_10px_20px_0_#FF612980] w-fit">Download Certificate</a>
                                @else
                                    <p class="font-medium leading-[30px]">Complete all lessons and pass the quiz to earn a certificate.</p>
                                @endif
                            </div>
                        </div>

                        <div id="Quiz" class="tabcontent hidden">
                            <div class="flex flex-col gap-5 w-[700px] shrink-0">
                                <h3 class="font-bold text-2xl">Test Your Knowledge</h3>
                                @if(isset($quizAttempt))
                                    <p class="font-medium leading-[30px]">
                                        Score: {{ $quizAttempt->score }}% – {{ $quizAttempt->is_passed ? 'Passed' : 'Failed' }}
                                    </p>
                                    @unless($quizAttempt->is_passed)
                                        <a href="{{ route('front.quiz', $course) }}" class="text-white font-semibold rounded-[30px] p-[16px_32px] bg-[#FF6129] transition-all duration-300 hover:shadow-[0_10px_20px_0_#FF612980] w-fit">Retake Quiz</a>
                                    @endunless
                                @else
                                    <p class="font-medium leading-[30px]">Quiz content will be displayed here. This section will contain questions related to the current lesson or course.</p>
                                    <a href="{{ route('front.quiz', $course) }}" class="text-white font-semibold rounded-[30px] p-[16px_32px] bg-[#FF6129] transition-all duration-300 hover:shadow-[0_10px_20px_0_#FF612980] w-fit">Start Quiz</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mentor-sidebar flex flex-col gap-[10px] w-full">
                        <div class="mentor-info bg-white flex flex-col gap-4 rounded-2xl p-5">
                            <p class="font-bold text-lg text-left w-full">Teacher</p>
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center gap-3">
                                    <div class="w-[50px] h-[50px] flex shrink-0 rounded-full overflow-hidden">
                                        <img src="{{ $course->trainer->user->avatar_url }}" class="w-full h-full object-cover" alt="photo">
                                    </div>
                                    <div class="flex flex-col gap-[2px]">
                                        <p class="font-semibold">{{ $course->trainer->user->name }}</p>
                                        <p class="text-sm text-[#6D7786]">{{ $course->trainer->user->pekerjaan }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript -->
   <!-- JavaScript -->
   <script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous">
    </script>

    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>  

    <script src="{{ asset('js/main.js') }}"></script>
    </body>
</body>
</html>
<div class="bg-[#F5F8FA] py-[50px] mt-10">
    <div class="max-w-[1100px] w-full mx-auto flex flex-col gap-[70px]">
        <div class="flex gap-[50px]">
            <div class="tabs-container w-[700px] flex shrink-0">
                <div id="About" class="tabcontent">
                    <div class="flex flex-col gap-5 w-[600px] shrink-0">
                        <h3 class="font-bold text-2xl">Grow Your Career</h3>
                        <p class="font-medium leading-[30px]">{{ $course->about }}</p>
                        <div class="grid grid-cols-2 gap-x-[30px] gap-y-5">
                            @foreach($course->course_keypoints as $keypoint)
                            <div class="benefit-card flex items-center gap-3">
                                <img src="{{ asset('assets/icon/tick-circle.svg') }}" class="w-6 h-6" alt="icon">
                                <p class="font-medium leading-[30px]">{{ $keypoint->name }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
