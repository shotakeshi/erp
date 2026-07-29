<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('site.dashboard') }}</a></li>
                    @if(count($breadcrumbs))
                        @foreach($breadcrumbs as $breadcrumb)
                            @if(!$loop->last)
                                <li class="breadcrumb-item">
                                    <a href="{{ $breadcrumb['url'] ?? 'javascript:void(0)' }}">{{ $breadcrumb['title'] }}</a>
                                </li>
                            @else
                                <li class="breadcrumb-item active">
                                    {{ $breadcrumb['title'] }}
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ol>
            </div>
            <h4 class="page-title">{{ $title }}</h4>
        </div><!--end page-title-box-->
    </div><!--end col-->
</div>
<!-- end page title end breadcrumb -->