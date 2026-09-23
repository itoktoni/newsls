<script>
    function warehouseApp() {
        return {
            drawerOpen: false,
            sidebarOpen: true,
            notifications: [],
            unreadCount: 0,

            init() {
                this.fetchNotifications();
                window.addEventListener('new-notification', (e) => this.handleRealtimeNotification(e.detail));
            },

            handleRealtimeNotification(notif) {
                const existingIds = new Set(this.notifications.map(n => n.id));
                if (!existingIds.has(notif.id)) {
                    this.notifications.unshift(notif);
                    this.unreadCount = this.notifications.filter(n => !n.read).length;
                    if (window.showToast) window.showToast(notif.title, notif.body);
                }
            },

            async fetchNotifications() {
                try {
                    const res = await fetch('/notifications-web', {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.notifications = data.notifications || [];
                    this.unreadCount = data.unread_count || 0;
                } catch (e) {
                    console.warn('Failed to fetch notifications:', e);
                }
            },

            async markRead(notif) {
                if (notif.read) return;
                notif.read = true;
                this.unreadCount = this.notifications.filter(n => !n.read).length;
                try {
                    await fetch('/notifications-web/' + notif.id + '/read', {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                } catch (e) {
                    console.warn('Failed to mark read:', e);
                }
            },

            openNotification(notif) {
                this.markRead(notif);
                if (notif.url) window.location.href = notif.url;
            },

            async markAllRead() {
                this.notifications.forEach(n => n.read = true);
                this.unreadCount = 0;
                try {
                    await fetch('/notifications-web/read-all', {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });
                } catch (e) {
                    console.warn('Failed to mark all read:', e);
                }
            },
        }
    }

    function scrollActiveMenuIntoView() {
        document.querySelectorAll('nav .bg-primary').forEach(function(el) {
            el.scrollIntoView({ block: 'center', behavior: 'smooth' });
        });
    }
    document.addEventListener('DOMContentLoaded', scrollActiveMenuIntoView);
    document.addEventListener('livewire:navigated', scrollActiveMenuIntoView);

    // Escape HTML to prevent XSS in dynamically-created toast content
    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Toast helper — appends to the #toast-container rendered by the toast component.
    // Usage:
    //   showToast(title, body)                  — info type (backward compatible)
    //   showToast(title, body, 'success')       — with type
    //   showToast(title, body, 'success', 3000) — with type and duration (ms)
    window.showToast = function(title, body, type, duration) {
        type     = type || 'info';
        duration = duration || 5000;

        var toastTypes = {
            'success': { bg: 'bg-green-50', border: 'border-green-200', text: 'text-green-800', icon: 'check_circle' },
            'danger':  { bg: 'bg-red-50',   border: 'border-red-200',   text: 'text-red-800',   icon: 'error' },
            'error':   { bg: 'bg-red-50',   border: 'border-red-200',   text: 'text-red-800',   icon: 'error' },
            'warning': { bg: 'bg-amber-50', border: 'border-amber-200', text: 'text-amber-800', icon: 'warning' },
            'info':    { bg: 'bg-blue-50',  border: 'border-blue-200',  text: 'text-blue-800',  icon: 'info' },
        };
        var t = toastTypes[type] || toastTypes['info'];

        var container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed bottom-4 right-4 z-50 space-y-2 w-80';
            document.body.appendChild(container);
        }

        var toast = document.createElement('div');
        toast.className = 'flex items-start gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium border ' + t.bg + ' ' + t.border + ' ' + t.text;
        toast.innerHTML =
            '<span class="material-symbols-outlined text-base mt-0.5">' + t.icon + '</span>' +
            '<div class="flex-1">' +
                (title ? '<div class="font-semibold">' + escapeHtml(title) + '</div>' : '') +
                (body   ? '<div>' + escapeHtml(body) + '</div>' : '') +
            '</div>' +
            '<button type="button" onclick="this.parentElement.remove()" class="ml-2 shrink-0 hover:opacity-70">' +
                '<span class="material-symbols-outlined text-base">close</span>' +
            '</button>';

        container.appendChild(toast);

        setTimeout(function() {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(0.5rem)';
            setTimeout(function() { toast.remove(); }, 300);
        }, duration);
    };

    // Image Picker Vanilla JS helper functions
    function imgPickerPreview(pickerId) {
        var input = document.getElementById(pickerId + '_input');
        var previewWrap = document.getElementById(pickerId + '_preview_wrap');
        var preview = document.getElementById(pickerId + '_preview');
        if (input && previewWrap && preview) {
            if (input.value) {
                preview.src = input.value;
                previewWrap.style.display = 'flex';
            } else {
                previewWrap.style.display = 'none';
            }
        }
    }

    // Vanilla JS Image Browser
    var imgBrowser = {
        activePickerId: null,
        onSelect: null,
        selectedImage: null,
        images: [],
        loading: false,
        searchQuery: '',
        currentPage: 1,
        totalPages: 1,
        uploadProgress: 0,
        uploading: false,

        open: function(pickerId, onSelect) {
            this.activePickerId = pickerId || null;
            this.onSelect = typeof onSelect === 'function' ? onSelect : null;
            this.selectedImage = null;
            this.searchQuery = '';
            this.currentPage = 1;
            this.show();
            this.loadImages(1);
        },

        show: function() {
            var modal = document.getElementById('image-browser-modal');
            if (modal) modal.style.display = 'flex';
        },

        hide: function() {
            var modal = document.getElementById('image-browser-modal');
            if (modal) modal.style.display = 'none';
            this.activePickerId = null;
            this.onSelect = null;
            this.selectedImage = null;
        },

        dragCounter: 0,

        handleDragEnter: function(e) {
            e.preventDefault();
            this.dragCounter++;
            var overlay = document.getElementById('img-browser-drop-overlay');
            if (overlay) overlay.style.display = 'flex';
        },

        handleDragLeave: function(e) {
            e.preventDefault();
            this.dragCounter--;
            if (this.dragCounter <= 0) {
                this.dragCounter = 0;
                var overlay = document.getElementById('img-browser-drop-overlay');
                if (overlay) overlay.style.display = 'none';
            }
        },

        handleDragOver: function(e) {
            e.preventDefault();
            if (e.dataTransfer) e.dataTransfer.dropEffect = 'copy';
        },

        handleDrop: function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.dragCounter = 0;
            var overlay = document.getElementById('img-browser-drop-overlay');
            if (overlay) overlay.style.display = 'none';
            var files = e.dataTransfer ? e.dataTransfer.files : [];
            var images = [];
            for (var i = 0; i < files.length; i++) {
                if (files[i].type.startsWith('image/')) images.push(files[i]);
            }
            if (images.length) this.uploadFiles(images);
        },

        loadImages: async function(page) {
            this.loading = true;
            this.currentPage = page || 1;
            this.updateUI();
            try {
                var params = new URLSearchParams({ page: this.currentPage, per_page: 24, search: this.searchQuery });
                var res = await fetch('/api/media?' + params.toString(), {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                var data = await res.json();
                this.images = data.data || [];
                this.totalPages = data.last_page || 1;
            } catch (e) {
                console.error('Failed to load images:', e);
                this.images = [];
            }
            this.loading = false;
            this.renderGrid();
        },

        renderGrid: function() {
            var grid = document.getElementById('img-browser-grid');
            var empty = document.getElementById('img-browser-empty');
            var spinner = document.getElementById('img-browser-loading');
            if (!grid) return;

            spinner.style.display = this.loading ? 'flex' : 'none';

            if (!this.loading && this.images.length === 0) {
                empty.style.display = 'block';
                grid.innerHTML = '';
                this.updateUI();
                return;
            }
            empty.style.display = 'none';

            var self = this;
            var html = '';
            this.images.forEach(function(img) {
                var isSelected = self.selectedImage && self.selectedImage.id === img.id;
                var src = img.thumbnail || img.url;
                html += '<div class="relative aspect-square rounded-lg overflow-hidden border-2 cursor-pointer transition-all hover:shadow-lg ' + (isSelected ? 'border-blue-500 ring-2 ring-blue-200' : 'border-transparent hover:border-gray-300') + '" onclick="imgBrowser.selectImage(' + img.id + ')">'
                    + '<img src="' + src + '" alt="' + (img.alt || img.filename) + '" class="w-full h-full object-cover">'
                    + (isSelected ? '<div class="absolute inset-0 bg-blue-500/20 flex items-center justify-center"><i class="icon-[tabler--circle-check] text-3xl text-blue-500"></i></div>' : '')
                    + '<div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-2"><p class="text-white text-xs truncate">' + img.filename + '</p></div>'
                    + '</div>';
            });
            grid.innerHTML = html;
            this.updateUI();
        },

        selectImage: function(id) {
            var img = this.images.find(function(i) { return i.id === id; });
            this.selectedImage = img || null;
            this.renderGrid();
        },

        confirm: function() {
            if (!this.selectedImage) return;
            // Callback mode (e.g. TinyMCE image dialog)
            if (typeof this.onSelect === 'function') {
                this.onSelect(this.selectedImage.url, { alt: this.selectedImage.alt || this.selectedImage.filename });
                this.hide();
                return;
            }
            if (this.activePickerId) {
                var input = document.getElementById(this.activePickerId + '_input');
                var previewWrap = document.getElementById(this.activePickerId + '_preview_wrap');
                var preview = document.getElementById(this.activePickerId + '_preview');
                var dropzoneContent = document.getElementById(this.activePickerId + '_dropzone_content');
                if (input) input.value = this.selectedImage.url;
                if (preview && previewWrap) {
                    preview.src = this.selectedImage.url;
                    previewWrap.style.display = 'flex';
                }
                if (dropzoneContent) dropzoneContent.style.display = 'none';
                this.hide();
            }
        },

        uploadFiles: function(eventOrFiles) {
            var files = eventOrFiles.target ? eventOrFiles.target.files : eventOrFiles;
            if (!files || !files.length) return;
            var self = this;
            self.uploading = true;
            self.uploadProgress = 0;
            self.updateUI();

            var formData = new FormData();
            for (var i = 0; i < files.length; i++) formData.append('images[]', files[i]);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/api/media/upload');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.onprogress = function(e) { if (e.lengthComputable) { self.uploadProgress = Math.round((e.loaded / e.total) * 100); self.updateUI(); } };
            xhr.onload = function() {
                self.uploading = false;
                if (xhr.status === 200 || xhr.status === 201) {
                    var data = JSON.parse(xhr.responseText);
                    if (data.images && data.images.length > 0) { self.selectedImage = data.images[0]; }
                    self.loadImages(1);
                } else { alert('Upload failed.'); self.updateUI(); }
            };
            xhr.onerror = function() { self.uploading = false; alert('Upload failed.'); self.updateUI(); };
            xhr.send(formData);
            if (eventOrFiles.target) eventOrFiles.target.value = '';
        },

        updateUI: function() {
            var spinner = document.getElementById('img-browser-loading');
            var progressBar = document.getElementById('img-browser-progress');
            var progressFill = document.getElementById('img-browser-progress-fill');
            var progressPct = document.getElementById('img-browser-progress-pct');
            var pageInfo = document.getElementById('img-browser-page-info');
            var btnConfirm = document.getElementById('img-browser-confirm');
            var prevBtn = document.getElementById('img-browser-prev');
            var nextBtn = document.getElementById('img-browser-next');

            if (spinner) spinner.style.display = this.loading ? 'flex' : 'none';
            if (progressBar) progressBar.style.display = this.uploading ? 'block' : 'none';
            if (progressFill) progressFill.style.width = this.uploadProgress + '%';
            if (progressPct) progressPct.textContent = this.uploadProgress + '%';
            if (pageInfo) pageInfo.textContent = 'Page ' + this.currentPage + ' of ' + this.totalPages;
            if (btnConfirm) btnConfirm.disabled = !this.selectedImage;
            if (prevBtn) prevBtn.disabled = this.currentPage <= 1;
            if (nextBtn) nextBtn.disabled = this.currentPage >= this.totalPages;
        },

        search: function(value) {
            this.searchQuery = value;
            this.loadImages(1);
        }
    };

    function openImageBrowser(pickerId) {
        imgBrowser.open(pickerId);
    }

    // Drag & drop + file upload handler for image fields
    function handleImageDrop(event, pickerId) {
        event.preventDefault();
        event.stopPropagation();
        var dropzone = document.getElementById(pickerId + '_dropzone');
        if (dropzone) {
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
        }
        var files = event.dataTransfer.files;
        if (files.length > 0 && files[0].type.startsWith('image/')) {
            uploadImageFile(files[0], pickerId);
        }
    }

    function handleImageFileSelect(event, pickerId) {
        var files = event.target.files;
        if (files.length > 0) {
            uploadImageFile(files[0], pickerId);
        }
    }

    function uploadImageFile(file, pickerId) {
        var progressDiv = document.getElementById(pickerId + '_upload_progress');
        var dropzoneContent = document.getElementById(pickerId + '_dropzone_content');
        var previewWrap = document.getElementById(pickerId + '_preview_wrap');
        var preview = document.getElementById(pickerId + '_preview');
        var input = document.getElementById(pickerId + '_input');

        if (progressDiv) progressDiv.classList.remove('hidden');
        if (dropzoneContent) dropzoneContent.style.display = 'none';

        var formData = new FormData();
        formData.append('images[]', file);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/media/upload');
        xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.onload = function() {
            if (progressDiv) progressDiv.classList.add('hidden');
            if (xhr.status === 200 || xhr.status === 201) {
                var data = JSON.parse(xhr.responseText);
                if (data.images && data.images.length > 0) {
                    var url = data.images[0].url;
                    if (input) input.value = url;
                    if (preview) preview.src = url;
                    if (previewWrap) previewWrap.style.display = 'flex';
                }
            } else {
                alert('Upload failed. Please try again.');
                if (dropzoneContent) dropzoneContent.style.display = 'block';
            }
        };
        xhr.onerror = function() {
            if (progressDiv) progressDiv.classList.add('hidden');
            alert('Upload failed. Please try again.');
            if (dropzoneContent) dropzoneContent.style.display = 'block';
        };
        xhr.send(formData);
    }

    function imgPickerRemove(pickerId) {
        var input = document.getElementById(pickerId + '_input');
        var previewWrap = document.getElementById(pickerId + '_preview_wrap');
        var dropzoneContent = document.getElementById(pickerId + '_dropzone_content');
        if (input) input.value = '';
        if (previewWrap) previewWrap.style.display = 'none';
        if (dropzoneContent) dropzoneContent.style.display = 'block';
    }
</script>

{{-- Image Browser Modal (Pure Vanilla JS - Livewire safe) --}}
<div id="image-browser-modal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4" style="display:none;">
    <div class="absolute inset-0 bg-black/60" onclick="imgBrowser.hide()"></div>
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[85vh] flex flex-col overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <i class="icon-[tabler--photo] text-blue-500"></i> Media Library
            </h2>
            <button type="button" onclick="imgBrowser.hide()" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="icon-[tabler--x] text-xl"></i>
            </button>
        </div>
        {{-- Toolbar --}}
        <div class="flex items-center gap-4 px-6 py-3 border-b border-gray-200 bg-white">
            <div class="flex-1 relative">
                <i class="icon-[tabler--search] absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="img-browser-search" placeholder="Search images..." oninput="imgBrowser.search(this.value)" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
            </div>
            <label class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors cursor-pointer">
                <i class="icon-[tabler--upload]"></i> <span>Upload</span>
                <input type="file" multiple accept="image/*" onchange="imgBrowser.uploadFiles(event)" class="hidden">
            </label>
        </div>
        {{-- Upload Progress --}}
        <div id="img-browser-progress" style="display:none;" class="px-6 py-2 bg-blue-50 border-b border-blue-100">
            <div class="flex items-center gap-3">
                <i class="icon-[tabler--loader] animate-spin text-blue-500"></i>
                <span class="text-sm text-blue-700">Uploading...</span>
                <div class="flex-1 h-2 bg-blue-200 rounded-full overflow-hidden">
                    <div id="img-browser-progress-fill" class="h-full bg-blue-600 transition-all duration-300" style="width:0%"></div>
                </div>
                <span id="img-browser-progress-pct" class="text-sm text-blue-700">0%</span>
            </div>
        </div>
        {{-- Content --}}
        <div id="img-browser-content" class="flex-1 overflow-y-auto p-6 relative"
             ondragenter="imgBrowser.handleDragEnter(event)"
             ondragleave="imgBrowser.handleDragLeave(event)"
             ondragover="imgBrowser.handleDragOver(event)"
             ondrop="imgBrowser.handleDrop(event)">
            <div id="img-browser-drop-overlay" style="display:none;" class="absolute inset-0 z-50 bg-blue-50/95 border-4 border-dashed border-blue-500 rounded-lg flex flex-col items-center justify-center pointer-events-none">
                <i class="icon-[tabler--cloud-upload] text-6xl text-blue-500 mb-4"></i>
                <p class="text-xl font-semibold text-blue-700">Drop images to upload</p>
                <p class="text-sm text-blue-500 mt-1">Release your files here</p>
            </div>
            <div id="img-browser-loading" class="flex items-center justify-center py-12" style="display:none;">
                <i class="icon-[tabler--loader] animate-spin text-3xl text-gray-400"></i>
            </div>
            <div id="img-browser-empty" class="text-center py-12 text-gray-400" style="display:none;">
                <i class="icon-[tabler--photo] text-5xl mb-4 block opacity-30"></i>
                <p class="text-lg font-medium">No images found</p>
                <p class="text-sm mt-1">Upload some images to get started</p>
            </div>
            <div id="img-browser-grid" class="grid grid-cols-4 md:grid-cols-6 gap-3"></div>
        </div>
        {{-- Footer --}}
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex items-center gap-2">
                <button type="button" id="img-browser-prev" onclick="imgBrowser.loadImages(imgBrowser.currentPage - 1)" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="icon-[tabler--chevron-left]"></i>
                </button>
                <span id="img-browser-page-info" class="text-sm text-gray-600">Page 1 of 1</span>
                <button type="button" id="img-browser-next" onclick="imgBrowser.loadImages(imgBrowser.currentPage + 1)" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="icon-[tabler--chevron-right]"></i>
                </button>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="imgBrowser.hide()" class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">Cancel</button>
                <button type="button" id="img-browser-confirm" onclick="imgBrowser.confirm()" disabled class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <i class="icon-[tabler--check] mr-1"></i> Select Image
                </button>
            </div>
        </div>
    </div>
</div>
