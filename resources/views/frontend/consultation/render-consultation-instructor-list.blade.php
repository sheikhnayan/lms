@forelse($consultationInstructors as $consultationInstructor)
    <!-- consultation-instructor-item start -->
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="card course-item consultation-instructor-item border-0 radius-3 bg-white">
            <div class="course-img-wrap overflow-hidden">
                <div class="consultation-tag position-absolute"><span class="text-white">
                        @if(get_currency_placement() == 'after')
                            {{$consultationInstructor->hourly_rate}}/h {{ get_currency_symbol() }}
                        @else
                            {{ get_currency_symbol() }} {{$consultationInstructor->hourly_rate}}/h
                        @endif
                    </span></div>
                <a href="#">
                    <img src="{{ getImageFile($consultationInstructor->user->image_path) }}" alt="course" class="img-fluid">
                </a>
            </div>
            <div class="card-body position-relative">
                <h5 class="card-title course-title"><a href="{{ route('instructorDetails', [$consultationInstructor->user->id, Str::slug($consultationInstructor->full_name)]) }}">{{ $consultationInstructor->full_name }}</a></h5>
                <p class="card-text instructor-name-certificate font-medium text-uppercase font-12">{{ $consultationInstructor->professional_title }} @if(get_instructor_ranking_level(@$consultationInstructor->user_id))
                        | {{ get_instructor_ranking_level(@$consultationInstructor->user_id) }}
                    @endif </p>
                <div class="course-item-bottom">
                    <div class="course-rating d-flex align-items-center">
                        <span class="font-medium font-14 me-2">
                            <?php
                                $average_rating = getUserAverageRating($consultationInstructor->user_id);
                            ?>
                            {{ number_format(@$average_rating, 1) }}
                        </span>
                        @include('frontend.home.partial.instructor-rating')
                        <span class="rating-count font-14">({{ getInstructorTotalReview($consultationInstructor->user_id) }})</span>
                    </div>
                    @php $hourly_fee = 0; @endphp
                    @if(get_currency_placement() == 'after')
                        @php $hourly_fee = $consultationInstructor->hourly_rate . ' ' . get_currency_symbol() . '/h'; @endphp

                    @else
                        @php $hourly_fee = get_currency_symbol() . ' ' . $consultationInstructor->hourly_rate . '/h'; @endphp

                    @endif
                    <div class="instructor-bottom-item">
                        <button type="button" data-type="{{ $consultationInstructor->available_type }}" data-booking_instructor_user_id="{{ $consultationInstructor->user_id }}"
                                data-hourly_fee="{{ $hourly_fee }}" data-hourly_rate="{{ $consultationInstructor->hourly_rate }}"
                                data-get_off_days_route="{{ route('getOffDays', $consultationInstructor->user_id) }}"
                                class="theme-btn theme-button1 theme-button3 w-100 bookSchedule"
                                data-bs-toggle="modal" data-bs-target="#consultationBookingModal">{{ __('Book Schedule') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- consultation-instructor-item end -->
@empty
    <div class="no-course-found text-center">
        <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
        <h5 class="mt-3">{{ __('No Record Found') }}</h5>
    </div>
@endforelse

<!-- Pagination Start -->
<div class="col-12">
    @if(@$consultationInstructors->hasPages())
        {{ @$consultationInstructors->links('frontend.paginate.paginate') }}
    @endif
</div>
<!-- Pagination End -->
