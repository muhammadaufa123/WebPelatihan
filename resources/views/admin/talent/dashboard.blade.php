@extends('layout.template.mainTemplate')

@section('title', 'Talent Dashboard')
@section('container')

{{-- Include Talent Request Notifications --}}
@include('components.talent-request-notifications')

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Welcome back, {{ $user->name }}! 👋</h1>
                    <p class="text-blue-100 text-lg">Ready to explore new opportunities and showcase your talent?</p>
                </div>
                <div class="hidden md:block">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl p-4 text-center">
                        <i class="fas fa-star text-4xl text-yellow-300 mb-2"></i>
                        <div class="text-sm font-medium">Talent Status</div>
                        <div class="text-xs opacity-90">
                            {{ $user->is_active_talent ? 'Active' : 'Inactive' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Profile Completeness --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-user-circle text-blue-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">{{ $profileCompleteness }}%</div>
                        <div class="text-sm text-gray-500">Complete</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700 mb-2">Profile Status</div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $profileCompleteness }}%"></div>
                </div>
            </div>

            {{-- Active Opportunities --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-briefcase text-green-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">{{ $jobOpportunities->count() }}</div>
                        <div class="text-sm text-gray-500">Available</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700">Job Opportunities</div>
                <div class="text-xs text-green-600 font-medium">
                    @if($jobOpportunities->where('created_at', '>=', now()->subDays(7))->count() > 0)
                        +{{ $jobOpportunities->where('created_at', '>=', now()->subDays(7))->count() }} new this week
                    @else
                        Check back for new opportunities
                    @endif
                </div>
            </div>

            {{-- Applications Sent --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-paper-plane text-purple-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">{{ $talentStats['total_applications'] }}</div>
                        <div class="text-sm text-gray-500">Sent</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700">Applications</div>
                <div class="text-xs text-purple-600 font-medium">{{ $talentStats['pending_applications'] }} pending review</div>
            </div>

            {{-- Completed Collaborations --}}
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <i class="fas fa-trophy text-orange-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800">{{ $talentStats['completed_collaborations'] }}</div>
                        <div class="text-sm text-gray-500">Completed</div>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700">Collaborations</div>
                <div class="text-xs text-orange-600 font-medium">{{ $talentStats['approved_applications'] }} projects delivered successfully</div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Latest Opportunities --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-800">🚀 Latest Opportunities</h2>
                            <a href="{{ route('talent.my_requests') }}" data-testid="view-all-link" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View All</a>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($jobOpportunities->take(3) as $opportunity)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all
                                @if(isset($opportunity['is_pre_approved']) && $opportunity['is_pre_approved'])
                                    ring-2 ring-emerald-200 bg-gradient-to-br from-emerald-50 to-white border-emerald-300
                                @endif">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h3 class="font-semibold text-gray-800">{{ $opportunity['title'] }}</h3>

                                            {{-- Pre-approved Badge --}}
                                            @if(isset($opportunity['is_pre_approved']) && $opportunity['is_pre_approved'])
                                                <span class="px-2 py-1 bg-emerald-500 text-white text-xs rounded-full font-bold">
                                                    <i class="fas fa-star mr-1"></i>PRE-APPROVED
                                                </span>
                                            @elseif($opportunity['posted_date']->diffInDays() <= 3)
                                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">New</span>
                                            @elseif($opportunity['urgency'] === 'high')
                                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">Urgent</span>
                                            @else
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">Available</span>
                                            @endif
                                        </div>
                                        <p class="text-gray-600 text-sm mb-2">{{ $opportunity['company'] }} • Remote</p>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                            <span class="flex items-center"><i class="fas fa-dollar-sign mr-1"></i> {{ $opportunity['budget'] }}</span>
                                            <span class="flex items-center"><i class="fas fa-clock mr-1"></i> {{ $opportunity['duration'] }}</span>
                                            <span class="flex items-center"><i class="fas fa-calendar mr-1"></i> {{ $opportunity['posted_date']->diffForHumans() }}</span>
                                        </div>

                                        {{-- Acceptance Status --}}
                                        @if(isset($opportunity['acceptance_status']))
                                            <div class="mb-3">
                                                <div class="text-xs font-medium text-gray-600 mb-1">Status:</div>
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    @if($opportunity['both_parties_accepted']) bg-green-100 text-green-800
                                                    @elseif($opportunity['talent_accepted']) bg-blue-100 text-blue-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ $opportunity['acceptance_status'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex flex-col space-y-2 ml-4">
                                        @if(isset($opportunity['can_accept']) && $opportunity['can_accept'])
                                            <button onclick="acceptRequest({{ $opportunity['request_id'] }})"
                                                    class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                                <i class="fas fa-check mr-1"></i> Accept
                                            </button>
                                            <button onclick="rejectRequest({{ $opportunity['request_id'] }})"
                                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                                <i class="fas fa-times mr-1"></i> Decline
                                            </button>
                                        @elseif(isset($opportunity['can_reject']) && $opportunity['can_reject'])
                                            <button onclick="rejectRequest({{ $opportunity['request_id'] }})"
                                                    class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                                <i class="fas fa-times mr-1"></i> Decline
                                            </button>
                                        @else
                                            <button class="px-4 py-2 bg-gray-400 text-white text-sm rounded-lg cursor-not-allowed" disabled>
                                                {{ isset($opportunity['both_parties_accepted']) && $opportunity['both_parties_accepted'] ? 'Accepted' : 'Pending' }}
                                            </button>
                                        @endif

                                        <button onclick="viewRequestDetails({{ $opportunity['request_id'] }})"
                                                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-eye mr-1"></i> Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-briefcase text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">No job opportunities available at the moment.</p>
                                <p class="text-gray-400 text-sm">Check back later for new opportunities!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800">📋 Recent Activity</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($recentActivity as $activity)
                            <div class="flex items-center space-x-3">
                                <div class="bg-{{ $activity['color'] }}-100 p-2 rounded-full">
                                    <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $activity['title'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-history text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">No recent activity yet.</p>
                                <p class="text-gray-400 text-sm">Start applying to jobs or completing courses!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Job History (Accepted Collaborations) --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-gray-800">🏆 Collaboration History</h2>
                            <span class="text-sm text-gray-500">{{ $jobHistory->count() }} total</span>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($jobHistory as $job)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition-all">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h3 class="font-semibold text-gray-800">{{ $job['project_title'] }}</h3>
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($job['status_color'] === 'green') bg-green-100 text-green-800
                                                @elseif($job['status_color'] === 'blue') bg-blue-100 text-blue-800
                                                @elseif($job['status_color'] === 'yellow') bg-yellow-100 text-yellow-800
                                                @elseif($job['status_color'] === 'purple') bg-purple-100 text-purple-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $job['formatted_status'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-2 mb-2">
                                            <p class="text-gray-600 text-sm">{{ $job['company'] }}</p>
                                            @if($job['company_role'])
                                                <span class="text-gray-400">•</span>
                                                <p class="text-gray-500 text-sm">{{ $job['company_role'] }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 mb-2">
                                            <span class="flex items-center"><i class="fas fa-dollar-sign mr-1"></i> {{ $job['budget_range'] }}</span>
                                            <span class="flex items-center"><i class="fas fa-clock mr-1"></i> {{ $job['duration_worked'] }}</span>
                                            @if($job['talent_accepted_at'])
                                                <span class="flex items-center"><i class="fas fa-calendar-check mr-1"></i> Started {{ \Carbon\Carbon::parse($job['talent_accepted_at'])->format('M d, Y') }}</span>
                                            @endif
                                        </div>
                                        @if($job['project_description'])
                                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit($job['project_description'], 120) }}</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col items-end space-y-2 ml-4">
                                        @if($job['is_completed'])
                                            <div class="flex items-center text-green-600 text-sm">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                <span>Completed</span>
                                            </div>
                                        @elseif($job['is_in_progress'])
                                            <div class="flex items-center text-blue-600 text-sm">
                                                <i class="fas fa-spinner fa-pulse mr-1"></i>
                                                <span>In Progress</span>
                                            </div>
                                        @endif
                                        <button onclick="viewJobDetails({{ $job['id'] }})"
                                                class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                                            <i class="fas fa-eye mr-1"></i> View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-briefcase text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">No collaboration history yet.</p>
                                <p class="text-gray-400 text-sm">Accept job opportunities to start building your portfolio!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Profile Quick Actions --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800">⚡ Quick Actions</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('profile.edit') }}" class="flex items-center p-3 rounded-lg hover:bg-blue-50 transition-colors group">
                            <div class="bg-blue-100 p-2 rounded-lg group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-user-edit text-blue-600"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-800">Edit Profile</div>
                                <div class="text-xs text-gray-500">Update your information</div>
                            </div>
                        </a>
                        <a href="#" onclick="document.getElementById('resumeUpload').click()" class="flex items-center p-3 rounded-lg hover:bg-green-50 transition-colors group">
                            <div class="bg-green-100 p-2 rounded-lg group-hover:bg-green-200 transition-colors">
                                <i class="fas fa-upload text-green-600"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-800">Upload Resume</div>
                                <div class="text-xs text-gray-500">Keep it updated</div>
                            </div>
                        </a>
                        <a href="#" onclick="showAlert('Skill Assessment feature coming soon!', 'info')" class="flex items-center p-3 rounded-lg hover:bg-purple-50 transition-colors group">
                            <div class="bg-purple-100 p-2 rounded-lg group-hover:bg-purple-200 transition-colors">
                                <i class="fas fa-cogs text-purple-600"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-800">Skill Assessment</div>
                                <div class="text-xs text-gray-500">Test your abilities</div>
                            </div>
                        </a>
                        <!-- Hidden file input for resume upload -->
                        <input type="file" id="resumeUpload" accept=".pdf,.doc,.docx" style="display: none;" onchange="handleResumeUpload(this)">
                    </div>
                </div>

                {{-- Skill Progress --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-800">🎯 Skill Progress</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($userSkills as $skill)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">{{ $skill['name'] }}</span>
                                    <span class="text-sm text-gray-500">{{ $skill['percentage'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $colorClass = $skill['percentage'] >= 80 ? 'bg-green-600' :
                                                     ($skill['percentage'] >= 60 ? 'bg-blue-600' : 'bg-purple-600');
                                    @endphp
                                    <div class="{{ $colorClass }} h-2 rounded-full" style="width: {{ $skill['percentage'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-cogs text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">No skills tracked yet.</p>
                                <p class="text-gray-400 text-sm">Complete courses to build your skill profile!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Messages --}}
                <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-gray-800">💬 Messages</h2>
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $talentStats['new_messages'] }}</span>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($recentRequests->take(2) as $request)
                            <div class="flex items-center space-x-3">
                                <img src="/asset/icons/profile-women.svg" alt="Recruiter" class="w-8 h-8 rounded-full">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-800">{{ $request->recruiter->user->name ?? 'Recruiter' }}</div>
                                    <div class="text-xs text-gray-500">{{ Str::limit($request->project_title ?? 'New opportunity', 30) }}</div>
                                </div>
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-envelope-open text-gray-300 text-2xl mb-2"></i>
                                <p class="text-gray-500 text-sm">No messages yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Request Details Modal --}}
<div id="talentRequestDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeRequestModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-white" id="modal-title">Request Details</h3>
                    <button type="button" class="text-white hover:text-gray-200 transition-colors" onclick="closeRequestModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div id="modalContent" class="px-6 py-6">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Loading request details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for modal management (simplified - no auto-close)
let isModalOpen = false;
let modalInitialized = false;
let processingAction = false; // Flag to prevent modal close during action processing

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing modal system');
    // Reset states
    isModalOpen = false;
    modalInitialized = false;
    processingAction = false;

    initializeModal();

    // Setup escape key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isModalOpen && !processingAction) {
            closeRequestModal();
        }
    });
});

// Initialize modal and set up event listeners
// Initialize modal and set up event listeners (simplified - no auto-close)
function initializeModal() {
    if (modalInitialized && document.getElementById('talentRequestDetailsModal')) {
        console.log('Modal already initialized and element exists.');
        return;
    }

    const modal = document.getElementById('talentRequestDetailsModal');
    const modalContent = document.getElementById('modalContent');

    console.log('Attempting modal initialization. Found modal:', !!modal, 'Found modalContent:', !!modalContent);

    if (!modal || !modalContent) {
        console.error('Modal elements not found during initialization.');
        modalInitialized = false;
        return;
    }

    modalInitialized = true;
    console.log('Modal initialized successfully.');

    // Click outside to close
    modal.addEventListener('click', function(e) {
        if (e.target === modal && !processingAction) {
            closeRequestModal();
        }
    });

    // Prevent closing when clicking inside modal content
    const modalPanel = modal.querySelector('.bg-white.rounded-2xl');
    if (modalPanel) {
        modalPanel.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

// Handle modal state changes (simplified - no auto-reload)
function handleModalStateChange(modalIsOpen) {
    if (modalIsOpen) {
        console.log('Modal is now OPEN.');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('modal-is-active');
    } else {
        console.log('Modal is now CLOSED.');
        document.body.style.overflow = '';
        document.body.classList.remove('modal-is-active');

        if (processingAction) {
            console.log('Resetting processingAction because modal closed.');
            processingAction = false;
        }
    }
}

// Modified accept request function
function acceptRequest(requestId) {
    if (processingAction) {
        console.log('Already processing an action, ignoring...');
        return;
    }

    if (!confirm('Are you sure you want to accept this collaboration request?')) {
        return;
    }

    processingAction = true;
    console.log('Starting accept request process for ID:', requestId);

    // Show loading state in modal
    const modalContent = document.getElementById('modalContent');
    const originalContent = modalContent ? modalContent.innerHTML : '';

    if (modalContent) {
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-green-600 mb-4"></i>
                <p class="text-gray-600">Processing your acceptance...</p>
                <p class="text-gray-500 text-sm mt-2">Please wait while we update the request status.</p>
            </div>
        `;
    }

    const notes = prompt('Optional: Add a note about your acceptance:') || '';

    fetch(`/talent/request/${requestId}/accept`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            acceptance_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Accept request response:', data);
        processingAction = false;

        if (data.success) {
            showAlert('Request accepted successfully! ' + data.message, 'success');

            // Simple reload after success
            setTimeout(() => {
                console.log('Reloading page after successful acceptance...');
                location.reload();
            }, 2000);
        } else {
            showAlert('Error: ' + (data.message || 'Failed to accept request'), 'error');
            // Restore original modal content on error
            if (modalContent && originalContent) {
                modalContent.innerHTML = originalContent;
            }
        }
    })
    .catch(error => {
        console.error('Error accepting request:', error);
        processingAction = false;
        showAlert('Network error occurred. Please try again.', 'error');
        // Restore original modal content on error
        if (modalContent && originalContent) {
            modalContent.innerHTML = originalContent;
        }
    });
}

// Modified reject request function
function rejectRequest(requestId) {
    if (processingAction) {
        console.log('Already processing an action, ignoring...');
        return;
    }

    if (!confirm('Are you sure you want to decline this collaboration request?')) {
        return;
    }

    processingAction = true;
    console.log('Starting reject request process for ID:', requestId);

    // Show loading state in modal
    const modalContent = document.getElementById('modalContent');
    const originalContent = modalContent ? modalContent.innerHTML : '';

    if (modalContent) {
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-red-600 mb-4"></i>
                <p class="text-gray-600">Processing your decline...</p>
                <p class="text-gray-500 text-sm mt-2">Please wait while we update the request status.</p>
            </div>
        `;
    }

    const notes = prompt('Please provide a reason for declining (optional):') || '';

    fetch(`/talent/request/${requestId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            rejection_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Reject request response:', data);
        processingAction = false;

        if (data.success) {
            showAlert('Request declined successfully.', 'success');

            // Simple reload after success
            setTimeout(() => {
                console.log('Reloading page after successful rejection...');
                location.reload();
            }, 2000);
        } else {
            showAlert('Error: ' + (data.message || 'Failed to decline request'), 'error');
            // Restore original modal content on error
            if (modalContent && originalContent) {
                modalContent.innerHTML = originalContent;
            }
        }
    })
    .catch(error => {
        console.error('Error rejecting request:', error);
        processingAction = false;
        showAlert('Network error occurred. Please try again.', 'error');
        // Restore original modal content on error
        if (modalContent && originalContent) {
            modalContent.innerHTML = originalContent;
        }
    });
}

// Simplified modal opening function (no auto-close timeouts)
function openModal() {
    console.log('Attempting to open modal. modalInitialized:', modalInitialized);

    let modal = document.getElementById('talentRequestDetailsModal');

    if (!modal) {
        console.error('Modal element not found in the DOM.');
        if (modalInitialized) {
            console.warn('Modal was marked initialized, but element is missing. Forcing re-init.');
            modalInitialized = false;
        }
    }

    if (!modalInitialized) {
        console.log('Modal not initialized. Attempting to initialize.');
        initializeModal();

        if (!modalInitialized) {
            console.error('Modal initialization failed. Cannot open modal.');
            showAlert('Error: Modal system not ready. Please refresh the page and try again.', 'error');
            return false;
        }

        modal = document.getElementById('talentRequestDetailsModal');
        if (!modal) {
            console.error('Modal initialized, but element still not found.');
            showAlert('Error: Modal component issue. Please refresh.', 'error');
            return false;
        }
    }

    processingAction = false; // Reset processing flag
    modal.classList.remove('hidden');
    isModalOpen = true;
    handleModalStateChange(true);

    console.log('Modal opened successfully.');
    return true;
}

// View Job Details Function (for history)
function viewJobDetails(jobId) {
    if (!openModal()) return;

    const modalContent = document.getElementById('modalContent');

    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Loading collaboration details...</p>
        </div>
    `;

    fetch(`/talent/api/my-requests`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.requests) {
            const job = data.requests.find(r => r.id == jobId);

            if (job) {
                modalContent.innerHTML = `
                    <div class="space-y-6">
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-4 rounded-xl border border-blue-200">
                            <h4 class="font-bold text-lg text-gray-900 mb-3">📋 ${job.project_title || 'Project Details'}</h4>
                            <p class="text-gray-700">${job.project_description || 'No description available'}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-green-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-green-900 mb-3">🏢 Collaboration Details</h4>
                                <div class="space-y-2">
                                    <div><span class="font-medium">Company:</span> ${job.recruiter_name || 'Unknown'}</div>
                                    <div><span class="font-medium">Role:</span> ${job.recruiter_company || 'Not specified'}</div>
                                    <div><span class="font-medium">Budget:</span> ${job.budget_range || 'Budget TBD'}</div>
                                    <div><span class="font-medium">Duration:</span> ${job.project_duration || 'Duration TBD'}</div>
                                    <div><span class="font-medium">Priority:</span> <span class="capitalize">${job.urgency_level || 'Medium'}</span></div>
                                </div>
                            </div>
                            <div class="bg-blue-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-blue-900 mb-3">📊 Progress & Timeline</h4>
                                <div class="space-y-2">
                                    <div><span class="font-medium">Status:</span>
                                        <span class="px-2 py-1 text-xs rounded-full ${getStatusBadgeClasses(job.status_badge_color)}">
                                            <i class="${job.status_icon} mr-1"></i>
                                            ${job.formatted_status}
                                        </span>
                                    </div>
                                    <div><span class="font-medium">Progress:</span> ${job.workflow_progress || 0}%</div>
                                    <div><span class="font-medium">Started:</span> ${job.created_at}</div>
                                    ${job.both_parties_accepted ? '<div><span class="font-medium">Completed:</span> <span class="text-green-600">✓ Finished</span></div>' : ''}
                                </div>
                                <div class="mt-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: ${job.workflow_progress || 0}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        ${job.both_parties_accepted ? `
                            <div class="bg-green-50 p-4 rounded-xl border border-green-200">
                                <div class="flex items-center justify-center space-x-2 text-green-700">
                                    <i class="fas fa-trophy text-2xl"></i>
                                    <div>
                                        <h4 class="font-bold text-lg">Collaboration Completed Successfully!</h4>
                                        <p class="text-sm">This project has been successfully completed and both parties are satisfied.</p>
                                    </div>
                                </div>
                            </div>
                        ` : `
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                                <div class="flex items-center justify-center space-x-2 text-blue-700">
                                    <i class="fas fa-clock text-xl"></i>
                                    <div>
                                        <h4 class="font-semibold">Collaboration In Progress</h4>
                                        <p class="text-sm">This project is currently active. Keep up the great work!</p>
                                    </div>
                                </div>
                            </div>
                        `}
                    </div>
                `;
            } else {
                modalContent.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-600 mb-4"></i>
                        <p class="text-gray-600">Collaboration details not found.</p>
                    </div>
                `;
            }
        } else {
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-4xl text-red-600 mb-4"></i>
                    <p class="text-gray-600">Error loading collaboration details.</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-wifi text-4xl text-red-600 mb-4"></i>
                <p class="text-gray-600">Network error occurred.</p>
                <button onclick="viewJobDetails(${jobId})" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-refresh mr-2"></i>Retry
                </button>
            </div>
        `;
    });
}

// View Request Details Function
function viewRequestDetails(requestId) {
    if (!openModal()) return;

    const modalContent = document.getElementById('modalContent');

    // Show loading state
    modalContent.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
            <p class="text-gray-600">Loading request details...</p>
        </div>
    `;

    console.log('Fetching details for request ID:', requestId);

    // Add timeout to prevent infinite loading
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout

    fetch(`/talent/api/my-requests`, {
        signal: controller.signal,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        clearTimeout(timeoutId);
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('API Response:', data);

        if (data.success && data.requests && Array.isArray(data.requests)) {
            const request = data.requests.find(r => r.id == requestId);
            console.log('Found request:', request);

            if (request) {
                modalContent.innerHTML = `
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-blue-900 mb-3">📋 Project Information</h4>
                                <div class="space-y-2">
                                    <div><span class="font-medium">Title:</span> ${request.project_title || 'Not specified'}</div>
                                    <div><span class="font-medium">Description:</span> <div class="text-sm text-gray-700 mt-1 max-h-20 overflow-y-auto">${request.project_description || 'No description provided'}</div></div>
                                    <div><span class="font-medium">Budget:</span> ${request.budget_range || 'Budget TBD'}</div>
                                    <div><span class="font-medium">Duration:</span> ${request.project_duration || 'Duration TBD'}</div>
                                    <div><span class="font-medium">Urgency:</span> <span class="capitalize">${request.urgency_level || 'Medium'}</span></div>
                                </div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-xl">
                                <h4 class="font-semibold text-green-900 mb-3">🏢 Recruiter Information</h4>
                                <div class="space-y-2">
                                    <div><span class="font-medium">Name:</span> ${request.recruiter_name || 'Unknown'}</div>
                                    <div><span class="font-medium">Company:</span> ${request.recruiter_company || 'Not specified'}</div>
                                    <div><span class="font-medium">Status:</span> <span class="capitalize">${request.formatted_status || request.status}</span></div>
                                    <div><span class="font-medium">Submitted:</span> ${request.created_at}</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl">
                            <h4 class="font-semibold text-gray-900 mb-3">📊 Status & Progress</h4>
                            <div class="space-y-3">
                                <div><span class="font-medium">Current Status:</span>
                                    <span class="px-2 py-1 text-xs rounded-full ${getStatusBadgeClasses(request.status_badge_color)}">
                                        <i class="${request.status_icon || 'fas fa-clock'} mr-1"></i>
                                        ${request.formatted_status}
                                    </span>
                                </div>
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-medium">Progress:</span>
                                        <span class="text-sm text-gray-600">${request.workflow_progress || 0}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-3">
                                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: ${request.workflow_progress || 0}%"></div>
                                    </div>
                                </div>
                                ${request.acceptance_status ? `
                                    <div><span class="font-medium">Acceptance Status:</span> 
                                        <span class="text-sm text-gray-700">${request.acceptance_status}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>

                        <div class="flex space-x-4">
                            ${request.can_accept ? `
                                <button onclick="acceptRequest(${request.id})"
                                        class="flex-1 px-6 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-semibold">
                                    <i class="fas fa-check mr-2"></i>Accept Request
                                </button>
                            ` : ''}
                            ${request.can_reject ? `
                                <button onclick="rejectRequest(${request.id})"
                                        class="flex-1 px-6 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-semibold">
                                    <i class="fas fa-times mr-2"></i>Decline Request
                                </button>
                            ` : ''}
                            ${!request.can_accept && !request.can_reject ? `
                                <div class="flex-1 text-center py-3 bg-gray-100 text-gray-600 rounded-xl">
                                    ${request.both_parties_accepted ? 
                                        '<i class="fas fa-check-circle text-green-600 mr-2"></i>Request accepted by both parties' : 
                                        '<i class="fas fa-clock text-gray-500 mr-2"></i>No actions available'}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            } else {
                modalContent.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-600 mb-4"></i>
                        <p class="text-gray-600">Request with ID ${requestId} not found.</p>
                        <p class="text-gray-500 text-sm mt-2">The request may have been deleted or you don't have access to it.</p>
                    </div>
                `;
            }
        } else {
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-4xl text-red-600 mb-4"></i>
                    <p class="text-gray-600">Error loading request details.</p>
                    <p class="text-gray-500 text-sm mt-2">${data.message || 'Invalid response format'}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        console.error('Error fetching request details:', error);
        
        let errorMessage = 'Network error occurred.';
        let errorDetail = 'Please check your internet connection and try again.';
        
        if (error.name === 'AbortError') {
            errorMessage = 'Request timed out.';
            errorDetail = 'The server took too long to respond. Please try again.';
        } else if (error.message.includes('HTTP')) {
            errorMessage = 'Server error occurred.';
            errorDetail = error.message;
        }
        
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-wifi text-4xl text-red-600 mb-4"></i>
                <p class="text-gray-600">${errorMessage}</p>
                <p class="text-gray-500 text-sm mt-2">${errorDetail}</p>
                <button onclick="viewRequestDetails(${requestId})" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-refresh mr-2"></i>Retry
                </button>
            </div>
        `;
    });
}

// Close Request Modal (simplified)
function closeRequestModal() {
    const modal = document.getElementById('talentRequestDetailsModal');
    if (modal) {
        // Don't close if we're processing an action
        if (processingAction) {
            console.log('Cannot close modal while processing action');
            return;
        }

        if (modal.classList.contains('hidden')) {
            console.log('Modal already hidden.');
            if (isModalOpen) {
                isModalOpen = false;
                handleModalStateChange(false);
            }
            return;
        }

        modal.classList.add('hidden');
        isModalOpen = false;
        handleModalStateChange(false);
        console.log('Modal closed successfully.');
    } else {
        console.warn('Modal element not found during close.');
        if (isModalOpen) {
            isModalOpen = false;
            handleModalStateChange(false);
        }
    }
}

// Helper function to process status badge classes
function getStatusBadgeClasses(statusBadgeColor) {
    // If statusBadgeColor is already a complete class string, return it
    if (typeof statusBadgeColor === 'string' && statusBadgeColor.includes('bg-')) {
        return statusBadgeColor;
    }
    
    // Map status types to Tailwind classes
    const colorMapping = {
        'success': 'bg-green-100 text-green-800',
        'warning': 'bg-yellow-100 text-yellow-800',
        'info': 'bg-blue-100 text-blue-800',
        'primary': 'bg-indigo-100 text-indigo-800',
        'danger': 'bg-red-100 text-red-800',
        'secondary': 'bg-gray-100 text-gray-800'
    };
    
    return colorMapping[statusBadgeColor] || 'bg-gray-100 text-gray-800';
}

// Show Alert Function
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
        type === 'info' ? 'bg-blue-100 border border-blue-400 text-blue-700' :
        'bg-red-100 border border-red-400 text-red-700'
    }`;
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'info' ? 'info-circle' : 'exclamation-circle'} mr-2"></i>
            ${message}
        </div>
    `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.transform = 'translateX(100%)';
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// Handle resume upload
function handleResumeUpload(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (file.size > maxSize) {
            showAlert('File size must be less than 5MB', 'error');
            return;
        }

        // Here you would typically upload the file to your server
        showAlert('Resume upload feature will be implemented soon!', 'info');
    }
}
</script>
@endsection
