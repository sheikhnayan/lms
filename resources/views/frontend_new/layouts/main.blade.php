<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Google fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/base.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

  <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Outlined" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />

  <!-- Stylesheets -->
  <link rel="stylesheet" href="{{ asset('frontend_new/css/vendors.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend_new/css/main.css') }}">

  <!-- FAVICONS -->
  <link rel="icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" type="image/png" sizes="16x16">
  <link rel="shortcut icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" type="image/x-icon">
  <link rel="shortcut icon" href="{{ getImageFile(get_option('app_fav_icon')) }}">

  <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" sizes="72x72" />
  <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" sizes="114x114" />
  <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" sizes="144x144" />
  <link rel="apple-touch-icon-precomposed" type="image/x-icon" href="{{ getImageFile(get_option('app_fav_icon')) }}" />

  <title>Educrat</title>
</head>

<body class="preloader-visible" data-barba="wrapper">
  <!-- preloader start -->
  <div class="preloader js-preloader">
    <div class="preloader__bg"></div>
  </div>
  <!-- preloader end -->


  <main class="main-content  ">

    <header data-anim="fade" data-add-bg="bg-dark-1" class="header -type-1 js-header">


      <div class="header__container">
        <div class="row justify-between items-center">

          <div class="col-auto">
            <div class="header-left">

              <div class="header__logo ">
                <a data-barba href="{{ url('/') }}">
                  <img src="{{ getImageFile(get_option('app_logo')) }}" alt="logo">
                </a>
              </div>


              <div class="header__explore text-green-1 ml-60 xl:ml-30 xl:d-none">
                <a href="#" class="d-flex items-center" data-el-toggle=".js-explore-toggle">
                  <i class="icon icon-explore mr-15"></i>
                  Explore
                </a>

                <div class="explore-content py-25 rounded-8 bg-white toggle-element js-explore-toggle">

                  @foreach ($categories as $item)
                      
                  <div class="explore__item">
                    <a href="{{ route('category-courses', $item->slug) }}" class="d-flex items-center justify-between text-dark-1">
                      {{ $item->name }}
                      @if(count($item->subcategories) > 0)
                      <div class="icon-chevron-right text-11"></div>
                      @endif
                    </a>
                    @if(count($item->subcategories) > 0)
                      <div class="explore__subnav rounded-8">
                        @foreach($item->subcategories as $subcategory)
                          <a class="text-dark-1" href="{{ route('subcategory-courses', $subcategory->slug) }}">{{ $subcategory->name }}</a>
                        @endforeach
                      </div>
                    @endif
                  </div>

                  @endforeach

                  <div class="explore__item border-top-1 pt-15">
                    <a href="{{ route('courses') }}" class="d-flex items-center justify-between text-dark-1">
                      All Courses
                    </a>
                  </div>


                </div>
              </div>

            </div>
          </div>


          <div class="header-menu js-mobile-menu-toggle ">
            <div class="header-menu__content">
              <div class="mobile-bg js-mobile-bg"></div>

              <div class="d-none xl:d-flex items-center px-20 py-20 border-bottom-light">
                @if (!Auth::check())
                <a href="{{ route('login') }}" class="text-dark-1">Log in</a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="text-dark-1 ml-30">Sign Up</a>
                @endif
                @endif
              </div>

              <div class="menu js-navList">
                <ul class="menu__nav text-white -is-active">
                  <li class="menu-item-has-children">
                    <a data-barba href="#">
                      {{__('Pages')}} <i class="icon-chevron-right text-13 ml-10"></i>
                    </a>

                    <ul class="subnav">
                      <li class="menu__backButton js-nav-list-back">
                        <a href="#"><i class="icon-chevron-left text-13 mr-10"></i> {{__('Pages')}}</a>
                      </li>

                      @foreach($staticMenus ?? [] as $staticMenu)
                        <li><a href="{{ route( $staticMenu->slug ) }}">{{ __($staticMenu->name)  }}</a></li>
                      @endforeach
                      @foreach($dynamicMenus ?? [] as $dynamicMenu)
                          <li><a href="{{ route('page', @$dynamicMenu->page->slug) }}">{{ __($dynamicMenu->name)  }}</a></li>
                      @endforeach


                    </ul>
                  </li>
                  @if(@auth::user()->role == 2 || @auth::user()->role == 3)
                      <li>
                          <a data-barba href="{{ route('forum.index') }}">{{__('Forum')}}</a>
                      </li>
                      @if(@auth::user()->role == 3 )
                          @if(@auth::user()->instructor)
                              <li>
                                  <span class="nav-link">{{__('Request Pending')}}</span>
                              </li>
                          @else
                              <li>
                                  <a data-barba href="{{route('student.become-an-instructor')}}">{{__('Become an Instructor')}}</a>
                              </li>
                          @endif
                      @elseif(@auth::user()->role == 2)
                          <!-- Status 1 = Approved,  Status 2 = Blocked,  Status 0 = Pending -->
                          @if(@auth::user()->instructor->status == 1)
                              @if(\Illuminate\Support\Str::contains(url()->current(), 'instructor'))
                                  <li>
                                      <a data-barba href="{{route('student.dashboard')}}">{{__('Student Panel')}}</a>
                                  </li>
                              @else
                                  <li>
                                      <a data-barba href="{{route('instructor.dashboard')}}">{{__('Instructor Panel')}}</a>
                                  </li>
                              @endif
                          @elseif(@auth::user()->instructor->status == 2)
                              <li>
                                  <span class="nav-link">{{__('Blocked From Instructor Panel')}}</span>
                              </li>
                          @else
                              <li>
                                  <span class="nav-link">{{__('Request Pending')}}</span>
                              </li>
                          @endif
                      @endif
                  @else
                      <li>
                          <a data-barba href="{{ route('forum.index') }}">{{__('Forum')}}</a>
                      </li>
                      <li>
                          <a data-barba href="{{ route('blogs') }}">{{__('Blog')}}</a>
                      </li>
                      <li>
                          <a data-barba href="{{ route('contact') }}">{{__('Contact')}}</a>
                      </li>
                  @endif
                </ul>
              </div>

              <div class="mobile-footer px-20 py-20 border-top-light js-mobile-footer">
                <div class="mobile-footer__number">
                  <div class="text-17 fw-500 text-dark-1">Call us</div>
                  <div class="text-17 fw-500 text-purple-1">800 388 80 90</div>
                </div>

                <div class="lh-2 mt-10">
                  <div>329 Queensberry Street,<br> North Melbourne VIC 3051, Australia.</div>
                  <div>hi@educrat.com</div>
                </div>

                <div class="mobile-socials mt-10">

                  <a href="#" class="d-flex items-center justify-center rounded-full size-40">
                    <i class="fa fa-facebook"></i>
                  </a>

                  <a href="#" class="d-flex items-center justify-center rounded-full size-40">
                    <i class="fa fa-twitter"></i>
                  </a>

                  <a href="#" class="d-flex items-center justify-center rounded-full size-40">
                    <i class="fa fa-instagram"></i>
                  </a>

                  <a href="#" class="d-flex items-center justify-center rounded-full size-40">
                    <i class="fa fa-linkedin"></i>
                  </a>

                </div>
              </div>
            </div>

            <div class="header-menu-close" data-el-toggle=".js-mobile-menu-toggle">
              <div class="size-40 d-flex items-center justify-center rounded-full bg-white">
                <div class="icon-close text-dark-1 text-16"></div>
              </div>
            </div>

            <div class="header-menu-bg"></div>
          </div>


          <div class="col-auto">
            <div class="header-right d-flex items-center">
              <div class="header-right__icons text-white d-flex items-center">

                  <div class="">
                    <button class="d-flex items-center text-white" data-el-toggle=".js-search-toggle">
                      <i class="text-20 icon icon-search"></i>
                    </button>
  
                    <div class="toggle-element js-search-toggle">
                      <div class="header-search pt-90 bg-white shadow-4">
                        <div class="container">
                          <div class="header-search__field">
                            <div class="icon icon-search text-dark-1"></div>
                            <input type="text" class="col-12 text-18 lh-12 text-dark-1 fw-500 searchCourse" value="{{request('keyword')}}" placeholder="{{__('Search Course')}}...">
  
                            <button class="d-flex items-center justify-center size-40 rounded-full bg-purple-3" data-el-toggle=".js-search-toggle">
                              <img src="{{ asset('frontend_new/img/menus/close.svg') }}" alt="icon">
                            </button>
                          </div>
  
                          <div class="header-search__content mt-30 searchBox d-none"> 
                            <div class="d-flex y-gap-5 flex-column mt-20 results">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="header-search__bg" data-el-toggle=".js-search-toggle"></div>
                    </div>
                  </div>


                <div class="relative ml-30 xl:ml-20">
                  <a href="{{ route('student.cartList') }}" class="d-flex items-center text-white menu-cart-btn" data-el-toggle=".js-cart-toggle">
                    <i class="text-20 icon icon-basket"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cartQuantity">
                      {{ @$cartQuantity }}
                    </span>
                  </a>
                </div>


                <div class="d-none xl:d-block ml-20">
                  <button class="text-white items-center" data-el-toggle=".js-mobile-menu-toggle">
                    <i class="text-11 icon icon-mobile-menu"></i>
                  </button>
                </div>

              </div>

              @if (Route::has('login'))
              @auth
                  <!-- Menu User Dropdown Menu Option Start -->
                  <li class="nav-item dropdown menu-round-btn menu-user-btn dropdown-top-space">
                      <a class="nav-link" href="#">
                          <img src="{{asset(auth::user()->image_path)}}" alt="user" class="radius-50">
                      </a>
                      <div class="dropdown-menu {{selectedLanguage()->rtl == 1 ? 'dropdown-menu-start' : 'dropdown-menu-end'}}" data-bs-popper="none">

                          <!-- Dropdown User Info Item Start -->
                          <div class="dropdown-user-info">
                              <div class="message-user-item d-flex align-items-center">
                                  <div class="message-user-item-left">
                                      <div class="d-flex align-items-center">
                                          <div class="flex-shrink-0">
                                              <div class="user-img-wrap position-relative radius-50">
                                                  <img src="{{asset(auth::user()->image_path)}}" alt="img" class="radius-50">
                                              </div>
                                          </div>
                                          <div class="flex-grow-1 {{selectedLanguage()->rtl == 1 ? 'me-2' : 'ms-2' }}">
                                              <h6 class="color-heading font-14 text-capitalize">{{auth::user()->name}}</h6>
                                              <p class="font-13">{{auth::user()->email}}</p>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <!-- Dropdown User Info Item End -->
                          @if(@auth()->user()->role != 1)
                          <ul class="user-dropdown-item-box">
                              <li><a class="dropdown-item" href="{{ route('student.my-learning') }}"><span class="iconify"
                                                                                                            data-icon="akar-icons:book"></span>{{__('My Learning')}}
                                  </a></li>
                              <li>
                                  <a class="dropdown-item" href="{{ route('student.my-consultation') }}">
                                      <span class="iconify mr-15" data-icon="ic:round-support-agent"></span> {{ __('My Consultation') }}
                                  </a>
                              </li>

                              <li><a class="dropdown-item" href="{{ route('student.wishlist') }}"><span class="iconify"
                                                                                                        data-icon="bx:bx-heart"></span>{{__('Wishlist')}}</a></li>
                          </ul>
                          <ul class="user-dropdown-item-box">
                              <li>
                                  <a class="dropdown-item" href="{{ route('student.profile') }}"><span class="iconify" data-icon="bytesize:settings"></span>
                                      {{ __('Profile Settings') }}</a></li>
                          </ul>
                          @endif
                          @if(@auth()->user()->role == 1)
                              <ul class="user-dropdown-item-box">
                                  <li>
                                      <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--bxs" width="1em" height="1em" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24" data-icon="bxs:dashboard"><path fill="currentColor" d="M4 13h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1zm-1 7a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v4zm10 0a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-7a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v7zm1-10h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1z"></path></svg>
                                          {{ __('Admin Dashboard') }}
                                      </a>
                                  </li>
                              </ul>
                          @endif
                          <ul class="user-dropdown-item-box">
                              <li><a class="dropdown-item" href="{{ route('support-ticket-faq') }}"><span class="iconify"
                                                                                                          data-icon="bx:bx-help-circle"></span>{{__('Help Support')}}
                                  </a></li>
                              <li><a class="dropdown-item" href="{{route('logout')}}"><span class="iconify" data-icon="ic:round-logout"></span>{{__('Logout')}}</a>
                              </li>
                          </ul>
                      </div>
                  </li>
                  <!-- Menu User Dropdown Menu Option End -->
              @else

                  <div class="header-right__buttons d-flex items-center ml-30 md:d-none">
                    <a href="{{ route('login') }}" class="button -underline text-white">Log in</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="button -sm -white text-dark-1 ml-30">Sign up</a>
                    @endif
                  </div>
                @endauth
              @endif
            </div>
          </div>

        </div>
      </div>
    </header>

    @yield('content')

    <footer class="footer -type-1 bg-dark-1 -green-links">
        <div class="container">
          <div class="footer-header">
            <div class="row y-gap-20 justify-between items-center">
              <div class="col-auto">
                <div class="footer-header__logo">
                  <img src="{{asset(get_option('app_logo'))}}" alt="logo">
                  <p>{{ __(get_option('footer_quote')) }}</p>
                </div>
              </div>
              <div class="col-auto">
                <div class="footer-header-socials">
                  <div class="footer-header-socials__title text-white">Follow us on social media</div>
                  <div class="footer-header-socials__list">
                    <a href="{{get_option('facebook_url')}}"><i class="icon-facebook"></i></a>
                    <a href="{{get_option('twitter_url')}}"><i class="icon-twitter"></i></a>
                    <a href="{{get_option('pinterest_url')}}"><i class="fa fa-pinterest"></i></a>
                    <a href="{{get_option('linkedin_url')}}"><i class="icon-linkedin"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="footer-columns">
            <div class="row y-gap-30">
              <div class="col-xl-4 col-lg-4 col-md-6">
                <div class="text-17 fw-500 text-white uppercase mb-25">{{__('Company')}}</div>
                <div class="d-flex y-gap-10 flex-column">
                  <a href="{{ route('about') }}">{{ __('About')  }}</a>
                  <a href="{{ route('faq') }}">{{__('FAQ')}}</a>
                  <a href="{{ route('blogs') }}">{{ __('Blogs') }}</a>
                </div>
              </div>

              <div class="col-xl-4 col-lg-8">
                <div class="text-17 fw-500 text-white uppercase mb-25">CATEGORIES</div>
                <div class="row justify-between y-gap-20">
                  @foreach ($categories->chunk(7) as $category)
                  <div class="col-md-6">
                    <div class="d-flex y-gap-10 flex-column">
                      @foreach ($category as $item)
                      <a href="{{ route('category-courses', $item->slug) }}">{{ $item->name }}</a>
                      @endforeach
                    </div>
                  </div>
                  @endforeach

                </div>
              </div>

              <div class="col-xl-4 col-lg-4 col-md-6" style="text-align: center">
                <div class="text-17 fw-500 text-white uppercase mb-25">{{__('Support')}}</div>
                <div class="d-flex y-gap-10 flex-column">
                  <a href="{{ route('contact') }}">{{  __('Contact')  }}</a>
                  <a href="{{ route('support-ticket-faq') }}">{{  __('Support')  }}</a>
                  <a href="{{ route('courses') }}">{{ __('Courses')  }}</a>
                </div>
              </div>
            </div>
          </div>

          <div class="py-30 border-top-light-15">
            <div class="row justify-between items-center y-gap-20">
              <div class="col-auto">
                <div class="d-flex items-center h-100 text-white">
                  {{ __(get_option('app_copyright')) }}
                </div>
              </div>

              <div class="col-auto">
                <div class="d-flex x-gap-20 y-gap-20 items-center flex-wrap">
                  <div>
                    <div class="d-flex x-gap-15 text-white">
                      <a href="{{ route('student.become-an-instructor') }}">{{__('Become Instructor')}}</a>
                      <a href="{{ route('privacy-policy') }}">{{__('Privacy Policy')}}</a>
                      <a href="{{ route('cookie-policy') }}">{{__('Cookie Policy')}}</a>
                    </div>
                  </div>

                  <div>
                    <a href="#" class="button px-30 h-50 -dark-6 rounded-200 text-white">
                      <i class="icon-worldwide text-20 mr-15"></i><span class="text-15">English</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </footer>


    </div>
  </main>

  <!-- JavaScript -->
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
  <script src="{{ asset('frontend_new/js/vendors.js') }}"></script>
  <script src="{{ asset('frontend_new/js/main.js') }}"></script>
  <input type="hidden" id="base_url" value="{{url('/')}}">
  <!-- Start:: Navbar Search  -->
  <input type="hidden" class="search_route" value="{{ route('search-course.list') }}">
  <script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
  <script src="{{ asset('frontend/assets/js/custom/search-course.js') }}"></script>
</body>

</html>