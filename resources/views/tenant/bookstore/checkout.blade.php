@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-md-5 col-lg-4 order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">Your Cart</span>
                    <span class="badge bg-primary rounded-pill">{{ count($cart) }}</span>
                </h4>
                <ul class="list-group mb-3 shadow-sm border-0">
                    @foreach ($cart as $details)
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0 text-truncate" style="max-width: 200px;">{{ $details['title'] }}</h6>
                                <small class="text-muted">Qty: {{ $details['quantity'] }}</small>
                            </div>
                            <span class="text-muted">{{ format_money($details['price'] * $details['quantity']) }}</span>
                        </li>
                    @endforeach
                    <li class="list-group-item d-flex justify-content-between bg-light">
                        <span class="fw-bold">Total ({{ current_currency() }})</span>
                        <strong class="text-primary">{{ format_money($total) }}</strong>
                    </li>
                </ul>
            </div>
            <div class="col-md-7 col-lg-8">
                <h4 class="mb-3">Billing Details</h4>
                <form action="{{ route('tenant.bookstore.checkout.process') }}" method="POST" class="needs-validation"
                    novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="customer_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                            <div class="invalid-feedback">
                                Valid full name is required.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="customer_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email"
                                placeholder="you@example.com" required>
                            <div class="invalid-feedback">
                                Please enter a valid email address for shipping updates.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="customer_phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone"
                                placeholder="+1234567890" required>
                            <div class="invalid-feedback">
                                Please enter a valid phone number.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="shipping_address" class="form-label">Shipping Address</label>
                            <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3"
                                placeholder="1234 Main St, City, Country" required></textarea>
                            <div class="invalid-feedback">
                                Please enter your shipping address.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label">Order Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h4 class="mb-3">Payment Method</h4>

                    <div class="my-3">
                        <div class="form-check">
                            <input id="cash" name="payment_method" type="radio" class="form-check-input"
                                value="cash" checked required>
                            <label class="form-check-label" for="cash">Cash on Delivery</label>
                        </div>
                        <div class="form-check">
                            <input id="mobile_money" name="payment_method" type="radio" class="form-check-input"
                                value="mobile_money" required>
                            <label class="form-check-label" for="mobile_money">Mobile Money</label>
                        </div>
                        <div class="form-check">
                            <input id="bank_transfer" name="payment_method" type="radio" class="form-check-input"
                                value="bank_transfer" required>
                            <label class="form-check-label" for="bank_transfer">Bank Transfer</label>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        Payment instructions will be provided after placing your order.
                    </div>

                    <hr class="my-4">

                    <button class="w-100 btn btn-primary btn-lg" type="submit">Place Order</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
@endsection
