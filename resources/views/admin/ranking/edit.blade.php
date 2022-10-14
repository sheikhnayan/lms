@extends('layouts.admin')

@section('content')
    <!-- Page content area start -->
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="breadcrumb__content">
                        <div class="breadcrumb__content__left">
                            <div class="breadcrumb__title">
                                <h2>{{ __('Edit Ranking Level') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item"><a href="{{route('ranking.index')}}">{{ __('All Ranking Level') }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{__('Edit Blog')}}</li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="customers__area bg-style mb-30">
                        <div class="item-title d-flex justify-content-between">
                            <h2>{{ __('Edit Ranking Level') }}</h2>
                        </div>
                        <form action="{{route('ranking.update', [$level->uuid])}}" method="post" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <div class="upload-img-box">
                                        @if($level->badge_image)
                                            <img src="{{getImageFile($level->image_path)}}">
                                        @else
                                            <img src="">
                                        @endif
                                        <input type="file" name="badge_image" id="badge_image" accept="image/*" onchange="previewFile(this)">
                                        <div class="upload-img-box-icon">
                                            <i class="fa fa-camera"></i>
                                            <p class="m-0">{{ __('Badge Image') }}</p>
                                        </div>
                                    </div>
                                    @if ($errors->has('badge_image'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('badge_image') }}</span>
                                    @endif
                                    <p>{{ __('Accepted Image Files') }}: PNG,JPG <br> {{ __('Accepted Size') }}: 86 x 66 (1MB)</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{$level->name}}" placeholder="Type name" class="form-control">
                                        @if ($errors->has('name'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('name') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label for="serial_no">{{ __('Serial No') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="serial_no" value="{{$level->serial_no}}" placeholder="Type serial no" class="form-control" required>
                                        @if ($errors->has('serial_no'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('serial_no') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label for="earning">{{ __('Earning') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="earning" value="{{$level->earning}}" placeholder="Type name" class="form-control">
                                        @if ($errors->has('earning'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('earning') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input__group mb-25">
                                        <label for="student">{{ __('Student') }} <span class="text-danger">*</span></label>
                                        <input type="number" name="student" id="student" placeholder="Type student"
                                               value="{{ $level->student }}"
                                               class="form-control" required>
                                        @if ($errors->has('student'))
                                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('student') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
                                    @updateButton
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->
@endsection

@push('style')
    <link rel="stylesheet" href="{{asset('admin/css/custom/image-preview.css')}}">
@endpush

@push('script')
    <script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
@endpush
