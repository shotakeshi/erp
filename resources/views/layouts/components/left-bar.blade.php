<!-- leftbar-tab-menu -->
<div class="leftbar-tab-menu">
    <div class="main-icon-menu slimscroll-menu">
        <a href="{{ route('dashboard') }}" class="logo logo-metrica d-block text-center">
            <span>
                <img src="{{ asset('images/logo-sm.png') }}" alt="ERP SYSTEM" class="logo-sm">
            </span>
        </a>
        <nav class="nav">
            <a href="#Dashboard" class="nav-link" data-toggle="tooltip-custom" data-placement="right"  data-trigger="hover" title="" data-original-title="Dashboard">
                <i data-feather="home" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end Dashboards-->

            <a href="#Apps" class="nav-link" data-toggle="tooltip-custom" data-placement="right"  data-trigger="hover" title="" data-original-title="Apps">
                <i data-feather="grid" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end Apps-->

            <a href="#Uikit" class="nav-link" data-toggle="tooltip-custom" data-placement="right"  data-trigger="hover" title="" data-original-title="UI Kit">
                <i data-feather="package" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end Uikit-->

            <a href="#Pages" class="nav-link" data-toggle="tooltip-custom" data-placement="right"  data-trigger="hover" title="" data-original-title="Pages">
                <i data-feather="copy" class="align-self-center menu-icon icon-dual"></i>
            </a><!--end Pages-->

            <a href="#Authentication" class="nav-link" data-toggle="tooltip-custom" data-placement="right"  data-trigger="hover" title="" data-original-title="Authentication">
                <i data-feather="lock" class="align-self-center menu-icon icon-dual"></i>
            </a> <!--end Authentication-->

        </nav><!--end nav-->
        <div class="pro-metrica-end">
            <a href="#" class="help" data-toggle="tooltip-custom" data-placement="right"  data-trigger="hover" title="" data-original-title="Chat">
                <i data-feather="message-circle" class="align-self-center menu-icon icon-md icon-dual mb-4"></i>
            </a>
            <a href="#" class="profile">
                <img src="{{ asset('images/users/user-4.jpg') }}" alt="profile-user" class="rounded-circle thumb-sm">
            </a>
        </div>
    </div><!--end main-icon-menu-->

    <div class="main-menu-inner">
        <!-- LOGO -->
        <div class="topbar-left">
            <a href="{{ route('dashboard') }}" class="logo">
                <span>
                    <img src="{{ asset('images/logo-lg.png') }}" alt="ERP SYSTEM" class="logo-lg logo-dark">
                    <img src="{{ asset('images/logo-lg.png') }}" alt="ERP SYSTEM" class="logo-lg logo-light">
                </span>
            </a>
        </div>
        <!--end logo-->
        <div class="menu-body slimscroll">
            <div id="Dashboard" class="main-icon-menu-pane active">
                <div class="title-box">
                    <h6 class="menu-title">{{ __('site.people') }}</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="{{ route('employees.index') }}">{{ __('site.employees.manage') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="crypto-index.html">Phòng ban</a></li>
                    <li class="nav-item"><a class="nav-link" href="crm-index.html">Chức vụ</a></li>
                </ul>
            </div><!-- end Dashboards -->

            <div id="Apps" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Apps</h6>
                </div>
                <ul class="nav metismenu">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript: void(0);"><span class="w-100">People</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="analytics-customers.html">Customers</a></li>
                            <li><a href="analytics-reports.html">Reports</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="javascript: void(0);"><span class="w-100">Crypto</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="crypto-exchange.html">Exchange</a></li>
                            <li><a href="crypto-wallet.html">Wallet</a></li>
                            <li><a href="crypto-news.html">Crypto News</a></li>
                            <li><a href="crypto-ico.html">ICO List</a></li>
                            <li><a href="crypto-settings.html">settings</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="javascript: void(0);"><span class="w-100">CRM</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="crm-contacts.html">Contacts</a></li>
                            <li><a href="crm-opportunities.html">Opportunities</a></li>
                            <li><a href="crm-leads.html">Leads</a></li>
                            <li><a href="crm-customers.html">Customers</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="javascript: void(0);"><span class="w-100">Projects</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="projects-clients.html">Clients</a></li>
                            <li><a href="projects-team.html">Team</a></li>
                            <li><a href="projects-project.html">Project</a></li>
                            <li><a href="projects-task.html">Task</a></li>
                            <li><a href="projects-kanban-board.html">Kanban Board</a></li>
                            <li><a href="projects-chat.html">Chat</a></li>
                            <li><a href="projects-users.html">Users</a></li>
                            <li><a href="projects-create.html">Project Create</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="javascript: void(0);"><span class="w-100">Ecommerce</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="ecommerce-products.html">Products</a></li>
                            <li><a href="ecommerce-product-list.html">Product List</a></li>
                            <li><a href="ecommerce-product-detail.html">Product Detail</a></li>
                            <li><a href="ecommerce-cart.html">Cart</a></li>
                            <li><a href="ecommerce-checkout.html">Checkout</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="javascript: void(0);"><span class="w-100">Helpdesk</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="helpdesk-teckets.html">Tickets</a></li>
                            <li><a href="helpdesk-reports.html">Reports</a></li>
                            <li><a href="helpdesk-agents.html">Agents</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="javascript: void(0);"><span class="w-100">Hospital</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li>
                                <a href="javascript: void(0);">Appointments <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="hospital-doctor-shedule.html">Dr. Shedule</a></li>
                                    <li><a href="hospital-all-appointments.html">All Appointments</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript: void(0);">Doctors <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="hospital-all-doctors.html">All Doctors</a></li>
                                    <li><a href="hospital-add-doctor.html">Add Doctor</a></li>
                                    <li><a href="hospital-doctor-edit.html">Doctor Edit</a></li>
                                    <li><a href="hospital-doctor-profile.html">Doctor Profile</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript: void(0);">Patients <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="hospital-all-patients.html">All Patients</a></li>
                                    <li><a href="hospital-add-patient.html">Add Patient</a></li>
                                    <li><a href="hospital-patient-edit.html">Patient Edit</a></li>
                                    <li><a href="hospital-patient-profile.html">Patient Profile</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript: void(0);">Payments <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="hospital-all-payments.html">All Payments</a></li>
                                    <li><a href="hospital-payment-invoice.html">Payment Invoice</a></li>
                                    <li><a href="hospital-cashless-payments.html">Cashless Payments</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript: void(0);">Staff <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="hospital-all-staff.html">All Staff</a></li>
                                    <li><a href="hospital-add-member.html">Add Member</a></li>
                                    <li><a href="hospital-edit-member.html">Edit Member</a></li>
                                    <li><a href="hospital-member-profile.html">Member Profile</a></li>
                                    <li><a href="hospital-salary.html">Staff Salary</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript: void(0);">General <span class="menu-arrow left-has-menu"><i class="mdi mdi-chevron-right"></i></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="hospital-all-rooms.html">Room Allotments</a></li>
                                    <li><a href="hospital-expenses.html">Expenses Report</a></li>
                                    <li><a href="hospital-departments.html">Departments</a></li>
                                    <li><a href="hospital-insurance-company.html">Insurance Co.</a></li>
                                    <li><a href="hospital-events.html">Events</a></li>
                                    <li><a href="hospital-leaves.html">Leaves</a></li>
                                    <li><a href="hospital-holidays.html">Holidays</a></li>
                                    <li><a href="hospital-attendance.html">Attendance</a></li>
                                    <li><a href="hospital-chat.html">Chat</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="javascript: void(0);"><span class="w-100">Email</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="apps-email-inbox.html">Inbox</a></li>
                            <li><a href="apps-email-read.html">Read Email</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item"><a class="nav-link" href="apps-chat.html">Chat</a></li>
                    <li class="nav-item"><a class="nav-link" href="apps-contact-list.html">Contact List</a></li>
                    <li class="nav-item"><a class="nav-link" href="apps-calendar.html">Calendar</a></li>
                    <li class="nav-item"><a class="nav-link" href="apps-invoice.html">Invoice</a></li>
                </ul>
            </div><!-- end Crypto -->

            <div id="Uikit" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">UI Kit</h6>
                </div>
                <ul class="nav metismenu">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><span class="w-100">UI Elements</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="ui-bootstrap.html">Bootstrap</a></li>
                            <li><a href="ui-animation.html">Animation</a></li>
                            <li><a href="ui-avatar.html">Avatar</a></li>
                            <li><a href="ui-clipboard.html">Clip Board</a></li>
                            <li><a href="ui-files.html">File Manager</a></li>
                            <li><a href="ui-ribbons.html">Ribbons</a></li>
                            <li><a href="ui-dragula.html">Dragula</a></li>
                            <li><a href="ui-check-radio.html">Check & Radio</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="#"><span class="w-100">Advanced UI</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="advanced-rangeslider.html">Range Slider</a></li>
                            <li><a href="advanced-sweetalerts.html">Sweet Alerts</a></li>
                            <li><a href="advanced-nestable.html">Nestable List</a></li>
                            <li><a href="advanced-ratings.html">Ratings</a></li>
                            <li><a href="advanced-highlight.html">Highlight</a></li>
                            <li><a href="advanced-session.html">Session Timeout</a></li>
                            <li><a href="advanced-idle-timer.html">Idle Timer</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="#"><span class="w-100">Forms</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="forms-elements.html">Basic Elements</a></li>
                            <li><a href="forms-advanced.html">Advance Elements</a></li>
                            <li><a href="forms-validation.html">Validation</a></li>
                            <li><a href="forms-wizard.html">Wizard</a></li>
                            <li><a href="forms-editors.html">Editors</a></li>
                            <li><a href="forms-repeater.html">Repeater</a></li>
                            <li><a href="forms-x-editable.html">X Editable</a></li>
                            <li><a href="forms-uploads.html">File Upload</a></li>
                            <li><a href="forms-img-crop.html">Image Crop</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="#"><span class="w-100">Charts</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="charts-apex.html">Apex</a></li>
                            <li><a href="charts-morris.html">Morris</a></li>
                            <li><a href="charts-chartist.html">Chartist</a></li>
                            <li><a href="charts-flot.html">Flot</a></li>
                            <li><a href="charts-peity.html">Peity</a></li>
                            <li><a href="charts-chartjs.html">Chartjs</a></li>
                            <li><a href="charts-sparkline.html">Sparkline</a></li>
                            <li><a href="charts-knob.html">Jquery Knob</a></li>
                            <li><a href="charts-justgage.html">JustGage</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="#"><span class="w-100">Tables</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="tables-basic.html">Basic</a></li>
                            <li><a href="tables-datatable.html">Datatables</a></li>
                            <li><a href="tables-responsive.html">Responsive</a></li>
                            <li><a href="tables-footable.html">Footable</a></li>
                            <li><a href="tables-jsgrid.html">Jsgrid</a></li>
                            <li><a href="tables-dragger.html">Dragger</a></li>
                            <li><a href="tables-editable.html">Editable</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="#"><span class="w-100">Icons</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="icons-materialdesign.html">Material Design</a></li>
                            <li><a href="icons-dripicons.html">Dripicons</a></li>
                            <li><a href="icons-fontawesome.html">Font awesome</a></li>
                            <li><a href="icons-themify.html">Themify</a></li>
                            <li><a href="icons-feather.html">Feather</a></li>
                            <li><a href="icons-typicons.html">Typicons</a></li>
                            <li><a href="icons-emoji.html">Emoji <i class="em em-ok_hand"></i></a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="#"><span class="w-100">Maps</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="maps-google.html">Google Maps</a></li>
                            <li><a href="maps-leaflet.html">Leaflet Maps</a></li>
                            <li><a href="maps-vector.html">Vector Maps</a></li>
                        </ul>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="#"><span class="w-100">Email Templates</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="nav-second-level" aria-expanded="false">
                            <li><a href="email-templates-basic.html">Basic Action Email</a></li>
                            <li><a href="email-templates-alert.html">Alert Email</a></li>
                            <li><a href="email-templates-billing.html">Billing Email</a></li>
                        </ul>
                    </li><!--end nav-item-->
                </ul><!--end nav-->
            </div><!-- end Others -->

            <div id="Pages" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Pages</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="pages-profile.html">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-tour.html">Tour</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-timeline.html">Timeline</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-treeview.html">Treeview</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-starter.html">Starter Page</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-pricing.html">Pricing</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-blogs.html">Blogs</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-faq.html">FAQs</a></li>
                    <li class="nav-item"><a class="nav-link" href="pages-gallery.html">Gallery</a></li>
                </ul>
            </div><!-- end Pages -->
            <div id="Authentication" class="main-icon-menu-pane">
                <div class="title-box">
                    <h6 class="menu-title">Authentication</h6>
                </div>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="auth-login.html">Log in</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth-login-alt.html">Log in alt</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth-register.html">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth-register-alt.html">Register-alt</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth-recover-pw.html">Re-Password</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth-recover-pw-alt.html">Re-Password-alt</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth-lock-screen.html">Lock Screen</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth-lock-screen-alt.html">Lock Screen-alt</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth-404.html">Error 404</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth-404-alt.html">Error 404-alt</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth-500.html">Error 500</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth-500-alt.html">Error 500-alt</a></li>
                </ul>
            </div><!-- end Authentication-->
        </div><!--end menu-body-->
    </div><!-- end main-menu-inner-->
</div>
<!-- end leftbar-tab-menu-->