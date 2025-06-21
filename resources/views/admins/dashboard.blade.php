@extends('admins.layout.main')
@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid p-0 ">

        <div class="row">
            <div class="col-12">
                <div class="page_title_box d-flex flex-wrap align-items-center justify-content-between">
                    <div class="page_title_left">
                        <h3 class="f_s_25 f_w_700 dark_text">Dashboard</h3>
                        <ol class="breadcrumb page_bradcam mb-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                            <li class="breadcrumb-item active">Analytic</li>
                        </ol>
                    </div>
                    <div class="page_title_right">
                        <div class="page_date_button">
                            August 1, 2020 - August 31, 2020
                        </div>
                        <div class="dropdown common_bootstrap_button ">
                            <span class="dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                action
                            </span>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item f_s_16 f_w_600" href="#"> Download</a>
                                <a class="dropdown-item f_s_16 f_w_600" href="#"> Print</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ">
            <div class="col-xl-8 ">
                <div class="white_card mb_30 card_height_100">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Revenue</h3>
                            </div>
                            <div class="float-lg-right float-none common_tab_btn2 justify-content-end">
                                <ul class="nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Month</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#">Week</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Day</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">
                        <div id="marketchart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 ">
                <div class="white_card card_height_100 mb_30 sales_card_wrapper">
                    <div class="white_card_header d-flex justify-content-end">
                        <button class="export_btn">Export</button>
                    </div>

                    <div class="sales_card_body">
                        <div class="single_sales">
                            <span>Paid Visit</span>
                            <h3>6550</h3>
                        </div>
                        <div class="single_sales">
                            <span>Total Visit</span>
                            <h3>5646,454</h3>
                        </div>
                        <div class="single_sales">
                            <span>Expence</span>
                            <h3>$650</h3>
                        </div>
                        <div class="single_sales">
                            <span>Commission</span>
                            <h3>$56</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 ">
                <div class="white_card card_height_100 mb_30 social_media_card">
                    <div class="white_card_header">
                        <div class="main-title">
                            <h3 class="m-0">Social media</h3>
                            <span>About Your Social Popularity</span>
                        </div>
                    </div>
                    <div class="media_thumb ml_25">
                        <img src="img/media.svg" alt>
                    </div>
                    <div class="media_card_body">
                        <div class="media_card_list">
                            <div class="single_media_card">
                                <span>Followers</span>
                                <h3>35.6 K</h3>
                            </div>
                            <div class="single_media_card">
                                <span>Followers</span>
                                <h3>35.6 K</h3>
                            </div>
                            <div class="single_media_card">
                                <span>Followers</span>
                                <h3>35.6 K</h3>
                            </div>
                            <div class="single_media_card">
                                <span>Followers</span>
                                <h3>35.6 K</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Visitors by Browser</h3>
                                <span>15654 Visaitors</span>
                            </div>
                            <div class="float-lg-right float-none common_tab_btn justify-content-end">
                                <ul class="nav">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#">Week</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Day</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">
                        <div id="chart-currently"></div>
                        <div class="monthly_plan_wraper">
                            <div class="single_plan d-flex align-items-center justify-content-between">
                                <div class="plan_left d-flex align-items-center">
                                    <div class="thumb">
                                        <img src="img/crome.png" alt>
                                    </div>
                                    <div>
                                        <h5>Chrome Users</h5>
                                        <span>Today</span>
                                    </div>
                                </div>
                                <span class="brouser_btn">+2155</span>
                            </div>
                            <div class="single_plan d-flex align-items-center justify-content-between">
                                <div class="plan_left d-flex align-items-center">
                                    <div class="thumb">
                                        <img src="img/safari.png" alt>
                                    </div>
                                    <div>
                                        <h5>Chrome Users</h5>
                                        <span>Today</span>
                                    </div>
                                </div>
                                <span class="brouser_btn">+54</span>
                            </div>
                            <div class="single_plan d-flex align-items-center justify-content-between">
                                <div class="plan_left d-flex align-items-center">
                                    <div class="thumb">
                                        <img src="img/OBJECTS.png" alt>
                                    </div>
                                    <div>
                                        <h5>Chrome Users</h5>
                                        <span>Today</span>
                                    </div>
                                </div>
                                <span class="brouser_btn">+22</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 ">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="main-title">
                            <h3 class="m-0">Visitors by Device</h3>
                            <span>15654 Visaitors</span>
                        </div>
                    </div>
                    <div class="white_card_body ">
                        <div class="card_container">
                            <div id="platform_type_dates_donut" style="height:280px"></div>
                        </div>
                        <div class="devices_btn text-center">
                            <a class="color_button color_button2" href="#">Android</a>
                            <a class="color_button" href="#">iphone</a>
                            <a class="color_button color_button3" href="#">Others</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="white_card card_height_100 mb_30 ">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="white_card_header">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h3 class="m-0">Running Campain</h3>
                                        <span>Overview</span>
                                    </div>
                                </div>
                            </div>
                            <div class="white_card_body QA_section">
                                <div class="QA_table ">

                                    <table class="table lms_table_active2 p-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Campain</th>
                                                <th scope="col">Start Time</th>
                                                <th scope="col">Company</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="customer d-flex align-items-center">
                                                        <div class="social_media">
                                                            <i class="fab fa-facebook-f"></i>
                                                        </div>
                                                        <div class="ml_18">
                                                            <h3 class="f_s_18 f_w_900 mb-0">Facebook Promotion
                                                            </h3>
                                                            <span class="f_s_12 f_w_700 text_color_8">Unique
                                                                Watch</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h3 class="f_s_18 f_w_900 mb-0">08:32</h3>
                                                        <span class="f_s_12 f_w_700 color_text_3">12.12.2022</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h3 class="f_s_18 f_w_800 mb-0">H&G Fashion</h3>
                                                        <span class="f_s_12 f_w_500 color_text_3">Fashion and
                                                            design</span>
                                                    </div>
                                                </td>
                                                <td class="f_s_14 f_w_400 color_text_3">
                                                    <a href="#" class="badge_active">Active</a>
                                                </td>
                                                <td>
                                                    <div class="action_btns d-flex">
                                                        <a href="#" class="action_btn mr_10"> <i
                                                                class="far fa-edit"></i> </a>
                                                        <a href="#" class="action_btn"> <i
                                                                class="fas fa-trash"></i> </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="customer d-flex align-items-center">
                                                        <div class="social_media insta_bg">
                                                            <i class="fab fa-instagram"></i>
                                                        </div>
                                                        <div class="ml_18">
                                                            <h3 class="f_s_18 f_w_900 mb-0">Instagram</h3>
                                                            <span class="f_s_12 f_w_700 text_color_9">Unique
                                                                Watch</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h3 class="f_s_18 f_w_900 mb-0">08:32</h3>
                                                        <span class="f_s_12 f_w_700 color_text_3">12.12.2022</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h3 class="f_s_18 f_w_800 mb-0">H&G Fashion</h3>
                                                        <span class="f_s_12 f_w_500 color_text_3">Fashion and
                                                            design</span>
                                                    </div>
                                                </td>
                                                <td class="f_s_14 f_w_400 color_text_3">
                                                    <a href="#" class="badge_active2">Posed</a>
                                                </td>
                                                <td>
                                                    <div class="action_btns d-flex">
                                                        <a href="#" class="action_btn mr_10"> <i
                                                                class="far fa-edit"></i> </a>
                                                        <a href="#" class="action_btn"> <i
                                                                class="fas fa-trash"></i> </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="customer d-flex align-items-center">
                                                        <div class="social_media twitter_bg">
                                                            <i class="fab fa-twitter"></i>
                                                        </div>
                                                        <div class="ml_18">
                                                            <h3 class="f_s_18 f_w_900 mb-0">Twitter</h3>
                                                            <span class="f_s_12 f_w_700 text_color_10">Unique
                                                                Watch</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h3 class="f_s_18 f_w_900 mb-0">08:32</h3>
                                                        <span class="f_s_12 f_w_700 color_text_3">12.12.2022</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h3 class="f_s_18 f_w_800 mb-0">H&G Fashion</h3>
                                                        <span class="f_s_12 f_w_500 color_text_3">Fashion and
                                                            design</span>
                                                    </div>
                                                </td>
                                                <td class="f_s_14 f_w_400 color_text_3">
                                                    <a href="#" class="badge_active3">Closed</a>
                                                </td>
                                                <td>
                                                    <div class="action_btns d-flex">
                                                        <a href="#" class="action_btn mr_10"> <i
                                                                class="far fa-edit"></i> </a>
                                                        <a href="#" class="action_btn"> <i
                                                                class="fas fa-trash"></i> </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="customer d-flex align-items-center">
                                                        <div class="social_media youtube_bg">
                                                            <i class="fab fa-youtube"></i>
                                                        </div>
                                                        <div class="ml_18">
                                                            <h3 class="f_s_18 f_w_900 mb-0">Youtube</h3>
                                                            <span class="f_s_12 f_w_700 text_color_11">Summer
                                                                Campain</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h3 class="f_s_18 f_w_900 mb-0">08:32</h3>
                                                        <span class="f_s_12 f_w_700 color_text_3">12.12.2022</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <h3 class="f_s_18 f_w_800 mb-0">H&G Fashion</h3>
                                                        <span class="f_s_12 f_w_500 color_text_3">Fashion and
                                                            design</span>
                                                    </div>
                                                </td>
                                                <td class="f_s_14 f_w_400 color_text_3">
                                                    <a href="#" class="badge_active4">End soon</a>
                                                </td>
                                                <td>
                                                    <div class="action_btns d-flex">
                                                        <a href="#" class="action_btn mr_10"> <i
                                                                class="far fa-edit"></i> </a>
                                                        <a href="#" class="action_btn"> <i
                                                                class="fas fa-trash"></i> </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 white_card_body pt_25">
                            <div class="project_complete">
                                <div class="single_pro d-flex">
                                    <div class="probox"></div>
                                    <div class="box_content">
                                        <h4>5685</h4>
                                        <span>Project completed</span>
                                    </div>
                                </div>
                                <div class="single_pro d-flex">
                                    <div class="probox blue_box"></div>
                                    <div class="box_content">
                                        <h4 class="bluish_text">5685</h4>
                                        <span class="grayis_text">Project completed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="white_card card_height_100 mb_30 ">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Work List</h3>
                                <span>Todo</span>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body QA_section">
                        <div class="todo_wrapper">
                            <div class="single_todo d-flex justify-content-between align-items-center">
                                <div class="lodo_left d-flex align-items-center">
                                    <div class="bar_line mr_10"></div>
                                    <div class="todo_box">
                                        <label class="form-label primary_checkbox  d-flex mr_10 ">
                                            <input type="checkbox">
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="todo_head">
                                        <h5 class="f_s_18 f_w_900 mb-0">Assign market analysis </h5>
                                        <p class="f_s_12 f_w_400 mb-0 text_color_8">Due 5 Days</p>
                                    </div>
                                </div>
                                <div class="lodo_right"> <a href="#" class="badge_complete">Complete</a> </div>
                            </div>
                            <div class="single_todo d-flex justify-content-between align-items-center">
                                <div class="lodo_left d-flex align-items-center">
                                    <div class="bar_line red_line mr_10"></div>
                                    <div class="todo_box">
                                        <label class="form-label primary_checkbox  d-flex mr_10 ">
                                            <input type="checkbox">
                                            <span class="checkmark red_check"></span>
                                        </label>
                                    </div>
                                    <div class="todo_head">
                                        <h5 class="f_s_18 f_w_900 mb-0">Assign market analysis </h5>
                                        <p class="f_s_12 f_w_400 mb-0 text_color_8">Due 5 Days</p>
                                    </div>
                                </div>
                                <div class="lodo_right"> <a href="#" class="mark_complete">Mark as
                                        completed</a> </div>
                            </div>
                            <div class="single_todo d-flex justify-content-between align-items-center">
                                <div class="lodo_left d-flex align-items-center">
                                    <div class="bar_line red_line mr_10"></div>
                                    <div class="todo_box">
                                        <label class="form-label primary_checkbox  d-flex mr_10 ">
                                            <input type="checkbox">
                                            <span class="checkmark red_check"></span>
                                        </label>
                                    </div>
                                    <div class="todo_head">
                                        <h5 class="f_s_18 f_w_900 mb-0">Assign market analysis </h5>
                                        <p class="f_s_12 f_w_400 mb-0 text_color_8">Due 5 Days</p>
                                    </div>
                                </div>
                                <div class="lodo_right"> <a href="#" class="mark_complete">Mark as
                                        completed</a> </div>
                            </div>
                            <div class="single_todo d-flex justify-content-between align-items-center">
                                <div class="lodo_left d-flex align-items-center">
                                    <div class="bar_line mr_10"></div>
                                    <div class="todo_box">
                                        <label class="form-label primary_checkbox  d-flex mr_10 ">
                                            <input type="checkbox">
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="todo_head">
                                        <h5 class="f_s_18 f_w_900 mb-0">Assign market analysis </h5>
                                        <p class="f_s_12 f_w_400 mb-0 text_color_8">Due 5 Days</p>
                                    </div>
                                </div>
                                <div class="lodo_right"> <a href="#" class="badge_complete">Complete</a> </div>
                            </div>
                            <div class="single_todo d-flex justify-content-between align-items-center">
                                <div class="lodo_left d-flex align-items-center">
                                    <div class="bar_line mr_10"></div>
                                    <div class="todo_box">
                                        <label class="form-label primary_checkbox  d-flex mr_10 ">
                                            <input type="checkbox">
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="todo_head">
                                        <h5 class="f_s_18 f_w_900 mb-0">Assign market analysis </h5>
                                        <p class="f_s_12 f_w_400 mb-0 text_color_8">Due 5 Days</p>
                                    </div>
                                </div>
                                <div class="lodo_right"> <a href="#" class="badge_complete">Complete</a> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-7">
                <div class="white_card card_height_100  mb_20">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Visitors from country</h3>
                                <span>Visitors all over the world</span>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">
                        <div id="world-map-markers" class="dashboard_w_map pb_20"></div>
                        <div class="world_list_wraper">
                            <div class="row justify-content-center">
                                <div class="col-lg-9">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="single_progressbar">
                                                <h6 class="f_s_14 f_w_400">USA</h6>
                                                <div id="bar4" class="barfiller">
                                                    <div class="tipWrap">
                                                        <span class="tip"></span>
                                                    </div>
                                                    <span class="fill" data-percentage="81"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="single_progressbar">
                                                <h6>Australia</h6>
                                                <div id="bar5" class="barfiller">
                                                    <div class="tipWrap">
                                                        <span class="tip"></span>
                                                    </div>
                                                    <span class="fill" data-percentage="58"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="single_progressbar">
                                                <h6>Brazil</h6>
                                                <div id="bar6" class="barfiller">
                                                    <div class="tipWrap">
                                                        <span class="tip"></span>
                                                    </div>
                                                    <span class="fill" data-percentage="42"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="single_progressbar">
                                                <h6>Latvia</h6>
                                                <div id="bar7" class="barfiller">
                                                    <div class="tipWrap">
                                                        <span class="tip"></span>
                                                    </div>
                                                    <span class="fill" data-percentage="55"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="white_card card_height_100 mb_30">
                    <div class="white_card_header">
                        <div class="box_header m-0">
                            <div class="main-title">
                                <h3 class="m-0">Recent Update</h3>
                            </div>
                        </div>
                    </div>
                    <div class="white_card_body">
                        <div class="Activity_timeline">
                            <ul>
                                <li>
                                    <div class="activity_bell"></div>
                                    <div class="timeLine_inner d-flex align-items-center">
                                        <div class="activity_wrap">
                                            <h6>5 min ago</h6>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque
                                                scelerisque
                                            </p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="activity_bell "></div>
                                    <div class="timeLine_inner d-flex align-items-center">
                                        <div class="activity_wrap">
                                            <h6>5 min ago</h6>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque
                                                scelerisque
                                            </p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="activity_bell bell_lite"></div>
                                    <div class="timeLine_inner d-flex align-items-center">
                                        <div class="activity_wrap">
                                            <h6>5 min ago</h6>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque
                                                scelerisque
                                            </p>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="activity_bell bell_lite"></div>
                                    <div class="timeLine_inner d-flex align-items-center">
                                        <div class="activity_wrap">
                                            <h6>5 min ago</h6>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque
                                                scelerisque
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="white_card card_height_100 mb_30">
                    <div class="date_picker_wrapper">
                        <div class="default-datepicker">
                            <div class="datepicker-here" data-language="en"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="white_card card_height_100 mb_30">
                    <div class="weatcher_update_wrapper height_100">
                        <div class="row height_100">
                            <div class="col-lg-6">
                                <div class="box_header m-0">
                                    <div class="main-title">
                                        <h3 class="m-0">Weather Update</h3>
                                    </div>
                                </div>
                                <div class="weather_img_1 mt_30">
                                    <img class="img-fluid" src="img/man.png" alt>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="weather_img_2">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
