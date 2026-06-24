@once
    <div id="checkout-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-950/60 px-4">
        <div class="w-full max-w-md rounded-lg bg-white shadow-xl">
            <div class="border-b px-6 py-4">
                <div class="text-lg font-semibold text-gray-950">Checkout</div>
                <div id="checkout-method-label" class="mt-1 text-sm text-gray-500"></div>
                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4" aria-label="Accepted payment methods">
                    <div data-checkout-logo="mobile_money" class="flex h-11 items-center justify-center rounded-md border border-gray-200 bg-white px-2 text-sm font-black tracking-tight text-emerald-700">
                        EcoCash
                    </div>
                    <div data-checkout-logo="zimswitch" class="flex h-11 items-center justify-center rounded-md border border-gray-200 bg-white px-2 text-xs font-black uppercase tracking-wide text-sky-800">
                        ZimSwitch
                    </div>
                    <div data-checkout-logo="visa" class="flex h-11 items-center justify-center rounded-md border border-gray-200 bg-white px-2 text-lg font-black uppercase tracking-wide text-blue-800">
                        VISA
                    </div>
                    <div data-checkout-logo="mastercard" class="flex h-11 items-center justify-center rounded-md border border-gray-200 bg-white px-2">
                        <span class="mr-[-6px] h-5 w-5 rounded-full bg-red-600"></span>
                        <span class="h-5 w-5 rounded-full bg-amber-400 opacity-90"></span>
                        <span class="ml-2 text-xs font-black uppercase tracking-tight text-gray-800">Mastercard</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4 px-6 py-5">
                <div id="checkout-error" class="hidden rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></div>
                <div id="checkout-success" class="hidden rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800">
                    Payment successful. Completing checkout...
                </div>

                <div data-delivery-fields class="hidden space-y-4 rounded-md border border-gray-200 bg-gray-50 p-4">
                    <div class="text-sm font-semibold text-gray-900">Delivery Details</div>
                    <div>
                        <x-input-label for="checkout_delivery_address" value="Delivery Address" />
                        <x-text-input id="checkout_delivery_address" class="mt-1 block w-full" placeholder="Street address" />
                    </div>
                    <div>
                        <x-input-label for="checkout_contact_mobile" value="Contact Mobile" />
                        <x-text-input id="checkout_contact_mobile" class="mt-1 block w-full" type="tel" inputmode="tel" placeholder="263772000000" />
                    </div>
                    <div>
                        <x-input-label for="checkout_landmark" value="Landmark" />
                        <x-text-input id="checkout_landmark" class="mt-1 block w-full" placeholder="Optional" />
                    </div>
                </div>

                <div data-mobile-fields class="space-y-4">
                    <div>
                        <x-input-label for="checkout_mobile" value="Mobile Number" />
                        <x-text-input id="checkout_mobile" class="mt-1 block w-full" type="tel" inputmode="tel" autocomplete="tel" placeholder="263772000000" />
                    </div>
                    <div>
                        <x-input-label for="checkout_pin" value="Mobile Money PIN" />
                        <x-text-input id="checkout_pin" class="mt-1 block w-full" type="password" inputmode="numeric" autocomplete="one-time-code" maxlength="8" />
                    </div>
                </div>

                <div data-card-fields class="hidden space-y-4">
                    <div>
                        <x-input-label for="checkout_card_number" value="Card Number" />
                        <x-text-input id="checkout_card_number" class="mt-1 block w-full" inputmode="numeric" autocomplete="cc-number" placeholder="4111111111111111" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="checkout_card_expiry" value="Expiry" />
                            <x-text-input id="checkout_card_expiry" class="mt-1 block w-full" autocomplete="cc-exp" placeholder="MM/YY" />
                        </div>
                        <div>
                            <x-input-label for="checkout_card_cvv" value="CVV" />
                            <x-text-input id="checkout_card_cvv" class="mt-1 block w-full" type="password" inputmode="numeric" autocomplete="cc-csc" maxlength="4" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t px-6 py-4">
                <button type="button" data-checkout-cancel class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700">Cancel</button>
                <button type="button" data-checkout-confirm class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white">Pay</button>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const modal = document.getElementById('checkout-modal');
            if (!modal) return;

            const methodLabel = document.getElementById('checkout-method-label');
            const errorBox = document.getElementById('checkout-error');
            const successBox = document.getElementById('checkout-success');
            const mobileFields = modal.querySelector('[data-mobile-fields]');
            const cardFields = modal.querySelector('[data-card-fields]');
            const deliveryFields = modal.querySelector('[data-delivery-fields]');
            const logos = modal.querySelectorAll('[data-checkout-logo]');
            const cancelButton = modal.querySelector('[data-checkout-cancel]');
            const confirmButton = modal.querySelector('[data-checkout-confirm]');
            const inputs = {
                mobile: document.getElementById('checkout_mobile'),
                pin: document.getElementById('checkout_pin'),
                cardNumber: document.getElementById('checkout_card_number'),
                cardExpiry: document.getElementById('checkout_card_expiry'),
                cardCvv: document.getElementById('checkout_card_cvv'),
                deliveryAddress: document.getElementById('checkout_delivery_address'),
                contactMobile: document.getElementById('checkout_contact_mobile'),
                landmark: document.getElementById('checkout_landmark'),
            };
            let activeForm = null;
            let activeMethod = 'mobile_money';

            const labels = {
                mobile_money: 'Mobile Money',
                zimswitch: 'Zimswitch Card',
                visa: 'Visa',
                mastercard: 'Mastercard',
            };

            const resetModal = () => {
                errorBox.classList.add('hidden');
                errorBox.textContent = '';
                successBox.classList.add('hidden');
                confirmButton.disabled = false;
                confirmButton.textContent = 'Pay';
                Object.values(inputs).forEach((input) => input.value = '');
            };

            const setHidden = (name, value) => {
                let input = activeForm.querySelector(`input[name="${name}"]`);
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = name;
                    activeForm.appendChild(input);
                }
                input.value = value;
            };

            const showError = (message) => {
                errorBox.textContent = message;
                errorBox.classList.remove('hidden');
            };

            const highlightLogo = () => {
                logos.forEach((logo) => {
                    const isActive = logo.dataset.checkoutLogo === activeMethod;
                    logo.classList.toggle('border-gray-900', isActive);
                    logo.classList.toggle('ring-2', isActive);
                    logo.classList.toggle('ring-gray-900', isActive);
                    logo.classList.toggle('opacity-40', !isActive);
                });
            };

            document.querySelectorAll('[data-checkout-form]').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (form.dataset.checkoutConfirmed === 'true') {
                        return;
                    }

                    if (!form.checkValidity()) {
                        return;
                    }

                    event.preventDefault();
                    activeForm = form;
                    activeMethod = form.querySelector('[name="payment_method"]')?.value || 'mobile_money';
                    resetModal();
                    methodLabel.textContent = labels[activeMethod] || 'Payment';
                    const isMobile = activeMethod === 'mobile_money';
                    const needsDelivery = form.dataset.deliveryRequired === 'true';
                    highlightLogo();
                    deliveryFields.classList.toggle('hidden', !needsDelivery);
                    mobileFields.classList.toggle('hidden', !isMobile);
                    cardFields.classList.toggle('hidden', isMobile);
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    setTimeout(() => (isMobile ? inputs.mobile : inputs.cardNumber).focus(), 50);
                });
            });

            cancelButton.addEventListener('click', () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                activeForm = null;
            });

            confirmButton.addEventListener('click', () => {
                if (!activeForm) return;

                errorBox.classList.add('hidden');
                if (activeMethod === 'mobile_money') {
                    if (!inputs.mobile.value.trim() || !inputs.pin.value.trim()) {
                        showError('Enter the mobile number and PIN to continue.');
                        return;
                    }
                    setHidden('payment_mobile', inputs.mobile.value.trim());
                    setHidden('payment_pin', inputs.pin.value.trim());
                } else {
                    if (!inputs.cardNumber.value.trim() || !inputs.cardExpiry.value.trim() || !inputs.cardCvv.value.trim()) {
                        showError('Enter the card number, expiry, and CVV to continue.');
                        return;
                    }
                    setHidden('card_number', inputs.cardNumber.value.trim());
                    setHidden('card_expiry', inputs.cardExpiry.value.trim());
                    setHidden('card_cvv', inputs.cardCvv.value.trim());
                }

                if (activeForm.dataset.deliveryRequired === 'true') {
                    if (!inputs.deliveryAddress.value.trim() || !inputs.contactMobile.value.trim()) {
                        showError('Enter the delivery address and contact mobile to continue.');
                        return;
                    }
                    setHidden('delivery_address', inputs.deliveryAddress.value.trim());
                    setHidden('contact_mobile', inputs.contactMobile.value.trim());
                    setHidden('landmark', inputs.landmark.value.trim());
                }

                confirmButton.disabled = true;
                confirmButton.textContent = 'Processing...';
                successBox.classList.remove('hidden');

                setTimeout(() => {
                    activeForm.dataset.checkoutConfirmed = 'true';
                    activeForm.submit();
                }, 900);
            });
        })();
    </script>
@endonce
