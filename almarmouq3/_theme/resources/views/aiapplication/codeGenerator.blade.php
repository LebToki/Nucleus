@extends('layout.layout')
@php
    $title='Code Generator';
    $subTitle = 'Code Generator';
@endphp

@section('content')

            <div class="row gy-4 flex-wrap-reverse">
                <div class="col-xxl-3 col-lg-4">
                    <div class="card h-100 p-0">
                        <div class="card-body p-0">
                            <div class="p-24">
                                <a  href="{{ route('codeGeneratorNew') }}" class="btn btn-primary text-sm btn-sm px-12 py-12 w-100 radius-8 d-flex align-items-center justify-content-center gap-2">
                                    <i class="ri-messenger-line"></i>
                                    New Page
                                </a>
                            </div>

                            <ul class="ai-chat-list scroll-sm pe-24 ps-24 pb-24">
                                <li class="mb-16 mt-0"><span class="text-primary-600 text-sm fw-semibold">Today</span></li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Please create a 5 Column table with HTML Css and js </a>
                                </li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Calorie-dense foods: Needs, healthy</a>
                                </li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Calorie-dense foods: Needs, healthy</a>
                                </li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Calorie-dense foods: Needs, healthy</a>
                                </li>

                                <li class="mb-16 mt-24"><span class="text-primary-600 text-sm fw-semibold">Yesterday</span></li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Online School Education Learning</a>
                                </li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Calorie-dense foods: Needs, healthy</a>
                                </li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Calorie-dense foods: Needs, healthy</a>
                                </li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Calorie-dense foods: Needs, healthy</a>
                                </li>

                                <li class="mb-16 mt-24"><span class="text-primary-600 text-sm fw-semibold">17/06/2024</span></li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Online School Education Learning</a>
                                </li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Calorie-dense foods: Needs, healthy</a>
                                </li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Calorie-dense foods: Needs, healthy</a>
                                </li>
                                <li class="mb-16">
                                    <a  href="{{ route('codeGenerator') }}" class="text-line-1 text-secondary-light text-hover-primary-600">Calorie-dense foods: Needs, healthy</a>
                                </li>

                                <li class="mb-16 mt-24"><span class="text-primary-600 text-sm fw-semibold">15/06/2024</span></li>
                                <li class="mb-0">
                                    <a  href="" class="text-line-1 text-secondary-light text-hover-primary-600">Calorie-dense foods: Needs, healthy</a>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
                <div class="col-xxl-9 col-lg-8">
                    <div class="chat-main card overflow-hidden">
                        <div class="chat-sidebar-single gap-8 justify-content-between cursor-default flex-nowrap">
                            <div class="d-flex align-items-center gap-16">
                                <a  href="{{ route('codeGeneratorNew') }}" class="text-primary-light text-2xl line-height-1"><i class="ri-arrow-left-line"></i></a>
                                <h6 class="text-lg mb-0 text-line-1">Please create a 5 Column table with HTML Css and js</h6>
                            </div>

                            <div class="d-flex align-items-center gap-16 flex-shrink-0">
                                <button type="button" class="text-secondary-light text-xl line-height-1 text-hover-primary-600"><i class="ri-edit-2-line"></i></button>
                                <button type="button" class="text-secondary-light text-xl line-height-1 text-hover-primary-600"><i class="ri-delete-bin-6-line"></i></button>
                            </div>
                        </div><!-- chat-sidebar-single end -->
                        <div class="chat-message-list max-h-612-px min-h-612-px">

                            <!-- User generated Text Start -->
                            <div class="d-flex align-items-start justify-content-between gap-16 border-bottom border-neutral-200 pb-16 mb-16">
                                <div class="d-flex align-items-center gap-16">
                                    <div class="img overflow-hidden flex-shrink-0">
                                        <img src="{{ asset('assets/images/chat/1.png') }}" alt="image" class="w-44-px h-44-px rounded-circle object-fit-cover">
                                    </div>
                                    <div class="info flex-grow-1">
                                        <h6 class="text-lg mb-4">Adam Milner</h6>
                                        <p class="mb-0 text-secondary-light text-sm">Please create a 5 Column table with HTML Css and js </p>
                                    </div>
                                </div>
                                <button type="button" class="d-flex align-items-center gap-6 px-8 py-4 bg-primary-50 radius-4 bg-hover-primary-100 flex-shrink-0"> <i class="ri-edit-2-fill"></i> Edit</button>
                            </div>
                            <!-- User generated Text End -->

                            <!-- WowDash generated Text Start -->
                            <div class="d-flex align-items-start gap-16 border-bottom border-neutral-200 pb-16 mb-16">
                                <div class="img overflow-hidden flex-shrink-0">
                                    <img src="{{ asset('assets/images/wow-dash-favicon.png') }}" alt="image" class="w-44-px h-44-px rounded-circle object-fit-cover">
                                </div>
                                <div class="info flex-grow-1">
                                    <h6 class="text-lg mb-16 mt-8">WowDash</h6>
                                    <pre class="language-html mb-16 text-secondary-light text-sm">
                                            <code>
                                    &lt;html lang=&quot;en&quot;&gt;
                                        &lt;head&gt;
                                            &lt;meta charset=&quot;UTF-8&quot;&gt;
                                            &lt;meta name=&quot;viewport&quot; content=&quot;width=device-width, initial-scale=1.0&quot;&gt;
                                            &lt;link rel=&quot;stylesheet&quot; href=&quot;styles.css&quot;&gt;
                                            &lt;title&gt;5 Column Table&lt;/title&gt;
                                        &lt;/head&gt;
                                        &lt;body&gt;
                                            &lt;div class=&quot;table-container&quot;&gt;
                                                &lt;table id=&quot;data-table&quot;&gt;
                                                    &lt;thead&gt;
                                                    &lt;tr&gt;
                                                        &lt;th&gt;Column 1&lt;/th&gt;
                                                        &lt;th&gt;Column 2&lt;/th&gt;
                                                        &lt;th&gt;Column 3&lt;/th&gt;
                                                        &lt;th&gt;Column 4&lt;/th&gt;
                                                        &lt;th&gt;Column 5&lt;/th&gt;
                                                    &lt;/tr&gt;
                                                    &lt;/thead&gt;
                                                    &lt;tbody&gt;
                                                    &lt;!-- Table content goes here --&gt;            
                                                    &lt;/tbody&gt;
                                                &lt;/table&gt;
                                            &lt;/div&gt;
                                            &lt;script src=&quot;script.js&quot;&gt;&lt;/script&gt;
                                        &lt;/body&gt;
                                    &lt;/html&gt;
                                            </code>
                                        </pre>

                                    <div class="mt-24 d-flex align-items-center justify-content-between gap-16">
                                        <div class="d-flex align-items-center gap-20 bg-neutral-50 radius-8 px-16 py-10 line-height-1">
                                            <button type="button" class="text-secondary-light text-2xl d-flex text-hover-info-600"><i class="ri-thumb-up-line line-height-1"></i></button>
                                            <button type="button" class="text-secondary-light text-2xl d-flex text-hover-info-600"><i class="ri-thumb-down-line"></i></button>
                                            <button type="button" class="text-secondary-light text-2xl d-flex text-hover-info-600"><i class="ri-share-forward-line"></i></button>
                                            <button type="button" class="text-secondary-light text-2xl d-flex text-hover-info-600"><i class="ri-file-copy-line"></i></button>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-8"> <i class="ri-repeat-2-line"></i> Regenerate</button>
                                    </div>
                                </div>
                            </div>
                            <!-- WowDash generated Text End -->

                            <!-- User generated Text Start -->
                            <div class="d-flex align-items-start justify-content-between gap-16 border-bottom border-neutral-200 pb-16 mb-16">
                                <div class="d-flex align-items-center gap-16">
                                    <div class="img overflow-hidden flex-shrink-0">
                                        <img src="{{ asset('assets/images/chat/1.png') }}" alt="image" class="w-44-px h-44-px rounded-circle object-fit-cover">
                                    </div>
                                    <div class="info flex-grow-1">
                                        <h6 class="text-lg mb-4">Adam Milner</h6>
                                        <p class="mb-0 text-secondary-light text-sm">Please create a 5 Column table with HTML Css and js </p>
                                    </div>
                                </div>
                                <button type="button" class="d-flex align-items-center gap-6 px-8 py-4 bg-primary-50 radius-4 bg-hover-primary-100 flex-shrink-0"> <i class="ri-edit-2-fill"></i> Edit</button>
                            </div>
                            <!-- User generated Text End -->
                        </div>
                        <form class="chat-message-box">
                            <input type="text" name="chatMessage" placeholder="Message wowdash...">
                            <button type="submit" class="w-44-px h-44-px d-flex justify-content-center align-items-center radius-8 bg-primary-600 text-white bg-hover-primary-700 text-xl">
                                <iconify-icon icon="f7:paperplane"></iconify-icon>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

@endsection