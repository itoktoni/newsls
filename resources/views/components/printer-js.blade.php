<script>
/**
 * BluetoothPrinter — JavaScript API untuk koneksi Bluetooth printer
 *
 * Callbacks:
 *   window.onPrintersFound(printersJSON)
 *   window.onPrinterConnected(dataJSON)
 *   window.onPrinterDisconnected(dataJSON)
 *   window.onPrintResult(dataJSON)
 *   window.onPrinterRemoved(dataJSON)
 */
const BluetoothPrinter = {

    isNative() {
        return typeof NativeBridge !== 'undefined' && typeof NativeBridge.getPairedPrinters === 'function';
    },

    hasPermission() {
        if (!this.isNative()) return true;
        if (typeof NativeBridge.hasBluetoothPermission !== 'function') return true;
        try {
            return NativeBridge.hasBluetoothPermission() === true;
        } catch (e) {
            return true;
        }
    },

    requestPermission() {
        if (!this.isNative()) return;
        if (typeof NativeBridge.requestBluetoothPermission !== 'function') return;
        NativeBridge.requestBluetoothPermission();
    },

    getEngine() {
        if (!this.isNative()) return 'manual';
        if (typeof NativeBridge.getPrinterEngine !== 'function') return 'manual';
        try {
            return NativeBridge.getPrinterEngine();
        } catch (e) {
            return 'manual';
        }
    },

    setEngine(engine) {
        if (!this.isNative()) return;
        if (typeof NativeBridge.setPrinterEngine !== 'function') return;
        NativeBridge.setPrinterEngine('manual');
    },

    // ─── Discovery ───

    getPairedPrinters() {
        if (!this.isNative()) return [];
        try {
            return JSON.parse(NativeBridge.getPairedPrinters());
        } catch (e) {
            return [];
        }
    },

    scanPrinters() {
        if (!this.isNative()) {
            if (typeof window.onPrintersFound === 'function') {
                window.onPrintersFound('[]');
            }
            return;
        }
        try {
            NativeBridge.scanPrinters();
        } catch (e) {
            if (typeof window.onPrintersFound === 'function') {
                window.onPrintersFound(JSON.stringify([{ error: e.message || 'Bluetooth scan failed' }]));
            }
        }
    },

    cancelScan() {
        if (!this.isNative()) return;
        NativeBridge.cancelPrinterScan();
    },

    // ─── Connection ───

    connect(address) {
        if (!this.isNative()) return;
        NativeBridge.connectPrinter(address);
    },

    disconnect() {
        if (!this.isNative()) return;
        NativeBridge.disconnectPrinter();
    },

    isConnected() {
        if (!this.isNative()) return false;
        return NativeBridge.isPrinterConnected();
    },

    getConnected() {
        if (!this.isNative()) return { connected: false, name: '', address: '' };
        try {
            return JSON.parse(NativeBridge.getConnectedPrinter());
        } catch (e) {
            return { connected: false, name: '', address: '' };
        }
    },

    getSaved() {
        if (!this.isNative()) return { address: '', name: '', connected: false };
        try {
            return JSON.parse(NativeBridge.getSavedPrinter());
        } catch (e) {
            return { address: '', name: '', connected: false };
        }
    },

    removeSaved() {
        if (!this.isNative()) return;
        NativeBridge.removeSavedPrinter();
    },

    autoConnect() {
        if (!this.isNative()) return;
        NativeBridge.autoConnectPrinter();
    },

    // ─── Print Receipt (ESC/POS) ───

    printReceipt(lines, options = {}) {
        if (!this.isNative()) return;
        const data = {
            lines: lines,
            paper_width: options.paper_width || 58,
            cut: options.cut !== false,
            beep: options.beep || false,
        };
        NativeBridge.printReceipt(JSON.stringify(data));
    },

    /**
     * Helper: print receipt from HTML content
     * @param {string} title - Judul struk
     * @param {Array} items - [{name, qty, price}]
     * @param {string} footer - Footer text
     * @param {Object} options - {paper_width, cut, beep}
     */
    printReceiptFromItems(title, items, footer, options = {}) {
        const lines = [];
        const pw = options.paper_width || 58;

        lines.push({ text: title, style: 'large' });
        lines.push({ divider: true });
        lines.push({ text: 'Date: ' + new Date().toLocaleString('id-ID'), style: 'normal' });
        lines.push({ text: '' });

        items.forEach(item => {
            const left = item.name;
            const right = item.qty + ' x ' + formatNumber(item.price);
            lines.push({ text: left + ' | ' + right, style: 'normal' });
        });

        lines.push({ divider: true });

        if (options.subtotal) {
            lines.push({ text: 'Subtotal | ' + formatNumber(options.subtotal), style: 'normal' });
        }
        if (options.discount) {
            lines.push({ text: 'Discount | -' + formatNumber(options.discount), style: 'normal' });
        }
        lines.push({ text: 'TOTAL | ' + formatNumber(options.total || 0), style: 'bold' });
        lines.push({ text: '' });

        if (footer) {
            lines.push({ text: footer, style: 'center' });
        }

        this.printReceipt(lines, options);
    },

    // ─── Print Label ───

    printLabel(data) {
        if (!this.isNative()) return;
        NativeBridge.printLabel(JSON.stringify(data));
    },

    /**
     * Helper: print barcode label
     * @param {Object} data - {title, lines: [], barcode, type, width, height, copies}
     */
    printBarcodeLabel(data) {
        const labelData = {
            type: data.type || 'escpos',
            content: '',
            width: data.width || 40,
            height: data.height || 30,
            gap: data.gap || 2,
            copies: data.copies || 1,
        };

        let content = '';
        if (data.title) content += data.title + '\n';
        if (data.lines) {
            data.lines.forEach(line => { content += line + '\n'; });
        }
        if (data.barcode) content += '*' + data.barcode + '*';
        labelData.content = content;

        this.printLabel(labelData);
    },

    // ─── Raw / Bitmap ───

    printRaw(base64Data) {
        if (!this.isNative()) return;
        NativeBridge.printRaw(base64Data);
    },

    printBitmap(base64Data) {
        if (!this.isNative()) return;
        NativeBridge.printBitmap(base64Data);
    },

    // ─── Test ───

    testPrint() {
        if (!this.isNative()) return;
        try {
            NativeBridge.testPrint();
        } catch (e) {
            if (typeof window.onPrintResult === 'function') {
                window.onPrintResult(JSON.stringify({ success: false, error: e.message || 'Print failed' }));
            }
        }
    },
};

function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}
</script>

