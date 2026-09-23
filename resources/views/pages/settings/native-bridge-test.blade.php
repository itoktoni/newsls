<x-layouts::app title="Settings - NativeBridge Test">
    <x-breadcrumb :items="[['url' => route('dashboard'), 'label' => 'Dashboard'], ['url' => '', 'label' => 'NativeBridge Test']]" />

    {{-- Log Panel --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">terminal</span>
                Console Log
            </h3>
            <button onclick="clearLog()" class="text-xs px-3 py-1 rounded-full bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest transition-colors">Clear</button>
        </div>
        <div id="log-panel" class="bg-black rounded-lg p-3 h-40 overflow-x-hidden overflow-y-auto font-mono text-xs text-green-400 break-all">
            <div class="text-gray-500">Ready. Click any button to test NativeBridge functions.</div>
        </div>
    </div>

    {{-- 1. Device Info --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <h3 class="font-headline-sm text-headline-sm text-on-surface pb-3 mb-3 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">info</span>
            Device Info
        </h3>
        <div class="grid grid-cols-1 gap-2">
            <button onclick="testGetDeviceModel()" class="btn-test">
                <span class="material-symbols-outlined text-sm">phone_iphone</span>
                getDeviceModel()
            </button>
            <button onclick="testGetDeviceBrand()" class="btn-test">
                <span class="material-symbols-outlined text-sm">branding_watermark</span>
                getDeviceBrand()
            </button>
            <button onclick="testGetDeviceManufacturer()" class="btn-test">
                <span class="material-symbols-outlined text-sm">factory</span>
                getManufacturer()
            </button>
            <button onclick="testGetSdkVersion()" class="btn-test">
                <span class="material-symbols-outlined text-sm">android</span>
                getSdkVersion()
            </button>
            <button onclick="testGetAppVersion()" class="btn-test">
                <span class="material-symbols-outlined text-sm">new_releases</span>
                getAppVersion()
            </button>
            <button onclick="testGetPackageName()" class="btn-test">
                <span class="material-symbols-outlined text-sm">package_2</span>
                getPackageName()
            </button>
            <button onclick="testGetAndroidId()" class="btn-test">
                <span class="material-symbols-outlined text-sm">fingerprint</span>
                getAndroidId()
            </button>
            <button onclick="testGetSerialNumber()" class="btn-test">
                <span class="material-symbols-outlined text-sm">qr_code</span>
                getSerialNumber()
            </button>
            <button onclick="testGetUniqueId()" class="btn-test">
                <span class="material-symbols-outlined text-sm">key</span>
                getUniqueId()
            </button>
            <button onclick="testGetDeviceInfo()" class="btn-test">
                <span class="material-symbols-outlined text-sm">devices</span>
                getDeviceInfo() (Full)
            </button>
        </div>
    </div>

    {{-- 2. UI Feedback --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <h3 class="font-headline-sm text-headline-sm text-on-surface pb-3 mb-3 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">notifications</span>
            UI Feedback
        </h3>
        <div class="grid grid-cols-1 gap-2">
            <button onclick="testShowToast()" class="btn-test">
                <span class="material-symbols-outlined text-sm">message</span>
                showToast()
            </button>
            <button onclick="testVibrate(100)" class="btn-test">
                <span class="material-symbols-outlined text-sm">vibration</span>
                vibrate(100ms)
            </button>
            <button onclick="testVibrate(500)" class="btn-test">
                <span class="material-symbols-outlined text-sm">vibration</span>
                vibrate(500ms)
            </button>
            <button onclick="testVibrate(1000)" class="btn-test">
                <span class="material-symbols-outlined text-sm">vibration</span>
                vibrate(1000ms)
            </button>
        </div>
    </div>

    {{-- 3. Network --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <h3 class="font-headline-sm text-headline-sm text-on-surface pb-3 mb-3 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">wifi</span>
            Network
        </h3>
        <div class="grid grid-cols-1 gap-2">
            <button onclick="testIsConnected()" class="btn-test">
                <span class="material-symbols-outlined text-sm">wifi_find</span>
                isConnected()
            </button>
            <button onclick="testStartNetworkCallback()" class="btn-test">
                <span class="material-symbols-outlined text-sm">wifi_find</span>
                startNetworkCallback()
            </button>
            <button onclick="testStopNetworkCallback()" class="btn-test">
                <span class="material-symbols-outlined text-sm">wifi_off</span>
                stopNetworkCallback()
            </button>
        </div>
        <div id="network-status" class="mt-3 px-3 py-2 rounded-lg bg-surface-container-high text-on-surface-variant text-sm font-mono">
            Network status: Checking...
        </div>
    </div>

    {{-- 4. Print & Save --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <h3 class="font-headline-sm text-headline-sm text-on-surface pb-3 mb-3 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">print</span>
            Print & Save
        </h3>
        <div class="grid grid-cols-1 gap-2">
            <button onclick="testPrintPage()" class="btn-test">
                <span class="material-symbols-outlined text-sm">print</span>
                printPage()
            </button>
            <button onclick="testPrintArea()" class="btn-test">
                <span class="material-symbols-outlined text-sm">print</span>
                Print Area Only
            </button>
            <button onclick="testSaveAsPdf()" class="btn-test">
                <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                saveAsPdf()
            </button>
        </div>
    </div>

    {{-- Dedicated print area. Browser/Android print hides the rest of this test page. --}}
    <div id="print-area" class="bg-white border-2 border-dashed border-outline-variant rounded-xl p-5 mb-4 text-center">
        <div class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Invoice</div>
        <div class="mt-2 text-xl font-bold text-on-surface">COLD STORAGE</div>
        <div class="mt-1 text-sm text-on-surface-variant">Print area example</div>
        <div class="mt-4 border-t border-outline-variant pt-3 text-left text-sm text-on-surface">
            <div class="flex justify-between"><span>Label</span><strong>LOC-01</strong></div>
            <div class="flex justify-between"><span>Qty</span><strong>1</strong></div>
        </div>
        <div class="print-area-boundary" aria-hidden="true">........................................................................</div>
    </div>

    {{-- 5. Share & Image --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <h3 class="font-headline-sm text-headline-sm text-on-surface pb-3 mb-3 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">share</span>
            Share & Image
        </h3>
        <div class="grid grid-cols-1 gap-2">
            <button onclick="testShareAsImage()" class="btn-test">
                <span class="material-symbols-outlined text-sm">share</span>
                shareAsImage()
            </button>
            <button onclick="testSaveAsImage()" class="btn-test">
                <span class="material-symbols-outlined text-sm">save</span>
                saveAsImage()
            </button>
        </div>
    </div>

    {{-- 6. Camera & Gallery --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <h3 class="font-headline-sm text-headline-sm text-on-surface pb-3 mb-3 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">photo_camera</span>
            Camera & Gallery
        </h3>
        <div class="grid grid-cols-1 gap-2">
            <button onclick="testCaptureCamera()" class="btn-test">
                <span class="material-symbols-outlined text-sm">photo_camera</span>
                captureCamera()
            </button>
            <button onclick="testPickFromGallery()" class="btn-test">
                <span class="material-symbols-outlined text-sm">photo_library</span>
                pickFromGallery()
            </button>
            <button onclick="testCaptureCameraForForm()" class="btn-test">
                <span class="material-symbols-outlined text-sm">add_a_photo</span>
                captureCameraForForm()
            </button>
            <button onclick="testPickFromGalleryForForm()" class="btn-test">
                <span class="material-symbols-outlined text-sm">add_photo_alternate</span>
                pickFromGalleryForForm()
            </button>
        </div>
        <div id="camera-preview" class="mt-3 hidden">
            <p class="text-xs text-on-surface-variant mb-2">Captured image:</p>
            <img id="captured-image" class="w-full max-h-60 object-contain rounded-lg border border-outline-variant" />
        </div>
    </div>

    {{-- 7. Location --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <h3 class="font-headline-sm text-headline-sm text-on-surface pb-3 mb-3 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">location_on</span>
            Location
        </h3>
        <div class="grid grid-cols-1 gap-2">
            <button onclick="testHasLocationPermission()" class="btn-test">
                <span class="material-symbols-outlined text-sm">admin_panel_settings</span>
                hasLocationPermission()
            </button>
            <button onclick="testRequestLocationPermission()" class="btn-test">
                <span class="material-symbols-outlined text-sm">lock_open</span>
                requestLocationPermission()
            </button>
            <button onclick="testGetCurrentLocation()" class="btn-test">
                <span class="material-symbols-outlined text-sm">my_location</span>
                getCurrentLocation()
            </button>
        </div>
        <div id="location-result" class="mt-3 px-3 py-2 rounded-lg bg-surface-container-high text-on-surface-variant text-sm font-mono hidden">
        </div>
    </div>

    {{-- 8. Push Notifications --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <h3 class="font-headline-sm text-headline-sm text-on-surface pb-3 mb-3 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">notifications_active</span>
            Push Notifications
        </h3>
        <div class="grid grid-cols-1 gap-2">
            <button onclick="testGetFcmToken()" class="btn-test">
                <span class="material-symbols-outlined text-sm">vpn_key</span>
                getFcmToken()
            </button>
            <button onclick="testConnectWebSocket()" class="btn-test">
                <span class="material-symbols-outlined text-sm">cable</span>
                connectWebSocket()
            </button>
            <button onclick="testDisconnectWebSocket()" class="btn-test">
                <span class="material-symbols-outlined text-sm">link_off</span>
                disconnectWebSocket()
            </button>
            <button onclick="testGetPollingStatus()" class="btn-test">
                <span class="material-symbols-outlined text-sm">info</span>
                getPollingStatus()
            </button>
            <button onclick="testConnectMqtt()" class="btn-test">
                <span class="material-symbols-outlined text-sm">cell_tower</span>
                connectMqtt()
            </button>
            <button onclick="testDisconnectMqtt()" class="btn-test">
                <span class="material-symbols-outlined text-sm">signal_cellular_off</span>
                disconnectMqtt()
            </button>
            <button onclick="testScheduleLocalNotification()" class="btn-test">
                <span class="material-symbols-outlined text-sm">alarm_add</span>
                scheduleLocal()
            </button>
            <button onclick="testCancelAllLocalNotifications()" class="btn-test">
                <span class="material-symbols-outlined text-sm">alarm_off</span>
                cancelAllLocal()
            </button>
        </div>
    </div>

    {{-- 9. Bluetooth Printer --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <h3 class="font-headline-sm text-headline-sm text-on-surface pb-3 mb-3 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">printer</span>
            Bluetooth Printer
        </h3>

        <div id="printer-status" class="mb-3 px-3 py-2 rounded-lg bg-surface-container-high text-on-surface-variant text-sm font-mono">
            Status: Checking...
        </div>

        <div class="grid grid-cols-1 gap-2">
            <button onclick="testGetPairedPrinters()" class="btn-test">
                <span class="material-symbols-outlined text-sm">bluetooth_searching</span>
                getPairedPrinters()
            </button>
            <button onclick="testScanPrinters()" class="btn-test">
                <span class="material-symbols-outlined text-sm">radar</span>
                scanPrinters()
            </button>
            <button onclick="testCancelPrinterScan()" class="btn-test">
                <span class="material-symbols-outlined text-sm">cancel</span>
                cancelScan()
            </button>
            <button onclick="testAutoConnectPrinter()" class="btn-test">
                <span class="material-symbols-outlined text-sm">link</span>
                autoConnect()
            </button>
            <button onclick="testDisconnectPrinter()" class="btn-test">
                <span class="material-symbols-outlined text-sm">link_off</span>
                disconnectPrinter()
            </button>
            <button onclick="testGetConnectedPrinter()" class="btn-test">
                <span class="material-symbols-outlined text-sm">info</span>
                getConnectedPrinter()
            </button>
            <button onclick="testGetSavedPrinter()" class="btn-test">
                <span class="material-symbols-outlined text-sm">bookmark</span>
                getSavedPrinter()
            </button>
            <button onclick="testRemoveSavedPrinter()" class="btn-test">
                <span class="material-symbols-outlined text-sm">bookmark_remove</span>
                removeSaved()
            </button>
            <button onclick="testPrinterTestPrint()" class="btn-test">
                <span class="material-symbols-outlined text-sm">print</span>
                testPrint()
            </button>
        </div>

        {{-- Paired Devices List --}}
        <div id="printer-paired-list" class="mt-3 space-y-2 hidden">
            <div class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1">Paired Devices</div>
        </div>

        {{-- Discovered Devices List --}}
        <div id="printer-discovered-list" class="mt-3 space-y-2 hidden">
            <div class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1">Available Devices</div>
        </div>

        {{-- Quick Connect --}}
        <div class="mt-3">
            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1 block">Quick Connect (MAC Address)</label>
            <div class="flex flex-col sm:flex-row gap-2">
                <input type="text" id="printer-mac-input" placeholder="AA:BB:CC:DD:EE:FF" class="flex-1 px-3 py-2 bg-surface-container-high border border-outline-variant rounded-lg text-sm font-mono">
                <button onclick="testConnectPrinter(document.getElementById('printer-mac-input').value)" class="btn-test">Connect</button>
            </div>
        </div>

        {{-- Print Receipt Test --}}
        <div class="mt-3">
            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1 block">Print Receipt Test</label>
            <div class="flex flex-col sm:flex-row gap-2">
                <button onclick="testPrintReceipt()" class="btn-test flex-1">
                    <span class="material-symbols-outlined text-sm">receipt_long</span>
                    Print Receipt
                </button>
                <button onclick="testPrintLabel()" class="btn-test flex-1">
                    <span class="material-symbols-outlined text-sm">label</span>
                    Print Label
                </button>
            </div>
        </div>
    </div>

    {{-- 10. File Operations --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <h3 class="font-headline-sm text-headline-sm text-on-surface pb-3 mb-3 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">folder</span>
            File Operations
        </h3>

        <div id="file-dir" class="mb-3 px-3 py-2 rounded-lg bg-surface-container-high text-on-surface-variant text-sm font-mono">
            Directory: Checking...
        </div>

        <div id="file-list" class="mb-3 px-3 py-2 rounded-lg bg-surface-container-high text-on-surface-variant text-sm font-mono hidden max-h-40 overflow-y-auto">
        </div>

        {{-- Create File --}}
        <div class="mb-3">
            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1 block">Create / Update File</label>
            <div class="flex flex-col sm:flex-row gap-2 mb-2">
                <input type="text" id="file-name-input" placeholder="filename.txt" value="test.txt" class="flex-1 px-3 py-2 bg-surface-container-high border border-outline-variant rounded-lg text-sm font-mono">
                <button onclick="testFileExists()" class="btn-test">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    exists()
                </button>
            </div>
            <textarea id="file-content-input" rows="3" placeholder="File content..." class="w-full px-3 py-2 bg-surface-container-high border border-outline-variant rounded-lg text-sm font-mono mb-2">Hello from NativeBridge!</textarea>
            <div class="grid grid-cols-1 gap-2">
                <button onclick="testCreateFile()" class="btn-test">
                    <span class="material-symbols-outlined text-sm">add_circle</span>
                    createFile()
                </button>
                <button onclick="testUpdateFile()" class="btn-test">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    updateFile()
                </button>
                <button onclick="testAppendFile()" class="btn-test">
                    <span class="material-symbols-outlined text-sm">playlist_add</span>
                    appendFile()
                </button>
                <button onclick="testReadFile()" class="btn-test">
                    <span class="material-symbols-outlined text-sm">visibility</span>
                    readFile()
                </button>
            </div>
        </div>

        {{-- Rename / Delete --}}
        <div class="mb-3">
            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1 block">Rename / Delete</label>
            <div class="flex flex-col gap-2 mb-2">
                <input type="text" id="file-rename-old" placeholder="old.txt" class="w-full px-3 py-2 bg-surface-container-high border border-outline-variant rounded-lg text-sm font-mono">
                <span class="material-symbols-outlined text-on-surface-variant self-center">arrow_forward</span>
                <input type="text" id="file-rename-new" placeholder="new.txt" class="w-full px-3 py-2 bg-surface-container-high border border-outline-variant rounded-lg text-sm font-mono">
            </div>
            <div class="grid grid-cols-1 gap-2">
                <button onclick="testRenameFile()" class="btn-test">
                    <span class="material-symbols-outlined text-sm">drive_file_rename</span>
                    renameFile()
                </button>
                <button onclick="testDeleteFile()" class="btn-test">
                    <span class="material-symbols-outlined text-sm">delete</span>
                    deleteFile()
                </button>
            </div>
        </div>

        {{-- List & Info --}}
        <div class="grid grid-cols-1 gap-2">
            <button onclick="testListFiles()" class="btn-test">
                <span class="material-symbols-outlined text-sm">list</span>
                listFiles()
            </button>
            <button onclick="testGetFilesDir()" class="btn-test">
                <span class="material-symbols-outlined text-sm">folder_open</span>
                getFilesDirectory()
            </button>
        </div>

        {{-- File Content Display --}}
        <div id="file-content-display" class="mt-3 hidden">
            <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1 block">File Content</label>
            <pre id="file-content-pre" class="px-3 py-2 rounded-lg bg-black text-green-400 text-xs font-mono max-h-48 overflow-y-auto whitespace-pre-wrap"></pre>
        </div>
    </div>

    {{-- 11. Push Notification Polling --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 mb-4">
        <h3 class="font-headline-sm text-headline-sm text-on-surface pb-3 mb-3 border-b border-outline-variant flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-xl">notifications_active</span>
            Push Notification Polling
        </h3>

        <div id="polling-status" class="mb-3 px-3 py-2 rounded-lg bg-surface-container-high text-on-surface-variant text-sm font-mono">
            Status: Checking...
        </div>

        {{-- Toggle Button --}}
        <div class="mb-3">
            <button id="btn-polling-toggle" onclick="togglePolling()" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold transition-colors">
                <span class="material-symbols-outlined text-lg" id="polling-icon">play_arrow</span>
                <span id="polling-btn-text">Start Polling</span>
            </button>
        </div>

        {{-- Config --}}
        <div class="space-y-3">
            <div>
                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1 block">Polling URL</label>
                <input type="text" id="polling-url" value="{{ url('/api/notifications/poll') }}" placeholder="https://your-server.com/notifications"
                    class="w-full px-3 py-2 bg-surface-container-high border border-outline-variant rounded-lg text-sm font-mono">
            </div>
            <div>
                <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1 block">Interval (seconds)</label>
                <div class="flex gap-2">
                    <button onclick="setPollingInterval(30)" id="poll-int-30" class="flex-1 py-2 rounded-xl text-sm font-bold border-2 border-gray-200 text-gray-500 hover:border-gray-300 transition-colors">30s</button>
                    <button onclick="setPollingInterval(60)" id="poll-int-60" class="flex-1 py-2 rounded-xl text-sm font-bold border-2 border-blue-500 bg-blue-50 text-blue-600 transition-colors">60s</button>
                    <button onclick="setPollingInterval(120)" id="poll-int-120" class="flex-1 py-2 rounded-xl text-sm font-bold border-2 border-gray-200 text-gray-500 hover:border-gray-300 transition-colors">120s</button>
                    <button onclick="setPollingInterval(300)" id="poll-int-300" class="flex-1 py-2 rounded-xl text-sm font-bold border-2 border-gray-200 text-gray-500 hover:border-gray-300 transition-colors">5m</button>
                </div>
            </div>
        </div>

        {{-- Manual Actions --}}
        <div class="mt-3 grid grid-cols-1 gap-2">
            <button onclick="testGetPollingStatus()" class="btn-test">
                <span class="material-symbols-outlined text-sm">info</span>
                getStatus()
            </button>
            <button onclick="testScheduleLocalNotification()" class="btn-test">
                <span class="material-symbols-outlined text-sm">alarm_add</span>
                Test Local
            </button>
        </div>
    </div>

    <style>
        .btn-test {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 0.75rem;
            background-color: var(--color-primary);
            color: var(--color-on-primary);
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1.25rem;
            border-radius: 0.5rem;
            border: 1px solid var(--color-primary);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
            cursor: pointer;
            transition: background-color 150ms ease, color 150ms ease, border-color 150ms ease, transform 150ms ease;
        }
        .btn-test:hover {
            background-color: var(--color-primary-container);
            color: #ffffff;
            border-color: var(--color-primary);
        }
        .btn-test:active {
            transform: scale(0.97);
        }
        .btn-test:focus-visible {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }

        /* Prevent horizontal overflow on status/log containers (long JSON, paths, etc.)
           regardless of Tailwind classes being swapped by JS */
        #log-panel,
        #network-status,
        #location-result,
        #printer-status,
        #file-dir,
        #file-list,
        #polling-status {
            overflow-wrap: anywhere;
            word-break: break-word;
            overflow-x: hidden;
        }

        @media print {
            body * { visibility: hidden !important; }
            #print-area,
            #print-area * { visibility: visible !important; }
            #print-area {
                position: absolute;
                inset: 0;
                width: 100%;
                margin: 0;
                border: 0;
            }
        }

        body.print-area-only {
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
        }
        body.print-area-only * {
            visibility: hidden !important;
        }
        body.print-area-only #print-area,
        body.print-area-only #print-area * {
            visibility: visible !important;
        }
        body.print-area-only #print-area {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
        }
        .print-area-boundary {
            margin-top: 1rem;
            padding-top: 0.25rem;
            border-top: 1px dotted #111827;
            color: #111827;
            font: 700 0.75rem/1 monospace;
            letter-spacing: 0.08em;
            overflow: hidden;
            white-space: nowrap;
        }
    </style>

    <script>
        function log(msg, type = 'info') {
            const panel = document.getElementById('log-panel');
            const time = new Date().toLocaleTimeString();
            const color = type === 'error' ? '#ef4444' : type === 'success' ? '#22c55e' : type === 'warn' ? '#eab308' : '#4ade80';
            panel.innerHTML += `<div><span style="color:#6b7280">[${time}]</span> <span style="color:${color}">${msg}</span></div>`;
            panel.scrollTop = panel.scrollHeight;
        }

        function clearLog() {
            document.getElementById('log-panel').innerHTML = '<div class="text-gray-500">Log cleared.</div>';
        }

        function hasNativeBridge() {
            if (typeof NativeBridge === 'undefined') {
                log('NativeBridge is not available (not running in Android WebView)', 'error');
                return false;
            }
            return true;
        }

        // === Device Info ===
        function testGetDeviceModel() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getDeviceModel();
            log('getDeviceModel() → ' + result, 'success');
        }

        function testGetDeviceBrand() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getDeviceBrand();
            log('getDeviceBrand() → ' + result, 'success');
        }

        function testGetDeviceManufacturer() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getDeviceManufacturer();
            log('getDeviceManufacturer() → ' + result, 'success');
        }

        function testGetSdkVersion() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getSdkVersion();
            log('getSdkVersion() → ' + result + (result >= 33 ? ' (Android 13+)' : ''), 'success');
        }

        function testGetAppVersion() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getAppVersion();
            log('getAppVersion() → ' + result, 'success');
        }

        function testGetPackageName() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getPackageName();
            log('getPackageName() → ' + result, 'success');
        }

        function testGetAndroidId() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getAndroidId();
            log('getAndroidId() → ' + result, 'success');
        }

        function testGetSerialNumber() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getSerialNumber();
            log('getSerialNumber() → ' + result, result === 'permission_required' ? 'warn' : 'success');
        }

        function testGetUniqueId() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getUniqueId();
            log('getUniqueId() → ' + result, 'success');
        }

        function testGetDeviceInfo() {
            if (!hasNativeBridge()) return;
            const result = JSON.parse(NativeBridge.getDeviceInfo());
            log('getDeviceInfo() → Model: ' + result.model + ', Android: ' + result.androidVersion + ', SDK: ' + result.sdkVersion + ', Battery: ' + result.batteryLevel + '%, Screen: ' + result.screenWidth + 'x' + result.screenHeight, 'success');
        }

        // === UI Feedback ===
        function testShowToast() {
            if (!hasNativeBridge()) return;
            NativeBridge.showToast('Hello from NativeBridge Test! (' + new Date().toLocaleTimeString() + ')');
            log('showToast() → sent', 'success');
        }

        function testVibrate(ms) {
            if (!hasNativeBridge()) return;
            NativeBridge.vibrate(ms);
            log('vibrate(' + ms + 'ms) → sent', 'success');
        }

        // === Network ===
        function testIsConnected() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.isConnected();
            log('isConnected() → ' + result, result ? 'success' : 'warn');
            document.getElementById('network-status').textContent = 'Network status: ' + (result ? 'Online' : 'Offline');
            document.getElementById('network-status').className = 'mt-3 px-3 py-2 rounded-lg text-sm font-mono ' + (result ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700');
        }

        function testStartNetworkCallback() {
            if (!hasNativeBridge()) return;
            window.onNetworkChanged = function(status) {
                log('onNetworkChanged → ' + status, status === 'online' ? 'success' : 'warn');
                const el = document.getElementById('network-status');
                el.textContent = 'Network status: ' + (status === 'online' ? 'Online' : 'Offline');
                el.className = 'mt-3 px-3 py-2 rounded-lg text-sm font-mono ' + (status === 'online' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700');
            };
            NativeBridge.startNetworkCallback();
            log('startNetworkCallback() → listening...', 'success');
        }

        function testStopNetworkCallback() {
            if (!hasNativeBridge()) return;
            NativeBridge.stopNetworkCallback();
            log('stopNetworkCallback() → stopped', 'success');
            document.getElementById('network-status').textContent = 'Network status: Monitoring stopped';
            document.getElementById('network-status').className = 'mt-3 px-3 py-2 rounded-lg bg-surface-container-high text-on-surface-variant text-sm font-mono';
        }

        // === Print & Save ===
        function runForPrintArea(nativeAction, message) {
            if (!hasNativeBridge()) return;

            document.body.classList.add('print-area-only');
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    NativeBridge[nativeAction]();
                    log(message, 'success');
                });
            });
        }

        function testPrintPage() {
            runForPrintArea('printPage', 'printPage() → print area only');
        }

        function testSaveAsPdf() {
            runForPrintArea('saveAsPdf', 'saveAsPdf() → print area only');
        }

        function testPrintArea() {
            runForPrintArea('printPage', 'printPage() → printing #print-area only');
        }

        // === Share & Image ===
        function testShareAsImage() {
            runForPrintArea('shareAsImage', 'shareAsImage() → print area only');
        }

        function testSaveAsImage() {
            runForPrintArea('saveAsImage', 'saveAsImage() → print area only');
        }

        // === Camera & Gallery ===
        function testCaptureCamera() {
            if (!hasNativeBridge()) return;
            window.onImageCaptured = function(data) {
                if (data.startsWith('{') && data.includes('"error"')) {
                    const error = JSON.parse(data);
                    log('captureCamera() → error: ' + error.error, 'error');
                } else {
                    log('captureCamera() → received image (' + Math.round(data.length / 1024) + ' KB)', 'success');
                    const preview = document.getElementById('camera-preview');
                    const img = document.getElementById('captured-image');
                    img.src = data;
                    preview.classList.remove('hidden');
                }
            };
            NativeBridge.captureCamera();
            log('captureCamera() → opening camera...', 'info');
        }

        function testPickFromGallery() {
            if (!hasNativeBridge()) return;
            window.onImagePicked = function(data) {
                if (data.startsWith('{') && data.includes('"error"')) {
                    const error = JSON.parse(data);
                    log('pickFromGallery() → error: ' + error.error, 'error');
                } else {
                    log('pickFromGallery() → received image (' + Math.round(data.length / 1024) + ' KB)', 'success');
                    const preview = document.getElementById('camera-preview');
                    const img = document.getElementById('captured-image');
                    img.src = data;
                    preview.classList.remove('hidden');
                }
            };
            NativeBridge.pickFromGallery();
            log('pickFromGallery() → opening gallery...', 'info');
        }

        function testCaptureCameraForForm() {
            if (!hasNativeBridge()) return;
            NativeBridge.captureCameraForForm();
            log('captureCameraForForm() → opening camera for form...', 'info');
        }

        function testPickFromGalleryForForm() {
            if (!hasNativeBridge()) return;
            NativeBridge.pickFromGalleryForForm();
            log('pickFromGalleryForForm() → opening gallery for form...', 'info');
        }

        // === Location ===
        function testHasLocationPermission() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.hasLocationPermission();
            log('hasLocationPermission() → ' + result, result ? 'success' : 'warn');
        }

        function testRequestLocationPermission() {
            if (!hasNativeBridge()) return;
            NativeBridge.requestLocationPermission();
            log('requestLocationPermission() → requesting permission...', 'info');
        }

        function testGetCurrentLocation() {
            if (!hasNativeBridge()) return;

            window.onLocationSuccess = function(data) {
                const loc = JSON.parse(data);
                log('onLocationSuccess → Lat: ' + loc.latitude + ', Lng: ' + loc.longitude + ', Acc: ' + loc.accuracy + 'm', 'success');
                const el = document.getElementById('location-result');
                el.classList.remove('hidden');
                el.innerHTML = '<strong>Latitude:</strong> ' + loc.latitude + '<br><strong>Longitude:</strong> ' + loc.longitude + '<br><strong>Accuracy:</strong> ' + loc.accuracy + 'm<br><strong>Speed:</strong> ' + loc.speed + ' m/s<br><strong>Bearing:</strong> ' + loc.bearing + '<br><strong>Timestamp:</strong> ' + new Date(loc.timestamp).toLocaleString();
            };

            window.onLocationError = function(error) {
                log('onLocationError → ' + error, 'error');
                const el = document.getElementById('location-result');
                el.classList.remove('hidden');
                el.innerHTML = '<span class="text-red-600">' + error + '</span>';
            };

            if (NativeBridge.hasLocationPermission()) {
                NativeBridge.getCurrentLocation();
                log('getCurrentLocation() → requesting location...', 'info');
            } else {
                NativeBridge.requestLocationPermission();
                log('requestLocationPermission() → requesting first, then get location', 'warn');
            }
        }

        // === Push Notifications ===
        function testGetFcmToken() {
            if (!hasNativeBridge()) return;
            window.onFcmToken = function(token) {
                log('onFcmToken → ' + token.substring(0, 30) + '...', 'success');
            };
            NativeBridge.getFcmToken();
            log('getFcmToken() → requesting token...', 'info');
        }

        function testConnectWebSocket() {
            if (!hasNativeBridge()) return;
            window.onPushNotification = function(data) {
                const payload = JSON.parse(data);
                log('WebSocket notification → ' + payload.title + ': ' + payload.body, 'success');
            };
            window.onNetworkStatus = function(status) {
                log('WebSocket status → ' + status, status === 'websocket_connected' ? 'success' : 'warn');
            };
            NativeBridge.connectWebSocket('wss://echo.websocket.org');
            log('connectWebSocket() → connecting...', 'info');
        }

        function testDisconnectWebSocket() {
            if (!hasNativeBridge()) return;
            NativeBridge.disconnectWebSocket();
            log('disconnectWebSocket() → disconnected', 'success');
        }

        // === Push Notification Polling ===
        let pollingInterval = 60;

        function setPollingInterval(seconds) {
            pollingInterval = seconds;
            document.querySelectorAll('[id^="poll-int-"]').forEach(btn => {
                btn.className = 'flex-1 py-2 rounded-xl text-sm font-bold border-2 border-gray-200 text-gray-500 hover:border-gray-300 transition-colors';
            });
            const active = document.getElementById('poll-int-' + seconds);
            if (active) {
                active.className = 'flex-1 py-2 rounded-xl text-sm font-bold border-2 border-blue-500 bg-blue-50 text-blue-600 transition-colors';
            }
        }

        function updatePollingStatus(isPolling, url, interval) {
            const el = document.getElementById('polling-status');
            const btn = document.getElementById('btn-polling-toggle');
            const icon = document.getElementById('polling-icon');
            const text = document.getElementById('polling-btn-text');

            if (isPolling) {
                el.textContent = 'Status: Polling every ' + interval + 's → ' + (url || 'N/A');
                el.className = 'mb-3 px-3 py-2 rounded-lg bg-green-50 text-green-700 text-sm font-mono';
                btn.className = 'w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold bg-red-500 text-white hover:bg-red-600 transition-colors';
                icon.textContent = 'stop';
                text.textContent = 'Stop Polling';
            } else {
                el.textContent = 'Status: Stopped';
                el.className = 'mb-3 px-3 py-2 rounded-lg bg-surface-container-high text-on-surface-variant text-sm font-mono';
                btn.className = 'w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold bg-green-500 text-white hover:bg-green-600 transition-colors';
                icon.textContent = 'play_arrow';
                text.textContent = 'Start Polling';
            }
        }

        function togglePolling() {
            if (!hasNativeBridge()) return;

            const isPolling = NativeBridge.isPolling();
            if (isPolling) {
                NativeBridge.stopPolling();
                log('stopPolling() → stopped', 'success');
                updatePollingStatus(false);
            } else {
                const url = document.getElementById('polling-url').value;
                if (!url) { log('Enter polling URL', 'error'); return; }

                window.onPushNotification = function(data) {
                    const payload = JSON.parse(data);
                    log('🔔 Push: ' + payload.title + ' → ' + payload.body, 'success');
                };
                window.onPollingStarted = function(data) {
                    const d = JSON.parse(data);
                    log('onPollingStarted → ' + d.url + ' every ' + d.interval + 's', 'success');
                    updatePollingStatus(true, d.url, d.interval);
                };
                window.onPollingStopped = function(data) {
                    log('onPollingStopped', 'success');
                    updatePollingStatus(false);
                };

                NativeBridge.startPolling(url, pollingInterval);
                log('startPolling() → polling every ' + pollingInterval + 's', 'info');
            }
        }

        function testGetPollingStatus() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getPollingStatus();
            log('getPollingStatus() → ' + result, 'success');
            const d = JSON.parse(result);
            updatePollingStatus(d.polling, d.url, d.interval);
        }

        function testConnectMqtt() {
            if (!hasNativeBridge()) return;
            window.onPushNotification = function(data) {
                const payload = JSON.parse(data);
                log('MQTT notification → ' + payload.title + ': ' + payload.body, 'success');
            };
            window.onNetworkStatus = function(status) {
                log('MQTT status → ' + status, status === 'mqtt_connected' ? 'success' : 'warn');
            };
            NativeBridge.connectMqtt('tcp://broker.example.com:1883', 'sidoraya-test', 'notifications/test');
            log('connectMqtt() → connecting...', 'info');
        }

        function testDisconnectMqtt() {
            if (!hasNativeBridge()) return;
            NativeBridge.disconnectMqtt();
            log('disconnectMqtt() → disconnected', 'success');
        }

        function testScheduleLocalNotification() {
            if (!hasNativeBridge()) return;
            const now = new Date();
            const h = now.getHours();
            const m = (now.getMinutes() + 1) % 60;
            NativeBridge.scheduleLocalNotification('Test Notification', 'This is a scheduled test from NativeBridge page', h, m, false);
            log('scheduleLocalNotification() → scheduled for ' + h + ':' + String(m).padStart(2, '0'), 'success');
        }

        function testCancelAllLocalNotifications() {
            if (!hasNativeBridge()) return;
            NativeBridge.cancelAllLocalNotifications();
            log('cancelAllLocalNotifications() → cancelled all', 'success');
        }

        // === Bluetooth Printer ===
        function updatePrinterStatus(text) {
            const el = document.getElementById('printer-status');
            el.textContent = 'Status: ' + text;
            el.className = 'mb-3 px-3 py-2 rounded-lg text-sm font-mono ' +
                (text.includes('Connected') ? 'bg-green-50 text-green-700' : 'bg-surface-container-high text-on-surface-variant');
        }

        function testGetPairedPrinters() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getPairedPrinters();
            log('getPairedPrinters() → ' + result, 'success');
            const devices = JSON.parse(result);
            const list = document.getElementById('printer-paired-list');
            if (devices.length === 0) {
                list.innerHTML = '<div class="text-sm text-on-surface-variant">No paired printers</div>';
            } else {
                list.innerHTML = '<div class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1">Paired Devices</div>' +
                    devices.map(d => '<div class="flex items-center justify-between p-2 bg-surface-container-high rounded-lg">' +
                        '<div><div class="text-sm font-bold">' + d.name + '</div><div class="text-xs text-on-surface-variant font-mono">' + d.address + '</div></div>' +
                        '<button onclick="testConnectPrinter(\'' + d.address + '\')" class="text-xs px-2 py-1 rounded bg-primary text-on-primary font-semibold">Connect</button>' +
                    '</div>').join('');
            }
            list.classList.remove('hidden');
        }

        function testScanPrinters() {
            if (!hasNativeBridge()) return;
            window.onPrintersFound = function(data) {
                const devices = JSON.parse(data);
                log('onPrintersFound → ' + devices.length + ' devices', 'success');
                const list = document.getElementById('printer-discovered-list');
                if (devices.length === 0) {
                    list.innerHTML = '<div class="text-sm text-on-surface-variant">No devices found</div>';
                } else {
                    list.innerHTML = '<div class="text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1">Available Devices</div>' +
                        devices.map(d => '<div class="flex items-center justify-between p-2 bg-surface-container-high rounded-lg">' +
                            '<div><div class="text-sm font-bold">' + d.name + '</div><div class="text-xs text-on-surface-variant font-mono">' + d.address + '</div></div>' +
                            '<button onclick="testConnectPrinter(\'' + d.address + '\')" class="text-xs px-2 py-1 rounded bg-primary text-on-primary font-semibold">Connect</button>' +
                        '</div>').join('');
                }
                list.classList.remove('hidden');
            };
            NativeBridge.scanPrinters();
            log('scanPrinters() → scanning...', 'info');
        }

        function testCancelPrinterScan() {
            if (!hasNativeBridge()) return;
            NativeBridge.cancelPrinterScan();
            log('cancelPrinterScan() → stopped', 'success');
        }

        function testConnectPrinter(address) {
            if (!hasNativeBridge()) return;
            if (!address) { log('Enter MAC address', 'error'); return; }
            window.onPrinterConnected = function(data) {
                const d = JSON.parse(data);
                log('onPrinterConnected → ' + JSON.stringify(d), d.success ? 'success' : 'error');
                updatePrinterStatus(d.success ? 'Connected: ' + d.name : 'Failed: ' + (d.error || ''));
            };
            NativeBridge.connectPrinter(address);
            log('connectPrinter(' + address + ') → connecting...', 'info');
            updatePrinterStatus('Connecting...');
        }

        function testDisconnectPrinter() {
            if (!hasNativeBridge()) return;
            window.onPrinterDisconnected = function(data) {
                log('onPrinterDisconnected → ' + data, 'success');
                updatePrinterStatus('Disconnected');
            };
            NativeBridge.disconnectPrinter();
            log('disconnectPrinter() → disconnecting...', 'info');
        }

        function testAutoConnectPrinter() {
            if (!hasNativeBridge()) return;
            window.onPrinterConnected = function(data) {
                const d = JSON.parse(data);
                log('onPrinterConnected (auto) → ' + JSON.stringify(d), d.success ? 'success' : 'error');
                updatePrinterStatus(d.success ? 'Connected: ' + d.name : 'Failed: ' + (d.error || ''));
            };
            NativeBridge.autoConnectPrinter();
            log('autoConnectPrinter() → attempting auto-connect...', 'info');
        }

        function testGetConnectedPrinter() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getConnectedPrinter();
            log('getConnectedPrinter() → ' + result, 'success');
            const d = JSON.parse(result);
            updatePrinterStatus(d.connected ? 'Connected: ' + d.name : 'Disconnected');
        }

        function testGetSavedPrinter() {
            if (!hasNativeBridge()) return;
            const result = NativeBridge.getSavedPrinter();
            log('getSavedPrinter() → ' + result, 'success');
        }

        function testRemoveSavedPrinter() {
            if (!hasNativeBridge()) return;
            window.onPrinterRemoved = function(data) {
                log('onPrinterRemoved → ' + data, 'success');
                updatePrinterStatus('No saved printer');
            };
            NativeBridge.removeSavedPrinter();
            log('removeSavedPrinter() → removed', 'success');
        }

        function testPrinterTestPrint() {
            if (!hasNativeBridge()) return;
            window.onPrintResult = function(data) {
                const d = JSON.parse(data);
                log('onPrintResult → ' + JSON.stringify(d), d.success ? 'success' : 'error');
            };
            NativeBridge.testPrint();
            log('testPrint() → sending test page...', 'info');
        }

        function testPrintReceipt() {
            if (!hasNativeBridge()) return;
            window.onPrintResult = function(data) {
                const d = JSON.parse(data);
                log('onPrintResult (receipt) → ' + JSON.stringify(d), d.success ? 'success' : 'error');
            };
            const data = {
                lines: [
                    { text: 'COLD STORAGE', style: 'large' },
                    { divider: true },
                    { text: 'Date: ' + new Date().toLocaleString('id-ID'), style: 'normal' },
                    { text: '' },
                    { text: 'ABC Product | 2 x 15.000', style: 'normal' },
                    { text: 'XYZ Product | 1 x 25.000', style: 'normal' },
                    { divider: true },
                    { text: 'TOTAL | 55.000', style: 'bold' },
                    { text: '' },
                    { text: 'Thank you!', style: 'center' }
                ],
                paper_width: 58,
                cut: true
            };
            NativeBridge.printReceipt(JSON.stringify(data));
            log('printReceipt() → printing receipt...', 'info');
        }

        function testPrintLabel() {
            if (!hasNativeBridge()) return;
            window.onPrintResult = function(data) {
                const d = JSON.parse(data);
                log('onPrintResult (label) → ' + JSON.stringify(d), d.success ? 'success' : 'error');
            };
            const data = {
                type: 'tspl',
                content: 'COLD STORAGE\nSKU-001\nProduct Name\n*123456789*',
                width: 40,
                height: 30,
                copies: 1
            };
            NativeBridge.printLabel(JSON.stringify(data));
            log('printLabel() → printing label...', 'info');
        }

        // === File Operations ===
        function updateFileDir() {
            if (!hasNativeBridge()) return;
            const path = NativeBridge.getFilesDirectory();
            document.getElementById('file-dir').textContent = 'Directory: ' + path;
        }

        function updateFileList() {
            if (!hasNativeBridge()) return;
            const result = JSON.parse(NativeBridge.listFiles());
            const el = document.getElementById('file-list');
            if (result.count === 0) {
                el.innerHTML = '<span class="text-on-surface-variant">No files yet</span>';
            } else {
                el.innerHTML = result.files.map(f =>
                    '<div class="flex justify-between items-center py-1 border-b border-outline-variant last:border-0">' +
                    '<span class="truncate">' + f.name + '</span>' +
                    '<span class="text-on-surface-variant text-xs ml-2 whitespace-nowrap">' + f.size + ' B</span>' +
                    '</div>'
                ).join('');
            }
            el.classList.remove('hidden');
        }

        function showFileContent(content) {
            const display = document.getElementById('file-content-display');
            const pre = document.getElementById('file-content-pre');
            pre.textContent = content;
            display.classList.remove('hidden');
        }

        function testCreateFile() {
            if (!hasNativeBridge()) return;
            const name = document.getElementById('file-name-input').value.trim();
            const content = document.getElementById('file-content-input').value;
            if (!name) { log('Enter filename', 'error'); return; }
            const result = JSON.parse(NativeBridge.createFile(name, content));
            log('createFile(\'' + name + '\') → ' + JSON.stringify(result), result.success ? 'success' : 'error');
            updateFileList();
        }

        function testReadFile() {
            if (!hasNativeBridge()) return;
            const name = document.getElementById('file-name-input').value.trim();
            if (!name) { log('Enter filename', 'error'); return; }
            const result = JSON.parse(NativeBridge.readFile(name));
            if (result.success) {
                log('readFile(\'' + name + '\') → ' + result.size + ' bytes', 'success');
                showFileContent(result.content);
            } else {
                log('readFile(\'' + name + '\') → ' + result.error, 'error');
            }
        }

        function testUpdateFile() {
            if (!hasNativeBridge()) return;
            const name = document.getElementById('file-name-input').value.trim();
            const content = document.getElementById('file-content-input').value;
            if (!name) { log('Enter filename', 'error'); return; }
            const result = JSON.parse(NativeBridge.updateFile(name, content));
            log('updateFile(\'' + name + '\') → ' + JSON.stringify(result), result.success ? 'success' : 'error');
            updateFileList();
        }

        function testAppendFile() {
            if (!hasNativeBridge()) return;
            const name = document.getElementById('file-name-input').value.trim();
            const content = document.getElementById('file-content-input').value;
            if (!name) { log('Enter filename', 'error'); return; }
            const result = JSON.parse(NativeBridge.appendFile(name, content));
            log('appendFile(\'' + name + '\') → ' + JSON.stringify(result), result.success ? 'success' : 'error');
            updateFileList();
        }

        function testDeleteFile() {
            if (!hasNativeBridge()) return;
            const name = document.getElementById('file-name-input').value.trim();
            if (!name) { log('Enter filename', 'error'); return; }
            const result = JSON.parse(NativeBridge.deleteFile(name));
            log('deleteFile(\'' + name + '\') → ' + JSON.stringify(result), result.success ? 'success' : 'error');
            updateFileList();
            document.getElementById('file-content-display').classList.add('hidden');
        }

        function testRenameFile() {
            if (!hasNativeBridge()) return;
            const oldName = document.getElementById('file-rename-old').value.trim();
            const newName = document.getElementById('file-rename-new').value.trim();
            if (!oldName || !newName) { log('Enter both filenames', 'error'); return; }
            const result = JSON.parse(NativeBridge.renameFile(oldName, newName));
            log('renameFile(\'' + oldName + '\', \'' + newName + '\') → ' + JSON.stringify(result), result.success ? 'success' : 'error');
            updateFileList();
        }

        function testFileExists() {
            if (!hasNativeBridge()) return;
            const name = document.getElementById('file-name-input').value.trim();
            if (!name) { log('Enter filename', 'error'); return; }
            const exists = NativeBridge.fileExists(name);
            log('fileExists(\'' + name + '\') → ' + exists, exists ? 'success' : 'warn');
        }

        function testListFiles() {
            if (!hasNativeBridge()) return;
            const result = JSON.parse(NativeBridge.listFiles());
            log('listFiles() → ' + result.count + ' file(s)', 'success');
            result.files.forEach(f => {
                log('  ' + f.name + ' (' + f.size + ' B)', 'info');
            });
            updateFileList();
        }

        function testGetFilesDir() {
            if (!hasNativeBridge()) return;
            const path = NativeBridge.getFilesDirectory();
            log('getFilesDirectory() → ' + path, 'success');
            document.getElementById('file-dir').textContent = 'Directory: ' + path;
        }

        // Init file dir on load
        if (hasNativeBridge()) {
            updateFileDir();
        }
    </script>
</x-layouts::app>
