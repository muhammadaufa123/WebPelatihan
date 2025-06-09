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
    <div style="background-image: url('{{ asset('assets/background/Hero-Banner.png') }}');" id="hero-section"
        class="max-w-[1200px] mx-auto w-full h-[393px] flex flex-col gap-10 pb-[50px] bg-center bg-no-repeat bg-cover rounded-[32px] overflow-hidden absolute transform -translate-x-1/2 left-1/2">
        
        <!-- Navigation Bar -->
@include('front.partials.nav')
        </nav>
    </div>

    <section id="video-content" class="max-w-[1100px] w-full mx-auto mt-[130px] flex flex-col gap-8">
        <div class="plyr__video-embed w-full overflow-hidden relative rounded-[20px]" id="player">
            <iframe src="https://www.youtube.com/embed/{{ $course->path_video }}?origin=https://plyr.io&iv_load_policy=3&modestbranding=1&playsinline=1&showinfo=0&rel=0&enablejsapi=1" allowfullscreen allowtransparency allow="autoplay" class="w-full h-[400px] sm:h-[500px]"></iframe>
        </div>

        <div class="video-player-sidebar flex flex-col w-full bg-[#F5F8FA] rounded-[20px] p-6 gap-5 max-h-[500px] overflow-y-auto">
            <p class="font-bold text-lg text-black">{{ $course->course_videos->count() }} Lessons</p>
            <div class="flex flex-col gap-4">
                <div class="group p-[12px_16px] flex items-center gap-[10px] bg-[#E9EFF3] rounded-full hover:bg-[#3525B3] transition-all">
                    <div class="text-black group-hover:text-white">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M11.97 2C6.45 2 1.97 6.48 1.97 12s4.48 10 10 10 10-4.48 10-10S17.52 2 11.97 2Zm3 12.23-2.9 1.67c-.36.21-.76.31-1.15.31s-.79-.1-1.15-.31c-.72-.42-1.15-1.16-1.15-2V10.55c0-.83.43-1.57 1.15-1.99.72-.42 1.6-.42 2.32 0l2.9 1.67c.72.42 1.15 1.16 1.15 1.99s-.43 1.57-1.15 1.99Z" fill="currentColor"/></svg>
                    </div>
                    <a href="{{ route('front.details', $course ) }}">
                        <p class="font-semibold group-hover:text-white">Course Trailer</p>
                    </a>
                </div>

                @foreach($course->modules as $module)
                    <p class="font-semibold mt-4">{{ $module->name }}</p>
                    @foreach($module->videos as $video)
                        @php
                            $isActive = request()->get('courseVideoId') == $video->id;
                            $hasAccess = Auth::check() && Auth::user()->hasActiveSubscription($course);
                            $completedVideos = $progress->completed_videos ?? [];
                        @endphp

                        @if($hasAccess || $course->price == 0)
                            <a href="{{ route('front.learning', [$course, 'courseVideoId' => $video->id]) }}" class="group p-[12px_16px] flex items-center gap-[10px] rounded-full transition-all duration-300 {{ $isActive ? 'bg-[#3525B3]' : 'bg-[#E9EFF3] hover:bg-[#3525B3]' }}">
                                <div class="text-black group-hover:text-white {{ $isActive ? 'text-white' : '' }}">
                                    ▶️
                                </div>
                                <p class="font-semibold {{ $isActive ? 'text-white' : 'group-hover:text-white text-black' }}">
                                    {{ $video->name }}
                                </p>
                                @if(in_array($video->id, $completedVideos))
                                    <span class="ml-auto text-green-500">✓</span>
                                @endif
                            </a>
                        @else
                            <div class="group p-[12px_16px] flex items-center gap-[10px] bg-[#E9EFF3] rounded-full opacity-50 cursor-not-allowed">
                                <div class="text-black">🔒</div>
                                <p class="font-semibold text-black">{{ $video->name }} <span class="text-xs text-red-500">(PRO)</span></p>
                            </div>
                        @endif
                    @endforeach

                    @if($module->materials->count())
                        <p class="font-medium mt-2 ml-4">Materials:</p>
                        <ul class="ml-8 flex flex-col gap-1">
                            @foreach($module->materials as $material)
                                <li>
                                    <a href="{{ Storage::url($material->file_path) }}" class="text-blue-500" download>{{ $material->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @if(isset($module->tasks) && count($module->tasks))
                        <p class="font-medium mt-2 ml-4">Tasks:</p>
                        <ul class="ml-8 flex flex-col gap-1">
                            @foreach($module->tasks as $task)
                                <li>
                                    {{ $task->name }}
                                    @if($task->is_completed ?? false)
                                        <span class="ml-2 text-green-500">✓</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section id="Video-Resources" class="flex flex-col mt-5">
        <div class="max-w-[1100px] w-full mx-auto flex flex-col gap-3">
            <h1 class="title font-extrabold text-[30px] leading-[45px]">{{$course->name}}</h1>
            <div class="flex items-center gap-5">
                <div class="flex items-center gap-[6px]">
                    <img src="{{asset('assets/icon/crown.svg')}}" alt="icon">
                    <p class="font-semibold">{{$course->category->name}}</p>
                </div>
                <div class="flex items-center gap-[6px]">
                    <img src="{{asset('assets/icon/award-outline.svg')}}" alt="icon">
                    <p class="font-semibold">Certificate</p>
                </div>
                <div class="flex items-center gap-[6px]">
                    <img src="{{asset('assets/icon/profile-2user.svg')}}" alt="icon">
                    <p class="font-semibold">{{$course->trainees->count()}} Trainees</p>
                </div>
            </div>

            <div class="max-w-[1100px] w-full mx-auto mt-10 tablink-container flex gap-3 px-4 sm:p-0 no-scrollbar overflow-x-scroll">
                <div class="tablink font-semibold text-lg h-[47px] cursor-pointer hover:text-[#FF6129]" onclick="openPage('About', this)" id="defaultOpen">About</div>
                <div class="tablink font-semibold text-lg h-[47px] cursor-pointer hover:text-[#FF6129]" onclick="openPage('Rewards', this)">Rewards</div>
                <div class="tablink font-semibold text-lg h-[47px] cursor-pointer hover:text-[#FF6129]" onclick="openPage('Quiz', this)">Quiz</div>
            </div>

            <div class="w-full bg-[#F5F8FA] py-[50px]">
                <div class="max-w-[1100px] w-full mx-auto flex flex-wrap lg:flex-nowrap gap-[50px] px-4 sm:px-0">
                    <div class="tabs-container w-full max-w-[700px] flex flex-col">
                        <div id="About" class="tabcontent hidden">
                            <div class="flex flex-col gap-5">
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
                            <div class="flex flex-col gap-5">
                                <h3 class="font-bold text-2xl">Rewards</h3>
                                @if(isset($certificate))
                                    <a href="{{ route('certificate.download', $certificate) }}" class="text-white font-semibold rounded-[30px] p-[16px_32px] bg-[#FF6129] transition-all duration-300 hover:shadow-[0_10px_20px_0_#FF612980] w-fit">Download Certificate</a>
                                @else
                                    <p class="font-medium leading-[30px]">Complete all lessons and pass the quiz to earn a certificate.</p>
                                @endif
                            </div>
                        </div>

                        <div id="Quiz" class="tabcontent hidden">
                            <div class="flex flex-col gap-5">
                                <h3 class="font-bold text-2xl">Test Your Knowledge</h3>
                                <p class="font-medium leading-[30px]">
                                    Quiz content will be displayed here. This section will contain questions related to the current lesson or course.
                                </p>
                                <!-- Placeholder for quiz elements -->
                                <a href="{{ route('front.quiz', $course) }}" class="text-white font-semibold rounded-[30px] p-[16px_32px] bg-[#FF6129] transition-all duration-300 hover:shadow-[0_10px_20px_0_#FF612980] w-fit">Start Quiz</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1"></div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function openPage(pageName, elmnt) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("text-[#FF6129]");
            }
            document.getElementById(pageName).style.display = "block";
            elmnt.classList.add("text-[#FF6129]");
        }

        document.getElementById("defaultOpen").click();
    </script>
</body>
</html>
