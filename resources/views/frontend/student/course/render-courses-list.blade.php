@if(count($orderItems) > 0)
    <div class="table-responsive">
        <table class="table bg-white my-courses-page-table">
            <thead>
            <tr>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Course')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Author')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Price')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Order ID')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Progress')}}</th>
                <th scope="col" class="color-gray font-15 font-medium">{{__('Action')}}</th>
            </tr>
            </thead>
            <tbody>

            @foreach($orderItems as $orderItem)
                @if($orderItem->course_id)
                <tr>
                    <td class="wishlist-course-item">
                        <div class="card course-item wishlist-item border-0 d-flex align-items-center">
                            <div class="course-img-wrap flex-shrink-0 overflow-hidden">
                                <?php
                                $special = @$course->specialPromotionTagCourse->specialPromotionTag->name;
                                ?>
                                @if($special)
                                    <span class="course-tag badge radius-3 font-12 font-medium position-absolute bg-orange">
                                        {{ @$special }}
                                    </span>
                                @endif
                                <a href="{{ route('student.my-course.show', @$orderItem->course->slug) }}"><img src="{{ getImageFile(@$orderItem->course->image_path) }}" alt="course" class="img-fluid"></a>
                            </div>
                            <div class="card-body flex-grow-1">
                                <h5 class="card-title course-title"><a href="{{ route('student.my-course.show', @$orderItem->course->slug) }}">{{ @$orderItem->course->title }}</a></h5>
                                <div class="card-text font-medium text-capitalize font-13 mb-1">
                                    <button class="color-gray2 me-2 font-medium bg-transparent border-0 my-course-give-a-review-btn star-full my-learning-give-review courseReview" data-course_id="{{ @$orderItem->course->id }}" data-bs-toggle="modal" data-bs-target="#writeReviewModal"><span class="iconify me-1" data-icon="bi:star-fill"></span>{{ __('Give Review') }}</button>
                                    <a href="{{route('student.download-invoice', [@$orderItem->id])}}" class="color-gray2 me-2 my-learning-invoice"><img src="{{ asset('frontend/assets/img/courses-img/invoice-icon.png') }}" alt="report" class="me-1">{{__('Invoice')}}</a>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="wishlist-price font-15 color-heading">{{ @$orderItem->course->instructor->name }}</td>
                    <td class="wishlist-price font-15 color-heading">
                        @if($orderItem->unit_price > 0)
                            @if(get_currency_placement() == 'after')
                                {{ $orderItem->unit_price }} {{ get_currency_symbol() }}
                            @else
                                {{ get_currency_symbol() }} {{ $orderItem->unit_price }}
                            @endif

                        @else
                            {{ __('Free') }}
                        @endif
                    </td>
                    <td class="wishlist-price font-15 color-heading">{{@$orderItem->order->order_number}}</td>

                    <td class="wishlist-price font-15 color-heading">
                        <div class="review-progress-bar-wrap">
                            <!-- Progress Bar -->
                            <div class="barras">
                                <div class="progress-bar-box">
                                    <div class="progress-hint-value font-14 color-heading">{{number_format(studentCourseProgress(@$orderItem->course->id), 2)}}%</div>
                                    <div class="barra">
                                        <div class="barra-nivel" data-nivel="{{number_format(studentCourseProgress(@$orderItem->course->id), 2)}}%" style="width: {{number_format(studentCourseProgress(@$orderItem->course->id), 2)}}%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="wishlist-add-to-cart-btn">
                        <a href="{{ route('student.my-course.show', @$orderItem->course->slug) }}" class="theme-button theme-button1 theme-button3 font-13">{{ __('View Course') }}</a>
                    </td>
                </tr>
                @endif
            @endforeach

            </tbody>
        </table>
    </div>
@else
    <!-- If there is no data Show Empty Design Start -->
    <div class="empty-data">
        <img src="{{ asset('frontend/assets/img/empty-data-img.png') }}" alt="img" class="img-fluid">
        <h4 class="my-3">{{ __('Empty Course') }}</h4>
    </div>
    <!-- If there is no data Show Empty Design End -->
@endif
<!-- Pagination Start -->
@if(@$orderItems->hasPages())
    {{ @$orderItems->links('frontend.paginate.paginate') }}
@endif
<!-- Pagination End -->
