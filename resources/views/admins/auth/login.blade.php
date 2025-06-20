<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Analytic</title>
    <link rel="icon" href="{{ asset('admins') }}/img/mini_logo.png" type="image/png">

    <link rel="stylesheet" href="{{ asset('admins') }}/css/bootstrap1.min.css" />

    <link rel="stylesheet" href="vendors/themefy_icon/themify-icons.css" />
    <link rel="stylesheet" href="vendors/font_awesome/css/all.min.css" />


    <link rel="stylesheet" href="vendors/scroll/scrollable.css" />

    <link rel="stylesheet" href="{{ asset('admins') }}/css/metisMenu.css">

    <link rel="stylesheet" href="{{ asset('admins') }}/css/style1.css" />
    <link rel="stylesheet" href="{{ asset('admins') }}/css/colors/default.css" id="colorSkinCSS">
</head>

<body class="crm_body_bg">
    <section class="main_content dashboard_part large_header_bg">
        <div class="main_content_iner flex-direction-column justify-content-center align-items-center">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="white_box mb_30">
                            <div class="row d-flex flex-direction-column justify-content-center align-items-center">
                                <div class="col-lg-6">
                                    <div class="modal-content cs_modal">
                                        <div class="modal-header justify-content-center theme_bg_1">
                                            <h5 class="modal-title text_white">Đăng nhập</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('admins.postLogin') }}" method="post">
                                                @csrf
                                                <div class>
                                                    <input type="text" class="form-control"
                                                        placeholder="Email đăng nhập" name="email"
                                                        value="{{ old('email') }}">
                                                    @error('email')
                                                        <p class="text-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div class>
                                                    <input type="password" class="form-control"
                                                        placeholder="Mật khẩu đăng nhập" name="password">
                                                    @error('password')
                                                        <p class="text-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <button class="btn_1 full_width text-center">Đăng nhập</button>

                                            </form>
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
    </section>


    <script src="{{ asset('admins') }}/js/jquery1-3.4.1.min.js"></script>

    <script src="{{ asset('admins') }}/js/popper1.min.js"></script>

    <script src="{{ asset('admins') }}/js/bootstrap1.min.js"></script>

    <script src="{{ asset('admins') }}/js/metisMenu.js"></script>

    <script src="vendors/scroll/perfect-scrollbar.min.js"></script>
    <script src="vendors/scroll/scrollable-custom.js"></script>

    <script src="{{ asset('admins') }}/js/custom.js"></script>
</body>

</html>
