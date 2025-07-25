@extends('layout.template.mainTemplate')

@section('title', 'Analitik Lanjutan - Admin Talent')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('container')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Page Heading with Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-chart-bar text-purple-600 mr-3"></i>
                Analitik Lanjutan
            </h1>
            <p class="text-gray-600">Analitik mendalam tentang keahlian, konversi, dan kinerja platform pencarian talent.</p>
            <div class="mt-2 text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                Terakhir diperbarui: <span id="last-updated">{{ now()->format('d M Y, H:i') }}</span>
                <span class="ml-3">
                    <i class="fas fa-keyboard mr-1"></i>
                    Shortcut: Ctrl+R (refresh), Ctrl+E (export)
                </span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2 mt-4 sm:mt-0 header-actions">
            <button onclick="refreshAnalytics()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                <i class="fas fa-sync-alt mr-2"></i>Perbarui Data
            </button>
            <button id="auto-refresh-toggle" onclick="toggleAutoRefresh()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors shadow-sm">
                <i class="fas fa-sync-alt mr-2"></i>Auto-refresh
            </button>
            <div class="relative">
                <button onclick="document.getElementById('export-menu').classList.toggle('hidden')" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors shadow-sm">
                    <i class="fas fa-download mr-2"></i>Ekspor
                </button>
                <div id="export-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-10">
                    <button onclick="exportAnalytics('pdf'); document.getElementById('export-menu').classList.add('hidden')" class="w-full text-left px-4 py-2 hover:bg-gray-50 rounded-t-lg">
                        <i class="fas fa-file-pdf text-red-600 mr-2"></i>Export PDF
                    </button>
                    <button onclick="exportAnalytics('excel'); document.getElementById('export-menu').classList.add('hidden')" class="w-full text-left px-4 py-2 hover:bg-gray-50 rounded-b-lg">
                        <i class="fas fa-file-excel text-green-600 mr-2"></i>Export Excel
                    </button>
                </div>
            </div>
            <a href="{{ route('talent_admin.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Conversion Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Conversion Funnel -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100 hover-lift">
                <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-t-2xl p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-funnel-dollar mr-3"></i>
                        Funnel Konversi
                    </h2>
                    <p class="text-green-100 text-sm mt-1">Analisis tahapan konversi pengguna menjadi talent</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @if(isset($skillAnalytics['conversion_funnel']['funnel_stages']))
                            @php $stages = $skillAnalytics['conversion_funnel']['funnel_stages']; @endphp
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                <span class="font-medium">👥 Total Pengguna</span>
                                <span class="text-2xl font-bold text-blue-600">{{ number_format($stages['total_users']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg">
                                <span class="font-medium">📚 Peserta Terdaftar</span>
                                <span class="text-2xl font-bold text-indigo-600">{{ number_format($stages['registered_trainees']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                                <span class="font-medium">✅ Penyelesaian Kursus</span>
                                <span class="text-2xl font-bold text-purple-600">{{ number_format($stages['course_completions']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-pink-50 rounded-lg">
                                <span class="font-medium">🎯 Perolehan Keahlian</span>
                                <span class="text-2xl font-bold text-pink-600">{{ number_format($stages['skill_acquisitions']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                                <span class="font-medium">💼 Daftar Talent</span>
                                <span class="text-2xl font-bold text-green-600">{{ number_format($stages['talent_opt_ins']) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg">
                                <span class="font-medium">🎉 Penempatan Berhasil</span>
                                <span class="text-2xl font-bold text-yellow-600">{{ number_format($stages['successful_placements']) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Conversion Readiness -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 hover-lift">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-2xl p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-chart-line mr-3"></i>
                        Kesiapan Konversi
                    </h2>
                    <p class="text-blue-100 text-sm mt-1">Tingkat kesiapan konversi talent</p>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="text-3xl font-bold text-blue-600">{{ $conversionAnalytics['conversion_ready'] }}</div>
                        <div class="text-gray-600">Siap Konversi</div>
                    </div>

                    <div class="space-y-3">
                        @if(isset($conversionAnalytics['readiness_distribution']))
                            <div class="flex justify-between items-center">
                                <span class="text-sm">🔥 Tinggi (80-100)</span>
                                <span class="font-bold text-red-600">{{ $conversionAnalytics['readiness_distribution']['high'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm">🟡 Sedang (60-79)</span>
                                <span class="font-bold text-yellow-600">{{ $conversionAnalytics['readiness_distribution']['medium'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm">🟢 Rendah (40-59)</span>
                                <span class="font-bold text-green-600">{{ $conversionAnalytics['readiness_distribution']['low'] }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm">⚪ Sangat Rendah (0-39)</span>
                                <span class="font-bold text-gray-600">{{ $conversionAnalytics['readiness_distribution']['very_low'] }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <div class="text-center">
                            <div class="text-lg font-bold text-blue-600">{{ $conversionAnalytics['average_readiness_score'] }}%</div>
                            <div class="text-xs text-blue-600">Skor Kesiapan Rata-rata</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skill Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Skill Categories Distribution -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 hover-lift">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 rounded-t-2xl p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-tags mr-3"></i>
                        Kategori Keahlian
                    </h2>
                    <p class="text-purple-100 text-sm mt-1">Distribusi kategori keahlian talent</p>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @if(isset($skillAnalytics['skill_categories']))
                            @foreach(array_slice($skillAnalytics['skill_categories'], 0, 8, true) as $category => $count)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-700">{{ $category }}</span>
                                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-bold">{{ $count }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Market Demand Analysis -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 hover-lift">
                <div class="bg-gradient-to-r from-orange-600 to-orange-700 rounded-t-2xl p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-chart-bar mr-3"></i>
                        Permintaan Pasar
                    </h2>
                    <p class="text-orange-100 text-sm mt-1">Analisis permintaan pasar keahlian</p>
                </div>
                <div class="p-6">
                    @if(isset($skillAnalytics['market_demand_analysis']['distribution']))
                        <div class="space-y-4">
                            @php $distribution = $skillAnalytics['market_demand_analysis']['distribution']; @endphp
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                                <span class="font-medium">🔥 Permintaan Sangat Tinggi</span>
                                <span class="text-red-600 font-bold">{{ $distribution['Very High'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                                <span class="font-medium">⚡ Permintaan Tinggi</span>
                                <span class="text-yellow-600 font-bold">{{ $distribution['High'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <span class="font-medium">📊 Permintaan Sedang</span>
                                <span class="text-blue-600 font-bold">{{ $distribution['Medium'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="font-medium">📉 Permintaan Rendah</span>
                                <span class="text-gray-600 font-bold">{{ $distribution['Low'] ?? 0 }}</span>
                            </div>
                        </div>

                        <!-- Top Demanded Skills -->
                        @if(isset($skillAnalytics['market_demand_analysis']['top_demanded_skills']))
                            <div class="mt-6">
                                <h3 class="font-bold text-gray-700 mb-3">🏆 Keahlian Paling Diminati</h3>
                                <div class="space-y-2">
                                    @foreach(array_slice($skillAnalytics['market_demand_analysis']['top_demanded_skills'], 0, 5, true) as $skill => $count)
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600">{{ $skill }}</span>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-bold">{{ $count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Conversion Candidates -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 hover-lift mb-8">
            <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 rounded-t-2xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-star mr-3"></i>
                            Kandidat Konversi Terbaik
                        </h2>
                        <p class="text-yellow-100 text-sm mt-1">Peserta dengan skor kesiapan konversi tinggi</p>
                    </div>
                    <span class="bg-white bg-opacity-20 text-black px-3 py-1 rounded-full text-sm font-medium">Siap Konversi</span>
                </div>
            </div>
            <div class="p-6">
                @if(isset($conversionAnalytics['top_conversion_candidates']) && count($conversionAnalytics['top_conversion_candidates']) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full analytics-table">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Pengguna</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Skor Kesiapan</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Keahlian</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Kursus</th>
                                    <th class="text-left py-3 px-4 font-semibold text-gray-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($conversionAnalytics['top_conversion_candidates'] as $candidate)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                                    {{ substr($candidate['user']['name'], 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $candidate['user']['name'] }}</div>
                                                    <div class="text-sm text-gray-600">{{ $candidate['user']['email'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-3 mr-3 progress-bar">
                                                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full" style="width: {{ $candidate['score'] }}%"></div>
                                                </div>
                                                <span class="font-bold text-green-600 metric-number" data-tooltip="Skor berdasarkan penyelesaian kursus, keahlian, dan aktivitas">{{ $candidate['score'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                                {{ $candidate['skills'] }} keahlian
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                                {{ $candidate['courses'] }} kursus
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <button onclick="suggestConversion({{ $candidate['user']['id'] }})"
                                                    class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors">
                                                <i class="fas fa-paper-plane mr-1"></i>
                                                Sarankan
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-user-clock text-4xl mb-4"></i>
                        <p>Tidak ada kandidat konversi yang siap saat ini.</p>
                        <p class="text-sm">Periksa kembali ketika pengguna menyelesaikan lebih banyak kursus dan memperoleh keahlian.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Learning to Earning Correlation -->
        @if(isset($skillAnalytics['learning_to_earning']))
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 hover-lift">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 rounded-t-2xl p-6">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-dollar-sign mr-3"></i>
                        Analisis Belajar ke Penghasilan
                    </h2>
                    <p class="text-indigo-100 text-sm mt-1">Korelasi antara pembelajaran dan potensi penghasilan</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- ROI Analysis -->
                        @if(isset($skillAnalytics['learning_to_earning']['roi_analysis']))
                            @php $roi = $skillAnalytics['learning_to_earning']['roi_analysis']; @endphp
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">Rp{{ number_format($roi['avg_investment_per_talent']) }}</div>
                                <div class="text-sm text-gray-600">Rata-rata Investasi per Talent</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">Rp{{ number_format($roi['avg_earning_potential']) }}</div>
                                <div class="text-sm text-gray-600">Rata-rata Potensi Penghasilan</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ $roi['roi_percentage'] }}%</div>
                                <div class="text-sm text-gray-600">Persentase ROI</div>
                            </div>
                        @endif
                    </div>

                    <!-- Hourly Rate by Category -->
                    @if(isset($skillAnalytics['learning_to_earning']['avg_hourly_rate_by_category']))
                        <div class="mt-6">
                            <h3 class="font-bold text-gray-700 mb-4">💰 Rata-rata Tarif Per Jam Berdasarkan Kategori Keahlian</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($skillAnalytics['learning_to_earning']['avg_hourly_rate_by_category'] as $category => $rate)
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <span class="font-medium text-gray-700">{{ $category }}</span>
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">Rp{{ $rate }}/jam</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Card hover effects and styling -->
<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.transition-all {
    transition: all 0.2s ease;
}

/* Enhanced dropdown styling */
#export-menu {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .analytics-table {
        font-size: 0.875rem;
    }

    .analytics-table th,
    .analytics-table td {
        padding: 0.5rem;
    }

    .header-actions {
        flex-direction: column;
        gap: 0.5rem;
    }

    .header-actions button,
    .header-actions a {
        width: 100%;
        text-align: center;
    }
}

/* Progress bar enhancements */
.progress-bar {
    position: relative;
    overflow: hidden;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.3) 50%, transparent 70%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Card number animations */
.metric-number {
    transition: all 0.3s ease;
}

.metric-number:hover {
    transform: scale(1.05);
}

/* Loading states */
.loading-pulse {
    animation: pulse 1.5s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Enhanced tooltips */
[data-tooltip] {
    position: relative;
    cursor: help;
}

[data-tooltip]:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    white-space: nowrap;
    z-index: 1000;
    opacity: 0;
    animation: tooltipFadeIn 0.3s ease-out forwards;
}

@keyframes tooltipFadeIn {
    from { opacity: 0; transform: translateX(-50%) translateY(5px); }
    to { opacity: 1; transform: translateX(-50%) translateY(0); }
}

/* Success state for sent suggestions */
.suggestion-sent {
    background: linear-gradient(135deg, #10b981, #059669);
    animation: successPulse 0.5s ease-out;
}

@keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>

<!-- JavaScript for interactions -->
<script>
function refreshAnalytics() {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memperbarui...';
    button.disabled = true;

    // Show progress notification
    Swal.fire({
        title: 'Memperbarui Data Analytics',
        text: 'Mengambil data terbaru...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Simulate refresh with progress (replace with actual API call when available)
    setTimeout(() => {
        updateLastUpdated();
        location.reload();
    }, 1500);
}

function suggestConversion(userId) {
    // Show confirmation dialog first
    Swal.fire({
        title: 'Kirim Saran Konversi?',
        text: 'Apakah Anda yakin ingin mengirim saran konversi talent kepada pengguna ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'Ya, Kirim Saran',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Mengirim Saran...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Simulate API call (replace with actual endpoint when available)
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Saran Berhasil Dikirim!',
                    text: `Notifikasi konversi talent telah dikirim ke pengguna`,
                    confirmButtonColor: '#16a34a'
                });

                // Update button state to show it was sent
                const button = document.querySelector(`button[onclick="suggestConversion(${userId})"]`);
                if (button) {
                    button.innerHTML = '<i class="fas fa-check mr-1"></i>Terkirim';
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-gray-500', 'cursor-not-allowed');
                    button.disabled = true;
                }
            }, 1500);

            // Uncomment and modify this when actual API endpoint is ready:
            /*
            fetch(`/talent-admin/suggest-conversion/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Saran Berhasil Dikirim!',
                    text: `Notifikasi konversi talent telah dikirim ke ${data.user.name}`,
                    confirmButtonColor: '#16a34a'
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim Saran',
                    text: 'Terjadi kesalahan saat mengirim saran konversi. Silakan coba lagi.',
                    confirmButtonColor: '#dc2626'
                });
            });
            */
        }
    });
}

// Initialize any charts or interactive elements
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard analitik dimuat');

    // Add auto-refresh option (optional - can be enabled by admin)
    let autoRefreshEnabled = false;
    let autoRefreshInterval;

    // Auto-refresh toggle function
    window.toggleAutoRefresh = function() {
        autoRefreshEnabled = !autoRefreshEnabled;
        const toggleBtn = document.getElementById('auto-refresh-toggle');

        if (autoRefreshEnabled) {
            // Start auto-refresh every 5 minutes
            autoRefreshInterval = setInterval(() => {
                console.log('Auto-refreshing analytics...');
                refreshAnalytics();
            }, 300000); // 5 minutes

            if (toggleBtn) {
                toggleBtn.innerHTML = '<i class="fas fa-stop mr-2"></i>Stop Auto-refresh';
                toggleBtn.classList.remove('bg-gray-600');
                toggleBtn.classList.add('bg-red-600', 'hover:bg-red-700');
            }

            // Show notification
            Swal.fire({
                icon: 'success',
                title: 'Auto-refresh Aktif',
                text: 'Data akan diperbarui otomatis setiap 5 menit',
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            // Stop auto-refresh
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }

            if (toggleBtn) {
                toggleBtn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Auto-refresh';
                toggleBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                toggleBtn.classList.add('bg-gray-600');
            }

            // Show notification
            Swal.fire({
                icon: 'info',
                title: 'Auto-refresh Dinonaktifkan',
                text: 'Data tidak akan diperbarui otomatis',
                timer: 3000,
                showConfirmButton: false
            });
        }
    };

    // Export analytics function
    window.exportAnalytics = function(format) {
        Swal.fire({
            title: 'Ekspor Data Analytics',
            text: `Menyiapkan file ${format.toUpperCase()}...`,
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Simulate export (replace with actual endpoint when available)
        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: 'Ekspor Berhasil!',
                text: `File ${format.toUpperCase()} telah diunduh`,
                confirmButtonColor: '#16a34a'
            });

            // In real implementation, trigger file download
            // window.open(`/talent-admin/analytics/export?format=${format}`, '_blank');
        }, 2000);
    };

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + R for refresh
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            refreshAnalytics();
        }

        // Ctrl/Cmd + E for export
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            exportAnalytics('pdf');
        }
    });

    // Add tooltips for better UX
    const addTooltips = () => {
        // Add tooltips to conversion scores
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            element.addEventListener('mouseenter', function() {
                const tooltip = this.getAttribute('data-tooltip');
                // Implementation would use a tooltip library
                console.log('Tooltip:', tooltip);
            });
        });
    };

    addTooltips();

    // Close export menu when clicking outside
    document.addEventListener('click', function(event) {
        const exportMenu = document.getElementById('export-menu');
        const exportButton = event.target.closest('button[onclick*="export-menu"]');

        if (!exportButton && !exportMenu.contains(event.target)) {
            exportMenu.classList.add('hidden');
        }
    });

    // Update last updated time
    window.updateLastUpdated = function() {
        const now = new Date();
        const formatted = now.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        document.getElementById('last-updated').textContent = formatted;
    };
});
</script>
@endsection
