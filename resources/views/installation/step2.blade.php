@extends('layouts.blank')

@section('content')
    <div class="text-center text-white mb-4">
        <h2>{{ "SafeCell Software Installation" }}</h2>
        <h6 class="fw-normal">
            {{ "Please proceed step by step with proper data according to instructions" }}
        </h6>
    </div>

    <div class="pb-2">
        <div class="progress cursor-pointer" role="progressbar" aria-label="SafeCell Software Installation"
             aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip"
             data-bs-placement="top" data-bs-custom-class="custom-progress-tooltip" data-bs-title="Second Step!"
             data-bs-delay='{"hide":1000}'>
            <div class="progress-bar width-40"></div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="p-4 mb-md-3 mx-xl-4 px-md-5">
            <div class="d-flex align-items-center column-gap-3 flex-wrap">
                <h5 class="fw-bold fs text-uppercase">{{ "Step 2." }}</h5>
                <h5 class="fw-normal">{{ "Account & License Verification" }}</h5>
            </div>
            <p class="mb-4">
                {{ "Provide your verification details" }}
            </p>

            <form method="POST" action="{{ route('purchase.code') }}">
                @csrf
                <div class="bg-light p-4 rounded mb-4">

                    <div class="px-xl-2 pb-sm-3">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label for="person_name" class="d-flex align-items-center gap-2 mb-2">
                                        <span class="fw-medium">{{ "Name" }}</span>
                                        <span class="cursor-pointer" data-bs-toggle="tooltip"
                                              data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                              data-bs-html="true"
                                              data-bs-title="{{ 'Enter your real full name.' }}">
                                            <img class="svg" alt=""
                                                 src="{{ dynamicAsset(path: 'public/assets/installation/assets/img/svg-icons/info2.svg') }}">
                                        </span>
                                    </label>
                                    <input type="text" id="person_name" class="form-control" name="name"
                                           placeholder="Ex: John Doe" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label for="person_email" class="d-flex align-items-center gap-2 mb-2">
                                        <span class="fw-medium">{{ "Email" }}</span>
                                        <span class="cursor-pointer" data-bs-toggle="tooltip"
                                              data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                              data-bs-html="true"
                                              data-bs-title="Enter your valid email address">
                                            <img class="svg" alt=""
                                                 src="{{ dynamicAsset(path: 'public/assets/installation/assets/img/svg-icons/info2.svg') }}">
                                        </span>
                                    </label>
                                    <input type="email" id="person_email" class="form-control" name="email"
                                           placeholder="{{ 'Ex: your-mail@example.com' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label for="username" class="d-flex align-items-center gap-2 mb-2">
                                        <span class="fw-medium">{{ "Username" }}</span>
                                        <span class="cursor-pointer" data-bs-toggle="tooltip"
                                              data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                              data-bs-html="true"
                                              data-bs-title="Your username">
                                            <img class="svg" alt=""
                                                 src="{{ dynamicAsset(path: 'public/assets/installation/assets/img/svg-icons/info2.svg') }}">
                                        </span>
                                    </label>
                                    <input type="text" id="username" class="form-control" name="username"
                                           placeholder="Ex: safecell" value="safecell" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label for="purchase_key" class="mb-2">{{ "License Key" }}</label>
                                    <input type="text" id="purchase_key" class="form-control" name="purchase_key"
                                           placeholder="Ex: SAFECELL-LICENSE" value="SAFECELL-LICENSE" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-dark px-sm-5">{{ "Continue" }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
