<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Temporary File Uploads
    |---------------------------------------------------------------------------
    |
    | Only this key is overridden; Livewire's service provider merges the rest of
    | its defaults for any top-level key absent here. The merge is shallow, so
    | every sub-key below has to be spelled out — omitting one drops it rather
    | than inheriting it.
    |
    | Livewire validates the temporary upload before Filament's own maxSize() is
    | ever consulted, and its default rule is max:12288 (12MB). The hero
    | background video is far larger than that, so saving the Hero block failed
    | while the same form saved fine with the video removed.
    |
    */

    'temporary_file_upload' => [
        'disk' => null,
        // 128MB, kept in step with upload_max_filesize in the Dockerfile and
        // FileUpload::maxSize() on the hero video field. All three have to agree:
        // the smallest one is what staff actually hit.
        'rules' => ['required', 'file', 'max:131072'],
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma', 'webm',
        ],
        // A 100MB upload over a domestic connection takes well over the default
        // 5 minutes, after which the temporary file is invalidated mid-save.
        'max_upload_time' => 15,
        'cleanup' => true,
    ],

];
