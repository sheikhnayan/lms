@extends('frontend.layouts.app')

@section('content')
<div class="bg-page">
<!-- Page Header Start -->
<header class="page-banner-header blank-page-banner-header gradient-bg position-relative">
    <div class="section-overlay">
        <div class="blank-page-banner-wrap pb-0 min-h-auto">
        </div>
    </div>
</header>
<!-- Page Header End -->

<!-- Cart Page Area Start -->
<section class="thankyou-page-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-7">
                <div class="thankyou-box text-center bg-white px-5 py-5">
                    <img src="{{ asset('frontend/assets/img/thank-you-img.png') }}" alt="img" class="img-fluid">
                    <h5 class="mt-5">{{ __('Thank you for Purchasing') }}</h5>
                </div>
            </div>
            <div class="col-md-12 col-lg-7">
                <div class="thankyou-course-list-area mt-30">
                    <div class="table-responsive">
                        <table class="table bg-white my-courses-page-table">
                            <tbody>
                                <tr>
                                    <td>{{ __('Details') }}</td>
                                    <td>{{ __('Type') }}</td>
                                </tr>
                                @foreach($orderCourses ?? [] as $orderCourse)
                                <tr>
                                    <td class="wishlist-course-item">
                                        <div class="card course-item wishlist-item border-0 d-flex align-items-center">
                                            <div class="course-img-wrap flex-shrink-0 overflow-hidden">
                                                <a href="{{ route('student.my-course.show', @$orderCourse->course->slug) }}"><img src="{{ getImageFile(@$orderCourse->course->image_path) }}" alt="course" class="img-fluid"></a>
                                            </div>
                                            <div class="card-body flex-grow-1">
                                                <h5 class="card-title course-title"><a href="{{ route('student.my-course.show', @$orderCourse->course->slug) }}">{{ @$orderCourse->course->title }}</a></h5>
                                                <p class="card-text instructor-name-certificate font-medium text-uppercase">{{ @$orderCourse->course->instructor->fullname }}
                                                    @if(get_instructor_ranking_level(@$orderCourse->course->instructor->user_id))
                                                        | {{ get_instructor_ranking_level(@$orderCourse->course->instructor->user_id) }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($orderCourse->type == 1)
                                            Course
                                        @elseif($orderCourse->type == 2)
                                            Product
                                        @elseif($orderCourse->type == 3)
                                            Bundle Course
                                        @elseif($orderCourse->type == 4)
                                            Consultation
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                                @foreach($new_consultations ?? [] as $new_consultation)
                                <tr>
                                    <td class="wishlist-course-item">
                                        <div class="card course-item wishlist-item border-0 d-flex align-items-center">
                                            <div class="course-img-wrap flex-shrink-0 overflow-hidden">
                                                <a href=""><img src="{{ getImageFile(@$new_consultation->consultationSlot->user->image_path) }}" alt="course" class="img-fluid"></a>
                                            </div>
                                            <div class="card-body flex-grow-1">
                                                <h5 class="card-title course-title"><a href="">{{ @$new_consultation->consultationSlot->user->instructor->full_name }}</a></h5>
                                                <p class="card-text instructor-name-certificate font-medium text-uppercase">{{ @$new_consultation->consultationSlot->user->instructor->full_namee }}
                                                    {{ @$new_consultation->consultationSlot->user->instructor->professional_title }}
                                                    @if(get_instructor_ranking_level(@$new_consultation->consultationSlot->user->id))
                                                        | {{ get_instructor_ranking_level(@$new_consultation->consultationSlot->user->id) }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Consultation</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('student.my-learning') }}" class="theme-btn theme-button1 theme-button3 w-100 mt-15">{{ __('My Learning') }}</a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('student.my-consultation') }}" class="theme-btn theme-button1 theme-button3 w-100 mt-15">{{ __('My Consultation') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Cart Page Area End -->
</div>
@endsection
