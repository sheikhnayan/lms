@extends('frontend_new.layouts.main')

@section('content')
    
<div class="content-wrapper  js-content-wrapper">

  <section class="masthead -type-1 js-mouse-move-container">
    <div class="masthead__bg">
      <img src="{{ asset('frontend_new/img/home-1/hero/bg.png')}}" alt="image">
    </div>

    <div class="container">
      <div data-anim-wrap class="row y-gap-30 justify-between items-end">
        <div class="col-xl-6 col-lg-6 col-sm-10">
          <div class="masthead__content">
            <h1 data-anim-child="slide-up" class="masthead__title">
              {{ __(@$home->banner_first_line_title) }} <br>
              {{ __(@$home->banner_second_line_title) }} <span class="text-green-1 underline">{{ @$home->banner_second_line_changeable_words[0] }}</span>
            </h1>
            <p data-anim-child="slide-up delay-1" class="masthead__text">
              {{ __(@$home->banner_subtitle) }}
            </p>
            <div data-anim-child="slide-up delay-2" class="masthead__buttons row x-gap-10 y-gap-10">
              <div class="col-12 col-sm-auto">
                @if (Route::has('register'))
                    <a data-barba href="{{ route('register') }}" class="button -md -purple-1 text-white">Join For Free</a>
                @endif
              </div>
              <div class="col-12 col-sm-auto">
                <a data-barba href="{{ route('courses') }}" class="button -md -outline-green-1 text-green-1">Find Courses</a>
              </div>
            </div>
            <div data-anim-child="slide-up delay-3" class="masthead-info row y-gap-15 sm:d-none">

              <div class="masthead-info__item d-flex items-center text-white">
                <div class="masthead-info__icon mr-10">
                  <img src="{{ asset('frontend_new/img/masthead/icons/1.svg')}}" alt="icon">
                </div>
                <div class="masthead-info__title lh-1">Over 12 million students</div>
              </div>

              <div class="masthead-info__item d-flex items-center text-white">
                <div class="masthead-info__icon mr-10">
                  <img src="{{ asset('frontend_new/img/masthead/icons/2.svg')}}" alt="icon">
                </div>
                <div class="masthead-info__title lh-1">More than 60,000 courses</div>
              </div>

              <div class="masthead-info__item d-flex items-center text-white">
                <div class="masthead-info__icon mr-10">
                  <img src="{{ asset('frontend_new/img/masthead/icons/3.svg')}}" alt="icon">
                </div>
                <div class="masthead-info__title lh-1">Learn anything online</div>
              </div>

            </div>
          </div>
        </div>

        <div data-anim-child="slide-up delay-5" class="col-xl-6 col-lg-6">
          <div class="masthead-image">
            <div class="masthead-image__el1">
              <img class="js-mouse-move" data-move="40" src="{{ asset('frontend_new/img/masthead/1.png')}}" alt="image">

              <div data-move="30" class="lg:d-none img-el -w-250 px-20 py-20 d-flex items-center bg-white rounded-8 js-mouse-move">
                <div class="size-50 d-flex justify-center items-center bg-red-2 rounded-full">
                  <img src="{{ asset('frontend_new/img/masthead/1.svg')}}" alt="icon">
                </div>
                <div class="ml-20">
                  <div class="text-orange-1 text-16 fw-500 lh-1">3.000 +</div>
                  <div class="mt-3">Free Courses</div>
                </div>
              </div>
            </div>

            <div class="masthead-image__el2">
              <img class="js-mouse-move" data-move="70" src="{{ asset('frontend_new/img/masthead/2.png')}}" alt="image">

              <div data-move="60" class="lg:d-none img-el -w-260 px-20 py-20 d-flex items-center bg-white rounded-8 js-mouse-move">
                <img src="{{ asset('frontend_new/img/masthead/4.png')}}" alt="icon">
                <div class="ml-20">
                  <div class="text-dark-1 text-16 fw-500 lh-1">Ali Tufan</div>
                  <div class="mt-3">UX/UI Designer</div>
                  <div class="d-flex x-gap-5 mt-3">
                    <div>
                      <div class="icon-star text-yellow-1 text-11"></div>
                    </div>
                    <div>
                      <div class="icon-star text-yellow-1 text-11"></div>
                    </div>
                    <div>
                      <div class="icon-star text-yellow-1 text-11"></div>
                    </div>
                    <div>
                      <div class="icon-star text-yellow-1 text-11"></div>
                    </div>
                    <div>
                      <div class="icon-star text-yellow-1 text-11"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="masthead-image__el3">
              <img class="js-mouse-move" data-move="40" src="{{ asset('frontend_new/img/masthead/3.png')}}" alt="image">

              <div data-move="30" class="shadow-4 img-el -w-260 px-30 py-20 d-flex items-center bg-white rounded-8 js-mouse-move">
                <div class="img-el__side">
                  <div class="size-50 d-flex justify-center items-center bg-purple-1 rounded-full">
                    <img src="{{ asset('frontend_new/img/masthead/2.svg')}}" alt="icon">
                  </div>
                </div>
                <div class="">
                  <div class="text-purple-1 text-16 fw-500 lh-1">Congrats!</div>
                  <div class="mt-3">Your Admission Completed</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <svg class="svg-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
      <defs>
        <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
      </defs>
      <g class="svg-waves__parallax">
        <use xlink:href="#gentle-wave" x="48" y="0" />
        <use xlink:href="#gentle-wave" x="48" y="3" />
        <use xlink:href="#gentle-wave" x="48" y="5" />
        <use xlink:href="#gentle-wave" x="48" y="7" />
      </g>
    </svg>
  </section>

  <section class="layout-pt-lg layout-pb-md">
    <div data-anim-wrap class="container">
      <div class="row justify-center">
        <div class="col text-center">
          <p class="text-lg text-dark-1">Trusted by the world’s best</p>
        </div>
      </div>

      <div class="row y-gap-30 justify-between sm:justify-start items-center pt-60 md:pt-50">

        <div data-anim-child="slide-up delay-1" class="col-lg-auto col-md-3 col-sm-4 col-6">
          <div class="d-flex justify-center items-center px-4">
            <img class="w-1/1" src="{{ asset('frontend_new/img/clients/1.svg')}}" alt="clients image">
          </div>
        </div>

        <div data-anim-child="slide-up delay-1" class="col-lg-auto col-md-3 col-sm-4 col-6">
          <div class="d-flex justify-center items-center px-4">
            <img class="w-1/1" src="{{ asset('frontend_new/img/clients/2.svg')}}" alt="clients image">
          </div>
        </div>

        <div data-anim-child="slide-up delay-1" class="col-lg-auto col-md-3 col-sm-4 col-6">
          <div class="d-flex justify-center items-center px-4">
            <img class="w-1/1" src="{{ asset('frontend_new/img/clients/3.svg')}}" alt="clients image">
          </div>
        </div>

        <div data-anim-child="slide-up delay-1" class="col-lg-auto col-md-3 col-sm-4 col-6">
          <div class="d-flex justify-center items-center px-4">
            <img class="w-1/1" src="{{ asset('frontend_new/img/clients/4.svg')}}" alt="clients image">
          </div>
        </div>

        <div data-anim-child="slide-up delay-1" class="col-lg-auto col-md-3 col-sm-4 col-6">
          <div class="d-flex justify-center items-center px-4">
            <img class="w-1/1" src="{{ asset('frontend_new/img/clients/5.svg')}}" alt="clients image">
          </div>
        </div>

        <div data-anim-child="slide-up delay-1" class="col-lg-auto col-md-3 col-sm-4 col-6">
          <div class="d-flex justify-center items-center px-4">
            <img class="w-1/1" src="{{ asset('frontend_new/img/clients/6.svg')}}" alt="clients image">
          </div>
        </div>

      </div>
    </div>
  </section>

  <section class="layout-pt-md layout-pb-md">
    <div data-anim-wrap class="container">
      <div class="row justify-center text-center">
        <div class="col-auto">

          <div class="sectionTitle ">

            <h2 class="sectionTitle__title ">Top Categories</h2>

            <p class="sectionTitle__text ">Lorem ipsum dolor sit amet, consectetur.</p>

          </div>

        </div>
      </div>

      <div class="overflow-hidden pt-50 js-section-slider" data-gap="30" data-loop data-pagination data-slider-cols="xl-6 lg-4 md-2 sm-2">
        <div class="swiper-wrapper">

          @foreach ($featureCategories as $item)
              
          <div class="swiper-slide">
            <div data-anim-child="slide-left delay-2" class="featureCard -type-1 -featureCard-hover">
              <a href="{{ route('category-courses', $item->slug) }}">
                <div class="featureCard__content">
                  <div class="featureCard__icon">
                    <img src="{{ $item->image }}" alt="icon">
                  </div>
                  <div class="featureCard__title">{{ $item->name }}</div>
                  @if ( count($item->courses) > 0 )
                  <div class="featureCard__text">{{ count($item->courses) }}+ Courses</div>
                  @endif
                </div>
              </a>
            </div>
          </div>

          @endforeach

        </div>

        <div class="d-flex justify-center x-gap-15 items-center pt-60 lg:pt-40">
          <div class="col-auto">
            <button class="d-flex items-center text-24 arrow-left-hover js-prev">
              <i class="icon icon-arrow-left"></i>
            </button>
          </div>
          <div class="col-auto">
            <div class="pagination -arrows js-pagination"></div>
          </div>
          <div class="col-auto">
            <button class="d-flex items-center text-24 arrow-right-hover js-next">
              <i class="icon icon-arrow-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="layout-pt-md layout-pb-lg">
    <div data-anim-wrap class="container">
      <div class="row justify-center text-center">
        <div class="col-auto">

          <div class="sectionTitle ">

            <h2 class="sectionTitle__title ">Our Most Popular Courses</h2>

            <p class="sectionTitle__text ">{{ count($courses) }}+ unique online course list designs</p>

          </div>

        </div>
      </div>

      <div class="tabs -pills pt-50 js-tabs">
        <div class="tabs__controls d-flex justify-center x-gap-10 js-tabs-controls">

          <div>
            <button class="tabs__button px-15 py-8 rounded-8 js-tabs-button is-active" data-tab-target=".-tab-item-all" type="button">All Categories</button>
          </div>

          @foreach ($featureCategories as $item)
          
          <div>
            <button class="tabs__button px-15 py-8 rounded-8 js-tabs-button " data-tab-target=".-tab-item-{{ $item->id }}" type="button">{{ $item->name }}</button>
          </div>
              
          @endforeach

        </div>

        <div class="tabs__content pt-60 js-tabs-content">

          <div class="tabs__pane -tab-item-all is-active">
            <div class="row y-gap-30 justify-center">

              @php
                  $courses = [];

                  foreach($featureCategories as $item)
                  {
                    foreach($item->courses as $course)
                    {
                      array_push($courses, $course);
                    }
                  }
              @endphp

              @foreach ($courses as $course)
                  
              <div class="col-lg-3 col-md-6">
                <div data-anim-child="slide-up delay-1">

                  <a href="{{ route('course-details', $course->slug) }}" class="coursesCard -type-1 ">
                    <div class="relative">
                      <div class="coursesCard__image overflow-hidden rounded-8">
                        <img class="w-1/1" src="{{getImageFile($course->image_path)}}" alt="image">
                        <div class="coursesCard__image_overlay rounded-8"></div>
                      </div>
                      <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                        @php
                            $special = @$course->specialPromotionTagCourse->specialPromotionTag->name;
                        @endphp
                          @if ($special)
                              
                          <div>
                            <div class="px-15 rounded-200 bg-purple-1">
                              <span class="text-11 lh-1 uppercase fw-500 text-white">{{ __(@$special) }}</span>
                            </div>
                          </div>

                          @endif

                      </div>
                    </div>

                    <div class="h-100 pt-15">
                      <div class="d-flex items-center">
                        <div class="text-14 lh-1 text-yellow-1 mr-10">{{ number_format($course->average_rating, 1) }}</div>
                        <div class="d-flex x-gap-5 items-center">
                          @if ($course->average_rating >= 1)
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          @elseif ($course->average_rating >= 2)
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          @elseif ($course->average_rating >= 3)
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          @elseif ($course->average_rating >= 4)
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9"></div>
                          @elseif ($course->average_rating >= 5)
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>   
                          @endif
                        </div>
                        <div class="text-13 lh-1 ml-10">({{ $course->reviews->count() }})</div>
                      </div>

                      <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">{{ Str::limit($course->title, 40) }}</div>

                      <div class="coursesCard-footer">
                        <div class="coursesCard-footer__author">
                          {{ @$course->instructor->name }}
                          @if(get_instructor_ranking_level(@$course->instructor->user_id))
                              | {{ get_instructor_ranking_level(@$course->instructor->user_id) }}
                          @endif
                        </div>

                        <div class="coursesCard-footer__price">
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
                          {{-- <div>$179</div>
                          <div>$79</div> --}}

                          @elseif($course->learner_accessibility == 'free')
                              <div class="instructor-bottom-item font-14 font-semi-bold text-uppercase">
                                  {{ __('Free') }}
                              </div>
                          @endif
                        </div>
                      </div>
                    </div>
                  </a>

                </div>
              </div>

              @endforeach

            </div>
          </div>

          @foreach ($featureCategories as $item)
              
          <div class="tabs__pane -tab-item-{{ $item->id }} ">
            <div class="row y-gap-30 justify-center">

              @foreach ($item->courses as $course)
                  
              <div class="col-lg-3 col-md-6">
                <div data-anim-child="slide-up delay-1">

                  <a href="{{ route('course-details', $course->slug) }}" class="coursesCard -type-1 ">
                    <div class="relative">
                      <div class="coursesCard__image overflow-hidden rounded-8">
                        <img class="w-1/1" src="{{getImageFile($course->image_path)}}" alt="image">
                        <div class="coursesCard__image_overlay rounded-8"></div>
                      </div>
                      <div class="d-flex justify-between py-10 px-10 absolute-full-center z-3">
                        @php
                            $special = @$course->specialPromotionTagCourse->specialPromotionTag->name;
                        @endphp
                          @if ($special)
                              
                          <div>
                            <div class="px-15 rounded-200 bg-purple-1">
                              <span class="text-11 lh-1 uppercase fw-500 text-white">{{ __(@$special) }}</span>
                            </div>
                          </div>

                          @endif

                      </div>
                    </div>

                    <div class="h-100 pt-15">
                      <div class="d-flex items-center">
                        <div class="text-14 lh-1 text-yellow-1 mr-10">{{ number_format($course->average_rating, 1) }}</div>
                        <div class="d-flex x-gap-5 items-center">
                          @if ($course->average_rating >= 1)
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          @elseif ($course->average_rating >= 2)
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          @elseif ($course->average_rating >= 3)
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9"></div>
                          <div class="icon-star text-9"></div>
                          @elseif ($course->average_rating >= 4)
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9"></div>
                          @elseif ($course->average_rating >= 5)
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>
                          <div class="icon-star text-9 text-yellow-1"></div>   
                          @endif
                        </div>
                        <div class="text-13 lh-1 ml-10">({{ $course->reviews->count() }})</div>
                      </div>

                      <div class="text-17 lh-15 fw-500 text-dark-1 mt-10">{{ Str::limit($course->title, 40) }}</div>

                      <div class="coursesCard-footer">
                        <div class="coursesCard-footer__author">
                          {{ @$course->instructor->name }}
                          @if(get_instructor_ranking_level(@$course->instructor->user_id))
                              | {{ get_instructor_ranking_level(@$course->instructor->user_id) }}
                          @endif
                        </div>

                        <div class="coursesCard-footer__price">
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
                          {{-- <div>$179</div>
                          <div>$79</div> --}}

                          @elseif($course->learner_accessibility == 'free')
                              <div class="instructor-bottom-item font-14 font-semi-bold text-uppercase">
                                  {{ __('Free') }}
                              </div>
                          @endif
                        </div>
                      </div>
                    </div>
                  </a>

                </div>
              </div>

              @endforeach



            </div>
          </div>

          @endforeach



        </div>
      </div>
    </div>
  </section>

  <section class="layout-pt-lg layout-pb-lg bg-purple-1 {{ @$home->customer_says_area == 1 ? '' : 'd-none' }}">
    <div class="container">
      <div class="row justify-center text-center">
        <div class="col-auto">

          <div class="sectionTitle ">

            <h2 class="sectionTitle__title text-green-1">{{ __(get_option('customer_say_title')) }}</h2>

          </div>

        </div>
      </div>

      <div data-anim-wrap class="js-section-slider pt-50" data-gap="30" data-pagination data-slider-cols="base-3 xl-3 lg-2 md-2 sm-1">
        <div class="swiper-wrapper">

          <div class="swiper-slide">
            <div data-anim-child="slide-left delay-1" class="testimonials -type-1">
              <div class="testimonials__content">
                <h4 class="testimonials__title">{{ __(get_option('customer_say_first_comment_title')) }}</h4>
                <p class="testimonials__text"> “{{ __(get_option('customer_say_first_comment_description') )}}”</p>

                <div class="testimonials-footer">
                  <div class="testimonials-footer__image">
                    <img src="{{ asset('frontend/assets/img/icons-svg/quote.svg') }}" alt="image">
                  </div>

                  <div class="testimonials-footer__content">
                    <div class="testimonials-footer__title">{{ __(get_option('customer_say_first_name')) }}</div>
                    <div class="testimonials-footer__text">{{ __(get_option('customer_say_first_position')) }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="swiper-slide">
            <div data-anim-child="slide-left delay-2" class="testimonials -type-1">
              <div class="testimonials__content">
                <h4 class="testimonials__title">{{ __(get_option('customer_say_second_comment_title')) }}</h4>
                <p class="testimonials__text"> “{{ __(get_option('customer_say_second_comment_description')) }}”</p>

                <div class="testimonials-footer">
                  <div class="testimonials-footer__image">
                    <img src="{{ asset('frontend/assets/img/icons-svg/quote.svg') }}" alt="image">
                  </div>

                  <div class="testimonials-footer__content">
                    <div class="testimonials-footer__title">{{ __(get_option('customer_say_second_name')) }}</div>
                    <div class="testimonials-footer__text">{{ __(get_option('customer_say_second_position')) }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="swiper-slide">
            <div data-anim-child="slide-left delay-3" class="testimonials -type-1">
              <div class="testimonials__content">
                <h4 class="testimonials__title">{{ __(get_option('customer_say_third_comment_title')) }}</h4>
                <p class="testimonials__text"> “{{ __(get_option('customer_say_third_comment_description')) }}”</p>

                <div class="testimonials-footer">
                  <div class="testimonials-footer__image">
                    <img src="{{ asset('frontend/assets/img/icons-svg/quote.svg') }}" alt="image">
                  </div>

                  <div class="testimonials-footer__content">
                    <div class="testimonials-footer__title">{{ __(get_option('customer_say_third_name')) }}</div>
                    <div class="testimonials-footer__text">{{ __(get_option('customer_say_third_position')) }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>


        <div class="d-flex x-gap-20 items-center justify-end pt-60 lg:pt-40">
          <div class="col-auto">
            <button class="button -outline-white text-white size-50 rounded-full d-flex justify-center items-center js-prev">
              <i class="icon icon-arrow-left text-24"></i>
            </button>
          </div>
          <div class="col-auto">
            <button class="button -outline-white text-white size-50 rounded-full d-flex justify-center items-center js-next">
              <i class="icon icon-arrow-right text-24"></i>
            </button>
          </div>
        </div>

      </div>

      <div data-anim-wrap class="row y-gap-30 counter__row {{ @$home->achievement_area == 1 ? '' : 'd-none' }}">

        <div class="col-lg-3 col-sm-6">
          <div data-anim-child="slide-left delay-1" class="counter -type-1">
            <div class="counter__number">{{ __(get_option('achievement_first_subtitle')) }}</div>
            <div class="counter__title">{{ __(get_option('achievement_first_title')) }}</div>
          </div>
        </div>

        <div class="col-lg-3 col-sm-6">
          <div data-anim-child="slide-left delay-2" class="counter -type-1">
            <div class="counter__number">{{ __(get_option('achievement_second_subtitle')) }}</div>
            <div class="counter__title">{{ __(get_option('achievement_second_title')) }}</div>
          </div>
        </div>

        <div class="col-lg-3 col-sm-6">
          <div data-anim-child="slide-left delay-3" class="counter -type-1">
            <div class="counter__number">{{ __(get_option('achievement_third_subtitle')) }}</div>
            <div class="counter__title">{{ __(get_option('achievement_third_title')) }}</div>
          </div>
        </div>

        <div class="col-lg-3 col-sm-6">
          <div data-anim-child="slide-left delay-4" class="counter -type-1">
            <div class="counter__number">{{ __(get_option('achievement_four_subtitle')) }}</div>
            <div class="counter__title">{{ __(get_option('achievement_four_title')) }}</div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <section class="layout-pt-lg layout-pb-lg bg-beige-1">
    <div data-anim-wrap class="container">
      <div class="row y-gap-30 justify-between items-center">
        <div class="col-xl-5 col-lg-6 col-md-10 order-2 order-lg-1">
          <div class="about-content">
            <h2 data-anim-child="slide-up delay-1" class="about-content__title">{{ Str::limit(__(get_option('become_instructor_video_title')), 100) }}</h2>
            {{-- <p data-anim-child="slide-up delay-2" class="about-content__text">Use the list below to bring attention to your product’s key<br> differentiator.</p> --}}
            <div data-anim-child="slide-up delay-3" class="y-gap-20 pt-30">

              <div class="d-flex items-center">
                <p>{{ Str::limit(get_option('become_instructor_video_subtitle'), 450) }}</p>
                <img src="{{ getImageFile(get_option('become_instructor_video_logo')) }}" alt="video" class="position-absolute">
              </div>

            </div>

            <div data-anim-child="slide-up delay-4" class="d-inline-block mt-30">
              <a href="{{ route('student.become-an-instructor') }}" class="button -md -dark-1 text-white">{{ __('Become an Instructor') }}</a>
            </div>
          </div>
        </div>

        <div class="col-xl-5 col-lg-6 order-1 order-lg-2">
          <div data-anim-child="slide-up delay-5" class="about-image">
            <img src="{{ getImageFile(get_option('become_instructor_video_preview_image')) }}" alt="image">
            <button type="button" class="play-btn position-absolute" data-bs-toggle="modal" data-bs-target="#newVideoPlayerModal">
              <img src="{{ asset('frontend/assets/img/icons-svg/play.svg') }}" alt="play">
          </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="layout-pt-lg layout-pb-lg bg-dark-2 {{ @$home->instructor_support_area == 1 ? '' : 'd-none' }}">
    <div data-anim-wrap class="container">
      <div class="row justify-center text-center">
        <div data-anim-child="slide-up delay-1" class="col-auto">

          <div class="sectionTitle ">

            <h2 class="sectionTitle__title text-white">{{ __(@$aboutUsGeneral->instructor_support_title) }}</h2>

            <p class="sectionTitle__text text-white">{{ __(@$aboutUsGeneral->instructor_support_subtitle) }}</p>

          </div>

        </div>
      </div>

      <div class="row y-gap-30 pt-50">

        @foreach($instructorSupports as $key => $instructorSupport)

        <div data-anim-child="slide-up delay-{{ $key + 2 }}" class="col-lg-4 col-md-6">
          <div class="stepCard -type-1 -stepCard-hover">
            <div class="stepCard__content">
              <div class="stepCard__icon">
                <img src="{{ getImageFile($instructorSupport->image_path) }}" alt="support">
              </div>
              <h4 class="stepCard__title">{{ __($instructorSupport->title) }}</h4>
              <p class="stepCard__text"> {{ __($instructorSupport->subtitle) }}</p>
            </div>
          </div>
        </div>

        @endforeach


      </div>
    </div>
  </section>

  <section class="layout-pt-lg layout-pb-lg {{ @$home->instructor_area == 1 ? '' : 'd-none' }}">
    <div data-anim-wrap class="container">
      <div data-anim-child="slide-left delay-1" class="row y-gap-20 justify-between items-center">
        <div class="col-lg-6">

          <div class="sectionTitle ">

            <h2 class="sectionTitle__title ">{{ __(get_option('top_instructor_title')) }}</h2>

            <p class="sectionTitle__text ">{{ __(get_option('top_instructor_subtitle')) }}</p>

          </div>

        </div>

        <div class="col-auto">

          <a href="{{ route('allInstructor') }}" class="button -icon -purple-3 text-purple-1">
            {{ __('View All Instructor') }}
            <i class="icon-arrow-top-right text-13 ml-10"></i>
          </a>

        </div>
      </div>

      <div class="row y-gap-30 pt-50">


        @foreach($instructors as $instructor)
        
        <div class="col-lg-3 col-sm-6">
          <a href="{{ route('instructorDetails', [$instructor->user_id, Str::slug($instructor->name)]) }}">
            <div data-anim-child="slide-left delay-2" class="teamCard -type-1 -teamCard-hover">
              <div class="teamCard__image">
                <img src="{{ getImageFile(@$instructor->user->image_path) }}" alt="image">
              </div>
              <div class="teamCard__content">
                <h4 class="teamCard__title">{{ $instructor->name }}</h4>
                <p class="teamCard__text">{{ @$instructor->professional_title }}</p>

                <div class="row items-center y-gap-10 x-gap-10 pt-10">
                  <div class="col-auto">
                    <div class="d-flex items-center">
                      <div class="icon-star text-yellow-1 text-11 mr-5"></div>
                      <div class="text-14 lh-12 text-yellow-1 fw-500">
                        <?php
                          $average_rating = \App\Models\Course::where('user_id', $instructor->user_id)->avg('average_rating');
                        ?>
                        {{ number_format(@$average_rating, 1) }}
                      </div>
                    </div>
                  </div>

                  <div class="col-auto">
                    <div class="d-flex items-center">
                      <div class="icon-online-learning text-light-1 text-11 mr-5"></div>
                      <div class="text-14 lh-12">{{ @$instructor->user->students->count() }} {{ __('Students') }}</div>
                    </div>
                  </div>

                  <div class="col-auto">
                    <div class="d-flex items-center">
                      <div class="icon-play text-light-1 text-11 mr-5"></div>
                      <div class="text-14 lh-12">{{ @$instructor->user->courses->count() }} {{ __('Courses') }}</div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </a>
        </div>

        @endforeach

      </div>

      <div class="row justify-center text-center pt-60 lg:pt-40">
        <div class="col-auto">
          <p class="lh-1">Want to help people learn, grow and achieve more in life? <a class="text-purple-1 underline" href="{{ route('allInstructor') }}">Become an instructor</a></p>
        </div>
      </div>
    </div>
  </section>

  <section class="layout-pt-lg layout-pb-lg bg-light-3">
    <div data-anim-wrap class="container">
      <div class="row y-gap-20 items-center">
        <div class="col-xl-7 col-lg-7">
          <div data-anim-child="slide-up delay-1" class="app-image">
            <img src="{{ asset('frontend_new/img/app/1.png')}}" alt="image">
          </div>
        </div>

        <div class="col-lg-5">
          <div class="app-content">
            <h2 data-anim-child="slide-up delay-3" class="app-content__title">Learn From<br> <span>Anywhere</span></h2>
            <p data-anim-child="slide-up delay-4" class="app-content__text">Take classes on the go with the educrat app. Stream or download to watch on the plane, the subway, or wherever you learn best.</p>
            <div data-anim-child="slide-up delay-5" class="app-content__buttons">
              <a href="#"><img src="{{ asset('frontend_new/img/app/buttons/1.svg')}}" alt="button"></a>
              <a href="#"><img src="{{ asset('frontend_new/img/app/buttons/2.svg')}}" alt="button"></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- <section class="layout-pt-lg layout-pb-lg">
    <div data-anim-wrap class="container">
      <div data-anim-child="slide-left delay-1" class="row y-gap-20 justify-between items-center">
        <div class="col-lg-6">

          <div class="sectionTitle ">

            <h2 class="sectionTitle__title ">Resources & News</h2>

            <p class="sectionTitle__text ">Lorem ipsum dolor sit amet, consectetur.</p>

          </div>

        </div>

        <div class="col-auto">

          <a href="" class="button -icon -purple-3 text-purple-1">
            Browse Blog
            <i class="icon-arrow-top-right text-13 ml-10"></i>
          </a>

        </div>
      </div>

      <div class="row y-gap-30 pt-50">

        <div data-anim-child="slide-left delay-2" class="col-lg-4 col-md-6">
          <a href="blog-single.html" class="blogCard -type-1">
            <div class="blogCard__image">
              <img src="{{ asset('frontend_new/img/blog/1.png')}}" alt="image">
            </div>
            <div class="blogCard__content">
              <div class="blogCard__category">EDUCATION</div>
              <h4 class="blogCard__title">Eco-Education in Our Lives: We Can Change the Future</h4>
              <div class="blogCard__date">December 16, 2022</div>
            </div>
          </a>
        </div>

        <div data-anim-child="slide-left delay-3" class="col-lg-4 col-md-6">
          <a href="blog-single.html" class="blogCard -type-1">
            <div class="blogCard__image">
              <img src="{{ asset('frontend_new/img/blog/2.png')}}" alt="image">
            </div>
            <div class="blogCard__content">
              <div class="blogCard__category">DESIGN</div>
              <h4 class="blogCard__title">How to design a simple, yet unique and memorable brand identity</h4>
              <div class="blogCard__date">December 16, 2022</div>
            </div>
          </a>
        </div>


        <div class="col-lg-4">
          <div class="row y-gap-30">

            <div class="col-lg-12 col-md-6">
              <a href="#" data-anim-child="slide-left delay-4" class="blogCard -type-2">
                <div class="blogCard__image">
                  <img src="{{ asset('frontend_new/img/blog/small/1.png')}}" alt="image">
                </div>
                <div class="blogCard__content">
                  <div class="blogCard__category">COURSES</div>
                  <h4 class="blogCard__title">Medical Chemistry: The Molecular Basis</h4>
                  <div class="blogCard__date">December 16, 2022</div>
                </div>
              </a>
            </div>

            <div class="col-lg-12 col-md-6">
              <a href="#" data-anim-child="slide-left delay-5" class="blogCard -type-2">
                <div class="blogCard__image">
                  <img src="{{ asset('frontend_new/img/blog/small/2.png')}}" alt="image">
                </div>
                <div class="blogCard__content">
                  <div class="blogCard__category">DEVELOPMENT</div>
                  <h4 class="blogCard__title">Qualification for Students’ Satisfaction Rate</h4>
                  <div class="blogCard__date">December 16, 2022</div>
                </div>
              </a>
            </div>

            <div class="col-lg-12 col-md-6">
              <a href="#" data-anim-child="slide-left delay-6" class="blogCard -type-2">
                <div class="blogCard__image">
                  <img src="{{ asset('frontend_new/img/blog/small/3.png')}}" alt="image">
                </div>
                <div class="blogCard__content">
                  <div class="blogCard__category">LIFESTYLE</div>
                  <h4 class="blogCard__title">Simple Words about Science Complications</h4>
                  <div class="blogCard__date">December 16, 2022</div>
                </div>
              </a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section> --}}

  <section class="layout-pt-md layout-pb-md bg-purple-1">
    <div class="container">
      <div class="row y-gap-20 justify-between items-center">
        <div class="col-xl-4 col-lg-5">
          <h2 class="text-30 lh-15 text-white">
            Join more than <span class="text-green-1">8 million learners</span> worldwide
          </h2>
        </div>

        <div class="col-auto">
          <a href="{{ route('login') }}" class="button -md -green-1 text-dark-1">Start Learning For Free</a>
        </div>
      </div>
    </div>
  </section>

  
@endsection