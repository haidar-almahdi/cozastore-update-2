                    <style>
                        .nav-container {
                            display: flex !important;
                            flex-direction: row-reverse !important;
                            width: 100%;
                            padding: 2%;
                            gap: 1%;
                        }

                        .favorite-btn {
                            background: none;
                            border: none;
                            padding: 0;
                            cursor: pointer;
                            color: #fff;
                            transition: color 0.3s ease;
                        }

                        .favorite-btn.active {
                            color: #dc3545;
                        }

                        .favorite-btn i {
                            font-size: 1.2rem;
                        }
                    </style>
                    <ul class="nav-container navbar-nav ml-auto align-items-center">
                        @auth
                            <li class="nav-item position-relative">
                                <button class="favorite-btn nav-link" onclick="window.location.href='{{ route('shop.favorites') }}'">
                                    <i class="fa fa-heart"></i>
                                    <span class="badge badge-danger position-absolute favorite-count" style="top:0;right:0;">0</span>
                                </button>
                            </li>
                            <li class="nav-item position-relative">
                                <a class="nav-link" href="{{ route('shop.my-points.index') }}" title="My Points">
                                    <i class="fa fa-star"></i>
                                    <span class="badge badge-info position-absolute" style="top:0;right:0;">{{ auth()->user()->loyalty_points_balance ?? 0 }}</span>
                                </a>
                            </li>
                        @endauth
                        <li class="nav-item position-relative">
                            <a class="nav-link" href="{{ route('shop.cart') }}">
                                <i class="zmdi zmdi-shopping-cart"></i>
                                <span class="badge badge-danger position-absolute cart-count" style="top:0;right:0;">0</span>
                            </a>
                        </li>
                    </ul>
