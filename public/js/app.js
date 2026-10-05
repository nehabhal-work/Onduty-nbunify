document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
	const input = document.getElementById(toggle.getAttribute('aria-controls'));

	if (!input) return;

	toggle.addEventListener('click', () => {
		const isVisible = input.type === 'text';
		const label = isVisible ? 'Show password' : 'Hide password';

		input.type = isVisible ? 'password' : 'text';
		toggle.setAttribute('aria-label', label);
		toggle.setAttribute('title', label);
		toggle.setAttribute('aria-pressed', String(!isVisible));
		toggle.textContent = isVisible ? 'Show' : 'Hide';
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

document.querySelectorAll('#duty-section').forEach((sectionSelect) => {
	const form = sectionSelect.closest('form');
	const otSelect = form.querySelector('[name="ot_no"]');
	const departmentSelect = form.querySelector('[name="department"]');
	const sectionRules = {
		'2nd floor section': { technician_name: true, ot_no: true, department: true, unit_no: true, surgery: true },
		'4th floor section': { technician_name: true, ot_no: true, department: true, unit_no: false, surgery: true },
		'LR section': { technician_name: true, ot_no: false, department: true, unit_no: true, surgery: true },
		'Recovery section': { technician_name: false, ot_no: false, department: false, unit_no: false, surgery: false },
		'Scope OT section': { technician_name: true, ot_no: false, department: true, unit_no: true, surgery: true },
	};
	const restrictedOtNumbers = new Set(['17', '18', '19', '20', '21']);

	const updateSectionFields = () => {
		const section = sectionSelect.value;
		const rules = sectionRules[section];

		Object.entries(rules).forEach(([field, visible]) => {
			const container = form.querySelector(`[data-duty-field="${field}"]`);
			const control = container.querySelector('select, input, textarea');
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

	};

	sectionSelect.addEventListener('change', updateSectionFields);
	updateSectionFields();
});
