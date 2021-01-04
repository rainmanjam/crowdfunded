 <div class="pledges-form-block text-dark mb-5">

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Success of pledge -->
    @if (Session::has('message'))
        <div class="alert alert-{{Session::get('class')}}">
            {{ Session::get('message') }}
        </div>
    @endif

    <div class="pledges-form-block text-dark mb-5">
        <form action="/stripe/pre-auth" method="POST" id="payment-form" name="paymentForm">
            @csrf
            <div>
                <div class="tab-progress row align-items-center text-center no-gutters py-3">
                    <div class="col tab-progress-dot active">
                        <span></span>
                    </div>
                    <div class="col tab-progress-dot">
                        <span></span>
                    </div>
                    <div class="col tab-progress-dot">
                        <span></span>
                    </div>
                </div>

                <!-- One "tab" for each step in the form: -->
                <div class="tab pt-3" style="display: block;">
                    <div class="row pl-4 mb-2">
                        <div class="col-sm">
                            <h4 class="color-accent-1 tab-title"><span>1</span> Pledge Form</h4>
                        </div>
                        <div class="col-sm-auto">
                            <h4 class="total_pledge_block">Amount: $<span class="total_amount">25</span></h4>
                        </div>
                    </div>

                    <div class="px-5 pb-4">
                        <div class="form-row pl-4">
                            <div class="form-group col">
                                <div class="pledge_level_radio">
                                    <input type="radio" name="pledge_level" id="pledge_level_customer" value="customer" onclick="calculateTotal();" checked="checked" />
                                    <label for="pledge_level_customer">
                                        <span>$25</span>
                                        Customer
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col">
                                <div class="pledge_level_radio">
                                    <input type="radio" name="pledge_level" id="pledge_level_creator" value="creator" onclick="calculateTotal();">
                                    <label for="pledge_level_creator">
                                        <span>$50</span>
                                        Creator
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col">
                                <div class="pledge_level_radio">
                                    <input type="radio" name="pledge_level" id="pledge_level_developer" value="developer" onclick="calculateTotal();">
                                    <label for="pledge_level_developer">
                                        <span>$100</span>
                                        Developer
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-row pl-4">
                            <div class="form-group col-sm-12">
                                <label for="token_amount_custom">Number of Pledges</label>
                                <input type="number" class="form-control" min="1" id="token_amount_custom" oninput="calculateTotal()" name="token_amount_custom" value="1">
                            </div>
                        </div>
                    </div>

                    <div class="form-row pl-4 pr-5 py-3 align-items-center text-center">
                        <div class="col-sm-auto mb-2 mb-sm-0">
                            <img src="https://chnlcrowdfundingcdn.b-cdn.net/images/powered_by_stripe.png" alt="Powered By Stripe">
                        </div>
                        <div class="col-sm-3 mb-2 mb-sm-0">
                        </div>

                        <div class="col-sm mb-2 mb-sm-0">
                            <button type="button" class="btn btn-main w-100" id="nextBtn" onclick="nextPrev(1);">Next Step</button>
                            <button type="submit" class="btn btn-main w-100" id="confirmDonation" style="display: none">Confirm my donation</button>
                        </div>
                    </div>
                </div>

                <!-- Tab 2 -->
                <div class="tab pt-3">
                    <div class="row pl-4 mb-2">
                        <div class="col-sm">
                            <h4 class="color-accent-1 tab-title"><span>2</span> Pledge Information</h4>
                        </div>
                        <div class="col-sm-auto">
                            <h4 class="total_pledge_block">Amount: $<span class="total_amount">25</span></h4>
                        </div>
                    </div>

                    <div class="px-5">
                        <div class="form-row pl-4 align-items-center">
                            <div class="form-group col">
                                <input type="text" name="fname" class="form-control" placeholder="First Name" />
                            </div>
                            <div class="form-group col">
                                <input type="text" name="lname" class="form-control" placeholder="Last Name" />
                            </div>
                        </div>

                        <div class="form-row pl-4 align-items-center">

                            <div class="form-group col-auto">
                                <input type="checkbox" name="remain_anon" value="1" id="remain_anon">
                                <label for="remain_anon">Remain Anonymous</label>
                            </div>
                        </div>

                        <div class="form-group pl-4">
                            <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Enter email address" required />
                        </div>

                        <div class="form-group pl-4">
                            <textarea name="message" id="message" class="form-control" placeholder="Optional message (limit 100 Char)" maxlength="100"></textarea>
                        </div>

                        <div class="form-row pl-4">
                            <div class="form-group col-sm mb-1">
                                <input type="checkbox" id="nlSignup" name="nlSignup" value="1">
                                <label for="nlSignup">Subscribe to Newsletter?</label>
                            </div>
                            <div class="form-group col-sm-auto mb-1">
                                <input type="checkbox" id="forumSignup" name="forumSignup" value="1">
                                <label for="forumSignup">Subscribe to our Forum?</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-row pl-4 pr-5 py-3 align-items-center text-center">
                        <div class="col-sm-auto mb-2 mb-sm-0">
                            <img src="https://chnlcrowdfundingcdn.b-cdn.net/images/powered_by_stripe.png" alt="Powered By Stripe">
                        </div>
                        <div class="col-sm-3 mb-2 mb-sm-0">
                            <button type="button" class="btn btn-accent-2 w-100 pull-right" id="prevBtn" onclick="goPrev(-1)">Back</button>
                        </div>

                        <div class="col-sm mb-2 mb-sm-0">
                            <button type="button" class="btn btn-main w-100" id="nextBtn" onclick="nextPrev(1);">Next Step</button>
                        </div>
                    </div>
                </div>

                <!-- Tab 3 -->
                <div class="tab pt-3">
                    <div class="row pl-4 mb-2">
                        <div class="col-sm">
                            <h4 class="color-accent-1 tab-title"><span>3</span> Payment Information</h4>
                        </div>
                        <div class="col-sm-auto">
                            <h4 class="total_pledge_block">Amount: $<span class="total_amount">25</span></h4>
                        </div>
                    </div>

                    <div class="px-5">
                        <div class="pl-4">
                            <div id="card-element">
                                <!-- A Stripe Element will be inserted here. -->
                            </div>
                            <div id="card-errors" class="mb-3"></div>

                            <h6>Terms of Service and Privacy Policy</h6>
                            <p class="small mb-2">
                                The links at the bottom of the page will redirect you to the <a href="https://www.iubenda.com/terms-and-conditions/54652815" title="Go to Terms amd Conditions">Terms and Conditions</a> and the <a href="https://www.iubenda.com/privacy-policy/54652815" title="Go to Privacy Policy"> Privacy Policy</a> or you can click on they hyperlinks in this sentence.
                            </p>

                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <label class="form-check-label" for="form-check-agree">
                                        <input type="checkbox" name="form_check_radio" id="form-check-agree" class="form-check-input" value="1" required/>
                                        I agree to the TOS and Privacy Policy
                                    </label>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="form-row pl-4 pr-5 py-3 align-items-center text-center">
                        <div class="col-sm-auto mb-2 mb-sm-0">
                            <img src="https://chnlcrowdfundingcdn.b-cdn.net/images/powered_by_stripe.png" alt="Powered By Stripe">
                        </div>
                        <div class="col-sm-3 mb-2 mb-sm-0">
                            <button type="button" class="btn btn-accent-2 w-100 pull-right" id="prevBtn" onclick="goPrev(-1)">Back</button>
                        </div>

                        <div class="col-sm mb-2 mb-sm-0">
                            <span id="processingPledge" class="btn btn-main w-100" style="display: none"><i class="fas fa-spinner fa-pulse"></i> Processing...</span>
                            <button type="submit" class="btn btn-main w-100" id="confirmDonation">Confirm my donation</button>
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </div>

</div>
