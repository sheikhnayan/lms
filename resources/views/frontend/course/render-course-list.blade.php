@forelse($courses as $course)
    <!-- Course item start -->
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="card course-item border-0 radius-3 bg-white">
            <div class="course-img-wrap overflow-hidden">
                    <?php
                    $special = @$course->specialPromotionTagCourse->specialPromotionTag->name;
                    ?>
                @if($special)
                    <span class="course-tag badge radius-3 font-12 font-medium position-absolute bg-orange">
                        {{ @$special }}
                    </span>
                @endif
                <a href="{{ route('course-details', $course->slug) }}"><img src="{{getImageFile($course->image_path)}}" alt="course" class="img-fluid"></a>
                <div class="course-item-hover-btns position-absolute">
                    <span class="course-item-btn addToWishlist" data-course_id="{{ $course->id }}" data-route="{{ route('student.addToWishlist') }}" title="Add to Wishlist">
                        <i data-feather="heart"></i>
                    </span>
                    <span class="course-item-btn addToCart" data-course_id="{{ $course->id }}" data-route="{{ route('student.addToCart') }}" title="Add to Cart">
                        <i data-feather="shopping-bag"></i>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <h5 class="card-title course-title"><a href="{{ route('course-details', $course->slug) }}">{{ Str::limit(__($course->title), 40) }}</a></h5>
                <p class="card-text instructor-name-certificate font-medium text-uppercase font-12">
                    {{ @$course->instructor->name }}
                    @if(get_instructor_ranking_level(@$course->instructor->user_id))
                        | {{ get_instructor_ranking_level(@$course->instructor->user_id) }}
                    @endif
                </p>
                <div class="course-item-bottom">
                    <div class="course-rating d-flex align-items-center">
                        <span class="font-medium font-14 me-2">{{ number_format($course->average_rating, 1) }}</span>
                        <ul class="rating-list d-flex align-items-center me-2">
                            @if($course->average_rating >= 1)
                                <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                @if($course->average_rating > 1 && $course->average_rating < 2)
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-half"></span></li>
                                    <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                @elseif($course->average_rating >= 2)
                                    <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    @if($course->average_rating > 2 && $course->average_rating < 3)
                                        <li class="star-full"><span class="iconify" data-icon="bi:star-half"></span></li>
                                        <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                        <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    @elseif($course->average_rating >= 3)
                                        <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                        @if($course->average_rating > 3 && $course->average_rating < 4)
                                            <li class="star-full"><span class="iconify" data-icon="bi:star-half"></span></li>
                                            <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                        @elseif($course->average_rating >= 4)
                                            <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                            @if($course->average_rating > 4 && $course->average_rating < 5)
                                                <li class="star-full"><span class="iconify" data-icon="bi:star-half"></span></li>
                                            @elseif($course->average_rating >= 5)
                                                <li class="star-full"><span class="iconify" data-icon="bi:star-fill"></span></li>
                                            @else
                                                <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                            @endif

                                        @else
                                            <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                            <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                        @endif
                                    @else
                                        <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                        <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                        <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    @endif
                                @else
                                    <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                    <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                @endif
                            @else
                                <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                                <li class=""><span class="iconify" data-icon="bi:star-fill"></span></li>
                            @endif
                        </ul>
                        <span class="rating-count font-14">({{ @$course->reviews->count() }})</span>
                    </div>
                    @if($course->learner_accessibility == 'paid')
                            <?php
                            $startDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->start_date));
                            $endDate = date('d-m-Y H:i:s', strtotime(@$course->promotionCourse->promotion->end_date));
                            $percentage = @$course->promotionCourse->promotion->percentage;
                            $discount_price = number_format($course->price - (($course->price * $percentage) / 100), 2);
                            ?>
                        @if(now()->gt($startDate) && now()->lt($endDate))
                            <div class="instructor-bottom-item font-14 font-semi-bold text-uppercase">
                                {{ __('Price') }}: <span class="color-hover">
                                    @if(get_currency_placement() == 'after')
                                        {{ $discount_price }} {{ get_currency_symbol() }}
                                    @else
                                        {{ get_currency_symbol() }} {{ $discount_price }}
                                    @endif
                                </span>
                                <span class="text-decoration-line-through fw-normal font-14 color-gray ps-3">
                                    @if(get_currency_placement() == 'after')
                                        {{ $course->price }} {{ get_currency_symbol() }}
                                    @else
                                        {{ get_currency_symbol() }} {{ $course->price }}
                                    @endif
                                </span>
                            </div>
                        @else
                            <div class="instructor-bottom-item font-14 font-semi-bold text-uppercase">
                                {{ __('Price') }}: <span class="color-hover">
                                @if(get_currency_placement() == 'after')
                                        {{ $course->price }} {{ get_currency_symbol() }}
                                    @else
                                        {{ get_currency_symbol() }} {{ $course->price }}
                                    @endif
                                </span>
                            </div>
                        @endif
                    @elseif($course->learner_accessibility == 'free')
                        <div class="instructor-bottom-item font-14 font-semi-bold text-uppercase">
                            {{ __('Free') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Course item end -->
@empty
    <div class="no-course-found text-center">
        <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
        <h5 class="mt-3">{{ __('Courses Not Found') }}</h5>
    </div>
@endforelse

<!-- Course item end -->
<!-- Pagination Start -->
<div class="col-12">
    @if(@$courses->hasPages())
        {{ @$courses->links('frontend.paginate.paginate') }}
    @endif
</div>
<!-- Pagination End -->
