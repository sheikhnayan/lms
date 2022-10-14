@extends('frontend.layouts.app')

@section('content')
    <div class="bg-page">

        <!-- Page Header Start -->
        <header class="page-banner-header gradient-bg position-relative">
            <div class="section-overlay">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12">
                            <div class="page-banner-content text-center">
                                <h3 class="page-banner-heading text-white pb-15">{{ __('About Instructor') }}</h3>

                                <!-- Breadcrumb Start-->
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb justify-content-center">
                                        <li class="breadcrumb-item font-14"><a href="{{ url('/') }}">{{ __('Home') }}</a></li>
                                        <li class="breadcrumb-item font-14 active" aria-current="page">{{ __('About Instructor') }}</li>
                                    </ol>
                                </nav>
                                <!-- Breadcrumb End-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Page Header End -->

        <!-- Instructor Details Area Start -->
        <section class="instructor-details-area section-t-space">
            <div class="container">
                <div class="row instructor-details-main-row">
                    <div class="col-12 col-md-12 col-lg-8 col-xl-9">
                        <div class="instructor-details-left-content">

                            <!-- about instructor box -->
                            <div class="instructor-details-left-inner-box about-instructor-box bg-white radius-3">
                                <h5 class="instructor-details-inner-title">{{ __('About') }} {{ @$userInstructor->name }}</h5>
                                <p>{{ @$userInstructor->instructor->about_me }}</p>
                            </div>

                            <!-- Certificate and awards -->
                            <div class="instructor-details-left-inner-box certificate-awards-box bg-white radius-3">
                                <div class="row">
                                    <div class="col-md-6 certificate-awards-inner">
                                        <h5 class="instructor-details-inner-title">{{ __('Certifications') }}</h5>
                                        <ul>
                                            @foreach(@$userInstructor->instructor->certificates as $certificate)
                                                <li class="font-15"><span class="color-heading">{{ $certificate->passing_year }}</span>{{ $certificate->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="col-md-6 certificate-awards-inner">
                                        <h5 class="instructor-details-inner-title">{{ __('Awards') }}</h5>
                                        <ul>
                                            @foreach(@$userInstructor->instructor->awards as $award)
                                                <li class="font-15"><span class="color-heading">{{ $award->winning_year }}</span>{{ $award->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- My others courses -->
                            <div class="instructor-details-left-inner-box my-others-courses bg-white radius-3">
                                <h5 class="instructor-details-inner-title">{{ __('My courses') }} ({{ @$userInstructor->courses->count() }} )</h5>
                                <div class="row" id="appendInstructorCourses">
                                    @include('frontend.instructor.render-instructor-courses')

                                </div>
                                @if(count($loadMoreButtonShowCourses) > 6)
                                <!-- Load More Button-->
                                <div class="d-block" id="loadMoreBtn"><button type="button" class="theme-btn theme-button2 load-more-btn loadMore">{{ __('Load More') }} <i data-feather="arrow-right"></i></button></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-12 col-lg-4 col-xl-3">
                        <div class="instructor-details-right-content radius-3">
                            <div class="course-info-box instructor-info-box bg-white">

                                <div class="instructor-details-right-img-box text-center">
                                    <div class="instructor-details-avatar-wrap radius-50 overflow-hidden mx-auto">
                                        <img src="{{ getImageFile($userInstructor->image_path) }}" alt="img" class="radius-50">
                                    </div>
                                    <h6 class="instructor-details-name">{{ $userInstructor->instructor->name }}</h6>
                                    <p class="instructor-details-designation text-uppercase font-12 font-semi-bold">{{ $userInstructor->instructor->professional_title }}</p>
                                </div>

                                <div class="course-includes-box p-0">
                                    <ul>
                                        @if(get_instructor_ranking_level(@$userInstructor->instructor->user_id))
                                        <li>
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--fa6-solid" width="1.25em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 640 512" data-icon="fa6-solid:ranking-star"><path fill="currentColor" d="M406.1 61.65c9.3 1.44 13.3 12.94 6.5 19.76l-38 36.69l9 52c.5 9.4-8.3 16.6-16.9 12.3l-46.5-24.5l-46.9 24.8c-8.6 4.3-18.3-2.9-16.9-12.2l9-52.1l-38-36.99c-6.8-6.82-2.8-18.32 6.5-19.76l52.3-7.54l23.6-47.778c4.3-8.621 16.5-8.262 20.4 0l23.6 47.778l52.3 7.54zM384 256c17.7 0 32 14.3 32 32v192c0 17.7-14.3 32-32 32H256c-17.7 0-32-14.3-32-32V288c0-17.7 14.3-32 32-32h128zm-224 64c17.7 0 32 14.3 32 32v128c0 17.7-14.3 32-32 32H32c-17.67 0-32-14.3-32-32V352c0-17.7 14.33-32 32-32h128zm288 96c0-17.7 14.3-32 32-32h128c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32H480c-17.7 0-32-14.3-32-32v-64z"></path></svg>
                                            <span>{{ get_instructor_ranking_level(@$userInstructor->instructor->user_id) }} (Ranking)</span>
                                        </li>
                                        @endif
                                        <li>
                                            <span class="iconify" data-icon="dashicons:book"></span>
                                            <span>{{ @$userInstructor->courses->count() }} {{ __('Courses') }}</span>
                                        </li>
                                        <li>
                                            <span class="iconify" data-icon="bi:camera-video"></span>
                                            <span>{{@$total_lectures}} {{ __('Video Lectures') }}</span>
                                        </li>
                                        <li>
                                            <span class="iconify" data-icon="healthicons:i-exam-multiple-choice-outline"></span>
                                            <span>{{ @$total_quizzes }} {{ __('Quizzes') }}</span>
                                        </li>
                                        <li>
                                            <span class="iconify" data-icon="bi:book"></span>
                                            <span>{{ @$total_assignments }} {{ __('Assignments') }}</span>
                                        </li>
                                        <li>
                                            <span class="iconify" data-icon="bi:star"></span>
                                            <span>{{ $total_rating }} Reviews ({{ number_format(@$average_rating, 1) }} average)</span>
                                        </li>
                                        <li>
                                            <span class="iconify" data-icon="codicon:globe"></span>
                                            <span>{{ @$userInstructor->instructor->address }}</span>
                                        </li>
                                    </ul>
                                </div>
                                @php
                                    $social_link = json_decode(@$userInstructor->instructor->social_link);
                                @endphp

                                <div class="instructor-social mt-25">
                                    <ul class="d-flex align-items-center">
                                        <li>
                                            <a href="{{@$userInstructor->instructor->social_link ? $social_link->facebook : ''}}">
                                                <span class="iconify" data-icon="ant-design:facebook-filled"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{@$userInstructor->instructor->social_link ? $social_link->twitter : ''}}">
                                                <span class="iconify" data-icon="ant-design:twitter-square-filled"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{@$userInstructor->instructor->social_link ? $social_link->linkedin : ''}}">
                                                <span class="iconify" data-icon="ant-design:linkedin-filled"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{@$userInstructor->instructor->social_link ? $social_link->pinterest : ''}}">
                                                <span class="iconify" data-icon="fa-brands:pinterest-square" data-width="1em" data-height="1em"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                @if(@$userInstructor->instructor->consultation_available == 1)
                                    @php $hourly_fee = 0; @endphp
                                    @if(get_currency_placement() == 'after')
                                        @php $hourly_fee = @$userInstructor->instructor->hourly_rate . ' ' . get_currency_symbol() . '/h'; @endphp
                                    @else
                                        @php $hourly_fee = get_currency_symbol() . ' ' . @$userInstructor->instructor->hourly_rate . '/h'; @endphp
                                    @endif
                                    <div class="instructor-bottom-item mt-25">
                                        <button type="button" data-type="{{ @$userInstructor->instructor->available_type }}" data-booking_instructor_user_id="{{ @$userInstructor->instructor->user_id }}"
                                                data-hourly_fee="{{ $hourly_fee }}" data-hourly_rate="{{ @$userInstructor->instructor->hourly_rate }}"
                                                data-get_off_days_route="{{ route('getOffDays', @$userInstructor->instructor->user_id) }}"
                                                class="theme-btn theme-button1 theme-button3 w-100 bookSchedule"
                                                data-bs-toggle="modal" data-bs-target="#consultationBookingModal">{{ __('Book Schedule') }}
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Instructor Details Area End -->

    </div>
    <input type="hidden" value="3" class="course_paginate_number">
    <input type="hidden" class="instructorCoursePaginateRoute" value="{{ route('instructorCoursePaginate', $userInstructor->id) }}">

    @include('frontend.home.partial.consultation-booking-schedule-modal')
@endsection

@push('script')
    <script src="{{ asset('frontend/assets/js/course/addToCart.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/course/addToWishlist.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom/instructor-course-review-paginate.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom/booking.js') }}"></script>
@endpush
