<div class="container-fluid g-0">
    <div class="row">
        <div class="col-lg-12 p-0 ">
            <div class="header_iner d-flex justify-content-between align-items-center">
                <div class="sidebar_icon d-lg-none">
                    <i class="ti-menu"></i>
                </div>
                <label class="form-label switch_toggle d-none d-lg-block" for="checkbox">
                    <input type="checkbox" id="checkbox">
                    <div class="slider round open_miniSide"></div>
                </label>
                <div class="header_right d-flex justify-content-between align-items-center">
                    <div class="header_notification_warp d-flex align-items-center">
                        <li>
                            <a class="bell_notification_clicker" href="#"> <img
                                    src="{{ asset('admins') }}/img/icon/bell.svg" alt>
                                <span>2</span>
                            </a>

                            <div class="Menu_NOtification_Wrap">
                                <div class="notification_Header">
                                    <h4>Notifications</h4>
                                </div>
                                <div class="Notification_body">

                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('admins') }}/img/staf/2.png" alt></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#">
                                                <h5>Cool Marketing </h5>
                                            </a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>

                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('admins') }}/img/staf/4.png" alt></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#">
                                                <h5>Awesome packages</h5>
                                            </a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>

                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('admins') }}/img/staf/3.png" alt></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#">
                                                <h5>what a packages</h5>
                                            </a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>

                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('admins') }}/img/staf/2.png" alt></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#">
                                                <h5>Cool Marketing </h5>
                                            </a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>

                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('admins') }}/img/staf/4.png" alt></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#">
                                                <h5>Awesome packages</h5>
                                            </a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>

                                    <div class="single_notify d-flex align-items-center">
                                        <div class="notify_thumb">
                                            <a href="#"><img src="{{ asset('admins') }}/img/staf/3.png" alt></a>
                                        </div>
                                        <div class="notify_content">
                                            <a href="#">
                                                <h5>what a packages</h5>
                                            </a>
                                            <p>Lorem ipsum dolor sit amet</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="nofity_footer">
                                    <div class="submit_button text-center pt_20">
                                        <a href="#" class="btn_1">See More</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </div>
                    <div class="profile_info">
                        <img src="{{ asset('admins') }}/img/client_img.png" alt="#">
                        <div class="profile_info_iner">
                            <div class="profile_author_name">
                                <h5>Admin</h5>
                            </div>
                            <div class="profile_info_details">
                                <a href="#">Tài khoản </a>
                                @if (auth()->check())
                                    <form action="{{ route('admins.logout', ['id' => auth()->user()->id]) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">Đăng
                                            xuất</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
