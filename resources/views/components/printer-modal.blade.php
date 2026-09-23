@props(['id' => 'printer-modal'])

<div id="{{ $id }}" class="fixed inset-0 z-[9999] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="PrinterModal.close()"></div>
    <div class="absolute inset-x-4 top-[10%] mx-auto max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[80vh]">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <span class="icon-[tabler--printer] text-xl text-blue-600"></span>
                <h3 class="text-base font-bold text-gray-900">Bluetooth Printer</h3>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="PrinterModal.testPrint()" class="text-xs px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 font-semibold hover:bg-blue-100 transition-colors" title="Test Print">
                    <span class="icon-[tabler--test]"></span> Test
                </button>
                <button onclick="PrinterModal.close()" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600">
                    <span class="icon-[tabler--x]"></span>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
            {{-- Status --}}
            <div id="pm-status" class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                <div class="w-3 h-3 rounded-full bg-gray-300 animate-pulse" id="pm-status-dot"></div>
                <div>
                    <div class="text-sm font-semibold text-gray-700" id="pm-status-text">Checking...</div>
                    <div class="text-xs text-gray-400" id="pm-status-sub"></div>
                </div>
            </div>

            {{-- Saved Printer --}}
            <div id="pm-saved" class="hidden">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2 block">Saved Printer</label>
                <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50 border border-blue-100">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="icon-[tabler--printer] text-lg text-blue-500 shrink-0"></span>
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-gray-900 truncate" id="pm-saved-name"></div>
                            <div class="text-xs text-gray-400 font-mono" id="pm-saved-address"></div>
                        </div>
                    </div>
                    <button onclick="PrinterModal.removeSaved()" class="text-xs px-2.5 py-1 rounded-lg bg-red-50 text-red-500 font-semibold hover:bg-red-100 transition-colors shrink-0 ml-2">
                        <span class="icon-[tabler--trash]"></span>
                    </button>
                </div>
            </div>

            {{-- Paired Devices --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Paired Devices</label>
                    <button onclick="PrinterModal.scan()" class="text-xs px-2.5 py-1 rounded-lg bg-green-50 text-green-600 font-semibold hover:bg-green-100 transition-colors">
                        <span class="icon-[tabler--bluetooth]"></span> Scan
                    </button>
                </div>
                <div id="pm-paired-list" class="space-y-2">
                    <div class="text-sm text-gray-400 text-center py-4">No paired printers found</div>
                </div>
            </div>

            {{-- Discovered Devices --}}
            <div id="pm-discovered" class="hidden">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Available Devices</label>
                    <div id="pm-scanning" class="hidden">
                        <span class="icon-[tabler--loader-2] animate-spin text-sm text-blue-500"></span>
                        <span class="text-xs text-blue-500 ml-1">Scanning...</span>
                    </div>
                </div>
                <div id="pm-discovered-list" class="space-y-2"></div>
            </div>

            {{-- Paper Width --}}
            <div>
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2 block">Paper Width</label>
                <div class="flex gap-2">
                    <button onclick="PrinterModal.setPaperWidth(58)" id="pm-paper-58" class="flex-1 py-2 rounded-xl text-sm font-bold border-2 transition-colors">58mm</button>
                    <button onclick="PrinterModal.setPaperWidth(80)" id="pm-paper-80" class="flex-1 py-2 rounded-xl text-sm font-bold border-2 transition-colors">80mm</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const PrinterModal = {
    modal: null,
    paperWidth: 58,

    init() {
        this.modal = document.getElementById('{{ $id }}');
        this.paperWidth = parseInt(localStorage.getItem('printer_paper_width') || '58');
        this.updatePaperButtons();

        // Auto-connect on init
        if (this.isNative()) {
            window.onPrinterConnected = (data) => this.onConnected(data);
            window.onPrinterDisconnected = (data) => this.onDisconnected(data);
            window.onPrintersFound = (data) => this.onPrintersFound(data);
            window.onPrintResult = (data) => this.onPrintResult(data);
            window.onPrinterRemoved = (data) => this.onRemoved(data);
            window.onBluetoothPermission = (status) => this.onPermissionResult(status);

            // Bluetooth permission (Android 12+) is required to list/connect
            if (!BluetoothPrinter.hasPermission()) {
                BluetoothPrinter.requestPermission();
            }

            this.refreshStatus();

            // Auto-connect
            const saved = BluetoothPrinter.getSaved();
            if (saved.address && BluetoothPrinter.hasPermission()) {
                BluetoothPrinter.autoConnect();
            }
        }
    },

    onPermissionResult(status) {
        if (status === 'granted') {
            if (typeof window.showToast === 'function') showToast('Bluetooth ready', 'success');
            this.refreshStatus();
        } else {
            if (typeof window.showToast === 'function') showToast('Bluetooth permission denied', 'error');
        }
    },

    isNative() {
        return typeof NativeBridge !== 'undefined' && typeof NativeBridge.getPairedPrinters === 'function';
    },

    open() {
        this.modal.classList.remove('hidden');
        this.refreshStatus();
    },

    close() {
        this.modal.classList.add('hidden');
    },

    refreshStatus() {
        if (!this.isNative()) {
            this.setStatus('disconnected', 'Not available', 'Run in Android app');
            return;
        }

        if (!BluetoothPrinter.hasPermission()) {
            this.setStatus('disconnected', 'Bluetooth permission needed', 'Allow Bluetooth access first');
            document.getElementById('pm-paired-list').innerHTML = '<div class="text-sm text-gray-400 text-center py-4">Bluetooth permission required</div>';
            return;
        }

        const connected = BluetoothPrinter.isConnected();
        if (connected) {
            const dev = BluetoothPrinter.getConnected();
            this.setStatus('connected', dev.name || 'Connected', dev.address);
            document.getElementById('pm-saved').classList.add('hidden');
        } else {
            const saved = BluetoothPrinter.getSaved();
            if (saved.address) {
                this.setStatus('disconnected', 'Disconnected', saved.name);
                document.getElementById('pm-saved').classList.remove('hidden');
                document.getElementById('pm-saved-name').textContent = saved.name;
                document.getElementById('pm-saved-address').textContent = saved.address;
            } else {
                this.setStatus('disconnected', 'No printer', 'Tap Scan to find printers');
            }
        }

        this.loadPairedDevices();
    },

    setStatus(status, text, sub) {
        const dot = document.getElementById('pm-status-dot');
        const textEl = document.getElementById('pm-status-text');
        const subEl = document.getElementById('pm-status-sub');

        dot.className = 'w-3 h-3 rounded-full ' + (status === 'connected' ? 'bg-green-500' : status === 'connecting' ? 'bg-yellow-500 animate-pulse' : 'bg-gray-300');
        textEl.textContent = text;
        subEl.textContent = sub || '';
    },

    loadPairedDevices() {
        const list = document.getElementById('pm-paired-list');
        let paired = [];
        try {
            paired = BluetoothPrinter.getPairedPrinters();
        } catch (e) {
            list.innerHTML = '<div class="text-sm text-red-400 text-center py-4">Unable to read paired devices</div>';
            return;
        }

        if (!Array.isArray(paired) || paired.length === 0) {
            list.innerHTML = '<div class="text-sm text-gray-400 text-center py-4">No paired printers found</div>';
            return;
        }

        list.innerHTML = paired.map(d => `
            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/50 transition-colors cursor-pointer" onclick="PrinterModal.connect('${d.address}')">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="icon-[tabler--printer] text-lg text-gray-400 shrink-0"></span>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-gray-900 truncate">${this.escapeHtml(d.name)}</div>
                        <div class="text-xs text-gray-400 font-mono">${d.address}</div>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-lg bg-blue-100 text-blue-600 font-semibold shrink-0 ml-2">Connect</span>
            </div>
        `).join('');
    },

    scan() {
        if (!BluetoothPrinter.hasPermission()) {
            this.setStatus('disconnected', 'Permission needed', 'Grant Bluetooth access first');
            BluetoothPrinter.requestPermission();
            return;
        }

        document.getElementById('pm-discovered').classList.remove('hidden');
        document.getElementById('pm-scanning').classList.remove('hidden');
        document.getElementById('pm-discovered-list').innerHTML = '';

        BluetoothPrinter.scanPrinters();
    },

    onPrintersFound(printersJSON) {
        document.getElementById('pm-scanning').classList.add('hidden');

        let devices;
        try {
            devices = typeof printersJSON === 'string' ? JSON.parse(printersJSON) : printersJSON;
        } catch (e) {
            devices = [];
        }

        const list = document.getElementById('pm-discovered-list');
        if (!devices || devices.length === 0) {
            list.innerHTML = '<div class="text-sm text-gray-400 text-center py-4">No devices found</div>';
            return;
        }

        if (devices[0]?.error) {
            list.innerHTML = `<div class="text-sm text-red-400 text-center py-4">${this.escapeHtml(devices[0].error)}</div>`;
            return;
        }

        list.innerHTML = devices.map(d => `
            <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50/50 transition-colors cursor-pointer" onclick="PrinterModal.connect('${d.address}')">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="icon-[tabler--bluetooth] text-lg text-gray-400 shrink-0"></span>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-gray-900 truncate">${this.escapeHtml(d.name)}</div>
                        <div class="text-xs text-gray-400 font-mono">${d.address}</div>
                    </div>
                </div>
                <span class="text-xs px-2 py-1 rounded-lg bg-blue-100 text-blue-600 font-semibold shrink-0 ml-2">Connect</span>
            </div>
        `).join('');
    },

    connect(address) {
        if (!BluetoothPrinter.hasPermission()) {
            this.setStatus('disconnected', 'Permission needed', 'Grant Bluetooth access first');
            BluetoothPrinter.requestPermission();
            return;
        }
        this.setStatus('connecting', 'Connecting...', address);
        BluetoothPrinter.connect(address);
    },

    onConnected(dataJSON) {
        let data;
        try {
            data = typeof dataJSON === 'string' ? JSON.parse(dataJSON) : dataJSON;
        } catch (e) {
            data = { success: false, error: 'Parse error' };
        }

        if (data.success) {
            this.setStatus('connected', data.name || 'Connected', data.address);
            this.refreshStatus();
            if (typeof window.showToast === 'function') {
                showToast('Printer connected: ' + data.name, 'success');
            }
        } else {
            this.setStatus('disconnected', 'Connection failed', data.error || '');
            if (typeof window.showToast === 'function') {
                showToast('Printer connection failed', 'error');
            }
        }
    },

    onDisconnected(data) {
        this.setStatus('disconnected', 'Disconnected', '');
        this.refreshStatus();
    },

    removeSaved() {
        BluetoothPrinter.removeSaved();
    },

    onRemoved(data) {
        this.refreshStatus();
        document.getElementById('pm-saved').classList.add('hidden');
    },

    testPrint() {
        if (!this.isNative()) {
            if (typeof window.showToast === 'function') showToast('Not available in browser', 'error');
            return;
        }
        if (!BluetoothPrinter.isConnected()) {
            if (typeof window.showToast === 'function') showToast('Connect to a printer first', 'error');
            return;
        }
        BluetoothPrinter.testPrint();
    },

    onPrintResult(dataJSON) {
        let data;
        try {
            data = typeof dataJSON === 'string' ? JSON.parse(dataJSON) : dataJSON;
        } catch (e) {
            data = { success: false, error: 'Parse error' };
        }

        if (data.success) {
            if (typeof window.showToast === 'function') showToast('Print successful', 'success');
        } else {
            if (typeof window.showToast === 'function') showToast('Print failed: ' + (data.error || ''), 'error');
        }
    },

    setPaperWidth(width) {
        this.paperWidth = width;
        localStorage.setItem('printer_paper_width', width.toString());
        this.updatePaperButtons();
    },

    updatePaperButtons() {
        const btn58 = document.getElementById('pm-paper-58');
        const btn80 = document.getElementById('pm-paper-80');

        if (this.paperWidth === 58) {
            btn58.className = 'flex-1 py-2 rounded-xl text-sm font-bold border-2 border-blue-500 bg-blue-50 text-blue-600 transition-colors';
            btn80.className = 'flex-1 py-2 rounded-xl text-sm font-bold border-2 border-gray-200 text-gray-500 hover:border-gray-300 transition-colors';
        } else {
            btn80.className = 'flex-1 py-2 rounded-xl text-sm font-bold border-2 border-blue-500 bg-blue-50 text-blue-600 transition-colors';
            btn58.className = 'flex-1 py-2 rounded-xl text-sm font-bold border-2 border-gray-200 text-gray-500 hover:border-gray-300 transition-colors';
        }
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },
};

// Init on DOM ready
document.addEventListener('DOMContentLoaded', () => PrinterModal.init());
</script>
@endpush
