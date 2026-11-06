(function () {
	'use strict';

	var data = window.digiZoghalIranLocations || {};
	var stateSelect = document.querySelector('#billing_state');
	var countySelect = document.querySelector('#billing_county');
	var citySelect = document.querySelector('#billing_city');
	if (!stateSelect || !countySelect || !citySelect) {
		return;
	}

	var provinces = Array.isArray(data.provinces) ? data.provinces : [];
	var counties = Array.isArray(data.counties) ? data.counties : [];
	var cities = Array.isArray(data.cities) ? data.cities : [];
	var selectedCounty = String(data.selectedCounty || countySelect.value || '');
	var selectedCity = String(data.selectedCity || citySelect.value || '');

	function normalizeText(value) {
		var text = String(value || '');
		var persianLabel = text.match(/\(([^()]*[\u0600-\u06ff][^()]*)\)/);
		if (persianLabel) {
			text = persianLabel[1];
		}
		return text
			.replace(/ي/g, 'ی')
			.replace(/ك/g, 'ک')
			.replace(/^استان\s+/, '')
			.replace(/\s+/g, ' ')
			.trim();
	}

	function selectedStateName() {
		var option = stateSelect.options[stateSelect.selectedIndex];
		return option ? normalizeText(option.textContent) : normalizeText(stateSelect.value);
	}

	function currentProvince() {
		var name = selectedStateName();
		return provinces.find(function (province) {
			return normalizeText(province.name) === name || String(province.id) === String(stateSelect.value);
		}) || null;
	}

	function replaceOptions(select, placeholder, records, selectedValue) {
		select.innerHTML = '';
		var placeholderOption = document.createElement('option');
		placeholderOption.value = '';
		placeholderOption.textContent = placeholder;
		select.appendChild(placeholderOption);

		records.forEach(function (record) {
			var option = document.createElement('option');
			option.value = record.name;
			option.textContent = record.name;
			option.dataset.locationId = record.id;
			if (normalizeText(record.name) === normalizeText(selectedValue)) {
				option.selected = true;
			}
			select.appendChild(option);
		});

		select.disabled = records.length === 0;
		select.dispatchEvent(new Event('change', { bubbles: true }));
	}

	function populateCities(valueToSelect) {
		var option = countySelect.options[countySelect.selectedIndex];
		var countyId = option ? option.dataset.locationId : '';
		var matches = cities.filter(function (city) {
			return String(city.county_id) === String(countyId);
		});
		replaceOptions(citySelect, 'شهر را انتخاب کنید', matches, valueToSelect || '');
	}

	function populateCounties(countyToSelect, cityToSelect) {
		var province = currentProvince();
		var matches = province ? counties.filter(function (county) {
			return String(county.province_id) === String(province.id);
		}) : [];
		replaceOptions(countySelect, province ? 'شهرستان را انتخاب کنید' : 'ابتدا استان را انتخاب کنید', matches, countyToSelect || '');
		populateCities(cityToSelect || '');
	}

	stateSelect.addEventListener('change', function () {
		selectedCounty = '';
		selectedCity = '';
		populateCounties('', '');
	});
	countySelect.addEventListener('change', function () {
		populateCities('');
	});

	populateCounties(selectedCounty, selectedCity);

	['billing_postcode', 'billing_house_number', 'billing_phone'].forEach(function (id) {
		var input = document.getElementById(id);
		if (!input) {
			return;
		}
		input.addEventListener('input', function () {
			input.value = String(input.value || '')
				.replace(/[۰-۹]/g, function (digit) { return String('۰۱۲۳۴۵۶۷۸۹'.indexOf(digit)); })
				.replace(/[٠-٩]/g, function (digit) { return String('٠١٢٣٤٥٦٧٨٩'.indexOf(digit)); })
				.replace(/\D/g, '');
		});
	});
})();
