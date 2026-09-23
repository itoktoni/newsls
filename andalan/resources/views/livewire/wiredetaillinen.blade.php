<div style="text-align: center">

    <div class="header-action">
        <a class="download" wire:click="export">Download Semua</a>

        @if($bus_id)
        <div class="wrapper">
            <div class="progress-bar">
                <span class="progress-bar-fill" style="width: {{ $progress }}%;"></span>
            </div>
        </div>
        @endif

    </div>

    @if($exporting && !$finish)
    <div wire:poll.500ms="updateProgress">
        Current time: {{ now() }}
    </div>
    @endif

    @if($finish)
    <div class="header-action">
        <a class="ready" wire:click="downloadExport">File Siap</a>
    </div>
    @endif

</div>