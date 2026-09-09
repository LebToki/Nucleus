(function () {
	const rows = document.querySelectorAll('[data-row]');
	const money = (value) => `AED ${
		value.toLocaleString('en-AE', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2
		})
	}`;

	const update = () => {
		let weight = 0;
		let subtotal = 0;

		rows.forEach((row) => {
			const quantity = parseFloat(row.querySelector('.quote-qty').value) || 0;
			const rate = parseFloat(row.querySelector('.quote-rate').value) || 0;
			const line = quantity * rate;

			weight += quantity;
			subtotal += line;
			row.querySelector('.quote-line-total').textContent = money(line);
		});

		const vat = subtotal * 0.05;
		document.querySelector('#quote-total-weight').textContent = `${
			weight.toFixed(4)
		} KG`;
		document.querySelector('#quote-subtotal').textContent = money(subtotal);
		document.querySelector('#quote-vat').textContent = money(vat);
		document.querySelector('#quote-total').textContent = money(subtotal + vat);
	};

	document.querySelectorAll('.quote-qty, .quote-rate').forEach((input) => input.addEventListener('input', update));
	update();
})();
