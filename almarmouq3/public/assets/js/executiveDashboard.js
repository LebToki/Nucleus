document.addEventListener('DOMContentLoaded', function () {
	if (typeof ApexCharts === 'undefined') {
		return;
	}

	const chartDefaults = {
		chart: {
			toolbar: {
				show: false
			},
			fontFamily: 'Inter, sans-serif'
		},
		dataLabels: {
			enabled: false
		},
		grid: {
			borderColor: '#D1D5DB',
			strokeDashArray: 3
		},
		tooltip: {
			theme: document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light'
		}
	};

	const salesChart = document.querySelector('#executiveSalesChart');
	if (salesChart) {
		new ApexCharts(salesChart, {
			... chartDefaults,
			series: [
				{
					name: 'Net sales',
					data: [
						285,
						312,
						298,
						344,
						376,
						402,
						480
					]
				}, {
					name: 'Gross profit',
					data: [
						194,
						212,
						203,
						235,
						258,
						274,
						329
					]
				},
			],
			colors: [
				'#487FFF', '#45B369'
			],
			chart: {
				... chartDefaults.chart,
				type: 'line',
				height: 280,
				zoom: {
					enabled: false
				}
			},
			stroke: {
				curve: 'smooth',
				width: 3
			},
			markers: {
				size: 0,
				hover: {
					size: 7
				}
			},
			xaxis: {
				categories: [
					'Jan',
					'Feb',
					'Mar',
					'Apr',
					'May',
					'Jun',
					'Jul'
				]
			},
			yaxis: {
				labels: {
					formatter: value => `AED ${value}k`
				}
			},
			legend: {
				position: 'top',
				horizontalAlign: 'right'
			}
		}).render();
	}

	const gradeChart = document.querySelector('#executiveGradeChart');
	if (gradeChart) {
		new ApexCharts(gradeChart, {
			series: [
				31, 29, 24, 16
			],
			labels: [
				'Al-Riyasi', 'Al-Nader', 'Al-Safwah', 'Al-Naqwah'
			],
			colors: [
				'#487FFF', '#45B369', '#FF9F29', '#EF4A00'
			],
			chart: {
				... chartDefaults.chart,
				type: 'donut',
				height: 260
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				width: 0
			},
			legend: {
				position: 'bottom'
			}
		}).render();
	}

	const paymentChart = document.querySelector('#executivePaymentChart');
	if (paymentChart) {
		new ApexCharts(paymentChart, {
			series: [
				72, 18, 10
			],
			labels: [
				'Paid', 'Pending', 'Overdue'
			],
			colors: [
				'#45B369', '#144BD6', '#FF9F29'
			],
			chart: {
				... chartDefaults.chart,
				type: 'donut',
				height: 220,
				sparkline: {
					enabled: true
				}
			},
			dataLabels: {
				enabled: false
			},
			stroke: {
				width: 0
			},
			legend: {
				show: false
			}
		}).render();
	}
});
