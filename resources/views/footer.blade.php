<div class="flex items-center justify-center gap-x-1 text-sm text-gray-500 dark:text-gray-400">
    @if($showCopyright && $copyright)
        <span>{{ $copyright }}</span>
    @endif

    @if($showCopyright && $copyright && ($showVersion || ($showServer && $server)))
        <span>|</span>
    @endif

    @if($showVersion && $version)
        <span>{{ $version }}</span>
    @endif

    @if($showVersion && $version && $showServer && $server)
        <span>|</span>
    @endif

    @if($showServer && $server)
        <span>{{ $server }}</span>
    @endif
</div>
