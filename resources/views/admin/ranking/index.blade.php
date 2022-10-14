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
                                <h2>{{ __('Ranking Level') }}</h2>
                            </div>
                        </div>
                        <div class="breadcrumb__content__right">
                            <nav aria-label="breadcrumb">
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('Dashboard')}}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{ __('Ranking Level') }}</li>
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
                            <h2>{{ __('Ranking Level') }}</h2>
                            <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#add-todo-modal">
                                <i class="fa fa-plus"></i> {{ __('Add Ranking Level') }}
                            </button>
                        </div>
                        <div class="customers__table">
                            <table id="customers-table" class="row-border data-table-filter table-style">
                                <thead>
                                <tr>
                                    <th>{{__('Image')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{ __('Earning Range') }}</th>
                                    <th>{{ __('Student Range') }}</th>
                                    <th>{{__('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($levels as $level)
                                    <tr class="removable-item">
                                        <td>
                                            <div class="admin-dashboard-blog-list-img">
                                                <img src="{{getImageFile($level->image_path)}}">
                                            </div>
                                        </td>
                                        <td>{{$level->name}}</td>
                                        <td>
                                            @if(get_currency_placement() == 'after')
                                                {{$level->earning}} {{ get_currency_symbol() }}
                                            @else
                                                {{ get_currency_symbol() }} {{$level->earning}}
                                            @endif
                                        </td>
                                        <td>{{$level->student}}</td>
                                        <td>
                                            <div class="action__buttons">
                                                <a href="{{ route('ranking.edit', $level->uuid) }}" class=" btn-action mr-1 edit" data-toggle="tooltip" title="Edit">
                                                    <img src="{{asset('admin/images/icons/edit-2.svg')}}" alt="edit">
                                                </a>
                                                <a href="javascript:void(0);" data-url="{{route('ranking.delete', [$level->uuid])}}" class="btn-action delete" title="Delete">
                                                    <img src="{{asset('admin/images/icons/trash-2.svg')}}" alt="trash">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3">
                                {{$levels->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Page content area end -->

    <!-- Add Modal section start -->
    <div class="modal fade" id="add-todo-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Ranking Level') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('ranking.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="input__group mb-25">
                            <label for="badge_image" class="col-lg-3">{{ __('Badge Image') }}</label>
                            <div class="col-lg-3">
                                <div class="upload-img-box">
                                    <img src="">
                                    <input type="file" name="badge_image" id="badge_image" accept="image/*" onchange="previewFile(this)">
                                    <div class="upload-img-box-icon">
                                        <i class="fa fa-camera"></i>
                                        <p class="m-0">{{ __('Badge Image') }}</p>
                                    </div>
                                </div>
                                @if ($errors->has('badge_image'))
                                    <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('badge_image') }}</span>
                                @endif
                                <p class="mb-0 mt-1">{{ __('Accepted Image Files') }}: PNG, JPG <br> {{ __('Accepted Size') }}: 86 x 66 (1MB)</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="input__group mb-25">
                                    <label for="name">{{ __('Name') }}</label>
                                    <input type="text" name="name" id="name" placeholder="Type name" value="{{ old('name') }}" required>
                                    @if ($errors->has('name'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input__group mb-25">
                                    <label for="serial_no">{{ __('Serial No') }}</label>
                                    <input type="number" name="serial_no" id="serial_no" placeholder="Type serial no" value="{{ old('serial_no') }}" required>
                                    @if ($errors->has('serial_no'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('serial_no') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="input__group">
                                    <label for="earning">{{ __('Earning Range Start') }}</label>
                                    <input type="number" name="earning" id="earning" placeholder="Type earning" value="{{ old('earning') }}"
                                           required>
                                    @if ($errors->has('earning'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('earning') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input__group">
                                    <label for="student">{{ __('Student') }}</label>
                                    <input type="number" name="student" id="student" placeholder="Type student" value="{{ old('student') }}" required>
                                    @if ($errors->has('student'))
                                        <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first('student') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-purple">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Modal section end -->

@endsection

@push('style')
    <link rel="stylesheet" href="{{asset('admin/css/jquery.dataTables.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/css/custom/image-preview.css')}}">
@endpush

@push('script')
    <script src="{{asset('admin/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('admin/js/custom/data-table-page.js')}}"></script>
    <script src="{{asset('admin/js/custom/image-preview.js')}}"></script>
@endpush
