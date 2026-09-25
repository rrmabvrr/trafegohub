import Chart from 'chart.js/auto';

window.Chart = Chart;

document.addEventListener('alpine:init', () => {
	Alpine.data('dashboardChart', (data) => ({
		chart: null,
		init() {
			this.chart = new Chart(this.$refs.canvas, {
				type: 'line',
				data: {
					labels: data.labels,
					datasets: [
						{ label: 'Investimento', data: data.spend, borderColor: '#00f2fe', backgroundColor: 'rgba(0,242,254,.12)', fill: true, tension: .35 },
						{ label: 'Receita', data: data.revenue, borderColor: '#34d399', backgroundColor: 'rgba(52,211,153,.08)', fill: true, tension: .35 },
					],
				},
				options: this.options(),
			});
			window.addEventListener('dashboard-chart-updated', (event) => {
				this.chart.data.labels = event.detail.labels;
				this.chart.data.datasets[0].data = event.detail.spend;
				this.chart.data.datasets[1].data = event.detail.revenue;
				this.chart.update();
			});
		},
		options() {
			return { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: '#94a3b8' } } }, scales: { x: { ticks: { color: '#64748b' }, grid: { color: 'rgba(148,163,184,.08)' } }, y: { ticks: { color: '#64748b' }, grid: { color: 'rgba(148,163,184,.08)' } } } };
		},
	}));

	Alpine.data('dashboardLeadsChart', (data) => ({
		chart: null,
		init() {
			this.chart = new Chart(this.$refs.canvas, {
				type: 'bar',
				data: { labels: data.labels, datasets: [{ label: 'Leads', data: data.leads, backgroundColor: '#a78bfa', borderRadius: 5 }] },
				options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: '#94a3b8' } } }, scales: { x: { ticks: { color: '#64748b' }, grid: { display: false } }, y: { beginAtZero: true, ticks: { color: '#64748b' }, grid: { color: 'rgba(148,163,184,.08)' } } } },
			});
			window.addEventListener('dashboard-chart-updated', (event) => {
				this.chart.data.labels = event.detail.labels;
				this.chart.data.datasets[0].data = event.detail.leads;
				this.chart.update();
			});
		},
	}));

	Alpine.data('dashboardPlatformChart', (data) => ({
		chart: null,
		init() {
			this.chart = new Chart(this.$refs.canvas, {
				type: 'doughnut',
				data: {
					labels: data.labels,
					datasets: [{
						label: 'Investimento',
						data: data.spend,
						backgroundColor: ['#00f2fe', '#a78bfa', '#34d399', '#fbbf24', '#f472b6', '#60a5fa'],
						borderWidth: 0,
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: { position: 'bottom', labels: { color: '#94a3b8', boxWidth: 12 } }
					}
				}
			});
		},
	}));
});
