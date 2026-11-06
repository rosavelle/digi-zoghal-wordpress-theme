(function () {
	'use strict';

	var config = window.digiZoghalAccount || {};
	var root = document.querySelector('[data-dz-otp]');
	if (!root || !config.enabled) {
		return;
	}

	var phoneInput = root.querySelector('#dz-otp-phone');
	var codeInput = root.querySelector('#dz-otp-code');
	var sendButton = root.querySelector('[data-dz-otp-send]');
	var verifyButton = root.querySelector('[data-dz-otp-verify]');
	var changeButton = root.querySelector('[data-dz-otp-change]');
	var codeStage = root.querySelector('[data-dz-otp-code-stage]');
	var status = root.querySelector('[data-dz-otp-status]');
	var timer = root.querySelector('[data-dz-otp-timer]');
	var timerLabel = root.querySelector('[data-dz-otp-timer-label]');
	var timerValue = root.querySelector('[data-dz-otp-timer-value]');
	var authGrid = document.querySelector('[data-dz-auth-grid]');
	var loginCard = document.querySelector('[data-dz-login-card]');
	var registrationForm = document.querySelector('[data-dz-registration-form]');
	var countdownTimer = null;

	function toEnglishDigits(value) {
		return String(value || '')
			.replace(/[۰-۹]/g, function (digit) {
				return String('۰۱۲۳۴۵۶۷۸۹'.indexOf(digit));
			})
			.replace(/[٠-٩]/g, function (digit) {
				return String('٠١٢٣٤٥٦٧٨٩'.indexOf(digit));
			});
	}

	function normalizePhone(value) {
		var phone = toEnglishDigits(value).replace(/[^0-9+]/g, '');
		if (phone.indexOf('+98') === 0) {
			phone = '0' + phone.slice(3);
		} else if (phone.indexOf('0098') === 0) {
			phone = '0' + phone.slice(4);
		} else if (phone.indexOf('98') === 0 && phone.length === 12) {
			phone = '0' + phone.slice(2);
		} else if (phone.indexOf('9') === 0 && phone.length === 10) {
			phone = '0' + phone;
		}
		return phone;
	}

	function setStatus(message, type) {
		status.textContent = message || '';
		status.classList.remove('is-error', 'is-success');
		if (type) {
			status.classList.add('is-' + type);
		}
	}

	function setBusy(button, isBusy, busyText) {
		if (!button.dataset.defaultText) {
			button.dataset.defaultText = button.textContent;
		}
		button.disabled = isBusy;
		button.textContent = isBusy ? busyText : button.dataset.defaultText;
	}

	function request(action, fields) {
		var body = new FormData();
		body.append('action', action);
		body.append('nonce', config.nonce || '');
		Object.keys(fields).forEach(function (key) {
			body.append(key, fields[key]);
		});

		return fetch(config.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body
		}).then(function (response) {
			return response.text().then(function (text) {
				var payload;
				try {
					payload = JSON.parse(text);
				} catch (error) {
					throw new Error((config.messages && config.messages.networkError) || 'خطا در ارتباط با سرور.');
				}
				if (!response.ok || !payload.success) {
					throw new Error(payload.data && payload.data.message ? payload.data.message : ((config.messages && config.messages.networkError) || 'خطا در ارتباط با سرور.'));
				}
				return payload.data || {};
			});
		});
	}

	function startCountdown(seconds) {
		var remaining = Math.max(0, Number(seconds) || 120);
		window.clearInterval(countdownTimer);
		sendButton.disabled = true;
		verifyButton.disabled = false;
		sendButton.textContent = (config.messages && config.messages.sendAgain) || 'ارسال مجدد کد';
		timer.classList.remove('is-expired');
		timerLabel.textContent = 'زمان باقی‌مانده برای واردکردن کد';

		function formatTime(totalSeconds) {
			var minutes = Math.floor(totalSeconds / 60);
			var secondsPart = totalSeconds % 60;
			return String(minutes).padStart(2, '0') + ':' + String(secondsPart).padStart(2, '0');
		}

		function render() {
			timerValue.textContent = formatTime(remaining);
			if (remaining <= 0) {
				window.clearInterval(countdownTimer);
				sendButton.disabled = false;
				verifyButton.disabled = true;
				sendButton.textContent = (config.messages && config.messages.sendAgain) || 'ارسال دوباره کد';
				timer.classList.add('is-expired');
				timerLabel.textContent = 'کد منقضی شد';
				setStatus('مهلت دو دقیقه‌ای کد تمام شد؛ کد تازه‌ای دریافت کنید.', 'error');
				return;
			}
			remaining -= 1;
		}

		render();
		countdownTimer = window.setInterval(render, 1000);
	}

	function sendOtp() {
		var phone = normalizePhone(phoneInput.value);
		phoneInput.value = phone;
		if (!/^09\d{9}$/.test(phone)) {
			setStatus((config.messages && config.messages.invalidPhone) || 'شماره موبایل صحیح نیست.', 'error');
			phoneInput.focus();
			return;
		}

		setStatus('');
		setBusy(sendButton, true, 'در حال ارسال…');
		request('digi_zoghal_send_otp', { phone: phone })
			.then(function (data) {
				phoneInput.readOnly = true;
				codeStage.hidden = false;
				setStatus(data.message || 'کد ورود پیامک شد.', 'success');
				startCountdown(data.expires || data.cooldown || 120);
				codeInput.focus();
			})
			.catch(function (error) {
				setBusy(sendButton, false, '');
				setStatus(error.message, 'error');
			});
	}

	function verifyOtp() {
		var phone = normalizePhone(phoneInput.value);
		var code = toEnglishDigits(codeInput.value).replace(/\D/g, '').slice(0, 6);
		codeInput.value = code;
		if (!/^\d{6}$/.test(code)) {
			setStatus((config.messages && config.messages.invalidCode) || 'کد شش‌رقمی را وارد کنید.', 'error');
			codeInput.focus();
			return;
		}

		setStatus('');
		setBusy(verifyButton, true, 'در حال بررسی…');
		request('digi_zoghal_verify_otp', { phone: phone, code: code })
			.then(function (data) {
				setStatus(data.message || 'ورود انجام شد.', 'success');
				if (data.state === 'register' && registrationForm) {
					window.clearInterval(countdownTimer);
					registrationForm.querySelector('#reg_username').value = data.phone || phone;
					registrationForm.querySelector('[data-dz-registration-token]').value = data.registrationToken || '';
					loginCard.hidden = true;
					registrationForm.hidden = false;
					authGrid.classList.remove('is-phone-entry');
					authGrid.classList.add('is-registration');
					registrationForm.querySelector('#reg_first_name').focus();
					return;
				}
				window.location.assign(data.redirect || config.redirect || window.location.href);
			})
			.catch(function (error) {
				setBusy(verifyButton, false, '');
				setStatus(error.message, 'error');
			});
	}

	function changePhone() {
		window.clearInterval(countdownTimer);
		phoneInput.readOnly = false;
		codeStage.hidden = true;
		codeInput.value = '';
		timer.classList.remove('is-expired');
		timerLabel.textContent = 'زمان باقی‌مانده برای واردکردن کد';
		timerValue.textContent = '02:00';
		sendButton.disabled = false;
		verifyButton.disabled = false;
		sendButton.textContent = sendButton.dataset.defaultText || 'دریافت کد ورود';
		setStatus('');
		phoneInput.focus();
	}

	sendButton.addEventListener('click', sendOtp);
	verifyButton.addEventListener('click', verifyOtp);
	changeButton.addEventListener('click', changePhone);
	phoneInput.addEventListener('keydown', function (event) {
		if (event.key === 'Enter') {
			event.preventDefault();
			sendOtp();
		}
	});
	codeInput.addEventListener('keydown', function (event) {
		if (event.key === 'Enter') {
			event.preventDefault();
			verifyOtp();
		}
	});

	var passwordDetails = document.querySelector('[data-dz-password-login]');
	if (passwordDetails) {

		var passwordPhone = passwordDetails.querySelector('#username');
		passwordDetails.addEventListener('toggle', function () {
			if (passwordDetails.open && !passwordPhone.value) {
				passwordPhone.value = normalizePhone(phoneInput.value);
			}
		});

	}

	if (registrationForm) {
		var registrationPassword = registrationForm.querySelector('#reg_password');
		var registrationPasswordConfirmation = registrationForm.querySelector('#reg_password_confirmation');
		var nationalId = registrationForm.querySelector('#reg_national_id');

		function syncPasswordConfirmation() {
			var message = registrationPassword.value === registrationPasswordConfirmation.value ? '' : 'رمز عبور و تکرار آن یکسان نیستند.';
			registrationPasswordConfirmation.setCustomValidity(message);
		}

		registrationPassword.addEventListener('input', syncPasswordConfirmation);
		registrationPasswordConfirmation.addEventListener('input', syncPasswordConfirmation);
		nationalId.addEventListener('input', function () {
			nationalId.value = toEnglishDigits(nationalId.value).replace(/\D/g, '').slice(0, 10);
		});
		registrationForm.addEventListener('submit', function (event) {
			syncPasswordConfirmation();
			if (registrationPassword.value !== registrationPasswordConfirmation.value) {
				event.preventDefault();
				registrationPasswordConfirmation.reportValidity();
			}
		});
	}
})();

