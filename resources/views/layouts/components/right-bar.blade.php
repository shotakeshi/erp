<!--  Modal content for the above example -->
<div class="modal modal-rightbar fade" tabindex="-1" role="dialog" aria-labelledby="MetricaRightbar" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="MetricaRightbar">{{ __('site.appearance') }}</h5>
                <button type="button" class="btn btn-sm btn-soft-primary btn-circle btn-square" data-dismiss="modal" aria-hidden="true"><i class="mdi mdi-close"></i></button>
            </div>
            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-pills nav-justified mt-2 mb-4" role="tablist">
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link active" data-toggle="tab" href="#ActivityTab" role="tab">{{ __('site.activity') }}</a>
                    </li>
                    <li class="nav-item waves-effect waves-light">
                        <a class="nav-link" data-toggle="tab" href="#TasksTab" role="tab">{{ __('site.tasks') }}</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active " id="ActivityTab" role="tabpanel">
                        <div class="bg-light mx-n3">
                            <img src="{{ 'images/small/img-1.gif' }}" alt="" class="d-block mx-auto my-4" height="140">
                        </div>
                        <div class="slimscroll scroll-rightbar">
                            <div class="activity">
                                <div class="activity-info">
                                    <div class="icon-info-activity">
                                        <i class="mdi mdi-login bg-soft-success"></i>
                                    </div>
                                    <div class="activity-info-text mb-2">
                                        <div class="mb-1">
                                            <small class="text-muted d-block mb-1">{{  auth()->user()->last_login_at?->diffForHumans() }}</small>
                                            <a href="javascript:void(0)" class="m-0 w-75">Login</a>
                                        </div>
                                        <p class="text-muted mb-2 text-truncate">
                                            {{ auth()->user()->last_login_ip }} <br/>
                                            {{ auth()->user()->last_login_browser }} - {{ auth()->user()->last_login_platform }}
                                        </p>
                                    </div>
                                </div>
                            </div><!--end activity-->
                        </div><!--end activity-scroll-->
                    </div><!--end tab-pane-->
                    <div class="tab-pane" id="TasksTab" role="tabpanel">
                        <div class="m-0">
                            <div id="rightbar_chart" class="apex-charts"></div>
                        </div>
                        <div class="slimscroll scroll-rightbar">
                            <div class="p-2">
                                <div class="media mb-3">
                                    <img src="{{ asset('images/widgets/project3.jpg') }}" alt="" class="thumb-lg rounded-circle">
                                    <div class="media-body align-self-center text-truncate ml-3">
                                        <p class="text-success font-weight-semibold mb-0 font-14">Project</p>
                                        <h4 class="mt-0 mb-0 font-weight-semibold text-dark font-18">Payment App</h4>
                                    </div><!--end media-body-->
                                </div>
                                <span><b>Deadline:</b> 02 June 2020</span>
                                <a href="javascript: void(0);" class="d-block mt-3">
                                    <p class="text-muted mb-0">Complete Tasks<span class="float-right">75%</span></p>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </a>
                            </div>
                            <hr class="hr-dashed">
                        </div>
                    </div><!--end tab-pane-->
                </div> <!--end tab-content-->
            </div><!--end modal-body-->
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->