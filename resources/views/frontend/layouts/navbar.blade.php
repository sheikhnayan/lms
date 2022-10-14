<section class="menu-section-area @auth isLoginMenu @endauth">
    <!-- Navigation -->
    <nav class="navbar sticky-header navbar-expand-lg" id="mainNav">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ getImageFile(get_option('app_logo')) }}" alt="Logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="main-menu-collapse collapse navbar-collapse" id="navbarSupportedContent">

                <div class="header-nav-left-side me-auto d-flex">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item dropdown dropdown-top-space">
                            <a class="nav-link dropdown-toggle" href="#" id="librariesDropdown" data-bs-toggle="dropdown">
                                {{ __('Courses') }}
                            </a>
                            <ul class="dropdown-menu {{selectedLanguage()->rtl == 1 ? 'dropdown-menu-end' : ''}}">
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{ route('category-courses', $category->slug) }}"
                                           class="dropdown-item @if(count($category->subcategories) > 0) dropdown-toggle @endif">{{ $category->name }}</a>
                                        @if(count($category->subcategories) > 0)
                                            <ul class="submenu dropdown-menu">
                                                @foreach($category->subcategories as $subcategory)
                                                    <li><a class="dropdown-item" href="{{ route('subcategory-courses', $subcategory->slug) }}">{{ $subcategory->name }}</a></li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li><a href="{{ route('courses') }}">{{ __('All Courses') }}</a></li>
                            </ul>
                        </li>
                    </ul>

                    <form action="#">
                        <div class="input-group">
                            <button class="input-group-text pe-0" id="basic-addon1"><img src="{{ asset('frontend/assets/img/icons-svg/search.svg') }}" alt="Search"></button>
                            <input class="form-control me-2 searchCourse" type="search" name="keyword" value="{{request('keyword')}}" placeholder="{{__('Search Course')}}..."
                                   aria-label="Search">
                        </div>

                        <!-- Search Bar Suggestion Box Start -->
                        <div class="search-bar-suggestion-box searchBox d-none custom-scrollbar">
                            <ul class="appendCourseSearchList">

                            </ul>
                        </div>
                        <!-- Search Bar Suggestion Box End -->
                    </form>
                </div>
                <div class="header-nav-right-side d-flex">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{__('Pages')}}</a>
                            <ul class="dropdown-menu">
                                @foreach($staticMenus ?? [] as $staticMenu)
                                <li><a class="dropdown-item" href="{{ route( $staticMenu->slug ) }}">{{ __($staticMenu->name)  }}</a></li>
                                @endforeach

                                @foreach($dynamicMenus ?? [] as $dynamicMenu)
                                    <li><a class="dropdown-item" href="{{ route('page', @$dynamicMenu->page->slug) }}">{{ __($dynamicMenu->name)  }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @if(@auth::user()->role == 2 || @auth::user()->role == 3)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('forum.index') }}">{{__('Forum')}}</a>
                            </li>
                            @if(@auth::user()->role == 3 )
                                @if(@auth::user()->instructor)
                                    <li class="nav-item">
                                        <span class="nav-link">{{__('Request Pending')}}</span>
                                    </li>
                                @else
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{route('student.become-an-instructor')}}">{{__('Become an Instructor')}}</a>
                                    </li>
                                @endif
                            @elseif(@auth::user()->role == 2)
                                <!-- Status 1 = Approved,  Status 2 = Blocked,  Status 0 = Pending -->
                                @if(@auth::user()->instructor->status == 1)
                                    @if(\Illuminate\Support\Str::contains(url()->current(), 'instructor'))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{route('student.dashboard')}}">{{__('Student Panel')}}</a>
                                        </li>
                                    @else
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{route('instructor.dashboard')}}">{{__('Instructor Panel')}}</a>
                                        </li>
                                    @endif
                                @elseif(@auth::user()->instructor->status == 2)
                                    <li class="nav-item">
                                        <span class="nav-link">{{__('Blocked From Instructor Panel')}}</span>
                                    </li>
                                @else
                                    <li class="nav-item">
                                        <span class="nav-link">{{__('Request Pending')}}</span>
                                    </li>
                                @endif
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('forum.index') }}">{{__('Forum')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('blogs') }}">{{__('Blog')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="{{ route('contact') }}">{{__('Contact')}}</a>
                            </li>
                        @endif
                        <li class="nav-item dropdown menu-round-btn menu-language-btn dropdown-top-space">
                            <a class="nav-link" href="#">
                                <img src="{{asset(selectedLanguage()->flag)}}" alt="Flag" class="radius-50">
                            </a>
                            <ul class="dropdown-menu {{selectedLanguage()->rtl == 1 ? 'dropdown-menu-start' : 'dropdown-menu-end'}}" data-bs-popper="none">
                                @foreach(appLanguages() as $app_lang)
                                    <li><a class="dropdown-item" href="{{ url('/local/'.$app_lang->iso_code) }}">
                                            <img src="{{asset($app_lang->flag)}}" alt="Flag" class="radius-50">{{$app_lang->language}}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @if(@auth()->user()->role != 1)
                        @if(auth::user())
                            <!-- Menu Notification Option Start -->
                            <li class="nav-item dropdown menu-round-btn menu-notification-btn dropdown-top-space">
                                <a class="nav-link menu-cart-btn" href="#">
                                    <i data-feather="bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                               {{count($student_notifications) + count($instructor_notifications)}}
                              </span>
                                </a>
                                <div class="dropdown-menu {{selectedLanguage()->rtl == 1 ? 'dropdown-menu-start' : 'dropdown-menu-end'}}" data-bs-popper="none">
                                    <ul class="nav nav-pills menu-notification-tab-list" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="pills-student-tab" data-bs-toggle="pill" data-bs-target="#pills-student" type="button" role="tab"
                                                    aria-controls="pills-student" aria-selected="false">{{__('Student')}}</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link " id="pills-instructor-tab" data-bs-toggle="pill" data-bs-target="#pills-instructor" type="button"
                                                    role="tab" aria-controls="pills-instructor" aria-selected="true">{{__('Instructor')}}</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content menu-notification-tab-content custom-scrollbar" id="pills-tabContent">
                                        <div class="tab-pane fade show active" id="pills-student" role="tabpanel" aria-labelledby="pills-student-tab">
                                            @forelse ( $student_notifications as $student_notification)
                                                <!-- Message User Item Start -->
                                                <a href="{{route('notification.url', [$student_notification->uuid])}}" class="message-user-item cursor d-flex align-items-center">
                                                    <div class="message-user-item-left">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <div class="user-img-wrap position-relative radius-50">
                                                                    <img src="{{ asset($student_notification->sender->image_path) }}" alt="img" class="radius-50">
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 {{selectedLanguage()->rtl == 1 ? 'me-2' : 'ms-2' }}">
                                                                <h6 class="color-heading font-14 text-capitalize">{{$student_notification->sender->name}}</h6>
                                                                <p class="font-13">{{$student_notification->text}}</p>
                                                                <div class="font-11 color-gray mt-1">{{@$student_notification->created_at->diffForHumans()}}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                                <!-- Message User Item End -->
                                            @empty
                                                <div class="no-notification-found-box">
                                                    <p class="text-center">{{__('No Data Found')}}</p>
                                                </div>
                                            @endforelse
                                        </div>

                                        <div class="tab-pane fade " id="pills-instructor" role="tabpanel" aria-labelledby="pills-instructor-tab">
                                            @forelse ( $instructor_notifications as $instructor_notification)
                                                <!-- Message User Item Start -->
                                                <a href="{{route('notification.url', [$instructor_notification->uuid])}}" class="message-user-item d-flex align-items-center">
                                                    <div class="message-user-item-left">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0">
                                                                <div class="user-img-wrap position-relative radius-50">
                                                                    <img src="{{ asset($instructor_notification->sender->image_path) }}" alt="img" class="radius-50">
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1 {{selectedLanguage()->rtl == 1 ? 'me-2' : 'ms-2' }}">
                                                                <h6 class="color-heading font-14 text-capitalize">{{$instructor_notification->sender->name}}</h6>
                                                                <p class="font-13">{{$instructor_notification->text}}</p>
                                                                <div class="font-11 color-gray mt-1">{{@$instructor_notification->created_at->diffForHumans()}}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                                <!-- Message User Item End -->
                                            @empty
                                            <div class="no-notification-found-box">
                                                <p class="text-center">{{__('No Data Found')}}</p>
                                            </div>
                                            @endforelse
                                        </div>


                                    </div>
                                </div>
                            </li>
                            <!-- Menu Notification Option End -->
                        @endif

                        <!-- Menu Cart Option Start -->
                        <li class="nav-item menu-round-btn menu-cart-btn">
                            <a class="nav-link menu-cart-btn" aria-current="page" href="{{ route('student.cartList') }}">
                                <i data-feather="shopping-bag"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cartQuantity">
                                {{ @$cartQuantity }}
                              </span>
                            </a>
                        </li>
                        <!-- Menu Cart Option End -->
                        @endif
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
                                <li class="nav-item  menu-sign-in-btn">
                                    <a href="{{ route('login') }}" class="nav-link theme-button1" aria-current="page">{{__('Sign In')}}</a>
                                </li>

                                @if (Route::has('register'))
                                    <li class="nav-item  menu-sign-in-btn">
                                        <a href="{{ route('register') }}" class="nav-link theme-button1" aria-current="page">{{__('Sign Up')}}</a>
                                    </li>
                                @endif
                            @endauth
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navigation -->
</section>

