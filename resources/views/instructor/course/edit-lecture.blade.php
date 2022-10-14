@extends('layouts.instructor')

@section('breadcrumb')
    <div class="page-banner-content text-center">
        <h3 class="page-banner-heading text-white pb-15"> {{__('Upload Course')}} </h3>

        <!-- Breadcrumb Start-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item font-14"><a href="{{route('instructor.dashboard')}}">{{__('Dashboard')}}</a></li>
                <li class="breadcrumb-item font-14"><a href="{{ route('instructor.course') }}">{{__('My Courses')}}</a></li>
                <li class="breadcrumb-item font-14 active" aria-current="page">{{__('Upload Course')}}</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="instructor-profile-right-part instructor-upload-course-box-part">
        <div class="instructor-upload-course-box">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div id="msform">
                            <!-- progressbar -->
                            <ul id="progressbar" class="upload-course-item-block d-flex align-items-center justify-content-center">
                                <li class="active" id="account"><strong>{{ __('Course Overview') }}</strong></li>
                                <li class="active"  id="personal"><strong>{{ __('Upload Video') }}</strong></li>
                                <li id="confirm"><strong>{{ __('Submit Process') }}</strong></li>
                            </ul>

                            <!-- Upload Course Step-1 Item Start -->
                            <div class="upload-course-step-item upload-course-overview-step-item">
                                <!-- Upload Course Step-2 Item Start -->
                                <div class="upload-course-step-item upload-course-video-step-item">
                                    <form method="POST" action="{{route('update.lecture', [$lecture->uuid])}}" class="row g-3 needs-validation" novalidate enctype="multipart/form-data">
                                    @csrf
                                    <!-- Upload Course Video-4 start -->
                                        <div id="upload-course-video-4" >
                                            <div class="upload-course-item-block course-overview-step1 radius-8">

                                                <div class="row mb-30">
                                                    <div class="col-md-12">
                                                        <div class="d-flex">
                                                            <div class="label-text-title color-heading font-medium font-16 mb-3 mr-15">Type: </div>
                                                            <div>
                                                                <label class="mr-15"><input type="radio" name="type" value="video" {{$lecture->type == 'video' ? 'checked' : '' }}  class="lecture-type"> Upload Video</label>
                                                                <label class="mr-15"><input type="radio" name="type" value="youtube" {{$lecture->type == 'youtube' ? 'checked' : '' }} class="lecture-type"> Youtube </label>
                                                                <label class="mr-15"><input type="radio" name="type" value="vimeo" {{$lecture->type == 'vimeo' ? 'checked' : '' }} class="lecture-type">  Vimeo</label>
                                                                <label class="mr-15"><input type="radio" name="type" value="text" {{$lecture->type == 'text' ? 'checked' : '' }} class="lecture-type" id="lectureTypeText"> Text </label>
                                                                <label class="mr-15"><input type="radio" name="type" value="image" {{$lecture->type == 'image' ? 'checked' : '' }} class="lecture-type" id="lectureTypeImage"> Image </label>
                                                                <label class="mr-15"><input type="radio" name="type" value="pdf" {{$lecture->type == 'pdf' ? 'checked' : '' }} class="lecture-type" id="lectureTypePDF"> PDF </label>
                                                                <label class="mr-15"><input type="radio" name="type" value="slide_document" {{$lecture->type == 'slide_document' ? 'checked' : '' }} class="lecture-type" id="lectureTypeSlideDocument"> Slide Document </label>
                                                                <label class="mr-15"><input type="radio" name="type" value="audio" {{$lecture->type == 'audio' ? 'checked' : '' }} class="lecture-type" id="lectureTypeAudio"> Audio </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="video" class="{{$lecture->type == 'video' ? '' : 'd-none' }}">
                                                    <div class="upload-course-video-4-wrap upload-introduction-box-content-left d-flex align-items-center flex-column">
                                                        <div class="upload-introduction-box-content-img mb-3">
                                                            <img src="{{asset('frontend/assets/img/instructor-img/upload-lesson-icon.png')}}" alt="upload">
                                                        </div>
                                                        <input type="hidden" id="file_duration" name="file_duration" value="{{ $lecture->file_duration }}">
                                                        <input type="file" name="video_file" accept="video/mp4" class="form-control" id="video_file" title="Upload lesson">
                                                    </div>

                                                    <p class="font-14 color-gray text-center mt-3 pb-30">{{ __('No file selected') }} (MP4 or WMV)</p>
                                                    @if ($errors->has('video_file'))
                                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('video_file') }}</span>
                                                    @endif
                                                </div>

                                                <div id="youtube" class="{{$lecture->type == 'youtube' ? '' : 'd-none' }}">
                                                    <input type="text" name="youtube_url_path" id="youtube_url_path"  value="{{$lecture->type == 'youtube' ? $lecture->url_path : ''}}" class="form-control youtube-url" placeholder="Type Your Youtube Video ID">
                                                    @if ($errors->has('youtube_url_path'))
                                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('youtube_url_path') }}</span>
                                                    @endif
                                                </div>

                                                <div id="vimeo" class="{{$lecture->type == 'vimeo' ? '' : 'd-none' }}">
                                                    <div class="upload-course-video-4-wrap upload-introduction-box-content-left d-flex align-items-center flex-column">
                                                        <div class="upload-introduction-box-content-img mb-3">
                                                            <img src="{{asset('frontend/assets/img/instructor-img/upload-lesson-icon.png')}}" alt="upload">
                                                        </div>
                                                        <input type="file" name="vimeo_url_path" accept="video/mp4" class="form-control " id="vimeo_url_path" title="Upload lesson">
                                                    </div>
                                                    @if ($errors->has('vimeo_url_path'))
                                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('vimeo_url_path') }}</span>
                                                    @endif
                                                </div>

                                                <div id="text" class="{{$lecture->type == 'text' ? '' : 'd-none' }}">
                                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Lesson Description') }} <span class="text-danger">*</span></label>
                                                    <textarea name="text_description" id="summernote" class="textDescription" cols="30" rows="10">{{ $lecture->text }}</textarea>
                                                </div>

                                                <div id="imageDiv" class="{{$lecture->type == 'image' ? '' : 'd-none' }}">
                                                    <div class="row align-items-center">
                                                        <div class="col-12">
                                                            <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Lesson Image') }} <span class="text-danger">*</span></label>
                                                        </div>
                                                        <div class="col-md-6 mb-30">
                                                            <div class="upload-img-box mt-3 height-200">
                                                                @if($lecture->image)
                                                                    <img src="{{ getImageFile($lecture->image) }}">
                                                                @else
                                                                    <img src="" alt="">
                                                                @endif
                                                                <input type="file" name="image" id="image" accept="image/*" onchange="previewFile(this)">
                                                                <div class="upload-img-box-icon">
                                                                    <i class="fa fa-camera"></i>
                                                                    <p class="m-0">{{__('Image')}}</p>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('image'))
                                                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('image') }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6 mb-30">
                                                            <p class="font-14 color-gray">{{ __('Preferable image size') }}: (1MB)</p>
                                                            <p class="font-14 color-gray">{{ __('Preferable filetype') }}: jpg, jpeg, png</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="pdfDiv" class="{{$lecture->type == 'pdf' ? '' : 'd-none' }}">
                                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Upload PDF') }} <span class="text-danger">*</span></label>
                                                    <div class="upload-course-video-4-wrap upload-introduction-box-content-left d-flex align-items-center flex-column">
                                                        <div class="upload-introduction-box-content-img mb-3">
                                                            <img src="{{asset('frontend/assets/img/instructor-img/upload-lesson-icon.png')}}" alt="upload">
                                                        </div>
                                                        <input type="file" name="pdf" accept="application/pdf" class="form-control" value="" id="pdf" title="Upload lesson pdf" >
                                                    </div>
                                                </div>
                                                <div id="slide_documentDiv" class="{{$lecture->type == 'slide_document' ? '' : 'd-none' }}">
                                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Write Your Slide Embed Code') }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="slide_document" class="form-control" value="{{ $lecture->slide_document }}" id="powerpoint" title="Upload lesson slide_document" >
                                                </div>
                                                <div id="audioDiv" class="{{$lecture->type == 'audio' ? '' : 'd-none' }}">
                                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Upload Audio') }} <span class="text-danger">*</span></label>
                                                    <div class="upload-course-video-4-wrap upload-introduction-box-content-left d-flex align-items-center flex-column">
                                                        <div class="upload-introduction-box-content-img mb-3">
                                                            <img src="{{asset('frontend/assets/img/instructor-img/upload-lesson-icon.png')}}" alt="upload">
                                                        </div>
                                                        <input type="file" name="audio" class="form-control" value="" id="audio" title="Upload lesson audio" >
                                                    </div>
                                                </div>
                                                <div>
                                                    @if ($errors->has('text_description'))
                                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('text_description') }}</span>
                                                    @endif
                                                    @if ($errors->has('image'))
                                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('image') }}</span>
                                                    @endif
                                                    @if ($errors->has('pdf'))
                                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('pdf') }}</span>
                                                    @endif
                                                    @if ($errors->has('slide_document'))
                                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('slide_document') }}</span>
                                                    @endif
                                                    @if ($errors->has('audio'))
                                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('audio') }}</span>
                                                    @endif
                                                </div>
                                                <div class="main-upload-video-processing-box">
                                                    <div class="d-flex main-upload-video-processing-item">
                                                        <div class="flex-grow-1 ms-3">
                                                            <div class="row mb-30">
                                                                <div class="col-md-12">
                                                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Lesson Title') }}</label>
                                                                    <input type="text" name="title" value="{{$lecture->title}}" class="form-control" placeholder="First steps" required>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-30">
                                                                <div class="col-md-12">
                                                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Learners Visibility') }}</label>
                                                                    <select name="lecture_type" class="form-select" required>
                                                                        <option value="" >--{{ __('Select Option') }}--</option>
                                                                        <option value="1" @if($lecture->lecture_type == 1) selected @endif >{{ __('Show') }}</option>
                                                                        <option value="2" @if($lecture->lecture_type == 2) selected @endif >{{ __('Lock') }}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-30 d-none" id="fileDuration">
                                                                <div class="col-md-12">
                                                                    <label class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Lesson File Duration') }} (00:00)</label>
                                                                    <input type="text" name="youtube_file_duration" value="{{$lecture->file_duration}}" class="form-control customFileDuration" placeholder="First file duration">
                                                                    @if ($errors->has('youtube_file_duration'))
                                                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('youtube_file_duration') }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="row mb-30">
                                                                <div class="col-md-12 main-upload-video-processing-item-btns">
                                                                    <button type="submit" class="theme-btn upload-video-processing-item-save-btn">{{__('Save')}}</button>
                                                                    <a href="{{route('instructor.course.edit', [$course->uuid, 'step=lesson'])}}" class="theme-btn default-hover-btn default-back-btn theme-button3">{{__('Back')}}</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <!-- Upload Course Video-4 end -->
                                    </form>
                                </div>
                                <!-- Upload Course Step-6 Item End -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" class="lecture_type" value="{{$lecture->type}}">
@endsection

@push('style')
    <link rel="stylesheet" href="{{asset('frontend/assets/css/custom/img-view.css')}}">

    <!-- Summernote CSS - CDN Link -->
    <link href="{{ asset('common/css/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="{{ asset('common/css/summernote/summernote-lite.min.css') }}" rel="stylesheet">
    <!-- //Summernote CSS - CDN Link -->
@endpush

@push('script')
    <script src="{{asset('frontend/assets/js/custom/form-validation.js')}}"></script>
    <script src="{{asset('frontend/assets/js/custom/img-view.js')}}"></script>
    <script src="{{asset('frontend/assets/js/custom/upload-lesson.js')}}"></script>

    <!-- Summernote JS - CDN Link -->
    <script src="{{ asset('common/js/summernote/summernote-lite.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#summernote").summernote();
            $('.dropdown-toggle').dropdown();
        });
    </script>
    <!-- //Summernote JS - CDN Link -->
@endpush
