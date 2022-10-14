<div class="instructor-profile-left-part bg-white">
    <nav class="account-page-menu">
        <ul>
            <li><a href="{{route('instructor.dashboard')}}" class="{{active_if_full_match('instructor/dashboard')}} {{ @$navDashboardActiveClass }}"><span class="iconify mr-15" data-icon="feather:home"></span>{{__('Dashboard')}}</a></li>
            <li><a href="{{route('instructor.course.create')}}" class="{{active_if_full_match('instructor/course/create')}} {{ @$navCourseUploadActiveClass }}"><span class="iconify mr-15" data-icon="feather:upload"></span>{{__('Upload Course')}}</a></li>
            <li><a href="{{route('instructor.course')}}" class="{{active_if_full_match('instructor/course')}} {{ @$navCourseActiveClass }}" ><span class="iconify mr-15" data-icon="ion:log-in-outline"></span>{{__('My Courses')}}</a></li>
            <li><a href="{{ route('instructor.bundle-course.index') }}" class="{{ @$navBundleCourseActiveClass }}" ><span class="iconify mr-15" data-icon="clarity:bundle-line"></span>{{__('Bundles Courses')}}</a></li>

            <li><a href="{{ route('instructor.all-student') }}" class="{{ @$navAllStudentActiveClass }}"><span class="iconify mr-15" data-icon="ph:student"></span>{{__('All Students')}}</a></li>
            <li><a href="{{ route('notice-board.course-notice.index') }}" class="{{ @$navNoticeBoardActiveClass }}"><span class="iconify mr-15" data-icon="ep:data-board"></span>{{__('Notice Board')}}</a></li>
            <li><a href="{{ route('live-class.course-live-class.index') }}" class="{{ @$navLiveClassActiveClass }}"><span class="iconify mr-15" data-icon="fluent:live-24-regular"></span>{{__('Live Class')}}</a></li>
            <li class="menu-has-children current-menu-item {{@$navConsultationActiveClass}}">
                <span class="toggle-account-menu">
                    <span class="iconify" data-icon="fontisto:angle-down"></span>
                </span>
                <a href="#" class="{{@$navConsultationActiveClass}}"><span class="iconify mr-15" data-icon="ic:round-support-agent"></span>{{ __('Consultation') }}</a>
                <ul class="account-sub-menu">
                    <li><a href="{{ route('instructor.consultation.dashboard') }}" class="{{@$subNavConsultationDashboardActiveClass}}">{{ __('Dashboard') }}</a></li>
                    <li><a href="{{ route('instructor.bookingRequest') }}" class="{{ @$subNavBookingRequestActiveClass }}">{{ __('Booking Request') }}</a></li>
                    <li><a href="{{ route('instructor.bookingHistory') }}" class="{{ @$subNavBookingHistoryActiveClass }}">{{ __('Booking History') }}</a></li>
                </ul>
            </li>
            <li><a href="{{route('instructor.certificate.index')}}" class="{{ @$navCertificateActiveClass }}" ><span class="iconify mr-15" data-icon="fluent:certificate-20-regular"></span>{{__('Certificate')}}</a></li>

            <li><a href="{{route('discussion.index')}}" class="{{ @$navDiscussionActiveClass }}" ><span class="iconify mr-15" data-icon="octicon:comment-discussion-24"></span>{{__('Discussion')}}</a></li>
            <li class="menu-has-children current-menu-item {{@$navFinanceActiveClass}}">
                <span class="toggle-account-menu">
                    <span class="iconify" data-icon="fontisto:angle-down"></span>
                </span>
                <a href="#" class="{{@$navFinanceActiveClass}}"><span class="iconify mr-15" data-icon="system-uicons:heart-rate"></span>{{__('Finance')}}</a>
                <ul class="account-sub-menu">
                    <li><a href="{{ route('finance.analysis.index') }}" class="{{ @$subNavAnalysisActiveClass }}">{{__('Analysis')}}</a></li>
                    <li><a href="{{ route('finance.withdraw-history.index') }}" class="{{ @$subNavWithdrawActiveClass }}">{{__('Withdraw History')}}</a></li>
                </ul>
            </li>
            <li><a href="{{route('instructor.profile')}}" class="{{active_if_full_match('instructor/profile')}}" ><span class="iconify mr-15" data-icon="bx:bx-user"></span>{{__('Profile')}}</a></li>
            <li><a href="{{route('instructor.my-card')}}" class="{{ @$navPaymentActiveClass }}" ><span class="iconify mr-15" data-icon="carbon:settings"></span>{{ __('Payment Settings') }}</a></li>
            <li><a href="{{route('instructor.zoom-setting.update')}}" class="{{ @$navZoomSettingActiveClass }}" ><span class="iconify mr-15" data-icon="carbon:settings"></span>{{ __('Zoom Settings') }}</a></li>
        </ul>
    </nav>
</div>
