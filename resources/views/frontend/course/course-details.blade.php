@extends('frontend.layouts.app')

@section('meta')
    <meta property="og:title" content="{{ __($course->title) }}">
    <meta property="og:description" content="{{ Str::limit(@$course->description, 150) }}">
    <meta property="og:image" content="{{ getImageFile(@$course->image) }}">
@endsection

@section('content')
<div class="bg-page">
<!-- Course Single Page Header Start -->
<header class="course-single-page-header gradient-bg position-relative">
    <div class="section-overlay">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                    <div class="course-single-banner-content">
                        <h3 class="page-banner-heading text-white pb-30">{{ __($course->title) }}</h3>
                        <p class="page-banner-sub-heading pb-30">{{ __($course->subtitle) }}</p>
                        <p class="instructor-name-certificate font-medium text-uppercase font-12 text-white">
                            <span class="text-decoration-underline">{{ @$course->instructor->name }}</span>
                            @if(get_instructor_ranking_level(@$course->instructor->user_id))
                                | {{ get_instructor_ranking_level(@$course->instructor->user_id) }}
                            @endif
                        </p>

                        <div class="course-rating d-flex align-items-center text-white">
                            <span class="font-medium font-14 me-2">{{ number_format($course->average_rating, 1) }}</span>
                            <ul class="rating-list d-flex align-items-center me-2">
                                @include('frontend.course.render-course-rating')
                            </ul>
                            <span class="rating-count font-14 me-3">({{ $total_user_review }})</span>
                            <span class="rating-count font-14">{{ @$course->orderItems->count() }} {{ __('Students') }}</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Course Single Page Header End -->

<!-- Course Single Details Area Start -->
<section class="course-single-details-area before-login-purchase-course-details">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-12 col-lg-8">

                <div class="course-single-details-left-content bg-white">

                    <!-- Tab panel nav list -->
                    <div class="course-tab-nav-wrap course-details-tab-nav-wrap d-flex justify-content-between">
                        <ul class="nav nav-tabs tab-nav-list border-0" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase active" id="Overview-tab" data-bs-toggle="tab" href="#Overview" role="tab" aria-controls="Overview" aria-selected="true">{{ __('Overview') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase" id="Curriculum-tab" data-bs-toggle="tab" href="#Curriculum" role="tab" aria-controls="Curriculum" aria-selected="false">{{ __('Curriculum') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase" id="Discussion-tab" data-bs-toggle="tab" href="#Discussion" role="tab" aria-controls="Discussion" aria-selected="false">{{ __('Discussion') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase" id="Review-tab" data-bs-toggle="tab" href="#Review" role="tab" aria-controls="Review" aria-selected="false">{{ __('Review') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link text-uppercase" id="Instructor-tab" data-bs-toggle="tab" href="#Instructor" role="tab" aria-controls="Review" aria-selected="false">{{ __('Instructor') }}</a>
                            </li>
                        </ul>
                    </div>
                    <!-- Tab panel nav list -->

                    <!-- Tab Content-->
                    <div class="tab-content" id="myTabContent">
                        @include('frontend.course.partial.partial-overview-tab')
                        @include('frontend.course.partial.partial-curriculum-tab')
                        @include('frontend.course.partial.partial-discussion-tab')
                        @include('frontend.course.partial.partial-review-tab')
                        @include('frontend.course.partial.partial-instructor-tab')
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-4">
                <div class="course-single-details-right-content">
                    <div class="course-info-box bg-white">

                        <div class="video-area-left position-relative d-flex align-items-center justify-content-center">
                            <div class="course-info-video-img"><img src="{{ getImageFile($course->image) }}" alt="video" class="img-fluid"></div>
                            @if($course->intro_video_check == 1 && getVideoFile($course->video))
                            <button type="button" class="play-btn position-absolute" data-bs-toggle="modal" data-bs-target="#newVideoPlayerModal">
                                <img src="{{ asset('frontend/assets/img/icons-svg/play.svg') }}" alt="play">
                            </button>

                            @endif
                            @if($course->intro_video_check == 2 && $course->youtube_video_id)
                            <button type="button" class="play-btn position-absolute" data-bs-toggle="modal" data-bs-target="#newVideoPlayerModal">
                                <img src="{{ asset('frontend/assets/img/icons-svg/play.svg') }}" alt="play">
                            </button>
                            @endif
                        </div>
                        @if($course->learner_accessibility == 'paid')
                            <div class="course-price-box d-flex justify-content-between align-items-center mt-30 mb-30">
                            <?php
                                $startDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->start_date));
                                $endDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->end_date));
                                $percentage = @$course->promotionCourse->promotion->percentage;
                                $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);
                            ?>

                            @if(now()->gt($startDate) && now()->lt($endDate))
                                <div>
                                    <h4 class="d-flex align-items-center mb-1">
                                        @if(get_currency_placement() == 'after')
                                            {{ $discount_price }} {{ get_currency_symbol() }}
                                        @else
                                            {{ get_currency_symbol() }} {{ $discount_price }}
                                        @endif

                                        <span class="text-decoration-line-through fw-normal font-16 color-gray ps-3">
                                        @if(get_currency_placement() == 'after')
                                                {{ $course->price }} {{ get_currency_symbol() }}
                                            @else
                                                {{ get_currency_symbol() }} {{ $course->price }}
                                            @endif
                                        </span>
                                    </h4>
                                    <span class="course-left-duration color-deep-orange font-14"> <span class="iconify me-2 font-18" data-icon="clarity:alarm-clock-line"></span>

                                         {{ getLeftDuration($startDate, $endDate) }} {{ __('Left at this Price') }}!</span>
                                </div>
                                <div class="price-off font-12 font-medium color-hover d-flex align-items-center justify-content-center">{{ @$percentage }}% off</div>
                            @else
                                <div>
                                    <h4 class="d-flex align-items-center mb-1">
                                        @if(get_currency_placement() == 'after')
                                            {{ $course->price }} {{ get_currency_symbol() }}
                                        @else
                                            {{ get_currency_symbol() }} {{ $course->price }}
                                        @endif
                                    </h4>
                                </div>
                            @endif
                        </div>
                        @elseif($course->learner_accessibility == 'free')
                            <div class="course-price-box d-flex justify-content-between align-items-center mt-30 mb-30">
                                <div>
                                    <h4 class="d-flex align-items-center mb-1">  {{ __('Free') }} </h4>
                                </div>
                            </div>
                        @endif

                        <div class="course-includes-box course-includes-box-top">
                            <ul class="pb-30">
                                <li class="d-flex justify-content-between">
                                    <div>
                                        <span class="iconify" data-icon="bytesize:clock"></span>
                                        <span>{{ __('Course Duration') }}</span>
                                    </div>
                                    <div>{{ @$course->VideoDuration }}</div>
                                </li>
                                <li class="d-flex justify-content-between">
                                    <div>
                                        <span class="iconify" data-icon="carbon:increase-level"></span>
                                        <span>{{ __('Course Level') }}</span>
                                    </div>
                                    <div>{{ @$course->difficultyLevel->name }}</div>
                                </li>
                                <li class="d-flex justify-content-between">
                                    <div>
                                        <span class="iconify" data-icon="heroicons-outline:users"></span>
                                        <span>{{ __('Student Enrolled') }}</span>
                                    </div>
                                    <div>{{ @$total_course_students }}</div>
                                </li>
                                <li class="d-flex justify-content-between">
                                    <div>
                                        <span class="iconify" data-icon="cil:language"></span>
                                        <span>{{ __('Language') }}</span>
                                    </div>
                                    <div>{{ @$course->language->name }}</div>
                                </li>
                            </ul>
                        </div>

                        @if($course_exits == 'enrolled')
                            <a href="{{ route('student.my-course.show', $course->slug) }}" class="theme-btn theme-button1 theme-button3 w-100 mb-30 ">
                                {{ __('Go to Course') }}
                                <i data-feather="arrow-right"></i>
                            </a>
                        @elseif($course_exits == 'cartList')
                            <buttton class="theme-btn theme-button1 theme-button3 w-100 mb-30" >
                                {{ __('Added to Cart') }}
                            </buttton>
                        @else
                            <button class="theme-btn theme-button1 theme-button3 w-100 mb-30 addToCart " data-course_id="{{ $course->id }}"
                                    data-route="{{ route('student.addToCart') }}" >
                                <span class="msgInfoChange">{{ __('Enroll the Course') }} <i data-feather="arrow-right"></i></span>

                            </button>
                        @endif


                        <div class="course-info-box-wishlist-btns d-flex mb-30">
                            <button class="theme-btn para-color font-medium mx-2 addToWishlist" title="Add to wishlist" data-course_id="{{ $course->id }}" data-route="{{ route('student.addToWishlist') }}"><span class="iconify me-2" data-icon="bytesize:heart"></span>{{ __('Add to Wishlist') }}</button>
                            <button class="theme-btn para-color font-medium mx-2" title="Share this course" data-bs-toggle="modal" data-bs-target="#shareThisCourseModal"><span class="iconify me-2" data-icon="ci:share-outline"></span>{{ __('Share Course') }}</button>
                        </div>

                        <div class="course-includes-box">
                            <h6 class="pb-30">{{ __('This Course Includes') }}</h6>
                            <ul>
                                <li>
                                    <span class="iconify" data-icon="bi:camera-video"></span>
                                    <span>{{@$course->lectures->count() > 0 ? @$course->video_duration : '0'}} {{ __('Video Lectures') }}</span>
                                </li>
                                <li>
                                    <span class="iconify" data-icon="healthicons:i-exam-multiple-choice-outline"></span>
                                    <span>{{ @$course->quizzes->count() }} {{ __('Quizzes') }}</span>
                                </li>
                                <li>
                                    <span class="iconify" data-icon="bi:book"></span>
                                    <span>{{ @$course->assignments->count() }} {{ __('Assignments') }}</span>
                                </li>
                                <li>
                                    <span class="iconify" data-icon="akar-icons:download"></span>
                                    <span>{{ @$course->resources->count() }} {{ __('Downloadable Resources') }}</span>
                                </li>
                                <li>
                                    <span class="iconify" data-icon="bytesize:clock"></span>
                                    <span>{{ __('Full Lifetime Access') }}</span>
                                </li>
                                <li>
                                    <span class="iconify" data-icon="lucide:award"></span>
                                    <span>{{ __('Certificate of Completion') }}</span>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Course Single Details Area End -->
</div>

<!--Share This Course Modal Start-->
<div class="modal fade" id="shareThisCourseModal" tabindex="-1" aria-labelledby="shareThisCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="shareThisCourseModalLabel">{{ __('Share This Course') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="share-course-list">
                    <li><a href="http://www.facebook.com/sharer.php?u={{ route('course-details', $course->slug) }}"><span class="iconify" data-icon="cib:facebook-f"></span></a></li>
                    <li><a href="https://twitter.com/share?url={{ route('course-details', $course->slug) }}"><span class="iconify" data-icon="el:twitter"></span></a></li>
                    <li><a href="https://www.linkedin.com/shareArticle?url={{ route('course-details', $course->slug) }}"><span class="iconify" data-icon="cib:linkedin-in"></span></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--Share This Course Modal End-->

<input type="hidden" class="courseReviewPaginateRoute" value="{{ route('frontend.reviewPaginate', $course->id) }}">

<!-- New Video Player Modal Start-->
<div class="modal fade VideoTypeModal" id="newVideoPlayerModal" tabindex="-1" aria-labelledby="newVideoPlayerModal" aria-hidden="true">

    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="video-player-area">
                    @if($course->intro_video_check == 1 && getVideoFile($course->video))
                    <!-- HTML 5 Video -->
                    <video id="player2" playsinline controls data-poster="{{ getImageFile($course->image) }}" controlsList="nodownload">
                        <source src="{{ getVideoFile($course->video) }}" type="video/mp4" />
                    </video>
                    @endif
                    @if($course->intro_video_check == 2 && $course->youtube_video_id)
                        <div class="plyr__video-embed" id="youtubePlayer2">
                            <iframe
                                src="https://www.youtube.com/embed/{{ @$course->youtube_video_id }}"
                                allowfullscreen
                                allowtransparency
                                allow="autoplay">
                            </iframe>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- New Video Player Modal End-->

<!--  Text Upload Modal Start -->
<div class="modal fade textUploadModal venoBoxTypeModal" id="textUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <p class="getLectureText"></p>
            </div>
        </div>
    </div>
</div>
<!-- Text Upload Modal End -->

<!-- Image Upload Modal Start -->
<div class="modal fade textUploadModal venoBoxTypeModal" id="imageUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body">
                <img src="" alt="" class="img-fluid getLectureImage">
            </div>
        </div>
    </div>
</div>
<!-- Image Upload Modal End -->

<!-- Slide Show Upload Modal Start-->
<div class="modal fade venoBoxTypeModal" id="slideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <iframe class="getLectureSlide" src="" width="100%" height="400" frameborder="0" scrolling="no"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- Slide Show Upload Modal End-->

<!-- Audio Player Modal Start-->
<div class="modal fade venoBoxTypeModal" id="audioPlayerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-header border-bottom-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span class="iconify" data-icon="akar-icons:cross"></span></button>
    </div>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <!--Audio -->
                <audio class="getLectureAudio" src="" type="audio/mp3" style="width: 550px" controls controlsList="nodownload">
                </audio>
            </div>
        </div>
    </div>
</div>
<!-- Audio Player Modal End-->
@endsection

@push('style')
    <!-- Video Player css -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/video-player/plyr.css') }}">
@endpush

@push('script')
    <script src="{{ asset('frontend/assets/js/course/addToCart.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/course/addToWishlist.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/course/course-review-paginate.js') }}"></script>

    <!--Tinymce js-->
    <script src="{{ asset('frontend/assets/js/tinymce.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/tinymce-script.js') }}"></script>

    <!-- Video Player js -->
    <script src="{{ asset('frontend/assets/vendor/video-player/plyr.js') }}"></script>
    <script>
        // const zai_player = new Plyr('#player');
        // const zai_player2 = new Plyr('#youtubePlayer');
        const zai_player1 = new Plyr('#player2');
        const zai_player3 = new Plyr('#youtubePlayer2');
        // const zai_player4 = new Plyr('#vimeoPlayer');
    </script>

    <script>
        const players = Array.from(document.querySelectorAll('.js-player')).map((p) => new Plyr(p));
    </script>
    <!-- Video Player js -->

    <script>
        "use strict"
        $('.lectureText').on('click', function () {
            var text = $(this).data("lecture_text")
            $('.getLectureText').html(text)
        })

        $('.lectureImage').on('click', function () {
            var image = $(this).data("lecture_image")
            $('.getLectureImage').attr('src', image)
        })

        $('.lectureSlide').on('click', function () {
            var slide = $(this).data("lecture_slide")
            $('.getLectureSlide').attr('src', slide)
        })

        $('.normalVideo').on('click', function () {
            var video = $(this).data("normal_video")
            console.log(video)
            $('.getNormalVideo').attr('src', video)
        })

        $('.lectureAudio').on('click', function () {
            var audio = $(this).data("lecture_audio")
            console.log(audio)
            $('.getLectureAudio').attr('src', audio)
        })
    </script>

@endpush
