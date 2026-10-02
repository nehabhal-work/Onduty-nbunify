import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';
import QRCode from 'qrcode';

document.querySelectorAll('[data-upi-qr]').forEach(async (image) => {
	const paymentUrl = new URL('upi://pay');
	paymentUrl.searchParams.set('pa', image.dataset.upiId);
	paymentUrl.searchParams.set('pn', image.dataset.payeeName);

	try {
		image.src = await QRCode.toDataURL(paymentUrl.toString(), {
			width: 320,
			margin: 2,
		});
	} catch {
		image.hidden = true;
	}
});

document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
	const input = document.getElementById(toggle.getAttribute('aria-controls'));
	const icon = toggle.querySelector('i');

	if (!input || !icon) return;

	toggle.addEventListener('click', () => {
		const isVisible = input.type === 'text';
		const label = isVisible ? 'Show password' : 'Hide password';

		input.type = isVisible ? 'password' : 'text';
		toggle.setAttribute('aria-label', label);
		toggle.setAttribute('title', label);
		toggle.setAttribute('aria-pressed', String(!isVisible));
		icon.classList.toggle('bi-eye', isVisible);
		icon.classList.toggle('bi-eye-slash', !isVisible);
	});
});

document.querySelectorAll('[data-trial-countdown]').forEach((countdown) => {
	const expiry = new Date(countdown.dataset.trialEndsAt).getTime();

	const updateCountdown = () => {
		const remainingSeconds = Math.max(0, Math.floor((expiry - Date.now()) / 1000));
		const days = Math.floor(remainingSeconds / 86400);
		const hours = Math.floor((remainingSeconds % 86400) / 3600);
		const minutes = Math.floor((remainingSeconds % 3600) / 60);
		const seconds = remainingSeconds % 60;

		countdown.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
	};

	updateCountdown();
	window.setInterval(updateCountdown, 1000);
});

const searchableChoices = new WeakMap();

document.querySelectorAll('.ot-duty-filter-form select, .searchable-select').forEach((select) => {
	searchableChoices.set(select, new Choices(select, {
		allowHTML: false,
		itemSelectText: '',
		searchEnabled: true,
		searchPlaceholderValue: 'Search options...',
		searchResultLimit: 100,
		shouldSort: false,
	}));
});

document.querySelectorAll('#duty-section').forEach((sectionSelect) => {
	const form = sectionSelect.closest('form');
	const otSelect = form.querySelector('[name="ot_no"]');
	const departmentSelect = form.querySelector('[name="department"]');
	const sectionRules = {
		'2nd floor section': { ot_no: true, department: true, unit_no: true },
		'4th floor section': { ot_no: true, department: true, unit_no: false },
		'LR section': { ot_no: false, department: true, unit_no: true },
		'Recovery section': { ot_no: false, department: false, unit_no: false },
		'Scope OT section': { ot_no: false, department: true, unit_no: true },
	};
	const restrictedOtNumbers = new Set(['17', '18', '19', '20', '21']);

	const refreshChoices = (select) => {
		const instance = searchableChoices.get(select);
		if (!instance) return;

		const choices = [...select.options].map((option) => ({
			value: option.value,
			label: option.textContent.trim(),
			selected: option.selected,
			disabled: option.disabled,
			placeholder: option.value === '',
		}));

		instance.setChoices(choices, 'value', 'label', true);
	};

	const updateSectionFields = () => {
		const section = sectionSelect.value;
		const rules = sectionRules[section];

		Object.entries(rules).forEach(([field, visible]) => {
			const container = form.querySelector(`[data-duty-field="${field}"]`);
			const control = container.querySelector('select');
			container.hidden = !visible;
			control.required = visible;
			if (!visible) control.value = '';
		});

		[...otSelect.options].forEach((option) => {
			const isAllowed = section === '4th floor section'
				? restrictedOtNumbers.has(option.value)
				: section !== '2nd floor section' || !restrictedOtNumbers.has(option.value);
			option.hidden = !isAllowed;
			option.disabled = !isAllowed;
		});
		if (otSelect.selectedOptions[0]?.disabled) otSelect.value = '';

		[...departmentSelect.options].forEach((option) => {
			const isAllowed = section !== 'LR section' || option.value === '' || option.value === 'OBGY';
			option.hidden = !isAllowed;
			option.disabled = !isAllowed;
		});
		if (section === 'LR section') departmentSelect.value = 'OBGY';

		refreshChoices(otSelect);
		refreshChoices(departmentSelect);
	};

	sectionSelect.addEventListener('change', updateSectionFields);
	updateSectionFields();
});
