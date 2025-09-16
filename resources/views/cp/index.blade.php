@extends('statamic::layout')

@section('title', 'Preview Links')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Preview Links</h1>
        <button 
            onclick="showGenerateModal()" 
            class="btn-primary"
        >
            Generate Preview Link
        </button>
    </div>

    @if($previewLinks->isEmpty())
        <div class="card p-8 text-center">
            <div class="text-gray-500 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold mb-2">No Active Preview Links</h2>
            <p class="text-gray-600 mb-4">Create shareable preview links for your draft content.</p>
            <button onclick="showGenerateModal()" class="btn-primary">
                Generate Your First Preview Link
            </button>
        </div>
    @else
        <div class="card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Entry</th>
                        <th>Collection</th>
                        <th>Preview URL</th>
                        <th>Expires</th>
                        <th>Access Count</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($previewLinks as $link)
                        <tr>
                            <td>
                                <div class="font-medium">{{ $link->entry_title ?: $link->entry_slug }}</div>
                                <div class="text-xs text-gray-500">{{ $link->entry_slug }}</div>
                            </td>
                            <td>
                                <span class="badge">{{ $link->collection }}</span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <input 
                                        type="text" 
                                        value="{{ $link->getPreviewUrl() }}" 
                                        readonly 
                                        class="text-xs w-64 p-1 border rounded"
                                        id="url-{{ $link->id }}"
                                    >
                                    <button 
                                        onclick="copyToClipboard('url-{{ $link->id }}')"
                                        class="btn-sm"
                                        title="Copy URL"
                                    >
                                        Copy
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm">{{ $link->expires_at->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-500">
                                    @if($link->getRemainingDays() > 0)
                                        {{ $link->getRemainingDays() }} days left
                                    @else
                                        Expires today
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                {{ $link->access_count }}
                                @if($link->last_accessed_at)
                                    <div class="text-xs text-gray-500">
                                        Last: {{ $link->last_accessed_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="text-sm">{{ $link->created_at->format('M j, Y') }}</div>
                                @if($link->created_by)
                                    <div class="text-xs text-gray-500">{{ $link->created_by }}</div>
                                @endif
                            </td>
                            <td>
                                <button 
                                    onclick="deleteLink({{ $link->id }})"
                                    class="btn-sm text-red-600 hover:text-red-800"
                                    title="Delete"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Generate Modal -->
    <div id="generateModal" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Generate Preview Link</h3>
                <button onclick="hideGenerateModal()" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="generateForm">
                    @csrf
                    <div class="field-group">
                        <label>Collection</label>
                        <select name="collection" id="collection" required class="form-select">
                            <option value="">Select Collection</option>
                            @foreach($collections as $collection)
                                <option value="{{ $collection['handle'] }}">{{ $collection['title'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="field-group">
                        <label>Entry ID</label>
                        <input type="text" name="entry_id" required class="form-input" placeholder="Enter entry ID">
                        <small class="help-text">You can find this in the entry's URL in the control panel</small>
                    </div>
                    
                    <div class="field-group">
                        <label>Expiry (days)</label>
                        <input type="number" name="expiry_days" value="7" min="1" max="30" class="form-input">
                        <small class="help-text">Preview link will expire after this many days</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="hideGenerateModal()" class="btn-secondary">Cancel</button>
                <button type="button" onclick="generatePreviewLink()" class="btn-primary">Generate Link</button>
            </div>
        </div>
    </div>

    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal.hidden { display: none; }
        .modal-content {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            max-height: 90%;
            overflow-y: auto;
        }
        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }
        .modal-body { padding: 24px; }
        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        .field-group { margin-bottom: 20px; }
        .field-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-input, .form-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
        }
        .help-text {
            color: #6b7280;
            font-size: 12px;
            margin-top: 4px;
            display: block;
        }
    </style>

    <script>
        function showGenerateModal() {
            document.getElementById('generateModal').classList.remove('hidden');
        }
        
        function hideGenerateModal() {
            document.getElementById('generateModal').classList.add('hidden');
        }
        
        function copyToClipboard(inputId) {
            const input = document.getElementById(inputId);
            input.select();
            document.execCommand('copy');
            
            // Show feedback
            const button = input.nextElementSibling;
            const originalText = button.textContent;
            button.textContent = 'Copied!';
            setTimeout(() => {
                button.textContent = originalText;
            }, 2000);
        }
        
        async function generatePreviewLink() {
            const form = document.getElementById('generateForm');
            const formData = new FormData(form);
            
            try {
                const response = await fetch('{{ route('preview-links.cp.generate') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Preview link generated successfully!\n\n' + result.preview_url);
                    hideGenerateModal();
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error generating preview link: ' + error.message);
            }
        }
        
        async function deleteLink(id) {
            if (!confirm('Are you sure you want to delete this preview link?')) {
                return;
            }
            
            try {
                const response = await fetch(`/cp/preview-links/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error deleting preview link: ' + error.message);
            }
        }
    </script>
@endsection